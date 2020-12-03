<?php

namespace Uccello\Import\Models;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id',
        'domain_id',
        'user_id',
        'name',
        'config',
    ];
}
