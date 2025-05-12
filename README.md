Here's a comprehensive `README.md` for your Laravel train display application submission:

````markdown
# Centric Ltd - Station Display Application

## Features

✔ Device registration interface with token storage  
✔ Real-time train schedule display  
✔ Automatic 30-second content refresh  
✔ Large, high-contrast fonts for public displays  
✔ MySQL database integration  
✔ Responsive design for various screen sizes

## Installation

### Prerequisites

-   PHP 8.1+
-   MySQL 8.0+
-   Composer 2.0+
-   Node.js 16+ (for asset compilation)

# Clone repository

git clone https://github.com/Ochiengjeck/Train-Display.git
cd Train-Display

# Install PHP dependencies

composer install

# Install JavaScript dependencies (if using frontend assets)

npm install
npm run build

## Configuration

1. Create database:

```sql
CREATE DATABASE train_display;
CREATE USER 'display_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT ALL PRIVILEGES ON train_display.* TO 'display_user'@'localhost';
FLUSH PRIVILEGES;
```
````

2. Setup environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Configure `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=train_display
DB_USERNAME=display_user
DB_PASSWORD=SecurePassword123!

# For production
APP_ENV=production
APP_DEBUG=false
```

4. Run migrations:

```bash
php artisan migrate
```

## Usage

Start development server:

```bash
php artisan serve
```

Access at: http://localhost:8000

## Testing

### Test Credentials

```yaml
Station:
  ID: 1 (Hardcoded for Nairobi Central)
  Name: "Mombasa" (Pre-registered)

Device:
  Serial: TEST123
  Model: DISPLAY-001
```

### Automated Tests

Run PHPUnit tests:

```bash
php artisan test
```

## License

This project is for evaluation purposes only.
