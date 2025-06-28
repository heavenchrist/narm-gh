<?php

namespace App\Models;

use App\Casts\TitleCase;
use App\Casts\UpperCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'shortname',
        'status',
        'user_id',
    ];
    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->user_id = auth()->user()->id;
        });
     static::addGlobalScope('status', function (Builder $builder) {
            $builder->whereStatus(true);
        });    /* */
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'boolean',
        'user_id' => 'integer',
        'name'=>TitleCase::class,
        'shortname'=>UpperCase::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}