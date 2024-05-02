<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\ConnectionRequest;
use App\Http\Resources\ConnectionResource;
use App\Repositories\ConnectionRepository;
use App\Services\FCMPushNotificationService;
use App\Http\Resources\ConnectRequestResource;
use App\Models\UserDeviceToken;
use App\Repositories\UserDeviceTokenRepository;

class ConnectionController extends ApiController
{
    public $userDeviceTokenRepository;
    public $userRepository;
    public $connectionRepository;

    public function __construct(ConnectionRepository $connectionRepository)
    {
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * @OA\Post(
     *  path="/connect-request",
     *  operationId="userConnectRequest",
     *  tags={"CliQ mode"},
     *  summary="Connect request",
     *  description="Save user Connect request",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User connect request",
     *      @OA\JsonContent(
     *          required={"user_id"},
     *          @OA\Property(property="user_id", type="integer")
     *      )
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

    public function connectRequest(ConnectionRequest $request)
    {
        // User Connect Request
        try {
            $data = [
                "from_user_id" => Auth::user()->id,
                "to_user_id" => $request['user_id']
            ];
            $user = Auth::user();
            $this->userDeviceTokenRepository = new UserDeviceTokenRepository;
            $this->userRepository = new UserRepository;
            $toUserDeviceTokens = $this->userDeviceTokenRepository->getUserDeviceTokens($data['to_user_id']);
            // dd($toUserDeviceTokens);
            $toUser = $this->userRepository->getUserById($data['to_user_id']);

            $connect = $this->connectionRepository->connectRequest($data);
            $connectData = new ConnectRequestResource($connect);

            $fromname = Auth::user()->first_name . " " . Auth::user()->last_name;
            $toUserName = $toUser->first_name . " " . $toUser->last_name;
            $click_action = "FLUTTER_NOTIFICATION_CLICK";
            $title = null;
            $body = null;
            $notificationType = null;
            switch ($connectData->status) {
                case 'pending':
                    $title = "Connection Request Received: ".$fromname;
                    $body = "you’ve received new connection, kindly check the profile";
                    $notificationType = "connection_request";
                    break;
                case 'connect':
                    $title = "Connection Matched: ".$fromname;
                    $body =  "you've matched, please go and check";
                    $notificationType = "connection_match";
                    break;
            }
            if ($toUserDeviceTokens) {
                $userTokenlist = array();
                foreach ($toUserDeviceTokens as $tokenList) {
                    $userTokenlist[] = $tokenList->device_token;
                    if ($tokenList->device_type == "android") {
                        $userTokenlist[] = $tokenList->device_token;
                        $fcm_andriod_notification = [
                            // 'registration_ids' => array_values($userTokenlist),
                            'to' => $tokenList->device_token,
                            'data' => array(
                                'url' => url("api/view-recommendations?user_id=" . $user->id),
                                'click_action' => $click_action,
                                'notification_type' => $notificationType,
                                'user_id' => Auth::user()->id,
                                'title' => $title,
                                'body' => $body
                            )
                        ];
                        $response = FCMPushNotificationService::send($fcm_andriod_notification);
                    } elseif ($tokenList->device_type == "ios") {
                        $userTokenlist[] = $tokenList->device_token;
                        $fcm_ios_notification = [
                            // 'registration_ids' => array_values($userTokenlist),
                            'to' => $tokenList->device_token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body
                            ],
                            'data' => array(
                                'url' => url("api/view-recommendations?user_id=" . $user->id),
                                'click_action' => $click_action,
                                'notification_type' => $notificationType,
                                'user_id' => Auth::user()->id,
                                'title' => $title,
                                'body' => $body
                            )
                        ];
                        $response = FCMPushNotificationService::send($fcm_ios_notification);
                    }
                }
            }

            return $this->sendResponse($connectData, 'Connection request sent');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/declined-request",
     *  operationId="userDeclinedRequest",
     *  tags={"CliQ mode"},
     *  summary="Declined connection request",
     *  description="Declined connection request",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User declined connection request",
     *      @OA\JsonContent(
     *          required={"user_id"},
     *          @OA\Property(property="user_id", type="integer")
     *      )
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

    public function declinedRequest(ConnectionRequest $request)
    {
        try {
            $data = [
                "from_user_id" => Auth::user()->id,
                "to_user_id" => $request['user_id']
            ];
            $user = Auth::user();
            $this->userDeviceTokenRepository = new UserDeviceTokenRepository;
            $this->userRepository = new UserRepository;
            $toUserDeviceTokens = $this->userDeviceTokenRepository->getUserDeviceTokens($data['to_user_id']);
            $toUser = $this->userRepository->getUserById($data['to_user_id']);
            $connect = $this->connectionRepository->declinedRequest($data);
            $connectdata = new ConnectRequestResource($connect);
            $fromname = Auth::user()->first_name . " " . Auth::user()->last_name;
            $click_action = "FLUTTER_NOTIFICATION_CLICK";
            $title = null;
            $body = null;
            $notificationType = null;
            switch ($connectdata->status) {
                case 'declined':
                    $title = "Connection rejected: ".$fromname;
                    $body =  "Thanks! I’m looking for something else right now, good luck!";
                    $notificationType = "connection_declined";
                    break;
            }
            if ($toUserDeviceTokens) {
                $userTokenlist = array();
                foreach ($toUserDeviceTokens as $tokenList) {
                    $userTokenlist[] = $tokenList->device_token;
                    if ($tokenList->device_type == "android") {
                        $userTokenlist[] = $tokenList->device_token;
                        $fcm_andriod_notification = [
                            // 'registration_ids' => array_values($userTokenlist),
                            'to' => $tokenList->device_token,
                            'data' => array(
                                'url' => url("api/view-recommendations?user_id=" . $user->id),
                                'click_action' => $click_action,
                                'notification_type' => $notificationType,
                                'user_id' => Auth::user()->id,
                                'title' => $title,
                                'body' => $body
                            )
                        ];
                        $response = FCMPushNotificationService::send($fcm_andriod_notification);
                    } elseif ($tokenList->device_type == "ios") {
                        $userTokenlist[] = $tokenList->device_token;
                        $fcm_ios_notification = [
                            // 'registration_ids' => array_values($userTokenlist),
                            'to' => $tokenList->device_token,
                            'notification' => [
                                'title' => $title,
                                'body' => $body
                            ],
                            'data' => array(
                                'url' => url("api/view-recommendations?user_id=" . $user->id),
                                'click_action' => $click_action,
                                'notification_type' => $notificationType,
                                'user_id' => Auth::user()->id,
                                'title' => $title,
                                'body' => $body
                            )
                        ];
                        $response = FCMPushNotificationService::send($fcm_ios_notification);
                    }
                }
            }

            // if ($toUserDeviceTokens) {
            //     $userTokenlist = array();
            //     foreach ($toUserDeviceTokens as $tokenList) {
            //         $userTokenlist[] = $tokenList->device_token;
            //     }
            // }

            // $notification = array(
            //     'title' => $title,
            //     'body' => $body,
            //     'click_action' => url("api/view-recommendations?user_id=" . $user->id)
            // );

            // $fcmNotification = [
            //     'registration_ids' => array_values($userTokenlist), //multple token array
            //     // 'to' => $user_device_token, //single token
            //     // 'notification' => $notification,
            //     'data' => array(
            //         'url' => url("api/view-recommendations?user_id=" . $user->id),
            //         'click_action' => $click_action,
            //         'notification_type' => $notificationType,
            //         'user_id' => Auth::user()->id,
            //         'title' => $title,
            //         'body' => $body
            //     )
            // ];

            // $response = FCMPushNotificationService::send($fcmNotification);

            return $this->sendResponse($connectdata, 'Connection request declined');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *  path="/connections",
     *  operationId="connections",
     *  tags={"User"},
     *  summary="Get list of connections",
     *  description="Returns list of connections",
     *  security={{ "api_key_security": {} }},
     *  @OA\Parameter(
     *      description="page",
     *      in="query",
     *      name="page",
     *      example="1",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *  ),
     *  @OA\Parameter(
     *      description="Number of records show on per page",
     *      in="query",
     *      name="perPage",
     *      example="10",
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

    public function connections(Request $request)
    {
        // get user connections
        try {
            // user_connections
            $connections = $this->connectionRepository->connections($request->all());

            $response['totalRecord'] = $connections['totalRecord'];
            $response['totalPageCount'] = $connections['totalPageCount'];
            $response['currentPage'] = $connections['currentPage'];
            $response['items'] = ConnectionResource::collection($connections['data']);

            return $this->sendResponse($response, 'Connections');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/disconnect-user",
     *  operationId="disconnectUser",
     *  tags={"User"},
     *  summary="Disconnect user",
     *  description="Save user disconnect request",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"user_id"},
     *          @OA\Property(property="user_id", type="integer")
     *      )
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

    public function disconnectUser(ConnectionRequest $request)
    {
        try {
            $connect = $this->connectionRepository->disconnectUser($request);
            $data = new ConnectRequestResource($connect);

            return $this->sendResponse($data, 'Connection blocked successfully');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/add-favorite",
     *  operationId="addFavorite",
     *  tags={"User"},
     *  summary="Add favorite user",
     *  description="Save user favorite",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"user_id"},
     *          @OA\Property(property="user_id", type="integer")
     *      )
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

    public function addFavorite(ConnectionRequest $request)
    {
        try {
            $favorite = $this->connectionRepository->addFavorite($request);
            return $this->sendResponse(null, $favorite);
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
