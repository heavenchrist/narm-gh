<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'distribution_item_id',
        'user_id',
        'office_id',
        'quantity',
        'status',
        'remarks',
        'distribution_list_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'distribution_item_id' => 'integer',
        'user_id' => 'integer',
        'office_id' => 'integer',
        'status' => 'boolean',
        'distribution_list_id' => 'integer',
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->user_id = auth()->user()->id;
        });
      /*static::addGlobalScope('users', function (Builder $builder) {
            $builder->whereIsAdmin(true);
        });    */
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }


    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'received_by','id');
    }

    public function distributionItem(): BelongsTo
    {
        return $this->belongsTo(DistributionItem::class);
    }

    public function distributionLists(): HasMany
    {
        return $this->hasMany(DistributionList::class);
    }
}
