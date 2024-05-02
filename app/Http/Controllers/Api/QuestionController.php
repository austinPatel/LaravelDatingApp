<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\AnswerRequest;
use Illuminate\Http\Request;
use App\Repositories\QuestionRepository;
use App\Http\Resources\QuestionResource;
use Illuminate\Support\Facades\Auth;
use Exception;

class QuestionController extends ApiController
{
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
     * @OA\Post(
     *  path="/questions",
     *  operationId="allBuildProfileQuestions",
     *  tags={"Build a profile"},
     *  summary="Get list of build profile questions",
     *  description="Returns list of build profile questions",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      description="Filter questions",
     *      @OA\JsonContent(
     *          required={"filter"},
     *          @OA\Property(
     *              property="filter",
     *              type="array",
     *              @OA\Items(
     *                  type="string"
     *              ),
     *          )
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

    public function index(Request $request)
    {
        try {
            $request = $request->all();
            $filter = $request ? $request['filter'] : [];
            $questions = $this->questionRepository->questions($filter);
            $data = QuestionResource::collection($questions);

            return $this->sendResponse($data, 'Question list');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/save-answers",
     *  operationId="saveBuildProfileAnswers",
     *  tags={"Build a profile"},
     *  summary="Save user profile answers",
     *  description="Save user profile answers",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      @OA\JsonContent(
     *          required={"questions"},
     *          @OA\Property(
     *              property="questions",
     *              type="array",
     *              @OA\Items(
     *                  type="object"
     *              ),
     *          )
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

    public function saveAnswers(AnswerRequest $request)
    {
        try {
            $input = $request->all();
            $questions = $input ? $input['questions'] : [];

            $this->questionRepository->saveAnswers($questions);
            return $this->sendResponse(null, 'Answers added successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
