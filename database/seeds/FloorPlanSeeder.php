<?php

use Illuminate\Database\Seeder;

class FloorPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    private function SittingGridSizeX() {
        $setting = DB::table('settings')->where('key', 'sitting_grid_size_x')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'sitting_grid_size_x',
                'value' => '0'
            ]);
        }
    }

    private function SittingGridSizeY() {
        $setting = DB::table('settings')->where('key', 'sitting_grid_size_y')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'sitting_grid_size_y',
                'value' => '0'
            ]);
        }
    }


    // icon
    private function IconBackgroundColor() {
        $setting = DB::table('settings')->where('key', 'icon_background_color')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_background_color',
                'value' => '0000FF'
            ]);
        }
    }

    private function IconWidth() {
        $setting = DB::table('settings')->where('key', 'icon_width')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_width',
                'value' => '600'
            ]);
        }
    }

    private function IconHeight() {
        $setting = DB::table('settings')->where('key', 'icon_height')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_height',
                'value' => '600'
            ]);
        }
    }

    private function IconPriceText() {
        $setting = DB::table('settings')->where('key', 'icon_price_text')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_text',
                'value' => ''
            ]);
        }
    }

    private function IconPriceFontSize() {
        $setting = DB::table('settings')->where('key', 'icon_price_font_size')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_font_size',
                'value' => '10'
            ]);
        }
    }

    private function IconPriceColor() {
        $setting = DB::table('settings')->where('key', 'icon_price_color')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_color',
                'value' => 'FF0000'
            ]);
        }
    }

    private function IconPriceBackgroundColor() {
        $setting = DB::table('settings')->where('key', 'icon_price_background_color')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_background_color',
                'value' => 'FFF000'
            ]);
        }
    }

    private function IconPriceLocationX() {
        $setting = DB::table('settings')->where('key', 'icon_price_location_x')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_location_x',
                'value' => 'left'
            ]);
        }
    }

    private function IconPriceLocationY() {
        $setting = DB::table('settings')->where('key', 'icon_price_location_y')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_price_location_y',
                'value' => 'top'
            ]);
        }
    }

    private function IconLableText() {
        $setting = DB::table('settings')->where('key', 'icon_label_text')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_text',
                'value' => ''
            ]);
        }
    }

    private function IconLableFontSize() {
        $setting = DB::table('settings')->where('key', 'icon_label_font_size')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_font_size',
                'value' => '75'
            ]);
        }
    }

    Private function IconLableColor() {
        $setting = DB::table('settings')->where('key', 'icon_label_color')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_color',
                'value' => '00FF50'
            ]);
        }
    }

    Private function IconLableBackgroundColor() {
        $setting = DB::table('settings')->where('key', 'icon_label_background_color')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_background_color',
                'value' => '000000'
            ]);
        }
    }

    Private function IconLableLocationX() {
        $setting = DB::table('settings')->where('key', 'icon_label_location_x')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_location_x',
                'value' => 'center'
            ]);
        }
    }

    Private function IconLableLocationY() {
        $setting = DB::table('settings')->where('key', 'icon_label_location_y')->first();

        if (!$setting) {
            DB::table('settings')->insert([
                'key' => 'icon_label_location_y',
                'value' => 'middle'
            ]);
        }
    }

    public function run()
    {

        $this->SittingGridSizeX();
        $this->SittingGridSizeY();

        $this->IconBackgroundColor();
        $this->IconWidth();
        $this->IconHeight();
        $this->IconPriceText();
        $this->IconPriceFontSize();
        $this->IconPriceColor();
        $this->IconPriceBackgroundColor();
        $this->IconPriceLocationX();
        $this->IconPriceLocationY();
        $this->IconLableText();
        $this->IconLableFontSize();
        $this->IconLableColor();
        $this->IconLableBackgroundColor();
        $this->IconLableLocationX();
        $this->IconLableLocationY();

    }
}
