<?php

namespace App\Http\Controllers;

use App\Models\Students;
use App\Models\StudentUrl;
use App\Services\ValidateCodeService;

class SpiderController extends Controller
{
    const VALIDATE_CODE_URL = "https://mis.ielts.cn/ValidateLogin.aspx";

    private $validateCodeService;

    public function __construct(ValidateCodeService $validateCodeService)
    {
        $this->validateCodeService = $validateCodeService;
    }

    public function index()
    {
        $sql = "select distinct(url.student_url) as link, student_name from student_url as url
left join students on url.student_id = students.id
where url.bill_status = 1";

        $result = \DB::select($sql);
        foreach ($result as $row) {
            $aa = explode("?", $row->link);
            $row->sId = "aaa";
        }

        return view('spider.index', compact('result'));
    }

    public function exportStudent()
    {
        $content = \File::get(storage_path('wqy0501.txt'));
        $studentArray = explode("\n", $content);
        $sql = "insert into students (student_name) values ";
        foreach ($studentArray as $studentName) {
            if ($studentName != "") {
                $sData[] = '("' . trim($studentName) . '")';
            }
        }
        $sql .= join(",", $sData);

        echo $sql;
    }
}
