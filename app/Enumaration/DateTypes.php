<?php
/**
 * Created by PhpStorm.
 * User: TechnoTree BD
 * Date: 8/23/2017
 * Time: 12:50 PM
 */

namespace App\Enumaration;


class DateTypes
{

    public $today,$yesterday,$this_week,$last_week,
        $this_month,$last_month,$this_year,$last_year,$all_time;


        function __construct(){

            $today = date('Y-m-d');
            $this->today = $today.'/'.$today;
            $this->yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today))).'/'.date('Y-m-d', strtotime('-1 day', strtotime($today)));
            $this->this_week = date('Y-m-d', strtotime('this week', strtotime($today))).'/'. date('Y-m-d');
            $this->last_week = date('Y-m-d', strtotime('last week', strtotime($today))).'/'.date('Y-m-d', strtotime('last week + 7 days', strtotime($today)));
            $this->this_month = date('Y-m-d', strtotime('-30 days', strtotime($today))).'/'.$today;
            $this->last_month = date('Y-m-d', strtotime('-60 days', strtotime($today))).'/'.date('Y-m-d', strtotime('-30 days', strtotime($today)));
            $this->this_year = date('Y-m-d', strtotime('-365 days', strtotime($today))).'/'.$today;
            $this->last_year = date('Y-m-d', strtotime('-730 days', strtotime($today))).'/'.date('Y-m-d', strtotime('-365 days', strtotime($today)));
            $this->all_time = '1900-01-01/'.$today;

        }

      public function getDates(){
          return $this;
      }


      public function searchDate($date_range){

          $dates = get_object_vars($this);
          $dateType  = "";
          return $dates;

      }


}