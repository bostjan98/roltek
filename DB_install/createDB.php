<?php
require_once(dirname(__FILE__,2) . "/Models/IngeniousImportOrderQueue.php");
require_once(dirname(__FILE__,2) . "/Models/IngeniousImportOrderQueueBcpk.php");
$import = new IngeniousImportOrderQueue();



/*for( $i=0;$i<400;$i++){
    $a = "INSERT INTO `tsys_ingeniousimportorderqueue` SET acOID = '".generateRandomOIDString()."', acDokumentenNr='".generateRandomString()."', ";
    $a .="anIdStatus= '".generateRandomStatus()."', anPriority= '".generateRandomtenNumber()."',adDateInserted= '".generateRandomDateTime()."',";
    $a .="adDateUpdated= '".generateRandomDateTime()."', anIdTaskType='1';";

    echo $a.'<br>';
} 


foreach($import->getAllData() as $key => $value){
    if ((strtotime($value['adDateInserted'])) > (strtotime($value['adDateUpdated']))){
        $date = strtotime($value['adDateInserted']);
        $date = strtotime("+7 day", $date);
        $updateTime = date('Y-m-d H:i:s', $date);

        $import->updateTime($value['anId'], 'adDateUpdated', $updateTime);
    }  
}

$importBckp = new IngeniousImportOrderQueueBcpk();

foreach($import->getAllData() as $key => $value){
    $errorText = (($value['anIdStatus'] == 3)?'Error by system':(($value['anIdStatus'] == 5)?'Error by migration':null));
    $d = [
        'anId' => $value['anId'],
        'anIDQueue' => generateAnId(),
        'acOID' => $value['acOID'],
        'acDokumentenNr' => $value['acDokumentenNr'],
        'anIdStatus' => $value['anIdStatus'],
        'anPriority' => $value['anPriority'],
        'acError' => $errorText,
        'adDateInserted' => $value['adDateInserted'],
        'adDateUpdated' => $value['adDateUpdated']
    ];
    var_dump($d);
    $importBckp->insertData($d);
}
*/



function generateRandomString() {
    // Generate two random uppercase letters
    $letters = chr(rand(65, 90)) . chr(rand(65, 90));

    // Generate four random numbers
    $numbers = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    
    $numbers_last = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

    // Combine the parts to create the final string
    $result = $letters . '-' . $numbers . '-' . $numbers_last;

    return $result;
}


function generateAnId()
{
    return str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
}


function generateRandomDateTime() {
    $startDate = strtotime('2023-07-01');
    $endDate = strtotime('2023-11-30');

    // Generate a random timestamp between start and end dates
    $randomTimestamp = rand($startDate, $endDate);

    // Format the timestamp as a datetime string
    $randomDateTime = date('Y-m-d H:i:s', $randomTimestamp);

    return $randomDateTime;
}

function generateRandomOIDString() {
    $length = rand(24, 27);

    // Generate the first character to ensure it is not 0
    $firstChar = rand(1, 9);

    // Generate numbers for the rest of the characters but the last one
    $numbers = $firstChar;
    for ($i = 2; $i < $length; $i++) {
        $numbers .= rand(0, 9);
    }

    // Generate a random uppercase letter for the last character
    $lastChar = chr(rand(65, 90));

    // Combine the parts to create the final string
    $result = $numbers . $lastChar;

    return $result;
}

function generateRandomtenNumber() {
    $options = [10, 20, 30, 40, 50, 60, 70, 80, 90];
    $randomIndex = array_rand($options);
    return $options[$randomIndex];
}


function generateRandomStatus() {
    $originalNumber = rand(1, 5);
    $adjustedNumber = adjustDistribution($originalNumber);

    return $adjustedNumber;
}

function adjustDistribution($number) {
    // Define the weights for each option
    $weights = [
        1 => 30,  // 30% weight
        2 => 25,  // 25% weight
        3 => 10,   // 10% weight
        4 => 25,  // 25% weight
        5 => 10,   // 10% weight
    ];

    // Validate the input number
    if ($number < 1 || $number > 5) {
        return $number; // Return the original number if it's not in the expected range
    }

    // Convert the original number to the desired distribution
    $randomNumber = rand(1, 100);
    $cumulativeWeight = 0;

    foreach ($weights as $option => $weight) {
        $cumulativeWeight += $weight;

        if ($randomNumber <= $cumulativeWeight) {
            return $option;
        }
    }

    // Fallback to the original number if something goes wrong
    return $number;
}