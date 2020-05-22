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
                    $customers=$sc->call('GET','/admin/api/2020-04/customers.json?limit=250');
                }else{
                    $customers=$sc->call('GET','/admin/api/2020-04/customers.json?limit=250&page_info='.$next_page);
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
                if (count($customers)>0){
                    foreach ($customers as $customer){
                        $sc->call('PUT','/admin/api/2020-04/customers/'.$customer['id'].'.json',array('customer'=>array('id'=>$customer['id'],'tags'=>TAG)));
                        $count++;
                    }
                }
            }catch (Exception $e){
                $loop=false;
            }

        }while($loop);
    }
}
echo "$count customers are tagged";
