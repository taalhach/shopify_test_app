<?php

namespace token;

class Token
{
    private $db;
    public function __construct($db)
    {
        $this->db=$db;
    }

    private function store($shop,$token)
    {
        $data=array('shop_url'=>$shop,'token'=>$token,'is_active'=>true);
        $id=$this->db->insert('shop_tokens',$data);
        return $id;
    }
    private function update($shop,$token){
        $data=array('token'=>$token,'is_active'=>true);
        $this->db->where('shop_url',$shop);
        return $this->db->update('shop_tokens',$data);
    }
    private function token_exist($shop){
        $this->db->where('shop_url',$shop);
        return $this->db->getValue('shop_tokens','count(*)')>0;
    }
    public function store_token($shop,$token){
        if ($this->token_exist($shop)){
           return $this->update($shop,$token);
        }else{
            return $this->store($shop,$token);
        }
    }
}
//require '../mysqli.php';
//$t= new Token(new \MysqliDb('localhost','root','root','test_app'));
//$t->store_token('custom','updated no no again');
////$t->storeUnique('custom','non-custom');