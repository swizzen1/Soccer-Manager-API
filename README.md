# Soccer Manager API

RESTful Laravel API for managing a soccer team, players, and transfer-market purchases.

## Requirements

- PHP 8.4+
- Composer
- SQLite, MySQL, MariaDB, PostgreSQL, or SQL Server

No extra localization package is required. API messages use Laravel translation files and the `Accept-Language` header.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

For local SQLite, set:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

## Tests

```bash
composer test
php artisan test
php artisan route:list
```

## Postman Collection

[Download Postman collection](./storage/app/private/postman/2026_06_18_112254_laravel_collection.json)

## Authentication

Register and login return a Sanctum bearer token. Send it on protected requests:

```http
Authorization: Bearer <token>
Accept: application/json
Accept-Language: en
```

Supported languages:

- `en`
- `ka`

## Endpoints

```http
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/me

GET    /api/team
PUT    /api/team
GET    /api/team/players
GET    /api/players/{player}
PUT    /api/players/{player}

GET    /api/market
POST   /api/players/{player}/transfer-list
DELETE /api/players/{player}/transfer-list
POST   /api/market/{listing}/buy
```

## Example Register Request

```json
{
  "name": "Giorgi Manager",
  "email": "giorgi@example.com",
  "password": "password123"
}
```

## Example Success Response

```json
{
  "success": true,
  "message": "Registration completed successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Giorgi Manager",
      "email": "giorgi@example.com"
    },
    "token": "1|token-value"
  }
}
```

## Example Error Response

```json
{
  "success": false,
  "message": "Not enough budget.",
  "errors": {}
}
```

## Business Rules

- Each user owns one team.
- Registering creates a team and 20 players.
- New teams start with a 5,000,000 budget.
- Generated squads contain 3 goalkeepers, 6 defenders, 6 midfielders, and 5 attackers.
- Players start with a 1,000,000 market value and random age between 18 and 40.
- Owners can edit team name/country and player first name, last name, and country.
- Only owners can list or cancel their player transfer listings.
- Buyers cannot buy their own players.
- Transfer purchases run inside a database transaction.
