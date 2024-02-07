<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Group extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Translatable attributes.
     *
     * @var array<int, string>
     */
    protected $translatable = ['group_name', 'group_description'];

    /**
     * MANY-TO-ONE
     * Several statuses for a group
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    /**
     * MANY-TO-ONE
     * Several types for a group
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }

    /**
     * MANY-TO-ONE
     * Several reactions for a group
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }
}
