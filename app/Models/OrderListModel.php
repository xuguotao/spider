<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\OrderListModel
 *
 * @property int $id APP版本Id
 * @property int|null $student_id
 * @property string $student_name
 * @property string|null $student_sn
 * @property int|null $student_type
 * @property string|null $school_area
 * @property string|null $order_sn
 * @property string|null $order_created_time
 * @property int|null $order_paid
 * @property string|null $order_pay_time
 * @property int $order_state
 * @property int $is_done
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereOrderCreatedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereOrderPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereOrderPayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereOrderState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereSchoolArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereStudentName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereStudentSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereStudentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderListModel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class OrderListModel extends Model
{
    public $table = 'order_list';

    public function addAll($data) {
        $rs = DB::table($this->getTable())->insert($data);

        return $rs;
    }
}