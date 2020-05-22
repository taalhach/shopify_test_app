<?php
require_once 'constants.php';
require_once 'db_handlers/db.php';
require_once 'ShopifyClient.php';
$db->where('is_active',1);
$shops=$db->get('shop_tokens');
$count=0;
if (count($shops)>0){
    foreach ($shops as $shop){
        $sc=new ShopifyClient($shop['shop_url'],$shop['token'],API_KEY,API_SECRET);
        $loop=true;
        do{
            try {
                if ($next_page=''){
                    $orders=$sc->call('GET','/admin/api/2020-04/orders.json?limit=250');
                }else{
                    $orders=$sc->call('GET','/admin/api/2020-04/orders.json?limit=250&page_info='.$next_page);
                }
                $headers=$sc->getHeaders();
                if(isset($headers['link']) && $headers['link'] != ''){

                    $pages_info = $sc->getPageInfo($headers['link']);

                    if(isset($pages_info)){
                        $next_page = $pages_info['next_page'];
                        $prev_page= $pages_info['prev_page'];
                    }else{
                        $next_page = '';
                        $prev_page = '';
                    }

                }else{
                    $loop = false;
                }
                if($next_page == ''){
                    $loop = false;
                }
                if (count($orders)>0){
                    foreach ($orders as $order){
                        $db->where('order_id',$order['id']);
                        $orderDb=$db->get(TABLE_SHOP_ORDERS);
                        if (count($orderDb)==0){
//                        if (0==0){

                            $created_at 			= '';
                            $created_at_original	= '';
                            $updated_at				= '';
                            $updated_at_original	= '';
                            $processed_at 			= '';
                            $processed_at_original	= '';
                            $cancelled_at 			= '';
                            $cancelled_at_original	= '';


                            if($order['created_at'] != '')
                            {
                                $created_at = date('Y-m-d H:i:s',strtotime($order['created_at']));
                                $created_at_original = $order['created_at'];
                            }

                            if($order['updated_at'] != '')
                            {
                                $updated_at = date('Y-m-d H:i:s',strtotime($order['updated_at']));
                                $updated_at_original = $order['updated_at'];
                            }

                            if($order['processed_at'] != '')
                            {
                                $processed_at = date('Y-m-d H:i:s',strtotime($order['processed_at']));
                                $processed_at_original = $order['processed_at'];
                            }

                            if($order['cancelled_at'] != '')
                            {
                                $cancelled_at = date('Y-m-d H:i:s',strtotime($order['cancelled_at']));
                                $cancelled_at_original = $order['cancelled_at'];
                            }

                            $newOrder = array(
                                'shop_id' 				=> $shop['shop_url'],
                                'order_id'				=> (isset($order['id'])?$order['id']:0),
                                'created_at'			=> $created_at,
                                'created_at_original' 	=> $created_at_original,
                                'updated_at'			=> $updated_at,
                                'updated_at_original' 	=> $updated_at_original,
                                'tag'                   =>TAG,
                                'number'				=> (isset($order['number'])?$order['number']:''),
                                'note'					=> (isset($order['note'])?$order['note']:''),
                                'token'					=> (isset($order['token'])?$order['token']:''),
                                'gateway'				=> (isset($order['gateway'])?$order['gateway']:''),
                                'total_price' 			=> (isset($order['total_price'])?$order['total_price']:''),
                                'subtotal_price'		=> (isset($order['subtotal_price'])?$order['subtotal_price']:0),
                                'total_weight'			=> (isset($order['total_weight'])?$order['total_weight']:0),
                                'total_tax'				=> (isset($order['total_tax'])?$order['total_tax']:0),
                                'currency'				=> (isset($order['currency'])?$order['currency']:''),
                                'financial_status'		=> (isset($order['financial_status'])?$order['financial_status']:''),
                                'confirmed'				=> (isset($order['confirmed'])?$order['confirmed']:0),
                                'total_discounts'		=> (isset($order['total_discounts'])?$order['total_discounts']:0),
                                'total_line_items_price'=> (isset($order['total_line_items_price'])?$order['total_line_items_price']:0),
                                'cart_token'			=> (isset($order['cart_token'])?$order['cart_token']:''),

                                'buyer_accepts_marketing'	=> (isset($order['buyer_accepts_marketing'])?$order['buyer_accepts_marketing']:0),
                                'name'						=> (isset($order['name'])?$order['name']:''),
                                'referring_site'			=> (isset($order['referring_site'])?$order['referring_site']:''),
                                'landing_site'				=> (isset($order['landing_site'])?$order['landing_site']:''),
                                'cancelled_at'				=> $cancelled_at,
                                'cancelled_at_original'		=> $cancelled_at_original,
                                'cancel_reason'				=> (isset($order['cancel_reason'])?$order['cancel_reason']:''),
                                'total_price_usd'			=> (isset($order['total_price_usd'])?$order['total_price_usd']:0),
                                'processed_at'				=> $processed_at,
                                'processed_at_original'		=> $processed_at_original,
                                'order_number'				=> (isset($order['order_number'])?$order['order_number']:''),
                                'processing_method'			=> (isset($order['processing_method'])?$order['processing_method']:''),
                                'checkout_id'				=> (isset($order['checkout_id'])?$order['checkout_id']:''),
                                'source_name'				=> (isset($order['source_name'])?$order['source_name']:''),
                                'contact_email'				=> (isset($order['contact_email'])?$order['contact_email']:''),
                                'payment_gateway_names'		=> (isset($order['payment_gateway_names'])?json_encode($order['payment_gateway_names']):json_encode(array())),
                                'tax_lines'					=> (isset($order['tax_lines'])?json_encode($order['tax_lines']):json_encode(array())),
                                'line_items'				=> (isset($order['line_items'])?json_encode($order['line_items']):json_encode(array())),
                                'shipping_lines'			=> (isset($order['shipping_lines'])?json_encode($order['shipping_lines']):json_encode(array())),
                                'billing_address'			=> (isset($order['billing_address'])?json_encode($order['billing_address']):json_encode(array())),
                                'shipping_address'			=> (isset($order['shipping_address'])?json_encode($order['shipping_address']):json_encode(array())),
                                'customer'					=> (isset($order['customer'])?json_encode($order['customer']):json_encode(array()))
                            );
//                            print_r($newOrder);
                            $db->insert(TABLE_SHOP_ORDERS,$newOrder);
                            $err=$db->getLastError();
                            if (isset($err)&&$err!=''){
                                echo "OOPS!!! an error with database: $err ";
                            }else{
                                $count++;
                            }
                        }
                    }
                }
            }catch (Exception $e){
                $loop=false;
            }

        }while($loop);
    }
}
echo "$count orders are stored in database";
