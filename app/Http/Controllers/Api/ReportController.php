<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportUserRequest;
use App\Repositories\ReportUserRepository;
use Exception;
use Illuminate\Http\Request;

class ReportController extends ApiController
{
    public function __construct(ReportUserRepository $reportUserRepository)
    {
        $this->reportUserRepository = $reportUserRepository;
    }

    /**
     * @OA\Post(
     *  path="/report-user",
     *  operationId="ReportUser",
     *  tags={"CliQ mode"},
     *  summary="Report user",
     *  description="Report user",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Report user",
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"user_id", "type", "objectional_type"},
     *              @OA\Property(property="user_id", type="integer", example="1"),
     *              @OA\Property(property="type", type="string", example="Chat"),
     *              @OA\Property(property="objectional_type", type="integer", example="1"),
     *              @OA\Property(property="reason", type="string", example="Some reason/comments"),
     *              @OA\Property(property="channel_url", type="string", example="https://someChannel.url"),
     *              @OA\Property(property="file", type="string", format="binary"),
     *              @OA\Property(property="file_url", type="string", example="https://something.url")
     *          )
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

    public function reportUser(ReportUserRequest $request)
    {
        try {
            $this->reportUserRepository->reportUser($request);
            return $this->sendResponse(null, 'Report submitted, Thank you!');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
