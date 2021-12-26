@extends("theme::frontend.master")
@section("content")
<!-- bread-crumb2 start -->
<nav class="breadcrumb-section">
    <div class="container wrapper">
        <div class="row">
            <div class="col-12">
                {{-- <ol class="breadcrumb bg-transparent m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="{{route('welcome')}}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$single_product->title}}</li>
                </ol> --}}
            </div>
        </div>
    </div>
</nav>
<!-- bread-crumb2 start -->
<div class="single-product-wrap">
    <div class="container wrapper">
        <div class="row mb-n10">
            @if($single_product != null)
            <div class="col-lg-5 mb-10">
                <div class="single-img">
                    @if($single_product->preview != null)
                    @if (str_contains($single_product->preview->content, 'http'))
                        <img src="{{ $single_product->preview->content }}" alt="">
                        @else
                        <img src="{{ asset($single_product->preview->content) }}" alt="img" />
                    @endif
                    @else
                    <img src="{{ asset($single_product->preview->content) }}" alt="img" />
                    @endif
                </div>
            </div>
            <div class="col-lg-7 mb-10">
                <div class="content">
                    <h3 class="title">{{$single_product->title}}</h3>
                    <div class="mb-4">
                        @php
                        $currency=\App\Options::where('key','currency_name')->select('value')->first();
                        @endphp
                        <span class="price-lg regular-price d-inline-block mx-1">{{ strtoupper($currency->value) }} {{ number_format($single_product->price->price,2) }}</span>
                    </div>
                    <p class="border-bottom pb-4">
                        {!!$single_product->excerpt->content!!}
                    </p>

                    {{-- <h4 class="modal-quantity">Quantity</h4> --}}
                    <div class="product-count style d-flex my-4">
                        {{-- <div class="count d-flex">
                            <input type="number" min="1" max="100" step="1" value="1" />
                            <div class="button-group">
                                <button class="count-btn increment">
                                    <span class="lnr lnr-chevron-up"></span>
                                </button>
                                <button class="count-btn decrement">
                                    <span class="lnr lnr-chevron-down"></span>
                                </button>
                            </div>
                        </div> --}}
                        <div>
                            {{-- <button data-bs-toggle="modal" data-bs-target="#add-to-cart" class="btn btn-warning btn-hover-primary text-uppercase">
                                Add to cart
                            </button> --}}
                            <input type="hidden" id="add_to_cart_url" value="{{url('add_to_cart')}}">
                            <a href="javascript:void(0)" onclick="product_add_to_cart('{{ $single_product->slug }}','{{ $single_product->user->slug }}')" class="btn btn-warning btn-hover-primary text-uppercase">
                                Lägg i kundvagn
                            </a>
                        </div>
                    </div>
                    {{-- <div>
                        <a href="#">Add to wishlist</a>
                        <a class="mx-2" href="#">My wishlist</a>
                    </div> --}}
                </div>
            </div>
            @else
            <h1>No Product Found :(</h1>
            <br><br><br><br><br><br>
            @endif
            
        </div>
    </div>
</div>

<br><br>

<!-- Product tab Start -->
<section class="section section-pb">
    <div class="container wrapper">
        <div class="row">
            <div class="col-12">
                <div class="title-section">
                    <!-- title section Start -->
                    <h3 class="title">related products</h3>
                    <!-- title section End -->
                </div>
            </div>

            <div class="col-12">
                <div class="product-carousel6">
                    <div class="d-none d-sm-block swiper-navination-arrows">
                        <div class="swiper-button-prev">
                            <span class="ion-android-arrow-back"></span>
                        </div>
                        <div class="swiper-button-next">
                            <span class="ion-android-arrow-forward"></span>
                        </div>
                    </div>
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($related_products as $related_product)

                            @if($related_product->preview != null && $related_product->price != null)
                                <!-- single slide Start -->
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <a class="thumb" href="{{route("single.product",$related_product->slug)}}">
                                        @if (str_contains($related_product->preview->content, 'http'))
                                            <img src="{{ $related_product->preview->content }}" alt="" >
                                            @else
                                            <img src="{{ asset($related_product->preview->content) }}" alt="img" />
                                        @endif

                                        <div class="onsales-badges">
                                            <span class="badge bg-dark">new</span>
                                        </div>
                                    </a>
                                    <div class="product-content">
                                        {{-- <a class="product-category" href="#?">Graphic Corner</a> --}}
                                        <h3 class="product-title">
                                            <a href="{{route("single.product",$related_product->slug)}}">{{$related_product->title}}</a>
                                        </h3>
                                        @php
                                        $currency=\App\Options::where('key','currency_name')->select('value')->first();
                                        @endphp
                                        <span class="price regular-price">{{ strtoupper($currency->value) }} {{ number_format($related_product->price->price,2) }}</span>
                                        <a class="product-btn btn btn-warning btn-hover-primary" href="javascript:void(0)" onclick="product_add_to_cart('{{ $related_product->slug }}','{{ $related_product->user->slug }}')">
                                            Lägg i kundvagn
                                        </a>
                                    </div>
                                    <!-- actions links start -->
                                    {{-- <ul class="actions">
                                        <li class="action-item"><button class="action quick-view" data-bs-toggle="modal" data-bs-target="#quickview"><span class="lnr lnr-magnifier"></span></button></li>
                                        <li class="action-item"><button class="action wishlist" data-bs-toggle="modal" data-bs-target="#addtowishlist"><span class="lnr lnr-heart"></span></button></li>
                                    </ul> --}}
                                    <!-- actions links end -->
                                </div>
                            </div>
                            <!-- single slide End -->
                            @endif
                            @endforeach
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Product tab End -->
@endsection