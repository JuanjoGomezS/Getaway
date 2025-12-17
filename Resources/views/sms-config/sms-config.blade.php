@extends('layouts.admin.app')
@push('css')
    <link href="{{asset('Modules/Gateways/public/assets/modules/css/select2.min.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('Modules/Gateways/public/assets/modules/config-settings/style.css')}}">
@endpush
@section('content')
    <div class="main-content">
        <div class="content container-fluid">
            <div class="payment-heading">
                <div class="page-title-wrap mb-3">
                    <h2 class="page-title">{{translate('sms_gateways_configuration')}}</h2>
                </div>
            </div>

            <div class="row">
                @foreach($data_values as $gateway)
                    <div class="col-md-6 mb-30 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="page-title">{{ucfirst(str_replace('_',' ',$gateway->key_name))}}</h4>
                            </div>
                            <div class="card-body p-30">
                                <form action="{{route('configuration.addon-sms-set')}}" method="POST"
                                      id="{{$gateway->key_name}}-form" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="discount-type">
                                        <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                            <div class="custom-radio">
                                                <input type="radio" id="{{$gateway->key_name}}-active"
                                                       name="status"
                                                       value="1" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'checked':''}}>
                                                <label
                                                    for="{{$gateway->key_name}}-active">{{translate('Active')}}</label>
                                            </div>
                                            <div class="custom-radio">
                                                <input type="radio" id="{{$gateway->key_name}}-inactive"
                                                       name="status"
                                                       value="0" {{$data_values->where('key_name',$gateway->key_name)->first()->live_values['status']?'':'checked'}}>
                                                <label
                                                    for="{{$gateway->key_name}}-inactive">{{translate('Inactive')}}</label>
                                            </div>
                                        </div>

                                        <input name="gateway" value="{{$gateway->key_name}}" class="d-none">
                                        <input name="mode" value="live" class="d-none">

                                        @php($skip=['gateway','mode','status'])
                                        @foreach($data_values->where('key_name',$gateway->key_name)->first()->live_values as $key=>$value)
                                            @if(!in_array($key,$skip))
                                                <div class="form-floating mb-30 mt-30">
                                                    <label for="exampleFormControlInput1"
                                                           class="form-label">{{ucfirst(str_replace('_',' ',$key))}}
                                                        *</label>
                                                    <input type="text" class="form-control"
                                                           name="{{$key}}"
                                                           placeholder="{{ucfirst(str_replace('_',' ',$key))}} *"
                                                           value="{{env('APP_ENV')=='demo'?'':$value}}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn--primary demo_check">
                                            {{translate('Update')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('Modules/Gateways/public/assets/modules/js/jquery.min.js')}}"></script>
    <script src="{{asset('Modules/Gateways/public/assets/modules/js/select2.min.js')}}"></script>
@endpush
