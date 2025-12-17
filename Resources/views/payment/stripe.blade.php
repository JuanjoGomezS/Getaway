@extends('Gateways::payment.layouts.master')

@push('script')
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/common.css')}}">
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    <h1 class="text-center">{{translate('Please do not refresh this page')}}...</h1>

    <script type="text/javascript">
        'use strict';

        var stripe = Stripe('{{$config->published_key}}');
        document.addEventListener("DOMContentLoaded", function () {
            fetch("{{ url("payment/stripe/token/?payment_id={$data->id}") }}", {
                method: "GET",
            }).then(function (response) {
                console.log(response)
                return response.text();
            }).then(function (session) {
                console.log(session)
                return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
            }).then(function (result) {
                if (result.error) {
                    alert(result.error.message);
                }
            }).catch(function (error) {
                console.error("error:", error);
            });
        });

    </script>
@endsection
