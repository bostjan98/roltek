<?php
require_once(dirname(__FILE__,2) . "../Db.php");

class IngeniousImportOrderQueueBcpk
{
    private $db;

    function __construct()
    {
        $this->db = new DB();
    }


    public function getAllData()
    {
        $query = "SELECT * from `tsys_ingeniousimportorderqueue_bcpk` ORDER BY anId ASC";

        return $this->db->runBaseQuery($query);
    }

    public function getDataByParameter(){
        $query = "SELECT anId from `tsys_ingeniousimportorderqueue_bcpk`WHERE adDateUpdated<adDateInserted ORDER BY anId ASC";

        return $this->db->runBaseQuery($query);
    }

    public function getDataBySpecificParameter($id, $parameter){
        $query = "SELECT ".$parameter." from `tsys_ingeniousimportorderqueue_bckp` WHERE anId = '".$id."'";
    

        return $this->db->runBaseQuery($query);
    }
    public function updateTime($id, $parameter, $data){
        $query = "Update `tsys_ingeniousimportorderqueue_bcpk` SET ".$parameter." = ? WHERE anId = ?";
        $paramType='ss';
        $paramValue = [
            $data,
            $id
        ];
        return $this->db->update($query, $paramType, $paramValue);
    }

    public function insertData($data)
    {

        $query = "INSERT into `tsys_ingeniousimportorderqueue_bckp` "; 
        $query .= "(anId,anIDQueue, acOID, acDokumentenNr, anIdStatus, anPriority, acError, adDateInserted, adDateUpdated) ";
        $query .= "VALUES (?, ?, ?, ?,?,?, ?, ?,?)";

        $paramType = 'iissiisss';
        $paramValue = [
            $data['anId'],
            $data['anIDQueue'],
            $data['acOID'],
            $data['acDokumentenNr'],
            $data['anIdStatus'],
            $data['anPriority'],
            (!is_null($data['acError']) ? $data['acError']:null),
            $data['adDateInserted'], 
            $data['adDateUpdated']
        ];

        $insert = $this->db->insert($query, $paramType, $paramValue);
    }
}


?>
