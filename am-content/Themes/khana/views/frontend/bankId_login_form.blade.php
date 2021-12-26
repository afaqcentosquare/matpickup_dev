@extends("theme::frontend.master")
@section("content")
<nav class="breadcrumb-section section-py bg-light2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="bread-crumb-title">Logga In Med BankId</h3>
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
                                
                                <div class="login-register-form">
                                    <div id="loading-div" style="visibility: hidden;" class="d-flex justify-content-center">
                                        <div class="spinner-border text-success" role="status">
                                          <span class="sr-only">Loading...</span>
                                        </div>
                                      </div>
                                      <span id="bankid_error" class="text-danger"></span><br><br>
                                    <form id="bankIdForm">
                                        @csrf
                                        <input type="number" id="personal_number" name="personal_number" placeholder="Personnummer" value="{{ old('personal_number') }}"/>
                                        <div class="button-box text-center">
                                            <button type="submit" class="btn btn-warning btn-hover-primary">
                                                LOGGA IN 
                                                {{-- {{ __('Login Now') }} --}}
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
<br><br><br><br>
@endsection