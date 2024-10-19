@extends('layouts.admin.app')

@section('title','Add new product')

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
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Hair Routine</h1>
                </div>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">


                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <h1 class="page-header-title">Cards</h1>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="dropdown dropright">
                                <button type="button" class="btn btn-primary" id="hair_routine">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="hairroutine"></div>

                {{--tips--}}

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <h1 class="page-header-title">Tips</h1>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="dropdown dropright">
                                <button type="button" class="btn btn-primary" id="tip_add">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tips"></div>
                </div>
            </div>
        </div>
    @endsection

@push('script')

@endpush

@push('script_2')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var i = 1;
        $("#hair_routine").click(function(){
            i++;
            $('#hairroutine').append(
                    '<br>'+
                    '<form class="form-inline" action="storeHairRoutine" method="post">'+
                    '<input type="hidden"  value="{{csrf_token()}}"  name="_token">'+
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Card'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="card_title_1" placeholder="Water Intake" name="card_title_1[]"  style="margin-left: -16px;">'+
                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Icon'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="file" class="form-control" id="image" name="image[]" style="margin-left: -16px;">'+
                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Title 1'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="title_2" placeholder="your daily fluid intake"  name="title_2[]" style="margin-left: -16px;">'+
                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Text'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="description" placeholder="drink 2 times of fluids a day" name="description[]"  style="margin-left: -16px;">'+
                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'id'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="card_title_3" placeholder="culy hair " name="card_title_3" style="margin-left: -16px;">'+
                    '</div>'+
                    '<button type="submit" class="btn btn-primary">'+ 'submit'+ '</button>'+
                    '</form>'+
                    '<br>'
            );
        });
    });
    $(document).ready(function(){
        var i = 1;
        $("#tip_add").click(function(){
            i++;
            $('#tips').append(
                    '<br>'+
                    ' <form class="form-inline" action="{{url('storeTips')}}"   method="post">'+
                    ' <input type="hidden"  value="{{csrf_token()}}"  name="_token">'+
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Tip 1'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="tip" placeholder="Balanced diet" name="tip[]" required style="margin-left: -16px;">'+
                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Background Image'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="file" class="form-control" id="image" name="image[]" required style="margin-left: -16px;">'+

                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Title 1'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="dialogue" placeholder="balance diet" name="dialogue[]" required style="margin-left: -16px;">'+

                    '</div>'+'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'Dialouge'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<textarea type="text" class="form-control" id="dialogue" placeholder="essential fattaaid" name="dialogue[]" required style="margin-left: -16px;">'+
                    '</textarea>'+
                    '</div>'+'<br>' +'<br>' +'<br>' +
                    '<div class="form-group col-sm-6">'+
                    '<label>'+'id'+'</label>'+
                    '</div>'+
                    '<div class="form-group col-sm-6">'+
                    '<input type="text" class="form-control" id="description" placeholder="curly hair "  name="description[]"  required style="margin-left: -16px;">'+
                    '</div>'+
                    '<button type="submit" class="btn btn-primary">'+ 'submit'+ '</button>'+
                    '</form>'+
                    '<br>'

            );
        });
    });
</script>
@endpush