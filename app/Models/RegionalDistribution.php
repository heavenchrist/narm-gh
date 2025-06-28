<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegionalDistribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table= 'distributions';

    protected $fillable = [
        'distribution_item_id',
        'user_id',
        'office_id',
        'quantity',
        'status',
        'remarks',
        'distribution_list_id',
        'received_date',
        'distribution_count',
        'received_by',
        'is_received'
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
        'received_date' => 'datetime',
        'distribution_count' => 'integer',
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->user_id = auth()->user()->id;
        });
     static::addGlobalScope('users', function (Builder $builder) {
            $builder->where('office_id',  auth()->user()->office?->id);
        });     /**/
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'received_by','id');
    }
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }



    public function distributionItem(): BelongsTo
    {
        return $this->belongsTo(DistributionItem::class,'distribution_item_id','id');
    }

    public function distributionLists(): HasMany
    {
        return $this->hasMany(DistributionList::class,'distribution_id','id');
    }
}