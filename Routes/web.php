<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

/*

*/

use Modules\Gateways\Http\Controllers\CashFreePaymentController;
use Modules\Gateways\Http\Controllers\InstamojoPaymentController;
use Modules\Gateways\Http\Controllers\PhonepeController;
use Modules\Gateways\Http\Controllers\SslCommerzPaymentController;
use Modules\Gateways\Http\Controllers\StripePaymentController;
use Modules\Gateways\Http\Controllers\PaymobController;
use Modules\Gateways\Http\Controllers\FlutterwaveV3Controller;
use Modules\Gateways\Http\Controllers\PaytmController;
use Modules\Gateways\Http\Controllers\PaypalPaymentController;
use Modules\Gateways\Http\Controllers\PaytabsController;
use Modules\Gateways\Http\Controllers\LiqPayController;
use Modules\Gateways\Http\Controllers\RazorPayController;
use Modules\Gateways\Http\Controllers\SenangPayController;
use Modules\Gateways\Http\Controllers\MercadoPagoController;
use Modules\Gateways\Http\Controllers\BkashPaymentController;
use Modules\Gateways\Http\Controllers\PaystackController;
use Modules\Gateways\Http\Controllers\FatoorahPaymentController;
use Modules\Gateways\Http\Controllers\TapPaymentController;
use Modules\Gateways\Http\Controllers\XenditPaymentController;
use Modules\Gateways\Http\Controllers\AmazonPaymentController;
use Modules\Gateways\Http\Controllers\IyziPayController;
use Modules\Gateways\Http\Controllers\HyperPayController;
use Modules\Gateways\Http\Controllers\FoloosiPaymentController;
use Modules\Gateways\Http\Controllers\CCavenueController;
use Modules\Gateways\Http\Controllers\PvitController;
use Modules\Gateways\Http\Controllers\MoncashController;
use Modules\Gateways\Http\Controllers\ThawaniPaymentController;
use Modules\Gateways\Http\Controllers\VivaWalletController;
use Modules\Gateways\Http\Controllers\HubtelPaymentController;
use Modules\Gateways\Http\Controllers\MaxiCashController;
use Modules\Gateways\Http\Controllers\EsewaPaymentController;
use Modules\Gateways\Http\Controllers\SwishPaymentController;
use Modules\Gateways\Http\Controllers\MomoPayController;
use Modules\Gateways\Http\Controllers\PayFastController;
use Modules\Gateways\Http\Controllers\WorldPayController;
use Modules\Gateways\Http\Controllers\SixcashPaymentController;
use Modules\Gateways\Http\Controllers\PaymentConfigController;
use Modules\Gateways\Http\Controllers\RedSysPaymentController;

$is_published = 0;
try {
    $full_data = include('Modules/Gateways/Addon/info.php');
    $is_published = $full_data['is_published'] == 1 ? 1 : 0;
} catch (\Exception $exception) {
}

if ($is_published) {
    Route::group(['prefix' => 'payment'], function () {

        //SSLCOMMERZ
        Route::group(['prefix' => 'sslcommerz', 'as' => 'sslcommerz.'], function () {
            Route::get('pay', [SslCommerzPaymentController::class, 'index'])->name('pay');
            Route::post('success', [SslCommerzPaymentController::class, 'success'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('failed', [SslCommerzPaymentController::class, 'failed'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::post('canceled', [SslCommerzPaymentController::class, 'canceled'])
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //STRIPE
        Route::group(['prefix' => 'stripe', 'as' => 'stripe.'], function () {
            Route::get('pay', [StripePaymentController::class, 'index'])->name('pay');
            Route::get('token', [StripePaymentController::class, 'payment_process_3d'])->name('token');
            Route::get('success', [StripePaymentController::class, 'success'])->name('success');
        });

        //RAZOR-PAY
        Route::group(['prefix' => 'razor-pay', 'as' => 'razor-pay.'], function () {
            Route::get('pay', [RazorPayController::class, 'index']);
            Route::post('payment', [RazorPayController::class, 'payment'])->name('payment')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //PAYPAL
        Route::group(['prefix' => 'paypal', 'as' => 'paypal.'], function () {
            Route::get('pay', [PaypalPaymentController::class, 'payment']);
            Route::any('success', [PaypalPaymentController::class, 'success'])->name('success')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);;
            Route::any('cancel', [PaypalPaymentController::class, 'cancel'])->name('cancel')
                ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //SENANG-PAY
        Route::group(['prefix' => 'senang-pay', 'as' => 'senang-pay.'], function () {
            Route::get('pay', [SenangPayController::class, 'index']);
            Route::any('callback', [SenangPayController::class, 'return_senang_pay'])
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //PAYTM
        Route::group(['prefix' => 'paytm', 'as' => 'paytm.'], function () {
            Route::get('pay', [PaytmController::class, 'payment']);
            Route::any('response', [PaytmController::class, 'callback'])->name('response')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //FLUTTERWAVE
        Route::group(['prefix' => 'flutterwave-v3', 'as' => 'flutterwave-v3.'], function () {
            Route::get('pay', [FlutterwaveV3Controller::class, 'initialize'])->name('pay');
            Route::get('callback', [FlutterwaveV3Controller::class, 'callback'])->name('callback');
        });

        //PAYSTACK
        Route::group(['prefix' => 'paystack', 'as' => 'paystack.'], function () {
            Route::get('pay', [PaystackController::class, 'index'])->name('pay');
            Route::post('payment', [PaystackController::class, 'redirectToGateway'])->name('payment');
            Route::get('callback', [PaystackController::class, 'handleGatewayCallback'])->name('callback');
        });

        //BKASH

        Route::group(['prefix' => 'bkash', 'as' => 'bkash.'], function () {
            Route::get('make-payment', [BkashPaymentController::class, 'make_tokenize_payment'])->name('make-payment');
            Route::any('callback', [BkashPaymentController::class, 'callback'])->name('callback')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //Liqpay
        Route::group(['prefix' => 'liqpay', 'as' => 'liqpay.'], function () {
            Route::get('payment', [LiqPayController::class, 'payment'])->name('payment');
            Route::any('callback', [LiqPayController::class, 'callback'])->name('callback')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //MERCADOPAGO
        Route::group(['prefix' => 'mercadopago', 'as' => 'mercadopago.'], function () {
            Route::get('pay', [MercadoPagoController::class, 'index'])->name('index');
            Route::post('make-payment', [MercadoPagoController::class, 'make_payment'])->name('make_payment');
        });

        //PAYMOB
        Route::group(['prefix' => 'paymob', 'as' => 'paymob.'], function () {
            Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
            Route::any('callback', [PaymobController::class, 'callback'])->name('callback')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        //PAYTABS
        Route::group(['prefix' => 'paytabs', 'as' => 'paytabs.'], function () {
            Route::any('pay', [PaytabsController::class, 'payment'])->name('pay');
            Route::any('callback', [PaytabsController::class, 'callback'])->name('callback')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
            Route::any('response', [PaytabsController::class, 'response'])->name('response')
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
        });

        Route::group(['prefix' => 'redsys', 'as' => 'redsys.'], function () {
            Route::any('pay', [RedSysPaymentController::class, 'get_initialize'])->name('pay');
            Route::any('callback/{status}/{payment_id}', [RedSysPaymentController::class, 'callback'])->name('callback');
        });

    });
}

Route::group(['prefix' => 'admin/payment'], function () {
    Route::group(['prefix' => 'configuration', 'as' => 'configuration.', 'middleware' => ['admin']], function () {
        Route::get('addon-payment-get', [PaymentConfigController::class, 'payment_config_get'])->name('addon-payment-get');
        Route::put('addon-payment-set', [PaymentConfigController::class, 'payment_config_set'])->name('addon-payment-set');
    });
});

Route::group(['prefix' => 'admin/sms'], function () {
    Route::group(['prefix' => 'configuration', 'as' => 'configuration.', 'middleware' => ['admin']], function () {
        Route::get('addon-sms-get', 'SMSConfigController@sms_config_get')->name('addon-sms-get');
        Route::put('addon-sms-set', 'SMSConfigController@sms_config_set')->name('addon-sms-set');
    });
});

