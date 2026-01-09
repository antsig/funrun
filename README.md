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
    composer install
    ```

    > [!TIP] > **Vendor Optimization**: To reduce the size of the `vendor` folder (removing testing tools like PHPUnit/Faker), use the `--no-dev` flag. This is recommended for production/hosting:
    >
    > ```bash
    > composer install --no-dev --optimize-autoloader
    > ```

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
  - Payment Status Check
- **Admin**:
  - Dashboard (Statistics)
  - Event Management (Events & Categories)
  - Order Management (Verify payments, Generate BIB)
  - Secure Login with OTP support

## 📂 Project Structure

- `app/Controllers`: Application logic (Admin, Auth, Registration).
- `app/Database`: Migrations and Seeds.
- `app/Views`: HTML Templates.
- `public`: Web root (assets, index.php).
- `vendor`: Composer dependencies (Don't edit manually).
