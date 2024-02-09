<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Message extends Model
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
     * One type for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * ONE-TO-MANY
     * One status for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * ONE-TO-MANY
     * One user for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ONE-TO-MANY
     * One addressee_user for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addressee_user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ONE-TO-MANY
     * One addressee_community for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addressee_community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * ONE-TO-MANY
     * One addressee_team for several messages
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function addressee_team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * MANY-TO-ONE
     * Several files for a message
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
