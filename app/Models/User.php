<?php

namespace App\Models;

use App\Notifications\UserResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
        'user_type',
        'name',
        'firstname',
        'lastname',
        'username',
        'email',
        'address',
        'avatar',
        'password',
        'google2fa_status',
        'google2fa_secret',
        'oauth_id',
        'oauth_provider',
        'is_viewed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'address' => 'object',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'username' => [
                'source' => 'name',
            ],
        ];
    }

    public function isSubscribed()
    {
        return $this->subscription;
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {
        return decrypt($value);
    }

    /**
     * Send Password Reset Notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    /**
     * Send Email Verification Notification.
     */
    public function sendEmailVerificationNotification()
    {
        if (settings('enable_email_verification')) {
            $this->notify(new VerifyEmailNotification());
        }
    }

    /**
     * Relationships
     */
    public function logs()
    {
        return $this->hasMany(UserLog::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
