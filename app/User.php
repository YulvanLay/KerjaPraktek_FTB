<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $rememberTokenName = false;
    public $timestamps = false;
    protected $fillable = ['username', 'password', 'menu_pelanggan', 'menu_pejabat', 'menu_laboran', 'menu_laboratorium', 'menu_periode', 'hak_akses_delete', 'aktif'];
    protected $hidden = ['password'];

    public function laboran()
    {
        return $this->hasOne('App\Laboran', 'user_id');
    }
    public function admin()
    {
        return $this->hasOne('App\Admin', 'user_id');
    }

    public function pelanggan()
    {
        return $this->hasOne('App\Pelanggan', 'users_id');
    }
    public function koordinator()
    {
        return $this->hasOne('App\Koordinator', 'kode_pejabat');
    }
    public function kalab()
    {
        return $this->hasOne('App\Laboratorium', 'kode_pejabat');
    }
}
