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

document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('admin-buses-tbody');
    if (!tbody) return;

    const renderAdminTable = () => {
        const state = window.BhraminData.getState();
        const buses = state.buses;

        let liveCount = 0;
        let offlineCount = 0;
        let errorCount = 0;

        tbody.innerHTML = '';

        buses.forEach(bus => {
            if (bus.status === 'live') liveCount++;
            if (bus.status === 'no_session') offlineCount++;
            if (bus.status === 'error') errorCount++;

            const tr = document.createElement('tr');
            if (bus.status === 'error') tr.className = 'highlight-error';

            let statusBadge = '';
            let actionBtn = `<a href="/admin/bus/${bus.id}" class="btn-secondary">Details</a>`;

            if (bus.status === 'live') {
                statusBadge = '<span class="status-badge live">● Live</span>';
            } else if (bus.status === 'error') {
                statusBadge = '<span class="status-badge error">⚠️ Needs Attention</span>';
                actionBtn = `<a href="/admin/bus/${bus.id}" class="btn-danger">Inspect</a>`;
            } else {
                statusBadge = '<span class="status-badge offline">Offline</span>';
            }

            const locationText = bus.lat ? `Lat: ${bus.lat.toFixed(4)}, Lng: ${bus.lng.toFixed(4)}` : 'Unknown';

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

    renderAdminTable();

    // Refresh Admin table live every 2 seconds
    setInterval(renderAdminTable, 2000);
});
