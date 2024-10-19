<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Model\Product;
use App\Model\SearchedItem;
use App\Model\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Model\Category;

use App\Model\Brand;
use App\User;
use App\Model\Branch;

class ProductController extends Controller
{

    public function get_latest_products(Request $request)
    {
        if (isset($_GET['customer_id'])) {
            $customer_id = $_GET['customer_id'];
        } else {
            $customer_id = 0;
        }
       
        // $paginator = Product::active()->with(['rating', 'reviews'])->orderBy("order","desc")->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
         
         $paginator = Product::active()->with(['rating', 'merchants' => function($query) {$query->limit(10); }])->orderBy("order","desc")->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
         $products  = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'products' => $paginator->items()
        ];
       
       
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function gift_product(Request $request)
    {
        $gift_product = Product::where('id', $request->gift_id)->first();
        $gift_product['variations'] = json_decode($gift_product['variations']);
        $gift_product['add_ons'] = json_decode($gift_product['add_ons']);
        $gift_product['attributes'] = json_decode($gift_product['attributes']);
        $gift_product['category_ids'] = json_decode($gift_product['category_ids']);
        $gift_product['choice_options'] = json_decode($gift_product['choice_options']);
        return response()->json($gift_product, 200);
    }

    public function get_searched_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        
        // if($request->user_id != 0){
        //     $user = User::where('id', $request->user_id)->first();
        //     $nearby_branch_id = Helpers::nearby_branch_id($user->id); // branches that have products list in our system
        // }else {
        //    $nearby_branch_id = 3; 
        // }
      
        $products = ProductLogic::search_products($request['name'], '50', $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true,[]);
        return response()->json($products, 200);
    }

    public function get_product($id)
    {
        try {
            $product = ProductLogic::get_product($id);
            $product = Helpers::product_data_formatting($product, false);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => 'Product not found!']
            ], 404);
        }
    }

    //Jerushan
    public function get_product_details($id)
    {
        try {
            $product = ProductLogic::get_product_details($id);
            $product = Helpers::product_data_formatting($product, false);
            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => 'Product not found!']
            ], 404);
        }
    }

    public function get_related_products($id)
    {
        if (isset($_GET['customer_id'])) {
            $customer_id = $_GET['customer_id'];
        } else {
            $customer_id = 0;
        }
        if (Product::find($id)) {
            $products = ProductLogic::get_related_products($id, $customer_id, 1);
            $tagsToMatch = [];
            if (isset($products['tags_to_match'])) {
                $tagsToMatch = $products['tags_to_match'];
            }
            $products = Helpers::product_data_formatting($products['products'], true, $tagsToMatch);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' => 'Product not found!']
        ], 404);
    }

    public function get_set_menus(Request $request)
    {
        try {
            $products = Helpers::product_data_formatting(Product::active()->with(['rating'])->where(['set_menu' => 1, 'status' => 1])->orderBy("order", "DESC")->get(), true);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'product-001', 'message' => 'Set menu not found!']
            ], 404);
        }
    }

    public function get_best_sellers(Request $request)
    {
        $user = $request->user();

     //   $nearby_branch_id = Helpers::nearby_branch_id($user->id); // branches that have products list in our system

        $products = Product::active()
            ->with('rating')
            ->where(['featured' => 1, 'status' => 1])
            ->orderBy("order", "DESC")
            ->get();

        $products_final = Helpers::product_data_formatting($products, true, []);
        return response()->json($products_final, 200);
    
    }

    public function get_product_reviews($id)
    {
        $reviews = Review::with(['customer'])->where(['product_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }

    public function get_product_rating($id)
    {
        try {
            $product = Product::find($id);
            $overallRating = ProductLogic::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function submit_product_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
        ]);

        $product = Product::find($request->product_id);
        if (isset($product) == false) {
            $validator->errors()->add('product_id', 'There is no such product');
        }

        $multi_review = Review::where(['product_id' => $request->product_id, 'user_id' => $request->user()->id])->first();
        if (isset($multi_review)) {
            $review = $multi_review;
        } else {
            $review = new Review;
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $image_array = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->file('attachment') as $image) {
                if ($image != null) {
                    if (!Storage::disk('public')->exists('review')) {
                        Storage::disk('public')->makeDirectory('review');
                    }
                    array_push($image_array, Storage::disk('public')->put('review', $image));
                }
            }
        }

        $review->user_id = $request->user()->id;
        $review->product_id = $request->product_id;
        $review->order_id = $request->order_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($image_array);
        $review->save();

        return response()->json(['message' => 'successfully review submitted!'], 200);
    }

    public function search_products(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'per_page' => 'required',
            'min_price' => 'nullable|int',
            'max_price' => 'nullable|int',
            'category_id' => 'nullable|int',
            'rating' => 'nullable|int',
        ]);

        if ($validator->fails()) return response()->json(['errors' => Helpers::error_processor($validator)], 403);

        // $whereMinPrice = (value($request->min_price)) ? 'price > "'.$request->min_price.'"' : 'price <> ""';
        // $whereMaxPrice = (value($request->max_price)) ? 'price < "'.$request->max_price.'"' : 'price <> ""';



        if (value($request->rating)) {
            $products = Product::active()->with(["rating"])
                ->whereHas("rating", function ($q) use ($request) {
                    if (value($request->rating)) $q->where('rating', '>=', $request->rating);
                    else $q->whereRaw('rating <> ""');
                })
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->name}%");
                })
                // ->whereRaw($whereMinPrice)
                // ->whereRaw($whereMaxPrice)
                ->get();
        } else {
            $products = Product::active()->with(["rating"])
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->name}%");
                })
                // ->whereRaw($whereMinPrice)
                // ->whereRaw($whereMaxPrice)
                ->get();
        }

        if (value($request->min_price)) {
            $products = $products->reject(function ($product) use ($request) {
                $discountPrice = ($product->price - (($product->discount / 100) * $product->price));
                return ($discountPrice <= $request->min_price);
            })->values()->all();

            $products = collect($products);
        }

        if (value($request->max_price)) {
            $products = $products->reject(function ($product) use ($request) {
                $discountPrice = ($product->price - (($product->discount / 100) * $product->price));
                return ($discountPrice >= $request->max_price);
            })->values()->all();
        }

        if (value($request->category_id)) {
            $categoryProducts = [];
            foreach ($products as $product) {
                foreach (json_decode($product['category_ids'], true) as $category) {
                    if (isset($category['id']) && $category['id'] == $request->category_id) {
                        array_push($categoryProducts, $product);
                    }
                }
            }
        } else $categoryProducts = $products;

        $collection = collect($categoryProducts);
        $perPage = $request->per_page;
        $page = $request->page ? $request->page : 0;

        $paginate = new \Illuminate\Pagination\LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => url('api/v1/products/searchProduct')]
        );

        return response()->json($paginate, 200);
    }


    public function filtered_routine_products(Request $request)
    {

        //  $type = $request->type;
        $problem = 'Damaged Hair';
        $service_id = $request->service_id;

        $main_categories = [];
        $secondry_categories = [];

        //  $type_category = Category::where('name', $type)->first();

        //  array_push($main_categories, $type_category->id);

        $problem_category = Category::where('name', $problem)->first();
        array_push($main_categories, $problem_category->id);



        $products = Product::where('status', 1)->get();

        $categoryProducts = [];


        /// MAIN CATES
        foreach ($products as $product) {
            $prCategoryIds = [];
            foreach (json_decode($product['category_ids'], true) as $category) {
                array_push($prCategoryIds, $category['id']);
            }

            if (in_array($main_categories[0], $prCategoryIds)) {

                array_push($categoryProducts, $product);
            }

            // if(in_array($main_categories[0], $prCategoryIds) && in_array($main_categories[1], $prCategoryIds)){
            //     json_decode($product['variations'], true);
            //     $product['variations'] = json_decode($product['variations'], true);
            //     $product['attributes'] = json_decode($product['attributes'], true);
            //     $product['category_ids'] = json_decode($product['category_ids'], true);
            //     $product['choice_options'] = json_decode($product['choice_options'], true);
            //     array_push($categoryProducts, $product);
            //     }
        }
        return $categoryProducts;


        $output['total_size'] = 20;
        $output['limit'] = '20';
        $output['offset'] = '1';
        $output['products'] = array_slice($categoryProducts, 0, 20);

        return response()->json($output, 200);
    }

    public function get_brands(Request $request)
    {
        $brands = Brand::where('status', 1)->get();
        return response()->json($brands, 200);
    }

    public function brand_categories(Request $request)
    {
        $category_ids = Product::where(['status' => 1, 'brand_id' => $request->brand_id])->pluck('category_ids');

        $category_ids_arr = [];
        foreach ($category_ids as $category_id) {
            $categories = json_decode($category_id, true);
            foreach ($categories as $c) {
                array_push($category_ids_arr, (int)$c['id']);
            }
        }
        $categories = Category::whereIn('id', $category_ids_arr)->get();
        return response()->json($categories, 200);
    }

    public function get_brand_products(Request $request)
    {
        if($request->user_id != 0){
            $user = User::where('id', $request->user_id)->first();
            $nearby_branch_id = Helpers::nearby_branch_id($user->id); // branches that have products list in our system
        }else {
           $nearby_branch_id = 3; // El Zomor 
        }
        
        if ($request->category_id == 0) {
            $paginator = Product::where(['status' => 1, 'brand_id' => $request->brand_id])
            ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
                ->paginate(10, ['*'], 'page', $request['offset']);

            $products = $paginator->items();
            $products_2 = Helpers::product_data_formatting($products, true, [], $nearby_branch_id);
            $paginator = [
                'total_size' => $paginator->total(),
                'limit' => 10,
                'offset' => $request['offset'], // page No
                'products' => $products_2

            ];
            return $paginator;
        } else {

            $category_ids = Category::where('id', $request->category_id)->pluck('id');
            $products = Product::where(['status' => 1, 'brand_id' => $request->brand_id])
            ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
            ->get();

            $products_arr = [];
            foreach ($products as $product) {
                $product_category_ids = json_decode($product->category_ids, true);
                foreach ($product_category_ids as $cate) {
                    if ($cate['id'] == $request->category_id) {
                        array_push($products_arr, $product);
                    }
                }
            }
            $total = count($products_arr);
            $perPage = $request['limit']; // How many items do you want to display.
            $currentPage = $request['offset'] - 1; // T

            $start = $currentPage * 10;
            $end = ($currentPage * 10) + 10;

            $newArr = array_splice($products_arr, $start, $end);
            $products_2 = Helpers::product_data_formatting($newArr, true, [], $nearby_branch_id);

            $paginator = [
                'total_size' => $total,
                'limit' => $request['limit'],
                'offset' => $request['offset'], // page No
                'products' => $products_2

            ];
        }

        return $paginator;
    }
    
    public function get_suggested_products(Request $request)
    {
        if($request->user_id != 0){
            $user = User::where('id', $request->user_id)->first();
            $nearby_branch_id = Helpers::nearby_branch_id($user->id); // branches that have products list in our system
        }else {
           $nearby_branch_id = 3; // El Zomor 
        }
        
        $product = Product::find($request->product_id);
        $category_ids = json_decode($product->category_ids, true);
       
        $category_ids_array = [];
        foreach($category_ids as $id){
          if($id['position'] == 2){
             array_push($category_ids_array, (int)$id['id']);
          }
        }
        
        $products = Product::whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')->get();
        $matched_product_ids = [];

        foreach($products as $product){
            $category_ids = json_decode($product->category_ids, true);
            $pr_category_ids_array = [];
        foreach($category_ids as $id){
          array_push($pr_category_ids_array, (int)$id['id']);
        }
        $containsValue = count(array_intersect($pr_category_ids_array, $category_ids_array)) > 0;
        if($containsValue){
            array_push($matched_product_ids, $product->id);
         }
        }
        
        $products = Product::with('rating')->where(['status' => 1])
        ->whereIn('id', $matched_product_ids)
        ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
        ->inRandomOrder()
        ->take(20)->get();
        
        $products = Helpers::product_data_formatting($products, true, []);
        return $products;
        
    }
    
    public function product_sellers(Request $request)
    {
       $branches = Branch::where('featured', 1)->get(); 
       return response()->json($branches, 200);
    }




    public function get_featured_products(Request $request)
    {

        // Offset => page number
        // total_size => 
        // limit => limit per page
        //products
        try 
        {
             
            $offset = $request->has('offset') ? $request->offset : 0;//offset on boolean value instead of array  error handling 1

            $featuredProducts = Product::active()
                ->where('featured', 1)
                ->with(['merchants'])
                ->orderBy("order", "DESC")
                ->paginate(10, ['*'], 'page', $request['offset']);


             $formattedProducts = Helpers::product_data_formatting($featuredProducts, true);
         

            $paginator = 
            [
                'total_size' => $featuredProducts->total(),
                'limit' => 10,
                'offset' => $offset, 
                'products' => $formattedProducts
            ];

            return response()->json($paginator, 200);
        }
        catch (\Exception $e) {
            
            Log::error('Error occurred while fetching featured products: ' . $e->getMessage());
        
            
            return response()->json([
                'errors' => [
                    'code' => '00',
                    'message' => 'Featured products not found!',
                    'exception_message' => $e->getMessage(), 
                    'exception_trace' => $e->getTraceAsString() 
                ]
            ], 404);
        }
    }


}
