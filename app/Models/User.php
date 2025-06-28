<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use App\Enums\Gender;
use App\Casts\UpperCase;
use App\Enums\MaritalStatus;
use App\Traits\TokenGenerator;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Relations\BelongsToThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Yebor974\Filament\RenewPassword\Traits\RenewPassword;
use Yebor974\Filament\RenewPassword\Contracts\RenewPasswordContract;

class User extends Authenticatable implements FilamentUser,ShouldQueue, HasAvatar, RenewPasswordContract
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    use RenewPassword;
    use HasRoles;
    use \Znck\Eloquent\Traits\BelongsToThrough;
    protected $fillable = [
        'name','email','password',
        'gender','registration_number',
        'pin_number','image_url','next_of_kin',
        'next_of_kin_contact','token',
        'staff_id','telephone','place_of_work',
        'rank_id','region_id','district',
        'marital_status','date_of_birth',
        'place_of_birth','residential_address','office_id'
    ];
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->image_url ? Storage::url($this->image_url) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return auth()->user()->is_admin === true && auth()->user()->region_id == null;
        }

         if ($panel->getId() === 'portal') {
            return auth()->user()->is_admin ===false;
        }
        if ($panel->getId() === 'region') {
            return auth()->user()->is_admin && auth()->user()->region_id != null;
           // return auth()->user()->region_id <> null;
        } /**//**/

       return false;
    }

    public static function boot(){

        parent::boot();


       // LastActivityTrait::save();

      static::creating(function($data){

            $data->is_admin = false;
            $data->token = TokenGenerator::create();
        });
     /* static::addGlobalScope('users', function (Builder $builder) {
            $builder->whereIsAdmin(false);
        });    */
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
        'password' => 'hashed',
        'gender'=>Gender::class,
        'registration_number'=>UpperCase::class,
        'pin_number'=>UpperCase::class,
        'staff_id'=>UpperCase::class,
        'marital_status'=>MaritalStatus::class,
        'rank_id' =>'integer',
        'region_id' =>'integer',
        'is_admin' =>'boolean',
    ];

    public function rank():BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
    public function ranks():HasMany
    {
        return $this->hasMany(Rank::class);
    }



    public function region():BelongsTo
    {
        return $this->belongsTo(Region::class,'region_id','id');
    }

      /**/

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class,'office_id','id');
    }

    public function contributions():HasMany
    {
        return $this->hasMany(Contribution::class,'staff_id','staff_id');
    }
    public function memberContributions():HasMany
    {
        return $this->hasMany(Contribution::class,'member_id','id');
    }
}