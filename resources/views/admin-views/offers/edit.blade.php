@extends('layouts.admin.app')

@section('title','Edit offer')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i>Edit offer</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{route('admin.offers.update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="offer_id"  value="{{$offer->id}}">
                <input type="hidden" name="offer_type"  value="{{$offer->offer_type}}">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.name')}}</label>
                            <input type="text" name="name" class="form-control" value="{{$offer->name}}" placeholder="New offer" required>
                        </div>
                    </div>
                    @php($saved_category=\App\Model\Category::where('id',$offer->category_id)->first())
                    <div class="col-6">
                        <div class="form-group" >
                            <label class="input-label">Category<span class="input-label-secondary"></span></label>
                          
                            <select name="category_id" class="form-control js-select2-custom">
                                  @foreach(\App\Model\Category::where('position', 1)->orderBy('name')->get() as $category)
                                    @if(isset($category->id))
                                    <option value="{{ $category->id }}" {{ ( $category->id == $saved_category->id) ? 'selected' : '' }}> {{ $category->name }} </option>
                                    @endif
                                    @endforeach 
                            </select>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label class="input-label" for="offer_type">Offer Type</label>
                            <select name="offer_type" id="offer_type" class="form-control"  onchange="show_item(this.value)" required>
                                <option value="product">Product</option>
                                <option value="discount">Discount</option>
                            </select>
                        </div>
                    </div>
                </div> -->
                
                <div class="row">
                @if($offer->offer_type == 'discount')
                    <div class="col-6" id="type-discount" >
                        <div class="form-group" >
                            <label class="input-label" for="discount">Discount %</label>
                            <input type="text" name="discount" value="{{$offer->discount}}" class="form-control" placeholder="Discount">
                        </div>
                    </div>

                    @else
                    @php($saved_product=\App\Model\Product::where('id',$offer->product_id)->first())
                    <div class="col-6" id="type-product">
                        <div class="form-group" >
                            <label class="input-label">Offered Product<span class="input-label-secondary"></span></label>
                          
                            <select name="product_id" class="form-control js-select2-custom">
                                    @foreach(\App\Model\Product::orderBy('name')->get() as $product)
                                    @if(isset($product->id))
                                    <option value="{{ $product->id }}" {{ ( $product->id == $saved_product->id) ? 'selected' : '' }}> {{ $product->name }} </option>
                                    @endif
                                    @endforeach 
                            </select>
                        </div>
                    </div>

                  
                    <div class="col-6" id="type-product-quantity">
                        <div class="form-group" >
                            <label class="input-label" for="offered_product_quantity">Offered Product Quantity</label>
                            <input type="number" name="offered_product_quantity" value="{{$offer->offered_product_quantity}}" class="form-control" placeholder="Offered Product Quantity">
                        </div>
                    </div>
                    @endif
                    
                </div>
                
                <button type="submit" class="btn btn-primary">{{trans('messages.submit')}}</button>
            </form>
        </div>

    </div>
</div>

@endsection



@push('script_2')
<script>
    function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-product-quantity").show();
                $("#type-discount").hide();
            } else {
                $("#type-product").hide();
                $("#type-product-quantity").hide();
                $("#type-discount").show();
            }
        }
</script>

<script>
    $(document).on('ready', function() {
        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function() {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>

@endpush