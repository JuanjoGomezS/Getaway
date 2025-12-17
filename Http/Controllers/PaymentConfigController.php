<?php

namespace Modules\Gateways\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Gateways\Traits\AddonActivationClass;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\Setting;

class PaymentConfigController extends Controller
{
    use Processor, AddonActivationClass;

    private $config_values;
    private $merchant_key;
    private $config_mode;
    private Setting $payment_setting;

    public function __construct(Setting $payment_setting)
    {
        $this->payment_setting = $payment_setting;
    }

    public function payment_config_get()
    {
        $response = $this->isActive();
        if(!$response['active']){
            Toastr::error(GATEWAYS_DEFAULT_400['message']);
            return redirect($response['route']);
        }

        $payment_gateway_list = ['ssl_commerz','paypal','stripe','razor_pay','senang_pay','paytabs','paystack','paymob_accept','paytm','flutterwave','liqpay','bkash','mercadopago','redsys'];
        $data_values = $this->payment_setting->whereIn('settings_type', ['payment_config'])->whereIn('key_name',$payment_gateway_list)->get();
        if (base64_decode(env('SOFTWARE_ID')) == '40224772') {
            return view('Gateways::payment-config.demandium-payment-config', compact('data_values'));
        } else {
            return view('Gateways::payment-config.payment-config', compact('data_values'));
        }
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function payment_config_set(Request $request): RedirectResponse
    {
        collect(['status'])->each(fn($item, $key) => $request[$item] = $request->has($item) ? (int)$request[$item] : 0);
        $validation = [
            'gateway' => 'required|in:ssl_commerz,paypal,stripe,razor_pay,senang_pay,paytabs,paystack,paymob_accept,paytm,flutterwave,liqpay,bkash,mercadopago,redsys',
            'mode' => 'required|in:live,test'
        ];

        $additional_data = [];

        if ($request['gateway'] == 'ssl_commerz') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'store_id' => 'required',
                'store_password' => 'required'
            ];
        } elseif ($request['gateway'] == 'paypal') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'client_id' => 'required',
                'client_secret' => 'required'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'api_key' => 'required',
                'published_key' => 'required',
            ];
        } elseif ($request['gateway'] == 'razor_pay') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'api_key' => 'required',
                'api_secret' => 'required'
            ];
        } elseif ($request['gateway'] == 'senang_pay') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required',
                'secret_key' => 'required',
                'merchant_id' => 'required'
            ];
        } elseif ($request['gateway'] == 'paytabs') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'profile_id' => 'required',
                'server_key' => 'required',
                'base_url' => 'required'
            ];
        } elseif ($request['gateway'] == 'paystack') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'public_key' => 'required',
                'secret_key' => 'required',
                'merchant_email' => 'required'
            ];
        } elseif ($request['gateway'] == 'paymob_accept') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'callback_url' => 'required',
                'api_key' => 'required',
                'iframe_id' => 'required',
                'integration_id' => 'required',
                'hmac' => 'required'
            ];
        } elseif ($request['gateway'] == 'mercadopago') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'access_token' => 'required',
                'public_key' => 'required'
            ];
        } elseif ($request['gateway'] == 'liqpay') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'private_key' => 'required',
                'public_key' => 'required'
            ];
        } elseif ($request['gateway'] == 'flutterwave') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'secret_key' => 'required',
                'public_key' => 'required',
                'hash' => 'required'
            ];
        } elseif ($request['gateway'] == 'paytm') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'merchant_key' => 'required',
                'merchant_id' => 'required',
                'merchant_website_link' => 'required'
            ];
        } elseif ($request['gateway'] == 'bkash') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'app_key' => 'required',
                'app_secret' => 'required',
                'username' => 'required',
                'password' => 'required',
            ];
        }elseif ($request['gateway'] == 'redsys') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'key' => 'required',
                'merchantcode' => 'required',
                'tradeName' => 'required',
                'titular' => 'required',
            ];
        }
        $request->validate(array_merge($validation, $additional_data));

        $settings = $this->payment_setting->where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();

        $additional_data_image = $settings['additional_data'] != null ? json_decode($settings['additional_data']) : null;

        if ($request->has('gateway_image')) {
            $gateway_image = $this->file_uploader('payment_modules/gateway_image/', 'png', $request['gateway_image'], $additional_data_image != null ? $additional_data_image->gateway_image : '');
        } else {
            $gateway_image = $additional_data_image != null ? $additional_data_image->gateway_image : '';
        }

        $payment_additional_data = [
            'gateway_title' => $request['gateway_title'],
            'gateway_image' => $gateway_image,
        ];

        $validator = Validator::make($request->all(), array_merge($validation, $additional_data));

        $this->payment_setting->updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'], [
            'key_name' => $request['gateway'],
            'live_values' => $validator->validate(),
            'test_values' => $validator->validate(),
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'is_active' => $request['status'],
            'additional_data' => json_encode($payment_additional_data),
        ]);

        Toastr::success(GATEWAYS_DEFAULT_UPDATE_200['message']);
        return back();
    }
}
