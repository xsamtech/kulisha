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
class Visibility extends Model
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
    protected $translatable = ['visibility_name', 'visibility_description'];

    /**
     * MANY-TO-ONE
     * Several users for a visibility
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * MANY-TO-ONE
     * Several restrictions for a visibility
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }
}
