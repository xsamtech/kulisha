<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class SentReaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * ONE-TO-MANY
     * One reaction for several sent_reactions
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reaction(): BelongsTo
    {
        return $this->belongsTo(Reaction::class);
    }

    /**
     * ONE-TO-MANY
     * One reaction_reason for several sent_reactions
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reaction_reason(): BelongsTo
    {
        return $this->belongsTo(ReactionReason::class);
    }

    /**
     * ONE-TO-MANY
     * One user for several sent_reactions
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
