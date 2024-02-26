<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasProfilePhoto, HasTeams, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['email_verified_at' => 'datetime'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['profile_photo_url'];

    /**
     * MANY-TO-MANY
     * Several roles for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * MANY-TO-MANY
     * Several fields for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(Field::class);
    }

    /**
     * MANY-TO-MANY
     * Several communities for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class)->withPivot('is_admin', 'created_at', 'updated_at', 'reaction_id');
    }

    /**
     * MANY-TO-MANY
     * Several events for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withPivot('is_speaker', 'created_at', 'updated_at', 'reaction_id');
    }

    /**
     * MANY-TO-MANY
     * Several surveychoices for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function surveychoices(): BelongsToMany
    {
        return $this->belongsToMany(Surveychoice::class);
    }

    /**
     * ONE-TO-MANY
     * One status for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * ONE-TO-MANY
     * One type for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * ONE-TO-MANY
     * One visibility for several users
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function visibility(): BelongsTo
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * MANY-TO-ONE
     * Several websites for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function websites(): HasMany
    {
        return $this->hasMany(Website::class);
    }

    /**
     * MANY-TO-ONE
     * Several subscriptions for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * MANY-TO-ONE
     * Several teams for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * MANY-TO-ONE
     * Several restrictions for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function restrictions(): HasMany
    {
        return $this->hasMany(Restriction::class);
    }

    /**
     * MANY-TO-ONE
     * Several owned_events for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owned_events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * MANY-TO-ONE
     * Several owned_communities for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owned_communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    /**
     * MANY-TO-ONE
     * Several carts for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * MANY-TO-ONE
     * Several payments for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * MANY-TO-ONE
     * Several files for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * MANY-TO-ONE
     * Several messages for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * MANY-TO-ONE
     * Several posts for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * MANY-TO-ONE
     * Several sent_reactions for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sent_reactions(): HasMany
    {
        return $this->hasMany(SentReaction::class);
    }

    /**
     * MANY-TO-ONE
     * Several histories_from for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories_from(): HasMany
    {
        return $this->hasMany(History::class, 'from_user_id');
    }

    /**
     * MANY-TO-ONE
     * Several histories_to for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function histories_to(): HasMany
    {
        return $this->hasMany(History::class, 'to_user_id');
    }

    /**
     * MANY-TO-ONE
     * Several notifications_from for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications_from(): HasMany
    {
        return $this->hasMany(Notification::class, 'from_user_id');
    }

    /**
     * MANY-TO-ONE
     * Several notifications_to for a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications_to(): HasMany
    {
        return $this->hasMany(Notification::class, 'to_user_id');
    }
}
