@extends('layouts.admin.app')

@section('title','Settings')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Store setup</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{route('admin.business-settings.update-setup')}}" method="post" enctype="multipart/form-data">
                @csrf
                @php($name=\App\Model\BusinessSetting::where('key','store_name')->first()->value)
                <div class="form-group">
                    <label class="input-label" for="exampleFormControlInput1">Store name</label>
                    <input type="text" name="store_name" value="{{$name}}" class="form-control" placeholder="New Store" required>
                </div>

                <hr>
                <div id="timeRangeContainer">

                    @php($openingHours = \App\Model\BusinessSetting::where('key', 'store_opening_hours')->first()->value)
                    @php($openingHoursArray = json_decode($openingHours, true))

                    @foreach($openingHoursArray as $index => $timeRange)

                    <div class="row">
                        <div class="col-md-4 col-12  pt-3">
                            <div class="form-group">
                                <label class="input-label">Ending time</label>
                                <input type="time" value="{{ $timeRange['start'] }}" name="store_opening_hours[{{ $index }}][start]" class="form-control" placeholder="Ex : 10:30 am" id="start_time_{{$index}}" required>
                            </div>
                        </div>

                        <div class="col-md-4 col-12 pt-3">
                            <div class="form-group">
                                <label class="input-label" for="end_time_{{$index}}">Starting time</label>
                                <input type="time" value="{{ $timeRange['end'] }}" name="store_opening_hours[{{ $index }}][end]" class="form-control" id="end_time_{{$index}}" placeholder="5:45 pm" required>
                            </div>
                        </div>

                        <div class="col-md-4 col-12  pt-3">
                            <div class="form-group">
                                <label class="input-label" style="color:white">#{{$index + 1}}</label>
                                <button type="button" class="btn btn-danger removeTimeRange">Remove</button>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Add button to dynamically add more time ranges -->

                </div>

                <div class="row pt-3">
                    <div class="col-md-12">
                        <button type="button" id="addTimeRange" class="btn btn-secondary">Add Time Range</button>
                    </div>
                </div>
                <hr>

                <div class="row">
                    @php($phone=\App\Model\BusinessSetting::where('key','phone')->first()->value)
                    <div class="col-md-4 col-12 pt-3">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.phone')}}</label>
                            <input type="text" value="{{$phone}}" name="phone" class="form-control" placeholder="" required>
                        </div>
                    </div>
                    @php($email=\App\Model\BusinessSetting::where('key','email_address')->first()->value)
                    <div class="col-md-4 col-12 pt-3">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.email')}}</label>
                            <input type="email" value="{{$email}}" name="email" class="form-control" placeholder="" required>
                        </div>
                    </div>
                    @php($address=\App\Model\BusinessSetting::where('key','address')->first()->value)
                    <div class="col-md-4 col-12 pt-3">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.address')}}</label>
                            <input type="text" value="{{$address}}" name="address" class="form-control" placeholder="" required>
                        </div>
                    </div>

                    @php($mov=\App\Model\BusinessSetting::where('key','minimum_order_value')->first()->value)
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.min')}} {{trans('messages.order')}} {{trans('messages.value')}} ( {{\App\CentralLogics\Helpers::currency_symbol()}} )</label>
                            <input type="number" min="1" value="{{$mov}}" name="minimum_order_value" class="form-control" placeholder="" required>
                        </div>
                    </div>

                    @php($app_version=\App\Model\BusinessSetting::where('key','app_version')->first()->value)
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">App version (Version on playstore)</label>
                            <input type="number" min="1" value="{{$app_version}}" name="app_version" class="form-control" placeholder="" required>
                        </div>
                    </div>
                    @php($currency_code=\App\Model\BusinessSetting::where('key','currency')->first()->value)
                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.currency')}}</label>
                            <select name="currency" class="form-control js-select2-custom">
                                @foreach(\App\Model\Currency::orderBy('currency_code')->get() as $currency)
                                <option value="{{$currency['currency_code']}}" {{$currency_code==$currency['currency_code']?'selected':''}}>
                                    {{$currency['currency_code']}} ( {{$currency['currency_symbol']}} )
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>


                    @php($footer_text=\App\Model\BusinessSetting::where('key','footer_text')->first()->value)
                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.footer')}} {{trans('messages.text')}}</label>
                            <input type="text" value="{{$footer_text}}" name="footer_text" class="form-control" placeholder="" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        @php($delivery=\App\Model\BusinessSetting::where('key','delivery_charge')->first()->value)
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{trans('messages.delivery')}} {{trans('messages.charge')}}</label>
                            <input type="number" min="0" max="10000" name="delivery_charge" value="{{$delivery}}" class="form-control" placeholder="100" required>
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        @php($ev=\App\Model\BusinessSetting::where('key','email_verification')->first()->value)
                        <div class="form-group">
                            <label>{{trans('messages.email')}} {{trans('messages.verification')}}</label><small style="color: red">*</small>
                            <div class="input-group input-group-md-down-break">
                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="1" name="email_verification" id="ev1" {{$ev==1?'checked':''}}>
                                        <label class="custom-control-label" for="ev1">{{trans('messages.on')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->

                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="0" name="email_verification" id="ev2" {{$ev==0?'checked':''}}>
                                        <label class="custom-control-label" for="ev2">{{trans('messages.off')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        @php($ev2=\App\Model\BusinessSetting::where('key','phone_otp')->first()->value)
                        <div class="form-group">
                            <label>Phone OTP Auth</label><small style="color: red">*</small>
                            <div class="input-group input-group-md-down-break">
                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="1" name="phone_otp" id="ev3" {{$ev2==1?'checked':''}}>
                                        <label class="custom-control-label" for="ev3">{{trans('messages.on')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->

                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="0" name="phone_otp" id="ev4" {{$ev2==0?'checked':''}}>
                                        <label class="custom-control-label" for="ev4">{{trans('messages.off')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        @php($closed=\App\Model\BusinessSetting::where('key','store_closed')->first()->value)
                        <div class="form-group">
                            <label>Store is closed</label><small style="color: red">*</small>
                            <div class="input-group input-group-md-down-break">
                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="1" name="store_closed" id="cl1" {{$closed==1?'checked':''}}>
                                        <label class="custom-control-label" for="cl1">{{trans('messages.on')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->

                                <!-- Custom Radio -->
                                <div class="form-control">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" value="0" name="store_closed" id="cl2" {{$closed==0?'checked':''}}>
                                        <label class="custom-control-label" for="cl2">{{trans('messages.off')}}</label>
                                    </div>
                                </div>
                                <!-- End Custom Radio -->
                            </div>
                        </div>
                    </div>
                </div>

                @php($logo=\App\Model\BusinessSetting::where('key','logo')->first()->value)
                <div class="form-group">
                    <label>{{trans('messages.logo')}}</label><small style="color: red">* ( {{trans('messages.ratio')}} 3:1 )</small>
                    <div class="custom-file">
                        <input type="file" name="logo" id="customFileEg1" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                        <label class="custom-file-label" for="customFileEg1">{{trans('messages.choose')}} {{trans('messages.file')}}</label>
                    </div>
                    <hr>
                    <center>
                        <img style="height: 100px;border: 1px solid; border-radius: 10px;" id="viewer" onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" src="{{asset('storage/app/public/store/'.$logo)}}" alt="logo image" />
                    </center>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">{{trans('messages.submit')}}</button>
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
    // JavaScript to handle dynamically adding time ranges
    document.getElementById('addTimeRange').addEventListener('click', function() {
        //  var container = document.querySelector('.row');
        var container = document.querySelector('#timeRangeContainer');
        var timeRangeIndex = container.childElementCount / 2;

        // Create start time input
        var startTimeInput = document.createElement('input');
        startTimeInput.type = 'time';
        startTimeInput.name = 'store_opening_hours[' + timeRangeIndex + '][start]';
        startTimeInput.className = 'form-control';
        startTimeInput.placeholder = 'Ex : 10:30 am';
        startTimeInput.required = true;

        // Create end time input
        var endTimeInput = document.createElement('input');
        endTimeInput.type = 'time';
        endTimeInput.name = 'store_opening_hours[' + timeRangeIndex + '][end]';
        endTimeInput.className = 'form-control';
        endTimeInput.placeholder = '5:45 pm';
        endTimeInput.required = true;

        // Create start time label
        var startTimeLabel = document.createElement('label');
        startTimeLabel.className = 'input-label';
        startTimeLabel.innerHTML = 'Starting Time ';

        // Create end time label
        var endTimeLabel = document.createElement('label');
        endTimeLabel.className = 'input-label';
        endTimeLabel.innerHTML = 'Ending Time ' ;

        // Create a row div
        var rowDiv = document.createElement('div');
        rowDiv.className = 'row';

        // Create new div for time range
        var timeRangeDiv = document.createElement('div');
        timeRangeDiv.className = 'col-md-4 col-12 pt-3';
        timeRangeDiv.appendChild(startTimeLabel);
        timeRangeDiv.appendChild(startTimeInput);

        // Create new div for time range
        var timeRangeDiv2 = document.createElement('div');
        timeRangeDiv2.className = 'col-md-4 col-12 pt-3';
        timeRangeDiv2.appendChild(endTimeLabel);
        timeRangeDiv2.appendChild(endTimeInput);

        // Append timeRangeDiv and timeRangeDiv2 to the rowDiv
        rowDiv.appendChild(timeRangeDiv);
        rowDiv.appendChild(timeRangeDiv2);



        // Create Remove label
        var removeLabel = document.createElement('label');
        removeLabel.className = 'input-label';
        removeLabel.innerHTML = 'Remove Time #' + (timeRangeIndex + 1);
        removeLabel.style.color = 'white';
        // Create remove button
        var removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.className = 'btn btn-danger removeTimeRange';
        removeButton.innerHTML = 'Remove';

        // Create div for remove button
        var removeButtonDiv = document.createElement('div');

        removeButtonDiv.className = 'col-md-4 col-12 pt-3';
        removeButtonDiv.appendChild(removeLabel);

        removeButtonDiv.className = 'col-md-4 col-12 pt-3';
        removeButtonDiv.appendChild(removeButton);



        // Append remove button div
        container.appendChild(removeButtonDiv);

        rowDiv.appendChild(removeButtonDiv);

        // Now append the rowDiv to the container or any other parent element
        container.appendChild(rowDiv);

        // Add click event listener for remove button
        removeButton.addEventListener('click', function() {
            container.removeChild(timeRangeDiv);
            container.removeChild(timeRangeDiv2);
            container.removeChild(removeButtonDiv);
        });
    });


    // Event delegation for dynamically added time range elements
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeTimeRange')) {
            var timeRangeContainer = event.target.closest('.row'); // Adjust this selector based on your actual structure
            if (timeRangeContainer) {
                timeRangeContainer.parentNode.removeChild(timeRangeContainer);
            }
        }
    });
</script>
@endpush