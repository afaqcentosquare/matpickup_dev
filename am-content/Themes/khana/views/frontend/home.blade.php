@extends("theme::frontend.master")
@section("content")
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="mobile-category-nav d-lg-none pt-4">
                <!--=======  category menu  =======-->
                <div class="hero-side-category">
                    <!-- Category Toggle Wrap -->
                    <div class="category-toggle-wrap">
                        <!-- Category Toggle -->
                        <button class="more-btn">
                            <span class="lnr lnr-text-align-left"></span>Städer Många
                        </button>
                    </div>

                    <!-- Category Menu -->
                    <nav class="category-menu">
                        <ul>
                            @foreach ($allCities as $allCity)
                            <li><a href="{{route("area",$allCity->slug)}}">{{$allCity->title}}</a></li>
                            @endforeach
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Hero Slider Start -->
<section class="hero-section position-relative">
    <div class="container">
        <div class="row mb-n7">
            <div class="col-xl-3 col-lg-4">
                <div class="vertical-menu d-none d-lg-block">
                    <button class="menu-btn d-flex">
                        <span class="lnr lnr-text-align-left"></span>Städer
                    </button>
                    <ul class="vmenu-content">
                        @foreach ($allCities as $allCity)
                        <li class="menu-item"><a href="{{route("area",$allCity->slug)}}">{{$allCity->title}}</a></li>
                        @endforeach
                    </ul>
                    <!-- menu content -->
                </div>

            </div>
            <div class="col-xl-6 col-lg-8">
                <div class="hero-slider position-relative">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <video width="100%" height="500" loop="true" autoplay="autoplay" controls="controls" id="vid" muted>
                                <source src="https://matpickup.se/movie/matpickup.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                              </video>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-3 offset-lg-4 col-lg-8 offset-xl-0 custom-padding">
                <div class="d-flex custom-flex-column">
                    @php
                        $img_count = 0;
                    @endphp
                    @foreach ($stores as $store_img)
                    @if($img_count == 2)
                    @break;
                    @else
                    @php
                    $img_count++;    
                    @endphp
                        <div class="product-card">
                            <a class="thumb" href="{{route("user.store.products",$store_img->slug)}}">
                                    <img src="{{ asset($store_img->avatar) }}" alt="" width="350px" height="235px">
                                
                            </a>
                            <div class="product-content">
                                <h3 class="product-title">
                                    <br>
                                    <a href="{{route("user.store.products",$store_img->slug)}}">{{$store_img->name}}</a>
                                </h3>
                            </div>
                        </div> --}}
                    {{-- <a class="zoom-in d-block mb-7 me-sm-7 mr-0" href="{{route("user.store.products",$store_img->slug)}}">
                        <img src="" alt="img" width="350px" height="235px"/>
                    </a> --}}
                    {{-- @endif
                    @endforeach
                </div>
            </div> --}}
        </div>
    </div>
</section>
<!-- Hero Slider End -->

<section class="section-pt">
    <div class="container">
        <div class="row g-0">
            @foreach ($allCitiesStores as $allCitiesStore)
            <div class="col-md-3">
                <div class="d-flex custom-flex-column">
                    
                        <div class="product-card">
                            <a class="thumb" href="{{route("area",$allCitiesStore->slug)}}">
                                    <img src="{{ asset($allCitiesStore->preview->content) }}" alt="" width="350px" height="235px">
                                
                            </a>
                            <div class="product-content">
                                <h3 class="product-title">
                                    <br>
                                    <a href="{{route("area",$allCitiesStore->slug)}}">{{$allCitiesStore->title}}</a>
                                </h3>
                            </div>
                        </div>
                    {{-- <a class="zoom-in d-block mb-7 me-sm-7 mr-0" href="{{route("user.store.products",$store_img->slug)}}">
                        <img src="" alt="img" width="350px" height="235px"/>
                    </a> --}}
                   
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<br><br>


<section class="static-media-section bg-primary section-py">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-6 py-3">
                <div class="d-flex static-media flex-column flex-sm-row">
                    <img class="align-self-center mb-2 mb-sm-0 me-auto me-sm-3" src="{{ theme_asset('khana/public/frontend/assets/images/icon/2.png') }}" alt="icon">
                    <div class="media-body">
                        <h4 class="title text-capitalize text-white">Hem Leverans</h4>
                        <p class="text text-white">60 mins</p>
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-3 col-sm-6 py-3">
                <div class="d-flex static-media flex-column flex-sm-row">
                    <img class="align-self-center mb-2 mb-sm-0 me-auto me-sm-3" src="{{ theme_asset('khana/public/frontend/assets/images/icon/3.png') }}" alt="icon">
                    <div class="media-body">
                        <h4 class="title text-capitalize text-white">Logga In Med BankId</h4>
                        <a class="text text-white" href="{{ url('user/login/bankid') }}"><img src="{{ theme_asset('khana/public/frontend/assets/images/banner/bank_id_logo.png') }}"/>
                        </a>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-4 col-sm-6 py-3">
                <div class="d-flex static-media flex-column flex-sm-row">
                    <img class="align-self-center mb-2 mb-sm-0 me-auto me-sm-3" src="{{ theme_asset('khana/public/frontend/assets/images/icon/5.png') }}" alt="icon">
                    <div class="media-body">
                        <h4 class="title text-capitalize text-white">Support 24/7</h4>
                        <p class="text text-white">Kontakta Oss 24 Timmar Om Dygnet</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 py-3">
                <div class="d-flex static-media flex-column flex-sm-row">
                    <img class="align-self-center mb-2 mb-sm-0 me-auto me-sm-3" src="{{ theme_asset('khana/public/frontend/assets/images/icon/4.png') }}" alt="icon">
                    <div class="media-body">
                        <h4 class="title text-capitalize text-white">
                            100% Betalning Säker
                        </h4>
                        <p class="text text-white">Din betalning 100 % säker och krypterat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@foreach ($stores as $store_product)

@if($store_product->productsTerm->count() > 0)
     <!-- Product tab Start -->
 <section class="section-pt">
    <div class="container">
        <div class="row g-0">
            <div class="col-12">
                <div class="title-section text-center text-lg-start">
                    <div class="row">
                        <!-- title section Start -->
                        <div class="col-12 col-lg-4">
                            <h3 class="title">{{$store_product->name}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="tab-content">
            <div class="tab-pane fade show active" id="pots">
                <div class="row mb-n7">
                    {{-- <div class="order-last order-lg-first col-lg-4 col-xl-3 custom-col-20 mb-7">
                        <a class="zoom-in text-center" href="shop-grid-left-sidebar.html">
                            <img src="https://assets.icanet.se/t_product_large_v1,f_auto/7310240075472.jpg" alt="img">
                        </a>
                    </div> --}}
                    <div class="col-lg-12 col-xl-12 mb-7">
                        <div class="row">
                            @foreach ($store_product->productsTerm as $product)
                            <div class="col-lg-3">
                                <div class="media-list mb-4">
                                    <div class="media">
                                        <a class="thumb" href="{{route("single.product",$product->slug)}}">
                                            @if (str_contains($product->content, 'http'))
                                            <img src="{{ $product->content }}" alt="" width="105px" height="105px">
                                            @else
                                            <img src="{{ asset($product->content) }}" alt="img" width="105px" height="105px"/>
                                            @endif
                                            
                                        </a>
                                        <div class="media-body">
                                            <h3 class="product-title">
                                                <a href="{{route("single.product",$product->slug)}}">{{$product->title}}</a>
                                            </h3>
                                            @php
                                            $currency=\App\Options::where('key','currency_name')->select('value')->first();
                                            @endphp
                                            <span class="price-lg regular-price">{{ strtoupper($currency->value) }} {{ number_format($product->price,2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Product tab End -->
@endif
@endforeach
 <!-- adto cart -->
    <!-- Modal -->
    <div class="modal fade" id="postal_search" tabindex="-1" aria-labelledby="add-to-cart">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary border-bottom-0 justify-content-center">
                    <h4 class="modal-title text-center text-white">Search by Postal code or City</h4>
                </div>
                <div class="modal-body p-5">
                    <form action="{{route('resturents.search.city')}}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12 mb-5">
                                <label for="postal_code">Search By Postal Code Or City</label>
                                <input type="text" name="postal_code" class="form-control" placeholder="search by postal code or city" id="postal_code">
                            </div>
                        </div>
                    </div class="row">
                        <div class="cart-content-btn text-center">
                            <button class="btn btn-warning btn-hover-primary text-uppercase" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                            <button type="submit" class="btn btn-warning btn-hover-primary text-uppercase">Submit</button>
                        </div>
                    </div>
                </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
@endsection