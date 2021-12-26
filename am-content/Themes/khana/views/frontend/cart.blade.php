@extends("theme::frontend.master")
@section("content")
@php
$currency=\App\Options::where('key','currency_name')->select('value')->first();
@endphp
<nav class="breadcrumb-section section-py bg-light2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="bread-crumb-title">Varukorg</h3>
                {{-- <ol class="breadcrumb bg-transparent justify-content-center m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Din Varukorg</li>
                </ol> --}}
            </div>
        </div>
    </div>
</nav>


<div class="check-out-section section-pb main_cart_ok">
    @if(Session::has('restaurant_cart'))
    @if(Session::has('cart'))
    @if(Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->count() > 0)
    @php 
    $store = App\User::where('slug',Session::get('restaurant_cart')['slug'])->with('pickup','delivery')->first();
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="billing-info-wrap">
                    <br><br>
                    <h3 class="title">Leveransinformation</h3>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="personal-information" action="{{ route('order.create') }}" method="POST">
                        @csrf
                       
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="billing-info">
                                    <label for="first_name">Namn</label>
                                    <input class="form-control" name="name" id="first_name" placeholder="Namn" type="text" value="{{ Auth::user()->name ?? '' }}">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="billing-info">
                                    <label for="phone">Telefonnummer</label>
                                    <input autocomplete="off" type="number" class="form-control" name="phone" id="phone" placeholder="Telefonnummer" required autocomplete="off">
                                </div>
                            </div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ $resturent_info->resturentlocation->latitude }}">
									<input type="hidden" name="longitude" id="longitude" value="{{ $resturent_info->resturentlocation->longitude }}">
                                    <input type="hidden" name="payment_method" value="stripe" id="stripe">


									<div class="col-lg-12">
										<div class="billing-info">
											<label for="billing">Leveransadress</label>
											<input  type="text" class="form-control location_input" autocomplete="off" id="location_input" placeholder="Leveransadress" name="delivery_address" required>
										</div>
									</div>
                                    {{-- <input type="hidden" name="shipping" id="shipping" value="130.00"> --}}
                                    <input type="hidden" name="shipping" id="shipping" value="80.00">



									<div class="col-lg-12">
										<div class="billing-info">
											<div class="map-canvas" id="map-canvas">

											</div>
											<input type="hidden" name="shipping" id="shipping">
										</div>
									</div>
                            <input type="hidden" name="order_type" value="1" >
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ number_format(str_replace(',', '', Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->total()),2) }}">
                            <div class="col-lg-12">
                                <div class="billing-info">
                                    <label for="order_details">Ordning Notera</label>
                                    <textarea name="note" class="form-control" name="order_note" id="order_details" cols="5" rows="5" maxlength="200" placeholder="Ordning Notera"></textarea>
                                </div>
                            </div>
                            {{-- <div class="col-12">
                                <h3 class="coupon-title">Discount coupon Code</h3>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="billing-info">
                                    <input class="form-control" placeholder="coupon Code" type="text">
                                </div>
                            </div> --}}
                            
                        </div>
                        <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <button type="submit" class="btn btn-warning btn-hover-primary text-capitalize check-out-btn" style="width: 100%; border:none; display:inline-block;">KASSA</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                
                <section class="whish-list-section section-py">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h3 class="title mb-5 pb-3 text-capitalize">Din Varukorg</h3>
                                
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center" scope="col">Produktnamn</th>
                                                <th class="text-center" scope="col">Antal</th>
                                                <th class="text-center" scope="col">Pris</th>
                                                <th class="text-center" scope="col">Ta bort</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(Cart::instance('cart_'.$store->slug)->content() as $cart)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="whish-title">{{ $cart->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="product-count style">
                                                        <div class="count d-flex justify-content-center">
                                                            <input type="text" id="total_limit{{ $cart->rowId }}" value="{{ $cart->qty }}">
                                                            <div class="button-group">
                                                                <a href="javascript:void(0)" class="count-btn" onclick="limit_plus('{{ $cart->rowId }}','{{ $store->slug }}')">
                                                                    <i class="lnr lnr-chevron-up"></i>
                                                                </a>
                                                                <a href="javascript:void(0)" class="count-btn" onclick="limit_minus('{{ $cart->rowId }}','{{ $store->slug }}')">
                                                                    <i class="lnr lnr-chevron-down"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="whish-list-price">{{ strtoupper($currency->value) }} {{ number_format($cart->price,2) }}</span>
                                                </td>

                                                <td class="text-center">
                                                    <a href="javascript:void(0)" onclick="delete_cart('{{ $cart->rowId }}','{{ $store->slug }}')">
                                                        <span class="trash"><i class="ion-android-delete"></i> </span></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </section>
                <div class="your-order-area">
                    <div class="your-order-wrap gray-bg-4">
                        <div class="your-order-product-info">
                            <div class="your-order-top">
                                <ul>
                                    <li>Delsumma</li>
                                    <li>{{ $currency->value }}: {{ Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->priceTotal() }}</li>
                                </ul>
                            </div>

                            <div class="your-order-total mb-0">
                                <ul>
                                    <li class="order-total">Leveransavgift (inkl. Moms)</li>
                                    {{-- <li>{{ $currency->value }}: 130.00</li> --}}
                                    <li>{{ $currency->value }}: 80.00</li>
                                </ul>
                            </div>
                            {{-- <div class="your-order-top mt-3">
                                <ul>
                                    <li class="order-total">Skatteavgift</li>
                                    <li>{{ $currency->value }}: {{ Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->tax() }}</li>
                                </ul>
                            </div> --}}
                            <div class="your-order-total mb-0">
                                <ul>
                                    <li class="order-total">Total Summa (inkl. Moms)</li>
                                    {{-- <li>{{ $currency->value }}: <span>{{ number_format(Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->total()+130.00,2) }}</li> --}}
                                        <li>{{ $currency->value }}: <span>{{ number_format(Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->total()+80.00,2) }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="Place-order mt-5">
                        <a class="btn btn-warning btn-hover-primary text-capitalize me-3" href="#">update cart</a>
                        <a class="btn btn-warning btn-hover-primary text-capitalize my-2 my-sm-0" href="#">checkout</a>
                    </div> --}}
                </div>
                
                
            </div>
        </div>
    </div>
    @else
    <div class="login-register-area section-py">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-12 mx-auto">
                    <div class="login-register-wrapper">

                        <div class="login-form-container">
                            <div class="login-register-form">
                                <div class="row">
                                    <div class="text-center">
                                        <h2>Du har inget föremål i din kundvagn</h2>
                                        <br>
                                        <a href="{{route('welcome')}}" class="btn btn-warning btn-hover-primary text-capitalize check-out-btn">Tillbaka Till Hem</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
    @endif
</div>
<input type="hidden" id="cart_update" value="{{ route('cart.update') }}">
<input type="hidden" id="cart_delete" value="{{ route('cart.delete') }}">
@endsection

@section('cart_script')
<script>
    $('#place_order_form').on('submit',function(){
       $('#place_order_button').attr('disabled','');
       $('#place_order_button').html('Please wait....');
    });
        //coupon form submit
        $('#couponform').on('submit',function(e){
       e.preventDefault();
       $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
       });
       $.ajax({
           type: 'POST',
           url: this.action,
           data: new FormData(this),
           dataType: 'json',
           contentType: false,
           cache: false,
           processData:false,

           success: function(response){ 
               if(response.message)
               {
                   $('#checkout_right').load(' #checkout_right');
                   $('.alert-message-area').fadeIn();
                   $('.ale').html(response.message);
                   $(".alert-message-area").delay( 2000 ).fadeOut( 2000 );
                   window.location.reload();
               }

               if(response.error)
               {
                   $('.error-message-area').fadeIn();
                   $('.error-msg').html(response.error);
                   $(".error-message-area").delay( 2000 ).fadeOut( 2000 );
               }

           },
           error: function(xhr, status, error) 
           {
               $('.errorarea').show();
               $.each(xhr.responseJSON.errors, function (key, item) 
               {
                   Sweet('error',item)
                   $("#errors").html("<li class='text-danger'>"+item+"</li>")
               });
               errosresponse(xhr, status, error);
           }
       })
   });

// $("body").on("contextmenu",function(e){
// return false;
// });
$(document).keydown(function(e){
if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)){
return false;
}
if(e.which === 123){
return false;
}
if(e.metaKey){
return false;
}
//document.onkeydown = function(e) {
// "I" key
if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
return false;
}
// "J" key
if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
return false;
}
// "S" key + macOS
if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
return false;
}
if (e.keyCode == 224 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
return false;
}
// "U" key
if (e.ctrlKey && e.keyCode == 85) {
return false;
}
// "F12" key
if (event.keyCode == 123) {
return false;
}
});
</script> 



<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('PLACE_KEY') }}&libraries=places&callback=initialize"></script>
<script>
   "use strict";
   if (localStorage.getItem('location') != null) {
       var locs= localStorage.getItem('location');
   }
   else{
       var locs = "{{ $json->full_address }}";
   }
   $('#location_input').val(locs);
   if (localStorage.getItem('lat') !== null) {
       var lati=localStorage.getItem('lat');
       $('#latitude').val(lati)
   }	
   else{
       var lati= {{ $resturent_info->resturentlocation->latitude }};
   }

   if (localStorage.getItem('long') !== null) {
       var longlat=localStorage.getItem('long');
       $('#longitude').val(longlat)
   }
   else{
       var longlat= {{ $resturent_info->resturentlocation->longitude }};

   }


   var resturentlocation="{{ $json->full_address }}";
   var feePerkilo= {{ $km_rate->value }};
   var mapOptions;
   var map;
   var marker;
   var searchBox;
   var city;


</script>
<script src="{{ theme_asset('khana/public/js/checkout/map.js') }}"></script>       
@endsection


