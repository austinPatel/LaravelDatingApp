<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Repositories\SubscriptionPlanRepository;

class SubscriptionPlanController extends ApiController
{
    public $subscriptionRepository;
    public $response;
    public function __construct(SubscriptionPlanRepository $subscriptionRepository)
    {
        $this->response = null;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @OA\Get(
     *  path="/subscription-plans",
     *  operationId="subscriptionPlans",
     *  tags={"User"},
     *  summary="Fetch subscription plans",
     *  description="subscription plans",
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
            $response = $this->subscriptionRepository->getSubscriptionPlans();
            $data = SubscriptionPlanResource::collection($response);

            return $this->sendResponse($data, 'Subscription Plan details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
