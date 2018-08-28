<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Media;
use Storage;
use File;
use Image;

class MediaController extends Controller
{
    public function index() {
        $page_data = [
            'page_title' => 'Media',
            'page_subtitle' => '',
        ];

        $images = Media::all();

        foreach($images as &$image) {
            $image->src = route('get_media_image', ['media' => $image->id]);
        }

        return view('media.index', compact('images'))->with($page_data);
    }

    public function showAddImage() {
        $page_data = [
            'page_title' => 'Add Media',
            'page_subtitle' => '',
        ];

        return view('media.add')->with($page_data);
    }

    public function postAddImage(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'logo' => 'required|max:1000|image',
        ]);

        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();

        $media = Media::create([
            'name' => $request->name,
            'extension' => $extension,
            'mime' => $file->getMimeType()
        ]);

        Storage::put(
            'media/'.$media->id.'.'.$extension, file_get_contents($request->logo)
        );

        return redirect()->route('media_view');
    }

    public function getImage(Media $media) {
        //return response(Storage::get('media/'.$media->id.'.'.$media->extension, 200, ['Content-Type' => $media->mime]));

        $img_data = Storage::get('media/'.$media->id.'.'.$media->extension, 200, ['Content-Type' => $media->mime]);


        $img = Image::cache(function($image) use ($img_data) {
           return $image->make($img_data)->resize(200, 200);
        });

        return $img;
    }

    public function deleteImage(Request $request) {
        $media = Media::where('id', $request->id)->first();
        $media->delete();
    }
}
