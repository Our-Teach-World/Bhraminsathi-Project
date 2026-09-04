/**
 * BhraminSathi — Admin Dashboard Client JS
 */

function filterAdminTable() {
    const input = document.getElementById('table-search-input');
    if (!input) return;
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#admin-buses-tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

function adminTerminateBus(busId) {
    if (!confirm(`Are you sure you want to forcibly terminate live status for ${busId}?`)) return;

    // Update state locally first
    const state = window.BhraminData ? window.BhraminData.getState() : null;
    if (state && state.buses) {
        const bus = state.buses.find(b => b.id === busId);
        if (bus) {
            bus.status = 'no_session';
            window.BhraminData.saveState(state);
        }
    }

    // Call API backend endpoint
    fetch(`/api/admin/buses/${busId}/terminate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        alert(`🛑 Bus ${busId} live session terminated successfully.`);
        if (window.renderAdminTable) window.renderAdminTable();
    })
    .catch(err => {
        alert(`Bus ${busId} status set to offline.`);
        if (window.renderAdminTable) window.renderAdminTable();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('admin-buses-tbody');
    if (!tbody) return;

    const renderAdminTable = () => {
        fetch('/api/passenger/buses')
            .then(res => res.json())
            .then(apiRes => {
                let buses = [];
                if (apiRes && apiRes.status === 'success' && apiRes.data && apiRes.data.length > 0) {
                    buses = apiRes.data.map(b => ({
                        id: b.id,
                        busNumber: b.bus_number,
                        routeName: b.route_name,
                        conductorName: 'Rajesh S.',
                        status: b.status,
                        lat: b.lat,
                        lng: b.lng,
                        lastSeen: b.last_updated || 'just now'
                    }));
                } else {
                    buses = window.BhraminData.getState().buses;
                }
                drawAdminRows(buses);
            })
            .catch(err => {
                drawAdminRows(window.BhraminData.getState().buses);
            });
    };

    const drawAdminRows = (buses) => {
        let liveCount = 0;
        let offlineCount = 0;
        let errorCount = 0;

        tbody.innerHTML = '';

        buses.forEach(bus => {
            if (bus.status === 'live') liveCount++;
            if (bus.status === 'no_session' || bus.status === 'offline') offlineCount++;
            if (bus.status === 'error') errorCount++;

            const tr = document.createElement('tr');
            if (bus.status === 'error') tr.className = 'highlight-error';

            let statusBadge = '';
            let actionBtn = `<a href="/admin/bus/${bus.id}" class="btn-secondary">Details</a>`;

            if (bus.status === 'live') {
                statusBadge = '<span class="status-badge live">● Live</span>';
                actionBtn = `
                    <a href="/admin/bus/${bus.id}" class="btn-secondary" style="margin-right: 4px;">Details</a>
                    <button class="btn-danger" style="padding: 6px 10px; font-size: 0.78rem;" onclick="adminTerminateBus('${bus.id}')">🛑 Terminate</button>
                `;
            } else if (bus.status === 'error') {
                statusBadge = '<span class="status-badge error">⚠️ Needs Attention</span>';
                actionBtn = `<a href="/admin/bus/${bus.id}" class="btn-danger">Inspect</a>`;
            } else {
                statusBadge = '<span class="status-badge offline">Offline</span>';
            }

            const locationText = bus.lat ? `Lat: ${Number(bus.lat).toFixed(4)}, Lng: ${Number(bus.lng).toFixed(4)}` : 'Unknown';

            tr.innerHTML = `
                <td><strong>${bus.id}</strong> <br><small style="color: var(--text-muted)">${bus.busNumber}</small></td>
                <td>${bus.routeName}</td>
                <td>${bus.conductorName}</td>
                <td><small style="font-family: monospace; color: var(--primary-blue)">${locationText}</small></td>
                <td>${bus.lastSeen}</td>
                <td>${statusBadge}</td>
                <td>${actionBtn}</td>
            `;

            tbody.appendChild(tr);
        });

        document.getElementById('kpi-total').textContent = buses.length;
        document.getElementById('kpi-live').textContent = liveCount;
        document.getElementById('kpi-offline').textContent = offlineCount;
        document.getElementById('kpi-error').textContent = errorCount;
    };

    window.renderAdminTable = renderAdminTable;
    renderAdminTable();

    // Refresh Admin table live every 2 seconds from backend API
    setInterval(renderAdminTable, 2000);
});
