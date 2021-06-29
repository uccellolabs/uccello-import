<?php

namespace Uccello\Import\Models;

use App\Models\User;
use Uccello\Core\Database\Eloquent\Model;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;

class Import extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'imports';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'object',
        'data' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'module_id',
        'user_id',
        'config',
        'data',
    ];

    protected function initTablePrefix()
    {
        $this->tablePrefix = env('UCCELLO_TABLE_PREFIX', 'uccello_');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
