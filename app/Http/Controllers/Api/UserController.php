<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Manager\UserManager;
use Illuminate\Http\Request;
use App\Services\TwilioSMSService;
use App\Http\Requests\ForgotRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\MatchResource;
use Illuminate\Support\Facades\Password;

class UserController extends ApiController
{
    public $userManager;
    public $response;
    public $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->response = null;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *  path="/mobile/sendOTP",
     *  operationId="mobileVerification",
     *  tags={"User"},
     *  summary="Mobile Verification ",
     *  description="Send verification code to registered mobile",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Mobile number with country code",
     *      @OA\JsonContent(
     *          required={"mobile"},
     *          @OA\Property(property="mobile", type="string", example="+910123456789")
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
    public function create(Request $request, TwilioSMSService $twilioSMSService)
    {
        //Twilio Verify services
        try {
            // Check mobile is exist or not
            $isExist = $this->userRepository->isMobileNumberExist($request->all());
            if ($isExist) {
                $this->twilioSMSService = $twilioSMSService;
                $this->response = $this->twilioSMSService->sendOTP($request->all());
                return $this->sendResponse($this->response, "Verification Code Sent");
            } else {
                return $this->sendError("Phone number is exist", $request->all());
            }
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/mobile/verify",
     *  operationId="mobileVerify",
     *  tags={"User"},
     *  summary="Verify mobile number by OTP code",
     *  description="Verify Mobile number",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Mobile number with country code",
     *      @OA\JsonContent(
     *          required={"mobile"},
     *          @OA\Property(property="mobile", type="string", example="+910123456789"),
     *          @OA\Property(property="otp_code", type="string", example="1223456")
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
    public function verify(Request $request, UserManager $userManager)
    {
        try {
            $this->userManager = $userManager;
            $this->response = $this->userManager->verify($request->all());
            if ($this->response['valid']) {
                // $this->userRepository->updateUser(['mobile' => $request->mobile]);
                return $this->sendResponse($this->response['user'], $this->response['message']);
            } else {
                return $this->sendError($this->response['message']);
            }
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *  path="/user/profile",
     *  operationId="userProfile",
     *  tags={"User"},
     *  summary="Fetch user details",
     *  description="User Details",
     *  security={{ "api_key_security": {} }},
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
    public function show()
    {
        try {
            $response = $this->userRepository->userProfile();
            $data = new MatchResource($response);

            return $this->sendResponse($data, 'User details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/update-user",
     *  operationId="updateUser",
     *  tags={"User"},
     *  summary="Update user detail",
     *  description="Update user detail",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      @OA\JsonContent(
     *          required={"mobile"},
     *          @OA\Property(property="ndis_number", type="number"),
     *          @OA\Property(property="term_conditions", type="boolean")
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

    public function updateUser(UserUpdateRequest $request)
    {
        try {
            $data = $request->all();
            $response = $this->userRepository->updateUser($data);
            return $this->sendResponse($response, 'User updated successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/user/forgot-password",
     *  operationId="forgotPassword",
     *  tags={"User"},
     *  summary="Forgot Password",
     *  description="Forgot Password",
     *  @OA\RequestBody(
     *      @OA\JsonContent(
     *          required={"email"},
     *          @OA\Property(property="email", type="string")
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

    public function forgot(ForgotRequest $request)
    {
        $email = $request->input('email');
        if (User::where('email', $email)->doesntExist()) {
            return $this->sendError('User does not exists');
        }
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status == Password::RESET_LINK_SENT) {
            return $this->sendResponse($email, "Reset Link has been sent to your email");
        }
    }

    public function sendCode(Request $request, TwilioSMSService $twilioSMSService)
    {

        try {
            $isNotExist = $this->userRepository->isMobileNumberExist($request->all());
            if ($isNotExist) {
                $this->twilioSMSService = $twilioSMSService;
                $this->response = $this->twilioSMSService->sendMessage($request->all());
                return $this->sendResponse($this->response, "Verification Code Sent");
            } else {
                return $this->sendError("Phone number is exist", $request->all());
            }
        } catch (Exception $exception) {
            return $this->sendError("Unable to create record: The number is unverified. Purchase a Twilio number to send messages to unverified numbers.");
        }
    }

    public function verifyCode(Request $request, UserManager $userManager)
    {
        try {
            $this->userManager = $userManager;
            $this->response = $this->userManager->verifySMSCode($request->all());
            if ($this->response['valid']) {
                return $this->sendResponse($this->response['user'], $this->response['message']);
            } else {
                return $this->sendError($this->response['message']);
            }
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/update-device-token",
     *  operationId="updateDeviceToken",
     *  tags={"User"},
     *  summary="Add or Update User device token",
     *  description="Add or Update User device token",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      @OA\JsonContent(
     *          required={"pushToken","deviceType"},
     *          @OA\Property(property="pushToken", type="string"),
     *          @OA\Property(property="deviceType", type="string"),
     *          @OA\Property(property="oldPushToken", type="string")
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


    public function updateUserDeviceToken(Request $request, UserManager $userManager)
    {
        try {
            $this->userManager = $userManager;
            $userUpdateToken = $this->userManager->updateUserDeviceToken($request->all());
            if ($userUpdateToken['success']) {
                $this->response['success'] = true;
                if ($userUpdateToken['insert']) {
                    $this->response['message'] = "User device token insert successfully";
                } else {
                    $this->response['message'] = "User device token update successfully";
                }
                return $this->sendResponse(['user_id' => Auth::user()->id], $this->response['message']);
            } else {
                $this->response['message'] = "Something went wrong";
                return $this->sendError($this->response['message']);
            }
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *  path="/get-user",
     *  operationId="getUserDetails",
     *  tags={"User"},
     *  summary="Get user details",
     *  description="Get user Details",
     *  security={{ "api_key_security": {} }},
     *  @OA\Parameter(
     *      description="get the user details",
     *      in="query",
     *      name="user_id",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
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

    public function getUser(Request $request){
        try {
            $reuestData= $request->all();
            if(isset($reuestData['user_id']) && !empty($reuestData['user_id'])){
                $userId=$reuestData['user_id'];
                $response = $this->userRepository->userProfile($userId);
                if(empty($response)){
                    return $this->sendError("User does not exist");
                }
            }else{
                return $this->sendError("User id required");
            }
            $data = new MatchResource($response);

            return $this->sendResponse($data, 'User details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
