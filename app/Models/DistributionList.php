<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionList extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'distribution_id',
        'is_received',
        'user_id',
        'region_id',
        'distribution_item_id',
        'quantity',
        'region_id',
        'received_date'
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->region_id = auth()->user()->region_id;
            $data->office_id = auth()->user()->office?->id;
            $data->user_id = auth()->user()->id;
            //$data->token = TokenGenerator::create();
        });
     /*  static::addGlobalScope('users', function (Builder $builder) {
            $builder->whereIsAdmin(true);
        });   */
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'member_id' => 'integer',
        'distribution_id' => 'integer',
        'is_received' => 'boolean',
        'user_id' => 'integer',
        'region_id' => 'integer',
        'distribution_item_id' => 'integer',
        'distribution_list_id' => 'integer',
        'received_date'=> 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class,'distribution_id','id');
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }


    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class,'member_id','id');
    }

    public function distributionItem(): BelongsTo
    {
        return $this->belongsTo(DistributionItem::class);
    }
}