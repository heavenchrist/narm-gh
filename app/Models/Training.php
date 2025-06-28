<?php

namespace App\Models;

use App\Enums\TrainingMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'registration_end_date',
        'start',
        'end',
        'training_mode',
        'status',
        'content',
        'user_id',
        'region_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'registration_end_date' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
        'status' => 'boolean',
        'user_id' => 'integer',
        'region_id' => 'integer',
        'training_mode' => TrainingMode::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
    public function trainingRegistrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }
    public function trainingRegistration(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function hasRegistered()
    {
        return $this->trainingRegistrations->isNotEmpty();
    }
}
