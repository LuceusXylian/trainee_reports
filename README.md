# trainee_reports

In Germany a trainee must write weekly a report. These reports are useless, but must still be written. 
No one is checking them carefully, so we can abuse this with making a generator.

## How to get started
1. sudo apt install apache2 php mariadb
2. cd /var/www/html
3. git clone https://github.com/XylianZeref/trainee_reports.git
4. cd trainee_reports
5. sudo mysql -h localhost -u user < create_Database.sql
6. add secrets.php  
  $server = "localhost";  
  $user = "user";  
  $password = "pass";  
  $database = "trainee_reports";  
  
7. configure config.php
8. go to /trainee_reports/editor.php
9. go to /trainee_reports/generator.php
