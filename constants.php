<?php
require_once 'db_handlers/db.php';
require_once 'ShopifyClient.php';

//define('ENVIRONMENT_URL','http://shopifytest.iserver.purelogics.net/test_app/');
define('ENVIRONMENT_URL','http://localhost/shopify/test_app/');
define('API_KEY','bd2e0dd0801b0b7f726a1525188d32d8');
define('API_SECRET','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');
define('TABLE_SHOP','shop_tokens');
define('TABLE_SHOP_PRODUCTS','shop_products');
define('TABLE_SHOP_ORDERS','orders');
define('TAG','PURELOGICS');