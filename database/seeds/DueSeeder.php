<?php

use Illuminate\Database\Seeder;

class DueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $sales = \App\Model\Sale::all();
        //Inserting old data
        foreach($sales as $aSale) {
            if($aSale->due < 0) {
                if(!\App\Model\Sale::checkIfDueAlreadyExistForSale($aSale->id)){
                    \App\Model\Sale::insertDuePaymentInPaymentLog($aSale);
                }

            }
        }

        //Removing duplicate payment logs
        $listOfDuplicateSaleId = \App\Model\Sale::getListSalesFromPaymentLogWithMultipleDue();
        foreach($listOfDuplicateSaleId as $aSale) {
            $count = \App\Model\PaymentLog::where("sale_id",$aSale->sale_id)
                ->where("payment_type",\App\Enumaration\PaymentTypes::$TypeList["Due"])->count();
            $skip = 1;
            $limit = $count - $skip;
            \App\Model\PaymentLog::where("sale_id",$aSale->sale_id)
                    ->where("payment_type",\App\Enumaration\PaymentTypes::$TypeList["Due"])
                    ->orderBy('created_at','desc')->skip($skip)->take($limit)->delete();
        }
    }
}
