<?php

namespace App\Http\Controllers\API;

use stdClass;
use App\Models\Event;
use App\Models\File;
use App\Models\Group;
use App\Models\History;
use App\Models\Notification;
use App\Models\PasswordResetToken;
use App\Models\PersonalAccessToken;
use App\Models\Reaction;
use App\Models\Status;
use App\Models\Subscription;
use App\Models\Type;
use App\Models\User;
use App\Models\Visibility;
use Nette\Utils\Random;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\User as ResourcesUser;
use App\Http\Resources\PasswordResetToken as ResourcesPasswordReset;
use App\Http\Resources\Community as ResourcesCommunity;
use App\Http\Resources\Event as ResourcesEvent;
use App\Http\Resources\Subscription as ResourcesSubscription;
use App\Models\Community;
use App\Models\Session;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderByDesc('created_at')->paginate(10);
        $count_users = User::count();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'), $users->lastPage(), $count_users);
    }

    /**
     * Store a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $member_type_group = Group::where('group_name->fr', 'Type de membre')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $to_connect_visibility_group = Group::where('group_name->fr', 'Visibilité pour se connecter')->first();
        // Statuses
        $activated_status = !empty($member_status_group) ? Status::where([['status_name->fr', 'Activé'], ['group_id', $member_status_group->id]])->first() : Status::where('status_name->fr', 'Activé')->first();
        $unread_status = !empty($notification_status_group) ? Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first() : Status::where('status_name->fr', 'Non lue')->first();
        // Types
        $ordinary_member_type = !empty($member_type_group) ? Type::where([['type_name->fr', 'Membre ordinaire'], ['group_id', $member_type_group->id]])->first() : Type::where('type_name->fr', 'Membre ordinaire')->first();
        $activities_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des activités')->first();
        $new_account_type = !empty($notification_type_group) ? Type::where([['type_name->fr', 'Nouveau compte'], ['group_id', $notification_type_group->id]])->first() : Type::where('type_name->fr', 'Nouveau compte')->first();
        // Visibility
        $everybody_on_kulisha_visibility = !empty($to_connect_visibility_group) ? Visibility::where([['visibility_name->fr', 'Tout le monde sur Kulisha (recommandé)'], ['group_id', $to_connect_visibility_group->id]])->first() : Visibility::where('visibility_name->fr', 'Tout le monde sur Kulisha (recommandé)')->first();
        // Get inputs
        $inputs = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
            'username' => $request->username,
            'about_me' => $request->about_me,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'country' => $request->country,
            'city' => $request->city,
            'address_1' => $request->address_1,
            'address_2' => $request->address_2,
            'p_o_box' => $request->p_o_box,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => empty($request->password) ? null : Hash::make($request->password),
            'prefered_theme' => $request->prefered_theme,
            'prefered_language' => $request->prefered_language,
            'status_id' => is_null($activated_status) ? (!empty($request->status_id) ? $request->status_id : null) : $activated_status->id,
            'type_id' => is_null($ordinary_member_type) ? (!empty($request->type_id) ? $request->type_id : null) : $ordinary_member_type->id,
            'visibility_id' => !empty($request->visibility_id) ? $request->visibility_id : (!is_null($everybody_on_kulisha_visibility) ? $everybody_on_kulisha_visibility->id : null)
        ];
        $users = User::all();
        $password_resets = PasswordResetToken::all();

        if (trim($inputs['email']) == null AND trim($inputs['phone']) == null) {
            return $this->handleError(__('validation.custom.email_or_phone.required'));
        }

        if ($inputs['email'] != null) {
            // Check if user email already exists
            foreach ($users as $another_user):
                if ($another_user->email == $inputs['email']) {
                    return $this->handleError($inputs['email'], __('validation.custom.email.exists'), 400);
                }
            endforeach;

            // If email exists in "password_reset" table, delete it
            if ($password_resets != null) {
                foreach ($password_resets as $password_reset):
                    if ($password_reset->email == $inputs['email']) {
                        $password_reset->delete();
                    }
                endforeach;
            }
        }

        if ($inputs['phone'] != null) {
            // Check if user phone already exists
            foreach ($users as $another_user):
                if ($another_user->phone == $inputs['phone']) {
                    return $this->handleError($inputs['phone'], __('validation.custom.phone.exists'), 400);
                }
            endforeach;

            // If phone exists in "password_reset" table, delete it
            if ($password_resets != null) {
                foreach ($password_resets as $password_reset):
                    if ($password_reset->phone == $inputs['phone']) {
                        $password_reset->delete();
                    }
                endforeach;
            }
        }

        if ($inputs['username'] != null) {
            // Check if username already exists
            foreach ($users as $another_user):
                if ($another_user->username == $inputs['username']) {
                    return $this->handleError($inputs['username'], __('validation.custom.username.exists'), 400);
                }
            endforeach;

            // Check correct username
            if (preg_match('#^[\w]+$#', $inputs['username']) == 0) {
                return $this->handleError($inputs['username'], __('miscellaneous.username.error'), 400);
            }
        }

        if ($inputs['password'] != null) {
            if ($request->confirm_password != $request->password OR $request->confirm_password == null) {
                return $this->handleError($request->confirm_password, __('notifications.confirm_password_error'), 400);
            }

            if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['password']) == 0) {
                return $this->handleError($inputs['password'], __('miscellaneous.password.error'), 400);
            }

            $random_string = (string) random_int(1000000, 9999999);

            if ($inputs['email'] != null AND $inputs['phone'] != null) {
                $password_reset = PasswordResetToken::create([
                    'phone' => $inputs['phone'],
                    'email' => $inputs['email'],
                    'token' => $random_string,
                    'former_password' => $request->password
                ]);

            } else {
                if ($inputs['email'] != null) {
                    PasswordResetToken::create([
                        'email' => $inputs['email'],
                        'token' => $random_string,
                        'former_password' => $request->password
                    ]);
                }

                if ($inputs['phone'] != null) {
                    $password_reset = PasswordResetToken::create([
                        'phone' => $inputs['phone'],
                        'token' => $random_string,
                        'former_password' => $request->password
                    ]);
                }
            }
        }

        if ($inputs['password'] == null) {
            $random_string = (string) random_int(1000000, 9999999);

            if ($inputs['email'] != null AND $inputs['phone'] != null) {
                $password_reset = PasswordResetToken::create([
                    'phone' => $inputs['phone'],
                    'email' => $inputs['email'],
                    'token' => $random_string,
                    'former_password' => Random::generate(10, 'a-zA-Z'),
                ]);

                $inputs['password'] = Hash::make($password_reset->former_password);

            } else {
                if ($inputs['email'] != null) {
                    $password_reset = PasswordResetToken::create([
                        'email' => $inputs['email'],
                        'token' => $random_string,
                        'former_password' => Random::generate(10, 'a-zA-Z'),
                    ]);

                    $inputs['password'] = Hash::make($password_reset->former_password);
                }

                if ($inputs['phone'] != null) {
                    $password_reset = PasswordResetToken::create([
                        'phone' => $inputs['phone'],
                        'token' => $random_string,
                        'former_password' => Random::generate(10, 'a-zA-Z'),
                    ]);

                    $inputs['password'] = Hash::make($password_reset->former_password);
                }
            }
        }

        $user = User::create($inputs);

        if ($request->role_id != null) {
            $user->roles()->attach([$request->role_id]);
        }

        if ($request->image_64 != null) {
            // $extension = explode('/', explode(':', substr($request->image_64, 0, strpos($request->image_64, ';')))[1])[1];
            $replace = substr($request->image_64, 0, strpos($request->image_64, ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image = str_replace($replace, '', $request->image_64);
            $image = str_replace(' ', '+', $image);
            // Create image URL
            $image_url = 'images/users/' . $user->id . '/avatar/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

            $user->update([
                'profile_photo_path' => '/storage/' . $image_url,
                'updated_at' => now()
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'api_token' => $token,
            'updated_at' => now()
        ]);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        $notification = Notification::create([
            'type_id' => is_null($new_account_type) ? null : $new_account_type->id,
            'status_id' => is_null($unread_status) ? null : $unread_status->id,
            'to_user_id' => $user->id
        ]);

        History::create([
            'type_id' => $activities_history_type->id,
            'status_id' => is_null($unread_status) ? null : $unread_status->id,
            'to_user_id' => $user->id,
            'for_notification_id' => $notification->id
        ]);

        $object = new stdClass();
        $object->password_reset = new ResourcesPasswordReset($password_reset);
        $object->user = new ResourcesUser($user);

        return $this->handleResponse($object, __('notifications.create_user_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $consultation_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des consultations')->first();
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($request->hasHeader('X-user-id') and $request->hasHeader('X-ip-address') or $request->hasHeader('X-user-id') and !$request->hasHeader('X-ip-address')) {
            $session = Session::where('user_id', $request->header('X-user-id'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->hasHeader('X-ip-address') ? $request->header('X-ip-address') : null,
                    'user_agent' => $request->header('X-user-agent'),
                    'user_id' => $request->header('X-user-id')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $new_session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $session->id
                ]);
            }
        }

        if ($request->hasHeader('X-ip-address')) {
            $session = Session::where('ip_address', $request->header('X-ip-address'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->header('X-ip-address'),
                    'user_agent' => $request->header('X-user-agent')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'to_user_id' => $user->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => is_null($session->user_id) ? null : $session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $session->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.find_user_success'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Get inputs
        $inputs = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
            'username' => $request->username,
            'about_me' => $request->about_me,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'country' => $request->country,
            'city' => $request->city,
            'address_1' => $request->address_1,
            'address_2' => $request->address_2,
            'p_o_box' => $request->p_o_box,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => empty($request->password) ? null : Hash::make($request->password),
            'confirm_password' => $request->confirm_password,
            'current_team_id' => $request->current_team_id,
            'email_verified_at' => $request->email_verified_at,
            'phone_verified_at' => $request->phone_verified_at,
            'prefered_theme' => $request->prefered_theme,
            'prefered_language' => $request->prefered_language,
            'two_factor_secret' => $request->two_factor_secret,
            'two_factor_recovery_codes' => $request->two_factor_recovery_codes,
            'two_factor_confirmed_at' => $request->two_factor_confirmed_at,
            'notify_connection_invites' => $request->notify_connection_invites,
            'notify_new_posts' => $request->notify_new_posts,
            'notify_post_answers' => $request->notify_post_answers,
            'notify_reactions' => $request->notify_reactions,
            'notify_post_shared' => $request->notify_post_shared,
            'notify_post_on_message' => $request->notify_post_on_message,
            'email_frequency' => $request->email_frequency,
            'allow_search_engine' => $request->allow_search_engine,
            'allow_search_by_email' => $request->allow_search_by_email,
            'allow_sponsored_messages' => $request->allow_sponsored_messages,
            'allow_messages_not_connected' => $request->allow_messages_not_connected,
            'tips_at_every_login' => $request->tips_at_every_login,
            'is_online' => $request->is_online,
            'status_id' => $request->status_id,
            'type_id' => $request->type_id,
            'visibility_id' => $request->visibility_id
        ];
        $users = User::all();
        $current_user = User::find($inputs['id']);

        if ($inputs['firstname'] != null) {
            $user->update([
                'firstname' => $inputs['firstname'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['lastname'] != null) {
            $user->update([
                'lastname' => $inputs['lastname'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['surname'] != null) {
            $user->update([
                'surname' => $inputs['surname'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['username'] != null) {
            // Check if username already exists
            foreach ($users as $another_user):
                if ($current_user->username != $inputs['username']) {
                    if ($another_user->username == $inputs['username']) {
                        return $this->handleError($inputs['username'], __('validation.custom.username.exists'), 400);
                    }
                }
            endforeach;

            // Check correct username
            if (preg_match('#^[\w]+$#i', $inputs['username']) == 0) {
                return $this->handleError($inputs['username'], __('miscellaneous.username.error'), 400);
            }

            $user->update([
                'username' => $request->username,
                'updated_at' => now(),
            ]);
        }

        if ($inputs['about_me'] != null) {
            $user->update([
                'about_me' => $inputs['about_me'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['gender'] != null) {
            $user->update([
                'gender' => $inputs['gender'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['birth_date'] != null) {
            $user->update([
                'birth_date' => $inputs['birth_date'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['country'] != null) {
            $user->update([
                'country' => $inputs['country'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['city'] != null) {
            $user->update([
                'city' => $inputs['city'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['address_1'] != null) {
            $user->update([
                'address_1' => $inputs['address_1'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['address_2'] != null) {
            $user->update([
                'address_2' => $inputs['address_2'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['p_o_box'] != null) {
            $user->update([
                'p_o_box' => $inputs['p_o_box'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email'] != null) {
            // Check if email already exists
            foreach ($users as $another_user):
                if ($current_user->email != $inputs['email']) {
                    if ($another_user->email == $inputs['email']) {
                        return $this->handleError($inputs['email'], __('validation.custom.email.exists'), 400);
                    }
                }
            endforeach;

            $user->update([
                'email' => $inputs['email'],
                'updated_at' => now(),
            ]);

            $password_reset_by_email = PasswordResetToken::where('email', $inputs['email'])->first();

            if ($password_reset_by_email == null) {
                $password_reset_by_phone = PasswordResetToken::where('phone', $current_user->phone)->first();

                if ($password_reset_by_phone != null) {
                    $password_reset_by_phone->update([
                        'email' => $inputs['email'],
                        'updated_at' => now(),
                    ]);

                } else {
                    PasswordResetToken::create([
                        'email' => $inputs['email'],
                    ]);
                }
            }
        }

        if ($inputs['phone'] != null) {
            // Check if phone already exists
            foreach ($users as $another_user):
                if ($current_user->phone != $inputs['phone']) {
                    if ($another_user->phone == $inputs['phone']) {
                        return $this->handleError($inputs['phone'], __('validation.custom.phone.exists'), 400);
                    }
                }
            endforeach;

            $user->update([
                'phone' => $inputs['phone'],
                'updated_at' => now(),
            ]);

            $password_reset_by_phone = PasswordResetToken::where('phone', $inputs['phone'])->first();

            if ($password_reset_by_phone == null) {
                $password_reset_by_email = PasswordResetToken::where('email', $inputs['email'])->first();

                if ($password_reset_by_email != null) {
                    $password_reset_by_email->update([
                        'phone' => $inputs['phone'],
                        'updated_at' => now(),
                    ]);

                } else {
                    PasswordResetToken::create([
                        'phone' => $inputs['phone'],
                    ]);
                }
            }
        }

        if ($inputs['password'] != null) {
            if ($inputs['confirm_password'] != $inputs['password'] OR $inputs['confirm_password'] == null) {
                return $this->handleError($inputs['confirm_password'], __('notifications.confirm_password_error'), 400);
            }

            if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['password']) == 0) {
                return $this->handleError($inputs['password'], __('miscellaneous.password.error'), 400);
            }

            $password_reset_by_email = PasswordResetToken::where('email', $inputs['email'])->first();
            $password_reset_by_phone = PasswordResetToken::where('phone', $inputs['phone'])->first();
            $random_string = (string) random_int(1000000, 9999999);

            // If password_reset doesn't exist, create it.
            if ($password_reset_by_email == null AND $password_reset_by_phone == null) {
                if ($inputs['email'] != null AND $inputs['phone'] != null) {
                    PasswordResetToken::create([
                        'email' => $inputs['email'],
                        'phone' => $inputs['phone'],
                        'token' => $random_string,
                        'former_password' => $inputs['password'],
                    ]);

                } else {
                    if ($inputs['email'] != null) {
                        PasswordResetToken::create([
                            'email' => $inputs['email'],
                            'token' => $random_string,
                            'former_password' => $inputs['password']
                        ]);
                    }

                    if ($inputs['phone'] != null) {
                        PasswordResetToken::create([
                            'phone' => $inputs['phone'],
                            'token' => $random_string,
                            'former_password' => $inputs['password']
                        ]);
                    }
                }

            // Otherwise, update it.
            } else {
                if ($password_reset_by_email != null) {
                    // Update password reset
                    $password_reset_by_email->update([
                        'token' => $random_string,
                        'former_password' => $inputs['password'],
                        'updated_at' => now(),
                    ]);
                }

                if ($password_reset_by_phone != null) {
                    // Update password reset
                    $password_reset_by_phone->update([
                        'token' => $random_string,
                        'former_password' => $inputs['password'],
                        'updated_at' => now(),
                    ]);
                }
            }

            $inputs['password'] = Hash::make($inputs['password']);

            $user->update([
                'password' => $inputs['password'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['current_team_id'] != null) {
            $user->update([
                'current_team_id' => $inputs['current_team_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email_verified_at'] != null) {
            $user->update([
                'email_verified_at' => $inputs['email_verified_at'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['phone_verified_at'] != null) {
            $user->update([
                'phone_verified_at' => $inputs['phone_verified_at'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['prefered_theme'] != null) {
            $user->update([
                'prefered_theme' => $inputs['prefered_theme'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['prefered_language'] != null) {
            $user->update([
                'prefered_language' => $inputs['prefered_language'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['two_factor_secret'] != null) {
            $user->update([
                'two_factor_secret' => $inputs['two_factor_secret'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['two_factor_recovery_codes'] != null) {
            $user->update([
                'two_factor_recovery_codes' => $inputs['two_factor_recovery_codes'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['two_factor_confirmed_at'] != null) {
            $user->update([
                'two_factor_confirmed_at' => $inputs['two_factor_confirmed_at'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_connection_invites'] != null) {
            $user->update([
                'notify_connection_invites' => $inputs['notify_connection_invites'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_new_posts'] != null) {
            $user->update([
                'notify_new_posts' => $inputs['notify_new_posts'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_post_answers'] != null) {
            $user->update([
                'notify_post_answers' => $inputs['notify_post_answers'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_reactions'] != null) {
            $user->update([
                'notify_reactions' => $inputs['notify_reactions'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_post_shared'] != null) {
            $user->update([
                'notify_post_shared' => $inputs['notify_post_shared'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['notify_post_on_message'] != null) {
            $user->update([
                'notify_post_on_message' => $inputs['notify_post_on_message'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['email_frequency'] != null) {
            $user->update([
                'email_frequency' => $inputs['email_frequency'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['allow_search_engine'] != null) {
            $user->update([
                'allow_search_engine' => $inputs['allow_search_engine'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['allow_search_by_email'] != null) {
            $user->update([
                'allow_search_by_email' => $inputs['allow_search_by_email'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['allow_sponsored_messages'] != null) {
            $user->update([
                'allow_sponsored_messages' => $inputs['allow_sponsored_messages'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['allow_messages_not_connected'] != null) {
            $user->update([
                'allow_messages_not_connected' => $inputs['allow_messages_not_connected'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['tips_at_every_login'] != null) {
            $user->update([
                'tips_at_every_login' => $inputs['tips_at_every_login'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['is_online'] != null) {
            $user->update([
                'is_online' => $inputs['is_online'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $user->update([
                'status_id' => $inputs['status_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['type_id'] != null) {
            $user->update([
                'type_id' => $inputs['type_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['visibility_id'] != null) {
            $user->update([
                'visibility_id' => $inputs['visibility_id'],
                'updated_at' => now(),
            ]);
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        $users = User::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.delete_user_success'));

        $password_reset = PasswordResetToken::where('email', $user->email)->orWhere('phone', $user->phone)->first();
        $personal_access_tokens = PersonalAccessToken::where('tokenable_id', $user->id)->get();
        $notifications = Notification::where('from_user_id', $user->id)->orWhere('to_user_id', $user->id)->get();
        $histories = History::where('from_user_id', $user->id)->orWhere('to_user_id', $user->id)->get();
        $directory = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $user->id;

        $user->delete();
        $password_reset->delete();

        if (!is_null($personal_access_tokens)) {
            foreach ($personal_access_tokens as $personal_access_token):
                $personal_access_token->delete();
            endforeach;
        }

        if (!is_null($notifications)) {
            foreach ($notifications as $notification):
                $notification->delete();
            endforeach;
        }

        if (!is_null($histories)) {
            foreach ($histories as $history):
                $history->delete();
            endforeach;
        }

        if (Storage::exists($directory)) {
            Storage::deleteDirectory($directory);
        }

        $users = User::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.delete_user_success'));
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Search a member
     *
     * @param  string $data
     * @param  int $visitor_id
     * @return \Illuminate\Http\Response
     */
    public function search($data, $visitor_id = null)
    {
        // Group
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Type
        $search_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des recherches'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des recherches')->first();
        // Search request
        $users = User::where('firstname', 'LIKE', '%' . $data . '%')->orWhere('lastname', 'LIKE', '%' . $data . '%')->orWhere('surname', 'LIKE', '%' . $data . '%')->orWhere('username', 'LIKE', '%' . $data . '%')->get();

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        if (is_null($users)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($users != null) {
            if ($visitor_id != null) {
                $visitor = User::find($visitor_id);

                if (is_null($visitor)) {
                    return $this->handleError(__('notifications.find_visitor_404'));
                }

                foreach ($users as $user): 
                    if ($visitor_id != $user->id) {
                        History::create([
                            'search_content' => $data,
                            'type_id' => $search_history_type->id,
                            'from_user_id' => $visitor->id,
                            'to_user_id' => $user->id
                        ]);
                    }
                endforeach;
            }
        }

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));
    }

    /**
     * Find by "username"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $username
     * @return \Illuminate\Http\Response

     */
    public function profile(Request $request, $username)
    {
        // Group
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Type
        $consultation_history_type = !empty($history_type_group) ? Type::where([['type_name->fr', 'Historique des consultations'], ['group_id', $history_type_group->id]])->first() : Type::where('type_name->fr', 'Historique des consultations')->first();
        // Request
        $user = User::where('username', $username)->first();

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($request->hasHeader('X-user-id') and $request->hasHeader('X-ip-address') or $request->hasHeader('X-user-id') and !$request->hasHeader('X-ip-address')) {
            $session = Session::where('user_id', $request->header('X-user-id'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->hasHeader('X-ip-address') ? $request->header('X-ip-address') : null,
                    'user_agent' => $request->header('X-user-agent'),
                    'user_id' => $request->header('X-user-id')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $new_session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => $session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $session->id
                ]);
            }
        }

        if ($request->hasHeader('X-ip-address')) {
            $session = Session::where('ip_address', $request->header('X-ip-address'))->first();

            if (is_null($session)) {
                $new_session = Session::create([
                    'id' => Str::random(255),
                    'ip_address' =>  $request->header('X-ip-address'),
                    'user_agent' => $request->header('X-user-agent')
                ]);

                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'to_user_id' => $user->id,
                    'session_id' => $new_session->id
                ]);

            } else {
                History::create([
                    'type_id' => is_null($consultation_history_type) ? null : $consultation_history_type->id,
                    'from_user_id' => is_null($session->user_id) ? null : $session->user_id,
                    'to_user_id' => $user->id,
                    'session_id' => $session->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.find_user_success'));
    }

    /**
     * Search all users having a specific role
     *
     * @param  string $locale
     * @param  string $role_name
     * @return \Illuminate\Http\Response
     */
    public function findByRole($locale, $role_name)
    {
        $users = User::whereHas('roles', function ($query) use ($locale, $role_name) {
                                    $query->where('role_name->' . $locale, $role_name);
                                })->orderByDesc('users.created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));    
    }

    /**
     * Search all users having a role different than the given
     *
     * @param  string $locale
     * @param  string $role_name
     * @return \Illuminate\Http\Response
     */
    public function findByNotRole($locale, $role_name)
    {
        $users = User::whereDoesntHave('roles', function ($query) use ($locale, $role_name) {
                                    $query->where('role_name->' . $locale, $role_name);
                                })->orderByDesc('users.created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));    
    }

    /**
     * Search all users having specific status.
     *
     * @param  string $status_id
     * @return \Illuminate\Http\Response
     */
    public function findByStatus($status_id)
    {
        $users = User::where('status_id', $status_id)->orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));
    }

    /**
     * Search all users having specific visibility.
     *
     * @param  string $visibility_id
     * @return \Illuminate\Http\Response
     */
    public function findByVisibility($visibility_id)
    {
        $users = User::where('visibility_id', $visibility_id)->orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));
    }

    /**
     * Search all users having specific visibility.
     *
     * @param  int $user_id
     * @return \Illuminate\Http\Response
     */
    public function connectionsProposal($user_id)
    {
        // Groups
        $to_connect_visibility_group = Group::where('group_name->fr', 'Visibilité pour se connecter')->first();
        // Visibility
        $everybody_on_kulisha_visibility = Visibility::where([['visibility_name->fr', 'Tout le monde sur Kulisha (recommandé)'], ['group_id', $to_connect_visibility_group->id]])->first();
        $only_mutual_connections_visibility = Visibility::where([['visibility_name->fr', 'Uniquement les connexions mutuelles'], ['group_id', $to_connect_visibility_group->id]])->first();
        // User
        $user = User::find($user_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // All users visibles by everybody or those who have the same connections as the current user
        $users = User::where(function ($query) use ($everybody_on_kulisha_visibility) {
                            $query->where('visibility_id.' . $everybody_on_kulisha_visibility->id);
                        })
                        ->orWhere(function ($query) use ($only_mutual_connections_visibility, $user) {
                            $query->where('visibility_id' . $only_mutual_connections_visibility->id)->whereHas('subscriptions', function ($q) use ($user) {
                                        $q->where('subscriptions.user_id' . $user->id)->orWhere('subscriptions.subscriber_id' . $user->id);
                                    });
                        })->orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Get inputs
        $inputs = [
            'username' => $request->username,
            'password' => $request->password
        ];

        if ($inputs['username'] == null OR $inputs['username'] == ' ') {
            return $this->handleError($inputs['username'], __('validation.required'), 400);
        }

        if ($inputs['password'] == null) {
            return $this->handleError($inputs['password'], __('validation.required'), 400);
        }

        if (is_numeric($inputs['username'])) {
            $user = User::where('phone', $inputs['username'])->first();

            if (!$user) {
                return $this->handleError($inputs['username'], __('auth.username'), 400);
            }

            if (!Hash::check($inputs['password'], $user->password)) {
                return $this->handleError($inputs['password'], __('auth.password'), 400);
            }

            $password_reset = PasswordResetToken::where('phone', $user->phone)->first();

            if ($user->phone_verified_at == null) {
                return $this->handleError(new ResourcesPasswordReset($password_reset), __('notifications.unverified_token'), 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->update([
                'api_token' => $token,
                'updated_at' => now(),
            ]);

            if ($request->hasHeader('X-ip-address') AND $request->hasHeader('X-user-agent') OR $request->hasHeader('X-ip-address') AND !$request->hasHeader('X-user-agent')) {
                $session = Session::where('ip_address', $request->header('X-ip-address'))->first();

                if (is_null($session)) {
                    Session::create([
                        'id' => Str::random(255),
                        'ip_address' =>  $request->header('X-ip-address'),
                        'user_agent' => $request->header('X-user-agent'),
                        'user_id' => $user->id
                    ]);
                }
            }

            if ($request->hasHeader('X-user-agent')) {
                $session = Session::where('user_agent', $request->header('X-user-agent'))->first();

                if (is_null($session)) {
                    Session::create([
                        'id' => Str::random(255),
                        'ip_address' =>  $request->header('X-ip-address'),
                        'user_agent' => $request->header('X-user-agent'),
                        'user_id' => $user->id
                    ]);
                }
            }

            return $this->handleResponse(new ResourcesUser($user), __('notifications.login_user_success'));

        } else {
            $user = User::where('email', $inputs['username'])->orWhere('username', $inputs['username'])->first();

            if (!$user) {
                return $this->handleError($inputs['username'], __('auth.username'), 400);
            }

            if (!Hash::check($inputs['password'], $user->password)) {
                return $this->handleError($inputs['password'], __('auth.password'), 400);
            }

            $password_reset = PasswordResetToken::where('email', $user->email)->first();

            if ($user->email_verified_at == null) {
                return $this->handleError(new ResourcesPasswordReset($password_reset), __('notifications.unverified_token'), 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->update([
                'api_token' => $token,
                'updated_at' => now(),
            ]);

            if ($request->hasHeader('X-ip-address') AND $request->hasHeader('X-user-agent') OR $request->hasHeader('X-ip-address') AND !$request->hasHeader('X-user-agent')) {
                $session = Session::where('ip_address', $request->header('X-ip-address'))->first();

                if (is_null($session)) {
                    Session::create([
                        'id' => Str::random(255),
                        'ip_address' =>  $request->header('X-ip-address'),
                        'user_agent' => $request->header('X-user-agent'),
                        'user_id' => $user->id
                    ]);
                }
            }

            if ($request->hasHeader('X-user-agent')) {
                $session = Session::where('user_agent', $request->header('X-user-agent'))->first();

                if (is_null($session)) {
                    Session::create([
                        'id' => Str::random(255),
                        'ip_address' =>  $request->header('X-ip-address'),
                        'user_agent' => $request->header('X-user-agent'),
                        'user_id' => $user->id
                    ]);
                }
            }

            return $this->handleResponse(new ResourcesUser($user), __('notifications.login_user_success'));
        }
    }

    /**
     * Ask subscription to another member.
     *
     * @param  int $id
     * @param  int $concerned_id
     * @return \Illuminate\Http\Response
     */
    public function addConnection($id, $concerned_id)
    {
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $member_type_group = Group::where('group_name->fr', 'Type de membre')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $on_hold_status = Status::where([['status_name->fr', 'En attente'], ['group_id', $susbcription_status_group->id]])->first();
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $ordinary_member_type = Type::where([['type_name->fr', 'Membre ordinaire'], ['group_id', $member_type_group->id]])->first();
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $connection_request_sent_type = Type::where([['type_name->fr', 'Demande de connexion envoyée'], ['group_id', $notification_type_group->id]])->first();
        // Users
        $user = User::find($id);
        $concerned = User::find($concerned_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (is_null($concerned)) {
            return $this->handleError(__('notifications.find_concerned_404'));
        }

        if ($concerned->type_id == $ordinary_member_type->id) {
            Subscription::create([
                'user_id' => $concerned->id,
                'subscriber_id' => $user->id,
                'status_id' => $accepted_status->id
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            $notification = Notification::create([
                'type_id' => $connection_request_sent_type->id,
                'status_id' => $unread_notification_status,
                'from_user_id' => $user->id,
                'to_user_id' => $concerned->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $user->id,
                'to_user_id' => $concerned->id,
                'for_notification_id' => $notification->id
            ]);

        } else {
            Subscription::create([
                'user_id' => $concerned->id,
                'subscriber_id' => $user->id,
                'status_id' => $on_hold_status->id
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            $notification = Notification::create([
                'type_id' => $connection_request_sent_type->id,
                'status_id' => $unread_notification_status,
                'from_user_id' => $user->id,
                'to_user_id' => $concerned->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $user->id,
                'to_user_id' => $concerned->id,
                'for_notification_id' => $notification->id
            ]);
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Ask subscription to another member.
     *
     * @param  int $id
     * @param  int $concerned_id
     * @return \Illuminate\Http\Response
     */
    public function invitationRefusal($id, $concerned_id)
    {
        // Groups
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $connection_request_rejected_type = Type::where([['type_name->fr', 'Demande de connexion rejetée'], ['group_id', $notification_type_group->id]])->first();
        // Users
        $user = User::find($id);
        $concerned = User::find($concerned_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (is_null($concerned)) {
            return $this->handleError(__('notifications.find_concerned_404'));
        }

        $subscription = Subscription::where([['user_id', $user->id], ['subscriber_id', $concerned->id]])->first();

        if (is_null($subscription)) {
            return $this->handleError(__('notifications.find_subscription_404'));
        }

        // Delete subscription
        $subscription->delete();

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        $notification = Notification::create([
            'type_id' => $connection_request_rejected_type->id,
            'status_id' => $unread_notification_status,
            'from_user_id' => $user->id,
            'to_user_id' => $concerned->id
        ]);

        History::create([
            'type_id' => $activities_history_type->id,
            'status_id' => $unread_history_status,
            'from_user_id' => $user->id,
            'to_user_id' => $concerned->id,
            'for_notification_id' => $notification->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Send an invitation to join the network
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendExternalInvitation(Request $request)
    {
        $object = new stdClass();
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        // Statuses
        $on_hold_status = Status::where([['status_name->fr', 'En attente'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $invitations_history_type = Type::where([['type_name->fr', 'Historique des invitations'], ['group_id', $history_type_group->id]])->first();
        // Users
        $user = User::find($request->id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (isset($request->phone)) {
            $subscription = Subscription::create([
                'phone' => $request->phone,
                'subscriber_id' => $user->id,
                'status_id' => $on_hold_status->id
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            History::create([
                'type_id' => $invitations_history_type->id,
                'status_id' => $unread_history_status->id,
                'subscription_id' => $subscription->id,
                'from_user_id' => $user->id
            ]);

            $object->subscription = new ResourcesSubscription($subscription);
        }

        if (isset($request->email)) {
            $subscription = Subscription::create([
                'email' => $request->email,
                'subscriber_id' => $user->id,
                'status_id' => $on_hold_status->id
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            History::create([
                'type_id' => $invitations_history_type->id,
                'status_id' => $unread_history_status->id,
                'subscription_id' => $subscription->id,
                'from_user_id' => $user->id
            ]);

            $object->subscription = new ResourcesSubscription($subscription);
        }

        $object->user = new ResourcesUser($user);

        return $this->handleResponse($object, __('notifications.invitation_sent'));
    }

    /**
     * Ask subscription to a community.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Request $request)
    {
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $access_type_group = Group::where('group_name->fr', 'Type d’accès')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $reaction_on_invitation_group = Group::where('group_name->fr', 'Réaction sur invitation')->first();
        // Statuses
        $on_hold_status = Status::where([['status_name->fr', 'En attente'], ['group_id', $susbcription_status_group->id]])->first();
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $public_type = Type::where([['type_name->fr', 'Public'], ['group_id' => $access_type_group->id]])->first();
        $private_type = Type::where([['type_name->fr', 'Privé'], ['group_id' => $access_type_group->id]])->first();
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $invitation_type = Type::where([['type_name->fr', 'Invitation'], ['group_id', $notification_type_group->id]])->first();
        // Reactions
        $i_accept_reaction = Reaction::where([['reaction_name->fr', 'J’y serai'], ['group_id', $reaction_on_invitation_group->id]])->first();
        // Requests
        $user = User::find($request->id);
        $visitor = User::find($request->visitor_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (is_null($visitor)) {
            return $this->handleError(__('notifications.find_visitor_404'));
        }

        if (isset($request->event_id)) {
            $event = Event::find($request->event_id);

            if (is_null($event)) {
                return $this->handleError(__('notifications.find_event_404'));
            }

            // If it's the current user who subscribed
            if ($user->id == $visitor->id) {
                // If the event is public, accept the user
                if ($event->type_id == $public_type->id) {
                    $event->users()->attach($user->id, [
                        'status_id' => $accepted_status->id,
                        'reaction_id' => $i_accept_reaction->id
                    ]);
                }

                // If the event is private, put the user on hold
                if ($event->type_id == $private_type->id) {
                    $event->users()->attach($user->id, [
                        'status_id' => $on_hold_status->id,
                        'reaction_id' => $i_accept_reaction->id
                    ]);
                }

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'event_id' => $event->id
                ]);
            }

            // If it's a event member who sent an invitation to another member
            if ($user->id != $visitor->id) {
                $event->users()->attach($user->id, ['status_id' => $accepted_status->id]);

                /*
                    HISTORY AND/OR NOTIFICATION MANAGEMENT
                */
                $notification = Notification::create([
                    'type_id' => $invitation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        if (isset($request->community_id)) {
            $community = Community::find($request->community_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            // If it's the current user who subscribed
            if ($user->id == $visitor->id) {
                // If the community is public, accept the user
                if ($community->type_id == $public_type->id) {
                    $community->users()->attach($user->id, [
                        'status_id' => $accepted_status->id,
                        'reaction_id' => $i_accept_reaction->id
                    ]);
                }

                // If the community is private, put the user on hold
                if ($community->type_id == $private_type->id) {
                    $community->users()->attach($user->id, [
                        'status_id' => $on_hold_status->id,
                        'reaction_id' => $i_accept_reaction->id
                    ]);
                }

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'community_id' => $community->id
                ]);
            }

            // If it's a community member who sent an invitation to another member
            if ($user->id != $visitor->id) {
                $community->users()->attach($user->id, ['status_id' => $accepted_status->id]);

                /*
                    HISTORY AND/OR NOTIFICATION MANAGEMENT
                */
                $notification = Notification::create([
                    'type_id' => $invitation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'community_id' => $community->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.subscribe_user_success'));
    }

    /**
     * Ask subscription to an event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe(Request $request)
    {
        // Groups
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $separation_type = Type::where([['type_name->fr', 'Séparation'], ['group_id', $notification_type_group->id]])->first();
        $expulsion_type = Type::where([['type_name->fr', 'Expulsion'], ['group_id', $notification_type_group->id]])->first();
        // Requests
        $user = User::find($request->id);
        $visitor = User::find($request->visitor_id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (is_null($visitor)) {
            return $this->handleError(__('notifications.find_visitor_404'));
        }

        if (isset($request->event_id)) {
            $event = Event::find($request->event_id);

            if (is_null($event)) {
                return $this->handleError(__('notifications.event_404'));
            }

            $event->users()->detach([$user->id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            // If it's the current user who has left
            if ($user->id == $visitor->id) {
                $notification = Notification::create([
                    'type_id' => $separation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            // If it's a event member who has withdrawn the current user
            if ($user->id != $visitor->id) {
                $notification = Notification::create([
                    'type_id' => $expulsion_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $user->id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        if (isset($request->community_id)) {
            $community = Community::find($request->community_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.community_404'));
            }

            $community->users()->detach([$user->id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            // If it's the current user who has left
            if ($user->id == $visitor->id) {
                $notification = Notification::create([
                    'type_id' => $separation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            // If it's a event member who has withdrawn the current user
            if ($user->id != $visitor->id) {
                $notification = Notification::create([
                    'type_id' => $expulsion_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $visitor->id,
                    'to_user_id' => $user->id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $user->id,
                    'community_id' => $community->id,
                    'for_notification_id' => $notification->id
                ]);
            }
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.unsubscribe_user_success'));
    }

    /**
     * Ask subscription to an event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reactToInvitation(Request $request)
    {
        // Groups
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        $reaction_on_invitation_group = Group::where('group_name->fr', 'Réaction sur invitation')->first();
        // Statuses
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $acceptation_maybe_type = Type::where([['type_name->fr', 'Acception peut-être d’une invitation'], ['group_id', $notification_type_group->id]])->first();
        $acceptation_type = Type::where([['type_name->fr', 'Acception d’une invitation'], ['group_id', $notification_type_group->id]])->first();
        $refusal_type = Type::where([['type_name->fr', 'Refus d’une invitation'], ['group_id', $notification_type_group->id]])->first();
        // Reactions
        $maybe_reaction = Reaction::where([['reaction_name->fr', 'Peut-être'], ['group_id', $reaction_on_invitation_group->id]])->first();
        $i_accept_reaction = Reaction::where([['reaction_name->fr', 'J’y serai'], ['group_id', $reaction_on_invitation_group->id]])->first();
        $decline_reaction = Reaction::where([['reaction_name->fr', 'Décliner'], ['group_id', $reaction_on_invitation_group->id]])->first();
        // Requests
        $user = User::find($request->id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (isset($request->event_id)) {
            $event = Event::find($request->event_id);

            if (is_null($event)) {
                return $this->handleError(__('notifications.find_event_404'));
            }

            $event->users()->updateExistingPivot($user->id, ['reaction_id' => $request->reaction_id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            if ($request->reaction_id == $maybe_reaction->id) {
                $notification = Notification::create([
                    'type_id' => $acceptation_maybe_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            if ($request->reaction_id == $i_accept_reaction->id) {
                $notification = Notification::create([
                    'type_id' => $acceptation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            if ($request->reaction_id == $decline_reaction->id) {
                $notification = Notification::create([
                    'type_id' => $refusal_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $event->user_id,
                    'event_id' => $event->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            $object = new stdClass();
            $object->event = new ResourcesEvent($event);
            $object->user = new ResourcesUser($user);
    
            return $this->handleResponse($object, __('notifications.reaction_sent'));
        }

        if (isset($request->community_id)) {
            $community = Event::find($request->community_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.community_404'));
            }

            $community->users()->updateExistingPivot($user->id, ['reaction_id' => $request->reaction_id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            if ($request->reaction_id == $i_accept_reaction->id) {
                $notification = Notification::create([
                    'type_id' => $acceptation_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            if ($request->reaction_id == $decline_reaction->id) {
                $notification = Notification::create([
                    'type_id' => $refusal_type->id,
                    'status_id' => $unread_notification_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id
                ]);

                History::create([
                    'type_id' => $activities_history_type->id,
                    'status_id' => $unread_history_status,
                    'from_user_id' => $user->id,
                    'to_user_id' => $community->user_id,
                    'community_id' => $community->id,
                    'for_notification_id' => $notification->id
                ]);
            }

            $object = new stdClass();
            $object->community = new ResourcesCommunity($community);
            $object->user = new ResourcesUser($user);

            return $this->handleResponse($object, __('notifications.reaction_sent'));
        }
    }

    /**
     * Update event/community subscription.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function acceptSubscription(Request $request)
    {
        // Groups
        $susbcription_status_group = Group::where('group_name->fr', 'Etat de la souscription')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $accepted_status = Status::where([['status_name->fr', 'Acceptée'], ['group_id', $susbcription_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $subscription_accepted_type = Type::where([['type_name->fr', 'Abonnement accepté'], ['group_id', $notification_type_group->id]])->first();
        // Requests
        $user = User::find($request->id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (isset($request->event_id)) {
            $event = Event::find($request->event_id);

            if (is_null($event)) {
                return $this->handleError(__('notifications.find_event_404'));
            }

            $event->users()->updateExistingPivot($user->id, ['status_id' => $accepted_status->id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            $notification = Notification::create([
                'type_id' => $subscription_accepted_type->id,
                'status_id' => $unread_notification_status,
                'from_user_id' => $event->user_id,
                'to_user_id' => $user->id,
                'event_id' => $event->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $event->user_id,
                'to_user_id' => $user->id,
                'event_id' => $event->id,
                'for_notification_id' => $notification->id
            ]);
        }

        if (isset($request->community_id)) {
            $community = Community::find($request->community_id);

            if (is_null($community)) {
                return $this->handleError(__('notifications.find_community_404'));
            }

            $community->users()->updateExistingPivot($user->id, ['status_id' => $accepted_status->id]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            $notification = Notification::create([
                'type_id' => $subscription_accepted_type->id,
                'status_id' => $unread_notification_status,
                'from_user_id' => $community->user_id,
                'to_user_id' => $user->id,
                'community_id' => $community->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $community->user_id,
                'to_user_id' => $user->id,
                'community_id' => $community->id,
                'for_notification_id' => $notification->id
            ]);
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.subscribe_user_success'));
    }

    /**
     * Switch between user statuses.
     *
     * @param  $id
     * @param  $status_id
     * @param  boolean $notify
     * @return \Illuminate\Http\Response
     */
    public function switchStatus($id, $status_id)
    {
        // Groups
        $member_status_group = Group::where('group_name->fr', 'Etat du membre')->first();
        $notification_status_group = Group::where('group_name->fr', 'Etat de la notification')->first();
        $history_status_group = Group::where('group_name->fr', 'Etat de l’historique')->first();
        $history_type_group = Group::where('group_name->fr', 'Type d’historique')->first();
        $notification_type_group = Group::where('group_name->fr', 'Type de notification')->first();
        // Statuses
        $activated_member_status = Status::where([['status_name->fr', 'Activé'], ['group_id', $member_status_group->id]])->first();
        $disabled_member_status = Status::where([['status_name->fr', 'Désactivé'], ['group_id', $member_status_group->id]])->first();
        $blocked_member_status = Status::where([['status_name->fr', 'Bloqué'], ['group_id', $member_status_group->id]])->first();
        $deleted_member_status = Status::where([['status_name->fr', 'Supprimé'], ['group_id', $member_status_group->id]])->first();
        $unread_notification_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $notification_status_group->id]])->first();
        $unread_history_status = Status::where([['status_name->fr', 'Non lue'], ['group_id', $history_status_group->id]])->first();
        // Types
        $activities_history_type = Type::where([['type_name->fr', 'Historique des activités'], ['group_id', $history_type_group->id]])->first();
        $reactivated_account_type = Type::where([['type_name->fr', 'Compte réactivé'], ['group_id', $notification_type_group->id]])->first();
        $disabled_account_type = Type::where([['type_name->fr', 'Compte désactivé'], ['group_id', $notification_type_group->id]])->first();
        $blocked_account_type = Type::where([['type_name->fr', 'Compte bloqué'], ['group_id', $notification_type_group->id]])->first();
        $deleted_account_type = Type::where([['type_name->fr', 'Compte supprimé'], ['group_id', $notification_type_group->id]])->first();
        // Requests
        $user = User::find($id);
        $status = Status::where([['id', $status_id], ['group_id', $member_status_group->id]])->first();

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if (is_null($status)) {
            return $this->handleError(__('notifications.find_status_404'));
        }

        // update "status_id" column
        $user->update([
            'status_id' => $status->id,
            'updated_at' => now()
        ]);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        // The user account is reactivated
        if ($status_id == $activated_member_status->id) {
            $notification = Notification::create([
                'type_id' => $reactivated_account_type->id,
                'status_id' => $unread_notification_status,
                'to_user_id' => $user->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $user->id,
                'for_notification_id' => $notification->id
            ]);
        }

        // The user account is disabled
        if ($status_id == $disabled_member_status->id) {
            $notification = Notification::create([
                'type_id' => $disabled_account_type->id,
                'status_id' => $unread_notification_status,
                'to_user_id' => $user->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $user->id,
                'for_notification_id' => $notification->id
            ]);
        }

        // The user account is blocked
        if ($status_id == $blocked_member_status->id) {
            $notification = Notification::create([
                'type_id' => $blocked_account_type->id,
                'status_id' => $unread_notification_status,
                'to_user_id' => $user->id
            ]);
        }

        // The user account is deleted
        if ($status_id == $deleted_member_status->id) {
            $notification = Notification::create([
                'type_id' => $deleted_account_type->id,
                'status_id' => $unread_notification_status,
                'to_user_id' => $user->id
            ]);

            History::create([
                'type_id' => $activities_history_type->id,
                'status_id' => $unread_history_status,
                'from_user_id' => $user->id,
                'for_notification_id' => $notification->id
            ]);
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Switch between user types.
     *
     * @param  $id
     * @param  $type_id
     * @param  boolean $notify
     * @return \Illuminate\Http\Response
     */
    public function switchType($id, $type_id, $notify = false)
    {
        $ordinary_type = Type::where('type_name->fr', 'Membre ordinaire')->first();
        $type_premium = Type::where('type_name->fr', 'Membre premium')->first();
        $unread_status = Status::where('status_name->fr', 'Non lue')->first();
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($type_id == $ordinary_type->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $ordinary_type->id,
                'updated_at' => now()
            ]);
        }

        if ($type_id == $type_premium->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $type_premium->id,
                'updated_at' => now()
            ]);

            /*
                HISTORY AND/OR NOTIFICATION MANAGEMENT
            */
            if ($notify == true) {
                Notification::create([
                    'notification_url' => 'about/terms_of_use',
                    'notification_content' => [
                        'af' => 'Welkom by Kulisha Premium Service. Klik asseblief hier om besonderhede oor hierdie diens te sien.',
                        'de' => 'Willkommen beim Kulisha Premium Service. Bitte klicken Sie hier, um Details zu diesem Service anzuzeigen.',
                        'ar' => 'مرحبًا بك في خدمة كوليشا المميزة. الرجاء الضغط هنا لعرض تفاصيل حول هذه الخدمة.',
                        'zh' => '欢迎使用 Kulisha 优质服务。 请点击此处查看有关此服务的详细信息。',
                        'en' => 'Welcome to Kulisha Premium Service. Please click here to view details about this service.',
                        'es' => 'Bienvenido al servicio premium de Kulisha. Haga clic aquí para ver detalles sobre este servicio.',
                        'fr' => 'Bienvenue au service Premium de Kulisha. Veuillez cliquer ici pour voir les détails sur ce service.',
                        'it' => 'Benvenuto nel servizio premium Kulisha. Fare clic qui per visualizzare i dettagli su questo servizio.',
                        'ja' => 'Kulisha プレミアム サービスへようこそ。 このサービスの詳細については、ここをクリックしてください。',
                        'nl' => 'Welkom bij Kulisha Premiumservice. Klik hier om details over deze service te bekijken.',
                        'ru' => 'Добро пожаловать в Кулиша Премиум Сервис. Пожалуйста, нажмите здесь, чтобы просмотреть подробную информацию об этой услуге.',
                        'sw' => 'Karibu Kulisha Premium Service. Tafadhali bofya hapa ili kuona maelezo kuhusu huduma hii.',
                        'tr' => 'Kulisha Premium Hizmetine hoş geldiniz. Bu hizmete ilişkin ayrıntıları görüntülemek için lütfen buraya tıklayın.',
                        'cs' => 'Vítejte v prémiové službě Kuliša. Kliknutím sem zobrazíte podrobnosti o této službě.' ],
                    'color' => 'success',
                    'icon' => 'bi bi-shield-lock',
                    'image_url' => 'assets/img/logo-reverse.png',
                    'status_id' => $unread_status->id,
                    'user_id' => $user->id,
                ]);
            }
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Switch between user visibilities.
     *
     * @param  $id
     * @param  $type_id
     * @return \Illuminate\Http\Response
     */
    public function switchVisibility($id, $type_id)
    {
        $activities_history_type = Type::where('type_name->fr', 'Historique des activités')->first();
        $ordinary_type = Type::where('type_name->fr', 'Membre ordinaire')->first();
        $type_premium = Type::where('type_name->fr', 'Membre premium')->first();
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        if ($type_id == $ordinary_type->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $ordinary_type->id,
                'updated_at' => now()
            ]);
        }

        if ($type_id == $type_premium->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $type_premium->id,
                'updated_at' => now()
            ]);
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'history_url' => 'account',
            'history_content' => [
                'af' => 'Jy het jou sigbaarheid verander.',
                'de' => 'Sie haben Ihre Sichtbarkeit geändert.',
                'ar' => 'لقد غيرت رؤيتك.',
                'zh' => '你已经改变了你的可见度。',
                'en' => 'You have changed your visibility.',
                'es' => 'Has cambiado tu visibilidad.',
                'fr' => 'Vous avez changé votre visibilité.',
                'it' => 'Hai cambiato la tua visibilità.',
                'ja' => '可視性が変わりました。',
                'nl' => 'Je hebt je zichtbaarheid veranderd.',
                'ru' => 'Вы изменили свою видимость.',
                'sw' => 'Umebadilisha mwonekano wako.',
                'tr' => 'Görünürlüğünüzü değiştirdiniz.',
                'cs' => 'Změnili jste viditelnost.'
            ],
            'color' => 'warning',
            'icon' => 'bi bi-shield-lock',
            'image_url' => $user->profile_photo_path,
            'type_id' => $activities_history_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Update user role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request, $id)
    {
        $user = User::find($id);

        $user->roles()->syncWithoutDetaching([$request->role_id]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Update user password in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        $activities_history_type = Type::where('type_name->fr', 'Historique des activités')->first();
        // Get inputs
        $inputs = [
            'former_password' => $request->former_password,
            'new_password' => $request->new_password,
            'confirm_new_password' => $request->confirm_new_password
        ];
        $user = User::find($id);

        if ($inputs['former_password'] == null) {
            return $this->handleError($inputs['former_password'], __('validation.custom.former_password.empty'), 400);
        }

        if ($inputs['new_password'] == null) {
            return $this->handleError($inputs['new_password'], __('validation.custom.new_password.empty'), 400);
        }

        if ($inputs['confirm_new_password'] == null) {
            return $this->handleError($inputs['confirm_new_password'], __('notifications.confirm_new_password'), 400);
        }

        if (Hash::check($inputs['former_password'], $user->password) == false) {
            return $this->handleError($inputs['former_password'], __('auth.password'), 400);
        }

        if ($inputs['confirm_new_password'] != $inputs['new_password']) {
            return $this->handleError($inputs['confirm_new_password'], __('notifications.confirm_new_password'), 400);
        }

        if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['new_password']) == 0) {
            return $this->handleError($inputs['new_password'], __('miscellaneous.password.error'), 400);
        }

        // Update password reset
        $password_reset_by_email = PasswordResetToken::where('email', $user->email)->first();
        $password_reset_by_phone = PasswordResetToken::where('phone', $user->phone)->first();

        if ($password_reset_by_email != null) {
            // Update password reset in the case user want to reset his password
            $password_reset_by_email->update([
                'code' => random_int(1000000, 9999999),
                'former_password' => $inputs['new_password'],
                'updated_at' => now(),
            ]);
        }

        if ($password_reset_by_phone != null) {
            // Update password reset in the case user want to reset his password
            $password_reset_by_phone->update([
                'code' => random_int(1000000, 9999999),
                'former_password' => $inputs['new_password'],
                'updated_at' => now(),
            ]);
        }

        // update "password" and "password_visible" column
        $user->update([
            'password' => Hash::make($inputs['new_password']),
            'updated_at' => now()
        ]);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'history_url' => 'account',
            'history_content' => [
                'af' => 'Jy het jou wagwoord verander.',
                'de' => 'Sie haben Ihr Passwort geändert.',
                'ar' => 'لقد قمت بتغيير كلمة المرور الخاصة بك.',
                'zh' => '您已更改密码。',
                'en' => 'You have changed your password.',
                'es' => 'Has cambiado tu contraseña.',
                'fr' => 'Vous avez modifié votre mot de passe.',
                'it' => 'Hai cambiato la tua password.',
                'ja' => 'パスワードを変更しました。',
                'nl' => 'U heeft uw wachtwoord gewijzigd.',
                'ru' => 'Вы изменили свой пароль.',
                'sw' => 'Umebadilisha nenosiri lako.',
                'tr' => 'Şifrenizi değiştirdiniz.',
                'cs' => 'Změnili jste heslo.'
            ],
            'color' => 'primary',
            'icon' => 'bi bi-shield-lock',
            'image_url' => $user->profile_photo_path,
            'type_id' => $activities_history_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_password_success'));
    }

    /**
     * Update user avatar picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatarPicture(Request $request, $id)
    {
        $activities_history_type = Type::where('type_name->fr', 'Historique des activités')->first();
        $inputs = [
            'user_id' => $request->user_id,
            'image_64' => $request->image_64
        ];
        // $extension = explode('/', explode(':', substr($inputs['image_64'], 0, strpos($inputs['image_64'], ';')))[1])[1];
        $replace = substr($inputs['image_64'], 0, strpos($inputs['image_64'], ',') + 1);
        // Find substring from replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $inputs['image_64']);
        $image = str_replace(' ', '+', $image);

        // Clean "avatars" directory
        $file = new Filesystem;
        $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $inputs['user_id'] . '/avatar');
        // Create image URL
		$image_url = 'images/users/' . $inputs['user_id'] . '/avatar/' . Str::random(50) . '.png';

		// Upload image
		Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

		$user = User::find($id);

        $user->update([
            'profile_photo_path' => $image_url,
            'updated_at' => now()
        ]);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'history_url' => 'account',
            'history_content' => [
                'af' => 'Jy het jou avatar gewysig.',
                'de' => 'Sie haben Ihren Avatar geändert.',
                'ar' => 'لقد قمت بتعديل الصورة الرمزية الخاصة بك.',
                'zh' => '您已经修改了头像。',
                'en' => 'You have changed your avatar.',
                'es' => 'Has modificado tu avatar.',
                'fr' => 'Vous avez modifié votre avatar.',
                'it' => 'Hai modificato il tuo avatar.',
                'ja' => 'アバターを変更しました。',
                'nl' => 'Je hebt je avatar aangepast.',
                'ru' => 'Вы изменили свой аватар.',
                'sw' => 'Umebadilisha avatar yako.',
                'tr' => 'Avatarınızı değiştirdiniz.',
                'cs' => 'Upravili jste svůj avatar.'
            ],
            'color' => 'info',
            'icon' => 'bi bi-file-earmark-person',
            'image_url' => $user->profile_photo_path,
            'type_id' => $activities_history_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Update user avatar picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCover(Request $request, $id)
    {
        $activities_history_type = Type::where('type_name->fr', 'Historique des activités')->first();
        $inputs = [
            'user_id' => $request->user_id,
            'image_64' => $request->image_64,
            'x' => $request->x,
            'y' => $request->y,
            'width' => $request->width,
            'height' => $request->height
        ];
        // $extension = explode('/', explode(':', substr($inputs['image_64'], 0, strpos($inputs['image_64'], ';')))[1])[1];
        $replace = substr($inputs['image_64'], 0, strpos($inputs['image_64'], ',') + 1);
        // Find substring from replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $inputs['image_64']);
        $image = str_replace(' ', '+', $image);

        // Clean "covers" directory
        $file = new Filesystem;
        $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $inputs['user_id'] . '/cover');
        // Create image URL
		$image_url = 'images/users/' . $inputs['user_id'] . '/cover/' . Str::random(50) . '.png';

		// Upload image
		Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

		$user = User::find($id);

        $user->update([
            'cover_photo_path' => $image_url,
            'cover_coordinates' => $inputs['x'] . '-' . $inputs['y'] . '-' . $inputs['width'] . '-' . $inputs['height'],
            'updated_at' => now()
        ]);

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        History::create([
            'history_url' => 'account',
            'history_content' => [
                'af' => 'Jy het jou dekking verander.',
                'de' => 'Sie haben Ihr Titelbild bearbeitet.',
                'ar' => 'لقد قمت بتحرير صورة الغلاف الخاصة بك.',
                'zh' => '您已编辑了封面照片。',
                'en' => 'You have changed your cover photo.',
                'es' => 'Has editado tu foto de portada.',
                'fr' => 'Vous avez modifié votre photo de couverture.',
                'it' => 'Hai modificato la tua foto di copertina.',
                'ja' => 'カバー写真を編集しました。',
                'nl' => 'Je hebt je omslagfoto bewerkt.',
                'ru' => 'Вы отредактировали обложку.',
                'sw' => 'Umehariri picha ya jalada lako.',
                'tr' => 'Kapak fotoğrafınızı düzenlediniz.',
                'cs' => 'Upravili jste svou titulní fotku.'
            ],
            'color' => 'danger',
            'icon' => 'bi bi-image',
            'image_url' => $user->cover_coordinates,
            'type_id' => $activities_history_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Upload user documents in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadDoc(Request $request, $id)
    {
        $inputs = [
            'file_name' => $request->file_name,
            'user_id' => $request->user_id,
            'document' => $request->file('document'),
            'extension' => $request->file('document')->extension()
        ];
        // Validate file mime type
        $validator = Validator::make($inputs, [
            'document' => 'required|mimes:txt,pdf,doc,docx,xls,xlsx,ppt,pptx,pps,ppsx'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        // Current user
		$user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Create file name
		$file_name = 'documents/users/' . $inputs['user_id'] . '/' . Str::random(50) . '.' . $inputs['extension'];

		// Upload file
		Storage::url(Storage::disk('public')->put($file_name, $inputs['audio']));

		// Find type by name to get its ID
		$document_type = Type::where('type_name', 'Document')->first();

        File::create([
            'file_name' => $inputs['file_name'],
            'file_url' => '/' . $file_name,
            'type_id' => $document_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Upload user audio in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadAudio(Request $request, $id)
    {
        $inputs = [
            'file_name' => $request->file_name,
            'user_id' => $request->user_id,
            'audio' => $request->file('audio'),
            'extension' => $request->file('audio')->extension()
        ];
        // Validate file mime type
        $validator = Validator::make($inputs, [
            'audio' => 'required|mimes:mp3,wav,m4a,mid,midi,oga,opus,weba,aac'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        // Current user
		$user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Create file name
		$file_name = 'audios/users/' . $inputs['user_id'] . '/' . Str::random(50) . '.' . $inputs['extension'];

		// Upload file
		Storage::url(Storage::disk('public')->put($file_name, $inputs['audio']));

		// Find type by name to get its ID
		$audio_type = Type::where('type_name', 'Audio')->first();

        File::create([
            'file_name' => $inputs['file_name'],
            'file_url' => '/' . $file_name,
            'type_id' => $audio_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Upload user video in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadVideo(Request $request, $id)
    {
        $inputs = [
            'file_name' => $request->file_name,
            'user_id' => $request->user_id,
            'video' => $request->file('video'),
            'extension' => $request->file('video')->extension()
        ];
        // Validate file mime type
        $validator = Validator::make($inputs, [
            'video' => 'required|mimes:avi,mp4,mpeg,ogg,ts,webm,3gp,3g2'
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());       
        }

        // Current user
		$user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        // Create file name
		$file_name = 'images/users/' . $inputs['user_id'] . '/' . Str::random(50) . '.' . $inputs['extension'];

		// Upload file
		Storage::url(Storage::disk('public')->put($file_name, $inputs['video']));

		// Find type by name to get its ID
		$video_type = Type::where('type_name', 'Vidéo')->first();

        File::create([
            'file_name' => $inputs['file_name'],
            'file_url' => '/' . $file_name,
            'type_id' => $video_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Update user picture in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request, $id)
    {
        $inputs = [
            'file_name' => $request->file_name,
            'user_id' => $request->entity_id,
            'image_64' => $request->base64image
        ];
        $replace = substr($inputs['image_64'], 0, strpos($inputs['image_64'], ',') + 1);
        // Find substring from replace here eg: data:image/png;base64,
        $image = str_replace($replace, '', $inputs['image_64']);
        $image = str_replace(' ', '+', $image);
        // Current user
		$user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

		// Create file URL
		$file_name = 'images/users/' . $inputs['user_id'] . '/' . Str::random(50) . '.png';

		// Upload file
		Storage::url(Storage::disk('public')->put($file_name, base64_decode($image)));

		// Find type by name to get its ID
		$photo_type = Type::where('type_name', 'Photo')->first();

        File::create([
            'file_name' => $inputs['file_name'],
            'file_url' => '/' . $file_name,
            'type_id' => $photo_type->id,
            'user_id' => $user->id
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
	}
}
