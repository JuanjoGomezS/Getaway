@extends('Gateways::payment.layouts.master')

@push('script')
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/razor-pay.css')}}">
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/common.css')}}">
@endpush

@section('content')
    <h1 class="text-center">{{translate('Please do not refresh this page')}}...</h1>

    <form action="{!!route('razor-pay.payment',['payment_id'=>$data->id])!!}" id="form" method="POST">
        @csrf
        <script src="https://checkout.razorpay.com/v1/checkout.js"
                data-key="{{ config()->get('razor_config.api_key') }}"
                data-amount="{{round($data->payment_amount, 2)*100}}"
                data-buttontext="Pay {{ round($data->payment_amount, 2) . ' ' . $data->currency_code }}"
                data-name="{{$business_name}}"
                data-description="{{$data->payment_amount}}"
                data-image="{{$business_logo}}"
                data-prefill.name="{{$payer->name ?? ''}}"
                data-prefill.email="{{$payer->email ?? ''}}"
                data-theme.color="#ff7529">
        </script>
        <button class="btn btn-block d--none" id="pay-button" type="submit"></button>
    </form>

    @push('script_2')
        <script src="{{asset('Modules/Gateways/public/assets/modules/js/razor-pay.js')}}"></script>
    @endpush
@endsection
