<?php

class Importer {

    private $keys = array();
    private $table_name;
    private $rules = array();
    private $status;
    private $validationErrors = array();

    function __construct($table_name,$keys,$rules ){
        $this->table_name = $table_name;
        $this->rules = $rules;
        $this->keys = $keys;
        $this->status = 0;
    }

    public function validateErrors($values){
        $this->validationErrors = $this->validate($values,$this->rules);
        return $this->validationErrors;
    }

}