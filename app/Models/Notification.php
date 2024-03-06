<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Notification extends Model
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
     * One type for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * ONE-TO-MANY
     * One status for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * ONE-TO-MANY
     * One from_user for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * ONE-TO-MANY
     * One to_user for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * ONE-TO-MANY
     * One post for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * ONE-TO-MANY
     * One event for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * ONE-TO-MANY
     * One community for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * ONE-TO-MANY
     * One message for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * ONE-TO-MANY
     * One team for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * ONE-TO-MANY
     * One reaction for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reaction(): BelongsTo
    {
        return $this->belongsTo(Reaction::class);
    }

    /**
     * ONE-TO-MANY
     * One cart for several notifications
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}
