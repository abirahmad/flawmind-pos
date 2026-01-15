<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type',
        'surname',
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'language',
        'contact_no',
        'address',
        'business_id',
        'allow_login',
        'status',
        'is_cmmsn_agnt',
        'cmmsn_percent',
        'selected_contacts',
        'is_enable_service_staff_pin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'allow_login' => 'boolean',
            'is_cmmsn_agnt' => 'boolean',
            'selected_contacts' => 'boolean',
            'is_enable_service_staff_pin' => 'boolean',
            'dob' => 'date',
        ];
    }
}
