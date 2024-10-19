<?php

namespace App\CentralLogics;


use App\Model\Product;
use App\Model\Review;
use App\Model\Category;
use App\Model\ServiceAnsweredQuestions;
use App\Model\ServicesQuestionsAnswers;
use App\Model\Brand;
use App\Model\CartProduct;
use App\Model\Wishlist;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->with(['rating'])->where('id', $id)->first();
    }
    
    public static function get_product_details($id)
    {
        $categories = [];
        $product = Product::active()->with(['rating'])->where('id', $id)->first();
        
        if($product && $product['category_ids']){
            foreach (json_decode($product['category_ids'], true) as $categoryObject) {
                if (isset($categoryObject['id'])) {
                    $category = Category::find($categoryObject['id']);
                    if($category) array_push($categories, $category);
                }
            }
        }
        
        $product->categories = $categories;
        return $product;
    }

    public static function get_latest_products($limit = 10, $offset = 1,$customer_id = 0)
    {
        $tags = [];
        $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
        if($customer_id != 0){
           
            if(count($tags) > 0) {
                $data = Product::active()->with(['rating'])->where(function ($q) use ($tags) {
                    foreach ($tags as $value) {
                        if(!empty(trim($value))){
                            $q->orWhere('tags', 'like', "%{$value},%");
                        }
                    }
                })->orderBy("order", "desc")->latest()->paginate($limit, ['*'], 'page', $offset);
                $datas = collect($data->items());
                $resultCount= count($datas);
                if($resultCount > 0 && $resultCount >= $limit){
                    return [
                        'total_size' => $paginator->total(),
                        'limit' => $limit,
                        'offset' => $offset,
                        'tags_to_match'=>$tags,
                        'products' => $datas
                    ];
                }
                else if ($resultCount < $limit){
                    $existingIds = $datas->pluck('id');
                    $data2 = Product::active()->whereNotIn('id',$existingIds)->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
                    $data1 = $datas->merge($data2->items());
                    return [
                        'total_size' => $paginator->total(),
                        'limit' => $limit,
                        'offset' => $offset,
                        'tags_to_match'=>$tags,
                        'products' => $data1
                    ];
                }
            }
            else{
            $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
            }
        }
        else{
            $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
        }
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'tags_to_match'=>$tags,
            'products' => $paginator->items()
        ];
    }
    
    public static function get_latest_target_products($limit = 10, $offset = 1,$customer_id = 0, $nearby_branch_id = 0)
    {  
        $keys = [];
        $prd_ids = [];
        $key = [];
        
          $paginator = Product::active()
            ->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
                $query->where('branch_id', $nearby_branch_id)->first();
            }])
            ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
            ->orderBy("order", "DESC")
            ->paginate($limit, ['*'], 'page', $offset);  
      
    
        if($customer_id != 0){
           
        
            if(count($keys) > 0) {
                $products = Product::active()->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
                  $query->where('branch_id', $nearby_branch_id)->first();
                  }])
                  ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')->where(['status' => 1])->orderBy("order","DESC")->get();
                
                foreach($products as $product){
                    
                    $tags = explode(',', $product->tags);
                foreach($keys as $k){
                  if(in_array($k, $tags)){
                      array_push($prd_ids, $product->id);
                  }}
                }
              //  $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->whereIn('id', $prd_ids)->paginate($limit, ['*'], 'page', $offset);
              
            //   $paginator = Product::active()->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
            //       $query->where('branch_id', $nearby_branch_id)->first();
            //       }])
            //       ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')->where(['status' => 1])->orderBy("order","DESC")->whereIn('id', $prd_ids)->paginate($limit, ['*'], 'page', $offset);
            
            // First query to retrieve records matching the whereIn condition
$firstQuery = Product::active()
    ->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
        $query->where('branch_id', $nearby_branch_id)->first();
    }])
    ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
    ->where(['status' => 1])
    ->whereIn('id', $prd_ids)
    ->orderBy("order","DESC");

// Second query to retrieve the remaining records
$secondQuery = Product::active()
    ->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
        $query->where('branch_id', $nearby_branch_id)->first();
    }])
    ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')
    ->where(['status' => 1])
    ->whereNotIn('id', $prd_ids)
    ->orderBy("order","DESC");

// Combine the queries using Union
$combinedQuery = $firstQuery->union($secondQuery);

// Paginate the combined results
$paginator = $combinedQuery->paginate($limit, ['*'], 'page', $offset);
            }
            else{
                $paginator = Product::active()->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
                  $query->where('branch_id', $nearby_branch_id)->first();
                  }])
                  ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')->where(['status' => 1])->paginate($limit, ['*'], 'page', $offset);
                  
               // $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
            }
        }
        else{
            $paginator = Product::active()->with(['rating', 'branch_product_info' => function ($query) use ($nearby_branch_id) {
                  $query->where('branch_id', $nearby_branch_id)->first();
                  }])
                  ->whereRaw('JSON_CONTAINS(branch_ids, \'["' . $nearby_branch_id . '"]\')')->where(['status' => 1])->paginate($limit, ['*'], 'page', $offset);
                  
         //   $paginator = Product::active()->with(['rating'])->orderBy("order","desc")->latest()->paginate($limit, ['*'], 'page', $offset);
        }
        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'tags_to_match'=>$keys,
            'products' => $paginator->items()
        ];
    }

    public static function get_related_products($product_id,$customer_id = 0,$fromApi = 0)
    {
        $tags = [];
        $product = Product::find($product_id);
        if($customer_id != 0){
          
            if(count($tags) > 0) {
                $products = Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
                    ->where('id', '!=', $product->id)
                    ->where(function ($q) use ($tags) {
                    foreach ($tags as $value) {
                        if(!empty(trim($value))){
                            $q->orWhere('tags', 'like', "%{$value},%");
                        }
                    }
                })->limit(10)->get();
                $cnt = $products->count();
                if($cnt == 0){
                    $products = Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
                        ->where('id', '!=', $product->id)
                        ->limit(10)
                        ->get();
                }
                elseif ($cnt < 10){
                    $lmt = 10-$cnt;
                    $ofst = $cnt;
                    $products1 = Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
                        ->where('id', '!=', $product->id)
                        ->limit($lmt)
                        ->offset($ofst)
                        ->get();
                    $products = $products->merge($products1);
                }
            }
            else{
                $products =  Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
                    ->where('id', '!=', $product->id)
                    ->limit(10)
                    ->get();
            }
        }
        else {
            $products= Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
                ->where('id', '!=', $product->id)
                ->limit(10)
                ->get();
        }
        if($fromApi == 1){
            $d=[];
            $d['products'] = $products;
            $d['tags_to_match'] = $tags;
            return $d;
        }
        else{
            return $products;
        }
    }

    public static function search_products($name, $limit = 50, $offset = 1)
    {
      
        $paginator1 = Product::active()->with(['rating'])
        ->where(function($query) use ($name){
            $query->where('name', 'like', "%{$name}%");
        })
        ->paginate($limit, ['*'], 'page', $offset);
        
        return [
            'total_size' => $paginator1->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator1->items()
        ];
 
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function product_attributes($product_id, $variaion_index)
    {
        $product = Product::find($product_id);
        $variationType = '';

        $choice_options = json_decode($product->choice_options, true);
       
    foreach ($choice_options as $index => $choiceOption) {
        try {
            $selectedOption = $choiceOption['options'][$variaion_index[$index]]; 
            $variationType .= ($index == 0) ? $selectedOption : '-' . $selectedOption;
        }catch (\Exception $e) {
        }
      
    }

    return $variationType;
    }


    public static function get_product_variation($product)
    {
        $choice_options = $product->choice_options;

        $arr = [];
        $choice_options = is_array($product->choice_options) ? $product->choice_options : json_decode($product->choice_options, true);
        foreach ($choice_options as $choice_option) {
            array_push($arr, $choice_option['options'][0]);
        }
        $combinedString = implode('-', $arr);
        return $combinedString;
    }

    public static function get_price_info($cart_product)
    {
      $price = $cart_product->product->price;

      $discount_amount = 0;
      
      $variationCombinedString = ProductLogic::product_attributes($cart_product->product->id, json_decode($cart_product->variation_index, true));

      $product_variations = is_array($cart_product->product->variations) ? $cart_product->product->variations : json_decode($cart_product->product->variations, true);

      foreach ($product_variations as $variation) {
            if ($variation['type'] == $variationCombinedString) {
                $price = $variation['price'];
            }
        }

        if($cart_product->product->discount_type == 'amount'){
            $discount_amount = $cart_product->product->discount;
          }else{
            $discount_amount = ($cart_product->product->discount * $price) / 100;
          }

        $price_info['price'] = $price;
        $price_info['discount_amount'] = $discount_amount;
        return $price_info;
    }

    public static function addToWhishlist($user_id, $product_id)
    {
        $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();
        if ($wishlist) {
            Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->delete();
        } else {
            $wishlist = new Wishlist;
            $wishlist->user_id = $user_id;
            $wishlist->product_id = $product_id;
            $wishlist->save();
        }
    }

    public static function calculateCartPrice($user_id)
    {
        $cart_products = CartProduct::where('user_id', $user_id)->get();
        $total_price = 0;
        $total_discount_amount = 0;
        foreach($cart_products as $cart_product){
           $price_info = ProductLogic::get_price_info($cart_product);
           $price = $price_info['price'] * $cart_product->quantity;
           $discount_amount = $price_info['discount_amount'] * $cart_product->quantity;

           $total_price = $total_price + $price;
           $total_discount_amount = $total_discount_amount + $discount_amount;
        }
        $cart_info['total_price'] = $total_price;
        $cart_info['total_discount_amount'] = $total_discount_amount;
        return $cart_info;
    }


    public static function getProductCategoryIds($id)
    {
           $category_ids = [];
        
           $product = Product::find($id);
           $categories = json_decode($product->category_ids, true);
           foreach($categories as $category){
            array_push($category_ids, $category['id']);
           }
           return $category_ids = array_unique($category_ids);
    }
    

    public static function wishlist_items($user_id)
    {
        $wishlists = Wishlist::with('product')->whereHas('product')
        ->where('user_id', $user_id)->get();
        $new_wishlists = [];
        foreach($wishlists as $wishlist){
            $product = $wishlist->product;
            $variations = json_decode($wishlist->product->variations);
            $attributes = json_decode($wishlist->product->attributes);
            $category_ids = json_decode($wishlist->product->category_ids);
            $choice_options = json_decode($wishlist->product->choice_options);
            $product['variations'] = $variations;
            $product['attributes'] = $attributes;
            $product['category_ids'] = $category_ids;
            $product['choice_options'] = $choice_options;

            $wishlist['product'] = $product;
            array_push($new_wishlists, $wishlist);
        }
        return $new_wishlists;
    }

}
