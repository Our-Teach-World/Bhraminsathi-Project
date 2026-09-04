@extends('layouts.app')

@section('content')
<div class="conductor-app-wrapper">
    <!-- Sleek Top Header -->
    <header class="conductor-header">
        <div class="conductor-profile-info">
            <div class="conductor-avatar">👨‍✈️</div>
            <div>
                <h3 class="conductor-name">Rajesh Sharma</h3>
                <span class="conductor-id-badge">ID: COND-9042 • Shift Active</span>
            </div>
        </div>
        <div class="gps-status-pill" id="gps-status-pill">
            <span class="pill-dot"></span>
            <span id="gps-status-text">GPS Standby</span>
        </div>
    </header>

    <!-- Main Mobile Dashboard Body -->
    <main class="conductor-main-content">
        <!-- Bus & Route Selection Card -->
        <div class="conductor-card-widget">
            <div class="widget-header">
                <span class="widget-icon">🚌</span>
                <div>
                    <h4 class="widget-title">Shift Assignment</h4>
                    <p class="widget-subtitle">Select your active bus vehicle & route</p>
                </div>
            </div>

            <div class="form-group-custom">
                <label>Assigned Bus Vehicle:</label>
                <select id="select-bus" class="custom-select-input">
                    <option value="BUS-12">BUS-12 (UK-07-PA-1234) — City Centre ↔ Airport</option>
                    <option value="BUS-42">BUS-42 (UK-07-PA-4242) — ISBT ↔ Rajpur Road</option>
                    <option value="BUS-08">BUS-08 (UK-07-PA-0808) — Stalled @ Ghanta Ghar</option>
                </select>
            </div>

            <div class="change-point-box">
                <div class="change-point-header">
                    <span>📍 Shift Change-Point:</span>
                    <strong id="change-point-name">Ghanta Ghar (Geofenced)</strong>
                </div>
                <p class="change-point-sub">
                    GPS broadcasting auto-expires upon reaching change-point for handover.
                </p>
            </div>
        </div>

        <!-- Live Shift Tracking Control Card -->
        <div class="conductor-card-widget shift-control-card">
            <div class="pulse-icon-large" id="conductor-status-icon">
                🚌
            </div>

            <h3 id="conductor-status-title" class="shift-status-heading">Ready to Start Shift</h3>
            <p id="conductor-status-desc" class="shift-status-sub">
                Turn on GPS broadcast so passengers can see your live bus arrival time.
            </p>

            <!-- Quick Telemetry Stats Grid -->
            <div class="telemetry-stats-grid" id="telemetry-grid">
                <div class="stat-card">
                    <span class="stat-value" id="stat-speed">0 km/h</span>
                    <span class="stat-label">Live Speed</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value" id="stat-passengers">34</span>
                    <span class="stat-label">Pax Boarded</span>
                </div>
                <div class="stat-card">
                    <span class="stat-value" id="stat-geofence">1.8 km</span>
                    <span class="stat-label">To Handoff</span>
                </div>
            </div>

            <button id="btn-toggle-shift" class="btn-conductor-primary">
                <span>🟢 Start Shift & Broadcast GPS</span>
            </button>

            <!-- Interactive Geofence Simulation Button -->
            <div id="sim-progress-box" style="display: none; margin-top: 16px;">
                <button id="btn-trigger-handoff" class="btn-conductor-secondary">
                    <span>🏁 Arrive at Change-Point (Trigger Handoff)</span>
                </button>
            </div>
        </div>
    </main>

    <!-- Conductor Mobile Bottom Navigation -->
    <nav class="conductor-bottom-nav">
        <div class="nav-tab active">
            <span class="nav-icon">📡</span>
            <span class="nav-label">Live GPS</span>
        </div>
        <div class="nav-tab" onclick="alert('Conductor Helpline: +91 1800-123-456')">
            <span class="nav-icon">📞</span>
            <span class="nav-label">Support</span>
        </div>
    </nav>
</div>
@endsection

@push('scripts')
<script src="/js/data.js"></script>
<script src="/js/conductor.js"></script>
@endpush
