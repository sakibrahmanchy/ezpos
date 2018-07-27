<?php

namespace App\Http\Controllers;

use App\Enumaration\ImportType;
use App\Enumaration\ItemStatus;
use App\Enumaration\PriceRuleTypes;
use App\Libraries\SSP;
use App\Library\SettingsSingleton;
use App\Model\Category;
use App\Model\Customer;
use App\Model\File;
use App\Model\ImportLog;
use App\Model\Item;
use App\Model\ItemKit;
use App\Model\ItemsImage;
use App\Model\Manufacturer;
use App\Model\PriceLevel;
use App\Model\Setting;
use App\Model\Supplier;
use Faker\Provider\zh_CN\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Model\ImporterWizard\Importer;
use PHPExcel_IOFactory;


class ItemController extends Controller
{
    public function GetItemForm(Request $request)
    {
        //Load all permissions from database
        $categoryList = Category::orderBy('category_name')->get();

        $supplierList = Supplier::all();

        $manufacturerList = Manufacturer::all();

        return view('items.new_item',['categoryList'=>$categoryList,'supplierList'=>$supplierList,
                                      'manufacturerList'=>$manufacturerList]);
    }

    public function AddItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isbn' => 'unique:items|nullable',
            'item_name' => 'required',
            'item_category' => 'required',
            'item_supplier' => 'required',
            'reorder_level' => 'sometimes|nullable|integer',
            'replenish_level' => 'sometimes|nullable|integer',
            'expire_days' => 'sometimes|nullable|integer',
            'cost_price' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'product_id' => 'nullable|unique:items'
        ]);

        if ($validator->fails()) {
            return redirect(URL::previous())
                ->withErrors($validator)
                ->withInput();
        }

        $item = new Item();
        $item->InsertItem($request);

        return redirect()->route('item_list');

    }

    public function cloneItem(Request $request) {
        if(!isset($request->item_id))
            return redirect()->route('item_list')->with(["error"=>"Item id is required to clone."]);

        $categoryList = Category::orderBy('category_name')->get();

        $supplierList = Supplier::all();

        $manufacturerList = Manufacturer::all();

        $previousItemId = $request->item_id;
        $previousItemInfo = Item::where("id",$previousItemId)->first();

        $item = new Item();
        $images = $item->getItemImages($previousItemId);

        return view('items.clone_item',['previous_item_info'=>$previousItemInfo,'categoryList'=>$categoryList,'supplierList'=>$supplierList,
                                        'manufacturerList'=>$manufacturerList, "images" => $images]);
    }



    public function GetItemListAjax() {
        $items = Item::all();
        return view('items.item_list_ajax',['allItems'=>$items]);
    }

    public function getAllItemsData(Request $request) {

        $where = '';

        if ($request->item_status != "0")
            $where .= "item_status = ".$request->item_status;



        $allItems = <<<EOT
(
     SELECT items.product_id as product_id, items.id as item_id, items.item_name as item_name, items.item_status as item_status,
            items.isbn as upc, items.item_quantity as quantity, items.item_size as size, items.cost_price as cost_price,   
            items.selling_price as selling_price, items.item_reorder_level as reorder_level, items.item_replenish_level as replenish_level, 
            items.days_to_expiration as expire_date, items.price_include_tax as price_include_tax, items.service_item as service_item,
            categories.category_name as category, suppliers.company_name as supplier
     FROM items
     LEFT JOIN categories on items.category_id = categories.id 
     LEFT JOIN suppliers on items.supplier_id = suppliers.id
     LEFT JOIN manufacturers on items.manufacturer_id = manufacturers.id 
     WHERE product_type = 0 and product_type <> 2 
     AND items.deleted_at is null
) temp
EOT;

        $primaryKey = 'item_id';

        $columns = array(
            array( 'db' => 'product_id', 'dt' => 'product_id' ),
            array( 'db' => 'item_name', 'dt' => 'item_name' ),
            array( 'db' => 'item_status', 'dt' => 'item_status' ),
            array( 'db' => 'supplier', 'dt' => 'supplier' ),
            array( 'db' => 'upc', 'dt' => 'upc' ),
            array( 'db' => 'quantity', 'dt' => 'quantity' ),
            array( 'db' => 'size', 'dt' => 'size' ),
            array( 'db' => 'cost_price', 'dt' => 'cost_price' ),
            array( 'db' => 'selling_price', 'dt' => 'selling_price' ),
            array( 'db' => 'category', 'dt' => 'category' ),
            array( 'db' => 'reorder_level', 'dt' => 'reorder_level' ),
            array( 'db' => 'replenish_level', 'dt' => 'replenish_level' ),
            array( 'db' => 'expire_date', 'dt' => 'expire_date' ),
            array('db' => 'price_include_tax', 'dt' => 'price_include_tax'),
            array('db' => 'service_item', 'dt' => 'service_item'),
            array("db" => "item_id", "dt" => "item_id")
        );

        $db_connection = config('database.default');

        $sql_details = array(
            'user' => config('database.connections.'.$db_connection.'.username'),
            'pass' => config('database.connections.'.$db_connection.'.password'),
            'db'   => config('database.connections.'.$db_connection.'.database'),
            'host' => config('database.connections.'.$db_connection.'.host')
        );

        $returnedData = SSP::complex( $_GET, $sql_details, $allItems, $primaryKey, $columns, $where );
        $returnedData = $this->convert_from_latin1_to_utf8_recursively($returnedData);
     //   dd($returnedData);
        return response()
            ->json($returnedData);

    }


    public static function convert_from_latin1_to_utf8_recursively($dat)
    {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d) $ret[ $i ] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d) $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
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
            'unit_price' => 'required|numeric',
            'product_id' => 'nullable|unique:items,product_id,'.$itemId
        ]);

        $item = new Item();
        $item->editItem($request,$itemId);
        return redirect()->route('item_list');

    }


    public function GetItemsAutocomplete(){

        $autoselect = Input::get('autoselect');

        if($autoselect=="true") {

            $search_param = (string) Input::get('q');
			$search_param = preg_replace('/[^0-9]/','',$search_param);

            if($search_param!=""||$search_param!=null){
                $scan_price_from_barcode = SettingsSingleton::getByKey('scan_price_from_barcode');
                $item_new_price_from_barcode = -1;
                $priceNeededToBeScanned = false;
                if(strlen($search_param)==12) {
                    if($scan_price_from_barcode=="true"){
                        $upc_code_prefix = $scan_price_from_barcode = SettingsSingleton::getByKey('upc_code_prefix');

                        if(substr($search_param,0,strlen($upc_code_prefix)) ===  $upc_code_prefix){
                            $item_new_price_from_barcode = (int) substr($search_param,6,12);
                            $item_new_price_from_barcode =  ((int)($item_new_price_from_barcode / 10))/100;
                            $search_param = substr($search_param,0,6);
                            $priceNeededToBeScanned = true;
                        }
                    }
                }
                if($priceNeededToBeScanned) {
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
                        ->select('items.id as item_id','price_rules.id as price_rule_id','items.*','files.*','price_rules.*','suppliers.*')
                        ->groupBy('items.item_name')
                        ->where('items.product_type','<>',2)
                        ->first();
                }
                else {
					//check if product exists by normal match query
					//it will solve if both query DB with checkdigit-scan with checkdigit, DB without checkdigit-scan without checkdigit
					$matchedItem = DB::table('items')
										->where('isbn','=',$search_param)
										->where('deleted_at',null)
										->where('item_status',ItemStatus::$ACTIVE)
										->where('product_type','<>',2)
										->first();
					if(!$matchedItem)
					{
						//check DB checkdigit-scan without checkdigit
						$matchedItem = DB::table('items')
										->where('isbn','like', '_' . $search_param . '_' )
										->where('deleted_at',null)
										->where('item_status',ItemStatus::$ACTIVE)
										->where('product_type','<>',2)
										->first();
					}
					
					if(!$matchedItem)
					{
						//check DB without checkdigit-scan with checkdigit
						$cleanedParam = strpos($search_param, 1 , -1);
						$matchedItem = DB::table('items')
										->where('isbn','=',$cleanedParam)
										->where('deleted_at',null)
										->where('item_status',ItemStatus::$ACTIVE)
										->where('product_type','<>',2)
										->first();
					}
					if(!$matchedItem)
						return json_encode([]);
				
                    $items =  DB::table('items')
                        ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
                        ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
                        ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
                        ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
                        ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
                        /*->where(function($query) use ($search_param) {
                            $query->where('isbn','=',$search_param);
                        })
                        ->where('items.deleted_at',null)
                        ->where('items.item_status',ItemStatus::$ACTIVE)*/
                        ->select('items.id as item_id','price_rules.id as price_rule_id','items.*','files.*','price_rules.*','suppliers.*')
                        ->groupBy('items.item_name')
                        //->where('items.product_type','<>',2)
						->where('items.id',$matchedItem->id)
                        ->first();
                }
                if(!is_null($items)) {

                    $itemsWithItemKits = array($items);
                    $current_date = new \DateTime('today');
                    // Check price rules on specific items

                    foreach($itemsWithItemKits as $anItem) {
                        if(is_null($anItem->price_rule_id))
                            $anItem->price_rule_id = 0;
                        if(isset($anItem->item_id)){

                            $anItem->scan_type = "auto";
                            if($priceNeededToBeScanned) {
                                $anItem->new_price = $item_new_price_from_barcode;
                                $anItem->useScanPrice = true;
                            }
                            if ($anItem->active){

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
                ->select('items.id as item_id','price_rules.id as price_rule_id','items.*','files.*','price_rules.*','suppliers.*')
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
                if(is_null($anItem->price_rule_id))
                    $anItem->price_rule_id = 0;
                if(isset($anItem->item_id)){
                    $anItem->scan_type = "list";
                    if ($anItem->active){

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

    public function getItemPrice(Request $request) {
        $item_id = $request->item_id;
        $customer_id = $request->customer_id;
        $itemPriceLevel = DB::table("customer_item")
            ->where("customer_id",$customer_id)
            ->where("item_id",$item_id)
            ->first();
        $itemPrice = Item::where("id",$item_id)->first()->selling_price;
        if(!is_null($itemPriceLevel)) {
            $priceLevelId = $itemPriceLevel->price_level_id;
            $priceLevel = PriceLevel::where("id",$priceLevelId)->first();
            $percentage = $priceLevel->percentage;
            $itemPriceToChange = ($itemPrice * ($percentage/100));
            $newItemPrice = $itemPrice + $itemPriceToChange;
            return response()->json(["success"=>true,"priceLevelStatus"=>true,"price"=>$newItemPrice]);
        }
        else
            return response()->json(["success"=>true,"priceLevelStatus"=>false,"price"=>$itemPrice]);

    }

}
