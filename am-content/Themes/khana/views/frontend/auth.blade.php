@extends("theme::frontend.master")
@section("content")
<nav class="breadcrumb-section section-py bg-light2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="bread-crumb-title">Logga In & Registrera</h3>
                {{-- <ol class="breadcrumb bg-transparent justify-content-center m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        login & register
                    </li>
                </ol> --}}
            </div>
        </div>
    </div>
</nav>

<div class="login-register-area section-py">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-12 mx-auto">
                <div class="login-register-wrapper">
                    <div class="login-register-tab-list nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="active" data-bs-toggle="tab" href="#lg1">
                            <h4>Logga In</h4>
                        </a>
                        <a data-bs-toggle="tab" href="#lg2">
                            <h4>Registrera</h4>
                        </a>
                    </div>

                    <div class="tab-content">
                        <div id="lg1" class="tab-pane show active">
                            <div class="login-form-container">
                           
                            @if(Session::has('errors'))
                            @if(is_array(Session::get('errors')))
                                @foreach ($errors->all() as $error)
                                <span class="text-danger">{{ $error }}</span><br><br>
                                <br>
                                @endforeach
                                @else
                                <span class="text-danger">{{ Session::get('errors') }}</span><br><br>
                            @endif
							@endif
                            {{-- @if ($errors->any())
                            <div class="row">
                                <div class="col-12">
                                    @foreach ($errors->all() as $error)
                                    <span class="text-danger">{{ $error }}</span><br>
                                    <br>
                                    @endforeach
                                </div>
                            </div>
                            @endif --}}
                                {{-- @if(Session::has('errors'))
                                <div class="row">
                                    <div class="col-12">
                                        <span class="text-danger">{{ Session::get('errors') }}</span><br>
                                    </div>
                                </div>
                                @endif --}}
                                
                                <div class="login-register-form">
                                    <div class="social-links text-center">
                                        {{-- <h6>{{ __('Login with social account') }}</h6> --}}
                                        <h6>Logga In Med Socialt Konto</h6>
                                        @if(env('FACEBOOK_CLIENT_ID') != null)
                                        <a class="facebook" href="{{ url('login/facebook') }}"><i class="fa fa-facebook"></i> {{ __('Facebook') }}</a>
                                        @endif
        
                                        @if(env('GOOGLE_CLIENT_ID') != null)
                                        <a class="google" href="{{ url('login/google') }}"><i class="fa fa-google"></i> {{ __('Google') }}</a>
                                        @endif
                                        {{-- <a class="text text-white remove-style-bankid" href="{{ url('user/login/bankid') }}"><img src="{{ theme_asset('khana/public/frontend/assets/images/banner/bank_id_logo.png') }}"/>
                                        </a> --}}
                                        <h6>----------ELLER----------</h6>
                                    </div>
                                    <form action="{{ route('login') }}" method="post">
                                        @csrf
                                        <input type="email" name="email" placeholder="ange din e-postadress" value="{{ old('email') }}"/>
                                        <input type="password" name="password"  placeholder="Lössenord" />
                                       
                                        <div class="button-box">
                                            <div class="login-toggle-btn">
                                                <input type="checkbox"  id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                                                <label for="remember">Kom Ihåg Mig</label>
                                                <a href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                                            </div>
                                            <button type="submit" class="btn btn-warning btn-hover-primary">
                                                LOGGA IN 
                                                {{-- {{ __('Login Now') }} --}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="lg2" class="tab-pane">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                   
                                    <form action="{{ route('user.register') }}" method="post">
                                        @csrf
                                        {{-- <input type="text" name="name" placeholder="{{ __('Your Full Name') }}" /> --}}
                                        <input type="text" name="name" placeholder="ditt fullständiga namn" />
                                        <input type="text" name="email" placeholder="Din email" />
                                        {{-- <input type="password" name="password" placeholder="{{ __('Password') }}" /> --}}
                                        {{-- <input name="password_confirmation" placeholder="{{ __('Password') }}" type="password_confirmation" /> --}}
                                        <input type="password" name="password" placeholder="lössenord" />
                                        <input type="password" name="password_confirmation" placeholder="lössenord" type="password_confirmation" />
                                        <div class="button-box">
                                            <div class="login-toggle-btn">
                                                <input type="checkbox"  id="agree" name="agree" {{ old('remember') ? 'checked' : '' }}/>
                                                <label for="remember">Jag <a href="{{ url('/page/terms-and-conditions') }}">Godkänner Villkoren</a></label>
                                            </div>
                                            <button type="submit" class="btn btn-warning btn-hover-primary">
                                                Registrera Nu
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection