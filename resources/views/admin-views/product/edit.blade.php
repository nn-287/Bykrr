@extends('layouts.admin.app')

@section('title','Update product')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> Product Update</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="javascript:" method="post" id="product_form"
                      enctype="multipart/form-data">
                    @csrf
                   
                    

                    <div class="row">
                    <div class="col-md-4 col-6">
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">Name</label>
                        <input type="text" name="name" value="{{$product['name']}}" class="form-control"
                               placeholder="New Product" required>
                    </div>
                        </div>

                        <div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Price</label>
                                <input type="number" value="{{$product['price']}}" min="1" max="100000" name="price"
                                       class="form-control" step="0.01"
                                       placeholder="Ex : 100" required>
                            </div>
                        </div>
                        <!--<div class="col-md-4 col-6">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label" for="exampleFormControlInput1">TAX</label>-->
                        <!--        <input type="number" value="{{$product['tax']}}" min="0" max="100000" name="tax"-->
                        <!--               class="form-control" step="0.01"-->
                        <!--               placeholder="Ex : 7" required>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-4 col-6">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="input-label" for="exampleFormControlInput1">TAX Type</label>-->
                        <!--        <select name="tax_type" class="form-control js-select2-custom">-->
                        <!--            <option value="percent" {{$product['tax_type']=='percent'?'selected':''}}>Percent-->
                        <!--            </option>-->
                        <!--            <option value="amount" {{$product['tax_type']=='amount'?'selected':''}}>Amount-->
                        <!--            </option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Discount</label>
                                <input type="number" min="0" value="{{$product['discount']}}" max="100000"
                                       name="discount" class="form-control"
                                       placeholder="Ex : 100">
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Discount Type</label>
                                <select name="discount_type" class="form-control js-select2-custom">
                                    <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>
                                        Percent
                                    </option>
                                    <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>
                                        Amount
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                    <div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Item Type</label>
                                <select name="item_type" class="form-control js-select2-custom">
                                    <option value="0" {{$product['set_menu']==0?'selected':''}}>Product Item</option>
                                    <option value="1" {{$product['set_menu']==1?'selected':''}}>Set Menu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">Featured Product</label>
                                <select name="featured" class="form-control js-select2-custom">
                                    <option value="1" {{$product['featured']==1?'selected':''}}>Yes</option>
                                    <option value="0" {{$product['featured']==0?'selected':''}}>No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">Category<span
                                        class="input-label-secondary">*</span></label>
                                <select name="category_id[]" id="category-id" class="form-control js-select2-custom" multiple="multiple" 
                                        onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+$( this ).val(),'sub-categories')">
                                    @foreach($categories as $category)
                                        <option
                                            value="{{$category['id']}}" {{ in_array($category->id,$cate_se) ? 'selected' : ''}} >{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">Sub Category<span
                                        class="input-label-secondary"></span></label>
                                <select name="sub_category_id[]" id="sub-categories" multiple="multiple" 
                                        class="form-control js-select2-custom"
                                        onchange="getRequest('{{url('/')}}/admin/product/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                        @foreach($sub_categories as $sub_category)
                                        <option
                                            value="{{$sub_category['id']}}" {{ in_array($sub_category->id,$sub_cate_se) ? 'selected' : ''}} >{{$sub_category['name']}}</option>
                                        @endforeach

                                </select>
                            </div>
                        </div>
                        {{--<div class="col-md-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">Sub Sub Category<span
                                        class="input-label-secondary"></span></label>
                                <select name="sub_sub_category_id" id="sub-sub-categories"
                                        data-id="{{count($product_category)>=3?$product_category[2]->id:''}}"
                                        class="form-control js-select2-custom">

                                </select>
                            </div>
                        </div>--}}
                    </div>

                    <div class="row" style="border: 1px solid #80808045; border-radius: 10px;padding-top: 10px;margin: 1px">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">Attribute<span
                                        class="input-label-secondary"></span></label>
                                <select name="attribute_id[]" id="choice_attributes"
                                        class="form-control js-select2-custom"
                                        multiple="multiple">
                                    @foreach(\App\Model\Attribute::orderBy('name')->get() as $attribute)
                                        <option
                                            value="{{$attribute['id']}}" {{in_array($attribute->id,json_decode($product['attributes'],true))?'selected':''}}>{{$attribute['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 mb-2">
                            <div class="customer_choice_options" id="customer_choice_options">
                                @include('admin-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 mb-2">
                            <div class="variant_combination" id="variant_combination">
                                @include('admin-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'],true)])
                            </div>
                        </div>
                    </div>
                    
                    <!--<div class="col-md-6 col-6">-->
                    <!--        <a class="btn btn-info"  method="post"-->
                    <!--                               href="{{route('admin.product.attributes-images',[$product['id']])}}">Attributes</a>-->
                    <!--    </div>-->
                 
                    <!--<div class="row">-->
                    <!--    <div class="col-6">-->
                    <!--        <div class="form-group">-->
                    <!--            <label class="input-label" for="exampleFormControlInput1">Available Time Starts</label>-->
                    <!--            <input type="time" value="{{$product['available_time_starts']}}"-->
                    <!--                   name="available_time_starts" class="form-control"-->
                    <!--                   placeholder="Ex : 10:30 am" required>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-6">-->
                    <!--        <div class="form-group">-->
                    <!--            <label class="input-label" for="exampleFormControlInput1">Available Time Ends</label>-->
                    <!--            <input type="time" value="{{$product['available_time_ends']}}"-->
                    <!--                   name="available_time_ends" class="form-control" placeholder="5:45 pm"-->
                    <!--                   required>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">Short Description</label>
                        <textarea type="text" name="description" class="form-control">{{$product['description']}}</textarea>
                    </div>

                    <div class="row">
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlSelect1">Product Availability
                                    <span class="input-label-secondary">*</span></label>
                                <select id="exampleFormControlSelect1" name="availability" class="form-control" required>
                                    <option  @if($product['availability'] == "high") selected @endif value="high">High</option>
                                    <option  @if($product['availability'] == "moderate") selected @endif value="moderate">Moderate</option>
                                    <option  @if($product['availability'] == "low") selected @endif value="low">Low</option>
                               
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlSelect1">Status
                                    <span class="input-label-secondary">*</span></label>
                                <select id="exampleFormControlSelect1" name="status" class="form-control" required>
                                    <option  @if($product['status'] == 1) selected @endif value="1">Active</option>
                                    <option  @if($product['status'] == 0) selected @endif value="0">Disabled</option>
                                    
                               
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                    <div class="col-6">
                    <div class="form-group">
                        <label>Product Image</label><small style="color: red">* ( Ratio 1:1 )</small>
                        <div class="custom-file">
                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="customFileEg1">Choose file</label>
                        </div>

                        <center style="display: block" id="image-viewer-section" class="pt-2">
                            <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                 src="{{asset('storage/app/public/product')}}/{{$product['image']}}"
                                 alt="product image"/>
                        </center>
                    </div>
                        </div>
                    </div>

                   

                    <hr>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')
    <script>
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }

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
            $('#image-viewer-section').show(1000)
        });

        $(document).ready(function () {
            setTimeout(function () {
                //let category = $("#category-id").val();
                //let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                //let sub_sub_category ='{{count($product_category)>=3?$product_category[2]->id:''}}';
                //getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                //getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });
    </script>

    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>


    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>

    <script>
        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.variant-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    console.log(data.view);
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }
    </script>

    <script>
        $('#product_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',[$product['id']])}}',
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('product updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{ url()->previous() }}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush

