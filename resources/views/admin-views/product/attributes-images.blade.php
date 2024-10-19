@extends('layouts.admin.app')

@section('title','Update Atrribute Images')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-edit"></i> Update Atrribute Images</h1>
            </div>
        </div>
        <hr>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#attributeModel-{{$product['id']}}">Add attribute</button>
    </div>

    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
         <div class="modal fade" tabindex="-1" role="dialog" id="attributeModel-{{$product['id']}}" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title h4" id="mySmallModalLabel2">New Attribute</h5>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('admin.product.store-attribute')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="product_id" value="{{$product->id}}">
                            
                            <div class="row">
                                <!--<div class="col-md-6 col-12">-->
                                <!--    <div class="form-group">-->
                                <!--        <label class="input-label" for="exampleFormControlSelect1">Brand<span class="input-label-secondary"></span></label>-->
                                <!--        <select name="brand_id" class="form-control js-select2-custom">-->
                                <!--            @foreach(\App\Model\Attribute::orderBy('name')->get() as $attribute)-->
                                <!--            <option value="{{$attribute['id']}}">{{$attribute['name']}}</option>-->
                                <!--            @endforeach-->
                                <!--        </select>-->
                                <!--    </div>-->
                                <!--</div>-->
                                
                                
                                @foreach($attributes as $key=> $attribute)
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{$attribute->name}}</label>
                                        <input type="text" name="type_{{$key}}" class="form-control"  required>
                                    </div>
                                </div>
                                @endforeach

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">Price</label>
                                        <input type="float" name="price" class="form-control" placeholder="Ex: 50" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="page-header-title">Image:</label>
                                        <input type="file" name="new_image" class="form-control">

                                        <img style="width: 100px;"><br>

                                    </div>
                                </div>
                            </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary float-right" data-dismiss="modal">Close</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
        <form action="{{route('admin.product.update-attributes-images',[$product['id']])}}" method="post" enctype="multipart/form-data">
            @csrf
            @foreach($variations as $key=> $variation)
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{$variation['type']}}</label>
                        <input type="float" name="price_{{$key}}" value="{{$variation['price']}}" class="form-control"
                               placeholder="Price" required>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                     <a class="btn btn-danger" href="{{url('admin/product/delete-attribute', [$key, $product['id']])}}">Delete</a>

                </div>
                <div class="col-md-6 col-12">
                    <row>
                        <div class="form-group">
                            <label>{{trans('messages.image')}}</label><small style="color: red">* ( {{trans('messages.ratio')}} 3:1 )</small>
                            <div class="custom-file">
                                <input type="file" name="image_{{$key}}" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                <label class="custom-file-label" for="customFileEg1">{{trans('messages.choose')}} {{trans('messages.file')}}</label>
                            </div>

                        </div>
                        <center>
                            @if(isset($variation['image']))
                            <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer" src="{{asset('storage/app/public/product')}}/{{$variation['image']}}" alt="" />
                            @else
                            <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer" src="{{asset('storage/app/public/product')}}/{{$product['image']}}" alt="" />
                            @endif

                        </center>
                    </row>

                </div>

            </div>
            <hr>

            @endforeach
            <hr>
            <button type="submit" class="btn btn-primary">{{trans('messages.update')}}</button>
        </form>
    </div>
    <!-- End Table -->
</div>
</div>

@endsection

@push('script_2')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#viewer').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function() {
        readURL(this);
    });
</script>

<script>
        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
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
    
    
    
    <script type="text/javascript" src="dist/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript">
    $(function() {
        $('a.pl').click(function(e) {
            e.preventDefault();
            $('#phone').append('<input type="text" value="Phone">');
        });
        $('a.mi').click(function (e) {
            e.preventDefault();
            if ($('#phone input').length > 1) {
                $('#phone').children().last().remove();
            }
        });
    });
    </script>
@endpush