<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MatPickup - Home</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />

    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/vendor/ionicons.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/vendor/linearicons-free.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/vendor/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/plugins/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/plugins/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('khana/public/frontend/assets/css/style.css') }}" />
   <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-1JM11TE35Z"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-1JM11TE35Z');
</script>
    <!-- Use the minified version files listed below for better performance and remove the files listed above -->

    <!--  Minified  css  -->

    <!--  # vendor min css,plugins min css,style min css -->

    <!-- <link rel="stylesheet" href="assets/css/vendor/vendor.min.css" />
<link rel="stylesheet" href="assets/css/plugins/plugins.min.css" />
<link rel="stylesheet" href="assets/css/style.min.css" /> -->
<style>


.remove-style-bankid {
    background:none !important;
    color: none !important;
    font-weight: 0 !important;
    padding: 0px !important;
    border-radius: 0px !important;
    line-height: 0px !important;
    margin-right: 0px !important;
}


.wh-color{
color: white !important;
}

.dropdown-menu{
    opacity: 100 !important;
    visibility: visible !important;
    width: 82%;
}

footer.footer-section {
    background-color: #2b46a8;
}

.footer-menu li .footer-link {
    color: white !important; 
    font-size: 14px;
}

/* ul.adress li{
    color: white !important;
}

ul.adress li a{
    color: white !important;
} */

    .StripeElement {
        box-sizing: border-box;
        
        height: 40px;
        
        padding: 10px 12px;
        
        border: 1px solid transparent;
        border-radius: 4px;
        background-color: white;
        
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>   
<script src="https://js.stripe.com/v3/"></script>
<style>

    
    .error-message-area {
    position: fixed;
    bottom: 4%;
    left: 3%;
    z-index: 9999999999;
    display: none;
    }

    .alert-message-area {
    position: fixed;
    bottom: 4%;
    left: 3%;
    z-index: 99999999999;
    display: none;
    }

    .alert-content {
    background: #001721;
    padding: 25px 42px;
    border-radius: 0;
    }

    .alert-content h4 {
    margin-bottom: 0;
    color: #fff;
    font-size: 18px;
    }

    .error-content {
    background: #ED4956;
    padding: 25px 42px;
    border-radius: 0;
    }

    .error-content h4 {
    margin-bottom: 0;
    color: #fff;
    font-size: 18px;
    }

    

.social-links a {
    background: #222;
    color: #fff;
    font-weight: 500;
    padding: 12px 25px;
    border-radius: 5px;
    line-height: 61px;
    margin-right: 10px;
}
.social-links a.facebook {
    background: #0573E7;
}
.social-links a.google {
    background: #EA4335;
}

body{
    margin: 0;
}
.container {
    width: 85% !important;
}

</style>
</head>

<body>
    <!-- Modal -->
    <div class="modal fade offcanvas-modal" id="exampleModal">
        <div class="modal-dialog offcanvas-dialog">
            <div class="modal-content">
                <div class="modal-header offcanvas-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                @yield("search_form_mobile")
                

                <!-- offcanvas-mobile-menu start -->

                <nav id="offcanvasNav" class="offcanvas-menu">
                    <ul>
                        <li>
                            <a href="{{route('welcome')}}">Hem</a>
                        </li>
                        <li>
                            <a href="{{route('user.all.stores')}}">Stad</a>
                        </li>
                        @if(Auth::guest())
                        <li>
                            <a class="main-menu-link" href="{{route('user.login')}}">Logga In</a>
                        </li>
                        @endif
                        @if(Auth::check())
                        <li>
                            <a class="main-menu-link" href="{{route('author.dashboard')}}">Mitt Konto</a>
                        </li>
                        @endif
                    </ul>
                </nav>
                <!-- offcanvas-mobile-menu end -->
                <div class="header-top">
                    <p>
                        Email:
                        <a class="header-top-link" href="#">info@matpickup.se</a>
                    </p>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- Header  Start -->
    <header>
        <div class="header-top bg-primary d-none d-lg-block">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>Email: <a class="header-top-link" href="#">info@matpickup.se</a></p>
                        <p>Telefonnummer: <a class="header-top-link" href="#">076 418 10 09</a></p>
                    </div>
                </div>
            </div>
        </div>


        <div id="active-sticky" class="header-section">
            <div class="container position-relative">
                <div class="row align-items-center">
                    <!-- Header Logo Start -->
                    <div class="col-6 col-md-3 col-md-3 col-lg-3">
                        <div class="header-logo">
                            <a href="{{route('welcome')}}"><img src="{{asset('uploads/2020-08-03-5f27e25e2a680.png')}}" alt="Site Logo" /></a>
                        </div>
                    </div>
                    <!-- Header Logo End -->

                    <!-- Header Menu Start -->
                    <div class="col-md-6 col-lg-4 d-none d-lg-block">
                        @yield("search_form")
                    </div>
                    <div class="col-6 col-md-9 col-lg-5">
                        <div class="d-flex align-items-center justify-content-end">
                            <nav class="main-menu d-none d-lg-inline-block">
                                <ul class="d-flex">
                                    <li class="main-menu-item">
                                        <a class="main-menu-link" href="{{route('welcome')}}">Hem</a>
                                    </li>
                                    <li class="position-static main-menu-item">
                                        <a class="main-menu-link" href="{{route('user.all.stores')}}">Stad</a>
                                    </li>
                                   
                                    @if(Auth::guest())
                                    <li class="main-menu-item">
                                        <a class="main-menu-link" href="{{route('user.login')}}">Logga In</a>
                                    </li>
                                    @endif
                                    @if(Auth::check())
                                    <li class="main-menu-item">
                                        <a class="main-menu-link" href="{{route('author.dashboard')}}">Mitt Konto</a>
                                    </li>
                                    @endif
                                </ul>
                            </nav>
                            <div class="d-flex align-items-center justify-content-end cart_load">
                                <div class="block-cart-btn-wrapp">
                                    
                                    <button class="cart-action">
                                        
                                        @if(Session::has('restaurant_cart'))
                                        <span class="lnr lnr-cart"></span>
                                        <span class="badge bg-dark count_load">{{ Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->count() }}</span>
                                        @else
                                        <span class="lnr lnr-cart"></span>
                                        <span class="badge bg-dark count_load">0</span>
                                        @endif
                                    </button>
                                    @php
                                    $currency=\App\Options::where('key','currency_name')->select('value')->first();
                                    @endphp
                                    @if(Session::has('restaurant_cart'))
                                    @if(Session::has('cart'))
                                    <div class="checkout-cart">
                                        <ul class="checkout-scroll">
                                            
                                            @if(Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->count() > 0)
                                            @php 
                                            $store = App\User::where('slug',Session::get('restaurant_cart')['slug'])->with('pickup','delivery')->first();
                                            @endphp
                                            @foreach(Cart::instance('cart_'.$store->slug)->content() as $cart)
                                            <li class="checkout-cart-list">
                                                {{-- <div class="checkout-img">
                                                    <img class="product-image" src="assets/images/mini-cart/1.jpg" alt="img" />
                                                    <span class="product-quantity">1x</span>
                                                </div> --}}
                                                <div class="checkout-block">
                                                    <a class="product-name" href="#">{{ $cart->name }}</a>
                                                    <span class="product-price">{{ strtoupper($currency->value) }} {{ number_format($cart->price,2) }}</span>
                                                    {{-- <a class="remove-cart" href="javascript:void(0)" onclick="delete_cart('{{ $cart->rowId }}','{{ $store->slug }}')">
                                                        <i class="fa fa-remove pull-xs-left"></i>
                                                    </a> --}}
                                                    <div class="product-size">
                                                        <span>Antal: {{ $cart->qty }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <div>
                                                
                                            </div>
                                            @endforeach
                                            @endif
                                        </ul>

                                        <ul class="list-group checkout-sub-total">
                                            <li class="list-group-item">
                                                <span>Delsumma</span>
                                                <span>{{ strtoupper($currency->value) }} {{ Cart::subtotal() }}</span>
                                            </li>
                                        </ul>

                                        <!-- checkout-action button start -->
                                        <div class="checkout-action">
                                            <a href="{{route('user.cart',Session::get('restaurant_cart')['slug'])}}" class="btn btn-lg btn-primary d-block">Till Kassan</a>
                                        </div>
                                        <!-- checkout-action button end -->
                                    </div>
                                    @endif
                                    @endif
                                </div>
                                <button class="toggle" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    <span class="icon-top"></span>
                                    <span class="icon-middle"></span>
                                    <span class="icon-bottom"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Header Menu End -->
                </div>
            </div>
        </div>
    </header>
    <!-- Header  End -->


   @yield("content")

    {{-- <footer class="footer-section">
        <div class="footer-top position-relative">
            <div class="container">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="border-bottom section-py">
                            <div class="row mb-n7"> 
                                <div class="text-center">
                                        <h4 class="title">Quick Links</h4>
                                            <a class="footer-link" href="{{ route('welcome') }}">{{ __('Home') }}</a>
                                            <a class="footer-link" href="{{route('user.all.stores')}}">Stad</a>
                                            <a class="footer-link" href="{{ url('/page/privacy-policy') }}">{{ __('Privacy Policy') }}</a>
                                            <a class="footer-link" href="{{ url('/page/refund-return-policy') }}">{{ __('Refund & Return Policy') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- coppy right satrt -->
        <div class="copy-right-section">
            <div class="container">
                <div class="row">
                    
                        <div class="copyright-info text-center text-md-start">
                            <p class="text-center">
                                Copyright &copy; <span id="currentYear"></span>
                                <a href="{{ route('welcome') }}">MatPickup</a> Sweden AB.
                            </p>
                        </div>
                </div>
            </div>
        </div>
        <!-- coppy right end -->
    </footer> --}}


    <footer class="footer-section">
        <div class="footer-top position-relative">
            <div class="container">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="border-bottom section-py">
                            <div class="row mb-n7">
                                <div class="col-lg-4 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <a class="footer-logo mb-8" href="{{route('welcome')}}">
                                            <img src="{{asset('uploads/2020-08-03-5f27e25e2a680.png')}}" alt="footer-logo" />
                                        </a>
                                        <ul class="adress">
                                            <li class="wh-color"><span class="text-dark fw-500 wh-color">Adress:</span> <p>Läroverksgatan 8 <br>641 36 Katrineholm</p></li>
                                            <li class="wh-color"><span class="text-dark fw-500 wh-color">Org.No:</span> 559299-4544</li>
                                            
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <h4 class="title wh-color">Viktig Information</h4>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="{{ url('/page/privacy-policy') }}">GDPR</a></li>
                                            <li><a class="footer-link" href="{{ url('/page/terms-and-conditions') }}">Köpvillkor</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <h4 class="title wh-color">MatPickup</h4>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="{{ url('/page/abut-us') }}">Om Oss</a></li>
                                            <li><a class="footer-link" href="{{url('/restaurant/register')}}">Öppna Webbutik</a></li>
                                            <li><a class="footer-link" href="{{ url('/rider/register') }}">Jobba Hos Oss</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <h4 class="title wh-color">Hitta Snabbt</h4>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="{{route('welcome')}}">Hem</a></li>
                                            <li><a class="footer-link" href="{{route('user.all.stores')}}">Stad</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- coppy right satrt -->
        <div class="copy-right-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 order-last order-md-first">
                        <div class="copyright-info text-center text-md-start">
                            <p class="wh-color">
                                © Upphovsrätt 2021 <a href="{{route('welcome')}}">MatPickup</a> Sweden AB.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 text-center text-md-end mb-3 mb-md-0">
                        <img src="{{ theme_asset('khana/public/frontend/assets/images/payment.png') }}" alt="images">
                    </div>
                </div>
            </div>
        </div>
        <!-- coppy right end -->
    </footer>


    {{-- <footer class="footer-section">
        <div class="footer-top position-relative">
            <div class="container">
                <div class="row g-0">
                    <div class="col-12">
                        <div class="border-bottom section-py">
                            <div class="row mb-n7 text-center">
                                <div class="col-lg-4 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <a class="footer-logo mb-8" href="index.html">
                                            <img src="https://matpickup.se/uploads/2020-08-03-5f27e25e2a680.png" alt="footer-logo" />
                                        </a>
                                        <p>
                                            We are a team of designers and developers that create high quality
                                            Magento, Prestashop, Opencart.
                                        </p>
                                        <ul class="adress">
                                            <li><span class="text-dark fw-500">Address:</span> 4710-4890 Breckinridge St,Fayetteville</li>
                                            <li><span class="text-dark fw-500">Email:</span> <a href="mailto:support@hasthemes.com">support@hasthemes.com</a></li>
                                            <li><span class="text-dark fw-500">Call us:</span><a href="tel:110012345678"><span class="phone-call">1-1001-234-5678</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <h4 class="title wh-color">Viktig Information</h4>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="{{ url('/page/privacy-policy') }}">GDPR</a></li>
                                            <li><a class="footer-link" href="#">Köpvillkor</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <a class="footer-logo mb-8" href="{{route('welcome')}}">
                                            <img src="{{asset('uploads/2020-08-03-5f27e25e2a680.png')}}" alt="footer-logo" />
                                        </a>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="#">Om Oss</a></li>
                                            <li><a class="footer-link" href="{{url('/restaurant/register')}}">Öppna Webbutik</a></li>
                                            <li><a class="footer-link" href="{{ url('/rider/register') }}">Jobba Hos Oss</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 mb-7">
                                    <div class="footer-widget">
                                        <h4 class="title wh-color">Quick Links</h4>
                                        <ul class="footer-menu">
                                            <li><a class="footer-link" href="{{route('welcome')}}">Hem</a></li>
                                            <li><a class="footer-link" href="{{route('user.all.stores')}}">Stad</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- coppy right satrt -->
        <div class="copy-right-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 order-last order-md-first">
                        <div class="copyright-info text-center text-md-start">
                            <p class="wh-color">
                                © Copyright 2021 <a href="{{route('welcome')}}">MatPickup</a> Sweden AB.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 text-center text-md-end mb-3 mb-md-0">
                        <img src="{{ theme_asset('khana/public/frontend/assets/images/payment.png') }}" alt="images">
                    </div>
                </div>
            </div>
        </div>
        <!-- coppy right end -->
    </footer> --}}





   

   

    <!-- <script src="assets/js/vendor/vendor.min.js"></script>
<script src="assets/js/plugins/plugins.min.js"></script>
<script src="assets/js/ajax-contact.js"></script>
<script src="assets/js/main.js"></script> -->

    <!-- Use the minified version files listed below for better performance and remove the files listed above -->

    <!-- #  Minified  js  -->

    <!-- vendor,plugins and main js -->

    <script src="{{ theme_asset('khana/public/frontend/assets/js/vendor/vendor.min.js') }}"></script>
    <script src="{{ theme_asset('khana/public/js/vendor/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ theme_asset('khana/public/frontend/assets/js/plugins/plugins.min.js') }}"></script>
    <script src="{{ theme_asset('khana/public/frontend/assets/js/ajax-contact.js') }}"></script>
    <script src="{{ theme_asset('khana/public/frontend/assets/js/main.min.js') }}"></script>
    {{-- <script src="{{ theme_asset('khana/public/js/store/cart.js') }}"></script> --}}
    <script>

        var base_url = window.location.origin;
        console.log(base_url);
        let store_pure_id = $('#store_pure_id').val();
        console.log(store_pure_id);
        $(document).on('click', '.pagination a', function(event){
        event.preventDefault(); 
        console.log($(this).attr('href'));
        page = $(this).attr('href').split('page=')[1];
        
       // is_product_url = $(this).attr('href').split('https://matpickup.com/user/store/product/');
       is_product_url = $(this).attr('href').split(base_url+'/user/store/')[1];
        if(is_product_url.split("/")[0] == "product"){
            window.location.href = $(this).attr('href');
        }else{
        slug_part_1 = $(this).attr('href').split(base_url+'/user/store/category/')[1];
        slug = slug_part_1.split('?page=')[0];
        
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0;
        $.ajax({
                type: "GET",
                beforeSend: function()
                {
                $('#loading-div').css("visibility", "visible");
                $('#product_data').css("visibility", "hidden");
                },
                dataType: "json",
                url:"/user/store/category/"+slug+"?page="+page,
                data: {'slug': slug,'store_pure_id': store_pure_id},
                success: function (response) {
                    $("#product_data").html(response.products);
                    
                },  
                    error: function(xhr, status, error) {
                        console.log(error);
                },
                complete: function(){
                    $('#loading-div').css("visibility", "hidden");
                    $('#product_data').css("visibility", "visible");
                }
                });
        }
        
       
        });
        function getData(val) {
            event.preventDefault();
        $.ajax({
                type: "GET",
                beforeSend: function()
                {
                $('#loading-div').css("visibility", "visible");
                $('#product_data').css("visibility", "hidden");
                },
                dataType: "json",
                url:"/user/store/category/"+val.getAttribute('value'),
                data: {'slug': val.getAttribute('value'),'store_pure_id': store_pure_id},
                success: function (response) {
                    $("#product_data").html(response.products);
                    
                },  
                    error: function(xhr, status, error) {
                        console.log(error);
                },
                complete: function(){
                    $('#loading-div').css("visibility", "hidden");
                    $('#product_data').css("visibility", "visible");
                }
                });
            }

            $('#user_settings_form').on('submit',function(e){
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
                    if(response.error)
                    {
                        $('.error-message-area').fadeIn();
                        $('.error-msg').html(response.error);
                        $(".error-message-area").delay( 2000 ).fadeOut( 2000 );
                    }

                    if(response == 'ok')
                    {
                        $('.alert-message-area').fadeIn();
                        $('.ale').html('Settings successfully updated');
                        $(".alert-message-area").delay( 2000 ).fadeOut( 2000 );
                    }
                }
            })
        });

        function product_add_to_cart(slug,store_slug) {
            var url = $('#add_to_cart_url').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'GET',
                url: url,
                data: {slug:slug,store_slug:store_slug},
                dataType: 'HTML',
                success: function(response){ 
                    if(response == 'ok')
                    {
                        $('.cart_load').load(' .cart_load >*');
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
        }

        function limit_plus(id,store_slug) {
        var l = $('#total_limit'+id).val();
        l++;
        document.getElementById('total_limit'+id).value = l;
        var url = $('#cart_update').val();
        var data_value = $('#total_limit'+id).val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'GET',
            url: url,
            data: {id:id,data_value:data_value,store_slug:store_slug},
            dataType: 'HTML',
            success: function(response){ 
                if(response == 'ok')
                {
                    $('.main_cart_ok').load(' .main_cart_ok >*');
                    $('.cart_load').load(' .cart_load >*');
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
    }

    function limit_minus(id,store_slug) {
        var l = $('#total_limit'+id).val();
        if($('#total_limit'+id).val() > 1)
        {
            l--;
            document.getElementById('total_limit'+id).value = l;
            var url = $('#cart_update').val();
            var data_value = $('#total_limit'+id).val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'GET',
                url: url,
                data: {id:id,data_value:data_value,store_slug:store_slug},
                dataType: 'HTML',
                success: function(response){ 
                    if(response == 'ok')
                    {
                        $('.main_cart_ok').load(' .main_cart_ok >*');
                        $('.cart_load').load(' .cart_load >*');
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
        }
    }


    function delete_cart(id,store_slug)
    {
        if(confirm('Are you want to delete this product from cart?'))
        {
            var url = $('#cart_delete').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'GET',
                url: url,
                data: {id:id,store_slug:store_slug},
                dataType: 'HTML',
                success: function(response){ 
                    if(response == 'ok')
                    {
                        $('.main_cart_ok').load(' .main_cart_ok >*');
                        $('.cart_load').load(' .cart_load >*');
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
        }
    }


    $('#bankIdForm').on('submit',function(e){
        e.preventDefault();

        let personal_number = $('#personal_number').val();

        $.ajax({
          url: "/user/bankid",
          type:"POST",
          data:{
            "_token": "{{ csrf_token() }}",
            personal_number:personal_number,
          },
          beforeSend: function()
          {
            $('#loading-div').css("visibility", "visible");
            $('#bankIdForm').css("visibility", "hidden");
          },
          success:function(response){
           // var returnedData = JSON.parse(response.data);
            if(response.data.authResponse.Success == true && response.data.apiCallResponse.Success == true){
                
                    $.ajax({
                        url: "/user/bankid/collectstatus",
                        type:"POST",
                        data:{
                            "_token": "{{ csrf_token() }}",
                            orderRef:response.data.apiCallResponse.Response.OrderRef,
                            personal_number:personal_number
                        },
                        success:function(response){
                            if(response.status == 200){
                                window.location = '{{ route('welcome') }}';
                            }else{
                                console.log(response);
                                $('#loading-div').css("visibility", "hidden");
                                $('#bankIdForm').css("visibility", "visible");
                                $("#bankid_error").text("failed to login");
                                
                            }
                        // var returnedData = JSON.parse(response.data);
                        },  
                        error: function(xhr, status, error) {
                            $('#loading-div').css("visibility", "hidden");
                            $('#bankIdForm').css("visibility", "visible");
                            $("#bankid_error").text("failed to login");
                                console.log(error);
                        },
                        complete: function(){
                            $('#loading-div').css("visibility", "hidden");
                            $('#bankIdForm').css("visibility", "visible");
                        }
                        });
            }
            
          },  
          error: function(xhr, status, error) {
            $('#loading-div').css("visibility", "hidden");
            $('#bankIdForm').css("visibility", "visible");
            $("#bankid_error").text("failed to login");
            console.log(error);
          }
         });
        });


    // function bankIdLogin(val) {
    //         event.preventDefault();
    //     $.ajax({
    //             type: "GET",
    //             beforeSend: function()
    //             {
    //             $('#loading-div').css("visibility", "visible");
    //             $('#product_data').css("visibility", "hidden");
    //             },
    //             dataType: "json",
    //             url:"/user/store/category/"+val.getAttribute('value'),
    //             data: {'slug': val.getAttribute('value')},
    //             success: function (response) {
    //                 $("#product_data").html(response.products);
                    
    //             },  
    //                 error: function(xhr, status, error) {
    //                     console.log(error);
    //             },
    //             complete: function(){
    //                 $('#loading-div').css("visibility", "hidden");
    //                 $('#product_data').css("visibility", "visible");
    //             }
    //             });
    //         }


    </script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>  
<script type="text/javascript">
    var path = "{{ route('autocomplete') }}";
    var search_input = document.getElementById("typehead");
    $('input.typeahead').typeahead({
        source:  function (query, process) {
            return $.get(path, { query: query }, function (data) {
                return process(data);
            });
        },
        afterSelect :function (item){
            console.log(item);
            document.getElementById("selected_serach_text").value = item.slug;
        },
        highlighter: function (item, data) {
            var parts = item.split('#'),
                html = '<div class="row">';
                html += '<div class="col-lg-12">';
                html += '<p class="m-0">'+data.name+'</p>';
                html += '</div>';
                html += '</div>';
                
                // html = '<ul>ghello</ul>';
                // document.getElementById("demo").innerHTML = "<ul>ghello</ul>";
                //console.log(item);
                document.getElementById("selected_serach_text").value = null;
                return html;
        }
    });

    var search_input_mob = document.getElementById("typeahead_mob");
    $('input.typeahead_mob').typeahead({
        source:  function (query, process) {
            return $.get(path, { query: query }, function (data) {
                return process(data);
            });
        },
        afterSelect :function (item){
            console.log(item);
            document.getElementById("selected_serach_text_mob").value = item.slug;
        },
        highlighter: function (item, data) {
            var parts = item.split('#'),
                html = '<div class="row">';
                html += '<div class="col-lg-12">';
                html += '<p class="m-0">'+data.name+'</p>';
                html += '</div>';
                html += '</div>';
                
                // html = '<ul>ghello</ul>';
                // document.getElementById("demo").innerHTML = "<ul>ghello</ul>";
                //console.log(item);
                return html;
        }
    });
 
</script>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#postal_search').modal('show');
    });
</script>

@yield("cart_script")



      
</body>

</html>