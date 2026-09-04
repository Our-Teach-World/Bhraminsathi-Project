/**
 * BhraminSathi — Professional Passenger Companion JS
 * Features: First-time location prompt, 1-3km distance calculation, header drop point search
 */

// Haversine Formula for Distance Calculation (in km)
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

function openDestinationModal() {
    document.getElementById('dest-select-modal').style.display = 'flex';
    populateBusSearchResults();
}

function filterBusSearchResults() {
    const input = document.getElementById('drop-location-input');
    if (!input) return;
    const filter = input.value.toLowerCase().trim();
    const items = document.querySelectorAll('#bus-results-list .bus-result-item');

    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(filter) ? 'flex' : 'none';
    });
}

function populateBusSearchResults() {
    const listContainer = document.getElementById('bus-results-list');
    if (!listContainer) return;

    const state = window.BhraminData ? window.BhraminData.getState() : null;
    const buses = state && state.buses ? state.buses : [
        { id: 'BUS-12', busNumber: 'UK-07-PA-1234', routeName: 'City Centre ↔ Airport', routeId: 12, heading: 'Airport', status: 'live' },
        { id: 'BUS-42', busNumber: 'UK-07-PA-4242', routeName: 'ISBT ↔ Rajpur Road', routeId: 42, heading: 'Rajpur Road', status: 'live' },
        { id: 'BUS-07', busNumber: 'UK-07-PA-0707', routeName: 'Railway Station ↔ ISBT', routeId: 7, heading: 'Railway Station', status: 'live' },
        { id: 'BUS-08', busNumber: 'UK-07-PA-0808', routeName: 'ISBT ↔ Rajpur Road', routeId: 42, heading: 'Ghanta Ghar', status: 'error' }
    ];

    listContainer.innerHTML = '';
    buses.forEach(b => {
        const div = document.createElement('div');
        div.className = 'bus-result-item';
        div.onclick = () => selectDropPoint(b.heading || b.routeName, b.routeId || 12);

        let statusText = b.status === 'live' ? '🟢 Live' : '🔴 Standby';

        div.innerHTML = `
            <div class="bus-result-info">
                <span class="bus-badge-tag">${b.id}</span>
                <div>
                    <div class="bus-result-title">${b.heading ? 'Drop: ' + b.heading : b.id}</div>
                    <div class="bus-result-route">${b.routeName} (${b.busNumber})</div>
                </div>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);">${statusText}</span>
        `;
        listContainer.appendChild(div);
    });
}

function closeDestinationModal(e) {
    if (e && e.target !== document.getElementById('dest-select-modal')) return;
    document.getElementById('dest-select-modal').style.display = 'none';
}

function selectDropPoint(name, routeId) {
    document.getElementById('drop-location-input').value = name;
    applyTwoPointSearch(routeId);
}

function selectQuickRoute(pickup, drop, routeId) {
    document.getElementById('pickup-location-input').value = pickup;
    document.getElementById('drop-location-input').value = drop;
    applyTwoPointSearch(routeId);
}

function applyTwoPointSearch(forcedRouteId = null) {
    const pickup = document.getElementById('pickup-location-input').value || 'Current Location';
    const drop = document.getElementById('drop-location-input').value || 'Any Destination';

    document.getElementById('header-dest-display').value = `${pickup} ➔ ${drop}`;
    document.getElementById('dest-select-modal').style.display = 'none';

    if (window.renderBuses) {
        const routeIdToUse = forcedRouteId ? forcedRouteId.toString() : '12';
        window.renderBuses(routeIdToUse);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const defaultCenter = [30.3240, 78.0415];
    let userCoords = { lat: 30.3240, lng: 78.0415 };
    let locationGranted = false;

    const map = L.map('passenger-map', { zoomControl: false }).setView(defaultCenter, 14);
    L.control.zoom({ position: 'topright' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap | BhraminSathi'
    }).addTo(map);

    let currentMarkers = [];
    let currentPolyline = null;
    let userLocationMarker = null;
    let activeRouteFilter = 'all';

    // Handle First-Time Location Permission & Persistence
    const locModal = document.getElementById('location-permission-modal');
    const btnAllowLoc = document.getElementById('btn-allow-location');
    const btnSkipLoc = document.getElementById('btn-skip-location');

    const savedChoice = localStorage.getItem('bhramin_location_choice');
    const savedCoords = localStorage.getItem('bhramin_saved_coords');

    if (savedCoords) {
        try {
            const parsed = JSON.parse(savedCoords);
            userCoords.lat = parsed.lat;
            userCoords.lng = parsed.lng;
        } catch(e) {}
    }

    const activateUserLocation = (lat, lng) => {
        locationGranted = true;
        userCoords.lat = lat;
        userCoords.lng = lng;

        map.setView([lat, lng], 14);

        const userIcon = L.divIcon({
            className: 'user-pin-marker',
            html: `<div style="background: #0F62FE; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 8px rgba(15,98,254,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        if (userLocationMarker) map.removeLayer(userLocationMarker);
        userLocationMarker = L.marker([lat, lng], { icon: userIcon })
            .addTo(map)
            .bindPopup("<strong>📍 You are here</strong>");

        locModal.style.display = 'none';
        renderBuses();
    };

    if (savedChoice === 'granted') {
        locModal.style.display = 'none';
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition((pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                localStorage.setItem('bhramin_saved_coords', JSON.stringify({ lat, lng }));
                activateUserLocation(lat, lng);
            }, () => {
                if (userCoords.lat) activateUserLocation(userCoords.lat, userCoords.lng);
                else renderBuses();
            });
        } else {
            renderBuses();
        }
    } else if (savedChoice === 'skipped') {
        locModal.style.display = 'none';
        renderBuses();
    } else {
        // First-time visit: show modal
        locModal.style.display = 'flex';
    }

    btnAllowLoc.addEventListener('click', () => {
        btnAllowLoc.disabled = true;
        btnAllowLoc.innerHTML = '<span>⏳ Requesting Location...</span>';

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    localStorage.setItem('bhramin_location_choice', 'granted');
                    localStorage.setItem('bhramin_saved_coords', JSON.stringify({ lat, lng }));
                    activateUserLocation(lat, lng);
                },
                (err) => {
                    console.warn("Geolocation permission error:", err);
                    localStorage.setItem('bhramin_location_choice', 'granted');
                    activateUserLocation(userCoords.lat, userCoords.lng);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                }
            );
        } else {
            localStorage.setItem('bhramin_location_choice', 'granted');
            activateUserLocation(userCoords.lat, userCoords.lng);
        }
    });

    btnSkipLoc.addEventListener('click', () => {
        localStorage.setItem('bhramin_location_choice', 'skipped');
        locModal.style.display = 'none';
        renderBuses();
    });

    // Custom Marker Pin Generator using Modern Blue Bus Icon
    const createBusIcon = (status, number) => {
        let badgeBg = '#0F62FE'; // Live Blue
        if (status === 'error') badgeBg = '#DC2626'; // Error Red
        if (status === 'no_session') badgeBg = '#9CA3AF'; // Offline Grey

        return L.divIcon({
            className: 'custom-bus-pin-container',
            html: `
                <div style="position: relative; width: 62px; height: 52px; display: flex; align-items: center; justify-content: center;">
                    <img src="/images/modern-public-bus-icon-vector.jpg" style="width: 58px; height: 44px; object-fit: contain; filter: drop-shadow(0 6px 10px rgba(0,0,0,0.3)); border-radius: 6px;">
                    <div style="position: absolute; top: -4px; right: -2px; background: ${badgeBg}; color: white; border-radius: 12px; padding: 2px 7px; font-weight: 800; font-size: 10px; border: 2px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                        ${number.replace('BUS-', '')}
                    </div>
                </div>
            `,
            iconSize: [62, 52],
            iconAnchor: [31, 26]
        });
    };

    window.renderBuses = (filteredRouteId = activeRouteFilter) => {
        activeRouteFilter = filteredRouteId;

        // Fetch live bus data from Laravel backend API endpoint first
        fetch(`/api/passenger/buses?route_id=${filteredRouteId}`)
            .then(res => res.json())
            .then(apiRes => {
                let busesList = [];
                if (apiRes && apiRes.status === 'success' && apiRes.data && apiRes.data.length > 0) {
                    busesList = apiRes.data.map(b => ({
                        id: b.id,
                        busNumber: b.bus_number,
                        routeName: b.route_name,
                        status: b.status,
                        lat: b.lat,
                        lng: b.lng,
                        heading: b.route_name.split('↔')[1] || 'Destination',
                        routeId: b.route_id || 12
                    }));
                } else {
                    busesList = window.BhraminData.getState().buses;
                }
                drawBusesOnMap(busesList, filteredRouteId);
            })
            .catch(err => {
                console.log('Using local state fallback:', err);
                drawBusesOnMap(window.BhraminData.getState().buses, filteredRouteId);
            });
    };

    const drawBusesOnMap = (busesList, filteredRouteId) => {
        // Clear existing markers & polyline
        currentMarkers.forEach(m => map.removeLayer(m));
        currentMarkers = [];
        if (currentPolyline) map.removeLayer(currentPolyline);

        // Calculate distance & filter buses within 1 to 15 km range if location granted
        const busesToDisplay = busesList.filter(b => {
            if (filteredRouteId !== 'all' && b.routeId && b.routeId !== parseInt(filteredRouteId)) return false;
            
            if (b.lat && b.lng && locationGranted) {
                const dist = calculateDistance(userCoords.lat, userCoords.lng, b.lat, b.lng);
                b.calculatedDistKm = dist;
                return dist <= 15.0; // Expand range to 15km for city testing
            }
            return true;
        });

        // Update range badge
        const rangeText = document.getElementById('range-text');
        if (rangeText) {
            rangeText.textContent = locationGranted ? 
                `Buses in Range (${busesToDisplay.length} nearby)` : 
                `Showing All Active Buses`;
        }

        // Draw active route trajectory
        const selectedRoute = window.BhraminData.routes.find(r => r.id === parseInt(filteredRouteId)) || window.BhraminData.routes[0];
        if (selectedRoute && selectedRoute.path) {
            currentPolyline = L.polyline(selectedRoute.path, {
                color: '#0F62FE',
                weight: 5,
                opacity: 0.7,
                dashArray: '8, 8'
            }).addTo(map);
        }

        busesToDisplay.forEach(bus => {
            if (bus.lat && bus.lng) {
                const marker = L.marker([bus.lat, bus.lng], {
                    icon: createBusIcon(bus.status, bus.id)
                }).addTo(map);

                marker.on('click', () => {
                    openBusDrawer(bus);
                });

                currentMarkers.push(marker);
            }
        });
    };

    const openBusDrawer = (bus) => {
        const drawer = document.getElementById('bus-drawer');
        const liveContent = document.getElementById('drawer-live-content');
        const offlineContent = document.getElementById('drawer-offline-content');

        document.getElementById('drawer-bus-title').textContent = `${bus.id} (${bus.busNumber})`;
        document.getElementById('drawer-bus-route').textContent = bus.routeName;
        document.getElementById('verify-bus-no').textContent = bus.id;
        document.getElementById('verify-question').textContent = `"${bus.heading} ja raha hai?"`;

        const distKm = bus.calculatedDistKm ? bus.calculatedDistKm.toFixed(1) : '1.2';
        document.getElementById('drawer-distance-text').textContent = `📍 ${distKm} km away from your location`;

        const badge = document.getElementById('drawer-bus-badge');
        const badgeText = document.getElementById('badge-text');

        if (bus.status === 'live') {
            badge.className = 'status-badge live';
            badgeText.textContent = 'Live';
            document.getElementById('drawer-eta').textContent = `${bus.etaMinutes || 4} min`;
            liveContent.style.display = 'block';
            offlineContent.style.display = 'none';
        } else if (bus.status === 'error') {
            badge.className = 'status-badge error';
            badgeText.textContent = 'Needs Attention';
            liveContent.style.display = 'none';
            offlineContent.style.display = 'block';
        } else {
            badge.className = 'status-badge offline';
            badgeText.textContent = 'Offline';
            liveContent.style.display = 'none';
            offlineContent.style.display = 'block';
        }

        drawer.classList.add('open');
    };

    // Route filter buttons listener
    document.querySelectorAll('.filter-chip').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            e.target.classList.add('active');
            const routeId = e.target.getAttribute('data-route');
            renderBuses(routeId);
        });
    });

    renderBuses();

    // Refresh every 2 seconds for live conductor GPS movement
    setInterval(() => {
        renderBuses();
    }, 2000);
});
