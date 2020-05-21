<?php
require_once 'db_handlers/db.php';
$db->update('shop_tokens',array('is_active'=>0));
