<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Product;
use App\Model\Tag;
use App\Model\Review;
use App\Model\Attribute;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Model\Store;
use App\Model\Branch;
use App\Model\BranchProductInfo;
use App\Model\ConversationService;
use App\Model\OrderDetail;

class ProductController extends Controller
{
    public function resize_images(Request $request)
    {
        ///$products = Product::where('id', '<', 1000)->get();
        $products = Product::whereBetween('id', [6409, 6464])->get();
       
        foreach($products as $product){
            $image_name = $product->image;
            $path = asset('storage/app/public/product');
            $image_path = $path.'/'.$image_name;
            
            // Open the image using Intervention Image
            $image = Image::make($image_path);

            // Resize the image to 40x40 pixels
            $image->resize(200, 200);
            $thumb_img_stream = $image->stream();
           
            Storage::disk('public')->put('product-thumbnail/' . $image_name, $thumb_img_stream);

            // You can update the product's image field with the new thumbnail path if needed
            $product->thumbnail = $image_name;
            $product->save();
         }  
          return 'success';
    }
    
    public function variant_combination(Request $request)
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name'))->render(),
        ]);
    }


    public function get_categories(Request $request)
    {
        $parent_ids = explode(",", $request->parent_id);
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach($parent_ids as $parent_id){
            $cat = Category::where(['parent_id' => $parent_id])->get();
            foreach ($cat as $row) {
                if ($row->id == $request->sub_category) {
                    $res .= '<option value="' .$row->id . '" selected >' . $row->name . '</option>';
                } else {
                    $res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
                }
            }
        }
       
        return response()->json([
            'options' => $res,
        ]);
    }

    public function index()
    {
        $categories = Category::where(['position' => 0])->get();
        return view('admin-views.product.index', compact('categories'));
    }

   public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        
        $words = explode(" ", $search);
        
        $words_2 = [];
        for ($i = 0; $i < count($words); $i++) {
        $word_2 = strtolower($words[$i]);
        if($i != 0){
           array_push($words_2, $word_2); 
         }
        }
        //// Main Search Here
        $words_array = [];
        for ($i = 0; $i < count($words); $i++) {
        $word_2 = strtolower($words[$i]);
        array_push($words_array, $word_2); 
        }
        $products = Product::get(); 
        
        $products_array = [];
        $product_ids_1=[];
        foreach($products as $product){
            $product_words_array = [];
            $product_points = 0;
            $name_words =  explode(" ", $product->name);
            for ($i = 0; $i < count($name_words); $i++) {
               $product_word_lowercase = strtolower($name_words[$i]);
               array_push($product_words_array, $product_word_lowercase); 
              }
              foreach($words_array as $w){
                if(in_array($w, $product_words_array)){
                    $product_points++;
                }  
              }
              $product['search_points'] = $product_points;
              
              array_push($products_array, $product);
            }

             $pr_array = collect($products_array)->sortBy('search_points')->reverse()->toArray();
            
             foreach($pr_array as $ar){
                 array_push($product_ids_1, (int)$ar['id']);
             }

        $two_words_cates_ids = [];
       //  strtolower($words[0]);
        
         
        $product_ids= [];
        $products = Product::get();
        
        foreach(Category::get() as $category){

            if($category->name == $search){
                foreach ($products as $product) {
                 foreach (json_decode($product['category_ids'], true) as $cate) {
                    if($cate['id'] == $category->id){
                        array_push($product_ids, $product->id);
                    }
                }
              }
            }elseif(isset($words[0]) && isset($words[1])){
               if($category->name == $words[0] || $category->name == $words[1]){
                array_push($two_words_cates_ids, $category->id);
             } 
            } 
          }
          
          foreach ($products as $product) {
              $prd_cates_ids = json_decode($product['category_ids'], true);
              $words_cates_ids = [];
              foreach($prd_cates_ids as $cate){
                  array_push($words_cates_ids, $cate['id']);
              }
              if(isset($two_words_cates_ids[0]) && isset($two_words_cates_ids[1])){
                 if(in_array($two_words_cates_ids[0], $words_cates_ids) && in_array($two_words_cates_ids[1], $words_cates_ids)){
                   array_push($product_ids, $product->id);
                 } 
              }
             }

        // if(count($product_ids_1) > 0){
        //       $query = Product::whereIn('id', $product_ids_1)->latest();
        //       $query_param = ['search' => $request['search']];
              
        //   }  else
          
      if(count($product_ids) > 0){
              $query = Product::whereIn('id', $product_ids)->latest();
              $query_param = ['search' => $request['search']];
          } 
        else if(Product::where('name', $search)->first()){
            $query = Product::where('name', $search)->latest();
            $query_param = ['search' => $request['search']];
        }
        
        else{
         if ($request->has('search')) {
            
            $key = explode(' ', $request['search']);
            $query = Product::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $query_param = ['search' => $request['search']];
        } else {
            $query = Product::latest();
         }   
        }
        
        $products = $query->paginate(10)->appends($query_param);
        return view('admin-views.product.list', compact('products', 'search'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $products=Product::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('admin-views.product.partials._table',compact('products'))->render()
        ]);
    }

    public function view($id)
    {
        $product = Product::where(['id' => $id])->first();
        $reviews=Review::where(['product_id'=>$id])->latest()->paginate(20);
        return view('admin-views.product.view', compact('product','reviews'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products',
            'category_id' => 'required',
            'image' => 'required',
            'price' => 'required|numeric|min:1',
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('product')) {
                Storage::disk('public')->makeDirectory('product');
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('product/' . $image_name, $note_img);
            
            $thumb_image = Image::make($request->file('image'));
            $thumb_image->resize(200, 200);
            $thumb_img_stream = $thumb_image->stream();
            Storage::disk('public')->put('product-thumbnail/' . $image_name, $thumb_img_stream);
        } else {
            $image_name = 'def.png';
        }

        $p = new Product;
        $p->name = $request->name;
      
        $category = [];
        if ($request->category_id != null) {
            foreach($request->category_id as $category_id){
                if ($category_id != null) {
                    array_push($category, [
                        'id' => $category_id,
                        'position' => 1,
                    ]);
                }
            }
        }
        if ($request->sub_category_id != null) {
            foreach($request->sub_category_id as $sub_category_id){
                if ($sub_category_id != null) {
                    array_push($category, [
                        'id' => $sub_category_id,
                        'position' => 2,
                    ]);
                }
            }
        }
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $p->category_ids = json_encode($category);
        $p->description = $request->description;

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $p->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                
                if (!empty($request->file('image_' . str_replace('.', '_', $str)))) {
                    $combination_image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
                    if (!Storage::disk('public')->exists('product')) {
                        Storage::disk('public')->makeDirectory('product');
                    }
             
                    $note_img = Image::make($request->file('image_' . str_replace('.', '_', $str)))->stream();
                    Storage::disk('public')->put('product/' . $combination_image_name, $note_img);
                } 
                else $combination_image_name = null;
                
                $item['image'] = $combination_image_name;
                
                array_push($variations, $item);
            }
        }
        //combinations end
        $p->variations = json_encode($variations);
        $p->price = $request->price;
        $p->set_menu = $request->item_type;
        $p->featured = $request->featured;
        $p->image = $image_name;
        $p->thumbnail = $image_name;
        $p->available_time_starts = '10:30:00';
        $p->available_time_ends = '19:30:00';
        $p->availability = $request->availability;

    //    $p->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
      //  $p->tax_type = $request->tax_type;
         $p->tax = $request->tax_type == 'amount';
         $p->tax_type = '0';

        $p->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $p->discount_type = $request->discount_type;

        $p->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);

        $p->save();

        return response()->json([], 200);
    }

    public function edit($id)
    {
        $product = Product::find($id);
        if(isset($product->category_ids)){
            $product_category = json_decode($product->category_ids);
        }else {
            $product_category = [];
        }
      

        
        $tag_se = [];
        $store_se = [];
        $cate_se = [];
        $sub_cate_se = [];
        
        foreach ($product_category as $cate){
            if($cate->position == 1){
                $cate_se[] = $cate->id;
            }
            if($cate->position == 2){
                $sub_cate_se[] = $cate->id;
            }
        }
     
        
        
        //var_dump($cate_se);exit;
        $categories = Category::where(['parent_id' => 0])->get();

        $sub_categories = Category::whereIn('parent_id', $cate_se)->get();
        return view('admin-views.product.edit', compact('product', 'product_category', 'categories', 'sub_categories',  'cate_se', 'sub_cate_se',));
    }

    public function status(Request $request)
    {
        $product = Product::find($request->id);
        $product->status = $request->status;
        $product->save();
        Toastr::success('Product status updated!');
        return back();
    }
    
    

    public function update(Request $request, $id)
    {
     
        //$category_id= $request->has('category_id') ? json_encode($request->category_id) : json_encode([]);
        //$sub_category_id= $request->has('sub_category_id') ? json_encode($request->sub_category_id) : json_encode([]);
        //var_dump($category_id);var_dump($sub_category_id);exit;
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric|min:1',
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $dis = ($request['price'] / 100) * $request['discount'];
        } else {
            $dis = $request['discount'];
        }

        if ($request['price'] <= $dis) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        if ($request['price'] <= $dis || $validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $p = Product::find($id);

        if (!empty($request->file('image'))) {
            $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
            if (!Storage::disk('public')->exists('product')) {
                Storage::disk('public')->makeDirectory('product');
            }
            if (Storage::disk('public')->exists('product/' . $p['image'])) {
                Storage::disk('public')->delete('product/' . $p['image']);
            }
            $note_img = Image::make($request->file('image'))->stream();
            Storage::disk('public')->put('product/' . $image_name, $note_img);
            
            $thumb_image = Image::make($request->file('image'));
            $thumb_image->resize(200, 200);
            $thumb_img_stream = $thumb_image->stream();
            Storage::disk('public')->put('product-thumbnail/' . $image_name, $thumb_img_stream);
        } else {
            $image_name = $p->image;
        }

        $p->name = $request->name;
        
        $category = [];
        if ($request->category_id != null) {
            foreach($request->category_id as $category_id){
                if ($category_id != null) {
                    array_push($category, [
                        'id' => $category_id,
                        'position' => 1,
                    ]);
                }
            }
        }
        if ($request->sub_category_id != null) {
            foreach($request->sub_category_id as $sub_category_id){
                if ($sub_category_id != null) {
                    array_push($category, [
                        'id' => $sub_category_id,
                        'position' => 2,
                    ]);
                }
            }
        }

        /*if ($request->category_id != null) {
            array_push($category, [
                'id' => $request->category_id,
                'position' => 1,
            ]);
        }
        if ($request->sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_category_id,
                'position' => 2,
            ]);
        }*/
        if ($request->sub_sub_category_id != null) {
            array_push($category, [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ]);
        }

        $p->category_ids = json_encode($category);
        $p->description = $request->description;
        $p->availability = $request->availability;

        $choice_options = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                array_push($choice_options, $item);
            }
        }
        $p->choice_options = json_encode($choice_options);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                array_push($options, explode(',', $my_str));
            }
        }
        //Generates the combinations of customer choice options
        
        
        $combinations = Helpers::combinations($options);
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                
                if (!empty($request->file('image_' . str_replace('.', '_', $str)))) {
                    $combination_image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
                    if (!Storage::disk('public')->exists('product')) {
                        Storage::disk('public')->makeDirectory('product');
                    }
             
                    $note_img = Image::make($request->file('image_' . str_replace('.', '_', $str)))->stream();
                    Storage::disk('public')->put('product/' . $combination_image_name, $note_img);
                } 
                else $combination_image_name = isset($request['old_image_' . str_replace('.', '_', $str)]) ? $request['old_image_' . str_replace('.', '_', $str)] : null ;
                
                $item['image'] = $combination_image_name;
        
                array_push($variations, $item);
            }
        }
     //   $collection = collect(['region' => $request->price, 'price' => 'Desk']);
        
        
        //combinations end
        
        
        $old_variations = json_decode($p->variations);
        if(count($old_variations)){
            foreach ($old_variations as $key => $old_variation) {
                if (count($variations)) {
                    $old_variation_type = $old_variation->type;
                    $hasFound = collect($variations)->first(function($variation) use($old_variation_type){
                        return $variation['type'] == $old_variation_type;
                    });

                    // $hasFound = collect($variations)->contains(function ($variation, $key) use($old_variation_type){
                    //     return $variation['type'] == $old_variation_type;
                    // });
                    
                    if(!$hasFound && isset($old_variation->image)){
                        if (Storage::disk('public')->exists('product/' . $old_variation->image)) {
                            Storage::disk('public')->delete('product/' . $old_variation->image);
                        }
                    }
                }
                else if(isset($old_variation->image)){
                    if (Storage::disk('public')->exists('product/' . $old_variation->image)) {
                        Storage::disk('public')->delete('product/' . $old_variation->image);
                    }
                }
            }
        }
        
        
        $p->variations = json_encode($variations);
        $p->price = $request->price;
        $p->set_menu = $request->item_type;
        $p->featured = $request->featured;
        $p->image = $image_name;
        $p->thumbnail = $image_name;
        $p->available_time_starts = '10:30:00';
        $p->available_time_ends = '19:30:00';

        // $p->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        // $p->tax_type = $request->tax_type;
        $p->tax = $request->tax_type == 'amount';
        $p->tax_type = '0';

        $p->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $p->discount_type = $request->discount_type;

        $p->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $p->status = $request->status;
        
        $p->save();

        return response()->json([], 200);
    }
    
    public function delete_product($id)
    {
        
        $product = Product::find($id);

        if (!$product) 
        {
            return back()->withErrors(['Product not found']);
        }

        $branches = $product->branches();

        foreach ($branches as $branch) 
        {
            $branch->delete();
        }

        $product->delete();
        Toastr::success('Product removed!');

        return back();
    }

    public function view_attributes_images($id)
    {   
        
        $product = Product::where(['id' => $id])->first();
        $variations = json_decode($product->variations, true);
        $attributes_ids = json_decode($product->attributes, true);
        $attributes = Attribute::whereIn('id',$attributes_ids)->get();
        return view('admin-views.product.attributes-images', compact('product', 'variations', 'attributes'));
    }
    
    public function update_attributes_images(Request $request, $id)
    {

        $product = Product::where('id', $id)->first();
        $variations = json_decode($product->variations, true);
       
       $arr = []; 
        foreach($variations as $key=>$variation){
            $variation['price'] = $request->input('price_'.$key);
            if (!empty($request->file('image_' . $key))) {
                $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
                if (Storage::disk('public')->exists('product/' . $request->file('image_' . $key))) {
                Storage::disk('public')->delete('product/' . $request->file('image_' . $key));
            }
            $note_img = Image::make($request->file('image_' . $key))->stream();
            Storage::disk('public')->put('product/' . $image_name, $note_img);
            $variation['image'] = $image_name;
            
            }
            
            array_push($arr, $variation);
            
        }
        $product->variations = json_encode($arr);
        $product->save();
        return redirect('admin/product/edit/'.$id);
      //  return redirect()->back();
    }
    
    public function delete_attribute(Request $request, $key_id, $product_id)
    {
        
        $product = Product::find($product_id);
        $variations = json_decode($product->variations, true);
        
        $keys_arr = [];
        foreach($variations as $key=>$variation){
            if($key != $key_id){
                array_push($keys_arr, $variation);
            }else {
                $image = $variation['image'];
            }
        }

      if (Storage::disk('public')->exists('product/' . $image)) {
            Storage::disk('public')->delete('product/' . $image);
        }
        $product->variations = json_encode($keys_arr);
        $product->save();
        Toastr::success('Attribute removed!');
        return redirect()->back();
     //   return back();
    }
    
    public function store_attribute(Request $request)
    {
        
        $product = Product::find($request->product_id);
        $variations = json_decode($product->variations, true);
        $attributes = json_decode($product->attributes, true);
        $choice_options = json_decode($product->choice_options, true);
        

        $in_array = [];
        $not_in_array = [];
        foreach($choice_options as $key=> $choice_option){
        $choice_option_arr = $choice_option['options'];
 
            if(in_array($request->input('type_'.$key), $choice_option_arr)){
               // return 'in array';
               array_push($in_array, $request->input('type_'.$key));
            } else {
                array_push($choice_option_arr, $request->input('type_'.$key));
                $choice_options[$key]['options'] = $choice_option_arr;
                array_push($not_in_array, $request->input('type_'.$key));
               // return 'not in array';
            }
        }

        if (!empty($request->file('new_image'))) {
                $image_name = Carbon::now()->toDateString() . "-" . uniqid() . "." . 'png';
                if (Storage::disk('public')->exists('product/' . $request->file('new_image'))) {
                Storage::disk('public')->delete('product/' . $request->file('new_image'));
            }
            $note_img = Image::make($request->file('new_image'))->stream();
            Storage::disk('public')->put('product/' . $image_name, $note_img);
            $variation['image'] = $image_name;
            }
        
        $str = '';
        foreach($attributes as $key=> $attribute){

            if ($key > 0) {
                        $str .= '-' . str_replace(' ', '', $request->input('type_'.$key));
                    } else {
                        $str .= str_replace(' ', '', $request->input('type_'.$key));
                    }
        }
     
        
        $new_variation['type'] = $str;
        $new_variation['price'] = $request->price;
        $new_variation['image'] = $image_name;

        
        array_push($variations, $new_variation);
  
        $product->variations = json_encode($variations);
        $product->choice_options = json_encode($choice_options);
        $product->save();
        Toastr::success('Attribute Added!');
        return redirect()->back();
     //   return back();
    }
    
    public function delete_order(Request $request)
    {
        $product = Product::find($request->id);
        if (Storage::disk('public')->exists('product/' . $product['image'])) {
            Storage::disk('public')->delete('product/' . $product['image']);
        }
        $product->delete();
        Toastr::success('Product removed!');
        return back();
    }
public function bulk_import_index()
    {
        return view('admin-views.product.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        $data = [];
        foreach ($collections as $collection) {
            

            array_push($data, [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => 'def.png',
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'add_ons' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => $collection['category_ids'], 'position' => 0]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'set_menu' => $collection['set_menu'],
            ]);
        }
        DB::table('products')->insert($data);
        Toastr::success(count($data) . ' - Products imported successfully!');
        return back();
    }

    public function bulk_export_data()
    {
        $products = Product::get();
        return (new FastExcel($products))->download('products.xlsx');
    }
    
    
    public function addProductQaList(Request $request){
        return view('admin-views.product.product-qa.create');
    }

   public function hairRoutine(Request $request){
   return view('admin-views.product.product-qa.hairRoutine');
   }
    
   public function branches_info($id){
       $product = Product::where('id', $id)->first();
       $branch_ids = json_decode($product->branch_ids, true);
       if($branch_ids !=null || $branch_ids != []){
           return view('admin-views.product.branches-info.edit', compact('branch_ids', 'id', 'product'));
       }else {
         Toastr::error('Please add branches first.');
         return back(); 
       }
   }
   
   public function save_info(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        $variations = json_decode($product->variations, true);
        $codes_arr = [];
        DB::table('branch_product_info')->where('product_id', $request->product_id)->delete();

        $branch_ids = json_decode($product->branch_ids, true);

        foreach ($branch_ids as $branch_id) {

            if ($variations == [] || $variations == null) {

                $branch_product_info = new BranchProductInfo();
                $branch_product_info->product_id = $request->product_id;
                $branch_product_info->branch_id = $branch_id;
                $branch_product_info->product_code = $request->input('product_code-' . $branch_id);
                $branch_product_info->price = $request->input('price-' . $branch_id);
                $branch_product_info->discount = $request->input('discount-' . $branch_id);
                $branch_product_info->quantity = $request->input('quantity-' . $branch_id);
                $branch_product_info->save();
            } else {

                foreach ($variations as $variation) {
                    if (strpos($variation['type'], '.') !== false) {
                        $variationType = str_replace('.', '_', $variation['type']);
                    } else {
                        $variationType = $variation['type'];
                    }
                    $productCode = $request->input("product_code-{$branch_id}-{$variationType}");
                    $productCode_2 = ("product_code-{$branch_id}-{$variationType}");

                    if ($productCode !== null) {
                        $branch_product_info = BranchProductInfo::create([
                            'product_id' => $request->product_id,
                            'branch_id' => $branch_id,
                            'product_code' => $productCode,
                            'price' => $request->input("price-{$branch_id}-{$variationType}"),
                            'discount' => $request->input("discount-{$branch_id}-{$variationType}"),
                            'quantity' => $request->input("quantity-{$branch_id}-{$variationType}"),
                            'variation_type' => $variation['type'],
                        ]);
                    }
                }
            }
        }

        Toastr::success('Updated successfully..');
        return back();
    }
}

