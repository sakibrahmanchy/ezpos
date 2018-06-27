<?php
namespace App\Model\Printer;

class FooterItem {
    private $name;
    private $value;

    public function __construct($name = '', $value = '') {
        $this -> name    = $name;
        $this -> value   = $value;
    }

    public function __toString() {
        //total = 42
        $name_cols = 20;
        $value_cols = 22;

        $name_text = str_pad($this -> name, $name_cols) ;
        $value_text = str_pad($this -> value, $value_cols, ' ', STR_PAD_LEFT);

        return "$name_text$value_text\n";
    }
}
?>