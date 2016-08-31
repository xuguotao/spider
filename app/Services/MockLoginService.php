<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Sunra\PhpSimple\HtmlDomParser;

class MockLoginService
{
    const LOGIN_URL = "https://mis.ielts.cn/Login.aspx";

    public function getLoginCookie()
    {
        $res = $this->getClient()->request('GET', self::LOGIN_URL, ['verify' => false]);
        $cookieStr = $res->getHeaderLine("Set-Cookie");
        $cookieArray = explode(";", $cookieStr);
        $rs['cookie'] = $cookieArray;
        $cookie = $cookieArray[0];
        $dom = HtmlDomParser::str_get_html($res->getBody()->getContents());
        $inputData = [];
        foreach ($dom->find('input') as $el) {
            $inputData[$el->name] = $el->value;
        }

        return [$cookie, $inputData];
    }

    public function postLogin()
    {

        list($cookie, $inputData) = $this->getLoginCookie();
        $validateCodeService = new ValidateCodeService();
        $validateCodeNumber = $validateCodeService->getValidateCodeNumber($cookie);

        $inputData['UserName'] = "7999";
        $inputData['PassWord'] = "Woaini123";
        $inputData['TBValidate'] = $validateCodeNumber;
        $inputData['LoginButton.x'] = 84;
        $inputData['LoginButton.y'] = 16;
        $inputData['__VIEWSTATEGENERATOR']="C2EE9ABB";
//        $inputData['__VIEWSTATE'] = urlencode($inputData['__VIEWSTATE']);
//        $inputData['__EVENTVALIDATION'] = urlencode($inputData['__EVENTVALIDATION']);

        $header[] = "Cookie: " . $cookie;
        print_r($header);
        print_r($inputData);
        echo \GuzzleHttp\Psr7\build_query($inputData);
        $ch = curl_init(self::LOGIN_URL);
        dd();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, \GuzzleHttp\Psr7\build_query($inputData));//POST数据

        $rs = curl_exec($ch);
        print_r($rs);
        print_r(curl_getinfo($ch));
        curl_close($ch);

//        $cookieJar = new CookieJar();
//        $setCookie = explode("=", $cookie);
//        $cookieJar->setCookie(new SetCookie($setCookie));
//        $res = $this->getClient()->post(self::LOGIN_URL, [
//            "form_params" => $inputData,
//            'cookies' => $cookieJar,
//        ]);
//
//        print_r($res->getBody()->getContents());
    }

    public function getFormData($body)
    {

    }

    private function getClient()
    {
        $client = new Client(
            [
                'base_uri'        => 'https://mis.ielts.cn',
                'timeout'         => 0,
                'defaults' => [
                    "verify" => false
                ],
            ]
        );

        return $client;
    }
}