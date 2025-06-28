<?php

namespace App\Models;

use App\Traits\TokenGenerator;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrator extends User
{
    use  HasFactory;
    use SoftDeletes;
    use HasRoles;
    protected $table = 'users';

    public function getMorphClass()
    {
        return User::class;
    }

    protected $fillable = [
        'name','email','password',
        'gender','registration_number',
        'pin_number','image_url','next_of_kin',
        'next_of_kin_contact','token',
        'staff_id','telephone','place_of_work',
        'rank_id','region_id','district',
        'marital_status','date_of_birth',
        'place_of_birth','residential_address'
    ];
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->image_url ? Storage::url($this->image_url) : null;
    }


    public static function boot(){

        parent::boot();

       // LastActivityTrait::save();

      static::creating(function($data){

            $data->is_admin = true;
            $data->token = TokenGenerator::create();
        });
      static::addGlobalScope('users', function (Builder $builder) {
            $builder->whereIsAdmin(true);
        });   /* */
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'rank_id' =>'integer',
        'region_id' =>'integer',
    ];

    public function rank():BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
    public function ranks():HasMany
    {
        return $this->hasMany(Rank::class);
    }

    public function regions():HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function region():BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

}