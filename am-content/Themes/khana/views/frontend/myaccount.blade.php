@extends("theme::frontend.master")
@section('content')
    @php
    $currency = \App\Options::where('key', 'currency_name')
        ->select('value')
        ->first();
    @endphp
    <div class="pjax-container">
        <!-- success-alert start -->
        <div class="alert-message-area">
            <div class="alert-content">
                <h4 class="ale">{{ __('Your Settings Successfully Updated') }}</h4>
            </div>
        </div>
        <!-- success-alert end -->

        <!-- error-alert start -->
        <div class="error-message-area">
            <div class="error-content">
                <h4 class="error-msg"></h4>
            </div>
        </div>
        <nav class="breadcrumb-section section-py bg-light2">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3 class="bread-crumb-title">Mitt Konto</h3>
                        {{-- <ol class="breadcrumb bg-transparent justify-content-center m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        my account
                    </li>
                </ol> --}}
                    </div>
                </div>
            </div>
        </nav>

        <div class="my-account section-py">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3 class="title text-capitalize mb-5 pb-4">Mitt Konto</h3>
                    </div>
                    <!-- My Account Tab Menu Start -->
                    <div class="col-lg-3 col-12 mb-5">
                        <div class="myaccount-tab-menu nav" role="tablist">
                            <a href="#dashboad" data-bs-toggle="tab" class="active"><i
                                    class="fa fa-tachometer"></i>Kontrollpanel</a>

                            <a href="#orders" data-bs-toggle="tab"><i class="fa fa-cart-arrow-down"></i>
                                {{ __('Orders') }}</a>
                            <a href="#addresses" data-bs-toggle="tab"><i class="fa fa-cart-arrow-down"></i> Addresses</a>

                            <a href="#account-info" data-bs-toggle="tab"><i class="fa fa-user"></i>Inställningar</a>

                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                data-bs-toggle="tab">
                                <i class="fa fa-sign-out"></i> {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <!-- My Account Tab Menu End -->

                    <!-- My Account Tab Content Start -->
                    <div class="col-lg-9 col-12 mb-5">
                        <div class="tab-content" id="myaccountContent">
                            <!-- Single Tab Content Start -->
                            <div class="tab-pane fade active show" id="dashboad" role="tabpanel">
                                <div class="myaccount-content">
                                    <h3>Kontrollpanel</h3>

                                    <div class="welcome mb-20">
                                        <p>
                                            Hej, <strong>{{ Auth::User()->name }}</strong>
                                        </p>
                                    </div>

                                    <p class="mb-0">
                                        Från din kontos Kontrollpanel . du kan enkelt kontrollera och se dina senaste
                                        beställningar, hantera dina leverans- och faktureringsadresser och redigera ditt
                                        lösenord och kontouppgifter.
                                    </p>
                                </div>
                            </div>
                            <!-- Single Tab Content End -->

                            <!-- Single Tab Content Start -->
                            <div class="tab-pane fade" id="orders" role="tabpanel">
                                <div class="myaccount-content">
                                    <h3>Orders</h3>

                                    <div class="myaccount-table table-responsive text-center">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Beställnings-id</th>
                                                    <th>Betalnings Sätt</th>
                                                    <th>Orderstatus</th>
                                                    <th>Belopp</th>
                                                    <th>Ta Bort</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($orders as $order)
                                                    <tr>
                                                        <td>{{ $order->id }}</td>
                                                        <td>{{ $order->payment_method }}</td>
                                                        <td>
                                                            @if ($order->status == 2)
                                                                <div class="badge bg-primary">I Väntan På</div>
                                                            @elseif($order->status == 3)
                                                                <div class="badge bg-info">Plocka upp</div>
                                                            @elseif($order->status == 1)
                                                                <div class="badge bg-info">Komplett</div>
                                                            @elseif($order->status == 0)
                                                                <div class="badge bg-danger">Avbryt</div>
                                                            @endif
                                                        </td>
                                                        {{-- <td>{{ strtoupper($currency->value) }} {{ $order->total + $order->shipping }}</td> --}}
                                                        <td>{{ strtoupper($currency->value) }}
                                                            {{ $order->total + 80.0 }}</td>
                                                        <td>
                                                            <div class="order-btn d-flex">
                                                                {{-- @if ($order->status == 1)
                                                    @if (!$order->review()->count() > 0)
                                                    <a class="view_btn mr-2 btn-send" href="#" data-toggle="modal" data-target="#send_review_{{ $order->id }}"><i class="fas fa-paper-plane"></i></i></a>
                                                    @endif
                                                    @endif --}}
                                                                <a class="ht-btn black-btn"
                                                                    href="{{ route('author.order.details', encrypt($order->id)) }}">Visa
                                                                    Detaljer</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Tab Content End -->


                            <!-- Single Tab Content Start -->
                            <div class="tab-pane fade" id="addresses" role="tabpanel">
                                <div class="myaccount-content">
                                    <h3>Addresses</h3>
                                    <p><b>Note: </b> Här kan du aktivera leverans tjänsten i ditt område. Genom att flera kunder i samma område lägger till adress i deras konto. Kunder ser inte varandras namn eller adress utan bara information om hur mycket leverans vi har i detta område. Leveransen aktiveras med att fem kunder i samma område gör en beställning.  

                                        Om du inte hittar din stad/ tätort med i vår leverans tjänst. Då har du nytta av denna funktion. Då kan du lägga dina hem eller jobb adresser. Och se om det finns leveranser i ditt område.</p>
                                    <div class="account-details-form">
                                        <form action="{{ route('author.store.address') }}" method="POST"
                                            id="address_form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="name">Address Namn</label>
                                                    <input type="text" name="address_name" placeholder="Address Namn" id="address_name">
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="city">City</label>
                                                    <input type="text" name="city" placeholder="City"
                                                        id="city">
                                                </div>
                                                <br><br><br><br>
                                                <div class="col-md-12">
                                                    <label for="address">Address</label>
                                                    <input type="text" name="address" placeholder="write your address here...."
                                                        id="address">
                                                </div>
                                                <br><br><br><br>
                                                <div class="col-md-12">
                                                    <button type="submit"
                                                        class="btn btn-warning btn-hover-primary">Submit</button>
                                                </div>
                                                
                                            </div>
                                        </form>
                                    </div>
                                    <br>
                                    <div class="myaccount-table table-responsive text-center">
                                        <table class="table table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Name</th>
                                                    <th>City</th>
                                                    <th>Address</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($addresses as $address)
                                                    <tr>
                                                        <td>{{ $address->name }}</td>
                                                        <td>{{ $address->city }}</td>
                                                        <td>{{ $address->address }}</td>
                                                       
                                                        <td>
                                                            @if(in_array(Str::lower($address->city), $cities))
                                                            <div class="badge bg-primary">Active</div>
                                                            @else
                                                            @if($address_count->has([$address->city]))
                                                           
                                                            @if($address_count[$address->city] >= 5)
                                                            <div class="badge bg-primary">Active</div>
                                                            @else
                                                                <div class="badge bg-danger">{{5 - $address_count[$address->city] }} adresser kvar for aktivering av leverans</div>
                                                            @endif
                                                            @endif
                                                            @endif

                                                            
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Tab Content End -->

                            <!-- Single Tab Content Start -->
                            <div class="tab-pane fade" id="account-info" role="tabpanel">
                                <div class="myaccount-content">
                                    <h3>Kontouppgifter</h3>

                                    <div class="account-details-form">
                                        <form action="{{ route('author.settings.update') }}" method="POST"
                                            id="user_settings_form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-lg-6 col-12 mb-5">
                                                    <label for="name">Namn</label>
                                                    <input type="text" name="name" placeholder="Namn" id="name"
                                                        value="{{ Auth::User()->name }}">
                                                </div>

                                                <div class="col-lg-6 col-12 mb-5">
                                                    <label for="email">{{ __('Email') }}</label>
                                                    <input type="text" name="email" placeholder="{{ __('Email') }}"
                                                        id="email" value="{{ Auth::User()->email }}">
                                                </div>

                                                <div class="col-12 mb-5">
                                                    <h4>Ändra Lösenord</h4>
                                                </div>

                                                <div class="col-12 mb-5">
                                                    <label for="current_password">Nuvarande lösenord</label>
                                                    <input type="password" placeholder="Nuvarande lösenord"
                                                        name="current_password" id="current_password">
                                                </div>

                                                <div class="col-lg-6 col-12 mb-5">
                                                    <label for="new_password">Nytt lösenord</label>
                                                    <input type="password" class="form-control"
                                                        placeholder="Nytt lösenord" name="password" id="new_password">
                                                </div>

                                                <div class="col-lg-6 col-12 mb-5">
                                                    <label for="confirm_password">Bekräfta lösenord</label>
                                                    <input type="password" class="form-control"
                                                        placeholder="Bekräfta lösenord" name="password_confirmation"
                                                        id="confirm_password">
                                                </div>

                                                <div class="col-12">
                                                    <button type="submit"
                                                        class="btn btn-warning btn-hover-primary">Uppdatera</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Single Tab Content End -->
                        </div>
                    </div>
                </div>
                <!-- My Account Tab Content End -->
            </div>
        </div>
    </div>
    <br><br><br>

@endsection
