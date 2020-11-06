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
                               ->select('service_procedures.id', 'service_procedures.serviceid', 'services.service', 'service_procedures.procedure', 'service_procedures.price')
                               ->get();
        return response()->json(['procedures' => $procedures], 200);
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   
        // return $request->get('procedures')[0]['procedure'];

        $rules = [
            'service.required' => 'Please enter service',   
            'procedures.required' => 'Please add at least 1 service procedure on the table'
        ];

        $validator = Validator::make($request->all(),[
            'service' => 'required',
            'procedures' => 'required'
        ], $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 401);
        }

        $ctr = count($request->get('procedures'));
        $procedure = $request->get('procedures');
        $price = $request->get('price');
        $procedureIsNull = false;
        $priceIsNull = false;

        //Validate procedure and price if null
        for($x=0; $x < $ctr; $x++)
        {
            if(empty($procedure[$x]['procedure']))
            {
                $procedureIsNull = true;
            }

            if(empty($procedure[$x]['price']))
            {
                $priceIsNull = true;
            }

        }

        //validated if procedure or price is null
        if($procedureIsNull == true || $priceIsNull == true)
        {
            return response()->json(['procedures' => 'Procedure and Price is required'], 401);
        }
        

        for($x=0; $x < $ctr; $x++)
        {
            $service = new ServiceProcedure();
            $service->serviceid = $request->get('service');
            $service->procedure = $procedure[$x]['procedure'];
            $service->price = $procedure[$x]['price'];
            $service->save();


            //Template Content
            $template_content = new TemplateContent();
            $template_content->procedureid = $service->id;
            $template_content->content = '';
            $template_content->save();

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
        // event(new EventNotification('create-procedure', 'service_procedures'));

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

    
    public function update(Request $request, $procedureid)
    {
        $rules = [
            'serviceid.required' => 'Service is required',   
            'procedure.required' => 'Procedure is required'
        ];

        $validator = Validator::make($request->all(),[
            'serviceid' => 'required',
            'procedure' => 'required'
        ], $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 401);
        }

        $procedure = ServiceProcedure::find($procedureid);
        $procedure->serviceid = $request->get('serviceid');
        $procedure->procedure = $request->get('procedure');
        $procedure->price = $request->get('price');
        $procedure->save();

        //PUSHER - send data/message if service procedure is updated
        // event(new EventNotification('edit-procedure', 'service_procedures'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $procedure->id;
        $activity_log->table_name = 'service_procedures';
        $activity_log->description = 'Update Service Procedure';
        $activity_log->action = 'update';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has been updated'], 200);
    }

    
    public function delete(Request $request)
    {
        $procedure_id = $request->get('procedureid');
        $procedure = ServiceProcedure::find($procedure_id);

        //if record is empty then display error page
        if(empty($procedure->id))
        {
            return abort(404, 'Not Found');
        }

        $procedure->delete();

        //PUSHER - send data/message if service procedure is deleted
        // event(new EventNotification('delete-procedure', 'service_procedures'));

        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $procedure->id;
        $activity_log->table_name = 'service_procedures';
        $activity_log->description = 'Delete Service Procedure';
        $activity_log->action = 'delete';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has been deleted'], 200);
    }

    public function content_create($procedure_id)
    {   
        $template_content = TemplateContent::where('procedureid', '=', $procedure_id)->first();

        //if record is empty then display error page
        if(empty($template_content->id))
        {
            return abort(404, 'Not Found');
        }


        return response()->json(['template_content' => $template_content], 200);
    }

    public function content_update(Request $request, $procedure_id)
    {   
        
        TemplateContent::where('procedureid', '=', $procedure_id)
                       ->update(['content' => $request->get('content')]);
        

        return response()->json(['success' => 'Record has been updated'], 200);
    }
}
