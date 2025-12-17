<?php

namespace Modules\Gateways\Http\Controllers;

use App\Models\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Modules\Gateways\Entities\PaymentRequest;

class RedSysPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private PaymentRequest $payment;
    public function __construct(PaymentRequest $payment)
    {
        $config = $this->payment_config('redsys', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values,true);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values,true);
        }
        $this->payment = $payment;
    }
    public function get_initialize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($payment_data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $config = $this->config_values;
        $customer_data = json_decode($payment_data->payer_information, true);
        $currencyCode = $payment_data->currency_code;

        if($currencyCode != 'EUR')
        {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $order_id = rand(100,200).$payment_data->attribute_id;
        $env = $config['mode']=='live'?'live':'test';
        try {
            //Example Key
           $key = $config['key'] ;

           $redsys = new \Sermepa\Tpv\Tpv();
           $redsys->setLanguage('001');
           $redsys-> setAmount ( $payment_data->payment_amount);
           $redsys-> setOrder ($order_id);
           $redsys-> setMerchantcode ( $config['merchantcode'] ); //Replace with the code provided by the bank
           $redsys-> setCurrency ( '978' );
           $redsys-> setTransactiontype ( '0' );
           $redsys-> setTerminal ( '1' );
           $redsys-> setMethod ( '' ); //Only card payment, we don't show iupay
        //    $redsys-> setNotification ( 'http://localhost/noti.php' ); //Notification url
           $redsys-> setUrlOk ( route('redsys.callback',['status'=>'success', 'payment_id'=>$request['payment_id']]) ); //Url OK
           $redsys-> setUrlKo ( route('redsys.callback',['status'=>'failed', 'payment_id'=>$request['payment_id']]) );//Url KO
           $redsys-> setVersion ( 'HMAC_SHA256_V1' );
           $redsys-> setTradeName ( $config['tradeName'] );
           $redsys-> setTitular ( $config['titular'] );
        //    $redsys-> setProductDescription ( 'Miscellaneous purchases' );
           $redsys-> setEnvironment ($env ); // test environment
           $signature = $redsys-> generateMerchantSignature ( $key );
           $redsys -> setMerchantSignature ( $signature );
           $form = $redsys -> createForm ();
       } catch (\Sermepa\Tpv\TpvException $e ) {
            return  $e -> getMessage ();
       }
        return view('Gateways::payment.redsys-payment',compact('form'));
    }

    public function callback(Request $request, $status, $payment_id)
    {
        $redsys = new \Sermepa\Tpv\Tpv();
        $config = $this->config_values;
        $key = $config['key'];
        $parameters = $redsys->getMerchantParameters($request["Ds_MerchantParameters"]);
        $DsResponse = $parameters["Ds_Response"];
        $DsResponse += 0;
        if($status == 'success' && $redsys->check($key, $request) && $DsResponse <= 99) {
            $this->payment::where(['id' => $payment_id])->update([
                'payment_method' => 'redsys',
                'is_paid' => 1,
                'transaction_id' => $request->Ds_Signature ?? uniqid(),
            ]);
            $data = $this->payment::where(['id' => $payment_id])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }
            return $this->payment_response($data,'success');
        }
        $payment_data = $this->payment::where(['id' => $payment_id])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data,'fail');

    }
}

