<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Renewal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pin_ain',
        'registration_number',
        'staff_id',
        'renewal_date',
        'expiry_date',
        'period',
        'name',
        'telephone'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'renewal_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class,'staff_id','staff_id');
    }
}
