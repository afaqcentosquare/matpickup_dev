@extends("theme::frontend.master")
@section('content')
    <nav class="breadcrumb-section section-py bg-light2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="bread-crumb-title">ORDERBEKRÄFTELSE</h3>
                    {{-- <ol class="breadcrumb bg-transparent justify-content-center m-0 p-0 align-items-center">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            order confirmation
                        </li>
                    </ol> --}}
                </div>
            </div>
        </div>
    </nav>

    <div class="login-register-area section-py">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-12 mx-auto">
                    <div class="login-register-wrapper">

                        <div class="login-form-container">
                            <div class="login-register-form">
                                <div class="row">
                                    <div class="text-center">
                                        <h2>Tack för din beställning</h2>
                                        <h2>Din beställning bekräftas!!!!</h2>
                                        <br>

                                        <a href="{{ route('author.dashboard') }}" class="btn btn-warning btn-hover-primary text-capitalize check-out-btn">Se Ordning</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
@endsection
