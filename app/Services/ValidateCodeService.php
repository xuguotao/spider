<?php


namespace App\Services;


use App\Constants\ValidateCodeNumber;

class ValidateCodeService
{
    const VALIDATE_CODE_URL = "https://mis.ielts.cn/ValidateLogin.aspx";

    public function getValidateCodeNumber($cookie)
    {
        $img = $this->getValidateCodeImage($cookie);
        return $this->getCodeNumberFromImage($img);
    }

    public function getCodeNumberFromImage($img)
    {
        $validateCodeNum = "";
        $sourceData = $this->generateSourceData($img);
        $clearData = $this->clearSourceData($sourceData);
        $splitNumData = $this->splitCode($clearData);
        foreach ($splitNumData as $num => $numData) {
            $validateCodeNum .= $this->getSimilarMaxNum($numData);
        }

        return $validateCodeNum;
    }

    private function generateSourceData($img)
    {
        list($width, $height) = getimagesize($img);
        $rs = imagecreatefromgif($img);

        $sourceData = [];
        for($i = 5; $i < $height - 5; $i++) {
            for ($j = 8; $j < $width - 12; $j++) {
                $index = imagecolorat($rs, $j, $i);
                $rgb = imagecolorsforindex($rs, $index);
                if ($rgb['red'] < 130 || $rgb['blue'] < 130 || $rgb['green'] < 130) {
                    $sourceData[$i][$j] = 1;
                } else {
                    $sourceData[$i][$j] = 0;
                }
            }
        }

        $rs = [];
        foreach ($sourceData as $row) {
            $rs[] = array_values($row);
        }

        return $rs;
    }

    private function clearSourceData($sourceData)
    {
        foreach ($sourceData as $i => $row) {
            foreach ($row as $j => $value) {
                $num = 0;
                if ($sourceData[$i][$j] == 1) {
                    if (isset($sourceData[$i][$j-1])) {
                        $num += $sourceData[$i][$j-1];
                    }
                    if (isset($sourceData[$i][$j+1])) {
                        $num += $sourceData[$i][$j+1];
                    }
                    if (isset($sourceData[$i-1][$j-1])) {
                        $num += $sourceData[$i-1][$j-1];
                    }
                    if (isset($sourceData[$i-1][$j])) {
                        $num += $sourceData[$i-1][$j];
                    }
                    if (isset($sourceData[$i-1][$j+1])) {
                        $num += $sourceData[$i-1][$j+1];
                    }
                    if (isset($sourceData[$i+1][$j-1])) {
                        $num += $sourceData[$i+1][$j-1];
                    }
                    if (isset($sourceData[$i+1][$j])) {
                        $num += $sourceData[$i+1][$j];
                    }
                    if (isset($sourceData[$i+1][$j+1])) {
                        $num += $sourceData[$i+1][$j+1];
                    }
                }

                if ($num <= 2) {
                    $sourceData[$i][$j] = 0;
                }
            }
        }

        return $sourceData;
    }

    private function splitCode($codeData)
    {
        $splitData = [];
        foreach ($codeData as $i => $row) {
            foreach ($row as $j => $value) {
                if ($j < 9) {
                    $splitData[0][$i][$j] = $value;
                } elseif ($j >= 9 && $j < 18) {
                    $splitData[1][$i][$j] = $value;
                } elseif ($j >= 18 && $j < 27) {
                    $splitData[2][$i][$j] = $value;
                }elseif ($j >= 27 && $j < 36) {
                    $splitData[3][$i][$j] = $value;
                }
            }
        }

        $rs = [];
        foreach ($splitData as $first => $num) {
            foreach ($num as $row) {
                $rs[$first][] = array_values($row);
            }
        }

        return $rs;
    }

    private function getSimilarMaxNum($splitNumData)
    {
        $max = 0;
        $rsNum = -1;
        foreach (ValidateCodeNumber::$number as $num => $numData) {
            $similarValue = similar_text(json_encode($numData), json_encode($splitNumData));
            if ($similarValue > $max) {
                $max = $similarValue;
                $rsNum = $num;
            }
        }

        return $rsNum;
    }

    private function getValidateCodeImage($loginCookie)
    {
        $header[] = "Cookie: " . $loginCookie;
        $ch = curl_init(self::VALIDATE_CODE_URL);
        $filename = date('YmdHis') . ".gif";
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $rs = curl_exec($ch);
        curl_close($ch);

        $tp = fopen(storage_path('validate_code') . "/" . $filename, 'a');
        fwrite($tp, $rs);
        fclose($tp);

        //$filename = "20160830060806.gif";
        return storage_path('validate_code/' . $filename);
    }

    private function printData($data)
    {
        $rs = "";
        foreach ($data as $row) {
            foreach ($row as $value) {
                if ($value == 1) {
                    $rs .= "◼︎";
                } else {
                    $rs .=  "◻︎";
                }
            }
            $rs .= "\n\r";
        }

        print $rs;
    }
}