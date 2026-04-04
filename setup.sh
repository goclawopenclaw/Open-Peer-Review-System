#!/bin/bash

set -e

echo "🚀 Building Open Peer Review System..."
echo "Stack: Laravel 11 + Vue.js 3 + PostgreSQL 15"
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ==========================================
# BACKEND SETUP (Laravel 11)
# ==========================================
echo -e "${BLUE}Setting up Laravel Backend...${NC}"

cd backend

# Create Laravel project structure
mkdir -p app/Http/{Controllers,Requests,Resources,Middleware} \
         app/Models \
         app/Events \
         app/Jobs \
         app/Listeners \
         app/Services \
         app/Policies \
         database/{migrations,factories,seeders} \
         routes \
         tests/{Feature,Unit} \
         config \
         storage/{app,logs,framework} \
         resources/{views,css,js}

# Composer files
cat > composer.json << 'EOF'
{
  "name": "oprs/open-peer-review-system",
  "description": "Open Peer Review System - ORE aligned",
  "type": "project",
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "laravel/tinker": "^2.8",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-event-sourcing": "^7.0",
    "aws/aws-sdk-php": "^3.283"
  },
  "require-dev": {
    "laravel/pint": "^1.13",
    "laravel/sail": "^1.26",
    "phpstan/phpstan": "^1.10",
    "pestphp/pest": "^2.28",
    "pestphp/pest-plugin-laravel": "^2.28"
  }
}
EOF

# .env.example
cat > .env.example << 'EOF'
APP_NAME="Open Peer Review System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=peer_review
DB_USERNAME=postgres
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@oprs.local
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
EOF

# Dockerfile
cat > Dockerfile << 'EOF'
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install --no-interaction --no-dev --prefer-dist

COPY . .

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
EOF

echo -e "${GREEN}✓ Laravel backend structure created${NC}"

# ==========================================
# FRONTEND SETUP (Vue.js 3)
# ==========================================
echo -e "${BLUE}Setting up Vue.js Frontend...${NC}"

cd ../frontend

mkdir -p src/{components/{layouts,common,forms,readers,dashboards,pages,articles},views,stores,composables,services,utils,router,assets/css}
mkdir -p public tests/{unit,e2e}

# package.json
cat > package.json << 'EOF'
{
  "name": "oprs-frontend",
  "version": "3.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "lint": "eslint .",
    "format": "prettier --write .",
    "test:unit": "vitest",
    "test:e2e": "cypress run"
  },
  "dependencies": {
    "vue": "^3.3.4",
    "vue-router": "^4.2.4",
    "pinia": "^2.1.4",
    "axios": "^1.5.0",
    "@tiptap/vue-3": "^2.0.4",
    "@tiptap/core": "^2.0.4",
    "@tiptap/starter-kit": "^2.0.4",
    "pdfjs-dist": "^3.11.174"
  },
  "devDependencies": {
    "vite": "^4.4.9",
    "@vitejs/plugin-vue": "^4.3.4",
    "typescript": "^5.2.2",
    "tailwindcss": "^3.3.3",
    "postcss": "^8.4.31",
    "autoprefixer": "^10.4.15",
    "eslint": "^8.49.0",
    "prettier": "^3.0.3",
    "vitest": "^0.34.4",
    "@vue/test-utils": "^2.4.1"
  }
}
EOF

# vite.config.js
cat > vite.config.js << 'EOF'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 3000,
    hmr: {
      host: 'localhost',
      port: 3000,
    },
  },
})
EOF

# tailwind.config.js
cat > tailwind.config.js << 'EOF'
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
EOF

# Dockerfile.dev
cat > Dockerfile.dev << 'EOF'
FROM node:20-alpine

WORKDIR /app

COPY package.json pnpm-lock.yaml* package-lock.json* ./

RUN npm install

COPY . .

EXPOSE 3000

CMD ["npm", "run", "dev"]
EOF

# Main Vue app
mkdir -p src
cat > src/main.js << 'EOF'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/css/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.mount('#app')
EOF

cat > src/App.vue << 'EOF'
<template>
  <div id="app">
    <nav class="bg-blue-600 text-white p-4">
      <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Open Peer Review System</h1>
        <div class="space-x-4">
          <router-link to="/" class="hover:bg-blue-700 px-3 py-2 rounded">Home</router-link>
          <router-link to="/dashboard" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</router-link>
        </div>
      </div>
    </nav>
    <main class="p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
EOF

# Router
mkdir -p src/router
cat > src/router/index.js << 'EOF'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/DashboardView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
EOF

# Stores
mkdir -p src/stores
cat > src/stores/authStore.js << 'EOF'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const login = async (email, password) => {
    // API call will go here
    console.log('Login:', email)
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  return { user, token, login, logout }
})
EOF

# Views
mkdir -p src/views
cat > src/views/HomeView.vue << 'EOF'
<template>
  <div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold mb-4">Welcome to Open Peer Review System</h1>
    <p class="text-lg text-gray-600 mb-8">
      A transparent, ORE-aligned platform for peer review.
    </p>
    <div class="grid grid-cols-2 gap-4">
      <router-link to="/submit" class="bg-blue-600 text-white p-6 rounded hover:bg-blue-700">
        <h2 class="text-2xl font-bold mb-2">Submit Article</h2>
        <p>Start your peer review journey</p>
      </router-link>
      <router-link to="/dashboard" class="bg-green-600 text-white p-6 rounded hover:bg-green-700">
        <h2 class="text-2xl font-bold mb-2">Dashboard</h2>
        <p>View your submissions and reviews</p>
      </router-link>
    </div>
  </div>
</template>
EOF

cat > src/views/DashboardView.vue << 'EOF'
<template>
  <div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    <div class="grid grid-cols-3 gap-4 mb-8">
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-gray-600 text-sm font-semibold">Submissions</h3>
        <p class="text-3xl font-bold">0</p>
      </div>
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-gray-600 text-sm font-semibold">Reviews</h3>
        <p class="text-3xl font-bold">0</p>
      </div>
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-gray-600 text-sm font-semibold">Published</h3>
        <p class="text-3xl font-bold">0</p>
      </div>
    </div>
  </div>
</template>
EOF

# CSS
mkdir -p src/assets/css
cat > src/assets/css/main.css << 'EOF'
@tailwind base;
@tailwind components;
@tailwind utilities;

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
    'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
EOF

# HTML
cat > public/index.html << 'EOF'
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Peer Review System</title>
  </head>
  <body>
    <div id="app"></div>
    <script type="module" src="/src/main.js"></script>
  </body>
</html>
EOF

echo -e "${GREEN}✓ Vue.js frontend structure created${NC}"

# ==========================================
# ROOT LEVEL
# ==========================================
cd ..

cat > README.md << 'EOF'
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
EOF

cat > .gitignore << 'EOF'
# Backend
backend/.env
backend/vendor/
backend/node_modules/
backend/storage/
backend/.vscode/
backend/.idea/

# Frontend
frontend/node_modules/
frontend/dist/
frontend/.env
frontend/.vscode/
frontend/.idea/

# OS
.DS_Store
.env.local

# IDE
*.swp
*.swo
*~

# Docker
.env.docker
EOF

echo -e "${GREEN}✓ Project root configured${NC}"
echo ""
echo -e "${GREEN}✅ Setup complete!${NC}"
echo ""
echo "Next steps:"
echo "1. docker-compose up -d"
echo "2. docker-compose exec php php artisan migrate"
echo "3. Visit http://localhost:3000"
echo ""
