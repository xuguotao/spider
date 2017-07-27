<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Str;
use Sunra\PhpSimple\HtmlDomParser;

class MockLoginService extends BaseService
{
    const LOGIN_URL = "https://mis.ielts.cn/Login.aspx";
    private $tryTimes = 1;

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
        echo "尝试第" . $this->tryTimes . "登陆....";

        list($cookie, $inputData) = $this->getLoginCookie();
        unset($inputData['LoginButton']);

        $validateCodeService = new ValidateCodeService();
        $validateCodeNumber = $validateCodeService->getValidateCodeNumber($cookie);

//        $inputData['UserName'] = "8072";
//        $inputData['PassWord'] = "1234qwerASDF";
        $inputData['UserName'] = "7999";
        $inputData['PassWord'] = "Yoyo2012";
        $inputData['TBValidate'] = $validateCodeNumber;
        $inputData['LoginButton.x'] = 84;
        $inputData['LoginButton.y'] = 16;
        $inputData['__VIEWSTATEGENERATOR']="C2EE9ABB";

        $header[] = "Cookie: " . $cookie;
        $postLoginHtml = $this->curlPost(self::LOGIN_URL, $inputData, $header);

        $dom = HtmlDomParser::str_get_html($postLoginHtml);
        $afterFormData = [];

        foreach ($dom->find('input') as $el) {
            $afterFormData[$el->name] = $el->value;
        }

        unset($afterFormData['Button_Login']);
        $afterFormData['Button_Login.x'] = 0;
        $afterFormData['Button_Login.y'] = 0;
        $afterFormData['SelectedBranchSN'] = 'BSN140711000001';
        $afterFormData['HiddenFiled_S_System'] = 'S07';

        $salesHtml = $this->curlPost(self::LOGIN_URL, $afterFormData, $header);
        preg_match('/Set-Cookie:(.*);/iU', $salesHtml, $returnCookie);

        if (isset($returnCookie[1]) && str_is("MIS_LoginUser*", trim($returnCookie[1]))) {
            echo "登陆成功..." . "\n\r";
            return trim($cookie) . "; " . trim($returnCookie[1]);
        } elseif ($this->tryTimes < 4) {
            $this->tryTimes++;
            return $this->postLogin();
        } else {
            echo "登陆失败..." . "\n\r";
            die();
        }
    }

    private function getClient()
    {
        $client = new Client(
            [
                'defaults' => [
                    "verify" => false
                ],
            ]
        );

        return $client;
    }
}