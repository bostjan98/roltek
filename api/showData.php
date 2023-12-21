<?php
require_once(dirname(__FILE__,2) . "/Models/IngeniousImportOrderQueue.php");
$import = new IngeniousImportOrderQueue();

$data = $import->getAllData();
echo json_encode($data);
?>