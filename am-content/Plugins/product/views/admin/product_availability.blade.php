@extends('layouts.backend.app')
@section('content')
@include('layouts.backend.partials.headersection',['title'=>'Product Checker'])

<div class="row">
    <div class="col-lg-9">      
     <div class="card">
      <div class="card-body">
       <form method="post"  action="{{ route('admin.check.product.availability.now') }}" enctype="multipart/form-data">
        @csrf
		@if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
		@if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="custom-form pt-20">
        <select class="form-control" name="store">
         <option value="" selected>Select Store</option>
         @foreach ($users as $user)
           <option value="{{$user->id}}">{{$user->name." ( ".$user->resturentlocationwithcity->area->title." ) "}}</option>
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
       <h5>Check Now</h5>
       <hr>
       <div class="btn-publish">
        <button type="submit"  name="check_availability" value="submit" class="btn btn-primary col-12 basicbtn"><i class="fa fa-save"></i>Check Availability</button>
		<br><br>
		<button type="submit" name="check_unavailability" value="save" class="btn btn-primary col-12 basicbtn"><i class="fa fa-save"></i>Check UnAvailability</button>
		<br><br>
		@if($table_type != "availability")
		<button type="submit" name="upload_products" value="upload_products" class="btn btn-primary col-12 basicbtn"><i class="fa fa-save"></i>Upload Products</button>
		@endif
      </div>
      
    </div>
  </div>
  </div>
  
  </div>

</div>





<div class="row">
	<div class="col-12 mt-2">
		<div class="card">
			@if($table_type == "availability")
			<div class="card-body">
					<table class="table">
						<thead>
							<tr>
								<th class="am-select">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
										<label class="custom-control-label" for="checkAll"></label>
									</div>
								</th>
								<th class="am-title"><i class="far fa-image"></i></th>
								<th class="am-title">{{ __('Title') }}</th>

								<th class="am-tags">{{ __('Price') }}</th>

								<th class="am-tags">{{ __('Total Sales') }}</th>
								<th class="am-tags">{{ __('Status') }}</th>

								<th class="am-date">{{ __('Last Modified') }}</th>
								<th class="am-date">{{ __('View') }}</th>

							</tr>
						</thead>
						<tbody>
							@foreach($posts as $post)
							@if ($post->price != null)
							<tr>
								<th>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $post->id }}" value="{{ $post->id }}">
										<label class="custom-control-label" for="customCheck{{ $post->id }}"></label>
									</div>
								</th>
								<td><img src="{{ asset($post->preview->content) }}" height="50" alt=""></td>
								<td>
									{{ $post->title }}
								</td>

								<td>{{ number_format($post->price->price,2) }}</td>
								<td>{{ $post->order_count }}</td>
								<td>@if($post->status==1)  Published @elseif($post->status==2)  {{ __('Draft') }} @else {{ __('Trash') }} @endif</td>
								<td>{{ __('Last Modified') }}
									<div class="date">
										{{ $post->updated_at->diffForHumans() }}
									</div>
								</td>
								<td><a href="{{ url('/store/'.$post->user->slug) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a></td>
							</tr>
							@endif
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th class="am-select">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
									<label class="custom-control-label" for="checkAll"></label>
								</div>
							</th>
							<th class="am-title"><i class="far fa-image"></i></th>
							<th class="am-title">{{ __('Title') }}</th>
							<th class="am-tags">{{ __('Price') }}</th>
							<th class="am-tags">{{ __('Total Sales') }}</th>
							<th class="am-tags">{{ __('Status') }}</th>

							<th class="am-date">{{ __('Last Modified') }}</th>
							<th class="am-date">{{ __('View') }}</th>
						</tr>
					</tfoot>
				</table>
				{{ $posts->links() }}
			</div>
			@else
			<div class="card-body">
				<table class="table">
					
					<thead>
						<tr>
							<th class="am-select">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
									<label class="custom-control-label" for="checkAll"></label>
								</div>
							</th>
							<th class="am-title"><i class="far fa-image"></i></th>
							<th class="am-title">ID</th>
							<th class="am-title">{{ __('Title') }}</th>

							<th class="am-tags">{{ __('Price') }}</th>

						</tr>
					</thead>
					<tbody>
						@foreach($posts as $post)
						<tr>
							<th>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="product_data[]" class="custom-control-input" id="customCheck{{ $post["id"] }}" 
									value="{{ $post["store_id"]."(--$$--)".$post["title"]."(--$$--)".$post["description"]."(--$$--)".$post["price"]."(--$$--)".$post["image"] }}">
									<label class="custom-control-label" for="customCheck{{ $post["id"] }}"></label>
								</div>
							</th>
							<td>{{$post["id"]}}</td>
							<td><img src="{{ asset($post["image"]) }}" height="50" alt=""></td>
							<td>
								{{ $post["title"] }}
							</td>

							<td>{{ number_format($post["price"],2) }}</td>
							
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<th class="am-select">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input checkAll" id="checkAll">
								<label class="custom-control-label" for="checkAll"></label>
							</div>
						</th>
						<th class="am-title"><i class="far fa-image"></i></th>
						<th class="am-title">ID</th>
						<th class="am-title">{{ __('Title') }}</th>
						<th class="am-tags">{{ __('Price') }}</th>
					</tr>
				</tfoot>
			</table>
		</div>
			@endif
		</div>
	</div>
</div>
</form>
@endsection

@section('script')
<script src="{{ asset('admin/js/custom_check.js') }}"></script>

@endsection