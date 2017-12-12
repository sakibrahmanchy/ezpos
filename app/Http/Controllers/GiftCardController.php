<?php

namespace App\Http\Controllers;

use App\Enumaration\GiftCardStatus;
use App\Model\Customer;
use App\Model\GiftCard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftCardController extends Controller
{

    public function GetGiftCardForm()
    {

        $customers = Customer::all();

        return view('gift_cards.new_gift_card', ["customers" => $customers]);
    }

    public function AddGiftCard(Request $request)
    {
        $this->validate($request,[
            "gift_card_number"=>"required|unique:gift_cards",
            "value"=>"required|numeric",
        ]);

        GiftCard::create($request->except('_token'));

        return redirect()->route('gift_card_list');

    }

    public function GetGiftCardList()
    {
        $gift_cards = GiftCard::with('customer')->get();

        return view('gift_cards.gift_card_list', ["gift_cards" => $gift_cards]);
    }

    public function EditGiftCardGet($gift_card_id)
    {

        $giftCardInfo = GiftCard::where('id',$gift_card_id)->first();

        $customers = Customer::all();

        return view('gift_cards.gift_card_edit', ['gift_card' => $giftCardInfo,"customers"=>$customers]);
    }


    public function  EditGiftCardPost(Request $request, $gift_card_id)
    {
        $this->validate($request,[
            "gift_card_number"=>"required|unique:gift_cards,gift_card_number,".$gift_card_id,
            "value"=>"required|numeric",
        ]);

        $giftCard = GiftCard::where("id", "=", $gift_card_id)->first();

        $giftCard->update($request->except('_token'));

        if($request->status==null)
           $giftCard->update([
              "status"=>GiftCardStatus::$INACTIVE
           ]);
        else
            $giftCard->update([
                "status"=>GiftCardStatus::$ACTIVE
            ]);

        return redirect()->route('gift_card_list');

    }

    public function DeleteGiftCardGet($gift_card_id){

        $giftCard = GiftCard::where("id",$gift_card_id)->first();

        $giftCard->delete();

        return redirect()->route('gift_card_list');
    }

    public function UseGiftCard(Request $request){

        $gift_card_number = $request->gift_card_number;
        $due = $request->due;

        if(GiftCard::where("gift_card_number",$gift_card_number)->exists()){

            $gift_card = GiftCard::where("gift_card_number",$gift_card_number)->first();

            if($gift_card->status==GiftCardStatus::$ACTIVE){

                if($gift_card->value>0){

                    $previous_value = $gift_card->value;

                    if($due<=$gift_card->value){

                        $gift_card->value -= $due;
                        $gift_card->save();

                        $current_value = $gift_card->value;
                        $value_deducted = $previous_value-$current_value;
                        $due = $due-$value_deducted;

                        return response()->json(["success"=>true,"due"=>$due,
                            "value_deducted"=>$value_deducted,"current_value"=>$current_value]);

                    }else{

                        $gift_card->value -= $previous_value;
                        $gift_card->save();

                        $current_value = $gift_card->value;
                        $value_deducted = $previous_value-$current_value;
                        $due = $due-$value_deducted;

                        return response()->json(["success"=>true,"due"=>$due,
                            "value_deducted"=>$value_deducted,"current_value"=>$current_value]);
                    }

                }
                else
                    return response()->json(["success"=>false,"message"=>"Low balance on gift card."],200);
            }
            else
                return response()->json(["success"=>false,"message"=>"Gift card is not active."],200);
        }else
            return response()->json(["success"=>false,"message"=>"Invalid gift card number."],200);
    }

    public function DeleteGiftCards(Request $request){

        $gift_card_list = $request->id_list;
        if(DB::table('gift_cards')->whereIn('id',$gift_card_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }
}

