<?php
namespace App\Model\Printer;

class Item {
    private $name;
    private $quantity;
    private $unit_price;
    private $total_price;

    public function __construct($quantity = '', $name = '',
                                $unit_price = '', $total_price = '') {
        $this -> quantity    = $quantity;
        $this -> name        = $name;
        $this -> unit_price  = $unit_price;
        $this -> total_price = $total_price;
    }

    public function __toString() {
        //total = 42
        $quantity_cols = 4;
        $name_cols = 18;
        $unit_price_cols = 10;
        $total_price_cols = 10;

        $name = wordwrap($this->name . "\n",18,"\n",false);
        $name_array = explode("\n",$name);


        $quantity_text = str_pad($this -> quantity, $quantity_cols) ;
        $name_text = str_pad($name_array[0], $name_cols) ;
        $unit_price_text = str_pad($this -> unit_price, $unit_price_cols, ' ', STR_PAD_LEFT);
        $total_price_text = str_pad($this -> total_price,
            $total_price_cols, ' ', STR_PAD_LEFT);

        $textToReturn="$quantity_text$name_text$unit_price_text$total_price_text\n";


        for($i =1;$i<sizeof($name_array);$i++) {
            if($name_array[$i]!=""){
                $textToReturn.=str_pad("    ",4);
                $textToReturn.=str_pad($name_array[$i],10);
                $textToReturn.="\n";
            }
        }
//        dump($textToReturn);
        return $textToReturn;
    }
}
?>