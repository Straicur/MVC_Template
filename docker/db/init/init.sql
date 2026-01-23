CREATE DATABASE IF NOT EXISTS mydatabase_test;

GRANT ALL PRIVILEGES ON mydatabase_test.* TO 'user'@'%';

FLUSH PRIVILEGES;
