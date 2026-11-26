MUNICIPAL VEHICLE RESERVATION AND DISPATCH TRACKING SYSTEM
PHP + MySQL starter project for XAMPP

FEATURES
- Role-based login: Admin, Secretary, Requester, GSO
- Requesters submit vehicle/ambulance reservations and track status
- Admin reviews/submits first-stage review
- Secretary records final approval/decline
- GSO manages vehicles/drivers, assigns a vehicle and driver, records dispatch and return
- Dashboard metrics, request list, vehicle list, user management basics
- Responsive interface inspired by the supplied car-booking UI

REQUIREMENTS
- XAMPP (Apache + MySQL)
- PHP 8+ recommended
- MySQL / MariaDB

INSTALL
1. Extract this folder to: C:\xampp\htdocs\municipal_vehicle_reservation
2. Start Apache and MySQL in XAMPP.
3. Open http://localhost/phpmyadmin
4. Create/import database by importing database.sql.
5. If your MySQL settings differ, edit config.php.
6. Open http://localhost/municipal_vehicle_reservation/
7. Sign in with one of the demo accounts below.

DEMO ACCOUNTS
admin@inabanga.gov.ph       / password
secretary@inabanga.gov.ph   / password
gso@inabanga.gov.ph         / password
requester@example.com       / password

IMPORTANT
- Demo accounts use password "password". Change these before real deployment.
- This is a student-project starter, not a production-secured government system.
- Before live use, configure HTTPS, stronger password policy, CSRF protection, audit retention,
  backup, privacy/security review, and confirm the actual LGU approval workflow.
- The system does not bypass or replace the Mayor's approval authority. The Secretary role
  records the decision in this prototype according to the proposal guide.
