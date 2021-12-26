@if ($products->count() > 0)
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="home" role="tabpanel">
        <div class="row grid-view g-0 shop-grid-5">

            @foreach ($products as $product)
            @if ($product->price != null)
                <!-- single slide Start -->
            <div class="col-xl-3 col-sm-6">
                <div class="product-card">
                    <a class="thumb" href="{{route("single.product",$product->slug)}}">
                        @if (str_contains($product->preview->content, 'http'))
                            <img src="{{ $product->preview->content }}" alt="">
                            @else
                            <img src="{{ asset($product->preview->content) }}" alt="img" />
                        @endif
                        
                    </a>
                    <div class="product-content">
                        <h3 class="product-title">
                            <br>
                            <a href="{{route("single.product",$product->slug)}}">{{$product->title}}</a>
                        </h3>
                        @php
                        $currency=\App\Options::where('key','currency_name')->select('value')->first();
                        @endphp
                        <span class="price regular-price">{{ strtoupper($currency->value) }} {{ number_format($product->price->price,2) }}</span>
                        <input type="hidden" id="add_to_cart_url" value="{{url('add_to_cart')}}">
                        <a class="product-btn btn btn-warning btn-hover-primary" href="javascript:void(0)" onclick="product_add_to_cart('{{ $product->slug }}','{{ $product->user->slug }}')">
                            Lägg i kundvagn
                        </a>
                    </div>
                </div>
            </div>
            <!-- single slide End -->
            @endif
            @endforeach
           

        </div>
    </div>
</div>
<div class="row g-0 align-items-center mt-md-5">
    {{$products->onEachSide(1)->links()}}
</div>
@else
<h1>No Stores Found<h1>
@endif
