# BhraminSathi — Locked Feature Scope

This is the final, agreed feature set for the prototype. Do not add features beyond this list without updating this document first.

## 1. Passenger App
- Enter source + destination via a map-based search (type a place or tap on map)
- See nearby **live buses** as markers, sourced from active conductor GPS sessions
- Live buses are visually distinct from buses with no active conductor session
- If a bus is heading toward the entered destination, it's highlighted
- Approximate position only — never claim precision the data doesn't have
- **Double verification design:** the app shows the likely bus (GPS signal); the passenger is prompted to confirm the destination verbally with the conductor when boarding and paying the fare. This is a deliberate human-in-the-loop trust layer, not a workaround.

## 2. Conductor Dashboard (separate simple interface)
- Conductor enters their bus number and taps **Start**
- Starting a session begins streaming that device's GPS as the bus's live location
- One bus = one active session at a time

## 3. Conductor Handoff Logic
- Each route has a **fixed change point** (a known location where conductor shifts happen structurally)
- When the bus's GPS reaches the change point, the **current session auto-ends** (geofence-triggered, no manual "stop" required)
- The next conductor is expected to start their own session (bus number → Start) at/around the same change point

## 4. Error State Handling
- If the bus is at/past the change point and **no new conductor session has started** within a short grace window, the bus is flagged as an **error state**
- The admin panel shows a clear message for that bus (e.g., "No conductor connected — Bus 12 at Central Chowk")
- The system automatically sends the conductor a reminder/nudge, retried a few times
- If still unresolved, it stays flagged until an admin manually intervenes (send reminder again, or mark resolved)
- **Passenger-facing behavior for an error-state bus:** show only static route info (bus number, route name, stops). No live position marker is shown — there is nothing real to show, and showing one would be dishonest.

## 5. Admin Panel
- Aggregated live view of all buses: number, route, session status (Live / No Session / Error), last known location/time
- Error-state buses are visually flagged and easy to find
- Manual actions: send reminder, mark resolved
- Basic session log (which conductor drove which bus, when) — a record-keeping view, not full analytics

## Explicitly Out of Scope (for this prototype)
- Crowd prediction (Low/Medium/High)
- Missed-call / SMS fallback for non-smartphone users
- Multi-role transport-authority analytics dashboard
- ML-based ETA prediction
- Any monetization or multi-city expansion features

These may be revisited in a later phase but should not be designed or built now — keep the prototype scope tight.
