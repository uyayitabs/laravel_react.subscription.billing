<?php

namespace App\Http\Controllers\Portal;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\Tenant;
use Logging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\UserService;

class AuthController extends BaseController
{
    protected $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Login user and create token for portal
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
            'customer_email' => 'required|string|email',
            'customer_password' => 'required|string',
            'customer_remember_me' => 'required|boolean'
        ]);

        $credentials = [
            'username' => $request->customer_email,
            'password' => $request->customer_password,
        ];

        if (!Auth::attempt($credentials)) {
            Logging::information('Invalid username or password.', $credentials, 4);
            return response()->json([
                'message' => 'Invalid username or password.'
            ], 401);
        }

        $user = $request->user();

        if (!$user->isEndCustomer) {
            Logging::information('Permission denied.', $user, 4);
            return response()->json([
                'message' => 'Permission denied.'
            ], 403);
        }

        if (!$user->enabled) {
            $message = 'A problem has occurred while logging in.';
            $message .= 'Please try again, or contact customer support.';
            return response()->json([
                'message' => $message
            ], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->customer_remember_me) {
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
        $relation = $person->relationsPerson->relation;

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'tenant' => Tenant::find($user->last_tenant_id),
            'customer' => [
                'user_id' => $user->id,
                'customer_id' => $relation->id,
                'customer_number' => $relation->customer_number,
                'first_name' => $person->first_name,
                'last_name' => $person->last_name,
                'full_name' => $person->getAttribute('full_name')
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
        Logging::information('Successfully logged out.', $request, 1);
        $request->user()->token()->revoke();
        return response()->json([
            'status' => 201,
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Forgot password
     *
     * @return [string] message
     */
    public function forgotPassword($username)
    {
        $tenant = $this->getTenantFromRequest(request());
        if (!$tenant) {
            return $this->sendError('Tenant not found', [], 500);
        }
        $result = $this->userService->getUserByUsername($username);
        if (!$result['success']) {
            return $this->sendError($result['message'], [], 500);
        }
        $this->userService->generateCode($tenant, 'forgot');
        return $this->sendResult('', 'Mail sent.');
    }

    /**
     * Verify code from forgot password mail
     *
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verify($code)
    {
        $userCode = $this->userService->getUserByCode($code);
        if (!$userCode || !$userCode->user) {
            return $this->sendError('Code is invalid', [], 400);
        }
        if ($userCode->expiration < Carbon::now()) {
            return $this->sendError('Code has expired', [], 400);
        }
        return $this->sendResponse('Code is valid');
    }

    /**
     * Change user password after password reset
     *
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword($code, Request $request)
    {
        $userCode = $this->userService->getUserByCode($code);
        if (!$userCode || !$userCode->user) {
            return $this->sendError('Code is invalid', [], 400);
        }
        if ($userCode->expiration < Carbon::now()) {
            return $this->sendError('Code has expired', [], 400);
        }

        $result = $this->userService->update($userCode->user, $request->all());

        if (isset($result['success']) && !$result['success']) {
            return $result;
        }

        $userCode->delete();
        return $this->sendResponse(
            '',
            'Password reset successfully.'
        );
    }

    /**
     * Update old user password to a new password
     *
     * @param \App\Models\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        $result = $this->userService->update($user, [
            'password' => request('new_password'),
            'current_password' => request('old_password')
        ]);

        if (isset($result['success']) && !$result['success']) {
            return $this->sendError($result['message'], [], 400);
        }

        return $this->sendResponse(
            '',
            'Password changed successfully.'
        );
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

    /**
     * Signup
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request)
    {
        $tenant = $this->getTenantFromRequest($request);
        if (!isset($tenant)) {
            Logging::information('Tenant is required.', $request, 4, 1);
            return $this->sendResult(
                ["success" => false],
                'Tenant not found',
                400
            );
        }

        if (!isset($request->customer_number)) {
            Logging::information('Customer number is required.', $request, 4, 1);
            return $this->sendResult(
                [],
                'CustomerNumber is required',
                400
            );
        }
        if (!isset($request->zip_code)) {
            Logging::information('Zip code is required.', $request, 4);
            return $this->sendResult(
                [],
                'Zipcode is required',
                400
            );
        }

        $response = $this->userService->signup([
            'tenant_id' => $tenant->id,
            'customer_number' => $request->customer_number,
            'zip_code' => $request->zip_code,
        ]);

        if ($response['success'] == false) {
            return $this->sendError(
                $response['errorMessage'],
                'Registration failed',
                $response['httpCode']
            );
        }
        return $this->sendResult(
            [$response['data']],
            'Thank you for registering'
        );
    }

    private function getTenantFromRequest(Request $request)
    {
        $origin = parse_url($request->headers->get('origin'), PHP_URL_HOST);
        $origin = substr($origin, strpos($origin, '.') + 1);

        return Tenant::where('identifier', $origin)->first();
    }
}
