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

## Quality Checks

```bash
php artisan test
./vendor/bin/phpstan analyse --memory-limit=2G
vendor/bin/pint --dirty
php artisan route:list
```

## Postman Collection

[Download Postman collection](./storage/app/private/postman/2026_06_18_112254_laravel_collection.json)

## Authentication

The API uses Laravel Sanctum bearer tokens. `POST /api/register` and `POST /api/login` return a token.

Send the token on protected requests:

```http
Authorization: Bearer <token>
Accept: application/json
Accept-Language: en
```

Supported languages:

- `en`
- `ka`

## Response Format

Successful responses use this envelope:

```json
{
  "success": true,
  "message": "Team retrieved successfully.",
  "data": {}
}
```

Error responses use this envelope:

```json
{
  "success": false,
  "message": "Not enough budget.",
  "errors": {}
}
```

Validation errors return `422 Unprocessable Entity` and include field-level errors.

## Status Codes

| Code | Meaning |
| --- | --- |
| `200` | Request completed successfully. |
| `201` | Resource created successfully. |
| `401` | Missing or invalid bearer token. |
| `403` | Authenticated user is not allowed to perform the action. |
| `404` | Route model binding could not find the resource. |
| `409` | Business rule conflict, such as an inactive or duplicate transfer listing. |
| `422` | Validation failed or the buyer does not have enough budget. |

## Resource Shapes

### User

```json
{
  "id": 1,
  "name": "Admin",
  "email": "admin@example.com",
  "team": {}
}
```

`team` is included only when the endpoint loads it.

### Team

```json
{
  "id": 1,
  "name": "Admin FC",
  "country": "Georgia",
  "budget": 5000000,
  "team_value": 20000000,
  "players": []
}
```

`players` is included only when the endpoint loads it.

### Player

```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "country": "Georgia",
  "position": "midfielder",
  "age": 24,
  "market_value": 1000000,
  "team_id": 1
}
```

Allowed positions are `goalkeeper`, `defender`, `midfielder`, and `attacker`.

### Transfer Listing

```json
{
  "id": 1,
  "asking_price": 1500000,
  "status": "active",
  "player": {},
  "seller_team": {}
}
```

Allowed statuses are `active`, `sold`, and `cancelled`.


## Endpoints

### Public Endpoints

```http
POST /api/register
POST /api/login
GET  /api/market
```

### Protected Endpoints

```http
POST   /api/logout
GET    /api/me
GET    /api/team
PUT    /api/team
GET    /api/team/players
GET    /api/players/{player}
PUT    /api/players/{player}
POST   /api/players/{player}/transfer-list
DELETE /api/players/{player}/transfer-list
POST   /api/market/{listing}/buy
```

## Auth API

### POST /api/register

Creates a user, creates their team, generates 20 players, and returns a Sanctum token.

Auth: public.

Request:

```json
{
  "name": "Admin",
  "email": "admin@example.com",
  "password": "password123"
}
```

Rules:

- `name` is required, string, max 255 characters.
- `email` is required, valid email, max 255 characters, unique in `users`.
- `password` is required, string, minimum 8 characters.

Success: `201 Created`

```json
{
  "success": true,
  "message": "Registration completed successfully.",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "team": {
        "id": 1,
        "name": "Admin FC",
        "country": "Georgia",
        "budget": 5000000,
        "team_value": 20000000,
        "players": []
      }
    },
    "token": "1|token-value"
  }
}
```

Errors: `422` for validation errors.

### POST /api/login

Authenticates a user and returns a Sanctum token.

Auth: public.

Request:

```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

Rules:

- `email` is required and must be a valid email.
- `password` is required and must be a string.

Success: `200 OK`

Errors: `401` for invalid credentials, `422` for validation errors.

### POST /api/logout

Deletes the current access token.

Auth: bearer token required.

Success: `200 OK`

### GET /api/me

Returns the authenticated user with team and players.

Auth: bearer token required.

Success: `200 OK`

## Team API

### GET /api/team

Returns the authenticated user's team with players, budget, and calculated team value.

Auth: bearer token required.

Success: `200 OK`

### PUT /api/team

Updates editable team fields.

Auth: bearer token required.

Request:

```json
{
  "name": "Dinamo API",
  "country": "Georgia"
}
```

Rules:

- `name` is optional, string, max 100 characters.
- `country` is optional, string, max 100 characters.
- `budget` is prohibited.
- `user_id` is prohibited.

Success: `200 OK`

Errors: `422` for validation errors.

## Players API

### GET /api/team/players

Returns the authenticated user's players.

Auth: bearer token required.

Success: `200 OK`

### GET /api/players/{player}

Returns one player by id.

Auth: bearer token required.

Success: `200 OK`

Errors: `404` if the player does not exist.

### PUT /api/players/{player}

Updates editable player fields. Only the player's team owner can update the player.

Auth: bearer token required.

Request:

```json
{
  "first_name": "Updated",
  "last_name": "Player",
  "country": "Georgia"
}
```

Rules:

- `first_name` is optional, string, max 100 characters.
- `last_name` is optional, string, max 100 characters.
- `country` is optional, string, max 100 characters.
- `age` is prohibited.
- `position` is prohibited.
- `market_value` is prohibited.
- `team_id` is prohibited.

Success: `200 OK`

Errors: `403` if the player belongs to another user's team, `404` if the player does not exist, `422` for validation errors.

## Transfer Market API

### GET /api/market

Returns active transfer listings ordered by newest first.

Auth: public.

Success: `200 OK`

### POST /api/players/{player}/transfer-list

Lists a player on the transfer market. Only the player's team owner can list the player.

Auth: bearer token required.

Request:

```json
{
  "asking_price": 1500000
}
```

Rules:

- `asking_price` is required, numeric, greater than 0, max `9999999999999.99`.
- The player must belong to the authenticated user's team.
- The player must not already have an active transfer listing.

Success: `201 Created`

Errors: `403` if the player belongs to another user's team, `404` if the player does not exist, `409` if the player is already active on the transfer list, `422` for validation errors.

### DELETE /api/players/{player}/transfer-list

Cancels a player's active transfer listing. Only the player's team owner can cancel it.

Auth: bearer token required.

Success: `200 OK`

Errors: `403` if the player belongs to another user's team, `404` if the player does not exist, `409` if the player has no active listing.

### POST /api/market/{listing}/buy

Buys a player from an active transfer listing.

Auth: bearer token required.

Request body: none.

Rules:

- The listing must be active.
- The buyer cannot own the seller team.
- The buyer must have enough budget.
- The listed player must still belong to the seller team.

Success: `200 OK`

Effects:

- Buyer team budget is decreased by `asking_price`.
- Seller team budget is increased by `asking_price`.
- Player is moved to the buyer team.
- Player market value is increased randomly by 10% to 100%.
- Transfer listing status becomes `sold`.

Errors: `403` if the buyer owns the listed player, `404` if the listing does not exist, `409` if the listing is inactive or stale, `422` if the buyer does not have enough budget.
