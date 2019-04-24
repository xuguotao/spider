<?php
namespace App\Console\Commands;


use App\Models\OrderListModel;
use App\Services\Second\LoginService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StudentList extends Command
{
    const STUDENT_LIST_URL = 'http://ql.ielts.cn/admin/order/list4ajax';

    protected $signature = 'spider:student-list';
    protected $description = '抓取本月有订单的学生列表';
    /** @var Client */
    protected $loginClient = null;
    protected $targetMonth = '';
    protected $orderList = [];

    public function handle(LoginService $loginService, OrderListModel $orderListModel)
    {
        $this->targetMonth = date('Y-m');
        $this->loginClient = $loginService->getLoginClient();
        if ($this->loginClient) {
            $this->info('登录成功');
            $data = $this->getStudentListData();
            $rs = $orderListModel->addAll($data);
        } else {
            $this->error("登录失败");
        }

    }

    private function getStudentListData($nowPage = 1)
    {
        $pageSize = 50;
        $startRecord = ($nowPage-1) * $pageSize;

        $fromData = array(
            'dtGridPager' => '{"isExport":false,"pageSize":' . $pageSize . ',"startRecord":' . $startRecord . ',"nowPage":' . $nowPage . ',"recordCount":-1,"pageCount":-1,"parameters":{"state":1,"name":"","mobile":"","orderId":""},"fastQueryParameters":{},"advanceQueryConditions":[],"advanceQuerySorts":[]}'
        );

        $response = $this->loginClient->request('POST', self::STUDENT_LIST_URL, [
            'form_params' => $fromData
        ]);
        $insertData = [];
        $data = json_decode($response->getBody()->getContents(), true);
        if (isset($data['exhibitDatas']) and is_array($data['exhibitDatas'])) {
            foreach ($data['exhibitDatas'] as $row) {
                if ($row['createTime'] >= $this->targetMonth . '-20 00:00:00' and $row['createTime'] <= $this->targetMonth . '-31 23:59:59') {
                    $insertData[] = array(
                        'student_id' => $row['relStudentId'],
                        'student_name' => $row['studentName'],
                        'student_sn' => $row['studentSn'],
                        'student_type' => $row['type'],
                        'res_id' => $row['relResourceId'],
                        'res_pid' => $row['relResourcePid'],
                        'school_area' => $row['schoolArea'],
                        'order_sn' => $row['orderId'],
                        'order_created_time' => $row['createTime'],
                        'order_paid' => $row['paid'],
                        'order_pay_time' => $row['payTime'],
                        'order_state' => $row['state']
                    );
                }
            }
        }

        if (count($insertData) > 0) {
            $this->orderList = array_merge($this->orderList, $insertData);
            $this->info("合并完第 " . $nowPage . " 页，共 " . count($this->orderList) . " 条数据");
            DB::table('order_list')->insert($insertData);
            sleep(rand(0,3));
            return $this->getStudentListData($nowPage+1);
        } else {
            return $this->orderList;
        }
    }



}