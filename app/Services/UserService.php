<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use Logging;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Relation;
use App\Models\UserGroup;
use App\Models\UserCode;
use App\Models\Person;
use App\Models\UserPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use App\Jobs\SendUserMail;

class UserService
{
    public $user;

    /**
     * Return user list
     *
     * @return mixed
     */
    public function list($tenant_id = 0)
    {
        $query = \Querying::for(User::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery();

        if ($tenant_id) {
            $query->whereHas('tenantUsers', function ($tu_query) use ($tenant_id) {
                $tu_query->where('tenant_id', $tenant_id);
            });
        }

        return $query;
    }

    /**
     * Get person record via email param
     *
     * @param mixed $email
     * @return (false|string)[]|(true|string)[]
     */
    public function getPersonByEmail($email)
    {
        $person = Person::where('email', $email)->first();
        if (!$person) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        $user = $person->user;
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        $this->user = $user;
        return ['success' => true, 'message' => ''];
    }

    /**
     * Get person record via email param
     *
     * @param mixed $email
     * @return (false|string)[]|(true|string)[]
     */
    public function getUserByUsername($username)
    {
        $user = User::where('username', $username)->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username'];
        }
        $this->user = $user;
        return ['success' => true, 'message' => '', 'data' => $user];
    }

    /**
     * Set user
     *
     * @param mixed $where
     */
    public function setUser($where)
    {
        $this->user = User::where($where)->first();
    }

    /**
     * Save user data
     *
     * @param mixed $attributes
     */
    public function saveUser($attributes)
    {
        if (!isset($attributes['last_tenant_id'])) {
            $attributes['last_tenant_id'] = currentTenant('id');
        }
        if (empty($attributes['password'])) {
            $attributes['password'] = Str::random(12);
        }

        Logging::information('Create User', $attributes, 1, 1);

        $user = User::create($attributes);
        $this->user = $user;
        $tenant = Tenant::find($attributes['last_tenant_id']);
        $this->generateCode($tenant, 'new');

        return $user;
    }

    /**
     * Create user
     *
     */
    public function create()
    {
        $attributes = request([
            'id',
            'username',
            'person_id',
            'password',
            'password_confirmation',
            'remember_token',
            'enabled'
        ]);

        $user = $this->saveUser($attributes);

        if (request('groups')) {
            $groups = request('groups');
            $usergroupAttributes['group_id'] = $groups['value'];
            $usergroupAttributes['user_id'] = $user->id;
            UserGroup::create($usergroupAttributes);
        }

        $query = QueryBuilder::for(User::where('id', $user->id))
            ->allowedIncludes(User::$scopes);
        return $query;
    }

    /**
     * Send validation code
     *
     * @param string $type
     */
    public function sendValidationCode($tenant, $type)
    {
        $userCode = $this->user->userCodes()->active()->first();
        $data = [
            "title" => $this->user->person->title,
            "user_fullname" => $this->user->person->full_name,
            "code" => $userCode->code,
            "slug" => $tenant->identifier,
            "tenant" => $tenant->name,
            "service_url" => $tenant->service_url,
            "service_number" => $tenant->service_number,
            "service_email" => $tenant->service_email,
            "identifier" => $tenant->identifier,
            "username" => $this->user->username
        ];

        SendUserMail::dispatch(
            $data,
            $this->user,
            $type,
            $tenant->id
        );
    }

    /**
     * Get user
     *
     * @param mixed $id
     * @return mixed
     */
    public function show($id)
    {
        return new UserResource(User::find($id));
    }

    /**
     * Update user
     *
     * @param mixed $user
     */
    public function update(User $user, $attributes)
    {
        if (array_key_exists('current_password', $attributes)) {
            if (!Hash::check($attributes['current_password'], $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect.',
                ];
            }
        }

        if (isset($attributes['username']) && $user->username != $attributes['username']) {
            $user->username = $attributes['username'];
        }

        if (isset($attributes['password']) && $attributes['password'] != '') {
            $list = [
                strtolower(Str::before($user->username, '@')),
                strtolower($user->person->first_name),
                strtolower($user->person->last_name),
                strtolower(Str::before($user->person->email, '@'))
            ];

            $contains = Str::contains(strtolower($attributes['password']), $list);
            if ($contains) {
                return [
                    'success' => false,
                    'message' => 'Please provide a password that doesn’t contain your name or email.',
                ];
            }

            if (!preg_match('/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%^&*?]).*$/', $attributes['password'])) {
                return [
                    'success' => false,
                    'message' => 'Please provide a password that has at least one Uppercase and one lowercase letter, a symbol, a number and is at least 12 characters long.',
                ];
            }

            if ($this->reUsePassword($user, $attributes['password'])) {
                return [
                    'success' => false,
                    'message' => 'This password has been used recently. Please provide a new password.',
                ];
            }

            $user->password = $attributes['password'];
            $this->saveHistorycalPassword($user, $attributes['password']);
        }

        $log['old_values'] = User::where('id', $user->id)->get();

        $user->save();
        $log['new_values'] = $user;
        $log['changes'] = $user->getChanges();

        Logging::information('Update User', $log, 1, 1);

        if (request('groups')) {
            $groups = request('groups');
            $usergroupAttributes['group_id'] = $groups['value'];
            $usergroupAttributes['user_id'] = $user->id;

            UserGroup::where('user_id', $user->id)->whereHas('group', function ($query) {
                $query->where('tenant_id', currentTenant('id'));
            })->update($usergroupAttributes);
        }

        return [
            'success' => true,
            'message' => 'User updated successfully',
        ];
    }

    /**
     * Get user by code
     *
     * @param mixed $code
     * @return mixed
     */
    public function getUserByCode($code)
    {
        return UserCode::where('code', $code)->first();
    }

    /**
     * Generate code
     *
     * @param mixed|null $code
     * @param string $type
     */
    public function generateCode($tenant, string $type, $code = null, $user = null)
    {
        if ($code) {
            $this->user = $this->getUserByCode($code);
        }

        if (!$this->user) {
            if ($user == null) {
                return;
            }
            $this->user = $user;
        }

        $this->user->userCodes()
            ->active()
            ->update([
                'expiration' => now()->subHour()
            ]);
        $this->user->userCodes()->create();
        $this->sendValidationCode($tenant, $type);
    }

    /**
     * Check if username exists
     *
     * @param mixed $username
     * @return bool
     */
    public function ifUsernameExists($username)
    {
        $checkUsername = User::where('username', $username)
            ->pluck('username')->first();

        if (!empty($checkUsername)) {
            return true;
        }

        return false;
    }

    /**
     * Save historical password
     *
     * @param User $user
     * @param mixed $password
     */
    public function saveHistorycalPassword(User $user, $password)
    {
        UserPassword::create(['user_id' => $user->id, 'password' => hash('sha256', $password)]);
    }

    /**
     * Re-use password
     *
     * @param User $user
     * @param mixed $password
     * @return mixed
     */
    public function reUsePassword(User $user, $password)
    {
        return UserPassword::where([
            ['user_id', $user->id],
            ['password', hash('sha256', $password)]
        ])->exists();
    }

    /**
     * Get person
     *
     * @param mixed $person_id
     * @return mixed
     */
    public function getPerson($person_id)
    {
        return Person::find($person_id);
    }

    /**
     * Signup function for external portals
     *
     * @param array $params
     *
     * @return array
     */
    public function signup($params = []): array
    {
        $customerNumberExists = array_key_exists('customer_number', $params);
        $zipCodeExists = array_key_exists('zip_code', $params);
        $tenantId = $params['tenant_id'];

        $errorMessage = $responseData = null;
        $isSuccess = false;
        $code = 500;

        if ($customerNumberExists && $zipCodeExists) {
            $relation = Relation::where([
                'customer_number' => $params['customer_number'],
                'tenant_id' => $tenantId
            ]);
            if ($relation->exists()) {
                $relation = $relation->first();
                $provisioningAddress = $relation->provisioningAddress();
                if (!blank($provisioningAddress) && $provisioningAddress->zipcode == $params['zip_code']) {
                    $person = $relation->primaryPerson()->first();
                    $user = User::where([
                        'username' => $person->email,
                        'person_id' => $person->id,
                    ]);
                    if (!$user->exists()) {
                        $user = User::create([
                            'username' => $person->email,
                            'person_id' => $person->id,
                            'last_tenant_id' => $tenantId,
                            'password' => Str::random(12),
                            'enabled' => true,
                        ]);
                        $this->user = $user;
                        $this->generateCode(Tenant::find($tenantId), 'new');

                        $responseData = [
                            'user_id' => $user->id,
                            'email' => preg_replace('/((?<=^[A-z0-9-_.]{3})|(?<=@[A-z0-9]{3}))([A-z0-9]+)/', '***', $person->email)
                        ];
                        $isSuccess = true;
                        $code = 200;
                    } else {
                        $errorMessage = 'User already exists.';
                        $code = 400;
                    }
                } else {
                    $errorMessage = 'Zipcode not found.';
                    $code = 400;
                }
            } else {
                $errorMessage = 'Customer number not found.';
                $code = 400;
            }
        }
        return [
            'success' => $isSuccess,
            'errorMessage' => $errorMessage,
            'data' => $responseData,
            'httpCode' => $code
        ];
    }
}
