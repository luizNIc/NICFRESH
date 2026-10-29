# NIC Fresh Supplies

A single-vendor farm-to-consumer produce e-commerce site for Masaka, Uganda.
Built with HTML, CSS, vanilla JavaScript, PHP (PDO), and MySQL.

## 1. Local Setup (XAMPP / MAMP / Laragon)

1. Copy the `nicfresh` folder into your server's web root (e.g. `htdocs/nicfresh`).
2. Create the database by importing the schema:
   - Open phpMyAdmin → **Import** → select `database/schema.sql`, OR
   - Command line: `mysql -u root -p < database/schema.sql`
3. Open `config/db.php` and confirm `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` match your MySQL setup (defaults work for a fresh XAMPP install: user `root`, empty password).
4. Visit `http://localhost/nicfresh/` in your browser.

**Already deployed and don't want to reset your data?** Run these migration files instead of re-importing the full schema: `database/add-contact-messages-table.sql` (adds the Support form's message inbox), `database/fix-product-images.sql` (points seeded produce products at local photos), `database/add-customer-accounts.sql` (adds customer accounts and links new orders to their owner), and `database/add-visit-analytics.sql` (adds consent-based customer visit records; run after the accounts migration, once only). For installations using the old seeded admin credentials, create a replacement admin account and then run `database/remove-default-admin.sql`.

For an existing hosted site, upload the complete `images/` folder and run the relevant SQL migrations against the hosted database using the host's phpMyAdmin. The filenames in the database must match the uploaded filenames exactly on case-sensitive Linux hosting. These migrations update seeded produce image paths and add customer account support without resetting products or orders.

Visitors must sign in or create a customer account before accessing the public storefront. After sign-in, first-time visitors are shown the analytics choice before continuing to the home page. Customers can register using their name, email, phone, and a password; they can then review their order history under **My Account**. Customer accounts are also required to place new orders. Existing orders are preserved and remain unlinked to accounts.

Consented analytics records a signed-in customer's account, visited page, and server timestamp in `customer_page_visits`. Administrators can view the latest records under **Admin → Analytics**. Signed-in customers can manage consent from **My Account**; withdrawing consent deletes their recorded visit history. Anonymous visitor identities are not collected; GA4 does not receive customer names or emails.

The schema does not create a default administrator. Create an admin password hash locally with `php -r "echo password_hash('replace-with-a-unique-password', PASSWORD_DEFAULT), PHP_EOL;"`, then insert an admin account in MySQL/phpMyAdmin using that hash:

```sql
INSERT INTO admin_users (username, password_hash)
VALUES ('your-admin-name', 'paste-the-generated-hash-here');
```

For an older installation, create and verify the replacement account before running `database/remove-default-admin.sql`. The admin login is not linked from the customer storefront, and protected admin pages still require an authenticated admin session; hiding the login URL alone is not a security control.

## 2. Free Hosting Deployment

This is a PHP + MySQL site, so it needs a host that supports both (a plain static host will not run the PHP files).

Good free options:
- **InfinityFree** (infinityfree.net) — free PHP + MySQL hosting, no card required
- **000webhost** — free PHP + MySQL hosting
- **Render / Railway** — if you containerize with a small Dockerfile (more setup, but works for a PHP+MySQL stack too)

Steps (InfinityFree-style, similar on others):
1. Create a free account and a new hosting subdomain.
2. Use the File Manager or FTP to upload everything **inside** the `nicfresh` folder to your `htdocs`/`public_html`.
3. Create a MySQL database in the host's control panel, then import `database/schema.sql` via phpMyAdmin.
4. Update `config/db.php` with the database host, name, username, and password the control panel gives you (these are almost never `localhost`/`root` on shared hosting).
5. Visit your live subdomain to confirm the site loads.

## 3. Security Measures Applied

- **Prepared statements everywhere** (PDO with `ATTR_EMULATE_PREPARES => false`) — no raw user input is ever concatenated into SQL, preventing SQL injection.
- **Password hashing** — admin passwords are stored with PHP's `password_hash()` (bcrypt) and checked with `password_verify()`; plaintext passwords are never stored.
- **Session regeneration on login** (`session_regenerate_id`) to reduce session fixation risk.
- **Output escaping** — all dynamic output is passed through `h()` (an `htmlspecialchars` wrapper) to prevent stored/reflected XSS.
- **Server-side total recalculation** at checkout — the client never dictates the price paid; `process-order.php` recalculates subtotal and delivery fee from the database and session cart.
- **Admin page authentication** — protected admin pages redirect unauthenticated visitors to the admin login. Use a unique administrator password and HTTPS; hiding the login link does not replace authentication.

For a production deployment you would add HTTPS (most free hosts provide a free SSL certificate you can enable in the control panel) and rate-limit the admin login form.

## 4. Google Analytics (GA4) Setup

1. Create a free GA4 property at [analytics.google.com](https://analytics.google.com).
2. Add a Web data stream for your live URL and copy the Measurement ID (`G-XXXXXXX`).
3. Set `$ga_measurement_id` in `config/analytics.php` to that ID and upload the file with the site. The public pages show an optional consent notice; analytics events are sent only after a visitor accepts.
4. Once the site has live traffic, use GA4's **Reports → Life cycle → Engagement → Pages and screens** for most-viewed pages, and **Acquisition → Traffic acquisition** for traffic sources, to write your performance report.

## 5. AI Tool Disclosure (fill in for your report)

Document here which AI tool(s) you used, and for which parts — e.g. "Used Claude to scaffold the initial PHP/MySQL structure, the shopping cart session logic, and the simulated Mobile Money checkout flow; reviewed and adjusted the delivery fee logic and product data myself." Be ready to explain any part of the code in your presentation.

## 6. Project Structure

```
nicfresh/
├── admin/              # Admin panel (login, dashboard, product & order management)
├── assets/css/js       # Stylesheet and client-side scripts
├── config/db.php       # Database connection
├── database/schema.sql # Full schema + seed data
├── includes/           # Shared header, footer, helper functions
└── *.php               # Public-facing pages (home, shop, cart, checkout, legal, etc.)
```
