<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Media;
use App\Model\Sitting;
use App\Model\Setting;
use Illuminate\Support\Facades\DB;
use Storage;


class FloorPlanController extends Controller
{
    public function index() {
        $page_data = [
            'page_title' => 'Floor Plan',
            'page_subtitle' => '',
        ];

        $size_x = DB::table('settings')->where('key', 'sitting_grid_size_x')->first();
        $size_x = (int) $size_x->value;
        $size_y = DB::table('settings')->where('key', 'sitting_grid_size_y')->first();
        $size_y = (int) $size_y->value;

        $sittings = Sitting::all();

        return view('floor_plan.index', compact('size_x', 'size_y', 'sittings'))
                ->with($page_data);
    }

    public function showNewPlan(Request $request) {
        $page_data = [
            'page_title' => 'New floor Plan',
            'page_subtitle' => '',
        ];

        $row = 0;
        $column = 0;

        if ($request->y)
            $row = $request->y;

        if ($request->x)
            $column = $request->x;

        $images = Media::all();
        foreach($images as &$image) 
            $image->src = route('get_media_image', ['media' => $image->id]);

        $settings_db = Setting::all();

        $settings = [];
        foreach($settings_db as $setting)
            $settings[$setting->key] = $setting->value;


        return view('floor_plan.new', compact('row', 'column', 'images', 'settings'))
                ->with($page_data);
    }

    public function postNewPlan(Request $request) {
        DB::table('sittings')->delete();

        for ($i=0; $i<sizeof($request->name); $i++) {
            if ($request->name[$i] != "" && $request->logo[$i] != "") {
                $sitting = Sitting::create([
                        'name' => $request->name[$i],
                        'sit_count' => $request->seat[$i],
                        'busy' => 0,
                        'sit_busy' => 0,
                        'position_x' => $request->position_x[$i],
                        'position_y' => $request->position_y[$i],
                    ]);

                Storage::put(
                    'sitting_images/'.$sitting->id.'.png',
                    file_get_contents($request->logo[$i])
                );

               DB::table('customers')->insert([
                   "first_name" => "Table ".$request->name,
                   "sitting_id" => $sitting->id
               ]);
            }
        }

        DB::table('settings')->where('key', 'sitting_grid_size_x')->update([
                'value' => $request->size_x,
            ]);

        DB::table('settings')->where('key', 'sitting_grid_size_y')->update([
                'value' => $request->size_y,
            ]);

        return redirect()->route('floor_plan');
    }

    public function showEditPlan() {
        $page_data = [
            'page_title' => 'Edit floor Plan',
            'page_subtitle' => '',
        ];


        $column = DB::table('settings')->where('key', 'sitting_grid_size_x')->first();
        $column = (int) $column->value;
        $row = DB::table('settings')->where('key', 'sitting_grid_size_y')->first();
        $row = (int) $row->value;

        $images = Media::all();
        foreach($images as &$image) 
            $image->src = route('get_media_image', ['media' => $image->id]);

        $sittings = Sitting::all();

        $settings_db = Setting::all();

        $settings = [];
        foreach($settings_db as $setting)
            $settings[$setting->key] = $setting->value;

        return view('floor_plan.edit', compact('row', 'column', 'images', 'sittings', 'settings'))->with($page_data);
    }

    public function postEditPlan(Request $request) {
        for ($i=0; $i<sizeof($request->name); $i++) {
            if ($request->id[$i] != "" && $request->name[$i] != "") {
                // edit
                $sitting = Sitting::where('id', $request->id[$i])->first();
                $sitting->name = $request->name[$i];
                $sitting->sit_count = $request->seat[$i];
                $sitting->busy = 0;
                $sitting->sit_busy = 0;
                $sitting->position_x = $request->position_x[$i];
                $sitting->position_y = $request->position_y[$i];
                $sitting->save();

                if ($request->logo[$i]) {
                    Storage::put(
                        'sitting_images/'.$sitting->id.'.png',
                        file_get_contents($request->logo[$i])
                    );
                }

            } else if ($request->id[$i] == "" && $request->name[$i] != "" && $request->logo[$i] != "") {
                // new
                $sitting = Sitting::create([
                        'name' => $request->name[$i],
                        'sit_count' => $request->seat[$i],
                        'busy' => 0,
                        'sit_busy' => 0,
                        'position_x' => $request->position_x[$i],
                        'position_y' => $request->position_y[$i],
                    ]);

                Storage::put(
                    'sitting_images/'.$sitting->id.'.png',
                    file_get_contents($request->logo[$i])
                );

                DB::table('customers')->insert([
                    "first_name" => "Table ".$request->name[$i],
                    "sitting_id" => $sitting->id
                ]);
            } else if ($request->id[$i] != "" && $request->name[$i] == "") {
                // delete
                $sitting = Sitting::where('id', $request->id[$i])->first();
                $sitting->delete();
            }
        }

        return redirect()->route('floor_plan');
    }

    public function getLogo(Sitting $sitting){
        return response(Storage::get('sitting_images/'.$sitting->id.'.png', 200, ['Content-Type' => 'image/png']));
    }
}
