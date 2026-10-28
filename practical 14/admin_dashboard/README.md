# Admin Dashboard (PHP + Bootstrap + MySQL)

## Setup (XAMPP)
1. Copy the `admin_dashboard` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP.
3. Open `http://localhost/phpmyadmin` > Import > choose `schema.sql` > Go.
4. Open `http://localhost/admin_dashboard/login.php`.

DB credentials are in `includes/db.php` (XAMPP default: root / empty password).

## Demo logins (password: `password`)
| Role    | Email             | Can open                                  |
|---------|-------------------|-------------------------------------------|
| admin   | admin@demo.com    | Dashboard, Students, Events, Users, Audit |
| faculty | faculty@demo.com  | Students, Events                          |
| student | student@demo.com  | Events                                    |

## Screenshot checklist
1. **Dashboard UI**: login as admin > `dashboard.php` (stat cards + latest registrations).
2. **Dynamic menu**: login as admin, faculty and student one by one (navbar changes).
3. **403 page**: login as faculty/student > type `http://localhost/admin_dashboard/dashboard.php` in the URL bar.
4. **Audit proof**: login as admin > Audit Logs page, and/or phpMyAdmin > `audit_logs` table.
   Do a few actions first (add/delete an event, failed login, 403 attempt).

## Files
- `schema.sql`: tables + seed data
- `includes/db.php`, `includes/auth.php` (RBAC + audit log), `includes/header.php` (dynamic menu), `includes/footer.php`
- `login.php`, `logout.php`, `dashboard.php`, `students.php`, `events.php`, `users.php`, `audit_logs.php`, `403.php`
