/**
 * BhraminSathi — Conductor Dashboard Client JS
 * Integrates HTML5 Geolocation watchPosition for live conductor tracking
 */

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function saveConductorProfile() {
    const name = document.getElementById('reg-conductor-name').value.trim() || 'Rajesh Sharma';
    const phone = document.getElementById('reg-conductor-phone').value.trim() || '9876543210';

    fetch('/api/conductor/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ name, phone })
    })
    .then(res => res.json())
    .then(data => {
        const conductorId = (data.conductor && data.conductor.id) ? data.conductor.id : ('COND-' + phone.slice(-4));
        const profile = { name, phone, id: conductorId };
        localStorage.setItem('bhramin_conductor_profile', JSON.stringify(profile));
        document.getElementById('conductor-register-modal').style.display = 'none';
        loadConductorProfile();
    })
    .catch(err => {
        console.warn('Database save notice (local fallback):', err);
        const profile = { name, phone, id: 'COND-' + phone.slice(-4) };
        localStorage.setItem('bhramin_conductor_profile', JSON.stringify(profile));
        document.getElementById('conductor-register-modal').style.display = 'none';
        loadConductorProfile();
    });
}

function loadConductorProfile() {
    const saved = localStorage.getItem('bhramin_conductor_profile');
    if (saved) {
        try {
            const p = JSON.parse(saved);
            document.getElementById('display-conductor-name').textContent = p.name;
            document.getElementById('display-conductor-badge').textContent = `ID: ${p.id} • Shift Active`;
            return p;
        } catch(e) {}
    } else {
        document.getElementById('conductor-register-modal').style.display = 'flex';
    }
    return null;
}

function resetConductorProfile() {
    localStorage.removeItem('bhramin_conductor_profile');
    document.getElementById('conductor-register-modal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', () => {
    loadConductorProfile();
    let isTracking = false;
    let locationInterval = null;
    let watchId = null;

    const btnToggle = document.getElementById('btn-toggle-shift');
    const busSelect = document.getElementById('select-bus');
    const statusIcon = document.getElementById('conductor-status-icon');
    const statusTitle = document.getElementById('conductor-status-title');
    const statusDesc = document.getElementById('conductor-status-desc');
    const simBox = document.getElementById('sim-progress-box');
    const btnHandoff = document.getElementById('btn-trigger-handoff');

    btnToggle.addEventListener('click', () => {
        if (!isTracking) {
            startShift();
        } else {
            stopShift('manual');
        }
    });

    const updateBusPositionInState = (busId, lat, lng) => {
        const state = window.BhraminData.getState();
        const bus = state.buses.find(b => b.id === busId);
        if (bus) {
            bus.status = 'live';
            bus.lat = lat;
            bus.lng = lng;
            bus.lastSeen = 'just now';
            window.BhraminData.saveState(state);
        }

        // Post to Laravel backend API endpoint
        const numericBusId = parseInt(busId.replace('BUS-', '')) || 12;
        fetch('/api/conductor/location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ bus_id: numericBusId, lat: lat, lng: lng })
        }).catch(err => console.log('API sync notice:', err));
    };

    let lastPos = null;
    let lastTime = null;

    const startShift = () => {
        isTracking = true;
        const busId = busSelect.value;
        lastPos = null;
        lastTime = null;

        const profile = loadConductorProfile() || { name: 'Rajesh Sharma', phone: '9876543210', id: 1 };
        const numericBusId = parseInt(busId.replace('BUS-', '')) || 12;

        // Post shift start session to database
        fetch('/api/conductor/session/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                bus_id: numericBusId,
                conductor_id: profile.id,
                conductor_name: profile.name,
                conductor_phone: profile.phone
            })
        }).then(res => res.json())
          .then(data => console.log('Database Shift session started:', data))
          .catch(err => console.warn('Database start notice:', err));

        // Update UI
        btnToggle.textContent = '🔴 End Shift & Stop GPS Stream';
        btnToggle.style.background = '#DC2626';
        statusIcon.classList.add('active');
        statusTitle.textContent = `Shift Active on ${busId}`;
        statusDesc.innerHTML = `Broadcasting GPS position live to passenger network...<br><small id="gps-coords-display" style="color: var(--primary-blue); font-weight: 600; font-size: 0.85rem; display: block; margin-top: 6px;">Fetching GPS hardware signal...</small>`;
        simBox.style.display = 'block';
        busSelect.disabled = true;

        const pill = document.getElementById('gps-status-pill');
        const pillText = document.getElementById('gps-status-text');
        if (pill) pill.classList.add('active');
        if (pillText) pillText.textContent = 'GPS Live';

        document.getElementById('stat-speed').textContent = '0 km/h';

        // Use HTML5 Geolocation watchPosition if available
        if ("geolocation" in navigator) {
            watchId = navigator.geolocation.watchPosition((pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const now = Date.now();

                let liveSpeedKmH = 0;

                if (lastPos && lastTime) {
                    const timeDiffSec = (now - lastTime) / 1000;
                    const distKm = calculateDistance(lastPos.lat, lastPos.lng, lat, lng);
                    const distMeters = distKm * 1000;

                    // Filter out stationary GPS drift (movement less than 12 meters is noise -> 0 km/h)
                    if (distMeters >= 12 && timeDiffSec > 1) {
                        if (pos.coords.speed !== null && pos.coords.speed !== undefined && !isNaN(pos.coords.speed) && pos.coords.speed > 0) {
                            liveSpeedKmH = Math.round(pos.coords.speed * 3.6);
                        } else {
                            liveSpeedKmH = Math.round((distKm / timeDiffSec) * 3600);
                        }

                        if (liveSpeedKmH > 120 || liveSpeedKmH < 3) liveSpeedKmH = 0;

                        lastPos = { lat, lng };
                        lastTime = now;
                    } else if (timeDiffSec >= 4) {
                        // Device stationary for 4+ seconds
                        liveSpeedKmH = 0;
                        lastPos = { lat, lng };
                        lastTime = now;
                    } else {
                        liveSpeedKmH = 0;
                    }
                } else {
                    lastPos = { lat, lng };
                    lastTime = now;
                    liveSpeedKmH = 0;
                }

                const speedEl = document.getElementById('stat-speed');
                if (speedEl) speedEl.textContent = `${liveSpeedKmH} km/h`;

                const display = document.getElementById('gps-coords-display');
                if (display) display.textContent = `📍 Live Signal: Lat ${lat.toFixed(5)}, Lng ${lng.toFixed(5)} (Accuracy: ${pos.coords.accuracy.toFixed(0)}m)`;

                updateBusPositionInState(busId, lat, lng);
            }, (err) => {
                console.warn('Geolocation fallback to route simulation:', err.message);
                startSimulation(busId);
            }, {
                enableHighAccuracy: true,
                maximumAge: 1000,
                timeout: 10000
            });
        } else {
            startSimulation(busId);
        }
    };

    const startSimulation = (busId) => {
        let currentLat = 30.3200;
        let currentLng = 78.0370;

        locationInterval = setInterval(() => {
            currentLat += (Math.random() - 0.5) * 0.0015;
            currentLng += (Math.random() - 0.5) * 0.0015;

            const display = document.getElementById('gps-coords-display');
            if (display) display.textContent = `📍 Live Signal: Lat ${currentLat.toFixed(5)}, Lng ${currentLng.toFixed(5)}`;

            updateBusPositionInState(busId, currentLat, currentLng);
        }, 3000);
    };

    const stopShift = (reason = 'manual') => {
        isTracking = false;
        if (locationInterval) clearInterval(locationInterval);
        if (watchId) navigator.geolocation.clearWatch(watchId);

        const busId = busSelect.value;
        btnToggle.textContent = '🟢 Start Shift & Broadcast GPS';
        btnToggle.style.background = 'linear-gradient(135deg, var(--primary-blue), #0043CE)';
        statusIcon.classList.remove('active');
        statusTitle.textContent = 'Ready to Start Shift';
        statusDesc.textContent = 'Turn on GPS broadcast so passengers can see your live bus arrival time.';
        simBox.style.display = 'none';
        busSelect.disabled = false;

        const pill = document.getElementById('gps-status-pill');
        const pillText = document.getElementById('gps-status-text');
        if (pill) pill.classList.remove('active');
        if (pillText) pillText.textContent = 'GPS Standby';

        document.getElementById('stat-speed').textContent = '0 km/h';

        const state = window.BhraminData.getState();
        const bus = state.buses.find(b => b.id === busId);
        if (bus) {
            if (reason === 'geofence') {
                bus.status = 'error'; // Flagged for shift change handoff
                bus.errorReason = 'Session auto-ended at change point Ghanta Ghar.';
                alert(`📍 Reached Change Point (Ghanta Ghar)! Shift auto-ended. Bus ${busId} is now awaiting next conductor session.`);
            } else {
                bus.status = 'no_session';
            }
            window.BhraminData.saveState(state);
        }

        // Post stop shift session to database
        const numericBusId = parseInt(busId.replace('BUS-', '')) || 12;
        fetch('/api/conductor/session/stop', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                bus_id: numericBusId,
                session_id: window.activeConductorSessionId || 1,
                reason: (reason === 'geofence' ? 'geofence_auto' : 'manual')
            })
        }).catch(err => console.log('API stop notice:', err));
    };

    btnHandoff.addEventListener('click', () => {
        stopShift('geofence');
    });
});
