<?php

namespace App\Http\Controllers;

use App\Model\Category;
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
}
