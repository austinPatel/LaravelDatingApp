<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SignInController extends ApiController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *  path="/user/sign-in",
     *  operationId="SignIn",
     *  tags={"User"},
     *  summary="User Login",
     *  description="User login",
     *  @OA\RequestBody(
     *      required=true,
     *      description="User login with (email or mobile number) and password",
     *      @OA\JsonContent(
     *          required={"username","password"},
     *          @OA\Property(property="username", type="string", example="abc@test.com or <countrycode><mobilenumber>"),
     *          @OA\Property(property="password", type="string", example="password"),
     *      )
     *  ),
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required',
                'password' => 'required'
            ]);
            $user = $this->userRepository->checkUserByEmailOrPhone($request->username);
            // $user = $this->userRepository->checkUserByEmail($request->email);

            // $expire_trail_date = $user->created_at->addDays(10);
            // $is_trail_valid = $expire_trail_date >= Carbon::now() ? true : false;

            if (!$user) {
                return $this->sendError('Invalid Username or Password', ['username' => $request->all()], 401);
            }

            // if (!$is_trail_valid) {
            //     return $this->sendError('Your trail period is over.', ['username' => $request->all()], 401);
            // }

            if (!$user->hasVerifiedEmail()) {
                return $this->sendError("Please verify your email address by clicking the link in the email sent.", ['email' => $user->email], 401);
            }
            if (is_numeric($request->input('username'))) {
                $field = 'mobile';
            } elseif (filter_var($request->input('username'))) {
                $field = 'email';
            }
            $request->merge([$field => $request->input('username')]);

            $credentials = $request->only([$field, 'password']);
            if (!Auth::attempt($credentials)) {
                return $this->sendError('Invalid credentials', $credentials, 401);
            }
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error in Login');
            }
            $success['token'] = $user->createToken('authToken')->accessToken;
            $success['user_detail'] = new LoginResource($user);
            return $this->sendResponse($success, 'Successfully Login');
        } catch (ValidationException $error) {
            return $this->sendError($error->getMessage(), $error, 500);
        }
    }
}
