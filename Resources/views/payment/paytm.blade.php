@extends('Gateways::payment.layouts.master')

@push('script')
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/common.css')}}">
@endpush

@section('content')
    <h1 class="text-center">{{translate('Please do not refresh this page')}}...</h1>
    <form method="post" action="<?php echo \Illuminate\Support\Facades\Config::get('paytm_config.PAYTM_TXN_URL') ?>"
          id="form">
        <table>
            <tbody>
            @foreach($paramList as $name => $value)
                <input type="hidden" name="{{$name}}" value="{{$value}}">
            @endforeach
            <input type="hidden" name="CHECKSUMHASH" value="{{$checkSum}}">
            </tbody>
        </table>
    </form>

    @push('script_2')
        <script src="{{asset('Modules/Gateways/public/assets/modules/js/paytm.js')}}"></script>
    @endpush
@endsection
