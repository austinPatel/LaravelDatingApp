<script type="text/javascript">
    $(document).ready(function() {

        let start_date = $("#start_date").val();
        let end_date = $("#end_date").val();

        let urlHit = "{{url('serviceProvider/booking')}}";

        if (start_date.length > 0 && end_date.length > 0) {
            urlHit = "{{url('serviceProvider/booking')}}" + "/" + btoa(start_date) + "/" + btoa(end_date);
        }
        $('#example1').dataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: urlHit,
                beforeSend: function() {
                    $('#example1').parent()
                        .find("#example1_processing").attr("ll", true)


                    // $('#example1').parent()
                    // .find("#example1_processing")
                    // .html(`<img src="{{url('public/website/images/loader.gif')}}" id="loaderIMG" />`);

                    // $("#loaderIMG").css({"position":"absolute","margin-left":"45%","margin-top":"35%","text-align":"center","top":"0","bottom":"0","z-index":"999"})
                },
                complete: function() {
                    //  $('#example1').parent()
                    // .find("#example1_processing")
                    // .html('')
                }
            },
            columns: [
                // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {
                    data: 'booking_random_id',
                    name: 'booking_random_id'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'booking_user_name',
                    name: 'booking_user_name'
                },
                {
                    data: 'booking_date',
                    name: 'booking_date'
                },
                {
                    data: 'start_booking_time',
                    name: 'start_booking_time'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]

        });
    });
</script>


<?php 
public function booking(Request $request){
    
    $serviceprovider = Auth::guard('serviceprovider')->user();
    $getReqStartDate = base64_decode($request->start_date);
    $getReqEndDate = base64_decode($request->end_date);
    
    if($getReqStartDate > $getReqEndDate){
        return redirect(route('sp.booking'))->with('error','End date should be greater than start date.');
    }
    if ($request->ajax()) {
        
        $start_date = base64_decode($request->start_date);
        $end_date = base64_decode($request->end_date);
        
        $column = "id";
        $asc_desc = $request->get("order")[0]['dir'];
        
        if($asc_desc == "asc"){
            $asc_desc = "desc";
        }else{
            $asc_desc = "asc";
}

$order =$request->get("order")[0]['column'];
if($order == 1){
    $column = "address";
}elseif ($order == 2) {
    $column = "booking_user_name";
}elseif ($order == 3) {
    $column = "booking_date";
}elseif ($order == 4) {
    $column = "start_booking_time";
}elseif ($order == 5) {
    $column = "phone_number";
}elseif ($order == 6) {
    $column = "category_name";
}else{
    $column = "booking_random_id";
}

if($start_date && $end_date){
    $data = BookingDetail::select("*",DB::raw('CONCAT(booking_details.first_name, " ", booking_details.last_name) AS booking_user_name'),DB::raw("TIME_FORMAT(booking_time, '%h:%i %p') as start_booking_time"),DB::raw("(select CONCAT(first_name, ' ',last_name) from users where id = booking_details.user_id) AS service_provider_name"),DB::raw("(select category_name from categories where id = booking_details.category_id) AS category_name"))
    ->whereDeletedAt(null)
    ->whereUserId($serviceprovider->id)
->whereDate('booking_date', '>=', $start_date)
->whereDate('booking_date','<=', $end_date) ->orderBy($column,$asc_desc);
}else{
    
    $data = BookingDetail::select("*",DB::raw('CONCAT(booking_details.first_name, " ", booking_details.last_name) AS booking_user_name'),DB::raw("TIME_FORMAT(booking_time, '%h:%i %p') as start_booking_time"),DB::raw("(select CONCAT(first_name, ' ',last_name) from users where id = booking_details.user_id) AS service_provider_name"),DB::raw("(select category_name from categories where id = booking_details.category_id) AS category_name"))
    ->whereUserId($serviceprovider->id)
    ->whereDeletedAt(null)
    ->orderBy($column,$asc_desc);
}
$total = $data->get()->count();

$search = $request->get("search")["value"];
$filter = $total;


if($search){
    $data = $data->where(function($query) use($search){
        $query->where('booking_random_id', 'Like', '%'. $search . '%')
        ->orWhere('address', 'Like', '%' . $search . '%')
        ->orWhere('phone_number', 'Like', '%' . $search . '%')
        ->orWhereHas('category', function($insideQuery) use ($search){
            return $insideQuery->where('category_name', 'like', '%'.$search.'%');
        })
        ->orWhere(DB::raw("TIME_FORMAT(booking_time, '%h:%i %p')"), 'Like', '%' . $search . '%')
        ->orWhere(DB::raw('CONCAT(booking_details.first_name, " ", booking_details.last_name)'), 'Like', '%' . $search . '%')
        ->orWhere('booking_date', 'Like', '%' . $search . '%');
    });
    
    
    $filter = $data->get()->count();
    
}

$data = $data->offset($request->start);
$data = $data->take($request->length);
$data = $data->get();


$start_from = $request->start;
if($start_from == 0){
    $start_from = 1;
    }
    if($start_from % 10 == 0){
        $start_from = $start_from + 1;
    }
    
    
    foreach ($data as $k => $booking_detail) {
        
        $booking_detail->phone_number = $booking_detail->country_code."". $booking_detail->phone_number;
        
        $view_user = url('serviceProvider/booking-details').'/'.base64_encode($booking_detail->id);
        
        $btn = '<a href="'.$view_user.'" class="btn btnlink" style="float: inherit;">View</a>';
        
        $booking_detail->action = $btn;
        $booking_detail->DT_RowIndex = $start_from++;
    }
    
    
    $return_data = [
        "data" => $data,
        "draw" => (int)$request->draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $filter,
        "input" => $request->all()
        ];
        return response()->json($return_data);
    }
    
    //$booking_management = $this->ServiceProviderBusinessModel()->bookingManagement();
    return view('service_provider.booking',compact('getReqStartDate','getReqEndDate'));
        ?>
    }