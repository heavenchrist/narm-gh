<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegionalNotification extends Model
{
    use HasFactory;


   //$table_name = (new GeneralNotification())->getTable();
    protected $table = 'general_notifications';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'expiry_date',
        'status',
        'region_id',
        'content',
        'region_only'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'expiry_date' => 'date',
        'status' => 'boolean',
        'region_id' => 'integer',
        'region_only' => 'boolean',
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->region_id = auth()->user()->region_id;
            $data->region_only = true;
        });
      static::addGlobalScope('region', function (Builder $builder) {
            $builder->where('region_id',auth()->user()->region_id);
        });   /* */
    }
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function generalNotificationRead(): HasMany
    {
        return $this->hasMany(GeneralNotificationRead::class,'general_notification_id','id');
    }
}