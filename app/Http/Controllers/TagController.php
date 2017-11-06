<?php

namespace App\Http\Controllers;

use App\Model\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function GetTagList(){
        $tag = new Tag();
        $tagList = $tag->FetchTags();

        return view('tag_list',['tagMenu'=>$tagList]);
    }

    public function AddTag(Request $request){
        $tag = new Tag();

        $tag->InsertTag($request->tagName);
        return response()->json(['success' => true], 200);
    }

    public function EditTag(Request $request){
        $tag = new Tag();

        $tag->EditTag($request->tagName,$request->tagId);

        return response()->json(['success' => true], 200);
    }

    public function DeleteTag(Request $request){
        $tag = new Tag();

        $tag->DeleteTag($request->tagId);

        return response()->json(['success' => true], 200);
    }

    public function GetTags(){
        $tag = new Tag();
        $tagList = $tag->FetchTags();

        return $tagList;
    }
}
