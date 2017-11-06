<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $categoryList;
    protected $generatedMenu = "";

    public function PriceRules(){
        return $this->belongsToMany('App\Model\PriceRule');
    }

    public function Items(){
        return $this->hasMany('App\Model\Item');
    }

    public function InsertCategory($category_name,$parent){
                $category = new Category();
                $category->category_name = $category_name;
                $category->parent = $parent;
                $category->save();
    }

    public function EditCategory($category_name,$category_id){

        $category = Category::where('id',$category_id)->first();
        $category->category_name = $category_name;
        $category->save();
    }


    public function DeleteCategory($category_id){
        Category::where('id', $category_id)->delete();
        Category::where('parent', $category_id)->delete();
    }

    public function FetchCategories(){
        $categoryList = Category::orderBy('category_name')->get();
        foreach($categoryList as $aCategory){
          $this->categoryList[$aCategory->id] = array('id'=>$aCategory->id,
                                                      'category_name'=> $aCategory->category_name,
                                                      'parent'=>$aCategory->parent);
        }
    }

    public function InitiateMenu(){
        $this->generatedMenu = '';
    }


    public function GenerateCategoryMenu($parent){

        $hasChilds = false;

        $categoryList = $this->categoryList;

        if(!($categoryList==null)) {
            foreach ($categoryList as $key => $value) {
                if ($value['parent'] == $parent) {

                    if ($hasChilds == false) {
                        $hasChilds = true;
                        $this->generatedMenu .= '<ul class>';
                    }

                    $this->generatedMenu .= '' . $value['category_name'] . ' <a class="child" href="javascript:void(0)" onclick="OpenAddCategoryDialog(this)" id = "' . $value['id'] . '"> [Add child category]</a><a class="child" href="javascript:void(0)" onclick ="OpenEditCategoryDialog(this)" data-value = "' . $value['category_name'] . '"  id = "' . $value['id'] . '"> [Edit]</a><a class="child" href="javascript:void(0)" onclick ="deleteCategory(this)" id = "' . $value['id'] . '"> [Delete]</a><br>';

                    $this->GenerateCategoryMenu($key);

                    $this->generatedMenu .= '';
                }
            }

            if ($hasChilds == true) $this->generatedMenu .= '</ul>';
        }
        else $this->generatedMenu .= '';
    }




    public function GetGeneratedMenu(){
        return $this->generatedMenu;
    }
}
