<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Http\Resources\AnswerResource;
use App\Repositories\QuestionRepository;
use Exception;

class AnswerController extends ApiController
{
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @OA\Get(
     *  path="/fetch-user-answers",
     *  operationId="userProfileAnswers",
     *  tags={"Build a profile"},
     *  summary="Get user profile answers",
     *  description="Returns user profile answers",
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

    public function fetchUserAnswers()
    {
        try {
            $filter = [];
            $questions = $this->questionRepository->questions($filter);
            $data = AnswerResource::collection($questions);

            return $this->sendResponse($data, 'User answers');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
