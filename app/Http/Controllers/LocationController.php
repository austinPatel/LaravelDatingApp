<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\LocationRepository;
use Exception;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller
{
    public $locationRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct(LocationRepository $locationRepository)
    {
        $this->middleware('auth');
        $this->locationRepository = $locationRepository;
    }

    public function getSuburbs(Request $request){
        try {
            $data=$request->all();
            $suburbs = $this->locationRepository->getSuburbsSearch($data);   
            return response($suburbs);   
        } catch (Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
            return redirect()->back();

        }

    }

}
