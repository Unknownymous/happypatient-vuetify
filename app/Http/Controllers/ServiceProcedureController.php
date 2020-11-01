<?php

namespace App\Http\Controllers;

use App\ServiceProcedure;
use App\Service;
use Illuminate\Http\Request;
use Validator;
use Auth;
use DB;
use App\ActivityLog;
use App\TemplateContent;

class ServiceProcedureController extends Controller
{
    
    public function index()
    {   
        $procedures = DB::table('services')
                               ->join('service_procedures', 'services.id', '=', 'service_procedures.serviceid')
                               ->select('service_procedures.id', 'services.service', 'service_procedures.procedure', 'service_procedures.price')
                               ->get();
        return response()->json(['procedures' => $procedures], 200);
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   
        return $request->all();

        $rules = [
            'service.required' => 'Please enter service',   
            'procedure.required' => 'Please add at least 1 service procedure on the table'
        ];

        $validator = Validator::make($request->all(),[
            'service' => 'required',
            'procedure' => 'required'
        ], $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 200);
        }

        $ctr = count($request->get('procedure'));
        $procedure = $request->get('procedure');
        $price = $request->get('price');
        $procedureIsNull = false;
        $priceIsNull = false;

        //Validate procedure and price if null
        for($x=0; $x < $ctr; $x++)
        {
            if(empty($procedure[$x]))
            {
                $procedureIsNull = true;
            }

            if(empty($price[$x]))
            {
                $priceIsNull = true;
            }

        }

        //validated if procedure or price is null
        if($procedureIsNull == true || $priceIsNull == true)
        {
            return response()->json(['procedures' => 'Procedure and Price is required'], 200);
        }

        for($x=0; $x < $ctr; $x++)
        {
            $service = new ServiceProcedure();
            $service->serviceid = $request->get('service');
            $service->procedure = $procedure[$x];
            $service->price = $price[$x];
            $service->save();


            //Template Content
            // $template_content = new TemplateContent();
            // $template_content->procedureid = $service->id;
            // $template_content->content = '';
            // $template_content->save();

            //Activity Log
            $activity_log = new ActivityLog();
            $activity_log->object_id = $service->id;
            $activity_log->table_name = 'service_procedures';
            $activity_log->description = 'Create Service Procedure';
            $activity_log->action = 'create';
            // $activity_log->userid = auth()->user()->id;
            $activity_log->save();

        }

        //PUSHER - send data/message if service procedure is created
        event(new EventNotification('create-procedure', 'service_procedures'));

        return response()->json(['success' => 'Record has successfully added'], 200);
    }

    
    public function show(ServiceProcedure $serviceProcedure)
    {
        //
    }

    
    public function edit(ServiceProcedure $serviceProcedure)
    {
        //
    }

    
    public function update(Request $request, ServiceProcedure $serviceProcedure)
    {
        //
    }

    
    public function destroy(ServiceProcedure $serviceProcedure)
    {
        //
    }
}
