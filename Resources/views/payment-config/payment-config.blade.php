@extends('layouts.admin.app')

@section('title', translate('Payment Setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/payment-config.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="payment-heading">
            <div class="page-title-wrap mb-3">
                <h2 class="page-title">{{translate('payment_gateway_configuration')}}</h2>
            </div>
        </div>

        <div class="row">
            @foreach($data_values as $payment)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <form action="{{route('configuration.addon-payment-set')}}" method="POST"
                              id="{{$payment->key_name}}-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-header d-flex flex-wrap align-content-around">
                                <h5>
                                    <span class="text-uppercase">{{str_replace('_',' ',$payment->key_name)}}</span>
                                </h5>
                                <label
                                    class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex {{ $payment['is_active'] == 1 ? 'checked' : '' }}">
                                    <span
                                        class="mr-2 switch--custom-label-text on text-uppercase">{{ translate('on') }}</span>
                                    <span
                                        class="mr-2 switch--custom-label-text off text-uppercase">{{ translate('off') }}</span>
                                    <input type="checkbox" name="status" value="1"
                                           class="toggle-switch-input" {{ $payment['is_active'] == 1 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                </label>
                            </div>

                            @php($additional_data = $payment['additional_data'] != null ? json_decode($payment['additional_data']) : [])
                            <div class="card-body">
                                <div class="payment--gateway-img">
                                    <img class="class-1" id="{{$payment->key_name}}-image-preview"
                                         src="{{asset('storage/app/public/payment_modules/gateway_image')}}/{{$additional_data != null ? $additional_data->gateway_image : ''}}"
                                         onerror="this.src='{{asset('Modules/Gateways/public/placeholder.png')}}'"
                                         alt="public">
                                </div>

                                <input name="gateway" value="{{$payment->key_name}}" class="d-none">

                                @php($mode=$data_values->where('key_name',$payment->key_name)->first()->live_values['mode'])
                                <div class="form-floating mb-3">
                                    <select class="js-select form-control theme-input-style w-100" name="mode">
                                        <option value="live" {{$mode=='live'?'selected':''}}>{{translate('Live')}}</option>
                                        <option value="test" {{$mode=='test'?'selected':''}}>{{translate('Test')}}</option>
                                    </select>
                                </div>

                                @php($skip=['gateway','mode','status'])
                                @foreach($data_values->where('key_name',$payment->key_name)->first()->live_values as $key=>$value)
                                    @if(!in_array($key,$skip))
                                        <div class="form-floating mb-3">
                                            <label for="exampleFormControlInput1"
                                                   class="form-label">{{ucwords(str_replace('_',' ',$key))}}
                                                *</label>
                                            <input type="text" class="form-control"
                                                   name="{{$key}}"
                                                   placeholder="{{ucwords(str_replace('_',' ',$key))}} *"
                                                   value="{{env('APP_ENV')=='demo'?'':$value}}">
                                        </div>
                                    @endif
                                @endforeach

                                <div class="form-floating mb-3">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('payment_gateway_title')}}</label>
                                    <input type="text" class="form-control"
                                           name="gateway_title"
                                           placeholder="{{translate('payment_gateway_title')}}"
                                           value="{{$additional_data != null ? $additional_data->gateway_title : ''}}">
                                </div>

                                <div class="form-floating mb-3">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('Choose_Logo')}}</label>
                                    <input type="file" class="form-control" name="gateway_image"
                                           accept=".jpg, .png, .jpeg|image/*">
                                </div>

                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('Modules/Gateways/public/assets/modules/js/payment-config.js')}}"></script>
@endpush
