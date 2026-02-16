# PHP User Management

A simple User Management system built with **Pure PHP (OOP / framework-style structure)**, **MySQL**, **Nginx**, and **Docker Compose**.


## Overview
This project is a minimal user/role management system designed without a full framework.

Includes:
- Authentication (login/logout) with session
- Role & Permission management (RBAC)
- Users CRUD basics (list/create/edit, toggle active)
- UI authorization (hide buttons/links based on permissions)

## Getting Started
1. Copy environment file:
```bash
cp .env.example .env
```

2. Start containers:
```bash
docker compose up -d --build
```

3. Install PHP dependencies inside the app container:
```bash
docker compose exec app composer install
docker compose exec app composer dump-autoload
```

4. Open application:
- App: [http://localhost:8080](http://localhost:8080)
- Health endpoint: [http://localhost:8080/health](http://localhost:8080/health)

## Database Setup
Run schema and seed scripts:

```bash
docker exec -i php_user_mgmt_db mysql -uroot -proot user_mgmt < bin/schema.sql
docker exec -i php_user_mgmt_db mysql -uroot -proot user_mgmt < bin/seed.sql
```

Then login with:
- Username: `admin`
- Password: `admin123`
