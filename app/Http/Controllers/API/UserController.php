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
            'country_id' => $request->country_id,
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
        if ($password_reset->phone != null) {
            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'en' => 'Please confirm your phone number.',
                    'fr' => 'Veuillez confirmer votre adresse n° de téléphone.'
                ],
                'icon' => 'bi bi-telephone',
                'color' => 'text-success',
                'status_id' => is_null($status_unread) ? null : $status_unread->id,
                'user_id' => $user->id
            ]);
        }

        if ($password_reset->email != null) {
            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'en' => 'Please confirm your email address.',
                    'fr' => 'Veuillez confirmer votre adresse E-mail.'
                ],
                'icon' => 'bi bi-envelope',
                'color' => 'text-success',
                'status_id' => is_null($status_unread) ? null : $status_unread->id,
                'user_id' => $user->id
            ]);
        }

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
            'id' => $request->id,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'surname' => $request->surname,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'city' => $request->city,
            'address_1' => $request->address_1,
            'address_2' => $request->address_2,
            'p_o_box' => $request->p_o_box,
            'email' => $request->email,
            'phone' => $request->phone,
            'username' => $request->username,
            'password' => $request->password,
            'confirm_password' => $request->confirm_password,
            'belongs_to' => $request->belongs_to,
            'parental_code' => $request->parental_code,
            'api_token' => $request->api_token,
            'country_id' => $request->country_id,
            'status_id' => $request->status
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

            $password_reset_by_email = PasswordReset::where('email', $inputs['email'])->first();

            if ($password_reset_by_email == null) {
                $password_reset_by_phone = PasswordReset::where('phone', $current_user->phone)->first();

                if ($password_reset_by_phone != null) {
                    $password_reset_by_phone->update([
                        'email' => $inputs['email'],
                        'updated_at' => now(),
                    ]);

                } else {
                    PasswordReset::create([
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

            $password_reset_by_phone = PasswordReset::where('phone', $inputs['phone'])->first();

            if ($password_reset_by_phone == null) {
                $password_reset_by_email = PasswordReset::where('email', $inputs['email'])->first();

                if ($password_reset_by_email != null) {
                    $password_reset_by_email->update([
                        'phone' => $inputs['phone'],
                        'updated_at' => now(),
                    ]);

                } else {
                    PasswordReset::create([
                        'phone' => $inputs['phone'],
                    ]);
                }
            }
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

            $user->update([
                'username' => $inputs['username'],
                'updated_at' => now(),
            ]);
        }

        // If it is a child's account, generate a code for his parent if the code does not exist
        if ($inputs['belongs_to'] != null) {
            $random_string = Random::generate(7);

            $parent = User::find($inputs['belongs_to']);

            if (is_null($parent)) {
                return $this->handleError(__('notifications.find_parent_404'));
            }

            if ($parent->parental_code == null) {
                $parent->update([
                    'parental_code' => $random_string,
                    'updated_at' => now()
                ]);
            }

            $user->update([
                'belongs_to' => $inputs['belongs_to'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['password'] != null) {
            if ($inputs['confirm_password'] != $inputs['password'] OR $inputs['confirm_password'] == null) {
                return $this->handleError($inputs['confirm_password'], __('notifications.confirm_password_error'), 400);
            }

            // if (preg_match('#^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$#', $inputs['password']) == 0) {
            //     return $this->handleError($inputs['password'], __('miscellaneous.password.error'), 400);
            // }

            $password_reset_by_email = PasswordReset::where('email', $inputs['email'])->first();
            $password_reset_by_phone = PasswordReset::where('phone', $inputs['phone'])->first();
            $random_string = (string) random_int(1000000, 9999999);

            // If password_reset doesn't exist, create it.
            if ($password_reset_by_email == null AND $password_reset_by_phone == null) {
                if ($inputs['email'] != null AND $inputs['phone'] != null) {
                    PasswordReset::create([
                        'email' => $inputs['email'],
                        'phone' => $inputs['phone'],
                        'token' => $random_string,
                        'former_password' => $inputs['password'],
                    ]);

                } else {
                    if ($inputs['email'] != null) {
                        PasswordReset::create([
                            'email' => $inputs['email'],
                            'token' => $random_string,
                            'former_password' => $inputs['password']
                        ]);
                    }

                    if ($inputs['phone'] != null) {
                        PasswordReset::create([
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

        if ($inputs['country_id'] != null) {
            $user->update([
                'country_id' => $inputs['country_id'],
                'updated_at' => now(),
            ]);
        }

        if ($inputs['status_id'] != null) {
            $user->update([
                'status_id' => $inputs['status_id'],
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
        $status_ongoing = Status::where('status_name->fr', 'En cours')->first();
        $status_blocked = Status::where('status_name->fr', 'Bloqué')->first();
        $status_unread = Status::where('status_name->fr', 'Non lue')->first();

        // If it's a member whose accessing is accepted, send notification
        if ($status_id == $status_activated->id OR $status_id == $status_ongoing->id) {
            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'en' => 'Your account has been activated. Please read our terms before you start.',
                    'fr' => 'Votre compte a été activé. Veuillez lire nos conditions avant de commencer.',
                    'ln' => 'Compte na yo esili ko activer. Tosɛngi yo otánga mibeko na biso liboso ya kobanda.',
                ],
                    'icon' => 'bi bi-unlock-fill',
                'color' => 'text-info',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);

            if ($user->id_card_recto == null AND $user->id_card_verso == null) {
                // update "status_id" column
                $user->update([
                    'status_id' => $status_ongoing->id,
                    'updated_at' => now()
                ]);

            } else {
                // update "status_id" column
                $user->update([
                    'status_id' => $status_activated->id,
                    'updated_at' => now()
                ]);
            }
        }

        // If it's a member whose accessing is blocked, send notification
        if ($status_id == $status_blocked->id) {
            Notification::create([
                'notification_url' => 'about/terms_of_use',
                'notification_content' => [
                    'en' => 'Your account has been blocked. If you have any questions, contact us via the telephone number displayed on our website.',
                    'fr' => 'Votre compte a été bloqué. Si vous avez des questions, contactez-nous via le n° de téléphone affiché sur notre site web.',
                    'ln' => 'Compte na yo ekangami. Soki ozali na mituna, benga biso na nzela ya nimero ya telefone oyo emonisami na site Internet na biso.',
                ],
                'icon' => 'bi bi-lock-fill',
                'color' => 'text-danger',
                'status_id' => $status_unread->id,
                'user_id' => $user->id,
            ]);

            // update "status_id" column
            $user->update([
                'status_id' => $status_blocked->id,
                'updated_at' => now()
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
        $password_reset_by_email = PasswordReset::where('email', $user->email)->first();
        $password_reset_by_phone = PasswordReset::where('phone', $user->phone)->first();

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
