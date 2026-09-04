@extends('layouts.app')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 8px;">Admin Navigation</h4>
        <a href="/admin/dashboard" class="nav-item">📊 Live Overview</a>
        <a href="/admin/bus/BUS-08" class="nav-item">🚍 Bus Detail</a>
        <a href="/admin/session-logs" class="nav-item active">📋 Session Logs</a>
    </aside>

    <main class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 1.6rem; font-weight: 700;">Session Log</h1>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Historical audit trail of all conductor shifts & shift handoff events</p>
            </div>
        </div>

        <div class="admin-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>Bus</th>
                        <th>Route</th>
                        <th>Conductor</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>S-001</td>
                        <td><strong>BUS-42</strong></td>
                        <td>Rajpur Rd ↔ Clock Tower</td>
                        <td>Ramesh K.</td>
                        <td>06:00 AM</td>
                        <td>08:30 AM</td>
                        <td>2h 30m</td>
                        <td><span class="status-badge offline">Completed</span></td>
                    </tr>
                    <tr>
                        <td>S-002</td>
                        <td><strong>BUS-17</strong></td>
                        <td>Prem Nagar ↔ ISBT</td>
                        <td>Anil S.</td>
                        <td>06:20 AM</td>
                        <td>Active</td>
                        <td>In Progress</td>
                        <td><span class="status-badge live">● Live</span></td>
                    </tr>
                    <tr class="highlight-error">
                        <td>S-004</td>
                        <td><strong style="color: #DC2626">BUS-08</strong></td>
                        <td>Ghanta Ghar ↔ Rajpur Rd</td>
                        <td>Suresh M.</td>
                        <td>07:10 AM</td>
                        <td>Stalled</td>
                        <td>8 mins stalled</td>
                        <td><span class="status-badge error">⚠️ Stalled Handoff</span></td>
                    </tr>
                    <tr>
                        <td>S-005</td>
                        <td><strong>BUS-31</strong></td>
                        <td>Clock Tower ↔ Rispana</td>
                        <td>Deepak R.</td>
                        <td>07:00 AM</td>
                        <td>Active</td>
                        <td>In Progress</td>
                        <td><span class="status-badge live">● Live</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection
