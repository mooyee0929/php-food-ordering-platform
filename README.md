# PHP Food Ordering Platform

A food-ordering web application built with PHP, MySQL (through PDO) and Bootstrap. Users register with a location and a wallet, search nearby shops by name, category, distance, meal or price range, and browse each shop's menu with product photos. Any user can open a shop and manage its menu: add dishes with an uploaded photo, change prices and stock, and remove items. Accounts are protected with salted SHA-256 password hashes and PHP sessions. The sign-up and shop-registration forms check name availability live over AJAX.

## Features

**Accounts**
- Registration (`registerpage.php` → `register.php`) validates every field on the server:
  - name: letters only
  - phone number: exactly 10 digits
  - account and password: alphanumeric only
  - password confirmation must match
  - latitude and longitude: within valid ranges
- `ajaxregister.php` reports whether an account name is already taken while the user types.
- Passwords are stored as `sha256(salt . password)` with a random per-user salt (`register.php`, `login.php`).
- Login creates a session holding the profile, location and wallet balance. `home.php` redirects to the login screen when the session is not authenticated.

**Profile and wallet** (`home.php`)
- Shows account details and lets the user edit their saved latitude and longitude.
- Wallet top-up updates the balance and inserts a `Recharge` entry into the `record` table.

**Shop search** (`search.php`)
- Filters by shop-name substring, shop category, distance bucket (`near`, `medium`, `far`), meal name, and a lower and/or upper price bound. Filters can be combined.
- Each matching shop appears in a results table, and its menu opens in a modal with product photo, name, price and quantity.

**Running a shop**
- "Start a business" registers a shop with a name, category and location (`shop.php`). `ajaxshop.php` checks shop-name availability live, and the registering user is recorded as the shop's manager.
- Adding a product stores its name, price, quantity and an uploaded photo (`add-food.php`). The photo is base64-encoded into the `image` table.
- Each product row can be edited (price and quantity) or deleted from the shop's menu table.

The "MyOrder", "Shop Order" and "Transaction Record" tabs are laid out in the UI with status filters, but have no backend logic yet.

## Data model

The repository does not include a schema file. The code expects the following tables and columns:

| Table | Columns used |
| --- | --- |
| `users` | `UID`, `Account`, `Password`, `Salt`, `Name`, `PhoneNumber`, `Longitude`, `Latitude`, `Balance` |
| `store` | `SID`, `Name`, `Category`, `Latitude`, `Longitude` |
| `manager` | `UID`, `SID` (which user manages which shop) |
| `product` | `PID`, `SID`, `Name`, `Price`, `Amount` |
| `image` | `PID`, `Image` (base64 text), `ImageType` (MIME type) |
| `record` | `UID1`, `UID2`, `Amount`, `Time`, `Action` |

## Repository structure

| Path | Purpose |
| --- | --- |
| `loginpage.php`, `login.php` | Sign-in form and authentication handler |
| `registerpage.php`, `register.php`, `ajaxregister.php` | Sign-up form, registration handler, live account-name check |
| `home.php` | Main app with tabs: profile, wallet, search results, shop management |
| `search.php` | Builds the filtered shop list and stores it in the session |
| `shop.php`, `ajaxshop.php` | Shop registration and live shop-name check |
| `add-food.php` | Product creation with image upload |
| `css/`, `sass/`, `js/`, `fonts/`, `images/` | Front-end assets (Bootstrap 3, jQuery, Animate.css, icon fonts) |
| `Picture/` | Sample food photos for product uploads |

The sign-in and sign-up pages are based on a free HTML5 login template by FreeHTML5.co, credited in the page footers.

## Requirements

- PHP 7.3 or newer with the `pdo_mysql` extension. The code uses heredoc blocks with indented closing markers, which need 7.3+.
- MySQL or MariaDB
- A web server that runs PHP, such as Apache (XAMPP, MAMP or similar stacks work)

## Setup

1. Copy the repository into the web server's document root.
2. Create a MySQL database named `food_ordering` containing the tables listed under [Data model](#data-model).
3. Check the connection settings at the top of each PHP file (`$DATABASE_HOST`, `$DATABASE_NAME`, `$DATABASE_USER`, `$DATABASE_PASS`, or `$dbservername`, `$dbname`, `$dbusername`, `$dbpassword`). They default to database `food_ordering` on `localhost`, with user `root` and an empty password.
4. Open `loginpage.php` in a browser and use the Sign Up link to create an account.

## Known limitations

This is a prototype. Some queries in `search.php` and `home.php` build SQL by concatenating request values instead of binding parameters. The wallet top-up computes the new balance in the browser and passes it in the query string. Both should be hardened before any real deployment.
