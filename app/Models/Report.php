<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'report_url',
        'user_id',
        'office_id',
        'region_id',
        'received_by',
        'is_received',
        'received_date',
        'is_submitted',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'office_id' => 'integer',
        'region_id' => 'integer',
        'received_by' => 'integer',
        'is_received' => 'boolean',
        'is_submitted' => 'boolean',
        'received_date' => 'datetime',
    ];

    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->region_id = auth()->user()->region_id;
            $data->office_id = auth()->user()->office_id;
            $data->user_id = auth()->user()->id;
            //$data->token = TokenGenerator::create();
        });
       /*  static::addGlobalScope('users', function (Builder $builder) {
            $builder->where('office_id',  auth()->user()?->office?->id);
        }); */
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'received_by','id');
    }
}