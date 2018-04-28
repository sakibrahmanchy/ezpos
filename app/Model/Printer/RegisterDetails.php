<?php
namespace App\Model\Printer;

class RegisterDetails {
    private $date;
    private $employee;
    private $amount;
    private $time;

    public function __construct($employee = '', $date = '',
                                $amount = '', $time = '') {
        $this -> date    = $employee;
        $this -> employee        = $date;
        $this -> amount  = $amount;
        $this -> time = $time;

    }

    public function __toString() {
        //total = 42
        $employee_cols = 14;
        $date_cols = 14;
        $amount_cols = 14;
        $time_cols = 14;

        $employee_text = str_pad($this -> date, $date_cols) ;
        $date_text = str_pad($this -> employee, $employee_cols) ;
        $amount_text = str_pad($this -> amount, $amount_cols, ' ', STR_PAD_LEFT);
        $time_text = str_pad($this -> time, $time_cols) ;
        return "$employee_text$date_text$amount_text\n$time_text";
    }
}
?>