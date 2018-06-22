<?php

namespace App\Eloquents;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relation: has many social accounts.
    **/
    public function accounts() : HasMany
    {
        return $this->hasMany('App\Eloquents\LinkedSocialAccount');
    }

    /**
     * Exists value of email column
    **/
    public function existsEmail() : Bool
    {
        return isset($this->email);
    }

    /**
     * Exists value of password column
    **/
    public function existsPassword() : Bool
    {
        return isset($this->password);
    }

    /**
     * Exists value of email column and password column
    **/
    public function existsEmailAndPassword() : Bool
    {
        return $this->existsEmail() && $this->existsPassword();
    }
}
