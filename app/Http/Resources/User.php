<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'surname' => $this->surname,
            'username' => $this->username,
            'about_me' => $this->about_me,
            'gender' => $this->gender,
            'birth_date' => !empty($this->birth_date) ? (str_starts_with(app()->getLocale(), 'fr') ? \Carbon\Carbon::createFromFormat('Y-m-d', $this->birth_date)->format('d/m/Y') : \Carbon\Carbon::createFromFormat('Y-m-d', $this->birth_date)->format('m/d/Y')) : null,
            'country' => $this->country,
            'city' => $this->city,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'p_o_box' => $this->p_o_box,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'remember_token' => $this->remember_token,
            'current_team_id' => $this->current_team_id,
            'email_verified_at' => $this->email_verified_at,
            'phone_verified_at' => $this->phone_verified_at,
            'prefered_theme' => $this->prefered_theme,
            'prefered_language' => $this->prefered_language,
            'profile_photo_path' => !empty($this->profile_photo_path) ? getWebURL() . '/storage/' . $this->profile_photo_path : getWebURL() . '/img/avatar.png',
            'cover_photo_path' => !empty($this->cover_photo_path) ? getWebURL() . '/storage/' . $this->cover_photo_path : null,
            'cover_coordinates' => $this->cover_coordinates,
            'two_factor_secret' => $this->two_factor_secret,
            'two_factor_recovery_codes' => $this->two_factor_recovery_codes,
            'two_factor_confirmed_at' => $this->two_factor_confirmed_at,
            'notify_connection_invites' => $this->notify_connection_invites,
            'notify_new_posts' => $this->notify_new_posts,
            'notify_post_answers' => $this->notify_post_answers,
            'notify_reactions' => $this->notify_reactions,
            'notify_post_shared' => $this->notify_post_shared,
            'notify_post_on_message' => $this->notify_post_on_message,
            'email_frequency' => $this->email_frequency,
            'allow_search_engine' => $this->allow_search_engine,
            'allow_search_by_email' => $this->allow_search_by_email,
            'allow_sponsored_messages' => $this->allow_sponsored_messages,
            'tips_at_every_connection' => $this->tips_at_every_connection,
            'api_token' => $this->api_token,
            'status' => Status::make($this->status),
            'type' => Type::make($this->type),
            'visibility' => Visibility::make($this->visibility),
            'roles' => Role::collection($this->roles),
            'fields' => Field::collection($this->fields),
            'websites' => Website::collection($this->websites),
            'files' => File::collection($this->files),
            'carts' => Cart::collection($this->carts)->sortByDesc('created_at')->toArray(),
            'payments' => Payment::collection($this->payments)->sortByDesc('created_at')->toArray(),
            'histories' => History::collection($this->histories_from)->sortByDesc('created_at')->toArray(),
            'notifications' => Notification::collection($this->notifications_to)->sortByDesc('created_at')->toArray(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
