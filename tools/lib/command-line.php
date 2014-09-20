<?php

function parse_arguments($defaults, $required=array()){
    global $argv;

    $arguments = $defaults;

    foreach($argv as $arg){
        if (substr($arg,0,2)=='--'){
            $raw = substr($arg,2);
            $key = strtok($raw, '=');
            $value = strtok('=');
            $arguments[$key] = $value;
        }
    }

    foreach($required as $arg){
        if (!array_key_exists($arg, $arguments)) return FALSE;
    }

    return $arguments;
}