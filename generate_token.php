<?php

require_once 'request/post.php';
require_once 'db_handlers/token/token.php';
require_once 'constants.php';

$hmac=$_GET['hmac'];

$params=array_diff_key($_GET,array('hmac'=>''));
ksort($params);

$computed_hmac=hash_hmac('sha256',http_build_query($params),API_SECRET);

if(hash_equals($hmac,$computed_hmac)){
    $base_uri="https://" . $params['shop'];
    $options='?code='.$params['code'].'&client_secret='.API_SECRET.'&client_id='.API_KEY;
    $response=make_post_request($base_uri,'/admin/oauth/access_token'.$options);
    $token= new \token\Token($db);
    if($token->store_token($params['shop'],$response['access_token'])){
        echo 'Token is stored in database';
    }else{
        echo 'internal server error';
        http_response_code(500);
    }

    $headers=array(
        "X-Shopify-Access-Token: " . $response['access_token'],
        "Accept: application/json",
        "Content-Type: application/json"
    );

    $url='https://'.API_KEY.':'.API_SECRET.'@'. $params['shop'];


    $payload=[
        'webhook'=>
            [
                'topic'=> 'app/uninstalled',
                'address'=>'https://shopifytest.iserver.purelogics.net/test_app/uninstall.php?shop='.$params['shop'],
                'format'=>'json'
            ]
    ];
    $resp=curlHttpApiRequest('POST',$url.'/admin/api/2020-04/webhooks.json','',$payload,$headers);
    $resp=json_decode($resp,true);
    if (isset($resp['errors'])){
        echo '<br> app/uninstall webhook is not subscribed error: '.var_dump($resp['errors']).'<br>';
        var_dump($resp);
        die();
    }
    echo '<br> app/uninstall webhook subscribed <br>';

    $payload=array(
        'application_charge'=>array(
            'name'=>'Spartan charges',
            'price'=>100.0,
            "test"=>true,
            "return_url"=> ENVIRONMENT_URL."free_trial.php",
        ));
    $resp=curlHttpApiRequest('POST',$url.'/admin/api/2020-04/application_charges.json','',$payload,$headers);
    $resp=json_decode($resp,true);
    if (isset($resp['errors'])){
        echo '<br> application did not go through time charges, error: '.var_dump($resp['errors']).'<br>';
        die();
    }

    echo $resp['application_charge']['confirmation_url'];
    var_dump($resp);
    header('Location: '.$resp['application_charge']['confirmation_url']);
}else{
    echo 'invalid hmac';
}