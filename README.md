# ClinicDesk Final Project

ClinicDesk is a private clinic management dashboard built with PHP and MySQL.

## Requirements
- XAMPP
- Apache
- MySQL
- PHP
- phpMyAdmin

## Setup Steps

1. Copy the project folder to:

   C:\xampp\htdocs\clinicdesk

2. Start Apache and MySQL from XAMPP Control Panel.

3. Open phpMyAdmin:

   http://localhost/phpmyadmin

4. Create a database named:

   clinicdesk_db

5. Import the database file:

   clinicdesk_db.sql

6. Open the project:

   http://localhost/clinicdesk

## Login Accounts

### Admin
Email: admin@clinic.local  
Password: Admin@1234

### Doctor
Email: doctor@clinic.local  
Password: Doctor@1234

### Patient
Email: patient@clinic.local  
Password: Patient@1234

## Main Features
- Login system
- Session-based authentication
- Role-based access control
- Admin dashboard
- User management
- Doctor management
- Specialization management
- Appointment booking
- Appointment status updates
- Prescriptions
- Secure prescription file access
- Reports
- CSV export

## Project Structure
- config
- core
- models
- controllers
- views
- public