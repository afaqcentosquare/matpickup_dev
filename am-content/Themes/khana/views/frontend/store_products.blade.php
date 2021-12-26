@extends("theme::frontend.master")
@section("search_form")
<form action="{{route('search.product')}}" method="GET">
    <div class="input-group border">
        <input type="text" class="form-control typeahead" placeholder="Ange din söknyckel ... " name="copy_search" autocomplete="off" id="typeahead"/>
        <input type="text" name="selected_serach_text" id="selected_serach_text" hidden/>
        
        <div class="input-group-text">
            <button id="search_button" class="btn-search btn btn-hover-primary" type="submit">
                Sök
            </button>
        </div>
    </div>
</form>
@endsection
@section("search_form_mobile")
<form action="{{route('search.product')}}" method="GET" class="offcanvas-form">
    <div class="input-group border">
        
        <input type="text" class="form-control typeahead_mob border-0" placeholder="Ange din söknyckel ... " name="copy_search"  autocomplete="off" id="typeahead_mob"/>
        <input type="text" name="selected_serach_text" id="selected_serach_text_mob" hidden/>
        <div class="input-group-text">
            <button id="search_button_mob" class="btn-search btn btn-hover-primary" type="submit">
                Sök
            </button>
        </div>
    </div>
</form>
@endsection

@section("content")
<input type="text" name="store_id" value="{{$store_data->slug}}" hidden/>
<input type="text" id="store_pure_id" name="store_pure_id" value="{{$store_data->id}}" hidden/>
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
                @if ($store_data->preview != null)
                <img class="mb-4" src="{{ asset($store_data->preview->content) }}" alt="banner" width="100%" height="270px"/>
                @endif
                <div id="loading-div" style="visibility: hidden;" class="d-flex justify-content-center">
                    <div class="spinner-border text-success" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                  </div>
                  <div id="product_data">
                    @include("theme::frontend.store_products_view")
                  </div>
            </div>
            <div class="col-lg-4 col-xl-3 mb-7 order-lg-first">
                <div class="vertical-menu d-none d-lg-block">
                    <button class="menu-btn d-flex">
                        <span class="lnr lnr-text-align-left"></span>Kategorier
                    </button>
                    <div class="widget-card">
                       
                        <ul id="offcanvas-menu2" class="blog-ctry-menu">
                            @foreach ($categories as $category)
                            <li>
                                <a href="javascript:void(0);" onClick="getData(this)" value="{{$category->slug}}"> {{$category->name}}</a>
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
@endsection