<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * \App\Models\ErrorLog
 *
 * @mixin \Eloquent
 * @property integer $id APP版本Id
 * @property string $error
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ErrorLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ErrorLog whereError($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ErrorLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ErrorLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ErrorLog whereDeletedAt($value)
 */
class ErrorLog extends Model
{
    public $table = "error_log";
}
