<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MatchResource;
use App\Http\Requests\UserSubscriptionRequest;
use App\Repositories\UserSubscriptionRepository;
use App\Http\Resources\UserSubscriptionResource;
use App\Repositories\Admin\UserRepository as AdminUserRepository;

class UserSubscriptionController extends ApiController
{
    public $userSubscriptionRepository;
    public $response;
    public $userRepository;
    public function __construct(UserSubscriptionRepository $userSubscriptionRepository)
    {
        $this->response = null;
        $this->userSubscriptionRepository = $userSubscriptionRepository;
    }

    /**
     * @OA\Post(
     *  path="/user-subscription",
     *  operationId="userSubscription",
     *  tags={"User"},
     *  summary="User Subscription",
     *  description="User Subscription",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User Subscription",
     *      @OA\JsonContent(
     *         required={"subscription_plan_id", "send_invoice", "plan_manager_email"},
     *          @OA\Property(property="subscription_plan_id", type="number"),
     *          @OA\Property(property="plan_manager_name", type="string"),
     *          @OA\Property(property="plan_manager_email", type="string"),
     *          @OA\Property(property="send_invoice", type="number")
     *      ),
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

    public function saveUserSubscription(UserSubscriptionRequest $request)
    {
        try {
            if (empty($request->plan_manager_email) && empty($request->send_invoice)) {
                return $this->sendError("Plan manager email or Send Invoice field is required");
            }
            $userId = Auth::user()->id;
            $checkUserSubscriptionExist = $this->userSubscriptionRepository->checkUserSubscriptionExist($userId);
            if ($checkUserSubscriptionExist) {
                return $this->sendError("User has already subscribed the plan");
            }
            $userSubcription = $this->userSubscriptionRepository->saveUserSubscription($request->all());
            return $this->sendResponse($userSubcription, 'User Subscription created');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *  path="/user-subscription",
     *  operationId="userSubscriptionDetails",
     *  tags={"User"},
     *  summary="get user subscription",
     *  description="get user subscription",
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
    public function getUserSubscriptionDetails()
    {
        try {
            $userId = Auth::user()->id;
            $this->userRepository = new UserRepository;
            $user = $this->userRepository->getUserSubscriptionDetails($userId);
            $data = new UserSubscriptionResource($user);
            return $this->sendResponse($data, 'User Subscription Details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
