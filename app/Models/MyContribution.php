<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Casts\SpecialDateCast;
use App\Casts\UpperCase;
use App\Models\Contribution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyContribution extends Model
{
    use HasFactory;

    //$table  = Contribution::getTableName();
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Assign Product's table name to this model
        $this->table = (new Contribution)->getTable();
    }

    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::addGlobalScope('contribution', function (Builder $builder) {
            $builder->where('staff_id',auth()->user()->staff_id);
        });    /**/
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'staff_id' => UpperCase::class,
        'amount'=>MoneyCast::class,

    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class,'staff_id','staff_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
