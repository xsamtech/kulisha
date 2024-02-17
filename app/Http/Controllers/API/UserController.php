<?php

namespace App\Http\Controllers\API;

use App\Models\Notification;
use App\Models\PasswordResetToken;
use App\Models\Status;
use App\Models\Type;
use App\Models\User;
use Nette\Utils\Random;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use stdClass;
use App\Http\Resources\User as ResourcesUser;
use App\Http\Resources\PasswordResetToken as ResourcesPasswordReset;

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
        $users = User::orderByDesc('created_at')->get();

        return $this->handleResponse(ResourcesUser::collection($users), __('notifications.find_all_users_success'));
    }

    /**
     * Store a resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status_ongoing = Status::where('status_name->fr', 'En cours')->first();
        $status_unread = Status::where('status_name->fr', 'Non lue')->first();
        $type_ordinary = Type::where('type_name->fr', 'Membre ordinaire')->first();
        // Get inputs
        $inputs = [
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
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
            'api_token' => $request->api_token,
            'status_id' => is_null($status_ongoing) ? null : $status_ongoing->id,
            'type_id' => is_null($type_ordinary) ? null : $type_ordinary->id,
            'visibility_id' => $request->visibility_id
        ];
        $users = User::all();
        $password_resets = PasswordResetToken::all();
        // $basic  = new \Vonage\Client\Credentials\Basic('', '');
        // $client = new \Vonage\Client($basic);

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

                // try {
                //     $client->sms()->send(new \Vonage\SMS\Message\SMS($password_reset->phone, 'DikiTivi', (string) $password_reset->token));

                // } catch (\Throwable $th) {
                //     return $this->handleError($th->getMessage(), __('notifications.create_user_SMS_failed'), 500);
                // }

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

                    // try {
                    //     $client->sms()->send(new \Vonage\SMS\Message\SMS($password_reset->phone, 'DikiTivi', (string) $password_reset->token));

                    // } catch (\Throwable $th) {
                    //     return $this->handleError($th->getMessage(), __('notifications.create_user_SMS_failed'), 500);
                    // }
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

                // try {
                //     $client->sms()->send(new \Vonage\SMS\Message\SMS($password_reset->phone, 'DikiTivi', (string) $password_reset->token));

                // } catch (\Throwable $th) {
                //     return $this->handleError($th->getMessage(), __('notifications.create_user_SMS_failed'), 500);
                // }

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

                    // try {
                    //     $client->sms()->send(new \Vonage\SMS\Message\SMS($password_reset->phone, 'DikiTivi', (string) $password_reset->token));

                    // } catch (\Throwable $th) {
                    //     return $this->handleError($th->getMessage(), __('notifications.create_user_SMS_failed'), 500);
                    // }
                }
            }
        }

        $user = User::create($inputs);
        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'api_token' => $token,
            'updated_at' => now()
        ]);

        if ($request->role_id != null) {
            $user->roles()->attach([$request->role_id]);
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        Notification::create([
            'notification_url' => 'about/tricks',
            'notification_content' => [
                'af' => 'Welkom by Kulisha. Klik asseblief hier om alles oor jou netwerk te leer.',
                'de' => 'Willkommen in Kulisha. Bitte klicken Sie hier, um alles über Ihr Netzwerk zu erfahren.',
                'ar' => 'مرحبا بكم في كوليشا. من فضلك انقر هنا لمعرفة كل شيء عن شبكتك.',
                'zh' => '欢迎来到库利沙。 请单击此处了解有关您的网络的所有信息。',
                'en' => 'Welcome to Kulisha. Please click here to learn everything about your network.',
                'es' => 'Bienvenidos a Kulisha. Haga clic aquí para aprender todo sobre su red.',
                'fr' => 'Bienvenue sur Kulisha. Veuillez cliquer ici pour tout savoir sur votre réseau.',
                'it' => 'Benvenuti a Kulisha. Fai clic qui per scoprire tutto sulla tua rete.',
                'ja' => 'クリシャへようこそ。 ネットワークに関するすべてを確認するには、ここをクリックしてください。',
                'nl' => 'Welkom bij Kulisha. Klik hier om alles over uw netwerk te leren.',
                'ru' => 'Добро пожаловать в Кулишу. Нажмите здесь, чтобы узнать все о вашей сети.',
                'sw' => 'Karibu Kulisha. Tafadhali bofya hapa ili kujifunza kila kitu kuhusu mtandao wako.',
                'tr' => 'Kulisha\'ya hoş geldiniz. Ağınızla ilgili her şeyi öğrenmek için lütfen buraya tıklayın.',
                'cs' => 'Vítejte v destinaci Kuliša. Kliknutím sem se dozvíte vše o vaší síti.'
            ],
            'color' => 'text-success',
            'icon' => 'bi bi-exclamation-circle',
            'image_url' => 'assets/img/logo-reverse.png',
            'status_id' => is_null($status_unread) ? null : $status_unread->id,
            'user_id' => $user->id
        ]);

        $object = new stdClass();
        $object->password_reset = new ResourcesPasswordReset($password_reset);
        $object->user = new ResourcesUser($user);

        return $this->handleResponse($object, __('notifications.create_user_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
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
            'tips_at_every_connection' => $request->tips_at_every_connection,
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

        if ($inputs['tips_at_every_connection'] != null) {
            $user->update([
                'tips_at_every_connection' => $inputs['tips_at_every_connection'],
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
    }

    // ==================================== CUSTOM METHODS ====================================
    /**
     * Find by "username"
     *
     * @param  string $username
     * @return \Illuminate\Http\Response
     */
    public function profile($username)
    {
        $user = User::where('username', $username)->first();

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
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

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->update([
                'api_token' => $token,
                'updated_at' => now(),
            ]);

            return $this->handleResponse(new ResourcesUser($user), __('notifications.find_user_success'));

        } else {
            $user = User::where('email', $inputs['username'])->orWhere('username', $inputs['username'])->first();

            if (!$user) {
                return $this->handleError($inputs['username'], __('auth.username'), 400);
            }

            if (!Hash::check($inputs['password'], $user->password)) {
                return $this->handleError($inputs['password'], __('auth.password'), 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $user->update([
                'api_token' => $token,
                'updated_at' => now(),
            ]);

            return $this->handleResponse(new ResourcesUser($user), __('notifications.find_user_success'));
        }
    }

    /**
     * Switch between user statuses.
     *
     * @param  $id
     * @param  $status_id
     * @return \Illuminate\Http\Response
     */
    public function switchStatus($id, $status_id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        $status_activated = Status::where('status_name->fr', 'Activé')->first();
        $status_disabled = Status::where('status_name->fr', 'Désactivé')->first();
        $status_blocked = Status::where('status_name->fr', 'Bloqué')->first();
        $status_deleted = Status::where('status_name->fr', 'Supprimé')->first();
        $status_unread = Status::where('status_name->fr', 'Non lue')->first();

        // The user account is activated
        if ($status_id == $status_activated->id) {
            // update "status_id" column
            $user->update([
                'status_id' => $status_activated->id,
                'updated_at' => now()
            ]);

            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'af' => 'Jou rekening is geaktiveer. Lees asseblief ons bepalings voordat jy begin.',
                    'de' => 'Dein Konto wurde aktiviert. Bitte lesen Sie unsere Bedingungen, bevor Sie beginnen.',
                    'ar' => 'تم تنشيط حسابك. يرجى قراءة شروطنا قبل أن تبدأ.',
                    'zh' => '您的帐号已经激活。 请在开始之前阅读我们的条款。',
                    'en' => 'Your account has been activated. Please read our terms before you start.',
                    'es' => 'Tu cuenta ha sido activada. Lea nuestros términos antes de comenzar.',
                    'fr' => 'Votre compte a été activé. Veuillez lire nos conditions avant de commencer.',
                    'it' => 'Il tuo account è stato attivato. Si prega di leggere i nostri termini prima di iniziare.',
                    'ja' => 'あなたのアカウントは有効化されました。 始める前に規約をお読みください。',
                    'nl' => 'Uw account is geactiveerd. Lees onze voorwaarden voordat u begint.',
                    'ru' => 'Ваша учетная запись активирована. Пожалуйста, прочтите наши условия, прежде чем начать.',
                    'sw' => 'Akaunti yako imewezeshwa. Tafadhali soma masharti yetu kabla ya kuanza.',
                    'tr' => 'Hesabınız aktive edildi. Lütfen başlamadan önce şartlarımızı okuyun.',
                    'cs' => 'Váš účet byl aktivován. Než začnete, přečtěte si prosím naše podmínky.'
                ],
                'color' => 'text-success',
                'icon' => 'bi bi-shield-lock',
                'image_url' => 'assets/img/logo-reverse.png',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);
        }

        // The user account is blocked
        if ($status_id == $status_blocked->id) {
            // update "status_id" column
            $user->update([
                'status_id' => $status_blocked->id,
                'updated_at' => now()
            ]);

            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'af' => 'Jou rekening is geblokkeer. Dit kan gebeur wanneer jy ons bepalings oortree of jou rekening gekap is.',
                    'de' => 'Ihr Konto wurde gesperrt. Dies kann passieren, wenn Sie gegen unsere Bedingungen verstoßen oder Ihr Konto gehackt wurde.',
                    'ar' => 'تم حظر حسابك. يمكن أن يحدث هذا عندما تنتهك شروطنا أو يتم اختراق حسابك.',
                    'zh' => '您的帐户已被冻结。 当您违反我们的条款或您的帐户被黑客入侵时，可能会发生这种情况。',
                    'en' => 'Your account has been blocked. This can happen when you violate our terms or your account has been hacked.',
                    'es' => 'Tu cuenta ha sido bloqueada. Esto puede suceder cuando viola nuestros términos o su cuenta ha sido pirateada.',
                    'fr' => 'Votre compte a été bloqué. Ceci peut arriver lorsque vous ne respectez pas nos conditions ou votre compte a été piraté.',
                    'it' => 'Il tuo account è stato bloccato. Ciò può accadere quando violi i nostri termini o il tuo account è stato violato.',
                    'ja' => 'あなたのアカウントはブロックされました。 これは、利用規約に違反した場合、またはアカウントがハッキングされた場合に発生する可能性があります。',
                    'nl' => 'Uw account is geblokkeerd. Dit kan gebeuren wanneer u onze voorwaarden schendt of uw account is gehackt.',
                    'ru' => 'Ваш аккаунт заблокирован. Это может произойти, если вы нарушите наши условия или ваша учетная запись была взломана.',
                    'sw' => 'Akaunti yako imezuiwa. Hili linaweza kutokea unapokiuka masharti yetu au akaunti yako imedukuliwa.',
                    'tr' => 'Hesabınız engellendi. Bu, şartlarımızı ihlal ettiğinizde veya hesabınız saldırıya uğradığında meydana gelebilir.',
                    'cs' => 'Váš účet byl zablokován. To se může stát, když porušíte naše podmínky nebo byl váš účet napaden hackery.'
                ],
                'color' => 'text-danger',
                'icon' => 'bi bi-lock-fill',
                'image_url' => 'assets/img/logo-reverse.png',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);
        }

        // The user account is disabled
        if ($status_id == $status_disabled->id) {
            // update "status_id" column
            $user->update([
                'status_id' => $status_disabled->id,
                'updated_at' => now()
            ]);

            Notification::create([
                'notification_url' => 'settings/account',
                'notification_content' => [
                    'af' => 'Jy het ons verlaat deur jou rekening te deaktiveer. Ons sien uit daarna om jou weer te sien. Om jou rekening te heraktiveer, klik hier.',
                    'de' => 'Sie haben uns verlassen, indem Sie Ihr Konto deaktiviert haben. Wir freuen uns auf ein Wiedersehen. Um Ihr Konto erneut zu aktivieren, klicken Sie hier.',
                    'ar' => 'لقد تركتنا عن طريق إلغاء تنشيط حسابك. اتمنى ان اراك مرة اخرى. لإعادة تنشيط حسابك، انقر هنا.',
                    'zh' => '您通过停用帐户离开了我们。 我们期待再次见到您。 要重新激活您的帐户，请单击此处。',
                    'en' => 'You left us by deactivating your account. We look forward to seeing you again. To reactivate your account, click here.',
                    'es' => 'Nos dejaste desactivando tu cuenta. Esperamos volver a verle de nuevo. Para reactivar su cuenta, haga clic aquí.',
                    'fr' => 'Vous nous avez quitté en désactivant votre compte. Nous avons hâte de vous revoir. Pour réactiver votre compte, cliquez ici.',
                    'it' => 'Ci hai lasciato disattivando il tuo account. Non vediamo l\'ora di rivederti. Per riattivare il tuo account, clicca qui.',
                    'ja' => 'アカウントを無効にして当社から離れました。 またお会いできるのを楽しみにしています。 アカウントを再アクティブ化するには、ここをクリックしてください。',
                    'nl' => 'U heeft ons verlaten door uw account te deactiveren. Wij kijken ernaar uit u weer te zien. Om uw account opnieuw te activeren, klikt u hier.',
                    'ru' => 'Вы покинули нас, деактивировав свою учетную запись. Мы с нетерпением ждем встречи с вами снова. Чтобы повторно активировать свою учетную запись, нажмите здесь.',
                    'sw' => 'Ulituacha kwa kuzima akaunti yako. Tunatazamia kukuona tena. Ili kuwezesha akaunti yako, bofya hapa.',
                    'tr' => 'Hesabınızı devre dışı bırakarak aramızdan ayrıldınız. Sizi tekrar görmeyi sabırsızlıkla bekliyoruz. Hesabınızı yeniden etkinleştirmek için burayı tıklayın.',
                    'cs' => 'Odešli jste od nás deaktivací svého účtu. Těšíme se na další shledání. Chcete-li znovu aktivovat svůj účet, klikněte sem.'
                ],
                'color' => 'text-danger',
                'icon' => 'bi bi-lock-fill',
                'image_url' => 'assets/img/logo-reverse.png',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);
        }

        // The user account is deleted
        if ($status_id == $status_deleted->id) {
            // update "status_id" column
            $user->update([
                'status_id' => $status_disabled->id,
                'updated_at' => now()
            ]);
        }

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Switch between user types.
     *
     * @param  $id
     * @param  $type_id
     * @return \Illuminate\Http\Response
     */
    public function switchType($id, $type_id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->handleError(__('notifications.find_user_404'));
        }

        /*
            HISTORY AND/OR NOTIFICATION MANAGEMENT
        */
        $type_ordinary = Type::where('type_name->fr', 'Membre ordinaire')->first();
        $type_premium = Type::where('type_name->fr', 'Membre premium')->first();
        $status_unread = Status::where('status_name->fr', 'Non lue')->first();

        if ($type_id == $type_ordinary->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $type_ordinary->id,
                'updated_at' => now()
            ]);
        }

        if ($type_id == $type_premium->id) {
            // update "type_id" column
            $user->update([
                'type_id' => $type_premium->id,
                'updated_at' => now()
            ]);

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
                'color' => 'text-success',
                'icon' => 'bi bi-shield-lock',
                'image_url' => 'assets/img/logo-reverse.png',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);
        }

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

        // if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['new_password']) == 0) {
        //     return $this->handleError($inputs['new_password'], __('validation.custom.new_password.incorrect'), 400);
        // }

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
		$image_url = 'images/users/' . $id . '/avatar/' . Str::random(50) . '.png';

		// Upload image
		Storage::url(Storage::disk('public')->put($image_url, base64_decode($image)));

		$user = User::find($id);

        $user->update([
            'avatar_url' => $image_url,
            'updated_at' => now()
        ]);

        return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
    }

    /**
     * Add user image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function addImage(Request $request, $id)
    {
        $inputs = [
            'user_id' => $request->user_id,
            'image_name' => $request->image_name,
            'image_64_recto' => $request->image_64_recto,
            'image_64_verso' => $request->image_64_verso
        ];

        if ($inputs['image_64_recto'] != null AND $inputs['image_64_verso'] != null) {
            // $extension = explode('/', explode(':', substr($inputs['image_64_recto'], 0, strpos($inputs['image_64_recto'], ';')))[1])[1];
            $replace_recto = substr($inputs['image_64_recto'], 0, strpos($inputs['image_64_recto'], ',') + 1);
            $replace_verso = substr($inputs['image_64_verso'], 0, strpos($inputs['image_64_verso'], ',') + 1);
            // Find substring from replace here eg: data:image/png;base64,
            $image_recto = str_replace($replace_recto, '', $inputs['image_64_recto']);
            $image_recto = str_replace(' ', '+', $image_recto);
            $image_verso = str_replace($replace_verso, '', $inputs['image_64_verso']);
            $image_verso = str_replace(' ', '+', $image_verso);

            // Clean "identity_data" directory
            $file = new Filesystem;
            $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $inputs['user_id'] . '/identity_data');
            // Create image URL
            $image_url_recto = 'images/users/' . $inputs['user_id'] . '/identity_data/' . Str::random(50) . '.png';
            $image_url_verso = 'images/users/' . $inputs['user_id'] . '/identity_data/' . Str::random(50) . '.png';

            // Upload image
            Storage::url(Storage::disk('public')->put($image_url_recto, base64_decode($image_recto)));
            Storage::url(Storage::disk('public')->put($image_url_verso, base64_decode($image_verso)));

            $user = User::find($id);

            $user->update([
                'id_card_type' => $inputs['image_name'],
                'id_card_recto' => $image_url_recto,
                'id_card_verso' => $image_url_verso,
                'updated_at' => now(),
            ]);

            return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));

        } else {
            if ($inputs['image_64_recto'] != null AND $inputs['image_64_verso'] == null) {
                // $extension = explode('/', explode(':', substr($inputs['image_64_recto'], 0, strpos($inputs['image_64_recto'], ';')))[1])[1];
                $replace_recto = substr($inputs['image_64_recto'], 0, strpos($inputs['image_64_recto'], ',') + 1);
                // Find substring from replace here eg: data:image/png;base64,
                $image_recto = str_replace($replace_recto, '', $inputs['image_64_recto']);
                $image_recto = str_replace(' ', '+', $image_recto);

                // Clean "identity_data" directory
                $file = new Filesystem;
                $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $inputs['user_id'] . '/identity_data');
                // Create image URL
                $image_url_recto = 'images/users/' . $inputs['user_id'] . '/identity_data/' . Str::random(50) . '.png';

                // Upload image
                Storage::url(Storage::disk('public')->put($image_url_recto, base64_decode($image_recto)));

                $user = User::find($id);

                $user->update([
                    'id_card_type' => $inputs['image_name'],
                    'id_card_recto' => $image_url_recto,
                    'updated_at' => now(),
                ]);

                return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
            }

            if ($inputs['image_64_recto'] == null AND $inputs['image_64_verso'] != null) {
                // $extension = explode('/', explode(':', substr($inputs['image_64_verso'], 0, strpos($inputs['image_64_verso'], ';')))[1])[1];
                $replace_verso = substr($inputs['image_64_verso'], 0, strpos($inputs['image_64_verso'], ',') + 1);
                // Find substring from replace here eg: data:image/png;base64,
                $image_verso = str_replace($replace_verso, '', $inputs['image_64_verso']);
                $image_verso = str_replace(' ', '+', $image_verso);

                // Clean "identity_data" directory
                $file = new Filesystem;
                $file->cleanDirectory($_SERVER['DOCUMENT_ROOT'] . '/public/storage/images/users/' . $inputs['user_id'] . '/identity_data');
                // Create image URL
                $image_url_verso = 'images/users/' . $inputs['user_id'] . '/identity_data/' . Str::random(50) . '.png';

                // Upload image
                Storage::url(Storage::disk('public')->put($image_url_verso, base64_decode($image_verso)));

                $user = User::find($id);

                $user->update([
                    'id_card_type' => $inputs['image_name'],
                    'id_card_verso' => $image_url_verso,
                    'updated_at' => now(),
                ]);

                return $this->handleResponse(new ResourcesUser($user), __('notifications.update_user_success'));
            }
        }
    }
}
