-- Create testing database for Laravel tests
CREATE DATABASE IF NOT EXISTS `testing` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges to application user for testing database
GRANT ALL PRIVILEGES ON `testing`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;