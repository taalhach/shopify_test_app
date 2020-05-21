<?php
require "vendor/autoload.php";
function make_post_request($base_uri,$url,$options=null){
    $client = new \GuzzleHttp\Client(["base_uri" => $base_uri]);
    $response = $client->post($url, $options);
//    echo $response->getStatusCode();
    return json_decode($response->getBody(),true);
}
function curlHttpApiRequest($method, $url, $query='', $payload='', $request_headers=array())
{
    $url = curlAppendQuery($url, $query);
    $ch = curl_init($url);
    curlSetopts($ch, $method, $payload, $request_headers);
    $response = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($errno) throw new ShopifyCurlException($error, $errno);
    list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
    $last_response_headers = curlParseHeaders($message_headers);
    return $message_body;
}
function curlParseHeaders($message_headers)
{
    $header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
    $headers = array();
    list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
    foreach ($header_lines as $header_line)
    {
        list($name, $value) = explode(':', $header_line, 2);
        $name = strtolower($name);
        $headers[$name] = trim($value);
    }

    return $headers;
}

function curlAppendQuery($url, $query)
{
    if (empty($query)) return $url;
    if (is_array($query)) return "$url?".http_build_query($query);
    else return "$url?$query";
}

function curlSetopts($ch, $method, $payload, $request_headers)
{
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
    if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    if ($method != 'GET' && !empty($payload))
    {

        curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }
}
class ShopifyCurlException extends Exception{
}


//curlHttpApiRequest('POST','https://bd2e0dd0801b0b7f726a1525188d32d8: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@my-personal-first-store.myshopify.com/admin/api/2020-04/webhooks.json',''
//    ,array(
//        'webhook'=>array(
//            'topic'=>'app/uninstalled',
//            'address'=>'https://shopifytest.iserver.purelogics.net/test_app/uninstall.php',
//            'format'=>'json')),
//    array(
//        "X-Shopify-Access-Token: " . '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',
//        "Accept: application/json",
//        "Content-Type: application/json"
//    )
//);
curlHttpApiRequest('POST','https://bd2e0dd0801b0b7f726a1525188d32d8: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@my-personal-first-store.myshopify.com/admin/api/2020-04/webhooks.json',''
    ,array(
        'recurring_application_charge'=>array(
            'name'=>'test_recipient',
            'price'=>100.0,
            "test"=>true,
            "return_url"=> "http://super-duper.shopifyapps.com",
        )),
    array(
        "X-Shopify-Access-Token: " . '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',
        "Accept: application/json",
        "Content-Type: application/json"
    )
);
