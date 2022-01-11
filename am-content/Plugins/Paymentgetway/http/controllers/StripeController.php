<?php 

namespace Amcoders\Plugin\Paymentgetway\http\controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Omnipay\Omnipay;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use Session;
use Mail;
class StripeController extends controller
{
	public static function redirect_if_payment_success()
    {
       return route('payment.success');
    }

    public static function redirect_if_payment_faild()
    {
        return route('payment.fail');
    }

    public static function make_payment($array)
    {

        try {
            $gateway = Omnipay::create('Stripe\PaymentIntents');
            $gateway->initialize([
                'apiKey' => env('STRIPE_SECRET',null),
            ]);
    
            $response = $gateway->purchase([
                'amount'                   => $array['amount'],
                'currency'                 =>  $array['currency'],
                'description'              => '',
                'paymentMethod'            => $array['stripeToken'],
                'returnUrl'                => route("payment.confirm", $array['stripeToken']),
                'confirm'                  => true,
                
            ])->send();
            Session::put('intent', $response->getPaymentIntentReference());
            $cust_temp_mail =  $array['email'];
            Session::put('temp_cust_email', $cust_temp_mail);
                if ($response->isRedirect()) {
                    $response->redirect();
                } elseif ($response->isSuccessful()) {
                    $data['payment_id'] = $response->getPaymentIntentReference();
                    $data['payment_method'] = "stripe";
                    $order_info= Session::get('order_info');
                    $data['ref_id'] =$order_info['ref_id'];
                    $data['amount'] =$order_info['amount'];
                    $data['vendor_id'] =$order_info['vendor_id'];
                    Session::forget('order_info');
                    Session::put('payment_info', $data);
                    
                    Mail::send(['text'=>'plugin::mail'], $data, function($message) {
                        $message->to('order@matpickup.se', 'Matpickup')->subject
                           ('New Order');
                        $message->from('send@matpickup.se','MatPickup');
                     });

                     Mail::send(['text'=>'plugin::customer_email'], $data, function($message) use ($cust_temp_mail) {
                        $message->to($cust_temp_mail, 'Matpickup')->subject
                           ('Matpickup Order Confirmed');
                        $message->from('send@matpickup.se','MatPickup');
                     });
                    return redirect(StripeController::redirect_if_payment_success());
                } else {
                    // payment failed: display message to customer
                    return redirect(StripeController::redirect_if_payment_faild());
                }
        } catch (Exception $e) {
            return redirect(StripeController::redirect_if_payment_faild());
        }
    } 
}