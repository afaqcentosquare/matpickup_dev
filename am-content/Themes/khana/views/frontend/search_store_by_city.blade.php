@extends("theme::frontend.master")
@section("content")
<nav class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{-- <ol class="breadcrumb bg-transparent m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stad</li>
                </ol> --}}
            </div>
        </div>
    </div>
</nav>

<div class="shop-product-tab section-pb">
    <div class="container">
        <div class="row mb-n7">
            <div class="col-lg-12 col-xl-12">
                {{-- <img class="mb-4" src="{{ theme_asset('khana/public/frontend/assets/images/banner/shop-banner.jpg') }}" alt="banner" width="100%"/> --}}
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <div class="row grid-view g-0 shop-grid-5">
                
                            @foreach ($locations as $location)
                            @if($location->users->preview->content != null)
                                <!-- single slide Start -->
                            <div class="col-xl-3 col-sm-6">
                                <div class="product-card">
                                    <a class="thumb" href="{{route("user.store.products",$location->users->slug)}}">
                                        @if (str_contains($location->users->preview->content, 'http'))
                                            <img src="{{ $location->users->preview->content }}" alt="" width="350px" height="235px">
                                            @else
                                            <img src="{{ asset($location->users->preview->content) }}" alt="img" width="350px" height="235px"/>
                                        @endif
                                        
                                    </a>
                                    <div class="product-content">
                                        <h3 class="product-title">
                                            <br>
                                            <a href="{{route("user.store.products",$location->users->slug)}}">{{$location->users->name}}</a>
                                        </h3>
                                    </div>
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
</div>
@endsection