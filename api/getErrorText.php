<?php
require_once(dirname(__FILE__,2) . "/Models/IngeniousImportOrderQueueBcpk.php");

$importBckp = new IngeniousImportOrderQueueBcpk();

$id=$_GET['id'];
$data = $importBckp->getDataBySpecificParameter($id, 'acError');

echo json_encode($data[0]);
?>