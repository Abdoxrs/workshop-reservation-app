# Project Planning Note — Mini Workshop Reservation Application

## 1. Understanding & Assumptions
- **User Roles:** Two conceptual user types: Organizers (manage workshops) and Visitors (browse and reserve). No authentication is required; organizer views are separated by URL (`/organizer/...`).
- **Workshops:** Have `title`, `description`, `scheduled_at` (datetime), `capacity`, and `status` (`draft` or `published`). Only published workshops that haven't started are open to visitors.
- **Reservations:** Have `attendee_name`, `attendee_email`, `workshop_id`, and `status` (`confirmed`, `waitlisted`, or `cancelled`).
- **Seats Available:** Confirmed reservations count < workshop capacity. When full, new reservations become `waitlisted`.
- **Oldest Eligible Waitlisted:** The `waitlisted` reservation with the earliest `created_at` timestamp is promoted to `confirmed` when a confirmed reservation is cancelled.

## 2. Proposed Database Design
- **Engine:** MySQL (via Laragon) for development and production; SQLite in-memory (`:memory:`) for automated feature tests.
- **`workshops` table:** `id`, `title`, `description`, `scheduled_at` (datetime), `capacity` (unsigned integer), `status` (string: `draft`/`published`, indexed), `timestamps`.
- **`reservations` table:** `id`, `workshop_id` (foreign key), `attendee_name`, `attendee_email`, `status` (string: `confirmed`/`waitlisted`/`cancelled`, indexed), `timestamps`.
- **Constraints:** Compound unique index on `(workshop_id, attendee_email)` to enforce uniqueness at the database level.

## 3. Implementation Order
1. Models, migrations, factories, and seeders
2. Business logic (`ReservationService`)
3. Organizer routes, controllers, and Blade views
4. Visitor routes, controllers, and Blade views
5. Health check route & automated tests
6. Documentation (`README.md`, `DEPLOYMENT.md`, `.env.example`)

## 4. Main Technical Risks & Mitigations
- **Race Conditions:** Two visitors reserving the last seat simultaneously could exceed capacity. *Mitigation:* Wrap reservation creation in a database transaction with pessimistic locking (`lockForUpdate()`) on the `Workshop` row before counting confirmed seats.
- **Waitlist Promotion:** Cancelling a confirmed reservation must atomically promote the oldest waitlisted reservation. *Mitigation:* Perform cancellation and promotion inside a single `DB::transaction()`.