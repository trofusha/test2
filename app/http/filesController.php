<?php

namespace app\http;

use XMLReader;

class filesController {
    public $filename;
    public $filename1;
    public $storge_path;
    public $filenames;
    
    public function __construct() {
        //$res можно положить сразу сюда и не таскать как параметр но я пока не хочу
        $this->storge_path=filter_input(INPUT_SERVER, 'DOCUMENT_ROOT').'/storage/';
    }

    public function getFilenames($BD) {
        
        if($this->filenames=$BD->QuerySelect("SELECT * FROM `files` WHERE 1")){
            return true;
        }else{
            return false;
        }
        
    }
    public function checkFilenamesRequest() {
        
        $filenames = filter_input_array(INPUT_GET);
        if(count($filenames['filenames'])!==0){
            $this->filenames=$filenames['filenames'];
            
            return true;
        }else{
            return false;
        }
        
        
    }
    
    public function checkFilenames($BD) {
        foreach ($this->filenames as $filename) {
            
            if (!$BD->QuerySelect("SELECT * FROM `files` WHERE `filename`='$filename'")) {
                return false;
            }
        }
        return true;
    }

    public function insertFilename($BD) {
        
        $BD->Query("INSERT INTO `files` (`Id`, `filename`, `filedate`) VALUES (NULL, '".$this->filename1."', CURRENT_TIMESTAMP)");
    }
    
    
    
    public function checkFileRequest($res) {
        if (!isset($res['file'])) {
            return false;
        } else {
            return true;
        }
    }
    public function saveFile($res) {
        $this->filename1=uniqid().'.xml';
        $this->filename=$this->storge_path.$this->filename1;
         
        if(!file_exists($this->filename)){
           
            if(move_uploaded_file($res['file']['tmp_name'], $this->filename) === true){
         
                return true;
               
            }else{

                return false;
                 
            }
               
        } else {
            $this->saveFile($res);
        }
    }

    public function XML2JSONFile() {
        //поскольку задача читать быстро не стоит юзаем другую библиотеку отличную от checkFileStructure        
        return simplexml_load_file($this->filename);
        
    }

    public function checkFileStructure($res) {
        //тут можно было бы сделать валидацию через схему или просто проверить вхождение паттерна но я почему решил распарсить файл
        $reader = new XMLReader();
        $reader->open($res['file']['tmp_name']);
        $i = 0;
        while ($reader->read()) {
            //проверяем елемент на соответствие компоненту
            if ($reader->nodeType === XMLReader::ELEMENT && strcasecmp($reader->name, 'Component') === 0 && $reader->depth !== 0) {
                //проверяем соответствие атрибута ID
                if ($reader->hasAttributes) {

                    while ($reader->moveToNextAttribute()) {
                        $i = 0;
                        if (strcasecmp($reader->name, 'ID') === 0 && $reader->value === '030-032-000-000') {

                            while ($reader->read()) {
                                //проверяем наличие и содержание узлов на соответствие паттерну
                                if ($reader->nodeType === XMLReader::ELEMENT && $reader->nodeType !== XMLReader::END_ELEMENT &&
                                        (strcasecmp($reader->name, 'Value') === 0 || strcasecmp($reader->name, 'Limit') === 0 || strcasecmp($reader->name, 'Error') === 0)) {
                                    $name = $reader->name;
                                    $reader->read();

                                    if ($reader->nodeType === XMLReader::TEXT || $reader->nodeType === XMLReader::END_ELEMENT) {
                                        $value = $reader->value;
                                        switch (strtolower($name)) {
                                            //увеличиваем счетчик совпадений
                                            case 'value':
                                                if (strlen(trim($value)) === 0) {
                                                    $i++;
                                                }
                                                break;
                                            case 'limit':
                                                if (strlen(trim($value)) === 0) {
                                                    $i++;
                                                }
                                                break;
                                            case 'error':
                                                if (strtolower(trim($value)) === 'error') {
                                                    $i++;
                                                }
                                                break;
                                        }
                                    }
                                } else if ($reader->nodeType == XMLReader::END_ELEMENT && strcasecmp($reader->name, 'Component') === 0) {

                                    break;
                                }
                            }
                            //файл фалиден если, в остальных случаях не валиден
                            if ($i === 3) {
                                return true;
                            }
                            break;
                        }
                    }
                }
            }
        }
        $reader->close();
        return false;
    }

}
