# Admin & User Wallet System

This project is a comprehensive wallet system with separate admin and user functionalities, built with Laravel. It includes custom permission handling, notifications, referral codes, and a JSON API for users.

## Features

### 🔗 Referral Code System
- **Admin Referral Codes**: Admins can generate referral codes from their dashboard
- **User Referral Codes**: Users can generate referral codes via API or web interface
- **Multiple Usage**: Referral codes can be used multiple times by different users
- **Latest Code Only**: Only the most recently generated code for each user/admin is active
- **Bonus System**: Both the referrer and new user receive 10 EGP when a referral code is used
- **Transaction Tracking**: All referral bonuses are recorded as transactions

### 💰 Top-up Requests
- **User Requests**: Users can create top-up requests via API or web interface
- **Admin Approval**: Admins can approve or reject top-up requests from their dashboard
- **Automatic Processing**: Approved requests automatically add funds to user wallets
- **Notifications**: Users receive email notifications about request status
- **Admin Notifications**: Admins get dashboard notifications for new requests

### 🏦 Withdrawal System
- **Admin Withdrawals**: Admins can request withdrawals from their own wallets
- **Peer Approval**: Other admins can approve or reject withdrawal requests
- **Held Balance**: Withdrawal amounts are held until approved/rejected
- **Balance Management**: Proper balance and held balance tracking

### 📄 Pagination System
- **User API Pagination**: Top-up and withdrawal request lists are paginated (10 items per page)
- **Admin Dashboard Pagination**: All dashboard sections use pagination (5 items per page):
  - Pending Top-up Requests
  - Pending Withdrawal Requests
  - Sent Email Notifications
  - Users Registered Through Referral Codes
- **Independent Pagination**: Each section maintains its own pagination state

### 🔐 Authentication & Permissions
- **Separate Guards**: Admin and user authentication with separate guards
- **Manual Permissions**: JSON-based permission system for admins
- **Policy-Based Authorization**: Laravel policies for transaction management
- **API Authentication**: Sanctum-based API authentication for users

## Project Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/wallet_system.git
   cd wallet_system
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment variables:**
   - Create a copy of the `.env.example` file and name it `.env`
   - Configure your database and email settings in the `.env` file
   - Run `php artisan key:generate`

4. **Run database migrations:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Run the development server:**
   ```bash
   php artisan serve
   ```

## Default Accounts

After seeding, the following accounts are available:

### Admins
- **Email**: `admin@example.com` | **Password**: `password`
- **Email**: `joe@example.com` | **Password**: `password`

### Users
- **Email**: `user@example.com` | **Password**: `password`

### Referral Codes
- **Admin**: `ADMIN123`
- **Joe**: `JOE456`
- **User**: `USER789`

## API Endpoints

### Authentication
- `POST /api/register` - User registration (referral code is optional)
- `POST /api/login` - User login

### User API (requires authentication)
- `GET /api/user` - Get authenticated user info
- `GET /api/top-up-requests` - List user's top-up requests (paginated, 10 per page)
- `POST /api/top-up-requests` - Create top-up request
- `GET /api/top-up-requests/{id}` - Get specific top-up request
- `GET /api/withdrawal-requests` - List user's withdrawal requests (paginated, 10 per page)
- `POST /api/withdrawal-requests` - Create withdrawal request
- `POST /api/referral-codes/generate` - Generate new referral code
- `GET /api/referral-codes/show` - Show current active referral code
- `GET /api/referrals` - Get users who registered using your referral code

### Admin Web Interface
- `GET /admin/login` - Admin login page
- `POST /admin/login` - Admin login
- `GET /admin` - Admin dashboard
- `POST /admin/logout` - Admin logout

### Admin API (requires authentication)
- `GET /admin/top-up-requests` - List all top-up requests
- `POST /admin/top-up-requests/{id}/approve` - Approve top-up request
- `POST /admin/top-up-requests/{id}/reject` - Reject top-up request
- `GET /admin/withdrawal-requests` - List withdrawal requests
- `POST /admin/withdrawal-requests` - Create withdrawal request
- `POST /admin/withdrawal-requests/{id}/approve` - Approve withdrawal request
- `POST /admin/withdrawal-requests/{id}/reject` - Reject withdrawal request
- `POST /admin/referral-codes/generate` - Generate admin referral code

### User Web Interface
- `GET /user/dashboard` - User dashboard (requires authentication)
- `POST /user/referral-codes/generate` - Generate user referral code
- `POST /user/top-up-requests` - Create top-up request via web

## Usage Examples

### Register without Referral Code
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'
```

### Register with Referral Code
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password",
    "password_confirmation": "password",
    "referral_code": "ADMIN123"
  }'
```

### Generate Referral Code
```bash
curl -X POST http://localhost:8000/api/referral-codes/generate \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### Create Top-up Request
```bash
curl -X POST http://localhost:8000/api/top-up-requests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100}'
```

### List Top-up Requests (with pagination)
```bash
curl -X GET "http://localhost:8000/api/top-up-requests?page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

### List Withdrawal Requests (with pagination)
```bash
curl -X GET "http://localhost:8000/api/withdrawal-requests?page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

## Database Schema

### Tables
- `users` - User accounts
- `admins` - Admin accounts with JSON permissions
- `wallets` - Wallet balances for users and admins
- `transactions` - All financial transactions (top-up, withdrawal, referral_bonus)
- `referral_codes` - Referral codes with owner tracking
- `notifications` - System notifications

### Key Relationships
- Users/Admins have one Wallet
- Wallets have many Transactions
- Users/Admins have many ReferralCodes
- Only the latest ReferralCode per owner is active

## Notifications

- **Email Notifications**: Sent for top-up request status updates
- **Dashboard Notifications**: Admin dashboard shows new requests
- **Database Notifications**: Stored for admin dashboard display

## Testing

Use the provided Postman collection (`wallet_system.postman_collection.json`) to test all API endpoints. The collection includes:

- User registration and authentication
- Referral code generation and usage
- Top-up request creation and management
- Admin dashboard functionality

## Development Notes

- **Referral Codes**: Can be used multiple times, but only the latest generated code per user/admin is active
- **Permissions**: All admin actions are controlled through the TransactionPolicy
- **Notifications**: Uses Laravel's notification system with database and mail drivers
- **API**: RESTful API with proper authentication and validation
- **Web Interface**: Bootstrap-based responsive design for both admin and user dashboards

## Security Features

- Separate authentication guards for admins and users
- Policy-based authorization for all sensitive operations
- Input validation on all endpoints
- CSRF protection for web forms
- Proper session management 

## Receive Notifications: 
  - All admins are notified in the dashboard when a new request is created. 
  - Email notification is also sent. 
  - The admin who created the request gets a specific notification (both in the dashboard & email).