<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyRenewal extends Model
{
    use HasFactory;

     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Assign Product's table name to this model
        $this->table = (new Renewal)->getTable();
    }

    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();


     static::addGlobalScope('renewals', function (Builder $builder) {
            $builder->where('staff_id',auth()->user()->staff_id);
        });    /* */
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'boolean',
       // 'name'=>UpperCase::class,
    ];

}
