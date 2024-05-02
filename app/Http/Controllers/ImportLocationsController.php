<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\State;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ImportLocationsController extends Controller
{
    public function index(){
        return view('importlocations' . '.index');
    }
    public function importLocations(Request $request){
        // DB::beginTransaction();
        try{
            if ($request->hasFile('attachment')) {
                $attachmentDoc = $request->file('attachment');
                $extension = $attachmentDoc->getClientOriginalExtension();
                $valid_extension = array("csv");
                if (in_array(strtolower($extension), $valid_extension)) {
                    $n = 1;
                    $importLocationsData=array();
                    if (($handle = fopen($attachmentDoc, "r")) !== FALSE) {
                        while (($rowData = fgetcsv($handle, 10000, ",")) !== FALSE) {
                            if (is_array($rowData) && !empty($rowData) && $n != 1) {
                                $stateName=$rowData[2];
                                $accuracy= empty($rowData[6])? 0: $rowData[6];
                                // DB::enableQueryLog();
                                $locations = Locations::with(['state' => function ($q) use ($stateName) {
                                    $q->where('name','LIKE',"%{$stateName}%" );
                                }])->where(['postcode'=>$rowData[0],"suburb_name"=>$rowData[1]]);
                        
                                if(!$locations->exists()){
                                    
                                    $states=State::where('name','LIKE',"%{$stateName}%" )->first();
                                    // dd(DB::getQueryLog());
                                    // $importLocationsData[]=;
                                    // print_r($importLocationsData);
                                    // exit;
                                    Locations::create(array(
                                        "postcode"=>$rowData[0],
                                        "suburb_name"=>$rowData[1]?? null,
                                        "state_id"=>$states->id,
                                        "latitude"=>$rowData[4] ?? null,
                                        "longitude"=>$rowData[5]?? null,
                                        "accuracy"=> $accuracy,
                                    ));
                                }
                            }
                            $n++;
                        }
                        // dd($importLocationsData);
                    }
                    // foreach (array_chunk($importLocationsData,400) as $t)  
                    // {
                    //     // dd($t);
                    //     // DB::table('locations')->insert($t); 
                        
                    // }

                    
                }
            }    
            // DB::commit();
            Session::flash('message', 'Locations import successfully'); 
            // return view('importlocations' . '.index');
            return redirect('upload-location')->with(['success' => 'Locations import successfully']);
        }catch(Exception $e){
            // DB::rollBack();
            Session::flash('error', $e->getMessage()); 
            // return view('importlocations' . '.index');
            return redirect('upload-location')->with(['error' => $e->getMessage()]);
        }
    }
}
