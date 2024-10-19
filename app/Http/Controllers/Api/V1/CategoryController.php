<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    public function get_category($id)
    {
        try {
            $category = Category::where('id', $id)->first();
            return response()->json($category, 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['code' => 'cate-001', 'message' => 'Cateory not found!']
            ], 404);
        }
    }

    public function get_categories()
    {
        try {
            $categories = Category::where(['position'=>0,'status'=>1])->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    public function get_categories_full()
    {
        try {
            $categories = Category::where(['position'=>0])->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    

    public function get_childes($id)
    {
        try {
            $categories = Category::where(['parent_id' => $id,'status'=>1])->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

  

    public function get_products(Request $request)
    {
        
        $result = CategoryLogic::products($request->category_id,  $request->page_number, 1);
        
        return response()->json(Helpers::product_data_formatting($result['products'], true), 200);
    }

    public function get_products_t(Request $request, $id)
    {
        //var_dump("dsadas");exit;
        $user = $request->user();
        $key_answer = $user->answer_nutrition.$user->answer_skin.$user->answer_hair;
        return response()->json(Helpers::product_data_formatting(CategoryLogic::products_t($id, $key_answer), true), 200);
    }
    
    public function get_all_products($id)
    {
        
        try {
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products($id, $user_tags), true), 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_all_products_t(Request $request, $id)
    {
       
        //try {
            $user = $request->user();
            $key_answer = $user->answer_nutrition.$user->answer_skin.$user->answer_hair;
            return response()->json(Helpers::product_data_formatting(CategoryLogic::all_products_t($id, $key_answer), true), 200);
        //} catch (\Exception $e) {
        //    return response()->json([], 200);
        //}
    }

}
