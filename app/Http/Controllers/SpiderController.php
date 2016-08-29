<?php

namespace App\Http\Controllers;

use App\Services\ValidateCodeService;
use Illuminate\Http\Request;

use App\Http\Requests;

class SpiderController extends Controller
{
    const VALIDATE_CODE_URL = "https://mis.ielts.cn/ValidateLogin.aspx";

    private $validateCodeService;

    public function __construct(ValidateCodeService $validateCodeService)
    {
        $this->validateCodeService = $validateCodeService;
    }

    public function validateCode()
    {
        $validateCodeImage = $this->getValidateCodeImage();
        $this->validateCodeService->getCodeNumberFromImage($validateCodeImage);
        echo $validateCodeImage;
    }

    private function getValidateCodeImage()
    {
        $ch = curl_init(self::VALIDATE_CODE_URL);
        $filename = date('YmdHis') . ".gif";
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $rs = curl_exec($ch);
        curl_close($ch);

        $tp = fopen(storage_path('validate_code') . "/" . $filename, 'a');
        fwrite($tp, $rs);
        fclose($tp);

        return storage_path('validate_code/' . $filename);
    }
}
