<?php
require_once 'constants.php';

if (isset($_REQUEST['shop'])&& $_REQUEST['shop']!=''){
    $db->where('shop_url',$_REQUEST['shop']);
    $db->update('shop_tokens',array('is_active'=>0));
}
