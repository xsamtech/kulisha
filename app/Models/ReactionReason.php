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
class ReactionReason extends Model
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
    protected $translatable = ['reason_content'];

    /**
     * MANY-TO-ONE
     * Several sent_reactions for a reaction_reason
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sent_reactions(): HasMany
    {
        return $this->hasMany(SentReaction::class);
    }
}
