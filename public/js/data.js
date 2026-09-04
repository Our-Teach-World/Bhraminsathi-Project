/**
 * BhraminSathi — Shared State & Database Data Engine (Jaipur City Network)
 */

window.BhraminData = {
    routes: [
        {
            id: 12,
            name: "Jaipur Junction ↔ Sanganer Airport",
            start: "Jaipur Junction",
            end: "Sanganer Airport",
            changePointName: "B2 Bypass Junction",
            changePointCoords: [26.8380, 75.7950],
            path: [
                [26.9200, 75.7877], // Jaipur Junction Railway Station
                [26.8950, 75.8070], // MI Road / Ajmer Gate
                [26.8650, 75.8150], // Rambagh Circle
                [26.8380, 75.7950], // Change Point: B2 Bypass / Tonk Phatak
                [26.8285, 75.8056]  // Sanganer Airport Terminal
            ]
        },
        {
            id: 42,
            name: "Sindhi Camp ISBT ↔ Sitapura Ind. Area",
            start: "Sindhi Camp ISBT",
            end: "Sitapura Industrial Area",
            changePointName: "B2 Bypass Junction",
            changePointCoords: [26.8380, 75.7950],
            path: [
                [26.9248, 75.7980], // Sindhi Camp Bus Stand ISBT
                [26.8920, 75.8100], // Ajmer Gate
                [26.8500, 75.8000], // SMS Hospital
                [26.8380, 75.7950], // Change Point: B2 Bypass
                [26.7820, 75.8230]  // Sitapura Industrial Area
            ]
        },
        {
            id: 7,
            name: "Mansarovar ↔ Vaishali Nagar",
            start: "Mansarovar Metro",
            end: "Vaishali Nagar",
            changePointName: "200 Feet Bypass",
            changePointCoords: [26.8950, 75.7420],
            path: [
                [26.8800, 75.7500], // Mansarovar Metro Station
                [26.8950, 75.7420], // 200 Feet Bypass
                [26.9150, 75.7400]  // Vaishali Nagar
            ]
        }
    ],

    buses: [
        {
            id: 'BUS-12',
            routeId: 12,
            routeName: "Jaipur Junction ↔ Sanganer Airport",
            busNumber: "RJ-14-PA-1234",
            status: "live", // 'live', 'error', 'no_session'
            lat: 26.8380,
            lng: 75.7950,
            conductorName: "Ramesh K.",
            conductorPhone: "+91 98765 43210",
            lastSeen: "1 min ago",
            etaMinutes: 4,
            heading: "Sanganer Airport"
        },
        {
            id: 'BUS-42',
            routeId: 42,
            routeName: "Sindhi Camp ISBT ↔ Sitapura Ind. Area",
            busNumber: "RJ-14-PA-4242",
            status: "live",
            lat: 26.7850,
            lng: 75.8200,
            conductorName: "Suresh M.",
            conductorPhone: "+91 98765 11223",
            lastSeen: "2 mins ago",
            etaMinutes: 8,
            heading: "Sitapura Industrial Area"
        },
        {
            id: 'BUS-08',
            routeId: 42,
            routeName: "Sindhi Camp ISBT ↔ Sitapura Ind. Area",
            busNumber: "RJ-14-PA-0808",
            status: "error", // Needs Attention: Stalled at change point
            lat: 26.8380,
            lng: 75.7950,
            conductorName: "Pending Handoff",
            conductorPhone: "+91 98765 99887",
            lastSeen: "8 mins ago",
            etaMinutes: null,
            heading: "Sitapura Ind. Area",
            errorReason: "Session not resumed after change point B2 Bypass Junction."
        },
        {
            id: 'BUS-55',
            routeId: 12,
            routeName: "Jaipur Junction ↔ Sanganer Airport",
            busNumber: "RJ-14-PA-5555",
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
        { id: 'S-001', bus: 'BUS-42', route: 'Sindhi Camp ↔ Sitapura', conductor: 'Ramesh K.', start: '06:00 AM', end: '08:30 AM', duration: '2h 30m', status: 'completed' },
        { id: 'S-002', bus: 'BUS-12', route: 'Jaipur Junction ↔ Airport', conductor: 'Anil S.', start: '06:20 AM', end: 'Active', duration: 'In Progress', status: 'live' },
        { id: 'S-003', bus: 'BUS-08', route: 'B2 Bypass ↔ Sitapura', conductor: 'Suresh M.', start: '07:10 AM', end: 'Stalled', duration: '8 mins stalled', status: 'error' }
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
