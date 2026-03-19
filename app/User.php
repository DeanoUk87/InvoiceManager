<?php
namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','username','avatar','provider', 'provider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            if ( preg_match('/^\$2y\$[0-9]*\$.{50,}$/', $password) ) {
                $this->attributes['password'] = $password;
            }
            else {
                $this->attributes['password'] = bcrypt($password);
            }
            return true;
        }
        return false;
    }
}
