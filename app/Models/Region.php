<?php

namespace App\Models;

use App\Casts\UpperCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
    ];

    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){
            if(Auth::check()){
                $data->user_id = auth()->user()->id;
            }

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
        'name'=>UpperCase::class,
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    public function office(): HasOne
    {
        return $this->hasOne(Office::class);
    }
}
