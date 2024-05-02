<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\InterestRequest;
use Illuminate\Http\Request;
use App\Repositories\InterestRepository;
use App\Http\Resources\InterestResource;
use Illuminate\Support\Facades\Auth;
use Exception;

class InterestController extends ApiController
{
    public function __construct(InterestRepository $interestRepository)
    {
        $this->interestRepository = $interestRepository;
    }

    /**
     * @OA\Post(
     *  path="/interests",
     *  operationId="allBuildProfileInterests",
     *  tags={"Build a profile"},
     *  summary="Get list of profile interests",
     *  description="Returns list of profile interests",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      description="Filter interests",
     *      @OA\JsonContent(
     *          required={"filter"},
     *          @OA\Property(
     *              property="filter",
     *              type="array",
     *              @OA\Items(
     *                  type="integer"
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
            $interests = $this->interestRepository->interests($filter);
            $data = InterestResource::collection($interests);

            return $this->sendResponse($data, 'Interest list');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/save-user-interest",
     *  operationId="saveUserInterests",
     *  tags={"Build a profile"},
     *  summary="Save user interest",
     *  description="Save use interest",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      @OA\JsonContent(
     *          required={"interests"},
     *          @OA\Property(
     *              property="interests",
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

    public function saveUserInterest(InterestRequest $request)
    {
        try {
            $input = $request->all();

            if (count($input['interests']) < 2) {
                return $this->sendError('You have to select atleast 2 interest.');
            }

            $interests = $input ? $input['interests'] : [];

            $this->interestRepository->saveUserInterest($interests);
            return $this->sendResponse(null, 'Interest added successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
