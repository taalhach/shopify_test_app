<?php
require_once 'db_handlers/db.php';
require_once 'ShopifyClient.php';

//define('ENVIRONMENT_URL','http://shopifytest.iserver.purelogics.net/test_app/');
define('ENVIRONMENT_URL','http://localhost/shopify/test_app/');
define('API_KEY','you_api_key');
define('API_SECRET','you_api_secret');
define('TABLE_SHOP','shop_tokens');
define('TABLE_SHOP_PRODUCTS','shop_products');
define('TABLE_SHOP_ORDERS','orders');
define('TAG','PURELOGICS');