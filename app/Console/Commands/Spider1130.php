<?php
namespace App\Console\Commands;


use App\Models\OrderListModel;
use App\Services\Second\LoginService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Spider1130 extends Command
{
    const STUDENT_PAY_LOG_URL = 'http://ql.ielts.cn/admin/orderPayInfo/findOrderPayDetailList4Ajax';

    protected $signature = 'spider:start';
    protected $description = '开始抓取';

    /** @var Client */
    protected $loginClient = null;

    public function handle(LoginService $loginService, OrderListModel $orderListModel) {
        $this->loginClient = $loginService->getLoginClient();

        if ($this->loginClient) {
            $this->info('登录成功');

            $studentIdList = $this->getStudentIdList();
            print_r(count($studentIdList));
            foreach ($studentIdList as $studentId)
            {
                $this->info('开始获取 ' . $studentId . ' 的缴费信息');
                $this->getStudentPayLog($studentId);
                //sleep(rand(0, 10));
            }
        } else {
            $this->error("登录失败");
        }
    }

    protected function getStudentIdList()
    {

        $studentIdList = OrderListModel::distinct('student_id')
            ->where('student_type', '0')
            ->where('id', '>=', 29636)
            ->pluck('student_id');

        return $studentIdList;
    }

    protected function getStudentPayLog($studentId) {
        $fromParams = [
            'dtGridPager' => '{"isExport":false,"pageSize":50,"startRecord":0,"nowPage":1,"recordCount":-1,"pageCount":-1,"parameters":{"studentId":"' . $studentId . '","payment":"","type":"","signupStatus":"","payState":""},"fastQueryParameters":{},"advanceQueryConditions":[],"advanceQuerySorts":[]}'
        ];

        $response = $this->loginClient->request('POST', self::STUDENT_PAY_LOG_URL, [
            'form_params' => $fromParams
        ]);
        $payLog = [];
        if ($response->getStatusCode() == 200) {
            $rsData = json_decode($response->getBody()->getContents(), true);
            if (isset($rsData['exhibitDatas']) and count($rsData['exhibitDatas']) > 0) {
                foreach ($rsData['exhibitDatas'] as $row) {
                    $payLog[] = array(
                        'student_id' => $studentId,
                        'order_id' => isset($row['orderId']) ? $row['orderId'] : '',
                        'pay_state' => isset($row['payState']) ? $row['payState'] : '',
                        'payment' => isset($row['payment']) ? $row['payment'] : '',
                        'pay_time' => isset($row['operateTime']) ? $row['operateTime'] : null,
                        'paid' => isset($row['receipts']) ? $row['receipts'] : ''
                    );
                }
                $rs = DB::table('student_pay_log')->insert($payLog);
                if ($rs) {
                    $this->info($studentId . '：一共有 ' . count($payLog) . ' 支付记录入库');
                }
            } else {
                $this->warn($studentId . "：无缴费记录");
            }
        }
    }

}