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
                        <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Hair Care Questions</h1>
                    </div>
                </div>
            </div>

            <div class="row gx-2 gx-lg-3">
                <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                    <form action="javascript:" method="post" id="product_form"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <p>Name</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="New Product" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <p>Title 1:</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="New Product" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <p>Title 2:</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="New Product" required>
                                </div>
                            </div>


                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <p>Title 3:</p>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                    <input type="text" name="name" class="form-control" placeholder="New Product" required>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="form-group">

                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <label class="page-header-title">Question System</label>
                            <div class="form-group">
                                <a href="{{route('admin.product.hair-routine')}}"><button type="submit" class="btn btn-secondary btn-lg">Hair Questions</button></a>
                            </div>

                        </div>
                    </div>
                    {{--Hair care Questions--}}

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <h1 class="page-header-title">Add New Question</h1>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="dropdown dropright">
                                    <button type="button" class="btn btn-primary" data-toggle="dropdown" >
                                        +
                                    </button>
                                    <div class="form-table" id="customFields">
                                        <div class="form-group dropdown-menu">
                                            <a class="dropdown-item btn btn-success add-more" type="button" id="add"><i class="glyphicon glyphicon-plus"></i> Add</a>
                                            <a class="dropdown-item" href="#">Skin Care</a>
                                            <a class="dropdown-item" href="#">Nutrition</a>
                                            <a class="dropdown-item" href="#">Add New +</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="form-group">
                            <div id="dynamic_field">
                            </div>
                    </div>


                {{--//*    Buttons//*--}}

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <h1 class="page-header-title">Add Buttons</h1>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <div class="dropdown dropright">
                                    <button type="button" class="btn btn-primary" id="buttons">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="add_buttons"></div>

                    {{--Cards--}}











                    </div>
                </div>
            </div>
    @endsection

    @push('script')

    @endpush

    @push('script_2')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>




        //Hair Care Questions//


        //    $('[id$=labelId]').append("NewText");
        $('#labelId').html('Add Hair Routinw');
        $(document).ready(function(){
            var i = 1;
            $("#add").click(function(){
                $('#dynamic_field').append(
                        '<form action="{{url('testStoreQue')}}" method="post">'+
                        '<input type="hidden"  value="{{csrf_token()}}"  name="_token">'+
                        '<div id="row'+i+'">' +
                        '<div class="form-group col-6">\n' +
                        '<h6>Question 1</h6>\n' +
                        '<input class="form-control question_1" name="question[]" type="text"  required maxlength="50">\n' + ' &nbsp;'+
                        '</div>'+
                        '<div id="custom_field">'+'</div>'+
                        '<label class="answer">Add new Answer</label>\n'+'<a class="btn btn-primary answer" id="add_more" style="margin-left:25px">'+'+'+
                        '</a>'+
                        '</div>\n' +
                        '<br>'+
                        '<div id="answeer_field">'+'</div>'+
                        '<div class="form-group col-6">\n' +
                        '<label>Next Page</label>\n' +
                        '<select  class="form-control" id="inputEmail">'+
                        '<option>'+'Question 2'+'</option>'+
                        '<option>'+'Question 3'+'</option>'+
                        '<option>'+'skin Routine Page'+'</option>'+
                        '<option>'+'Hair Routine Page+'+'</option>'+
                        '</select>'+
                        '</div>'+
                        '<label class="">Add new Answer</label>\n'+'<a class="btn btn-primary" id="addd" style="margin-left:25px">'+'+'+'</a>'+


                        '<div class="col-sm-6">'+
                        '<button type="submit" class="btn btn-primary" onclick="hairpage()">submit</button>'+

                        '</div>'+





                        '</form>'
                );
            });

            $(document).on('click', '#add_more', function(){

                $("#add_more").click(function(){
                    i++;
                    $('#custom_field').append(
                            '<div id="row'+i+'" style="margin-left:26px;margin-top: -21px;">' +
                            '<div class="form-group col-6">\n' +
                            '<label>Answer 1</label>\n' +
                            '<input class="form-control name" name="answer[]" type="text" required   maxlength="50">\n' + ' &nbsp;'+
                            '<label>Tags</label>\n' +
                            '<input class="form-control name" name="tags[]" type="text" required  maxlength="50">\n' + ' &nbsp;'+
                            '<br>'+ '</div>'+
                            '<label>Add text to Routine Field</label>\n'+
                            '<a class="btn btn-primary add_answer" style="margin-left:25px" id="add_answer">'+'+'+'</a>'+
                            '</div>'+
                            '<br>'+

                            '</div>\n'
                    );

                });
                $(".answer").on("click", function(e){
                    e.preventDefault();

                    $(".answer").hide();
                });
            });

            $(document).on('click', '#add_answer', function(){
                $("#add_answer").click(function(){
                    $('#answeer_field').append(
                            '<div class="form-group col-6"">'+
                            '<label class="sr-only" for="inputEmail">Email</label>'+
                            '<input class="form-control" id="inputEmail" style="margin-left: -16px;">'+
                            '</div>'+
                            '<br>'+
                            ' <form class="form-inline" method="post">'+
                            '<div class="form-group mr-4">'+
                            '<select  class="form-control" id="inputEmail">'+
                            '<option>'+'Hair Routine'+'</option>'+
                            '<option>'+'Skin Routine'+'</option>'+
                            '<option>'+'Diet Routine'+'</option>'+
                            '<option>'+'Create New + '+'</option>'+
                            '</select>'+
                            '</div>'+
                            '<div class="form-group mr-4">'+
                            '<select class="form-control" id="inputEmail">'+
                            '<option>'+'Hair Problem'+'</option>'+
                            '<option>'+'Hair Type'+'</option>'+
                            '<option>'+'Hair Shape'+'</option>'+
                            '<option>'+'Create New +'+'</option>'+
                            '</select>'+
                            '</div>'+
                            '</form>'+
                            '<br>'


                    );
                });

            });



        });


        function hairpage() {
            var question_1 = $('.question_1').val();
            var answer = $('.answer').val();
            var tags = $('.tags').val();
            if(question_1 == ''){
                toastr.clear();
                toastr.error('question_1 Required');
            }
            else if(answer == ''){
                toastr.clear();
                toastr.error('answer Required');
            }
            else if(tags == ''){
                toastr.clear();
                toastr.error('tags Required');
            }
        }


        //buttons///

        $(document).ready(function(){
            var i = 1;
            $("#buttons").click(function(){
                i++;
                $('#add_buttons').append(
                        '<br>'+
                        ' <form class="form-inline" action="{{url('store/add')}}" method="POST">'+
                        ' <input type="hidden"  value="{{csrf_token()}}"  name="_token">'+
                        '<div class="form-group col-sm-6">'+
                        ' <input type="hidden" name="hair_new_id[]">'+
                        '<label>'+'Button 1'+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6">'+
                        '<input type="text" class="form-control" id="button_shampooe" placeholder="Enter Shampoo" name="button_shampooe[]"  style="margin-left: -16px;">'+

                        '</div>'+'<br>' +'<br>' +
                        '<div class="form-group col-sm-6">'+
                        '<label>'+'Image'+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6">'+
                        '<input type="file" class="form-control" id="image" name="image[]"  style="margin-left: -16px;">'+

                        '</div>'+'<br>' +'<br>' +
                        '<div class="form-group col-sm-6">'+
                        '<label>'+'Title 1'+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6">'+
                        '<input type="text" class="form-control" id="title_1" placeholder="Enter Shampoo Name" name="title_1[]"  style="margin-left: -16px;">'+

                        '</div>'+'<br>' +'<br>' +
                        '<div class="form-group col-sm-6">'+
                        '<label>'+'Title 2'+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6">'+
                        '<input type="text" class="form-control" id="title_2" placeholder="Enter Usage of Shampoo"  name="title_2[]"  style="margin-left: -16px;">'+

                        '</div>'+'<br>' +'<br>' +
                        '<div class="form-group col-sm-6">'+
                        '<label>'+'Title 3'+'</label>'+
                        '</div>'+
                        '<div class="form-group col-sm-6">'+
                        '<input type="text" class="form-control" id="inputEmail" placeholder="When You will use" name="title_3[]"    style="margin-left: -16px;">'+

                        '</div>'+

                        '<br>'+'<br>'+'<br>'+'<br>'+

                        '<button type="submit" class="btn btn-primary" style="margin-left: 118px">submit</button>'+
                        '</form>'+
                        '<br>'

                );

            });
        });
















    </script>

    @endpush

