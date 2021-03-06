<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * \App\Models\Students
 *
 * @property integer $id APP版本Id
 * @property string $student_name
 * @property string $student_url
 * @property boolean $is_done
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereStudentName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereStudentUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereIsDone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Students whereDeletedAt($value)
 * @mixin \Eloquent
 */
class Students extends Model
{
    use SoftDeletes;

    public $table = "students";
}
