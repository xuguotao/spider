<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * \App\Models\StudentUrl
 *
 * @property integer $id Id
 * @property integer $student_id
 * @property string $student_url
 * @property boolean $bill_status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereStudentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereStudentUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereBillStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\StudentUrl whereDeletedAt($value)
 * @mixin \Eloquent
 */
class StudentUrl extends Model
{
    use SoftDeletes;

    public $table = "student_url";
}
