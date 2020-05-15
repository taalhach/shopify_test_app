<?php

namespace token;

class Token
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn=$conn;
    }

    public function store($shop,$token)
    {
        $query ="insert into shop_tokens (token,shop_url) values (?,?);";
        if($this->token_exists($shop,$token)){
            $query ="update shop_tokens set token=? where shop_url=? ;";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $token,$shop);
        if (!$stmt->execute()){
            return false;
        }
        $stmt->close();

        return true;
    }
    private function token_exists($shop,$token){
        $stmt = $this->conn->prepare("select * from  shop_tokens where shop_url =? and  token=?");
        $stmt->bind_param("ss", $shop,$token);
        $status=false;
        if (!$stmt->execute()){
            $status=false;
        }else{
            $result=$stmt->get_result();
            if ($result->num_rows>0){
                $status=true;
            }
        }
        $stmt->close();
        return $status;
    }

    public function close()
    {
        $this->conn->close();
    }
}
