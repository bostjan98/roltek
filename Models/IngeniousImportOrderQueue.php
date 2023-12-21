<?php
require_once(dirname(__FILE__,2) . "../Db.php");

class IngeniousImportOrderQueue
{
    private $db;

    function __construct()
    {
        $this->db = new DB();
    }


    public function getAllData()
    {
        $query = "SELECT * from `tsys_ingeniousimportorderqueue` ORDER BY anId ASC";

        return $this->db->runBaseQuery($query);
    }

    public function getDataByParameter(){
        $query = "SELECT anId from `tsys_ingeniousimportorderqueue`WHERE adDateUpdated<adDateInserted ORDER BY anId ASC";

        return $this->db->runBaseQuery($query);
    }

    public function getDataBySpecificParameter($id, $parameter){
        $query = "SELECT ".$parameter." from `tsys_ingeniousimportorderqueue` WHERE anId = '".$id."'";
    

        return $this->db->runBaseQuery($query);
    }
    public function updateTime($id, $parameter, $data){
        $query = "Update `tsys_ingeniousimportorderqueue` SET ".$parameter." = ? WHERE anId = ?";
        $paramType='ss';
        $paramValue = [
            $data,
            $id
        ];
        return $this->db->update($query, $paramType, $paramValue);
    }
}


?>
