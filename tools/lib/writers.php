<?php

class Writer {
    public function writeHeaders($f){
    }
    public function write($object, $f){
    }
}

class JSONWriter extends Writer{
    public function write($object, $f){
        fputs($f, json_encode($object));
        fputs($f, "\n");
    }
}

class CSVWriter extends Writer{
    public function __construct($mapping){
        $this->mapping = $mapping;
    }

    public function writeHeaders($f){
        $row = array_values($this->mapping);
        fputcsv($f, $row);
    }

    public function write($object, $f){
        $row = array();

        foreach($this->mapping as $from_key=>$to_key){
            if (is_numeric($from_key)) $from_key = $to_key;

            if (strpos($from_key, '.')===false) {
                $value = @$object->$from_key;
            } else {
                list($key1, $key2) = explode(".", $from_key);
                $value = null;

                if (isset($object->$key1)){
                    $value = @$object->$key1->$key2;
                }
            }

            if ($value===true) $value = 'Y';
            if ($value===false) $value = 'N';
            if ($value===null) $value = '';

            $row[] = $value;
        }

        fputcsv($f, $row);
    }
}