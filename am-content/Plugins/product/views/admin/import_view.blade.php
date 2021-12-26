@extends('layouts.backend.app')
@section('content')
<div class="row">
 <div class="col-lg-9">      
  <div class="card">
   <div class="card-body">
     
    <h4>Import Multiple Products</h4>
    <form method="post" class="basicform" action="{{ route('admin.import.excel.data') }}" enctype="multipart/form-data">
     @csrf
     <div class="custom-form pt-20">
     <select class="form-control" name="store">
      <option value="" selected>Select Store</option>
      @foreach ($users as $user)
      @if($user->resturentlocationwithcity != null)
      <option value="{{$user->id}}">{{$user->name." ( ".$user->resturentlocationwithcity->area->title." ) "}}</option>
      @endif
      @endforeach
    </select>
    <br>
       <div class="form-group">
        <label >Select Excel File</label>
        <input type="file" name="file" class="form-control">
      </div>
     </div>
   </div>
 </div>

</div>
<div class="col-lg-3">
  <div class="single-area">
   <div class="card">
    <div class="card-body">
     <h5>{{ __('Publish') }}</h5>
     <hr>
     <div class="btn-publish">
      <button type="submit" class="btn btn-primary col-12 basicbtn"><i class="fa fa-save"></i> Upload File</button>
    </div>
  </div>
</div>
</div>

</div>
</div>


</div>


</form>

@endsection

@section('script')
<script src="{{ asset('admin/js/form.js') }}"></script>
<script>
	"use strict";	
	//success response will assign this function
	function success(res){
		location.reload();
	}
</script>
@endsection