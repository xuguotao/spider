<?php
/**
 * Created by PhpStorm.
 * Date: 2018/11/30
 * Time: 6:09 PM
 */

namespace App\Services\Second;


use GuzzleHttp\Client;

class LoginService
{
    const LOGIN_URL = 'http://gedu.pxjy.com/login';
    const QINGLONG_DIRECT_URL = 'http://gedu.pxjy.com/direct?subId=19';

    public function getLoginClient()
    {
        $client = new Client(['cookies' => true]);
        $loginParams = array(
            'loginName' => env('WJJ_USERNAME'),
            'password' => env('WJJ_PASSWORD')
        );
        $client->request('POST', self::LOGIN_URL, ['form_params' => $loginParams]);
        $response = $client->request('GET',self::QINGLONG_DIRECT_URL, [
            'allow_redirects' => [
                'max'             => 10,        // allow at most 10 redirects.
                'strict'          => true,      // use "strict" RFC compliant redirects.
                'referer'         => true,      // add a Referer header
                'protocols'       => ['http','https'], // only allow https URLs
//                'on_redirect'     => $onRedirect,
//                'track_redirects' => true
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            return $client;
        }
    }
}