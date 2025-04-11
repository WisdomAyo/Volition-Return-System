# Fund Return Management System

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

A complete solution for managing investment fund returns with compounding calculations, historical tracking, and reversion capabilities.

## Features

### Core Functionality
- 🏦 Fund creation with starting balance
- 📈 Monthly/quarterly/yearly return tracking
- 🔄 Both compounding and non-compounding returns
- ⏪ Clean return reversion with accurate value restoration
- 📅 Historical value queries for any date

### Deliverables
✅ Well-structured Laravel models and migrations  
✅ Proper use of Eloquent relationships  
✅ Accurate return calculation logic  
✅ Clean revert functionality  
✅ Historical value tracking  
✅ Complete CLI and API interfaces  

## Installation
## 1. Manual Installation
# git clone https://github.com/WisdomAyo/Volition-Return-System.git

cd Volition-Return-System

## 2. Install dependencies:
composer install


### 3. One-Command Setup
Run this to install dependencies, migration, setup database, seeder, and configure the application:

```bash
php artisan app:install
````


## 4. Start development server:
php artisan serve

### CLI Commands
```
php artisan fund:add-return 1 2023-01-01 5 --frequency=monthly --compounding -- Add a Return
```
----- Example : php artisan fund:add-return 1 2023-01-01 5 --frequency=monthly --compounding
```
php artisan fund:revert-return {return_id} --Revert a Return

php artisan fund:value {fund_id} {date}  - Get Fund Value
```


## API Documentation

### Base URL
`https://yourdomain.com/api`

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST   | `api/funds` | Create new fund |
| GET    | `api/funds/{id}` | Get fund details |
| POST   | `api/funds/{id}/returns` | Add return to fund |
| POST   | `api/returns/{id}/revert` | Revert a return |
| GET    | `api/funds/{id}/value-at-date?date=yyy-mm-dd` | Get historical value |

