# Open Peer Review System

A transparent, community-driven peer review platform aligned with Open Research Europe (ORE) best practices.

## Tech Stack

- **Backend:** PHP 8.2 + Laravel 11
- **Frontend:** Vue.js 3 + Vite
- **Database:** PostgreSQL 15
- **Storage:** AWS S3
- **Containerization:** Docker

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Setup

```bash
# Clone repository
git clone https://github.com/goclawopenclaw/Open-Peer-Review-System.git
cd Open-Peer-Review-System

# Install dependencies (optional, Docker handles it)
cd backend && composer install && cd ..
cd frontend && npm install && cd ..

# Start services
docker-compose up -d

# Run migrations
docker-compose exec php php artisan migrate

# Visit applications
- Frontend: http://localhost:3000
- Backend API: http://localhost:8000/api
- Database Admin: http://localhost:5050 (pgAdmin)
```

## Project Structure

```
Open-Peer-Review-System/
├── backend/              # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Events/
│   │   ├── Jobs/
│   │   ├── Services/
│   │   └── Policies/
│   ├── database/
│   │   ├── migrations/
│   │   ├── factories/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── docker/
├── frontend/             # Vue.js 3 SPA
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   ├── router/
│   │   └── services/
│   └── public/
├── docker-compose.yml
└── README.md
```

## Specifications

See `/specs` directory for detailed specifications:
- `SPEC-v1.md` - Initial specification
- `SPEC-v2.md` - ORE-aligned workflow
- `SPEC-v3.md` - Complete tech stack & implementation guide

## API Documentation

[Detailed API docs coming soon]

## Contributing

[Contributing guidelines coming soon]

## License

MIT
