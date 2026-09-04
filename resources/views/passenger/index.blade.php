@extends('layouts.app')

@section('content')
<!-- First-Time Location Permission Modal Prompt -->
<div class="modal-overlay" id="location-permission-modal">
    <div class="permission-modal-card">
        <div class="permission-icon">📍</div>
        <h3 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Enable Location Access</h3>
        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 24px; line-height: 1.6;">
            BhraminSathi requires your location to discover live active buses running within a <strong>1 to 3 km range</strong> around you.
        </p>

        <div style="display: flex; flex-direction: column; gap: 10px;">
            <button class="btn-primary" id="btn-allow-location">
                <span>📍 Allow Location Access</span>
            </button>
            <button class="btn-secondary" id="btn-skip-location">
                Continue with Default Area (City Centre)
            </button>
        </div>
    </div>
</div>

<!-- Professional Centered Header Search Bar -->
<header class="passenger-header-bar">
    <div class="header-search-container">
        <div class="header-search-trigger" id="search-trigger-bar" onclick="openDestinationModal()">
            <span class="search-icon-left">📍</span>
            <input type="text" id="header-dest-display" readonly value="Where do you want to go? Select drop point..." style="cursor: pointer;">
            <button class="header-search-btn" type="button">
                <span>🔍</span>
                <span>Search</span>
            </button>
        </div>
    </div>
</header>

<!-- Main Responsive Map Canvas -->
<div class="passenger-container">
    <!-- Range Badge Overlay -->
    <div class="range-badge-overlay" id="range-indicator">
        <span>📡</span>
        <span id="range-text">Showing Buses in 1 – 3 km Range</span>
    </div>

    <!-- Route Filter Chips -->
    <div class="route-filters-overlay">
        <button class="filter-chip active" data-route="all">All Routes</button>
        <button class="filter-chip" data-route="12">Route 12</button>
        <button class="filter-chip" data-route="42">Route 42</button>
        <button class="filter-chip" data-route="7">Route 7</button>
    </div>

    <div id="passenger-map"></div>

    <!-- Selected Bus Drawer -->
    <div class="bus-drawer" id="bus-drawer">
        <div class="drawer-header">
            <div>
                <h3 id="drawer-bus-title" style="font-size: 1.25rem; font-weight: 700;">Bus 12</h3>
                <p id="drawer-bus-route" style="color: var(--text-secondary); font-size: 0.85rem;">City Centre ↔ Airport</p>
            </div>
            <div id="drawer-bus-badge" class="status-badge live">
                <span>●</span> <span id="badge-text">Live</span>
            </div>
        </div>

        <div id="drawer-live-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; background: var(--bg-light-blue); padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--border-blue);">
                <div>
                    <p style="font-weight: 600; color: var(--primary-blue); font-size: 0.85rem;" id="drawer-distance-text">📍 1.2 km away from you</p>
                    <p style="font-size: 0.75rem; color: var(--text-muted);">Broadcasting via Conductor GPS</p>
                </div>
                <div style="background: var(--surface-white); color: var(--primary-blue); font-weight: 700; padding: 6px 14px; border-radius: var(--radius-full); font-size: 0.85rem; border: 1px solid var(--border-blue);">
                    <span id="drawer-eta">4 min</span>
                </div>
            </div>

            <!-- Double Verification Human Safety Net Layer -->
            <div class="verification-card">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                    <div style="width: 24px; height: 24px; background: var(--primary-blue); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">👤</div>
                    <div>
                        <strong style="font-size: 0.85rem; color: var(--text-primary);">Verbal Verification Step</strong>
                    </div>
                </div>

                <div class="verification-step">
                    <div class="step-num">1</div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary);">Verify bus matches <strong id="verify-bus-no">Bus 12</strong></p>
                </div>
                <div class="verification-step">
                    <div class="step-num">2</div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary);">Ask conductor: <strong id="verify-question" style="color: var(--primary-blue);">"Airport ja raha hai?"</strong></p>
                </div>
                <div class="verification-step">
                    <div class="step-num">3</div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary);">Board once confirmed verbally</p>
                </div>
            </div>

            <button class="btn-primary" style="margin-top: 16px;" onclick="alert('Journey initiated! Keep your verbal confirmation ready when boarding.')">
                Get on this bus
            </button>
        </div>

        <!-- Honest Offline Content -->
        <div id="drawer-offline-content" style="display: none; text-align: center; padding: 16px 0;">
            <div style="font-size: 32px; margin-bottom: 8px;">⚠️</div>
            <h4 style="font-size: 1rem; color: var(--text-primary); margin-bottom: 6px;">No live tracking right now</h4>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 12px;">
                No conductor currently connected for this bus. Showing static route stop schedule.
            </p>
        </div>
    </div>
</div>

<!-- Two-Point Route Search Modal (Pickup & Drop Location) -->
<div class="modal-overlay" id="dest-select-modal" style="display: none;" onclick="closeDestinationModal(event)">
    <div class="search-modal-card" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="modal-top-bar">
            <button class="back-btn" onclick="closeDestinationModal()">←</button>
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Search Route</h3>
            <div style="width: 24px;"></div>
        </div>

        <!-- Input Fields (Pickup & Drop) -->
        <div class="route-input-group">
            <div class="input-row">
                <span class="icon-pickup">🟢</span>
                <input type="text" id="pickup-location-input" placeholder="Your Pickup Location (e.g. Jaipur Junction)" value="Current Location (Jaipur)">
                <button class="clear-input-btn" onclick="document.getElementById('pickup-location-input').value=''">✕</button>
            </div>
            <div class="input-divider"></div>
            <div class="input-row">
                <span class="icon-drop">🔴</span>
                <input type="text" id="drop-location-input" placeholder="Where to? Type or choose bus below..." onkeyup="filterBusSearchResults()">
                <button class="clear-input-btn" onclick="document.getElementById('drop-location-input').value=''; filterBusSearchResults();">✕</button>
            </div>
        </div>

        <!-- Dynamic Live Bus & Destination Search Results List -->
        <div id="live-bus-results-container" class="live-results-dropdown">
            <div class="search-section-title">ALL AVAILABLE BUSES & DESTINATIONS</div>
            <div id="bus-results-list" class="bus-results-list-group">
                <!-- Dynamically populated via passenger.js -->
            </div>
        </div>

        <button class="btn-primary" style="margin-bottom: 20px;" onclick="applyTwoPointSearch()">
            <span>🔎 Search Live Buses</span>
        </button>

        <!-- Recent Searches Section -->
        <div class="search-section-title">POPULAR JAIPUR ROUTES</div>
        <div class="recent-searches-list">
            <button class="recent-chip" onclick="selectQuickRoute('Jaipur Junction', 'Sanganer Airport', 12)">
                <span>📍 Jaipur Junction ↔ Airport (Route 12)</span>
            </button>
            <button class="recent-chip" onclick="selectQuickRoute('Sindhi Camp ISBT', 'Sitapura Ind. Area', 42)">
                <span>📍 Sindhi Camp ↔ Sitapura (Route 42)</span>
            </button>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border-light); margin: 18px 0;">

        <!-- Popular City / Stop Suggestions -->
        <div class="search-section-title">POPULAR STOPS IN JAIPUR</div>
        <div class="popular-cities-grid">
            <div class="city-card" onclick="selectDropPoint('Sanganer Airport', 12)">
                <span class="city-icon">🛬</span>
                <span class="city-name">Sanganer Airport</span>
            </div>
            <div class="city-card" onclick="selectDropPoint('Jaipur Junction', 12)">
                <span class="city-icon">🚉</span>
                <span class="city-name">Jaipur Junction</span>
            </div>
            <div class="city-card" onclick="selectDropPoint('Sindhi Camp ISBT', 42)">
                <span class="city-icon">🚌</span>
            </div>
            <div class="city-card" onclick="selectDropPoint('Kota Highway', 12)">
                <span class="city-icon">🛣️</span>
                <span class="city-name">Kota Highway</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/data.js"></script>
<script src="/js/passenger.js"></script>
@endpush
