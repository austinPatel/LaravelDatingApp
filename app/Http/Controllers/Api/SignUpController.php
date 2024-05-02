<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\ApiController;
use App\Models\User;

class SignUpController extends ApiController
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @OA\Post(
     *  path="/user/sign-up",
     *  operationId="SignUp",
     *  tags={"User"},
     *  summary="User SignUp",
     *  description="User registration",
     *  @OA\RequestBody(
     *      required=true,
     *      description="User Registration",
     *      @OA\JsonContent(
     *          required={"email","password","confirm_password"},
     *          @OA\Property(property="first_name", type="string", example="first name"),
     *          @OA\Property(property="last_name", type="string", example="last name"),
     *          @OA\Property(property="email", type="string", example="abc@test.com"),
     *          @OA\Property(property="password", type="string", example="password"),
     *          @OA\Property(property="confirm_password", type="string", example="password")
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

    public function register(UserRequest $request)
    {
        try {
            $user = $this->userRepository->requestUser($request->all());
            $user->status = User::USER_STATUS[$user->status];
            $token = $user->createToken('token')->accessToken;
            $success = [
                'token' => $token,
                'user' => $user
            ];
            return $this->sendResponse($success, 'User Created');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
