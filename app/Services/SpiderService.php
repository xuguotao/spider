<?php


namespace App\Services;


use App\Models\ErrorLog;
use App\Models\Students;
use App\Models\StudentUrl;
use Sunra\PhpSimple\HtmlDomParser;

class SpiderService extends BaseService
{
    const SEARCH_URL = "http://leads.ielts.cn/Student/PartialIndex";
    const Bill_LIST_URL = "https://mis.ielts.cn/Student/SignUp/StudentAccount_show.aspx?";

    private $searchCookie;
    private $activeRecordList = [];
    private $firstDay;
    private $startTime;
    private $mockLoginService;

    public function __construct(MockLoginService $mockLoginService)
    {
        $this->mockLoginService = $mockLoginService;
        $this->startTime = time();
        //$this->firstDay = strtotime('midnight first day of this month');
        $this->firstDay = strtotime('2018-09-01');
    }

    public function search($searchCookie, $offset, $limit)
    {
        /** @var Students $studentModel */
        $studentModelList = $this->getStudentList($offset, $limit);
        $this->searchCookie = $searchCookie;
        $headers[] = "Cookie:" . $searchCookie;

        foreach ($studentModelList as $studentModel) {
            if (time() - $this->startTime > 25 * 60) {
                echo "重新登录..." . "\n\r";
                $this->searchCookie = $this->mockLoginService->postLogin();
                $this->startTime = time();
            }

            echo "开始查询: " . $studentModel->student_name . "\n\r";
            $searchData = $this->generateSearchData($studentModel->student_name);
            $this->searchResult($studentModel, $searchData);
            $studentModel->is_done = 1;
            $studentModel->save();
        }
    }

    private function searchResult($studentModel, $searchData)
    {
        try {
            $studentListHtml = $this->curlPost(self::SEARCH_URL, $searchData, $this->generateHeaders());
            $dom = HtmlDomParser::str_get_html($studentListHtml);
            $maxPage = 1;
            if ($dom) {
                $maxPage = $this->getStudentPageMaxPage($dom);
                if ($maxPage >= 10) {
                    $maxPage = 10;
                    \Log::info('只查10页', $studentModel->toArray());
                }
                $aTag = $dom->find('tr td a');
                $studentURLList = [];

                foreach ($aTag as $el) {
                    if (str_is("*Student/SignUp/Student.aspx*", $el->href)) {
                        $billCount = $el->parent()->parent()->children(3)->innertext;
                        if ($billCount == 0) continue;
                        if (\Cache::has(md5($el->href))) {
                            continue;
                        } else {
                            $this->activeRecordList = [];
                            $url = $el->href;
                            $tempArray = explode("?", $url);
                            $billDateList = $this->getBillList($tempArray[1]);
                            $studentUrlModel = new StudentUrl();
                            $studentUrlModel->student_id = $studentModel->id;
                            $studentUrlModel->student_url = $url;
                            if ($this->isTarget($billDateList)) {

                                $studentUrlModel->bill_status = 1;
                                echo "查询到有交费: " . $studentModel->student_name . "\n\r";
                            }

                            $studentUrlModel->save();
                            \Cache::add(md5($url), 1 , 60);
                        }
                    }
                }
            } else {
                $this->searchResult($studentModel, $searchData);
            }

            if ($searchData['Page'] < $maxPage) {
                $searchData['Page'] += 1;
                $this->searchResult($studentModel, $searchData);
            }
        } catch (\Exception $e) {
            print_r($e->getTraceAsString());
        }
    }

    private function generateSearchData($studentName)
    {
        $searchData['Key'] = $studentName;
        $searchData['Page'] = 1;
        $searchData['SchoolSn'] = "SCH090306000001";
        $searchData['SearchType'] = 1;

        return $searchData;
    }

    private function getStudentList($offset, $limit)
    {
        $studentList = Students::whereIsDone(2)->skip($offset)->take($limit)->get();
        return $studentList;
    }

    private function isTarget($billList)
    {
        if (empty($billList)) {
            return false;
        }

        $timeFlag = false;
        $dateFlag = false;
        if (count($billList) >= 2) {
            $timeFlag = true;
        }
        foreach ($billList as $billDate) {
            if ($billDate >= $this->firstDay) {
                $dateFlag = true;
                break;
            }
        }

        return $timeFlag && $dateFlag;
    }

    public function getBillList($studentId, $page=1)
    {
        $billListHtml = $this->curlGet(self::Bill_LIST_URL . $studentId . "&page=" . $page , $this->generateHeaders());
        $dom = HtmlDomParser::str_get_html($billListHtml);
        if ($dom) {
            $getMaxPage = $this->getBillPageMaxPage($dom);
            $trList = $dom->find('table tr');

            foreach ($trList as $tr) {
                $billType = trim($tr->children(1)->innertext);
                $billDate = strtotime(trim($tr->children(11)->innertext));

                if ($billType == '充值') {
                    $this->activeRecordList[] = $billDate;
                }
            }

            $page++;
            if ($page <= $getMaxPage) {
                return $this->getBillList($studentId, $page);
            } else {
                return $this->activeRecordList;
            }
        } else {
            $errorLog = new ErrorLog();
            $errorLog->error = __METHOD__ . $studentId . $page;
        }

    }

    private function getBillPageMaxPage($billDom)
    {
        $aTagList = $billDom->find('div[id=ctl00_ContentPlaceHolderForm_Page_SA_S] a', -1);
        if ($aTagList) {
            $tempArr = explode("=",$aTagList->href);
            return $tempArr[count($tempArr) -1];
        } else {
            return 1;
        }
    }

    private function getStudentPageMaxPage($dom)
    {
        $pageDom = $dom->find('input[id=sourceCount]', 0);
        if(!empty($pageDom)) {
            $maxPage = ceil($pageDom->value / 15);
        } else {
            $maxPage = 1;
        }

        return $maxPage;
    }

    private function generateHeaders()
    {
        $headers[] = "Cookie: " . $this->searchCookie;

        return $headers;
    }
}