<?php

namespace App\Livewire\Product;

use App\Model\CartProduct;
use App\Model\Category;
use App\Model\Product;
use App\Model\Wishlist;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithPagination;

class ProductListComponent extends Component
{
    public $searchQuery;
    public $choose;
    public $products;
    protected $listeners = ['searchQueryUpdated'];
    public $products_per_page = 5;
    public $current_page_number = 1;
    public $total_products_size = 0;
    public $all_pages_count = 0;
    public $categoryId;

    use WithPagination;

    public function render()
    {
        return view('livewire.product.product-list-component')->layout('livewire.layouts.base');
    }

    public function mount($search = null, Request $request = null)
    { 
        $categoryId = $request->query('categoryId');
        $this->categoryId = $categoryId;
         if(!empty($search)){
            $this->searchQuery = $search;
            // $this->products = Product::where('name', 'like', '%' . $search . '%')->get();

            if(!empty($categoryId)){
                
                $paginator = Product::active()
                 ->where('name', 'like', '%' . $search . '%')
                 ->where(function ($query) use ($categoryId) {
                    $query->orWhereJsonContains('category_ids', ['id' => $categoryId]);
                })
                ->latest()
                ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);

            }else{
                if(!empty($categoryId)){
                    $paginator = Product::where('name', 'like', '%' . $search . '%')
                    ->where(function ($query) use ($categoryId) {
                        $query->orWhereJsonContains('category_ids', ['id' => $categoryId]);
                    })
                    ->latest()
                    ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
                }else{
                    $paginator = Product::where('name', 'like', '%' . $search . '%')->latest()
                    ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
                }
     
            }

            $this->products = $paginator->items();
            $this->total_products_size = $paginator->total();
            $this->all_pages_count = $paginator->total() / $this->products_per_page;
        }else{
            // $this->products = Product::get(); 
            $paginator = Product::latest()
                ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
           $this->products = $paginator->items();
           $this->total_products_size = $paginator->total();
           $this->all_pages_count = $paginator->total() / $this->products_per_page;
        }
    }

    // public function render()
    // {
    //     return view('livewire.product.product-list-component')->layout('livewire.layouts.base');
    // }

    public function updateCurrentPageNumber($number)
    {
        $this->current_page_number = $number;

        if(!empty($search)){
            $this->searchQuery = $search;
            // $this->products = Product::where('name', 'like', '%' . $search . '%')->get();
            $paginator = Product::latest()
                ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
    
            $this->products = $paginator->items();
            $this->total_products_size = $paginator->total();
            $this->all_pages_count = $paginator->total() / $this->products_per_page;
        }else{
            // $this->products = Product::get(); 
            $paginator = Product::latest()
                ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
    
           $this->products = $paginator->items();
           $this->total_products_size = $paginator->total();
           $this->all_pages_count = $paginator->total() / $this->products_per_page;
        }
    }


    public function updateCurrentList($categoryId)
    {
           $this->current_page_number = 1;
           $this->categoryId = $categoryId;
            $paginator = Product::latest()
            ->where(function ($query) use ($categoryId) {
                $query->orWhereJsonContains('category_ids', ['id' => $categoryId]);
            })
                ->paginate($this->products_per_page, ['*'], 'page', $this->current_page_number);
    
            $this->products = $paginator->items();
            $this->total_products_size = $paginator->total();
            $this->all_pages_count = $paginator->total() / $this->products_per_page;
    }

    

    public function search($searchQuery)
    {
        $this->searchQuery = $searchQuery;
        // Optionally, trigger a method to update products based on the search query
    }

    // public function mount(){
    //    $products = Product::pluck('category_ids');
    //    $arrayData = [];
    //    foreach($products as $product){
    //     $arrayData[] = json_decode($product, true);
    //    }
        
    //     foreach($arrayData as $item){
    //         $category_name = Category::where('id', $item)->get();
    //     }
    

    public function addToWishlist($product_id){
        $wishlist = Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if ($wishlist) {
            Wishlist::where('user_id', auth()->user()->id)->where('product_id', $product_id)->delete();
        }else{
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product_id;
            $wishlist->save();
        }
    }

    public function addToCart($product_id){
        $checkProduct = CartProduct::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if(!$checkProduct){
            $myCart = new CartProduct();
            $myCart->user_id = auth()->user()->id;
            $myCart->product_id = $product_id;
            $myCart->save();
        }else{
            CartProduct::where('user_id', auth()->user()->id)->where('product_id', $product_id)->delete();
        }
        // if(!empty($this->searchQuery)){
        //     $products = Product::where('name', 'like', '%' . $this->searchQuery . '%')->paginate(2);
        // }else{
        //     $products = Product::paginate(2);
        // }
        // // return redirect()->route('product.list');
    }

    public function removeFromCart($product_id){
        $product = CartProduct::where('user_id', auth()->user()->id)->where('product_id', $product_id)->first();
        if($product){
            $product->delete();
        }
        // return redirect()->route('product.list');
    }
    
}
