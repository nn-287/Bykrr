<?php

namespace App\CentralLogics;

use App\Model\Category;
use App\Model\Product;
use App\Model\ServiceAnsweredQuestions;
use App\Model\ServicesQuestionsAnswers;

class CategoryLogic
{

    public static function parents()
    {
        return Category::where('position', 0)->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }
    


    public static function products($category_id, $page_number,$fromApi = 0)
    {  
       
        $products = Product::active()
        ->get();
        //var_dump($products );exit;
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if ($category['id'] == $category_id) {
                    array_push($product_ids, $product['id']);
                }
            }
        }
         
        $p = Product::with('rating')->whereIn('id', $product_ids)->orderBy("order","DESC")->latest()->paginate(50,['*'],'page',$page_number);

        if($fromApi == 1){
            $data = [];
            $data['products']=$p;
            return $data;
        }
        else{
            return $p;
        }
    }

    public static function products_t($category_id,  $user_tags)
    {
        $key_tags = explode(',', $user_tags);
        $products =  Product::with('rating')->active()->orderBy("order","DESC")->latest()->get();
        //var_dump($products );exit;
        $product_ids = [];

        $product_ids_tags = [];
        $product_ids_tmp = [];
        $product_ids = [];


        foreach ($products as $product) {
            $product_tags = explode(',', $product->tags);
            foreach (json_decode($product['category_ids'], true) as $category) {
                if ($category['id'] == $category_id) {
                    $result=array_intersect($key_tags,$product_tags);
                    if(count($result)>0){
                        array_push($product_ids_tags, $product);
                    }else{
                        array_push($product_ids_tmp, $product);
                    }

                    //array_push($product_ids, $product['id']);
                }
            }
        }
        $product_ids = array_merge($product_ids_tags, $product_ids_tmp);
        return $product_ids;
        //return Product::with('rating')->whereIn('id', $product_ids)->orderBy("order","DESC")->latest()->get();
    }

    public static function all_products($id)
    {
        $cate_ids=[];
        array_push($cate_ids,(int)$id);
        foreach (CategoryLogic::child($id) as $ch1){
            array_push($cate_ids,$ch1['id']);
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                array_push($cate_ids,$ch2['id']);
            }
        }
       
        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if (in_array($category['id'],$cate_ids)) {
                    array_push($product_ids, $product['id']);
                }
            }
        }

        return Product::with('rating')->whereIn('id', $product_ids)->orderBy("order","DESC")->latest()->get();
    }

    public static function all_products_t($id, $user_tags)
    {
        $key_tags = explode(',', $user_tags);
        $cate_ids=[];
        array_push($cate_ids,(int)$id);
        foreach (CategoryLogic::child($id) as $ch1){
            array_push($cate_ids,$ch1['id']);
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                array_push($cate_ids,$ch2['id']);
            }
        }
       
        $products = Product::with('rating')->active()->orderBy("order","DESC")->latest()->get();
        $product_ids_tags = [];
        $product_ids_tmp = [];
        $product_ids = [];
        foreach ($products as $product) {
            //$product_tags = $product->tags;
            $product_tags = explode(',', $product->tags);
            $cate = $product['category_ids'];
            foreach (json_decode( $cate, true) as $category) {


                if (in_array($category['id'],$cate_ids)) {
                    $result=array_intersect($key_tags,$product_tags);
                    if(count($result)>0){
                        array_push($product_ids_tags, $product);
                    }else{
                        array_push($product_ids_tmp, $product);
                    }
                    
                }
            }
        }
        
        $product_ids = array_merge($product_ids_tags, $product_ids_tmp);

        //var_dump($product_ids);
        //var_dump($product_ids_tmp);
        //exit;
        return $product_ids;//Product::with('rating')->whereIn('id', $product_ids)->orderBy("order","DESC")->latest()->get();
    }
}
