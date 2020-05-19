<?php

require_once 'request/post.php';
require_once 'db_handlers/db.php';
require_once 'db_handlers/token/token.php';

$api_key='bd2e0dd0801b0b7f726a1525188d32d8';
$api_secret='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';

$hmac=$_GET['hmac'];

$params=array_diff_key($_GET,array('hmac'=>''));
ksort($params);

$computed_hmac=hash_hmac('sha256',http_build_query($params),$api_secret);

if(hash_equals($hmac,$computed_hmac)){
    $base_uri="https://" . $params['shop'];
    $options='?code='.$params['code'].'&client_secret='.$api_secret.'&client_id='.$api_key;
    $response=make_post_request($base_uri,'/admin/oauth/access_token'.$options);
    $token= new \token\Token($db);
    if($token->store_token($params['shop'],$response['access_token'])){
        echo 'Token is stored in database';
    }else{
        echo 'internal server error';
        http_response_code(500);
    }
}else{
    echo 'invalid hmac';
}