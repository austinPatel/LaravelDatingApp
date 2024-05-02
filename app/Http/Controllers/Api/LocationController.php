<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Locations;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class LocationController extends ApiController
{
    public $locationRepository;
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }
    public function getStates(Request $request){
        try {
            $states = $this->locationRepository->getStates();            
            return $this->sendResponse($states, 'States Details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
    public function getSuburbs(Request $request){
        try {
            $data=$request->all();
            $suburbs = $this->locationRepository->getSuburbs($data);            
            return $this->sendResponse($suburbs, 'Suburbs Details');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

    }
    public function saveUserLocation(Request $request){
        try {
            $userId=Auth::user()->id;
            $data=$request->all();
            $data['user_id']=$userId;
            $suburbs = $this->locationRepository->saveUserLocation($data);            
            return $this->sendResponse($data, 'User location save successfully');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }

    }
}
