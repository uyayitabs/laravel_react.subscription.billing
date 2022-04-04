<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\DataViewModels\TenantUser;
use Logging;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Services\UserService;

class AuthController extends BaseController
{
    protected $userService;
    protected $guzzleHttpService;

    public function __construct()
    {
        $this->userService = new UserService();
        // $this->guzzleHttpService = new GuzzleHttpService();
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password
        ]);
        $user->save();
        return response()->json([
            'status' => 201,
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token for grid
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        $credentials = request(['username', 'password']);

        if (!Auth::attempt($credentials)) {
            Logging::warning('Invalid username or password.', $credentials, 4, 1);
            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }
        $user = $request->user();

        if (!$user->isEmployee) {
            Logging::warning('Permission denied. User is not employee', $user, 4, 1);
            return response()->json([
                'message' => 'Permission denied.'
            ], 403);
        }

        if (!$user->enabled) {
            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }

        if ($user->tenantUsers()->where('children', '>', 0)->count() == 0) {
            Logging::warning('Permission denied. Is end user.', $credentials, 4, 1);
            return response()->json([
                'message' => 'Permission denied.'
            ], 403);
        }

        if (config('app.enable_password_expiration')) {
            if ($user->password_expiration == null || $user->password_expiration <= now()) {
                $this->userService->setUser([['id', '=', $user->id]]);
                $this->userService->generateCode(null, 'forgot');
                return response()->json([
                    'message' => 'Your password has expired. An email has been sent to reset your password.'
                ], 401);
            }
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = now()->addWeeks(1);
        }
        $token->save();

        if (!$user->last_tenant_id) {
            $user->tenantUsers()->where('children', '>', 0)->first();
            $tenant = $user->person->relationsPerson->relation->tenant;
            $user->last_tenant_id = $tenant->id;
        }

        $user->last_login = now();
        $user->save();

        // $this->guzzleHttpService->post('notice', [
        //     'channel' => 'notice_user_' . $user->id,
        //     'socket_id' => request()->header('socketid')
        // ]);

        $person = $user->person;

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'tenant' => Tenant::find($user->last_tenant_id),
            'person' => [
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'full_name' => $person->full_name,
            ],
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        Logging::information('Successfully logged out.', $request, 1, 1);
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 201,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        Logging::information('Logged in user.', $request, 1, 1);
        return response()->json([
            'status' => 200,
            'message' => 'Logged in user',
            'data' => $request->user()
        ]);
    }

    public function forgotPassword()
    {
        $username = request('email');
        $result = $this->userService->getUserByUsername($username);
        if (!$result['success']) {
            return $this->sendError($result['message'], [], 500);
        }

        $user = $result['data'];
        if (TenantUser::where([['user_id', $user->id], ['tenant_id', 1]])->exists()) {
            $this->userService->generateCode(Tenant::find(1), 'forgot');
        }

        return $this->sendError('Invalid Username', []);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $result = $this->userService->update($user, [
            'password' => request('new-password'),
            'current_password' => request('old-password')
        ]);

        if (isset($result['success']) && !$result['success']) {
            return $result;
        }

        return $this->sendResponse(
            '',
            'Password changed successfully.'
        );
    }

    /**
     * Reset the password with verification code sent by forgotPassword email
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $user = $this->userService->getUserByCode(request('code'));
        if (!$user) {
            return $this->sendError('Code is not valid', [], 500);
        }

        $result = $this->userService->update($user, $request->all());

        if (isset($result['success']) && !$result['success']) {
            return $result;
        }

        return $this->sendResponse(
            '',
            'Password reset successfully.'
        );
    }
}
