@extends("theme::frontend.master")


@section("content")

<nav class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                {{-- <ol class="breadcrumb bg-transparent m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$store_data->name}}</li>
                </ol> --}}
            </div>
        </div>
    </div>
</nav>

<div class="shop-product-tab section-pb">
    <div class="container">
        <div class="row mb-n7">
            <div class="col-lg-8 col-xl-9">
                {{-- @if ($store_data->preview != null)
                <img class="mb-4" src="{{ asset($store_data->preview->content) }}" alt="banner" width="100%" height="270px"/>
                @endif
                <div id="loading-div" style="visibility: hidden;" class="d-flex justify-content-center">
                    <div class="spinner-border text-success" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div> --}}
                  <div id="product_data">
                    @if ($allCitiesStores->count() > 0)
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel">
                                <div class="row grid-view g-0 shop-grid-5">

                                    @foreach ($allCitiesStores as $cityStore)
                                    
                                        <!-- single slide Start -->
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="product-card">
                                            <a class="thumb" href="{{route("user.store.products",$cityStore->users->slug)}}">
                                                
                                                    <img src="{{ asset($cityStore->users->avatar) }}" alt="img" />
                                                
                                            </a>
                                            <div class="product-content">
                                                <h3 class="product-title">
                                                    <br>
                                                    <a href="{{route("user.store.products",$cityStore->users->slug)}}">{{$cityStore->users->name}}</a>
                                                </h3>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <!-- single slide End -->
                                   
                                    @endforeach
                                

                                </div>
                            </div>
                        </div>
                        <div class="row g-0 align-items-center mt-md-5">
                            {{$allCitiesStores->onEachSide(1)->links()}}
                        </div>
                        @else
                        <h1>No Product Found<h1>
                        @endif
                  </div>
            </div>
            <div class="col-lg-4 col-xl-3 mb-7 order-lg-first">
                <div class="vertical-menu d-none d-lg-block">
                    <button class="menu-btn d-flex">
                        <span class="lnr lnr-text-align-left"></span>Städer
                    </button>
                    <div class="widget-card">
                       
                        <ul id="offcanvas-menu2" class="blog-ctry-menu">
                            @foreach ($allCities as $city)
                            <li>
                                <a href="{{route("area",$city->slug)}}">{{$city->title}}</a>
                            </li>
                            @endforeach
                            
                        </ul>
                    </div>
                    <!-- menu content -->
                </div>

            </div>
        </div>
    </div>
</div>
<br><br><br><br><br>
@endsection