<?php
require 'MysqliDb.php';

define('DB_SERVER', 'localhost');
//define('DB_USERNAME', 'shopifytest');
//define('DB_PASSWORD', 'Shop@123');
//define('DB_NAME', 'shopifytest');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'test_app');

// Create connection
$db = new MysqliDb(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
