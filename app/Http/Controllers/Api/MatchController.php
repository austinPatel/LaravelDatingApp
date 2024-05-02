<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotInterestedUserRequest;
use Illuminate\Http\Request;
use App\Repositories\MatchRepository;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Resources\MatchResource;

class MatchController extends ApiController
{
    public function __construct(MatchRepository $matchRepository)
    {
        $this->matchRepository = $matchRepository;
    }

    /**
     * @OA\Get(
     *  path="/view-recommendations",
     *  operationId="viewRecommendations",
     *  tags={"CliQ mode"},
     *  summary="Get list of recommendations",
     *  description="Returns list of recommendations",
     *  security={{ "api_key_security": {} }},
     *  @OA\Parameter(
     *      description="user latitude",
     *      in="query",
     *      name="user_lat",
     *      example="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *  ),
     *  @OA\Parameter(
     *      description="user longitude",
     *      in="query",
     *      name="user_long",
     *      example="",
     *         @OA\Schema(
     *             type="string"
     *         )
     *  ),
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
     *  @OA\Parameter(
     *      description="get the user recommendation details",
     *      in="query",
     *      name="user_id",
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

    public function index(Request $request)
    {
        try {
            $matches = $this->matchRepository->viewMatches($request->all());

            $response['totalRecord'] = $matches['totalRecord'];
            $response['totalPageCount'] = $matches['totalPageCount'];
            $response['currentPage'] = $matches['currentPage'];
            $response['items'] = MatchResource::collection($matches['data']);
            if ($matches['totalRecord'] == 0) {
                $response['message_flag'] = "User profile does not match";
            }


            return $this->sendResponse($response, 'View recommendations');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/not-interested-user",
     *  operationId="notInterested",
     *  tags={"CliQ mode"},
     *  summary="Not interested user request",
     *  description="Save not interested user request",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Not interested user request",
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

    public function notInterested(NotInterestedUserRequest $request)
    {
        try {
            $this->matchRepository->notInterested($request);
            return $this->sendResponse(null, 'Removed from your recommendation');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
