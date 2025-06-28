<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralNotificationRead extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id',
        'general_notification_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'general_notification_id' => 'integer',
        'member_id' => 'integer',
    ];

    public function generalNotification(): BelongsTo
    {
        return $this->belongsTo(GeneralNotification::class);
    }

    public function regionalNotification(): BelongsTo
    {
        return $this->belongsTo(RegionalNotification::class,'general_notification_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'member_id','id');
    }
}