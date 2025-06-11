<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

class ZoomApi
{
    public function generateJWT()
    {
        $key = 'SQQ1hz9WTUC661rEmBbasA';
        $secret = 'rvVLqPkEcYtrcdyBwO6YrEZWPcDYtwyP8xL8';
        $token = [
            'iss' => $key,
            // The benefit of JWT is expiry tokens, set this one to expire in 1 minute
            'exp' => time() + 600,
        ];

        return JWT::encode($token, $secret);
    }

    public function processCurl($method, $url, $data = false)
    {
        $curl = curl_init();
        $header = [
            'Authorization: Bearer '.$this->generateJWT(),
        ];

        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                array_push($header, 'Accept:application/json', 'Content-Type: application/json', 'Content-Length: '.strlen(json_encode($data)));

                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_PUT, 1);

                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data));
                }
                break;
            case 'PATCH':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                }
                array_push($header, 'Accept:application/json', 'Content-Type: application/json', 'Content-Length: '.strlen(json_encode($data)));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data));
                }
                break;
            default:
                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data));
                }
        }

        // add token to the authorization header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($curl));
        $info = curl_getinfo($curl);

        curl_close($curl);

        $response = [
            'data' => $result,
            'http_code' => $info['http_code'],
        ];

        return $response;
    }
}
