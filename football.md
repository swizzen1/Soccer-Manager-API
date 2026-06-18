# Codex Instruction — Soccer Manager API Laravel Project

შექმენი ახალი Laravel პროექტი Laravel-ის latest stable version-ზე და ააწყე სრულად RESTful API აპლიკაცია სახელით **Soccer Manager API**.

მთავარი მოთხოვნაა კოდი იყოს production-ready, სუფთა არქიტექტურით, SOLID პრინციპებით და OOP მიდგომით. არ დაწერო business logic პირდაპირ Controller-ში. Controller უნდა იყოს თხელი და მხოლოდ Request → Service → Resource response flow-ს მართავდეს.

## Core Requirements

### 1. Authentication / Authorization

გამოიყენე Laravel Sanctum ან Passport. უპირატესობა მიანიჭე Sanctum-ს API token authentication-ისთვის.

უნდა არსებობდეს შემდეგი endpoints:

```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/me
```

Register-ის დროს user უნდა შეიქმნას email-ით, password-ით და name-ით.

User იდენტიფიცირდება email-ით.

ერთ user-ს უნდა ჰქონდეს მხოლოდ ერთი team.

Register-ის დასრულებისას ავტომატურად უნდა შეიქმნას user-ის team და 20 player.

---

## 2. Database Architecture

შექმენი migrations, models, factories, seeders და relationships.

საჭირო tables:

```text
users
teams
players
transfer_listings
```

### users

ველები:

```text
id
name
email unique
password
timestamps
```

### teams

ველები:

```text
id
user_id unique foreign key
name
country
budget decimal default 5000000
timestamps
```

Team-ს აქვს:

```text
team name
country
budget
team value = sum of player market values
```

Team name და country უნდა იყოს editable owner-ის მიერ.

### players

ველები:

```text
id
team_id foreign key
first_name
last_name
country
position enum: goalkeeper, defender, midfielder, attacker
age integer
market_value decimal default 1000000
timestamps
```

Player fields:

```text
first name
last name
country
age random 18-40
market value initial 1,000,000
```

Player first_name, last_name და country შეიძლება edit გააკეთოს მხოლოდ team owner-მა.

### transfer_listings

ველები:

```text
id
player_id unique foreign key
seller_team_id foreign key
asking_price decimal
status enum: active, sold, cancelled
timestamps
```

Player ერთდროულად მხოლოდ ერთხელ შეიძლება იყოს active transfer list-ზე.

---

## 3. Player Generation Logic

როცა user დარეგისტრირდება, სისტემა ავტომატურად აგენერირებს 20 player-ს:

```text
3 goalkeepers
6 defenders
6 midfielders
5 attackers
```

ყველა player-ის initial market_value უნდა იყოს:

```text
1,000,000
```

ყველა player-ის age უნდა იყოს random 18-დან 40-მდე.

Player generation logic არ უნდა იყოს Controller-ში.

შექმენი ცალკე Service, მაგალითად:

```php
App\Services\TeamCreationService
App\Services\PlayerGenerationService
```

---

## 4. RESTful API Endpoints

### Auth

```http
POST /api/register
POST /api/login
POST /api/logout
GET  /api/me
```

### Team

```http
GET /api/team
PUT /api/team
```

GET `/api/team` აბრუნებს logged-in user-ის team-ს, players-ს, budget-ს და calculated team value-ს.

PUT `/api/team` საშუალებას აძლევს owner-ს შეცვალოს:

```text
name
country
```

### Players

```http
GET /api/team/players
GET /api/players/{player}
PUT /api/players/{player}
```

PUT `/api/players/{player}` უნდა შეეძლოს მხოლოდ ამ player-ის team owner-ს.

შესაცვლელი ველები:

```text
first_name
last_name
country
```

არ უნდა შეეძლოს:

```text
age
position
market_value
team_id
```

---

## 5. Transfer Market

### Add player to transfer list

```http
POST /api/players/{player}/transfer-list
```

Request body:

```json
{
  "asking_price": 1500000
}
```

Rules:

```text
Only player owner can list player
asking_price must be positive
player cannot already be listed as active
```

### Remove player from transfer list

```http
DELETE /api/players/{player}/transfer-list
```

Rules:

```text
Only player owner can cancel listing
Only active listing can be cancelled
```

### Market list

```http
GET /api/market
```

ყველა user-ს უნდა შეეძლოს transfer list-ზე არსებული active players-ის ნახვა.

Response-ში უნდა იყოს:

```text
listing id
asking price
player info
seller team info
```

### Buy player

```http
POST /api/market/{listing}/buy
```

Rules:

```text
Buyer must be authenticated
Buyer cannot buy own player
Buyer team budget must be >= asking_price
Seller team budget increases by asking_price
Buyer team budget decreases by asking_price
Player team_id changes to buyer team
Listing status becomes sold
Player market_value increases randomly between 10% and 100%
Transfer operation must be inside DB transaction
```

Market value increase example:

```php
$percentage = random_int(10, 100);
$newValue = $oldValue + ($oldValue * $percentage / 100);
```

---

## 6. Architecture Requirements

აუცილებლად გამოიყენე SOLID და OOP პრინციპები.

### Controllers

Controllers უნდა იყოს thin.

მაგალითად:

```php
AuthController
TeamController
PlayerController
TransferMarketController
```

Controller-ში არ დაწერო core business logic.

### Services

შექმენი Services:

```php
App\Services\AuthService
App\Services\TeamCreationService
App\Services\PlayerGenerationService
App\Services\TeamService
App\Services\PlayerService
App\Services\TransferService
```

### DTOs Optional but Preferred

შეგიძლია გამოიყენო DTO classes request data-სთვის:

```php
App\DTOS\RegisterUserData
App\DTOS\UpdateTeamData
App\DTOS\UpdatePlayerData
App\DTOS\CreateTransferListingData
```

### Policies

Authorization-ისთვის გამოიყენე Laravel Policies:

```php
TeamPolicy
PlayerPolicy
TransferListingPolicy
```

Rules:

```text
Only team owner can edit team
Only player owner can edit player
Only player owner can list/cancel player transfer
User cannot buy own player
```

### Form Requests

ყველა validation უნდა იყოს Form Request-ში:

```php
RegisterRequest
LoginRequest
UpdateTeamRequest
UpdatePlayerRequest
CreateTransferListingRequest
```

### API Resources

Response formatting-ისთვის გამოიყენე Laravel API Resources:

```php
UserResource
TeamResource
PlayerResource
TransferListingResource
```

არ დააბრუნო raw models პირდაპირ Controller-იდან.

---

## 7. Localization

Application-ს უნდა ჰქონდეს localization support English და Georgian ენებზე.

შექმენი:

```text
lang/en/messages.php
lang/ka/messages.php
```

ყველა error/success message უნდა მოდიოდეს translation files-დან.

მაგალითად:

```php
__('messages.auth.login_success')
__('messages.team.updated')
__('messages.transfer.not_enough_budget')
```

API-ში locale შეიძლება განისაზღვროს header-ით:

```http
Accept-Language: en
Accept-Language: ka
```

შექმენი middleware:

```php
SetLocale
```

რომელიც Accept-Language header-ის მიხედვით დააყენებს app locale-ს.

---

## 8. Testing Requirements

დაწერე Feature Tests და საჭიროების შემთხვევაში Unit Tests.

Tests აუცილებელია:

### Auth tests

```text
user can register
user can login
user can logout
register creates team
register generates 20 players
```

### Team tests

```text
authenticated user can see own team
team value is calculated correctly
team owner can update team name and country
user cannot access without authentication
```

### Player tests

```text
team owner can update player first_name, last_name, country
team owner cannot update age, position, market_value
other user cannot update another team's player
```

### Transfer tests

```text
owner can list player on transfer market
owner cannot list same player twice
users can see market list
buyer can buy listed player
buyer cannot buy own player
buyer cannot buy player without enough budget
buying player updates buyer and seller budgets
buying player changes player team_id
buying player marks listing as sold
buying player increases market value between 10% and 100%
```

Use:

```bash
php artisan test
```

Tests should use database refresh:

```php
use RefreshDatabase;
```

---

## 9. API Response Format

ყველა response იყოს ერთიანი ფორმატით.

Success example:

```json
{
  "success": true,
  "message": "Team updated successfully.",
  "data": {}
}
```

Error example:

```json
{
  "success": false,
  "message": "Not enough budget.",
  "errors": {}
}
```

შექმენი helper ან trait:

```php
App\Traits\ApiResponse
```

---

## 10. Business Rules Summary

```text
Each user has only one team
Each team starts with 5,000,000 budget
Each new team starts with 20 generated players
Initial player value is 1,000,000
Team value is calculated from sum of player market values
Only owners can edit their team/player
Only owners can place players on transfer list
Transfer listing requires asking price
Buyers pay asking price
Seller receives asking price
Buyer budget decreases
Seller budget increases
Transferred player gets new team_id
Transferred player market value increases randomly between 10% and 100%
All transfer operations must use DB transaction
```

---

## 11. Code Quality Requirements

Follow these rules strictly:

```text
Use strict typing where reasonable
Use service classes for business logic
Use policies for authorization
Use form requests for validation
Use API resources for responses
Use transactions for money/player transfer logic
Avoid fat controllers
Avoid duplicated logic
Avoid magic numbers; use constants/enums where possible
Use meaningful method names
Use dependency injection
Use Eloquent relationships correctly
Use eager loading where needed
Write readable and maintainable code
```

Suggested constants/enums:

```php
PlayerPosition::GOALKEEPER
PlayerPosition::DEFENDER
PlayerPosition::MIDFIELDER
PlayerPosition::ATTACKER

TransferListingStatus::ACTIVE
TransferListingStatus::SOLD
TransferListingStatus::CANCELLED
```

---

## 12. Expected Final Deliverable

Codex-მა უნდა შექმნას სრული Laravel API project შემდეგით:

```text
Migrations
Models
Relationships
Factories
Seeders if needed
Controllers
Services
Policies
Form Requests
API Resources
Localization files
Middleware for locale
Routes
Tests
README.md with setup instructions
```

README-ში დაამატე:

```text
Installation steps
.env setup
Migration command
Test command
API endpoint list
Authentication usage
Example request/response
```

Final code must pass:

```bash
composer test
php artisan test
php artisan route:list
```

არ დატოვო TODO comments. არ გამოიყენო temporary code. ყველაფერი უნდა იყოს დასრულებული და გაშვებადი.

## Mandatory DTO, Form Request and API Resource Architecture

Codex-მა აუცილებლად უნდა გამოიყენოს:

```text
DTO classes
Form Request classes
Laravel API Resources
Service classes
Policies
```

Controller-ში არ უნდა მოხდეს validation, authorization logic, response formatting ან business logic.

Controller-ის flow უნდა იყოს ასეთი:

```php
public function update(UpdateTeamRequest $request): JsonResponse
{
    $team = $this->teamService->update(
        $request->user(),
        UpdateTeamData::fromRequest($request)
    );

    return $this->success(
        __('messages.team.updated'),
        new TeamResource($team)
    );
}
```

## DTO Requirements

შექმენი DTO classes:

```text
app/DTOS/RegisterUserData.php
app/DTOS/LoginUserData.php
app/DTOS/UpdateTeamData.php
app/DTOS/UpdatePlayerData.php
app/DTOS/CreateTransferListingData.php
```

DTO class-ებმა უნდა მიიღონ მხოლოდ validated data Form Request-ებიდან.

Example:

```php
final readonly class UpdateTeamData
{
    public function __construct(
        public string $name,
        public string $country,
    ) {}

    public static function fromRequest(UpdateTeamRequest $request): self
    {
        $data = $request->validated();

        return new self(
            name: $data['name'],
            country: $data['country'],
        );
    }
}
```

DTO არ უნდა შეიცავდეს business logic-ს. DTO მხოლოდ data transfer-ს ემსახურება.

## Form Request Requirements

ყველა validation უნდა იყოს Form Request class-ში.

შექმენი:

```text
app/Http/Requests/Auth/RegisterRequest.php
app/Http/Requests/Auth/LoginRequest.php
app/Http/Requests/Team/UpdateTeamRequest.php
app/Http/Requests/Player/UpdatePlayerRequest.php
app/Http/Requests/Transfer/CreateTransferListingRequest.php
```

Example:

```php
final class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
        ];
    }
}
```

Authorization ownership checks უნდა მოხდეს Policy-ში ან Service-ში `Gate::authorize()` გამოყენებით და არა validation rules-ში.

## API Resource Requirements

ყველა API response უნდა დაბრუნდეს Laravel API Resources-ით.

არ დააბრუნო raw Eloquent models ან arrays Controller-იდან.

შექმენი:

```text
app/Http/Resources/UserResource.php
app/Http/Resources/TeamResource.php
app/Http/Resources/PlayerResource.php
app/Http/Resources/TransferListingResource.php
```

Example TeamResource:

```php
final class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'budget' => (float) $this->budget,
            'team_value' => (float) $this->players->sum('market_value'),
            'players' => PlayerResource::collection(
                $this->whenLoaded('players')
            ),
        ];
    }
}
```

Example PlayerResource:

```php
final class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'position' => $this->position,
            'age' => $this->age,
            'market_value' => (float) $this->market_value,
            'team_id' => $this->team_id,
        ];
    }
}
```

## Unified API Response

Resources უნდა დაბრუნდეს unified response wrapper-ით.

შექმენი trait:

```text
app/Traits/ApiResponse.php
```

Example:

```php
trait ApiResponse
{
    protected function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message, mixed $errors = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
```

Controller response example:

```php
return $this->success(
    __('messages.player.updated'),
    new PlayerResource($player)
);
```

Collection response example:

```php
return $this->success(
    __('messages.market.list'),
    TransferListingResource::collection($listings)
);
```

## Controller Rule

Controllers must only do:

```text
Receive request
Call DTO::fromRequest()
Call service
Return API Resource inside unified response
```

Controllers must NOT do:

```text
Validation manually
Business logic
Database transaction logic
Budget calculation logic
Player generation logic
Ownership checks directly with if statements
Raw model response
```

## Final Architecture Flow

Use this structure everywhere:

```text
Route
→ Controller
→ Form Request
→ DTO
→ Policy/Gate
→ Service
→ Model/Repository if needed
→ API Resource
→ Unified JSON Response
```

This architecture is mandatory for the whole project.
