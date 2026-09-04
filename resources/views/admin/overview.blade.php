@extends('layouts.app')

@section('content')
<div class="admin-app-container">
    <!-- Top Admin App Bar -->
    <header class="admin-top-nav">
        <div class="admin-brand">
            <span class="brand-icon">🛡️</span>
            <div>
                <h2 class="brand-title">BhraminSathi Command Center</h2>
                <span class="brand-subtitle">Public Transit Fleet & Handoff Management</span>
            </div>
        </div>
        <div class="admin-user-profile">
            <span class="status-indicator-dot"></span>
            <span>Fleet Control • City Operations</span>
            <div class="admin-avatar">⚙️</div>
        </div>
    </header>

    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-section-title">MAIN MENU</div>
            <a href="/admin/dashboard" class="admin-nav-link {{ request()->is('admin*') && !request()->is('admin/bus*') && !request()->is('admin/session-logs*') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Live Fleet Overview</span>
            </a>
            <a href="/admin/bus/BUS-08" class="admin-nav-link {{ request()->is('admin/bus*') ? 'active' : '' }}">
                <span class="nav-icon">🚍</span>
                <span>Bus Detail & GPS</span>
            </a>
            <a href="/admin/session-logs" class="admin-nav-link {{ request()->is('admin/session-logs*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span>Shift Session Logs</span>
            </a>

            <div class="sidebar-section-title" style="margin-top: 24px;">SYSTEM STATUS</div>
            <div class="system-health-box">
                <div class="health-item">
                    <span>GPS Telemetry</span>
                    <span class="health-badge status-good">Operational</span>
                </div>
                <div class="health-item">
                    <span>Geofence Engine</span>
                    <span class="health-badge status-good">Active</span>
                </div>
            </div>
        </aside>

        <!-- Main Dashboard View Area -->
        <main class="admin-content">
            <!-- Header Title Row -->
            <div class="admin-page-header">
                <div>
                    <h1 class="page-title">Live Fleet Dashboard</h1>
                    <p class="page-subtitle">Real-time GPS tracking, conductor shift sessions & change-point alerts</p>
                </div>
                <div class="header-action-group">
                    <span class="pulse-live-badge">🔴 Live Syncing</span>
                    <button class="btn-refresh" onclick="location.reload()">🔄 Refresh Data</button>
                </div>
            </div>

            <!-- Enhanced Metric KPI Grid -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-info">
                        <span class="kpi-label">TOTAL FLEET BUSES</span>
                        <span class="kpi-val" id="kpi-total">24</span>
                        <span class="kpi-sub">Active City Routes</span>
                    </div>
                    <div class="kpi-icon-circle blue">🚌</div>
                </div>

                <div class="kpi-card green-accent">
                    <div class="kpi-info">
                        <span class="kpi-label">LIVE BROADCASTING</span>
                        <span class="kpi-val text-green" id="kpi-live">17</span>
                        <span class="kpi-sub">Conductors Connected</span>
                    </div>
                    <div class="kpi-icon-circle green">📶</div>
                </div>

                <div class="kpi-card gray-accent">
                    <div class="kpi-info">
                        <span class="kpi-label">STANDBY / NO SESSION</span>
                        <span class="kpi-val text-gray" id="kpi-offline">4</span>
                        <span class="kpi-sub">Awaiting Shift Login</span>
                    </div>
                    <div class="kpi-icon-circle gray">💤</div>
                </div>

                <div class="kpi-card red-accent">
                    <div class="kpi-info">
                        <span class="kpi-label">NEEDS ATTENTION</span>
                        <span class="kpi-val text-red" id="kpi-error">3</span>
                        <span class="kpi-sub">Stalled Shift Handoffs</span>
                    </div>
                    <div class="kpi-icon-circle red">⚠️</div>
                </div>
            </div>

            <!-- Stalled Shift Intervention Banner -->
            <div class="intervention-banner">
                <div class="banner-header">
                    <div class="banner-title">
                        <span>⚠️</span>
                        <span>Shift Handoff Interventions (Geofenced Change-Points)</span>
                    </div>
                    <span class="status-badge error">2 Pending Actions</span>
                </div>
                <div class="intervention-item">
                    <div>
                        <strong class="bus-tag">BUS-08</strong> (RJ-14-PA-0808 • Sindhi Camp ISBT ↔ Sitapura Ind. Area)
                        <p class="intervention-text">Passed change-point B2 Bypass Junction 8 mins ago. Shift auto-expired. Conductor shift handoff pending.</p>
                    </div>
                    <a href="/admin/bus/BUS-08" class="btn-inspect-action">Inspect & Dispatch SMS</a>
                </div>
            </div>

            <!-- Buses Monitoring Data Table Card -->
            <div class="admin-card">
                <div class="card-table-header">
                    <h3 class="table-title">Bus Fleet Operational Monitoring</h3>
                    <div class="table-search-box">
                        <span>🔍</span>
                        <input type="text" id="table-search-input" placeholder="Search Bus No, Route, Conductor..." onkeyup="filterAdminTable()">
                    </div>
                </div>

                <div class="admin-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>BUS VEHICLE</th>
                                <th>ASSIGNED ROUTE</th>
                                <th>CONDUCTOR</th>
                                <th>LIVE GPS LOCATION</th>
                                <th>LAST SIGNAL</th>
                                <th>SHIFT STATUS</th>
                                <th>COMMAND ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="admin-buses-tbody">
                            <!-- Populated dynamically via admin.js -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/data.js"></script>
<script src="/js/admin.js"></script>
@endpush
