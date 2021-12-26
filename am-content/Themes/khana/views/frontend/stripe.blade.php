@extends("theme::frontend.master")
@section("content")
<nav class="breadcrumb-section section-py bg-light2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="bread-crumb-title">KASSA</h3>
                {{-- <ol class="breadcrumb bg-transparent justify-content-center m-0 p-0 align-items-center">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        checkout
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
                        <div class="text-center">
                            <img src="{{ theme_asset('khana/public/frontend/assets/images/payment.png') }}" alt="images">
                        </div>
                        <br>
                        <div class="login-register-form">
                            
                            <form action="{{ route('stripe.charge') }}" method="post" id="payment-form">
                                <div>
                                    <label for="card-element">
                                        Kredit-Eller Betalkort
                                    </label>
                                    <div id="card-element">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                            
                                    <!-- Used to display form errors. -->
                                    <div id="card-errors" role="alert"></div>
                                </div>
                                <br>
                                <div class="text-center">
                                <button class="btn btn-warning btn-hover-primary text-capitalize check-out-btn">Skicka Betalning</button>
                                </div>
                                {{ csrf_field() }}
                            </form>
                                {{-- <form action="#" method="post">
                                    <input type="text" name="user-name" placeholder="Username" />
                                    <input type="password" name="user-password" placeholder="Password" />
                                    <div class="button-box">
                                        <div class="login-toggle-btn">
                                                <input id="remember" type="checkbox" />
                                           <label for="remember">Remember me</label>
                                            <a href="#">Forgot Password?</a>
                                        </div>
                                    <a href="#" class="btn btn-warning btn-hover-primary">
                                       <span>Login</span>
                                    </a>
                                </div>
                            </form> --}}
                        </div>
                    </div>
                       
                </div>
            </div>
        </div>
    </div>
</div>
<br><br><br><br><br><br>
@endsection

@section("cart_script")
<script>
    var publishable_key = '{{ env('STRIPE_KEY') }}';
</script>

<script type="text/javascript">
    // Create a Stripe client.
    var stripe = Stripe(publishable_key);

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
    base: {
        color: '#32325d',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');
$('.__PrivateStripeElement-input').attr('placeholder', 'Search for Stuff');
// Handle real-time validation errors from the card Element.
card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Handle form submission.
var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
    event.preventDefault();

    stripe.createToken(card).then(function(result) {
        if (result.error) {
            // Inform the user if there was an error.
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = result.error.message;
        } else {
            // Send the token to your server.
            stripeTokenHandler(result.token);
        }
    });
});

// Submit the form with the token ID.
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    // Submit the form
    form.submit();
}
</script> 
@endsection