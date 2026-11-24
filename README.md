# Mini Workshop Reservation Application

A full-stack Laravel application where organizers create and publish workshops, and visitors reserve seats with concurrency-safe capacity enforcement, automatic waitlisting, and FIFO waitlist promotion upon cancellation.

## Key Features

- **Organizer Dashboard:** Create draft workshops, publish them when ready, monitor confirmed vs. waitlisted counts, and cancel confirmed reservations.
- **Visitor Portal:** Browse published upcoming workshops, view live seat availability, and reserve a seat or join the waitlist.
- **Automatic Waitlist & Promotion:** When a workshop reaches full capacity, new reservations are automatically marked as `waitlisted`. Cancelling a `confirmed` reservation promotes the oldest eligible `waitlisted` attendee (`created_at ASC, id ASC`).
- **Concurrency Safety:** Uses database transactions with pessimistic row-level locking (`lockForUpdate()`) inside `ReservationService` to prevent overbooking under simultaneous requests.
- **Zero Frontend Build Step:** Styled with Tailwind CSS via CDN using Blade layouts.

## Requirements

- **PHP:** 8.1 or higher (with `pdo_mysql` and `pdo_sqlite` extensions enabled)
- **Composer**
- **MySQL:** 8.0+ (Laragon recommended for local Windows development)

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd <project-folder>
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Create your environment file:**
   ```bash
   cp .env.example .env
   ```

4. **Generate the application key:**
   ```bash
   php artisan key:generate
   ```

5. **Create and configure the MySQL database:**
   - Open Laragon's **HeidiSQL** (or MySQL CLI) and create a database named `workshop_reservation`.
   - Update the database settings in your `.env` file:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=workshop_reservation
     DB_USERNAME=root
     DB_PASSWORD=
     ```

6. **Run migrations and seed sample data:**
   ```bash
   php artisan migrate --seed
   ```
   *(Seeds a draft workshop, an open published workshop, a full workshop with waitlisted attendees, and a past workshop.)*

7. **Start the local development server:**
   ```bash
   php artisan serve
   ```

8. **Open the application in your browser:**
   - **Visitor View:** `http://localhost:8000`
   - **Organizer View:** `http://localhost:8000/organizer/workshops`

## Running Tests

The test suite runs against an isolated in-memory SQLite database configured in `phpunit.xml` without touching your local MySQL database:

```bash
php artisan test
```

## Key Routes

### Visitor Routes
| Method | URI | Name | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/` | `workshops.index` | List all published workshops with remaining seat counts |
| `GET` | `/workshops/{workshop}` | `workshops.show` | View published workshop details and reservation form |
| `POST` | `/workshops/{workshop}/reserve` | `workshops.reserve` | Submit a seat reservation (auto-confirms or waitlists) |

### Organizer Routes
| Method | URI | Name | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/organizer/workshops` | `organizer.workshops.index` | List all workshops (Draft & Published) with confirmed counts |
| `GET` | `/organizer/workshops/create` | `organizer.workshops.create` | Display form to create a new workshop |
| `POST` | `/organizer/workshops` | `organizer.workshops.store` | Store a new workshop as `draft` |
| `GET` | `/organizer/workshops/{workshop}` | `organizer.workshops.show` | View workshop details and manage attendee reservations |
| `PATCH` | `/organizer/workshops/{workshop}/publish` | `organizer.workshops.publish` | Publish a draft workshop |
| `PATCH` | `/organizer/reservations/{reservation}/cancel` | `organizer.reservations.cancel` | Cancel a confirmed reservation and promote oldest waitlisted attendee |

### System Routes
| Method | URI | Name | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/health` | `health` | JSON health check verifying live database connectivity |

## Completed Items

- Published workshop listing and detail pages
- Reservation form with name and email
- Duplicate email prevention per workshop
- Automatic Confirmed vs Waitlisted status based on capacity
- Draft and past-workshop reservation blocking
- Organizer: create, publish, view reservations, cancel
- Waitlist promotion on cancellation
- Capacity enforcement
- 6 required automated tests
- Health check route
- Planning note
- `DEPLOYMENT.md`
- `.env.example`
- Database migrations, seeders, and factories

## Incomplete Items / Known Limitations

- No authentication (organizer pages are publicly accessible as per assessment scope)
- No email notifications for reservation confirmation or waitlist promotion
- No pagination on workshop or reservation lists
- UI is minimal/functional — no advanced styling