
@extends('layouts.admin.app')

@section('title','Add new notification')

@push('css_or_js')

@endpush

@section('content')
  <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
  <thead>
    <tr>
      <th class="th-sm">Id

      </th>
      <th class="th-sm">Name

      </th>
      <th class="th-sm">Office

      </th>
      <th class="th-sm">Age

      </th>
      <th class="th-sm">Start date

      </th>
      <th class="th-sm">Salary

      </th>
    </tr>
  </thead>
  <tbody>
      @foreach($usersnotify as $usernotify)
    <tr>
      <td>{{$usernotify -> id}}</td>
      <td>{{$usernotify -> f_name}}</td>
     
     
    </tr>
   @endforeach
  </tbody>
 
  <tfoot>
    <tr>
      <th>Name
      </th>
      <th>Position
      </th>
      <th>Office
      </th>
      <th>Age
      </th>
      <th>Start date
      </th>
      <th>Salary
      </th>
    </tr>
  </tfoot>
</table>
@endsection
