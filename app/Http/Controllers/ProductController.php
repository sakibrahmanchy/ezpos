<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use File;
use Validator;
use App\Model\Product;
use App\Model\Category;
use App\Model\Location;
use App\Model\Media;
use App\Model\Setting;
use Uuid;

class ProductController extends Controller
{
    public function showAddProduct(){
    	$categories = Category::all();
        $locations = Location::all();
        $images = Media::all();
        $settings_db = Setting::all();

        $settings = [];
        foreach($settings_db as $setting)
            $settings[$setting->key] = $setting->value;

        foreach($images as &$image) {
            $image->src = route('get_media_image', ['media' => $image->id]);
        }


    	return view('product.add', compact('categories', 'locations', 'images', 'settings'));
    }

    public function postAddProduct(Request $request) {
    	$this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'logo' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
        ]);

        /*$file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();*/

        $filename = Uuid::generate(4)->string;

        $product = Product::create([
        		'name' => $request->name,
        		'description' => $request->description,
        		'category_id' => $request->category_id,
                'location_id' => $request->location_id,
                'logo_filename' => $filename.'.png',
        	]);

        $file = file_get_contents($request->logo);
        //Storage::disk('product_images/')->put($product->id.'.png', $file);

        Storage::put(
            'product_images/'.$filename.'.png',
            file_get_contents($request->logo)
        );


        // Add to gallery
        if ($request->logo_from == "0") {
            $media = Media::create([
                'name' => Uuid::generate(4)->string,
                'extension' => 'png',
                'mime' => 'image/png'
            ]);

            Storage::put(
                'media/'.$media->id.'.png', file_get_contents($request->logo)
            );
        }

        return redirect()->route('all_product_view');
    }

    public function showAllProduct(Request $request) {
        $page_data = [
            'page_title' => 'Products',
            'page_subtitle' => '',
        ];

        $categories = Category::all();
        $parameters = [];
        $appends = array();

        if ($request->category_id){
            $parameters [] = array('category_id', '=', $request->category_id);
            $appends['category_id'] = $request->category_id;
        }

        $products = Product::with('category')->where($parameters)->paginate(10);
        
    	return view('product.all', compact('products', 'categories', 'appends'))
                ->with($page_data);
    }

    public function getLogo($filename){
        if (Storage::disk('local')->exists('product_images/'.$filename)) {
            return response(Storage::get('product_images/'.$filename, 200, ['Content-Type' => 'image/png']));
        }
    }

    public function showEditProduct(Product $product) {
    	$categories = Category::all();
        $locations = Location::all();
        $images = Media::all();
        $settings_db = Setting::all();

        $settings = [];
        foreach($settings_db as $setting)
            $settings[$setting->key] = $setting->value;

        foreach($images as &$image) {
            $image->src = route('get_media_image', ['media' => $image->id]);
        }

    	return view('product.edit', compact('product', 'categories', 'locations', 'images', 'settings'));
    }

    public function postEditProduct(Request $request, Product $product) {
    	$this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'category_id' => 'required',
            'location_id' => 'required',
        ]);

        $product->name = $request->name;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->location_id = $request->location_id;

        if ($request->logo){
            $filename = Uuid::generate(4)->string.'.png';
            $product->logo_filename = $filename;

            $file = file_get_contents($request->logo);
            Storage::put(
                'product_images/'.$filename,
                file_get_contents($request->logo)
            );

            // Add to gallery
            if ($request->logo_from == "0") {
                $media = Media::create([
                    'name' => Uuid::generate(4)->string,
                    'extension' => 'png',
                    'mime' => 'image/png'
                ]);

                Storage::put(
                    'media/'.$media->id.'.png', file_get_contents($request->logo)
                );
            }
        }

        $product->save();
        return redirect()->route('all_product_view');
    }

    public function deleteProduct(Request $request) {
    	$id = $request->id;
    	
    	$product = Product::find($id);

        $product->menus()->detach();
        $product->delete();
    }

    public function getProductDetails(Request $request) {
        $id  = $request->id;

        $product = Product::find($id);
        return $product->toArray();
    }

    public function ajaxProductUpdate(Request $request) {
        $id = $request->id;

        $product = Product::find($id);

        $product->price = $request->price;
        $product->stock_price = $request->stock_price;
        $product->quantity = $request->quantity;
        $product->save();

        InventoryLog::create([
            'product_id' => $id,
            'quantity' => $request->quantity,
            'stock_price' => $request->stock_price,
            'price' => $request->price
        ]);
    }

    public function createImage(Request $request) {
        if(isset($_REQUEST['icon_width']) && $_REQUEST['icon_width'] != '') {
            $width = $_REQUEST['icon_width'];
        }
        else {
            $width = '400';
        }
        
        if(isset($_REQUEST['icon_height']) && $_REQUEST['icon_height'] != '') {
            $height = $_REQUEST['icon_height'];
        }
        else {
            $height = '400';
        }
        
        
        if(isset($_REQUEST['price_text']) && $_REQUEST['price_text'] != '') {
            $price_text = $_REQUEST['price_text'];
        }
        else {
            $price_text = '';
        }
        
        if(isset($_REQUEST['label_text']) && $_REQUEST['label_text'] != '') {
            $label_text = $_REQUEST['label_text'];
        }
        else {
            $label_text = '';
        }
        
        if(isset($_REQUEST['price_location_x']) && $_REQUEST['price_location_x'] != '') {
            $price_location_x = $_REQUEST['price_location_x'];
        }
        else {
            $price_location_x = '';
        }
        
        
        if(isset($_REQUEST['price_location_y']) && $_REQUEST['price_location_y'] != '') {
            $price_location_y = $_REQUEST['price_location_y'];
        }
        else {
            $price_location_y = '';
        }
        
        
        if(isset($_REQUEST['label_location_x']) && $_REQUEST['label_location_x'] != '') {
            $label_location_x = $_REQUEST['label_location_x'];
        }
        else {
            $label_location_x = '';
        }
        
        
        if(isset($_REQUEST['label_location_y']) && $_REQUEST['label_location_y'] != '') {
            $label_location_y = $_REQUEST['label_location_y'];
        }
        else {
            $label_location_y = '';
        }
        
        
        if(isset($_REQUEST['price_font_size']) && $_REQUEST['price_font_size'] != '') {
            $price_font_size = $_REQUEST['price_font_size'];
        }
        else {
            $price_font_size = '10';
        }
        
        if(isset($_REQUEST['label_font_size']) && $_REQUEST['label_font_size'] != '') {
            $label_font_size = $_REQUEST['label_font_size'];
        }
        else {
            $label_font_size = '20';
        }
        
        if( ! empty($_FILES['bg_image'])) {
            //image uploadv
            $uploads_dir = "upload-bg/";

            // Check file size 500kb
            if ($_FILES['bg_image']['size'] > 500000*2) {
                // Too large
                echo json_encode(['success' => true, 'data' => 'Image larger than 1MB.']);
                return;
            }

            $check = getimagesize($_FILES["bg_image"]["tmp_name"]);
            if($check === false) {
                // Not Image
                echo json_encode(['success' => true, 'data' => 'Invalid Image.']);
                return;
            }

            $tmp_name = $_FILES['bg_image']['tmp_name'];
            // basename() may prevent filesystem traversal attacks;
            // further validation/sanitation of the filename may be appropriate
            $name = basename($_FILES["bg_image"]["name"]);

            move_uploaded_file($_FILES["bg_image"]["tmp_name"], $uploads_dir.$name);
            $target_file = $uploads_dir.$name;
        }
        else {
            $uploads_dir = "upload-bg/";
            $name = 'plain_bg_image.png';
            //for hex to rgb convert : Text Color 
            //list($label_color_r, $label_color_g, $label_color_b) = sscanf($label_color, "#%02x%02x%02x");
            list($bg_color_r, $bg_color_g, $bg_color_b) = sscanf($_REQUEST['bg_color'], "#%02x%02x%02x");
            
            // Create the image
            $bg_image = imagecreatetruecolor($width, $height);

            // Create some colors
            $white = imagecolorallocate($bg_image, 255, 255, 255);
            $grey = imagecolorallocate($bg_image, 128, 128, 128);
            $black = imagecolorallocate($bg_image, 0, 0, 0);
            imagefilledrectangle($bg_image, 0, 0, $width, $height, $white);


            $bg_text_color = imagecolorallocate($bg_image, $bg_color_r, $bg_color_g, $bg_color_b);
            $bg_color = imagecolorallocate($bg_image, $bg_color_r, $bg_color_g, $bg_color_b);

            imagefilledrectangle($bg_image, 0, 0, $width, $height, $bg_color);

            // The text to draw
            $bg_text = '';
            // Replace path by your own font path
            $font = public_path('proxima.ttf');
            $font_size = '12';
            // Add the text
            imagettftext($bg_image, $font_size, 0, 0, 0, $bg_text_color, $font, $bg_text);  
            imagepng($bg_image, $uploads_dir.'/'.$name);
            
            $target_file = $uploads_dir.'/'.$name;
        }

        $this->txt_to_img($_REQUEST['price_color'], $_REQUEST['price_bg_color'], $_REQUEST['label_color'], $_REQUEST['label_bg_color'], $width, $height, $label_text, $price_text, $price_location_x, $price_location_y, $label_location_x, $label_location_y, $target_file, $price_font_size, $label_font_size);
    }

    public function imagecreatefromfile($image_path) {
        list($width, $height, $image_type) = getimagesize($image_path);
        switch ($image_type)
        {
            case IMAGETYPE_GIF: return imagecreatefromgif($image_path); break;
            case IMAGETYPE_JPEG: return imagecreatefromjpeg($image_path); break;
            case IMAGETYPE_PNG: return imagecreatefrompng($image_path); break;
            default: return ''; break;
        }
    }

    public function txt_to_img($price_text_color, $price_bg_color, $label_color, $label_color_bg_color, $width, $height, $label_text, $price_text, $price_location_x, $price_location_y, $label_location_x, $label_location_y, $target_file, $price_font_size, $label_font_size){
        
        /*$t=time();
        echo($t . "<br>");
        echo(date("Y-m-d",$t));*/
        $t=time();
        
        //for hex to rgb convert
        list($price_color_r, $price_color_g, $price_color_b) = sscanf($price_text_color, "#%02x%02x%02x");
        list($price_bg_color_r, $price_bg_color_g, $price_bg_color_b) = sscanf($price_bg_color, "#%02x%02x%02x");
        
        
        //for hex to rgb convert : Text Color 
        list($label_color_r, $label_color_g, $label_color_b) = sscanf($label_color, "#%02x%02x%02x");
        list($label_color_bg_color_r, $label_color_bg_color_g, $label_color_bg_color_b) = sscanf($label_color_bg_color, "#%02x%02x%02x");
        
        //$font = 'arial.ttf';
        
        if($label_text == '') {
            
        }
        else {  
        
            $label_font_size = $label_font_size;
            $label_from_x = '10';
            $label_from_y = '25';
            $width_label = $width;
            $height_label = $label_font_size + 10;

            if($label_location_x == '' && $label_location_y == '') {

            }
            else {
                if($label_location_x == 'left' && $label_location_y == 'top') {
                    //bottom tag left bottom
                    $label_from_x = 5;
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = '0';

                }

                else if($label_location_x == 'left' && $label_location_y == 'bottom') {
                    //bottom tag left bottom

                    $label_from_x = 5;
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = $height- $height_label;
                }

                else if($label_location_x == 'left' && $label_location_y == 'middle') {
                    $label_from_x = 5;
                    $label_from_y = $label_font_size+3;


                    $watermark_pos_x = '0';
                    $watermark_pos_y = ($height/2)- ($height_label/2);
                }



                else if($label_location_x == 'right' && $label_location_y == 'top') {


                    $label_from_x = $width_label - ($label_font_size*strlen($label_text)/2)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = '0';
                }   

                else if($label_location_x == 'right' && $label_location_y == 'bottom') {
                    $label_from_x = $width_label - ($label_font_size*strlen($label_text)/2)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = $height- $height_label;
                }       

                else if($label_location_x == 'right' && $label_location_y == 'middle') {
                    $label_from_x = $width_label - ($label_font_size*strlen($label_text)/2)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = ($height/2)- ($height_label/2);
                }





                else if($label_location_x == 'center' && $label_location_y == 'top') {
                    $label_from_x = ($width_label/2) - ($label_font_size*strlen($label_text)/4)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = '0';
                }

                else if($label_location_x == 'center' && $label_location_y == 'bottom') {
                    $label_from_x = ($width_label/2) - ($label_font_size*strlen($label_text)/4)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = $height- $height_label;
                }

                else if($label_location_x == 'center' && $label_location_y == 'middle') {
                    $label_from_x = ($width_label/2) - ($label_font_size*strlen($label_text)/4)-5;          
                    $label_from_y = $label_font_size+3;

                    $watermark_pos_x = '0';
                    $watermark_pos_y = ($height/2)- ($height_label/2);
                } 
                else {

                }
            }






            // Create the image
            $im = imagecreatetruecolor($width, $height_label);

            // Create some colors
            $white = imagecolorallocate($im, 255, 255, 255);
            $grey = imagecolorallocate($im, 128, 128, 128);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, $width, $height_label, $white);


            $text_color = imagecolorallocate($im, $label_color_r, $label_color_g, $label_color_b);
            $label_color_bg_color = imagecolorallocate($im, $label_color_bg_color_r, $label_color_bg_color_g, $label_color_bg_color_b);

            imagefilledrectangle($im, 0, 0, $width, $height_label, $label_color_bg_color);

            // The text to draw
            $label_text = $label_text;
            // Replace path by your own font path
            $font = public_path('proxima.ttf');
                    //$font = $cwd . 'proxima.ttf';

            // Add the text
            imagettftext($im, $label_font_size, 0, $label_from_x, $label_from_y, $text_color, $font, $label_text);  
            imagepng($im, 'created-images/yourfile.png');
        
        
        }
        
        
        if($price_text == '') {
            
        }
        else {
            
        
            //creating price tag triangle starts
            //$png_image = imagecreatetruecolor(300, 300);

            //$width_price_tag = $width/2;
            //$height_price_tag = $height/2;
            
            
            $width_price_tag = $price_font_size+($price_font_size)*4;
            $height_price_tag = $price_font_size+($price_font_size)*4;
            $price_text_font = $price_font_size;

            if( ($price_location_x == 'left' && $price_location_y == 'top') || ($price_location_x == 'left' && $price_location_y == 'bottom')  || ($price_location_x == 'right' && $price_location_y == 'top')  || ($price_location_x == 'right' && $price_location_y == 'bottom'))  {
                $bg_type = 'polygon';
            }
            else {
                $bg_type = 'rectangle';
            }





            if($bg_type == 'polygon') {
                $png_image = imagecreatetruecolor($width_price_tag, $height_price_tag);

                imagesavealpha($png_image , true);

                $trans_colour = imagecolorallocatealpha($png_image , 0, 0, 0, 127);
                imagefill($png_image , 0, 0, $trans_colour);
            }
            else if($bg_type == 'rectangle') {

                $width_price_rectangle = $price_font_size+($price_font_size*strlen($price_text)/2);
                //$height_price_rectangle = ($price_text_font*strlen($price_text));
                
                //$width_label = $width;
                $height_price_rectangle = $price_font_size+10;
                //$price_text_font*strlen($price_text)
                // Create the image
                //$png_image = imagecreatetruecolor(400, 30);
                $png_image = imagecreatetruecolor($width_price_rectangle, $height_price_rectangle);

            }






            if($price_location_x == '' && $price_location_y == '') {

            }
            else {
                if($price_location_x == 'left' && $price_location_y == 'top') {
                    //bottom tag left bottom
                    $poly_points = array(0, 0, 0, $width_price_tag, $width_price_tag, 0);           

                    $watermark_price_pos_x = 0;
                    $watermark_price_pos_y = 0;

                    $price_text_loc_x = $width_price_tag/20;            
                    $price_text_loc_y = $height_price_tag/2;

                }

                else if($price_location_x == 'left' && $price_location_y == 'bottom') {
                    //bottom tag left bottom
                    $poly_points = array(0, 0, 0, $width_price_tag, $width_price_tag, $width_price_tag);

                    $watermark_price_pos_x = 0;
                    $watermark_price_pos_y = $height-$height_price_tag;

                    $price_text_loc_x = $width_price_tag/20;            
                    $price_text_loc_y = $height_price_tag/2+$price_font_size;
                }

                else if($price_location_x == 'left' && $price_location_y == 'middle') {
                    $watermark_price_pos_x = 0;
                    $watermark_price_pos_y = ($height/2)-($height_price_rectangle/2);
                    
                    $rectangle_text_pos_y = $price_font_size+5;
                }       


                else if($price_location_x == 'right' && $price_location_y == 'top') {
                    $poly_points = array($width_price_tag, 0, $width_price_tag, $width_price_tag, 0, 0);

                    $watermark_price_pos_x = $width-$width_price_tag;
                    $watermark_price_pos_y = 0;

                    $price_text_loc_x = $width_price_tag/2;         
                    $price_text_loc_y = $height_price_tag/2;
                }   

                else if($price_location_x == 'right' && $price_location_y == 'bottom') {
                    $poly_points = array($width_price_tag, 0, $width_price_tag, $width_price_tag, 0, $width_price_tag);
                    $watermark_price_pos_x = $width-$width_price_tag;
                    $watermark_price_pos_y = $height-$height_price_tag;                 

                    $price_text_loc_x = $width_price_tag - ($price_text_font*strlen($price_text)) +5;           
                    $price_text_loc_y = $height_price_tag/2+$price_font_size;
                }       

                else if($price_location_x == 'right' && $price_location_y == 'middle') {
                    $watermark_price_pos_x = $width-$width_price_rectangle;
                    $watermark_price_pos_y = ($height/2)-($height_price_rectangle/2);
                    
                    
                    $rectangle_text_pos_y = $price_font_size+5;
                }


                else if($price_location_x == 'center' && $price_location_y == 'top') {
                    $watermark_price_pos_x = ($width/2)-($width_price_rectangle/2);
                    $watermark_price_pos_y = 0;
                    
                    
                    $rectangle_text_pos_y = $price_font_size+5;
                }

                else if($price_location_x == 'center' && $price_location_y == 'bottom') {
                    $watermark_price_pos_x = ($width/2)-($width_price_rectangle/2);
                    $watermark_price_pos_y = $height-$height_price_rectangle;
                    
                    $rectangle_text_pos_y = $price_font_size+5;
                }

                else if($price_location_x == 'center' && $price_location_y == 'middle') {
                    $watermark_price_pos_x = ($width/2)-($width_price_rectangle/2);
                    $watermark_price_pos_y = ($height/2)-($height_price_rectangle/2);
                    
                    $rectangle_text_pos_y = $price_font_size+5;
                } 
                else {

                }
            }

            if($bg_type == 'polygon') {
                $price_bg_color_poly = imagecolorallocate($png_image, $price_bg_color_r, $price_bg_color_g, $price_bg_color_b);

                imagefilledpolygon ($png_image, $poly_points, 3, $price_bg_color_poly);      // POLYGON

                $price_color = imagecolorallocate($png_image, $price_color_r, $price_color_g, $price_color_b);
                // The text to draw
                $price_text = $price_text;
                // Replace path by your own font path
                $font = public_path('proxima.ttf');

                // Add the text
                //imagettftext($png_image, 10, 0, 20, 50, $label_color, $font, $price_text);
                imagettftext($png_image, $price_text_font, 0, $price_text_loc_x, $price_text_loc_y, $price_color, $font, $price_text);



            }
            else if($bg_type == 'rectangle') {
                $price_bg_color_poly = imagecolorallocate($png_image, $price_bg_color_r, $price_bg_color_g, $price_bg_color_b);

                imagefilledrectangle($png_image, 0, 0, $width_price_rectangle, $height_price_rectangle, $price_bg_color_poly);

                $price_color = imagecolorallocate($png_image, $price_color_r, $price_color_g, $price_color_b);


                // The text to draw
                $price_text = $price_text;
                // Replace path by your own font path
                $font = public_path('proxima.ttf');

                // Add the text
                imagettftext($png_image, $price_font_size, 0, 0, $rectangle_text_pos_y, $price_color , $font, $price_text); 
            }

            imagepng($png_image, 'created-images/yourfile-price.png');

            //creating price tag triangle ends
        }
        
        
        
        
        
        //creating watermark image with label tag starts    
        //$filename = 'sample1.jpg';
        $filename = $target_file;

        // load source image to memory
        $image1 = $this->imagecreatefromfile($filename);
        if (!$image1) die('Unable to open image');  
        
        if($label_text == '') {
            // load watermark to memory
            $watermark = $this->imagecreatefromfile($filename);
            if (!$image1) die('Unable to open watermark');
            
            $watermark_pos_x = 0;
            $watermark_pos_y = 0;
        }
        else {      
            // load watermark to memory
            $watermark = $this->imagecreatefromfile('created-images/yourfile.png');
            if (!$image1) die('Unable to open watermark');  
        }   
        

        // Get new sizes
        list($width1, $height1) = getimagesize($filename);
        $newwidth = $width;
        $newheight = $height;


        // Load
        $image = imagecreatetruecolor($newwidth, $newheight);
        //$source = imagecreatefromjpeg($filename);

        // Resize
        imagecopyresized($image, $image1, 0, 0, 0, 0, $newwidth, $newheight, $width1, $height1);

        // merge the source image and the watermark
        imagecopy($image, $watermark,  $watermark_pos_x, $watermark_pos_y, 0, 0, imagesx($watermark), imagesy($watermark));

        imagepng($image, 'created-images/sample1-water.png', 9);
        //creating watermark image with label tag ends  
        
        
        
        
        
        //creating final image ends 
        $final_image = 'created-images/yourfile-final-'.$t.'.png';
        $final_image_filename = 'created-images/yourfile-final-'.$t.'.png';
        // load source image to memory
        $image_water = $this->imagecreatefromfile('created-images/sample1-water.png');
        if (!$image_water) die('Unable to open image');
        
        
        
        if($price_text == '') {
            // load pricetag to memory
            $watermark_pricetag = $this->imagecreatefromfile('created-images/sample1-water.png');
            if (!$image_water) die('Unable to open watermark');
            
            $watermark_price_pos_x = 0;
            $watermark_price_pos_y = 0;
            
        }
        else {
            // load pricetag to memory
            $watermark_pricetag = $this->imagecreatefromfile('created-images/yourfile-price.png');
            if (!$image_water) die('Unable to open watermark');
        }
        
        
        /*$watermark_price_pos_x = 0;
        $watermark_price_pos_y = 0;*/

        // merge the source image and the watermark
        imagecopy($image_water, $watermark_pricetag,  $watermark_price_pos_x, $watermark_price_pos_y, 0, 0, imagesx($watermark_pricetag), imagesy($watermark_pricetag));
        
        
        // output watermarked image to browser
        //header('Content-Type: image/png');
        imagepng($image_water, $final_image, 9);  // use best image quality (100)
        echo json_encode(['success' => true, 'data' => base64_encode(file_get_contents($final_image))]);
        //echo(base64_encode(file_get_contents($final_image)));

        /*return response()->json(['success' => true, 
            'data' => base64_encode(file_get_contents($final_image))]);*/
        
    /*  ?>
        <img src="<?php echo $final_image; ?>">
        <?php*/

        // remove the images from memory
        imagedestroy($image_water);
        imagedestroy($watermark_pricetag);
        
        unlink($final_image_filename);
        unlink($target_file);
    }
}
