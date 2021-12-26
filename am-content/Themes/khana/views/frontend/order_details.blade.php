@extends("theme::frontend.master")
@section("content")
@php
$currency=\App\Options::where('key','currency_icon')->select('value')->first();
$customerInfo=json_decode($info->data);
@endphp
								
{{-- <nav class="breadcrumb-section section-py bg-light2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="bread-crumb-title">Order Details</h3>
            </div>
        </div>
    </div>
</nav> --}}

<div class="check-out-section section-py">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="your-order-area">
                    <h3 class="title">Personuppgifter</h3>
                    <div class="your-order-wrap gray-bg-4">
                        <div class="your-order-product-info">
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Namn:</li>
                                    <li>{{ $customerInfo->name }}</li>
                                </ul>
                            </div>
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Telefonnummer:</li>
                                    <li>{{ $customerInfo->phone }}</li>
                                </ul>
                            </div>
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Leveransadress:</li>
                                    <li>{{ $customerInfo->address }}</li>
                                </ul>
                            </div>
                            <div class="panel-heading" id="method-one">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#method1">
                                        Ordning Notera
                                    </a>
                                </h4>
                            </div>
                            <div id="method1" class="panel-collapse collapse show">
                                <div class="panel-body">
                                    <p>
                                        {{ $customerInfo->note }}
                                    </p>
                                </div>
                            </div>
                            {{-- <div class="your-order-total">
                                <ul>
                                    <li class="order-total">Frakt</li>
                                    <li>SEK {{ $info->shipping }}</li>
                                </ul>
                            </div> --}}
                            <div class="your-order-total">
                                <ul>
                                    <li class="order-total">Betalnings sätt</li>
                                    <li>{{ strtoupper($info->payment_method) }}</li>
                                </ul>
                            </div>
                            <div class="your-order-total">
                                <ul>
                                    <li class="order-total">Betalningsstatus</li>
                                    @if($info->payment_status == 0)
                                    <div class="badge bg-danger">I Väntan På</div>
                                    @elseif($info->payment_status == 1)
                                    <div class="badge bg-success">Avslutad</div>
                                    @endif
                                </ul>
                            </div>
                            <div class="your-order-total">
                                <ul>
                                    <li class="order-total">Orderstatus</li>
                                    @if($info->status == 0)
                                    <div class="badge bg-danger">Inställt</div>
                                    @elseif($info->status == 2)
                                    <div class="badge bg-info">I Väntan På</div>
                                    @elseif($info->status == 3)
                                    <div class="badge bg-primary">Accepterad</div>
                                    @elseif($info->status == 1)
                                    <div class="badge bg-success">Avslutad</div>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="your-order-area">
                    <h3 class="title">Du Beställer</h3>
                    <div class="your-order-wrap gray-bg-4">
                        <div class="your-order-product-info">
                            <div class="your-order-top">
                                <ul>
                                    <li>Produktnamn</li>
                                    <li>Totala Summan</li>
                                </ul>
                            </div>
                            <div class="your-order-middle">
                                @php
									$subtotal=0;
									@endphp
									@foreach($info->orderlist as $key => $itemrow)
									@php
									$total= $itemrow->total*$itemrow->qty+$subtotal;
									$subtotal = $total; 
									@endphp
                                <ul>
                                    <li>
                                        <span class="order-middle-left">{{ $itemrow->products->title ?? '' }} X {{ $itemrow->qty ?? '' }}</span>
                                        <span class="order-price">SEK {{ $itemrow->total*$itemrow->qty }}</span>
                                    </li>
                                </ul>
                                @endforeach
                            </div>
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Delsumma:</li>
                                    <li> SEK {{ $subtotal }}</li>
                                </ul>
                            </div>
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Leveransavgift</li>
                                     {{-- <li>SEK  130.00</li> --}}
                                     <li>SEK  80.00</li>
                                </ul>
                            </div>
                            @if($info->coupon_id != null)
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Discount Code:</li>
                                    <li>{{ $info->coupon->title ?? '' }}</li>
                                </ul>
                            </div>
                            <div class="your-order-bottom">
                                <ul>
                                    <li class="your-order-shipping">Discount:</li>
                                    <li>SEK {{ $info->discount }}</li>
                                </ul>
                            </div>
							@endif
                            <div class="your-order-total">
                                <ul>
                                    <li class="order-total">Totala summan:</li>
                                    {{-- <li>SEK {{ $info->total+130.00 }}</li> --}}
                                    <li>SEK {{ $info->total+80.00 }}</li>
                                </ul>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br><br><br>
@endsection