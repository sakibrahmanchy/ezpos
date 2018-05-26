<?php

namespace App\Http\Controllers;

use App\Enumaration\ImportType;
use App\Model\ImporterWizard\Importer;
use App\Model\ImportLog;
use App\Model\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Excel;
use PHPExcel_IOFactory;


class SupplierController extends Controller
{
    //
    public function GetSupplierForm()
    {
        return view('suppliers.new_supplier');
    }

    public function AddSupplier(Request $request){

        $rules = [
            'company_name' => 'required|unique:suppliers',

        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('new_supplier')
                ->withErrors($validator)
                ->withInput($request->input());
        }

        $supplier = new Supplier();

        $supplierCredentials['company_name'] = $request->company_name;
        $supplierCredentials['first_name'] = $request->first_name;
        $supplierCredentials['last_name'] = $request->last_name;
        $supplierCredentials['phone'] = $request->phone;
        $supplierCredentials['address_1'] = $request->address_1;
        $supplierCredentials['address_2'] = $request->address_2;
        $supplierCredentials['city'] = $request->city;
        $supplierCredentials['state'] = $request->state;
        $supplierCredentials['zip'] = $request->zip;
        $supplierCredentials['country'] = $request->country;
        $supplierCredentials['comments'] = $request->comments;
        $supplierCredentials['account'] = $request->account;

        $userImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {

            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('supplier_user_pictures')->put($userImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $supplierCredentials['image_location'] = $userImageToken.'.jpg';
        }



        $supplierId = \App\Model\Supplier::create($supplierCredentials)->id;

        return redirect()->route('supplier_list');


    }

    public function getSupplierList(){

            $suppliers = Supplier::orderBy('company_name', 'ASC')->get();


            return view('suppliers.supplier_list', ["suppliers" => $suppliers]);

    }

    public function EditSupplierGet($supplierId)
    {

        $supplierInfo = Supplier::where("id", "=", $supplierId)->first();

        return view('suppliers.supplier_edit', ['supplier' => $supplierInfo]);
    }

    public function EditSupplierPost(Request $request, $supplierId){
        $supplier = Supplier::where("id", "=", $supplierId)->first();

        /* var_dump($supplier);*/
        $rules = [
            'company_name' => 'required'
        ];
        $allInput = $request->all();

        $validator = Validator::make($allInput, $rules);
        if ($validator->fails()) {


            return redirect()->route('supplier_edit', ['supplier_id' => $supplierId])
                ->withErrors($validator)
                ->withInput($request->input());
        }


        $supplier->company_name = $request->company_name;
        $supplier->first_name = $request->first_name;
        $supplier->last_name = $request->last_name;
        $supplier->phone = $request->phone;
        $supplier->address_1 = $request->address_1;
        $supplier->address_2 = $request->address_2;
        $supplier->city = $request->city;
        $supplier->state = $request->state;
        $supplier->zip = $request->zip;
        $supplier->country = $request->country;
        $supplier->comments = $request->comments;
        $supplier->account = $request->account;


        $supplierImageToken = uniqid();
        $file = $request->file('image');

        if ($file) {

            $image = Image::make($file)->stream(); //Resizing image using Intervention Image
            Storage::disk('supplier_user_pictures')->put($supplierImageToken . '.jpg', $image);  // Storing image in the disk as the name according to user id
            $supplier->image_location = $supplierImageToken . ".jpg";
        }

        $supplier->save();
        return redirect()->route('supplier_list');
    }

    public function SuppliersDataToExcel(){

        $suppliers = Supplier::all()->toArray();

        Excel::create('suppliers', function($excel) use ($suppliers) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('Suppliers');
            $excel->setCreator('EZPOS')->setCompany('EZ POS, LLC');
            $excel->setDescription('suppliers file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($suppliers) {
                $sheet->fromArray($suppliers, null, 'A1', false, false);
            });

        })->download('xlsx');
    }

    public function DeleteSuppliers(Request $request){

        $supplier_list = $request->id_list;
        if(DB::table('suppliers')->whereIn('id',$supplier_list)->delete())
            return response()->json(["success"=>true],200);
        return response()->json(["success"=>false],200);

    }

    public function importExcelGet(){

        return view('suppliers.supplier_import_excel');
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

                $defaultValues = array();

                $rules = array(
                    "name" => "required",
                );

                $columnMaps = array(
                    "name" => "company_name",
                    "phone" => "phone",
                    "address1" => "address_1",
                    "address2" => "address_2",
                    "city" => "city",
                    "state" => "state",
                    "zip" => "zip"
                );

                $importer = new Importer("suppliers",$columnMaps,$data,$rules,$defaultValues);

                $importer->insertIntoDB("company_name");

                $excelFile = $this->downloadLogFile($importer->getErrorLogs());
                $objWriter = PHPExcel_IOFactory::createWriter($excelFile->excel, 'Excel2007');
                $objWriter->save(str_replace(__FILE__,'item_import_logs/'.$excelFile->filename  .'.xlsx',__FILE__));

                $itemImportLog = new ImportLog();
                $itemImportLog->user_id = Auth::user()->id;
                $itemImportLog->uploaded_file_path = asset('/item_import_uploads/'.$importedFileName);
                $itemImportLog->downloaded_file_path = asset("item_import_logs/".$excelFile->filename.'.xlsx');
                $itemImportLog->percentage = $importer->getStatusPercentage();
                $itemImportLog->type = ImportType::$SUPPLIER;
                $itemImportLog->save();

//                Storage::disk('uploaded_import_files')->put($excelFile->filename, file_get_contents($excelFile));
                $failedItems = $importer->getFailureItems();
                if($failedItems>0) {
                    return redirect()->route('supplier_list')->with(["error"=>$failedItems. " suppliers failed to import","html"=>"<a href=\'".asset("item_import_logs/".$excelFile->filename.'.xlsx')."\' target=\'_blank\'>(Click here to view the log file)</a>"]);
                }else{
                    return redirect()->route('supplier_list')->with(["success"=>"All suppliers imported successfully"]);
                }

            }else {
                return redirect()->route('item_import_excel')->with(["error"=>"Only xls or csv files are allowed."]);
            }

        }
        return redirect()->back()->with(["error" => "No files selected"]);
    }

    public function downloadLogFile($errors){

        $excelFile = Excel::create('supplier_import_log'.time(), function($excel) use ($errors) {

            // Set the spreadsheet title, creator, and description
            $excel->setTitle('suppplier_import_status_file');
            $excel->setCreator('EZPOS')->setCompany('EZ POS, LLC');
            $excel->setDescription('supplier_import_status_file');

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($errors) {
                $sheet->fromArray($errors, null, 'A4', false, false);
            });

        });
        return $excelFile;
    }
}
