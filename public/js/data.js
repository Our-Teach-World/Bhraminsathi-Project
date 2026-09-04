/**
 * BhraminSathi — Shared State & Database Data Engine
 */

window.BhraminData = {
    routes: [
        {
            id: 12,
            name: "City Centre ↔ Airport",
            start: "City Centre",
            end: "Airport",
            changePointName: "Ghanta Ghar",
            changePointCoords: [30.3240, 78.0415],
            path: [
                [30.3165, 78.0322], // City Centre
                [30.3200, 78.0370],
                [30.3240, 78.0415], // Change Point: Ghanta Ghar
                [30.3280, 78.0460],
                [30.3340, 78.0510]  // Airport
            ]
        },
        {
            id: 42,
            name: "ISBT ↔ Rajpur Road",
            start: "ISBT",
            end: "Rajpur Road",
            changePointName: "Ghanta Ghar",
            changePointCoords: [30.3240, 78.0415],
            path: [
                [30.2850, 78.0050], // ISBT
                [30.3000, 78.0200],
                [30.3240, 78.0415], // Change Point: Ghanta Ghar
                [30.3400, 78.0600],
                [30.3650, 78.0800]  // Rajpur Road
            ]
        },
        {
            id: 7,
            name: "Prem Nagar ↔ Railway Station",
            start: "Prem Nagar",
            end: "Railway Station",
            changePointName: "Ballupur Chowk",
            changePointCoords: [30.3320, 78.0120],
            path: [
                [30.3450, 77.9650], // Prem Nagar
                [30.3320, 78.0120], // Change Point: Ballupur Chowk
                [30.3180, 78.0350]  // Railway Station
            ]
        }
    ],

    buses: [
        {
            id: 'BUS-12',
            routeId: 12,
            routeName: "City Centre ↔ Airport",
            busNumber: "UK-07-PA-1234",
            status: "live", // 'live', 'error', 'no_session'
            lat: 30.3200,
            lng: 78.0370,
            conductorName: "Ramesh K.",
            conductorPhone: "+91 98765 43210",
            lastSeen: "1 min ago",
            etaMinutes: 4,
            heading: "Airport"
        },
        {
            id: 'BUS-42',
            routeId: 42,
            routeName: "ISBT ↔ Rajpur Road",
            busNumber: "UK-07-PA-4242",
            status: "live",
            lat: 30.3300,
            lng: 78.0480,
            conductorName: "Suresh M.",
            conductorPhone: "+91 98765 11223",
            lastSeen: "2 mins ago",
            etaMinutes: 8,
            heading: "Rajpur Road"
        },
        {
            id: 'BUS-08',
            routeId: 42,
            routeName: "ISBT ↔ Rajpur Road",
            busNumber: "UK-07-PA-0808",
            status: "error", // Needs Attention: Stalled at change point
            lat: 30.3240,
            lng: 78.0415,
            conductorName: "Pending Handoff",
            conductorPhone: "+91 98765 99887",
            lastSeen: "8 mins ago",
            etaMinutes: null,
            heading: "Rajpur Road",
            errorReason: "Session not resumed after change point Ghanta Ghar."
        },
        {
            id: 'BUS-55',
            routeId: 12,
            routeName: "City Centre ↔ Airport",
            busNumber: "UK-07-PA-5555",
            status: "no_session",
            lat: null,
            lng: null,
            conductorName: "N/A",
            conductorPhone: "N/A",
            lastSeen: "45 mins ago",
            etaMinutes: null,
            heading: "Unknown"
        }
    ],

    sessions: [
        { id: 'S-001', bus: 'BUS-42', route: 'Rajpur Rd ↔ Clock Tower', conductor: 'Ramesh K.', start: '06:00 AM', end: '08:30 AM', duration: '2h 30m', status: 'completed' },
        { id: 'S-002', bus: 'BUS-17', route: 'Prem Nagar ↔ ISBT', conductor: 'Anil S.', start: '06:20 AM', end: 'Active', duration: 'In Progress', status: 'live' },
        { id: 'S-004', bus: 'BUS-08', route: 'Ghanta Ghar ↔ Rajpur Rd', conductor: 'Suresh M.', start: '07:10 AM', end: 'Stalled', duration: '8 mins stalled', status: 'error' }
    ],

    getState: function() {
        const local = localStorage.getItem('bhraminsathi_state');
        if (local) {
            try { return JSON.parse(local); } catch(e) {}
        }
        return { buses: this.buses, sessions: this.sessions };
    },

    saveState: function(state) {
        localStorage.setItem('bhraminsathi_state', JSON.stringify(state));
    }
};
