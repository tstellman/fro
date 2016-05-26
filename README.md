# GSR_Share
This is the Grocery Store Recovery repo for sharing the application code with other food banks. This is not the production repo.

# Harvesters Grocery Store Recovery Application
## Version 1.0 - For Distribution to other food banks

Designed for agencies to submit grocery store recovery pickups to the food bank.

To setup the application in your environment:
- Step 1: Download the application code.
- Step 2: Create gsr_dist MySQL database.
- Step 3: Import gsr_dist.sql file to create database structure and insert data.
- Step 4: Update dbc.php config file to match your system credentials

The following users should be created when you import the sql file:
- Admin
  - Username: admin
  - Password: grocery
- Agency
  - Username: F0000
  - Password: grocery
