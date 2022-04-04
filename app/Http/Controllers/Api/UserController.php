<?php

namespace App\Http\Controllers\Api;

use App\Models\Relation;
use App\DataViewModels\TenantUser;
use App\Models\User;
use App\Http\Requests\UserApiRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends BaseController
{
    protected $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new UserService();
    }

    private function userValidate($pass = true, $un = false)
    {
        $messages = [
            'password.min' => 'The new :attribute is too short. Please use at least 12 characters.',
            'password.regex' => 'The :attribute  is not strong enough. The :attribute must contain at least 1 capital letter (A-Z), 1 lowercase letter (a-z), 1 digit (0-9) and 1 special character (! @ # $ % ^ & * ?)'
        ];

        $opt = [];

        if ($un) {
            $opt['username'] = 'required|string|min:3|max:191';
        }

        if ($pass) {
            $opt['password'] = 'required|min:12|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!@#$%^&*?]).*$/|confirmed';
        }

        return Validator::make(request()->all(), $opt, $messages);
    }

    /**
     * Return a listing of users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(currentTenant('id')),
            'User listing retrieved successfully'
        );
    }

    /**
     * Return a listing of users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexForTenant($tenant_id)
    {
        return $this->sendPaginate(
            $this->service->list($tenant_id),
            'User listing retrieved successfully'
        );
    }


    /**
     * Creates news users and add to a group
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $validator = $this->userValidate(false, true);

        $username = request('username');

        $validator->after(function ($validator) use ($username) {
            //check if username already exists
            if ($this->service->ifUsernameExists($username)) {
                $validator->errors()->add('username', $username . ' username already exists.');
            }

            $person = $this->service->getPerson(request('person_id'));
            //check if username already exists
            if ($person->user) {
                $validator->errors()->add('username', 'Person already has a user.');
            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendError(
                implode('<br />', $errors->all()),
                [],
                500
            );
        }

        return $this->sendSingleResult(
            $this->service->create(),
            'User created successfully.'
        );
    }

    /**
     * Return the specified user
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->sendResponse(
            $this->service->show($id),
            'User retrieved successfully.'
        );
    }

    /**
     * Update the specified user
     *
     * @param \App\Models\Models\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(User $user)
    {
        $validator = $this->userValidate(request()->has('password'), true);

        $username = request('username');
        $validator->after(function ($validator) use ($username, $user) {
            $list = [
                strtolower(Str::before($username, '@')),
                strtolower($user->person->first_name),
                strtolower($user->person->last_name),
                strtolower(Str::before($user->person->email, '@'))
            ];
            if (request()->has('password')) {
                $contains = Str::contains(strtolower(request('password')), $list);
                if ($contains) {
                    $validator->errors()->add('password', "Please provide a password that doesn’t contain your name or email.");
                }

                if ($this->service->reUsePassword($user, request('password'))) {
                    $validator->errors()->add('password', "This password has been used recently. Please provide a new password.");
                }
            }

            //check if username already exists and exclude the existing username
            if ($user->username != request('username')) {
                $validator->errors()->add('username', request('username') . ' already exists!');
            }
        });

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendError(
                $errors->first(),
                [],
                500
            );
        }

        $result = $this->service->update($user, request()->all());

        if (is_array($result) && isset($result['success']) && !$result['success']) {
            return $this->sendError($result['message'], [], 500);
        }

        $result['data'] = $this->service->show($user->id);

        return $this->sendResponse($result, 'User updated successfully.');
    }

    /**
     * Remove the specified user
     *
     * @param \App\Models\Models\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse($user, 'User deleted successfully.');
    }

    /**
     * Update the specified user password
     *
     * @param \App\Models\Models\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword($code)
    {
        $user = $this->service->getUserByCode($code);

        if (!$user) {
            return $this->sendError('Code is not valid', [], 500);
        }

        $validator = $this->userValidate();

        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendError(
                $errors->first(),
                [],
                500
            );
        }

        $this->service->update($user);

        return $this->sendSingleResult(
            $this->service->show($user->id),
            'Reset Password updated successfully.'
        );
    }

    public function validateCode($code)
    {
        return $this->sendResult('', '', $this->service->validateCode($code));
    }

    public function resendEmail()
    {
        $userId = request('user_id');
        $relationId = request('relation_id');
        if ($userId == null || $relationId == null) {
            return $this->sendError('Missing id of User or Relation.');
        }
        $tuser = TenantUser::where([['user_id', $userId], ['relation_id', $relationId]])->first();
        if ($tuser == null) {
            return $this->sendError('No user was found with this data.');
        }
        $this->service->setUser([['id', $userId]]);
        $this->service->generateCode($tuser->tenant, 'forgot');
        return $this->sendResult([], 'Mail has been successfully sent to user.');
    }
}
