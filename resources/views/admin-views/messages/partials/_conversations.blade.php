<style>
    div.scroll-down {
        max-height: 300px;
        overflow-y: scroll;
    }
</style>
<div class="card mb-3 mb-lg-5">
    <!-- Header -->
    <a class="btn btn-primary" href="{{route('admin.customer.view', [$user->id])}}">User Details</a>
    <div class="card-header">
        <div class="avatar avatar-lg avatar-circle">
            <img class="avatar-img" style="width: 54px;height: 54px"
                src="{{asset('storage/app/public/profile/'.$user['image'])}}"
                onerror="this.src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'" alt="Image Description">
        </div>
        <h5 class="mb-0 mr-3">{{$user['f_name'].' '.$user['l_name']}}</h5>

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#answersListModal">History</button>
        <button type="button" class="btn btn-info">User Details</button>

    </div>

    <tbody>
        <tr>
            <td>
                <div class="modal fade" tabindex="-1" role="dialog" id="answersListModal"
                    aria-labelledby="mySmallModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title h4" id="mySmallModalLabel">User history</h5>

                            </div>

                            <div class="modal-body">
                                @foreach ($serviceData as $s)
                                <h5 class="mb-0 mr-3">---------------------------------------</h5>
                                <h5 class="mb-0 mr-3">{{trans($s['service'])}} : {{$s['keys']}}</h5>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>


    <div class="card-body" id="messages-card">
        <div class="row scroll-down">
            @foreach($convs as $con)
            @if($con->image!=null)
            <div class="col-12 pt1 pb-1">
                <div
                    style="background:#fdffddd1;padding: 10px;border: 1px solid #80808057;border-radius: 10px; width: 50%">
                     <img style="width:100%" src="{{asset('storage/app/public/conversation').'/'.$con->image}}">
                    <br>
                    
                </div>
            </div>
          @endif
            @if($con->message!=null)
            <div class="col-12 pt1 pb-1">
                <div
                    style="background:#fdffddd1;padding: 10px;border: 1px solid #80808057;border-radius: 10px; width: 50%">
                    <h6>{{$con->message}}</h6>
                    <br>
                    
                </div>
            </div>
            @endif
            @if($con->reply!=null)
            <div class="col-12 pt-1 pb-1">
                <div class="float-right"
                    style="background:#89faff47;padding: 10px;border:1px solid #80808057;border-radius: 10px; width: 50%">
                    <h6>{{$con->reply}}</h6>
                </div>
            </div>
            @endif
            @endforeach
            <div id="scroll-here"></div>
        </div>
    </div>
    <!-- Body -->
</div>
<form action="javascript:" method="post" id="reply-form">
    @csrf
    <div class="card mb-3 mb-lg-5">
        <!-- Body -->
        <div class="card-body">
            <label class="input-label">{{trans('messages.reply')}}</label>
            <!-- Quill -->
            <div class="quill-custom_">
                <textarea class="form-control" name="reply"></textarea>
            </div>
            <!-- End Quill -->
        </div>
        <!-- Body -->

        <!-- Footer -->
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <button type="submit" onclick="replyConvs('{{route('admin.message.store',[$user->id])}}')"
                    class="btn btn-primary">{{trans('messages.send')}}
                </button>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <a class="btn btn-primary"
                    href="{{route('admin.notification.new-message-notification', [$user->id])}}">Send Notification</a>
            </div>
        </div>

        <!-- End Footer -->
    </div>
</form>


<script>
    $(document).ready(function () {
        $('.scroll-down').animate({
            scrollTop: $('#scroll-here').offset().top
        }, 0);
    });

</script>

