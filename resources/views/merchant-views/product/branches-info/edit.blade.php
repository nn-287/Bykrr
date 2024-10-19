@extends('layouts.admin.app')

@section('title','Update Branch Product Info')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> Branches Product Info</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.product.branches.save')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf @method('put')

                    @foreach($branch_ids as $key=>$branch_id)
                    @php($branch=\App\Model\Branch::where('id',$branch_id)->first())
                    
                    @php($branch_product_info=\App\Model\BranchProductInfo::where(['branch_id'=> $branch_id, 'product_id'=>$id])->first())
                    <h4 style="color:#39aeb5">{{$branch->name}}</h4>
                    <input name="product_id" type="hidden" value="{{$id}}">
                   
                   
                    @php($variations=json_decode($product->variations, true))
                    @if($variations == [] || $variations == null)
                     <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Product Code</label>
                                <input type="text" name="product_code-{{$branch_id}}" value="{{$branch_product_info->product_code ?? ''}}" class="form-control"
                                       placeholder="Product Code" required>
                            </div>
                        </div>
                        
                         <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Price</label>
                                <input type="text" name="price-{{$branch_id}}" value="{{$branch_product_info->price ?? ''}}" class="form-control"
                                       placeholder="Price" required>
                            </div>
                        </div>
                        
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Discount</label>
                                <input type="text" name="discount-{{$branch_id}}" value="{{$branch_product_info->discount ?? ''}}" class="form-control"
                                       placeholder="Discount" required>
                            </div>
                        </div>
                        
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Quantity</label>
                                <input type="text" name="quantity-{{$branch_id}}" value="{{$branch_product_info->quantity ?? ''}}" class="form-control"
                                       placeholder="Quantity" required>
                            </div>
                        </div>
                    </div>
                    @else
                      @foreach($variations as $key=> $variation)
                      @php($branch_product_info_variation=\App\Model\BranchProductInfo::where(['branch_id'=> $branch_id, 'product_id'=>$id, 'variation_type'=>$variation['type']])->first())
                      <b style="color:red">{{$variation['type']}}</b>
                    <div class="row">
                        <input name="variation" type="hidden" value="{{$variation['type']}}">
                        
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Product Code</label>
                                <input type="text" name="product_code-{{$branch_id}}-{{$variation['type']}}" value="{{$branch_product_info_variation->product_code ?? ''}}" class="form-control"
                                       placeholder="Product Code" required_if="{{$key}} == 0">
                            </div>
                        </div>
                        
                         <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Price</label>
                                <input type="text" name="price-{{$branch_id}}-{{$variation['type']}}" value="{{$branch_product_info_variation->price ?? ''}}" class="form-control"
                                       placeholder="Price" required_if="{{$key}} == 0">
                            </div>
                        </div>
                        
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Discount</label>
                                <input type="text" name="discount-{{$branch_id}}-{{$variation['type']}}" value="{{$branch_product_info_variation->discount ?? ''}}" class="form-control"
                                       placeholder="Discount" required_if="{{$key}} == 0">
                            </div>
                        </div>
                        
                        <div class="col-3">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Quantity</label>
                                <input type="text" name="quantity-{{$branch_id}}-{{$variation['type']}}" value="{{$branch_product_info_variation->quantity ?? ''}}" class="form-control"
                                       placeholder="Quantity" required_if="{{$key}} == 0">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                <hr>
                    @endforeach
                    
                    <button type="submit" class="btn btn-primary">{{trans('messages.update')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        function show_item(type) {
            if (type === 'product') {
                $("#type-product").show();
                $("#type-category").hide();
            } else {
                $("#type-product").hide();
                $("#type-category").show();
            }
        }
    </script>
@endpush
