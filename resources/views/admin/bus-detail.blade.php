@extends('layouts.app')

@section('content')
<div class="admin-layout">
    <aside class="admin-sidebar">
        <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.5px; margin-bottom: 8px;">Admin Navigation</h4>
        <a href="/admin/dashboard" class="nav-item">📊 Live Overview</a>
        <a href="/admin/bus/BUS-08" class="nav-item active">🚍 Bus Detail</a>
        <a href="/admin/session-logs" class="nav-item">📋 Session Logs</a>
    </aside>

    <main class="admin-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <h1 style="font-size: 1.6rem; font-weight: 700;">BUS-08</h1>
                    <span class="status-badge error">Needs Attention</span>
                </div>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Route: ISBT ↔ Rajpur Road | Vehicle: UK-07-PA-0808</p>
            </div>
            <a href="/admin" class="btn-secondary">← Back to Overview</a>
        </div>

        <!-- Alert Notification Box -->
        <div style="background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="font-size: 32px; color: #DC2626;">🚨</div>
                <div>
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: #DC2626; margin-bottom: 4px;">Session not resumed after change point</h4>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Bus BUS-08 passed the handoff point at Ghanta Ghar 8 minutes ago. No new conductor has connected.</p>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn-danger" id="btn-admin-remind" onclick="triggerReminder()">🔔 Send Reminder to Conductor</button>
                <button class="btn-secondary" onclick="resolveBusError()">✅ Mark Resolved</button>
            </div>
        </div>

        <!-- Detail Breakdown Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Today's Handoff History Timeline -->
            <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--border-light); padding: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;">Today's Handoff Sessions</h3>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="border-left: 3px solid var(--primary-blue); padding-left: 16px;">
                        <strong style="color: var(--text-primary);">Session 1: Mohan D.</strong>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">05:30 AM – 07:10 AM | ISBT ↔ Ghanta Ghar (Auto-ended at geofence)</p>
                    </div>
                    <div style="border-left: 3px solid #DC2626; padding-left: 16px; background: #FEF2F2; padding: 12px; border-radius: var(--radius-sm);">
                        <strong style="color: #DC2626;">Session 2: Suresh M. (Stalled)</strong>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">07:10 AM – In Progress | Ghanta Ghar ↔ Rajpur Rd</p>
                        <p style="font-size: 0.8rem; color: #DC2626; margin-top: 4px;">⚠️ Device signal lost or conductor failed to start shift.</p>
                    </div>
                </div>
            </div>

            <!-- Route Info Snapshot -->
            <div style="background: white; border-radius: var(--radius-md); border: 1px solid var(--border-light); padding: 24px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;">Route Info</h3>
                <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted)">Start Point:</span>
                        <strong>ISBT</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted)">Change Point:</span>
                        <strong style="color: var(--primary-blue)">Ghanta Ghar</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted)">End Point:</span>
                        <strong>Rajpur Road</strong>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="/js/data.js"></script>
<script>
    function triggerReminder() {
        const btn = document.getElementById('btn-admin-remind');
        btn.textContent = '⏳ Sending SMS Nudge...';
        setTimeout(() => {
            alert('🔔 Automated SMS & App notification sent to registered conductor Suresh M. (+91 98765 11223)');
            btn.textContent = '✓ Reminder Sent (Retry in 2m)';
            btn.style.background = '#059669';
        }, 1200);
    }

    function resolveBusError() {
        const state = window.BhraminData.getState();
        const bus = state.buses.find(b => b.id === 'BUS-08');
        if (bus) {
            bus.status = 'live';
            bus.lastSeen = 'just now';
            window.BhraminData.saveState(state);
            alert('✅ Bus BUS-08 manually marked as Resolved!');
            window.location.href = '/admin';
        }
    }
</script>
@endpush
