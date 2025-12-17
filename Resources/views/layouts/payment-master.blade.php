<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{translate('Configuration')}}</title>
        @stack('css')
    </head>
    <body>
        @yield('payment')
        <script src="{{asset('Modules/Gateways/public/assets/modules/js/select2.min.js')}}"></script>
        @stack('script_2')
    </body>
</html>
