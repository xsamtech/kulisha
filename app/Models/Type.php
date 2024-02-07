<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Type extends Model
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
    protected $translatable = ['type_name', 'type_description'];

    /**
     * ONE-TO-MANY
     * One group for several types
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * MANY-TO-ONE
     * Several users for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * MANY-TO-ONE
     * Several posts for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * MANY-TO-ONE
     * Several messages for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * MANY-TO-ONE
     * Several files for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * MANY-TO-ONE
     * Several websites for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    /**
     * MANY-TO-ONE
     * Several carts for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * MANY-TO-ONE
     * Several payments for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * MANY-TO-ONE
     * Several histories for a type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class);
    }
}
