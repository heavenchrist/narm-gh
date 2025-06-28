<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Casts\UpperCase;
use App\Casts\SpecialDateCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'staff_id',
        'place_of_work',
        'district',
        'region',
        'period',
        'amount',
        'user_id',
        'is_updated'
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->user_id = auth()->user()->id;
        });
     /* static::addGlobalScope('users', function (Builder $builder) {
            $builder->whereIsAdmin(false);
        });    */
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
        'period' => SpecialDateCast::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class,'member_id','id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
