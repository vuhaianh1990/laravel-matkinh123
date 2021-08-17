<?php

namespace Matkinh123\Models;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    protected $fillable = [
        'name',
        'type',
        'money',
        'prepay',
        'status',
        'created_at',
        'updated_at',
        'monthlyfee'
    ];
}
