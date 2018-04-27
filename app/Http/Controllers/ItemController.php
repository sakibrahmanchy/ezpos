<?php

namespace App\Http\Controllers;

use App\Enumaration\ImportType;
use App\Enumaration\ItemStatus;
use App\Enumaration\PriceRuleTypes;
use App\Library\SettingsSingleton;
use App\Model\Category;
use App\Model\File;
use App\Model\ImportLog;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\ItemsImage;
use App\Model\Manufacturer;
use App\Model\Setting;
use App\Model\Supplier;
use Faker\Provider\zh_CN\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Model\ImporterWizard\Importer;
use PHPExcel_IOFactory;


class ItemController extends Controller
{
    public function GetItemForm()
    {
        //Load all permissions from database
        $categoryList = Category::orderBy('category_name')->get();

        $supplierList = Supplier::all();

        $manufacturerList = Manufacturer::all();

        return view('items.new_item',['categoryList'=>$categoryList,'supplierList'=>$supplierList,'manufacturerList'=>$manufacturerList]);
    }

    public function AddItem(Request $request)
    {

        $this->validate($request, [
            'isbn' => 'unique:items|nullable',
            'item_name' => 'required',
            'item_category' => 'required',
            'item_supplier' => 'required',
            'reorder_level' => 'sometimes|nullable|integer',
            'replenish_level' => 'sometimes|nullable|integer',
            'expire_days' => 'sometimes|nullable|integer',
            'cost_price' => 'required|numeric',
            'unit_price' => 'required|numeric'
        ]);

        $item = new Item();
        $item->InsertItem($request);

        return redirect()->route('item_list');

    }


    public function GetItemList()
    {
        $allItems = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')->leftJoin('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->leftJoin('manufacturers', 'items.manufacturer_id', '=', 'manufacturers.id')
            ->select('items.id as item_id', 'items.*', 'suppliers.*','categories.*','manufacturers.*')
            ->where('product_type',0)
            ->where('product_type','<>',2)
            ->whereNull('items.deleted_at')
            ->get();

        $activeItems = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')->leftJoin('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->leftJoin('manufacturers', 'items.manufacturer_id', '=', 'manufacturers.id')
            ->select('items.id as item_id', 'items.*', 'suppliers.*','categories.*','manufacturers.*')
            ->where('item_status',ItemStatus::$ACTIVE)
            ->where('product_type',0)
            ->where('product_type','<>',2)
            ->whereNull('items.deleted_at')
            ->get();

        $inactiveItems = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')->leftJoin('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->leftJoin('manufacturers', 'items.manufacturer_id', '=', 'manufacturers.id')
            ->select('items.id as item_id', 'items.*', 'suppliers.*','categories.*','manufacturers.*')
            ->where('item_status',ItemStatus::$INACTIVE)
            ->where('product_type',0)
            ->where('product_type','<>',2)
            ->whereNull('items.deleted_at')
            ->get();

        $draftItems = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')->leftJoin('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->leftJoin('manufacturers', 'items.manufacturer_id', '=', 'manufacturers.id')
            ->select('items.id as item_id', 'items.*', 'suppliers.*','categories.*','manufacturers.*')
            ->where('item_status',ItemStatus::$DRAFTED)
            ->where('product_type',0)
            ->where('product_type','<>',2)
            ->whereNull('items.deleted_at')
            ->get();

        return view('items.item_list', ["allItems" => $allItems, "activeItems"=>$activeItems,
                                        "inactiveItems"=>$inactiveItems, "draftItems"=>$draftItems]);
    }

    public function EditItemGet($itemId)
    {
        $categoryList = Category::orderBy('category_name')->get();

        $supplierList = Supplier::all();

        $manufacturerList = Manufacturer::all();

        $itemInfo = DB::table('items')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'items.supplier_id', '=', 'suppliers.id')
            ->leftJoin('manufacturers', 'items.manufacturer_id', '=', 'manufacturers.id')
            ->where('items.id', '=', $itemId)->select('items.id as item_id', 'items.*', 'suppliers.*','categories.*','manufacturers.*')
            ->first();

        $item = new Item();
        $images = $item->getItemImages($itemId);

        return view('items.item_edit', ['item' => $itemInfo, 'categoryList' => $categoryList,'supplierList'=>$supplierList,'manufacturerList'=>$manufacturerList,'images'=>$images]);
    }

    public function DeleteItemImage($item_id,$image_id){

        $item_image = ItemsImage::where('item_id','=',$item_id)->where('file_id','=',$image_id)->first();
        $item_image->delete();

        return redirect()->route('item_edit',['item_id'=>$item_id]);
    }


    public function  EditItemPost(Request $request, $itemId)
    {
        /* var_dump($item);*/
        $this->validate($request, [
            'isbn' => 'unique:items,isbn,'.$itemId.'|nullable',
            'item_name' => 'required',
            'item_category' => 'required',
            'item_supplier' => 'required',
            'reorder_level' => 'sometimes|nullable|integer',
            'replenish_level' => 'sometimes|nullable|integer',
            'expire_days' => 'sometimes|nullable|integer',
            'cost_price' => 'required|numeric',
            'unit_price' => 'required|numeric'
        ]);

        $item = new Item();
        $item->editItem($request,$itemId);
        return redirect()->route('item_list');

    }


    public function GetItemsAutocomplete(){

        $autoselect = Input::get('autoselect');

        if($autoselect=="true"){

            $search_param = (string) Input::get('q');
            if($search_param!=""||$search_param!=null){
                $scan_price_from_barcode = SettingsSingleton::getByKey('scan_price_from_barcode');
                $item_new_price_from_barcode = -1;
                $priceNeededToBeScanned = false;
                if(strlen($search_param)==12) {
                    if($scan_price_from_barcode=="true"){
                        $priceNeededToBeScanned = true;
                        $upc_code_prefix = $scan_price_from_barcode = SettingsSingleton::getByKey('upc_code_prefix');
                        if(substr($search_param,0,strlen($upc_code_prefix)) ===  $upc_code_prefix){
                            $item_new_price_from_barcode = (int) substr($search_param,6,12);
                            $item_new_price_from_barcode = $item_new_price_from_barcode / 1000;
                            $search_param = substr($search_param,0,6);
                        }
                    }
                }

                $items =  DB::table('items')
                    ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
                    ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
                    ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
                    ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
                    ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
                    ->where(function($query) use ($search_param) {
                        $query->where('isbn','like',"%".$search_param."%");
                    })
                    ->where('items.deleted_at',null)
                    ->where('items.item_status',ItemStatus::$ACTIVE)
                    ->select('items.id as item_id','items.*','files.*','price_rules.*','suppliers.*')
                    ->groupBy('items.item_name')
                    ->where('items.product_type','<>',2)
                    ->first();

                if(!is_null($items)) {

                    $itemsWithItemKits = array($items);
                    $current_date = new \DateTime('today');
                    // Check price rules on specific items

                    foreach($itemsWithItemKits as $anItem) {

                        if(isset($anItem->item_id)){

                            $anItem->type = "auto";
                            if($priceNeededToBeScanned) {
                                $anItem->new_price = $item_new_price_from_barcode;
                                $anItem->useScanPrice = true;
                            }
                            if ($anItem->active){

                                if($anItem->unlimited||$anItem->num_times_to_apply>0)
                                {

                                    if($anItem->type==1){

                                        if($anItem->percent_off>0){

                                            $rule_start_date = new \DateTime($anItem->start_date);
                                            $rule_expire_date = new \DateTime($anItem->end_date);

                                            if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                                $discountPercentage = $anItem->percent_off;
                                                if($discountPercentage>100){
                                                    $anItem->discountPercentage = 100;
                                                    $anItem->itemPrice = $anItem->selling_price;
                                                    $anItem->discountName = $anItem->name;
                                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                                    $anItem->discountApplicable = true;
                                                }else{
                                                    $anItem->discountPercentage = $discountPercentage;
                                                    $anItem->itemPrice = $anItem->selling_price;
                                                    $anItem->discountName = $anItem->name;
                                                    $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                                    $anItem->discountApplicable = true;
                                                }

                                            }else{
                                                $anItem->discountApplicable = false;
                                            }

                                            //echo "Item should be discounted by ".$anItem->percent_off." percent";

                                        }else if($anItem->fixed_of>0){

                                            $rule_start_date = new \DateTime($anItem->start_date);
                                            $rule_expire_date = new \DateTime($anItem->end_date);

                                            if( ($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                                $discountPercentage = ($anItem->fixed_of/$anItem->selling_price)*100;
                                                if($discountPercentage>100){
                                                    $anItem->discountPercentage = 100;
                                                    $anItem->discountAmount = $anItem->selling_price;
                                                    $anItem->discountName = $anItem->name;
                                                    $anItem->itemPrice = $anItem->selling_price;
                                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->itemPrice;
                                                    $anItem->discountApplicable = true;
                                                }
                                                else{
                                                    $anItem->discountPercentage = $discountPercentage;
                                                    $anItem->discountAmount = $anItem->fixed_of;
                                                    $anItem->discountName = $anItem->name;
                                                    $anItem->itemPrice = $anItem->selling_price;
                                                    $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->discountAmount;
                                                    $anItem->discountApplicable = true;
                                                }

                                            }else{
                                                $anItem->discountApplicable = false;
                                            }
                                            // echo "Item should be discounted by ".$anItem->fixed_of." dollar";
                                        }

                                    }
                                }

                            }


                        }
                    }
                    echo json_encode($itemsWithItemKits);
                }
            }

        }else{

            // Get all items with images
            $search_param = (string) '%'.Input::get('q').'%';
            $items =  DB::table('items')
                ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
                ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
                ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
                ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
                ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
                ->where(function($query) use ($search_param) {
                    $query->where('item_name','LIKE',$search_param)
                        ->orWhere('isbn','LIKE',$search_param);
                })
                ->where('items.deleted_at',null)
                ->where('items.item_status',ItemStatus::$ACTIVE)
                ->select('items.id as item_id','items.*','files.*','price_rules.*','suppliers.*')
                ->groupBy('items.item_name')
                ->where('items.product_type','<>',2)
                ->get()->toArray();


            // Get all item kits


            //dd($itemKits);

            //Merge Item Kits with Items
            $itemsWithItemKits =$items;


            $current_date = new \DateTime('today');
            // Check price rules on specific items
            foreach($itemsWithItemKits as $anItem) {
                if(isset($anItem->item_id)){
                    $anItem->type = "list";
                    if ($anItem->active){

                        if($anItem->unlimited||$anItem->num_times_to_apply>0)
                        {

                            if($anItem->type==1){

                                if($anItem->percent_off>0){

                                    $rule_start_date = new \DateTime($anItem->start_date);
                                    $rule_expire_date = new \DateTime($anItem->end_date);

                                    if(($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                        $discountPercentage = $anItem->percent_off;
                                        if($discountPercentage>100){
                                            $anItem->discountPercentage = 100;
                                            $anItem->itemPrice = $anItem->selling_price;
                                            $anItem->discountName = $anItem->name;
                                            $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                            $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                            $anItem->discountApplicable = true;
                                        }else{
                                            $anItem->discountPercentage = $discountPercentage;
                                            $anItem->itemPrice = $anItem->selling_price;
                                            $anItem->discountName = $anItem->name;
                                            $anItem->discountAmount = $anItem->itemPrice*($discountPercentage/100);
                                            $anItem->itemPriceAfterDiscount = $anItem->itemPrice-$anItem->discountAmount;
                                            $anItem->discountApplicable = true;
                                        }

                                    }else{
                                        $anItem->discountApplicable = false;
                                    }

                                    //echo "Item should be discounted by ".$anItem->percent_off." percent";

                                }else if($anItem->fixed_of>0){

                                    $rule_start_date = new \DateTime($anItem->start_date);
                                    $rule_expire_date = new \DateTime($anItem->end_date);

                                    if( ($current_date>=$rule_start_date) && ($current_date<=$rule_expire_date) ) {
                                        $discountPercentage = ($anItem->fixed_of/$anItem->selling_price)*100;
                                        if($discountPercentage>100){
                                            $anItem->discountPercentage = 100;
                                            $anItem->discountAmount = $anItem->selling_price;
                                            $anItem->discountName = $anItem->name;
                                            $anItem->itemPrice = $anItem->selling_price;
                                            $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->itemPrice;
                                            $anItem->discountApplicable = true;
                                        }
                                        else{
                                            $anItem->discountPercentage = $discountPercentage;
                                            $anItem->discountAmount = $anItem->fixed_of;
                                            $anItem->discountName = $anItem->name;
                                            $anItem->itemPrice = $anItem->selling_price;
                                            $anItem->itemPriceAfterDiscount = $anItem->itemPrice - $anItem->discountAmount;
                                            $anItem->discountApplicable = true;
                                        }

                                    }else{
                                        $anItem->discountApplicable = false;
                                    }
                                    // echo "Item should be discounted by ".$anItem->fixed_of." dollar";
                                }

                            }
                        }

                    }
                }
            }

            // return response()->json($itemsWithItemKits);
            echo json_encode($itemsWithItemKits);
            // return response()->json(['success' => true,'items'=>$items], 200);

        }
    }



    public function DeleteItemGet($item_id){
        $item = new Item();
        $item->DeleteItem($item_id);

        return redirect()->route('item_list');
    }

    public function importExcelGet(){

        return view('items.item_import_excel');
    }

    public function importExcel(Request $request)
    {

        if(Input::hasFile('import_file')){
            $importedFileName = time().".csv";
            $path = Input::file('import_file')->getRealPath();
            $extension = $request->import_file->getClientOriginalExtension();
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                Storage::disk('uploaded_import_files')->put($importedFileName, file_get_contents(Input::file('import_file')));

                $data = Excel::load($path, function($reader) {
                })->get();

                $defaultValues = array(
                    "category" => -1,
                    "supplier" => -1,
                    "manufacturer"=>-1,
                    "quantity" => 100,
                    "status" => ItemStatus::$ACTIVE
                );

                $rules = array(
                    "upc" => "unique:items,isbn|nullable",
                    "name" => "required",
                    "cost" => "required",
                    "sell" => "required"
                );

                $columnMaps = array(
                    "upc" => "isbn",
                    "name" => "item_name",
                    "category_id" => "category_id",
                    "supplier_id" => "supplier_id",
                    "manufacturer" => "manufacturer_id",
                    "cost" => "cost_price",
                    "sell" => "selling_price",
                    "quantity" => "item_quantity",
                    "size" => "item_size",
                    "status" => "item_status",
                    "product_id" => "product_id"
                );

                $importer = new Importer("items",$columnMaps,$data,$rules,$defaultValues);

                $importer->insertIntoDB("upc");

                $excelFile = $this->downloadLogFile($importer->getErrorLogs());
                $objWriter = PHPExcel_IOFactory::createWriter($excelFile->excel, 'Excel2007');
                $objWriter->save(str_replace(__FILE__,'item_import_logs/'.$excelFile->filename  .'.xlsx',__FILE__));

                $itemImportLog = new ImportLog();
                $itemImportLog->user_id = Auth::user()->id;
                $itemImportLog->uploaded_file_path = asset('/item_import_uploads/'.$importedFileName);
                $itemImportLog->downloaded_file_path = asset("item_import_logs/".$excelFile->filename.'.xlsx');
                $itemImportLog->percentage = $importer->getStatusPercentage();
                $itemImportLog->type = ImportType::$ITEM;
                $itemImportLog->save();

//                Storage::disk('uploaded_import_files')->put($excelFile->filename, file_get_contents($excelFile));
                $failedItems = $importer->getFailureItems();
                if($failedItems>0) {
                    return redirect()->route('item_list')->with(["error"=>$failedItems. " items failed to import","html"=>"<a href=\'".asset("item_import_logs/".$excelFile->filename.'.xlsx')."\' target=\'_blank\'>(Click here to view the log file)</a>"]);
                }else{
                    return redirect()->route('item_list')->with(["success"=>"All items imported successfully"]);
                }

            }else {
                return redirect()->route('item_import_excel')->with(["error"=>"Only xls or csv files are allowed."]);
            }
        }
        return redirect()->back()->with(["error" => "No files selected"]);
    }

    public function downloadLogFile($errors){

        $excelFile = Excel::create('item_import_log'.time(), function($excel) use ($errors) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('item_import_status_file');
            $excel->setCreator('EZPOS')->setCompany('EZ POS, LLC');
            $excel->setDescription('item_import_status_file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($errors) {
                $sheet->fromArray($errors, null, 'A4', false, false);
            });

        });
        return $excelFile;
    }

    public function DeleteItems(Request $request){

        $item_list = $request->id_list;
        if(DB::table('items')->whereIn('id',$item_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);
    }

}
