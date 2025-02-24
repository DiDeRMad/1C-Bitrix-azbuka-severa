<?php

namespace Dev\SendPulse;


class BasicController
{
    private static $clientId = '5d2bc0f2d5ac8dcb09f2446ca70948c1';
    private static $secret = 'c34df94cede0458cffa9db4fe854cc84';
    protected static $token = '';

    protected static function getToken()
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => self::$clientId,
            'client_secret' => self::$secret
        ];
        $pulseRequest = self::sendRequest('POST', 'oauth/access_token', $data);
        $pulseRequest = json_decode($pulseRequest, 1);
        if ($pulseRequest['access_token']) {
            self::$token = $pulseRequest['access_token'];
            return json_encode(['status' => 'ok', 'token' => self::$token]);
        } else {
            return json_encode(['status' => 'error', 'error' => $pulseRequest['error']]);
        }
    }

    protected static function sendRequest($type = 'POST', $method = '', $data = [], $token = '')
    {
        $data = json_encode($data);

        $curl = curl_init();

        $headers = ['Content-Type: application/json'];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.sendpulse.com/' . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}