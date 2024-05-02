<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CliQModeRequest;
use App\Http\Requests\LocationRequest;
use Illuminate\Http\Request;
use App\Repositories\PreferenceRepository;
use App\Http\Resources\PreferenceResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PreferenceRequest;
use App\Http\Resources\LocationResource;
use Exception;

class PreferenceController extends ApiController
{
    public function __construct(PreferenceRepository $preferenceRepository)
    {
        $this->preferenceRepository = $preferenceRepository;
    }

    /**
     * @OA\Get(
     *  path="/user-preference",
     *  operationId="userPreference",
     *  tags={"CliQ mode"},
     *  summary="Get list of user preference",
     *  description="Returns list of user preference",
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

    public function index()
    {
        try {
            $preferences = $this->preferenceRepository->userPreferences();
            $data = $preferences ? new PreferenceResource($preferences) : null;

            return $this->sendResponse($data, 'User preferences');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/save-user-preference",
     *  operationId="saveUserPreference",
     *  tags={"CliQ mode"},
     *  summary="Save user preference",
     *  description="Save user preference",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User Preference",
     *      @OA\JsonContent(
     *          required={"ageRange", "distance", "interested_in", "show_more_people"},
     *          @OA\Property(property="ageRange", type="string", example="18-30"),
     *          @OA\Property(property="distance", type="string", example="20"),
     *          @OA\Property(property="interested_in", type="string", example="Male"),
     *          @OA\Property(property="show_more_people", type="boolean", example="true")
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

    public function saveUserPreference(PreferenceRequest $request)
    {
        try {
            $explodeAgeRange = explode('-', $request['ageRange']);

            $data = $request->all();
            $data['min_age'] = $explodeAgeRange[0];
            $data['max_age'] = $explodeAgeRange[1];

            if ($data['min_age'] < 18 || $data['max_age'] > 70) {
                return $this->sendError("Age should be between 18 to 70");
            }

            $this->preferenceRepository->saveCliqMode($data);
            return $this->sendResponse(null, 'CliQ-mode added successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Get(
     *  path="/location",
     *  operationId="userLocation",
     *  tags={"Location"},
     *  summary="Get user location",
     *  description="Returns user location",
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

    public function location()
    {
        try {
            $location = $this->preferenceRepository->userPreferences();
            $data = $location ? new LocationResource($location) : null;

            return $this->sendResponse($data, 'User location');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/save-location",
     *  operationId="saveLocation",
     *  tags={"Location"},
     *  summary="Save user location",
     *  description="Save user location",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User location",
     *      @OA\JsonContent(
     *          required={"current_lat", "current_long"},
     *          @OA\Property(property="current_lat", type="string", example="21"),
     *          @OA\Property(property="current_long", type="string", example="2"),
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

    public function saveLocation(LocationRequest $request)
    {
        try {
            $data = [
                'user_id' => Auth::user()->id,
                'current_lat' => $request['current_lat'],
                'current_long' => $request['current_long'],
            ];

            $this->preferenceRepository->saveLocation($data);
            return $this->sendResponse(null, 'Location added successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/save-cliQ-mode",
     *  operationId="saveCliQMode",
     *  tags={"CliQ mode"},
     *  summary="Save user cliQ mode",
     *  description="Save user cliQ mode",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="User cliQ mode",
     *      @OA\JsonContent(
     *          required={"mode"},
     *          @OA\Property(property="mode", type="string", example="friendship")
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

    public function saveCliQMode(CliQModeRequest $request)
    {
        try {
            $this->preferenceRepository->saveCliqMode($request->all());
            return $this->sendResponse(null, 'CliQ-mode added successfully.');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
