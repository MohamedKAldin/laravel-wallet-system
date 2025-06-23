# Admin & User Wallet System

This project is a wallet system with separate admin and user functionalities, built with Laravel. It includes custom permission handling, notifications, and a JSON API for users.

## Project Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/wallet_system.git
    cd wallet_system
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    ```

3.  **Set up environment variables:**
    - Create a copy of the `.env.example` file and name it `.env`.
    - Configure your database and email settings in the `.env` file.
    - Run `php artisan key:generate`

4.  **Run database migrations:**
    ```bash
    php artisan migrate
    ```

5.  **Run the development server:**
    ```bash
    php artisan serve
    ```

## Development Steps

### 1. Project Initialization
- Created a new Laravel 10.2.5 project named `wallet_system`.

### 2. Authentication
- **Admin Guard:** Set up a separate guard for admin authentication in `config/auth.php`.
- **User Guard (API):** Configured API authentication for users using Laravel Sanctum.
- Created `Admin` model and migration.

### 3. Database Schema
- Created migrations for `wallets`, `transactions`, and `referral_codes` tables.
- Defined relationships between models (`User`, `Admin`, `Wallet`, `Transaction`, `ReferralCode`).

### 4. Core Functionality
- **Referral System:**
    - Implemented `ReferralService` to handle referral code generation and application.
    - Added user registration with referral code support.
- **Top-up Requests:**
    - Users can create top-up requests via the API.
    - Admins can view, approve, and reject top-up requests.
- **Withdrawal Requests:**
    - Admins can create withdrawal requests.
    - Other admins with permissions can approve or reject withdrawal requests.
- **Notifications:**
    - Database notifications for new top-up and withdrawal requests for admins.
    - Email notifications for users and admins when their requests are approved or rejected.

### 5. Permissions
- Implemented a manual permission system using a JSON column on the `admins` table.
- Used Laravel Policies to authorize actions.

## Postman Collection

A Postman collection for the User API is available in the root of the project: `wallet_system.postman_collection.json`.

Import this file into Postman to test the API endpoints. You will need to set the `base_url` and `token` variables in Postman.
The `token` is returned in the response of the `Register` request. 