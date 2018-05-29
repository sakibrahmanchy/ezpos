<?php

namespace App\Http\Controllers;

use App\Enumaration\ItemStatus;
use App\Model\Category;
use App\Model\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    //

    public function GetCategoryList(){

        $categoryList = new Category();
        $categoryList->fetchCategories();
        $categoryList->GenerateCategoryMenu(0);
        $generatedCategoryMenu = $categoryList->GetGeneratedMenu();

        return view('category_list',['categoryMenu'=>$generatedCategoryMenu]);
    }

    public function AddCategory(Request $request){
        $category = new Category();

            $category->InsertCategory($request->categoryName,$request->parent);
            return response()->json(['success' => true], 200);
    }

    public function EditCategory(Request $request){
        $category = new Category();

        $category->EditCategory($request->categoryName,$request->categoryId);

        return response()->json(['success' => true], 200);
    }

    public function DeleteCategory(Request $request){
        $category = new Category();

        $category->DeleteCategory($request->categoryId);

        return response()->json(['success' => true], 200);
    }


    public function FindCategoriesByLevel(Request $request){
        if(!isset($request->category_level))
            return response()->json(["success" => false, "message" => "Category level is required", "data"=>null],200);

        $level = $request->category_level;
        $categoriesByLevel = Category::where("parent", $level)->get();
        return response()->json(["success" => true, "message" => "Category list fetch success", "data"=>$categoriesByLevel],200);
    }

    public function FindCategoryParent(Request $request){
        if(!isset($request->category_id))
            return response()->json(["success" => false, "message" => "Category id is required", "data"=>null],200);

        $id = $request->category_id;
        $category = Category::where("id", $id)->first();
        return response()->json(["success" => true, "message" => "Category id fetch success", "data"=>$category->parent],200);
    }

    public function FetchProductsInCategory(Request $request) {
        if(!isset($request->category_id))
            return response()->json(["success" => false, "message" => "Category id is required", "data"=>null],200);

        $id = $request->category_id;
        $products =  $items =  DB::table('items')
            ->leftJoin('items_images', 'items.id', '=', 'items_images.item_id')
            ->leftJoin('files', 'files.id', '=', 'items_images.file_id')
            ->leftJoin('item_price_rule','items.id','=','item_price_rule.item_id')
            ->leftJoin('price_rules','item_price_rule.price_rule_id','=','price_rules.id')
            ->leftJoin('suppliers','suppliers.id','=','items.supplier_id')
            ->where("category_id",$id)
            ->where('items.deleted_at',null)
            ->where('items.item_status',ItemStatus::$ACTIVE)
            ->select('items.id as item_id','items.*','files.*','price_rules.*','suppliers.*')
            ->groupBy('items.item_name')
            ->where('items.product_type','<>',2)
            ->get();
        return response()->json(["success" => true, "message" => "Products fetch success", "data"=>$products],200);
    }
}
