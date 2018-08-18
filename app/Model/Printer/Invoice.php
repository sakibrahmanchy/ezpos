<?php
namespace App\Model\Printer;

class Invoice {
    private $date;
    private $sale_id;
    private $amount;

    public function __construct($sale_id = '', $date = '',
        $amount = '') {
        $this -> sale_id    = $sale_id;
        $this -> date        = $date;
        $this -> amount  = $amount;

    }

    public function __toString() {
        //total = 42
        $sale_id_cols = 10;
        $date_cols = 20;
        $amount_cols = 12;

        $sale_id_text = str_pad($this -> sale_id, $sale_id_cols) ;
        $date_text = str_pad($this -> date, $date_cols) ;
        $amount_text = str_pad($this -> amount, $amount_cols, ' ', STR_PAD_LEFT);
        return "$sale_id_text$date_text$amount_text\n";
    }
}
?>