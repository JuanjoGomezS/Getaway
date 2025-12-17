@extends('adminmodule::layouts.master')
@section('title', translate('Payment Setup'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/css/demandium-payment-config.css')}}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="payment-heading">
            <div class="page-title-wrap mb-3">
                <h2 class="page-title">{{translate('payment_gateway_configuration')}}</h2>
            </div>
        </div>

        <div class="row">
            @foreach($data_values as $gateway)
                <div class="col-12 col-md-6 mb-30">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="page-title">{{translate($gateway->key_name)}}</h4>
                        </div>
                        <div class="card-body p-30">
                            <form action="{{route('configuration.addon-payment-set')}}" method="POST"
                                  id="{{$gateway->key_name}}-form" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @php($additional_data = $gateway['additional_data'] != null ? json_decode($gateway['additional_data']) : [])
                                <div class="discount-type">
                                    <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                        <div class="custom-radio">
                                            <input type="radio" id="{{$gateway->key_name}}-active"
                                                   name="status"
                                                   value="1" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'checked':''}}>
                                            <label
                                                for="{{$gateway->key_name}}-active">{{translate('active')}}</label>
                                        </div>
                                        <div class="custom-radio">
                                            <input type="radio" id="{{$gateway->key_name}}-inactive"
                                                   name="status"
                                                   value="0" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'':'checked'}}>
                                            <label
                                                for="{{$gateway->key_name}}-inactive">{{translate('inactive')}}</label>
                                        </div>
                                    </div>

                                    <div class="payment--gateway-img justify-content-center d-flex align-items-center">
                                        <img class="class-1" id="{{$gateway->key_name}}-image-preview"
                                             src="{{asset('storage/app/public/payment_modules/gateway_image')}}/{{$additional_data != null ? $additional_data->gateway_image : ''}}"
                                             onerror="this.src='{{asset('public/assets/admin-module')}}/img/placeholder.png'"
                                             alt="public">
                                    </div>

                                    <input name="gateway" value="{{$gateway->key_name}}" class="hide-div">

                                    @php($mode=$data_values->where('key_name',$gateway->key_name)->first()->live_values['mode'])
                                    <div class="form-floating mb-30 mt-30">
                                        <select class="js-select theme-input-style w-100" name="mode">
                                            <option
                                                value="live" {{$mode=='live'?'selected':''}}>{{translate('live')}}</option>
                                            <option
                                                value="test" {{$mode=='test'?'selected':''}}>{{translate('test')}}</option>
                                        </select>
                                    </div>

                                    @php($skip=['gateway','mode','status'])
                                    @foreach($data_values->where('key_name',$gateway->key_name)->first()->live_values as $key=>$value)
                                        @if(!in_array($key,$skip))
                                            <div class="form-floating mb-30 mt-30">
                                                <input type="text" class="form-control"
                                                       name="{{$key}}"
                                                       placeholder="{{translate($key)}} *"
                                                       value="{{env('APP_ENV')=='demo'?'':$value}}">
                                                <label>{{translate($key)}} *</label>
                                            </div>
                                        @endif
                                    @endforeach

                                    <div class="form-floating mb-4">
                                        <input type="text" class="form-control" id="{{$gateway->key_name}}-title"
                                               name="gateway_title"
                                               placeholder="{{translate('payment_gateway_title')}}"
                                               value="{{$additional_data != null ? $additional_data->gateway_title : ''}}">
                                        <label for="{{$gateway->key_name}}-title"
                                               class="form-label">{{translate('payment_gateway_title')}}</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="file" class="form-control" name="gateway_image"
                                               accept=".jpg, .png, .jpeg|image/*" id="{{$gateway->key_name}}-image">
                                        <label for="{{$gateway->key_name}}-image"
                                               class="form-label">{{translate('Choose_Logo')}}</label>
                                    </div>

                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn--primary demo_check">
                                        {{translate('update')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('Modules/Gateways/public/assets/modules/js/demandium-payment-config.js')}}"></script>
@endpush

