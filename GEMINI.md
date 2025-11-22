# GEMINI.md

## Project Overview

This project is a web-based inventory management system named "H&S Inventory Management System". It is built with PHP and uses a custom-built MVC (Model-View-Controller) framework. The application allows users to manage products, track stock levels, and handle inventory transactions. It also includes features for generating QR codes and barcodes for products.

### Features
*   **Bulk Data Import**: Users can bulk add products and stock using CSV files. The application provides sample CSV files for download.
*   **Enhanced Logging**: The activity logs are now categorized for easier viewing. A new "Error Log" tab has been added to display PHP errors for development purposes.
*   **Improved Reports**: Reports have been cleaned up to remove redundant ID columns, making them more readable.
*   **AJAX Master Data Management**: Add, edit, and delete operations for Categories, Sizes, Departments, Locations, and Sellers now use AJAX for a smoother user experience, preventing full page reloads.

### Key Technologies

*   **Backend:** PHP 8.2 (or higher)
*   **Frontend:** HTML, CSS, JavaScript (with Tailwind CSS for styling, and a centralized `master-data-ajax.js` for AJAX interactions)
*   **Database:** MySQL (using PDO for database access)
*   **Dependencies:**
    *   `endroid/qr-code`: For generating QR codes.
    *   `picqer/php-barcode-generator`: For generating barcodes.
    *   `vlucas/phpdotenv`: For loading environment variables from a `.env` file.

### Architecture

The application follows a basic MVC architecture:

*   **Models:** Located in `app/models/`, they handle database interactions. Each model corresponds to a database table and extends `App\Core\Model`.
*   **Views:** Located in `app/views/`, they contain the presentation logic (HTML, PHP, and JavaScript). Views can be loaded with or without a main layout (`app/views/layouts/main.php`).
*   **Controllers:** Located in `app/controllers/`, they handle user requests, interact with models, and load the appropriate views. Controllers extend `App\Core\Controller` which provides common functionalities like database access, view loading, redirection, authentication checks, logging, and notification management.

The application uses a simple router (in `app/core/Router.php`) that maps URLs to controller actions based on an exact match. Routes are defined in `index.php`. For example, the route `/activity-logs` is mapped to the `index` method of the `LogController`. The entry point for the application is `index.php`.

Configuration is managed via environment variables loaded from a `.env` file, with a fallback to `config.local.php` for legacy setups. Error logging is directed to `logs/error.log`.

## Building and Running

This is a web-based PHP application, so it needs to be run on a web server with PHP and a MySQL database.

### Prerequisites

*   A web server (like Apache or Nginx)
*   PHP 8.2 or higher
*   MySQL database
*   Composer for installing PHP dependencies

### Setup Instructions

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    ```
2.  **Install dependencies:**
    ```bash
    composer install
    ```
3.  **Database Setup:**
    *   Create a MySQL database.
    *   Import the database schema from `database/schema.sql`.
4.  **Configuration:**
    *   Copy `.env.example` to `.env` and configure your database connection and other settings.
    *   The application will redirect to `installer.php` if not installed.
5.  **Running the application:**
    *   Point your web server's document root to the project's `public/` directory.
    *   Access the application through your web browser.

### Running Tests

Automated tests are available using PHPUnit.
```bash
composer test
```

## Development Conventions

*   **Coding Style:** While no formal coding standard is strictly enforced, `php_codesniffer` is used for linting.
    *   Linting: `composer lint`
    *   Fixing linting issues: `composer lint:fix`
*   **Static Analysis:** PHPStan is used for static analysis to catch potential errors.
    *   Run analysis: `composer analyse`
*   **Database Access:** Database interactions are handled through models using PDO with prepared statements to prevent SQL injection.
*   **Routing:** Routes are defined in `index.php` and handled by the `Router` class, which uses exact URL matching.
*   **Frontend:** The frontend uses Tailwind CSS for styling and vanilla JavaScript for UI interactions, with `master-data-ajax.js` managing AJAX operations for master data.
*   **Error Handling:** Errors are logged to `logs/error.log`. A new "Error Log" tab is available in the logs section for easier debugging during development.
*   **Authentication:** User authentication is managed via PHP sessions, with a `requireAuth` helper in the base controller.
*   **CSRF Protection:** Implemented in frontend AJAX requests to prevent cross-site request forgery.
