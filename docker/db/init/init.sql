CREATE DATABASE app_test;

GRANT ALL PRIVILEGES ON DATABASE app_test TO "user";

\c app_test
GRANT ALL ON SCHEMA public TO "user";

