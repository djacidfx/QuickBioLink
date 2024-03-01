<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public function scopeMonthly($query)
    {
        $query->where('interval', 1);
    }

    public function scopeYearly($query)
    {
        $query->where('interval', 2);
    }

    public function scopeFree($query)
    {
        $query->where('is_free', 1);
    }

    public function scopeNotFree($query)
    {
        $query->where('is_free', 0);
    }

    public function isFree()
    {
        return $this->is_free;
    }

    public function isFeatured()
    {
        return $this->is_featured;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'position',
        'short_description',
        'translations',
        'interval',
        'price',
        'settings',
        'advertisements',
        'custom_features',
        'is_free',
        'is_featured',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'translations' => 'object',
        'settings' => 'object',
        'custom_features' => 'object',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function transactions()
    {
        return $this->hasMany(Subscription::class);
    }
}
