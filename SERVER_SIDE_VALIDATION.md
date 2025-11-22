# SERVER_SIDE_VALIDATION.md

This document outlines the locations in the codebase where server-side validation should be implemented or enhanced to prevent NULL values for specific critical fields, as discussed.

## Validation Strategy:
The goal is to ensure that values for the identified columns are never `NULL` when submitted through forms. This will be achieved by adding explicit checks in the relevant controller methods before data is passed to the models for persistence. If a required field is `NULL` or empty, an error message will be generated, and the form submission will be rejected.

---

### `handsdb.inventory`

**Columns to validate:** `size_id`, `quantity`, `min_quantity`
*(Note: `min_quantity` is often configured at the product level, but if it can be set during stock operations, it needs validation here.)*

**Validation Location:**
*   **File:** `app/Controllers/StockController.php`
*   **Method:** `validateStockData(array $data, string $type = 'add')`
    *   This method is called by `add()`, `out()`, and `transfer()` actions.
    *   **`size_id`**: Ensure `size_id` is present and refers to a valid size.
    *   **`quantity`**: Ensure `quantity` is present, is a positive integer, and potentially within reasonable bounds.
    *   **`min_quantity`**: If `min_quantity` is submitted via stock forms, validate it as a non-negative integer.

---

### `handsdb.products`

**Columns to validate:** `product_type`, `category_id`, `type_id`, `low_stock_threshold`, `available_sizes`

**Validation Location:**
*   **File:** `app/Controllers/ProductController.php`
*   **Methods:**
    *   `add()`: Implement a new `validateProductData()` method and call it here before adding a new product.
    *   `edit()`: Call the same `validateProductData()` method before updating an existing product.
    *   `bulkAdd()`: This method handles CSV imports. Server-side validation for each row of the CSV should occur here, ensuring `product_type`, `category_id`, `type_id`, `low_stock_threshold`, and `available_sizes` are valid. This might involve iterating through the parsed CSV data and applying similar checks.

---

### `handsdb.stock_transactions`

**Columns to validate:** `type_id`, `department_id`, `size_id`, `user_id`, `seller_id`

**Validation Location:**
*   **Context:** `stock_transactions` records are typically created internally by the system during stock `add`, `out`, and `transfer` operations. The values are derived from other forms or session data.
*   **File:** `app/Controllers/StockController.php`
*   **Methods:**
    *   `add()`: When creating an 'in' transaction.
    *   `transfer()`: When creating 'out' and 'in' transactions for a transfer.
    *   **`type_id`**: This is determined by the application logic (e.g., 'in', 'out', 'transfer'). Validation ensures the application correctly sets this.
    *   **`department_id`**: For 'out' transactions, ensure it's provided via the form and is valid. This should be part of `StockController::validateStockData()`.
    *   **`size_id`**: Ensure it's provided via the form and is valid. This should be part of `StockController::validateStockData()`.
    *   **`user_id`**: Derived from `$_SESSION['user_id']`. Ensure user is logged in (handled by `requireAuth()`).
    *   **`seller_id`**: For 'in' transactions, ensure it's provided via the form and is valid. This should be part of `StockController::validateStockData()`.

---

### `handsdb.stock_transfer`

**Columns to validate:** `size_id`, `created_by`

**Validation Location:**
*   **File:** `app/Controllers/StockController.php`
*   **Method:** `transfer()`
    *   **`size_id`**: Ensure `size_id` is present and valid in the transfer form. This will be part of `StockController::validateStockData()`.
    *   **`created_by`**: Derived from `$_SESSION['user_id']`. Ensure user is logged in (handled by `requireAuth()`).

---

### `handsdb.types`

**Columns to validate:** `category_id`

**Validation Location:**
*   **File:** `app/Controllers/ConfigController.php`
*   **Method:** `types()`
    *   When `action` is 'add' or 'edit' for a type.
    *   Implement validation to ensure `category_id` is provided and refers to an existing category.

---

### `user sessions`

**Columns to validate:** `ip_address`, `user_agent`

**Validation Location:**
*   **Context:** These are automatically captured by the server and saved during login. They are not direct user input.
*   **File:** `app/Controllers/AuthController.php`
*   **Methods:** `login()` and `addFailedLoginLog()`
    *   Ensure that `$_SERVER['REMOTE_ADDR']` or `$_SERVER['HTTP_X_FORWARDED_FOR']` is captured for `ip_address`. If not available, provide a default (e.g., 'Unknown' or an empty string, depending on database schema allowing empty strings).
    *   Ensure `$_SERVER['HTTP_USER_AGENT']` is captured for `user_agent`. If not available, provide a default.
    *   The `addLog` method already attempts to get the IP. The main change here would be to ensure the database schema for these columns does not disallow empty strings if `NULL` is to be avoided, or explicitly set a default non-NULL value in code.
