<?php
namespace App\Repositories;
use App\Models\Locations;
use App\Models\State;
use App\Models\UserLocation;
use Illuminate\Support\Facades\Auth;
Use Illuminate\Support\Facades\DB;
class LocationRepository{

    public function getStates(){
        return State::all();
    }
    public function getSuburbs(array $data){
        $state_id=$data['state_id'];
        $search_suburb= $data['search_text'] ?? null;
        // DB::enableQueryLog();
        $suburbs=array();
        $query = Locations::select('locations.id','locations.suburb_name','locations.postcode')->join('states', 'states.id', '=', 'locations.state_id');
        $query->where('locations.state_id',$state_id);
        // $query= Locations::with('locationState')->where('state_id',$state_id);
        if(!empty($search_suburb)){
            $query->where('locations.suburb_name', 'LIKE', "{$search_suburb}%");
            $query->orWhere('locations.postcode', 'LIKE', "{$search_suburb}%");
        }
        $query->orderBy('locations.suburb_name','asc');
        if($query->exists()){
            // dd(DB::getQueryLog());
            $suburbs=$query->get();
        }
        return $suburbs;
    }
    public function getSuburbsSearch(array $data){
        $state_id=$data['state_id'];
        
        $search_suburb= $data['search_text'] ?? null;
        // DB::enableQueryLog();
        $suburbs=array();
        $query = Locations::select('locations.id AS id',DB::raw('CONCAT(locations.suburb_name,",",states.code, ",", locations.postcode) AS value'))->join('states', 'states.id', '=', 'locations.state_id');
        if(!empty($state_id)){
            $query->where('locations.state_id',$state_id);

        }
        // $query= Locations::with('locationState')->where('state_id',$state_id);
        if(!empty($search_suburb)){
            $query->where('locations.suburb_name', 'LIKE', "{$search_suburb}%");
            $query->orWhere('locations.postcode', 'LIKE', "{$search_suburb}%");
        }
        $query->orderBy('locations.suburb_name','asc');
        if($query->exists()){
            // dd(DB::getQueryLog());
            $suburbs=$query->get();
        }
        return $suburbs;

    }
    public function saveUserLocation(array $data){
        // DB::enableQueryLog();
        $query= UserLocation::where('user_id',$data['user_id']);
        if(!$query->exists()){
            // dd(DB::getQueryLog());
            return $query->create($data);
        }else{
            // echo "Here\n";
            // dd(DB::getQueryLog());
            return $query->update($data);
        }
    }
    public function getAllStates(){
        $states= State::pluck('name','id');
        
        return $states;
    }
    public function getSuburbById($suburbId = null){
        
        $suburbs=array();
        $query = Locations::select('locations.id AS id',DB::raw('CONCAT(locations.suburb_name,",",states.code, ",", locations.postcode) AS suburb_name'))->join('states', 'states.id', '=', 'locations.state_id');
        if(!empty($suburbId)){
            $query->where('locations.id',$suburbId);
        }
        $query->orderBy('locations.suburb_name','asc');
        if($query->exists()){
            // dd(DB::getQueryLog());
            $suburbs=$query->first();
        }
        return $suburbs;

    }

}