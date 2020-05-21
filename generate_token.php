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
    //TODO add webhook registration here
    $payload=['webhook'=>['topic'=>'app/uninstalled','address'=>'https://shopifytest.iserver.purelogics.net/test_app/uninstall.php','format'=>'json']];
    $headers=array(
            "X-Shopify-Access-Token: " . $response['access_token'],
            "Accept: application/json",
            "Content-Type: application/json"
        );
    $url='https://'.$api_key.':'.$api_secret.'@'. $params['shop'];
    $resp=curlHttpApiRequest('POST',$url.'/admin/api/2020-04/webhooks.json','',$payload,$headers);
    $resp=json_decode($resp,true);
    if (isset($resp['errors'])){
        echo '<br> app/uninstall webhook is not subscribed error: '.var_dump($resp['errors']).'<br>';
        die();
    }
    echo '<br> app/uninstall webhook subscribed <br>';
    $payload=array(
        'recurring_application_charge'=>array(
            'name'=>'test_recipient',
            'price'=>100.0,
            "test"=>true,
            "return_url"=> "http://shopifytest.iserver.purelogics.net/test_app/free_trial.php",
        ));
    $resp=curlHttpApiRequest('POST',$url.'/admin/api/2020-04/recurring_application_charges.json','',$payload,$headers);
    $resp=json_decode($resp,true);
    if (isset($resp['errors'])){
        echo '<br> app/uninstall webhook is not subscribed error: '.var_dump($resp['errors']).'<br>';
        die();
    }
    var_dump($resp);
    echo $resp['recurring_application_charge']['confirmation_url'];
    header('Location: '.$resp['recurring_application_charge']['confirmation_url']);
}else{
    echo 'invalid hmac';
}