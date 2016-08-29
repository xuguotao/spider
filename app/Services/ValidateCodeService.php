<?php


namespace App\Services;


class ValidateCodeService
{
    public function getCodeNumberFromImage($img)
    {
        $sourceData = $this->generateSourceData($img);
        $this->printData($sourceData);
        $clearData = $this->clearSourceData($sourceData);
        $this->printData($clearData);
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