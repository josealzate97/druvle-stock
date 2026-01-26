<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable {
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    // Define las columnas personalizadas para las marcas de tiempo
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    const DELETED_AT = 'delete_date';

    // Roles
    const ROLE_ROOT = 1;
    const ROLE_ADMIN = 2;
    const ROLE_SALES = 3;

    const ACTIVE = 1;
    const INACTIVE = 2;

    protected $fillable = [
        'id',
        'name',
        'lastname',
        'email',
        'username',
        'password',
        'rol',
        'status',
        'creation_date',
        'update_date',
        'delete_date',
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

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
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName() {
        return 'id';
    }
}
