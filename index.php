<?php
require 'vendor/autoload.php';
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
error_reporting(E_ALL ^ E_DEPRECATED);

 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
 $spreadsheet = $reader->load("./data-2k24/factures.xlsx");
 $reader = ReaderEntityFactory::createXLSXReader();
 $path = "./data-2k24/factures.xlsx";
 $path2 = "./data-2k24/coords.xlsx"; 

function extractInfoFromString($input) {
    $lines = explode("\n", $input);
    $info = array();
    foreach ($lines as $line) {
        $parts = explode(':', $line, 2);
        if (count($parts) == 2) {
            $label = trim($parts[0]);
            $value = trim($parts[1]);
            $info[$label] = $value;
        }
    }
    return $info;
}

function getLatAndLong($address){
    if($address == "" || $address == "Adresse de facturation"){
        return ;
    }
    sleep(1);
    $key = "AIzaSyArv0zDFWad2xEFtI9p4nVc-fhocwEHioY" ; 
    $address = str_replace(" ", "+", $address);

    $url = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=$key");
    $json = json_decode($url);  

    if (!empty($json->results) && isset($json->results[0]->geometry)) {
    return $json->results[0]->geometry->location;
} else {
    
    return "erreur";
}
}

 $nomClients = [] ; 
 $reader->open($path); 
 foreach ($reader->getSheetIterator() as $sheet) {
     foreach ($sheet->getRowIterator() as $row) { 
        $cells = $row->getCells() ;
        $name = ($cells[4]);
        if($name->getValue() == "" || $name->getValue() == "Nom") {
            continue;
        }
      array_push($nomClients,$name->getValue());
     }
 }
$reader->close(); 
$reader->open($path2);
 foreach ($reader->getSheetIterator() as $sheet) {
     foreach ($sheet->getRowIterator() as $row) { 
        $cells = $row->getCells() ; 
        $nomClient = $cells[1]->getValue(); 
        $numClientInfos = $cells[2]->getValue(); 
        $clientEmail = $cells[3]->getValue(); 
        $clientFullName = $cells[3]->getValue(); 
        $clientAdresseExp = $cells[5]->getValue(); 
        $clientObj = ["nom" => $nomClient , "infosNumero" => extractInfoFromString($numClientInfos) , "Email" =>  $clientEmail , "Nom Complet" => $clientFullName , "addresse" => $clientAdresseExp , "xAndY" => getLatAndLong($clientAdresseExp)] ; 
        if(array_search($nomClient, $nomClients)){
        var_dump($clientObj) ;
                  
 
    }
     }
 } 
?>