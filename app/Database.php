<?php

namespace app;
use mysqli;
class Database  {
    
    public $connection;
    public $BDData;
    public function __Construct($BDData) {
        $this->BDData=$BDData;
       
       
    }
   
    public function ConnectBD(): bool {
        
        if($obj= new mysqli($this->BDData->host, $this->BDData->user,$this->BDData->pass, $this->BDData->BDName)){
            $obj->set_charset($this->BDData->charset);
            
            $this->connection=$obj;
            return true;
        }
        return false;
        
    }
    public function TableIsset(): bool {
        if ($result = $this->connection->query("SHOW TABLES LIKE '" . $this->BDData->tableName . "';")) {

            if ($result->num_rows !== 1) {
                return true;
            }
        }
        return false;
    }

    public function CreateTable(): bool {


        $fields = array_combine($this->BDData->fields, $this->BDData->fieldsType);
        $query = "CREATE TABLE `" . $this->BDData->BDName . "`.`" . $this->BDData->tableName . "` (";
        foreach ($fields as $name => $type) {
            $query .= " `$name` $type ,";
        }
        $query .= " PRIMARY KEY (`" . $this->BDData->PKey . "`));";
       
        $query .= " PRIMARY KEY (`" . $this->BDData->PKey . "`));";
        if ($result = $this->connection->query($query)) {
            return true;
        }

        return false;
    }


    public function Query($query):bool {
 
        if ($result = $this->connection->query($query)){    
            return true;
        }

        return false;
    }


    public function QuerySelect($query) {
        $arr = false;
        if ($result = $this->connection->query($query)){   
            while ($res= $result->fetch_assoc()){
                $arr[]=$res;
            }
            
            return $arr;
        }

        return false;
    }

}
