<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    @include_once filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/'.$fileName;

}
spl_autoload_register('autoload');

$data='  
    {
    "host": "localhost",
    "user": "trofusha-u",
    "pass": "Ashmg0qmqUkIxz0j!",
    "BDName": "test2",
    "tableName": "files",
    "charset": "utf8mb4",
    "fields": ["Id", "filename", "filedate"],
    "fieldsType": ["INT NOT NULL AUTO_INCREMENT", "VARCHAR(32) NOT NULL", "DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP"],
    "PKey": "Id",
    "BDType": "Database"
    }
    ';

$BDData= json_decode($data);
//print_r($BDData);
$BD=New app\Database($BDData);

if(!$BD->ConnectBD()){
    die('нет подключения');
}
else if(!$BD->TableIsset()){
    $BD->CreateTable();
}

//echo 'ok';