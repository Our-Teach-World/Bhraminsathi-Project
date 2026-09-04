# BhraminSathi — Tech Stack & Database

## Tech Stack (Decided)
- **Backend/Framework:** Laravel (PHP)
- **Frontend:** HTML, CSS, vanilla JavaScript (Blade templates, no separate JS framework)
- **Database:** MySQL
- **Map:** Leaflet.js + OpenStreetMap tiles
  - Free, no API key, no billing account required — right fit for a hackathon build
  - Geocoding (place search): Nominatim (OSM's free geocoding service)
  - Route line drawing: Leaflet Routing Machine (uses OSRM)
- **Live position updates:** plain AJAX/fetch polling from passenger app to a Laravel API endpoint (websockets not required for the prototype)

## Database Schema

### `buses`
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| bus_number | varchar, unique | |
| route_id | FK → routes.id | |
| current_lat | decimal(10,7), nullable | null when no live session |
| current_lng | decimal(10,7), nullable | null when no live session |
| status | enum('live','no_session','error') | drives passenger-facing display |
| last_updated_at | timestamp, nullable | detects stale/dropped signal |

### `routes`
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| name | varchar | e.g. "Railway Station – City Mall" |
| start_point_lat/lng | decimal | fixed route start |
| end_point_lat/lng | decimal | fixed route end |
| change_point_lat/lng | decimal | geofence trigger point for conductor handoff |
| stops_json | json/text | ordered stop names + coordinates, for static route info |

### `conductors`
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| name | varchar | |
| phone | varchar | used for reminder/alert |

### `conductor_sessions`
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| bus_id | FK → buses.id | |
| conductor_id | FK → conductors.id | |
| started_at | timestamp | |
| ended_at | timestamp, nullable | null while session is active |
| end_reason | enum('geofence_auto','manual','none') | |

### `bus_error_flags`
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| bus_id | FK → buses.id | |
| flagged_at | timestamp | when change point passed with no new session |
| reminder_count | int, default 0 | increments on each auto-retry |
| resolved_at | timestamp, nullable | |
| resolved_by | varchar, nullable | admin who resolved it |

### `bus_location_history` (optional)
| Column | Type | Notes |
|---|---|---|
| id | bigint, PK | |
| bus_id | FK → buses.id | |
| lat / lng | decimal | |
| recorded_at | timestamp | |

Optional log table — only needed if you want to show a path trail on the map instead of just the latest point.

## Design Notes
- `buses.status` is a cached/derived field, updated directly when a session starts/ends or an error is flagged — not recalculated on every passenger request.
- The **change point** belongs to `routes`, not `buses`, since it's a property of the route itself, shared by every bus running it.
- `conductor_sessions` is the source of truth for "who is driving right now"; `buses.current_lat/lng` is just the latest cached position for fast reads by the passenger app.
- `bus_error_flags` is kept separate from `buses.status` so error history/resolutions are preserved for the admin session log, rather than being overwritten.

## High-Level API Endpoints (for reference — no code, just the shape)
- `POST /api/conductor/session/start` — bus number + conductor → starts session
- `POST /api/conductor/location` — periodic GPS update from active conductor session
- (system/scheduled) geofence check against route's change point → auto-ends session, flags error if no new session follows
- `GET /api/buses/nearby` — passenger app polls this for live bus positions near a location
- `GET /admin/buses` — admin panel's aggregated live view
- `POST /admin/buses/{id}/remind` — manual reminder trigger
- `POST /admin/buses/{id}/resolve` — mark error resolved
