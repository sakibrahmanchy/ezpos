<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public function InsertTag($tag_name){
        $tag = new Tag();
        $tag->tag_name = $tag_name;
        $tag->save();
    }

    public function EditTag($tag_name,$tag_id){

        $tag = Tag::where('id',$tag_id)->first();
        $tag->tag_name = $tag_name;
        $tag->save();
    }


    public function DeleteTag($tag_id){
        $tag = Tag::find($tag_id);
        $tag->delete();

    }

    public function FetchTags(){
        $tagList = Tag::orderBy('tag_name')->get();
        return $tagList;
    }

}
