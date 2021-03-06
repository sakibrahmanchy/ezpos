<?php

namespace  App\Model\ImporterWizard;
use App\Library\SettingsSingleton;
use Illuminate\Support\Facades\DB;
use Validator;
use Excel;

class Importer {

    private $values = array();
    private $table_name;
    private $rules = array();
    private $uploadSuccess;
    private $uploadFailed;
    private $validationErrors = array();
    private $defaultValues;
    private $columnMaps;
    private $logfile;

    function __construct($table_name,$columnMaps,$values,$rules, $defaultValues){
        $this->table_name = $table_name;
        $this->columnMaps = $columnMaps;
        $this->rules = $rules;
        $this->values = $values;
        $this->defaultValues = $defaultValues;
        $this->uploadSuccess = 0;
        $this->uploadFailed = 0;
        $this->logfile = "";
    }

    public function replaceWithDefaultValues($aRow){
        foreach ($this->defaultValues as $aKey => $aValue){
            if(is_null($aRow->{$aKey}) || empty($aRow->{$aKey})){
                $aRow->{$aKey} = $aValue;
            }
        }
    }

    public function validateErrors($aRow,$uniqueField){
        $rowData = $aRow->toArray();
        $validator = Validator::make($rowData,$this->rules);

        if($validator->fails()){
            $errorMessages = (string) $validator->errors();
            $errorData = array(
                "field" => $aRow->name,
                $uniqueField => $aRow->{$uniqueField},
                "status" => "Failure",
                "errors" => $errorMessages
            );
            array_push($this->validationErrors,$errorData);
            return false;
        }else{
            $errorData = array(
                "field" => $aRow->name,
                "upc" => $aRow->upc,
                "status" => "Success",
                "erros" => ""
            );
            array_push($this->validationErrors,$errorData);
        }
        return true;
    }

    public function mapColumns($aRow){
        $prefixCode = SettingsSingleton::getByKey("upc_code_prefix");
        $insertObject = array();
        foreach ($this->columnMaps as $aKey => $aValue) {
            $aRow->{$aValue} = $aRow->{$aKey};
            $insertObject[$aValue] = $aRow->{$aKey};
            unset($aRow->{$aKey});
        }
        if(substr($insertObject["isbn"],0,strlen($prefixCode))==$prefixCode) {
           $insertObject["item_quantity"] = 0;
        }
        return $insertObject;
    }

    public function insertIntoDB($uniqueFieldName){
        foreach ($this->values as $aRow) {
            $this->replaceWithDefaultValues($aRow);
            if($this->validateErrors($aRow,$uniqueFieldName)) {
                $aRow = $this->mapColumns($aRow);
                DB::table($this->table_name)->insert($aRow);
                $this->uploadSuccess++;
            }
            else
                $this->uploadFailed++;
        }
    }

    public function getStatusPercentage(){
        $fractionSuccess = ( $this->uploadSuccess)/($this->uploadSuccess + $this->uploadFailed);
        $statusPercentage = $fractionSuccess * 100;
        return  $statusPercentage;
    }

    public function getSucessItems(){
       return $this->uploadSuccess;
    }

    public function getFailureItems(){
        return $this->uploadFailed;
    }

    public function getErrorLogs(){
        return $this->validationErrors;
    }

}