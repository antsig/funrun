# FunRun Gorontalo 2026

Web application for managing the FunRun event. This system handles participant registration, payments (via Midtrans), and event management.

## 📋 Requirements

- PHP version 8.1 or higher
- Composer
- MySQL Database
- [Midtrans Account](https://midtrans.com/) (Server Key & Client Key)

## 🚀 Installation & Setup

1.  **Clone the repository** (if you haven't already):

    ```bash
    git clone <repository_url>
    cd funrun
    ```

2.  **Install Dependencies**:

    ```bash
    composer install --no-dev --optimize-autoloader
    ```

3.  **Environment Setup**:
    Copy `env` to `.env` and configure your database and Midtrans keys.

    ```bash
    cp env .env
    ```

    Open `.env` and update:

    ```ini
    CI_ENVIRONMENT = development # Change to 'production' when live

    # Database
    database.default.hostname = localhost
    database.default.database = funrun_db
    database.default.username = root
    database.default.password =
    database.default.DBDriver = MySQLi

    # Midtrans
    MIDTRANS_SERVER_KEY = your_server_key
    MIDTRANS_CLIENT_KEY = your_client_key
    MIDTRANS_IS_PRODUCTION = false
    ```

4.  **Database Migration & Seeding**:
    Create tables and insert default data (Admin & Initial Event):
    ```bash
    php spark migrate
    php spark db:seed InitialSeeder
    ```

## 🏃‍♂️ Running the App

Start the built-in server:

```bash
php spark serve
```

Access the application at `http://localhost:8080`.

## 🔐 Default Credentials

### Admin Panel

- **Login URL**: `http://localhost:8080/admin/login`
- **Email**: `admin@funrun.com`
- **Password**: `admin123`

## 🛠 Features

- **Public**:
  - Event Registration (Single/Group)
  - Checkout & Payment (Midtrans Gateway)
  - Manual Payment Proof Upload
  - Payment Status Check
  - Event Countdown Timer
- **Admin**:
  - **Dashboard**:
    - Real-time Statistics & Daily Trend Charts.
    - Conversion Rate Tracking.
    - Revenue & Ticket Sales Visuals.
  - **Event Management**: Create Events & Categories, Manage Quotas.
  - **Order Management**: Verify payments, Filter by Status, BIB Generation.
  - **Reports Module**:
    - Orders & Participants Report.
    - Export to Excel (.xls) with Styling.
  - **Social Media Management**: Manage links with auto-detected icons.
  - **Settings**:
    - General Site Settings.
    - Email Configuration (SMTP Port, Crypto).
  - **System Tools (Hardened)**:
    - Database Backup & Restore (Production-Safe).
    - Source Code Backup.
    - Audit Logs for critical actions.
- **System**:
  - **Service Layer Architecture**: Decoupled logic.
  - **Async Email Queue**: Reliable background email sending.
  - **Read-Only API**: Protected endpoints.

## ⏰ Cron Job Setup (Email Queue)

To process the email queue, set up a cron job to run the following command every minute:

```bash
php spark email:process
```

## 🔐 API Security

To access the Read-Only API, add the following to your `.env` file and use it as `X-API-TOKEN` header:

```ini
API_READ_TOKEN=your_secure_secret_token
```

## 📂 Project Structure

- `app/Controllers`: Application logic (Admin, Auth, Registration).
- `app/Database`: Migrations and Seeds.
- `app/Views`: HTML Templates.
- `public`: Web root (assets, index.php).
- `vendor`: Composer dependencies (Don't edit manually).
