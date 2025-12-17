<?php

namespace Modules\Gateways\Traits;

trait AddonActivationClass
{
    public function isActive(): array
    {
        if (self::is_local()) {
            return [
                'active' => 1
            ];
        } else {
            $remove = array("http://", "https://", "www.");
            $url = str_replace($remove, "", url('/'));
            $info = include('Modules/Gateways/Addon/info.php');

            foreach (SOFTWARE_INFO as $soft_info) {
                if ($soft_info['software_id'] == base64_decode(env('SOFTWARE_ID'))) {
                    $route = route($soft_info['values']['addon_index_route']);
                }
            }

            if ($info['username'] == '' || $info['purchase_code'] == '') {
                return [
                    'active' => 0,
                    'route' => $route
                ];
            }

            $post = [
                base64_decode('dXNlcm5hbWU=') => $info['username'],//un
                base64_decode('cHVyY2hhc2Vfa2V5') => $info['purchase_code'],//pk
                base64_decode('c29mdHdhcmVfaWQ=') => '48481246',//sid
                base64_decode('ZG9tYWlu') => $url,
            ];
            try {
                $ch = curl_init(base64_decode('aHR0cHM6Ly9jaGVjay42YW10ZWNoLmNvbS9hcGkvdjEvYWN0aXZhdGlvbi1jaGVjaw=='));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = (int)base64_decode(json_decode($response, true)['active']);
            } catch (\Exception $exception) {
                $response = 1;
            }

            if (!$response) {
                $info = include('Modules/Gateways/Addon/info.php');
                $info['is_published'] = 0;
                $info['username'] = '';
                $info['purchase_code'] = '';
                $str = "<?php return " . var_export($info, true) . ";";
                file_put_contents(base_path('Modules/Gateways/Addon/info.php'), $str);
            }

            return [
                'active' => $response,
                'route' => $route
            ];
        }
    }

    public function is_local(): bool
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if (!in_array(request()->ip(), $whitelist)) {
            return true;
        }

        return true;
    }
}
