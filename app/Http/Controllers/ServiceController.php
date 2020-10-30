<?php

namespace App\Http\Controllers;

use App\Service;
use App\ActivityLog;
use Illuminate\Http\Request;
use Validator;
use Auth;

class ServiceController extends Controller
{
    
    public function index()
    {
        $services = Service::all();
        return response()->json(['services' => $services], 200);
    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   

        $rules = [
            'service.required' => 'Please enter service',
            'service.unique' => 'Service already exists'
        ];

        $validator = Validator::make($request->all(),[
            'service' => 'required|unique:services,service'
        ], $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 200);
        }

        $service = new Service();
        $service->service = $request->get('service');
        $service->status = $request->get('status');
        $service->save();

        //PUSHER - send data/message if service is created
        // event(new EventNotification('create-service', 'services'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $service->id;
        $activity_log->table_name = 'services';
        $activity_log->description = 'Create Service';
        $activity_log->action = 'create';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has successfully added', 'service' => $service], 200);
    }

    
    public function show(Service $service)
    {
        //
    }

    
    public function edit(Service $service)
    {
        //
    }

    
    public function update(Request $request, $serviceid)
    {

        $rules = [
            'service.required' => 'Please enter service',
            'service.unique' => 'Service already exists'
        ];

        $validator = Validator::make($request->all(),[
            'service' => 'required|unique:services,service,'.$serviceid
        ], $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 200);
        }

        $service = Service::find($serviceid);

        //if record is empty then display error page
        if(empty($service->id))
        {
            return abort(404, 'Not Found');
        }

        $service->service = $request->get('service');
        $service->status = $request->get('status');
        $service->save();

        //PUSHER - send data/message if service is updated
        // event(new EventNotification('edit-service', 'services'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $service->id;
        $activity_log->table_name = 'services';
        $activity_log->description = 'Update Service';
        $activity_log->action = 'update';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has been updated']);
    }


    public function delete(Request $request)
    {   

        $serviceid = $request->get('serviceid');
        $service = Service::find($serviceid);

        //if record is empty then display error page
        if(empty($service->id))
        {
            return abort(404, 'Not Found');
        }

        $service->delete();

        //PUSHER - send data/message if service is deleted
        // event(new EventNotification('delete-service', 'services'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $service->id;
        $activity_log->table_name = 'services';
        $activity_log->description = 'Delete Service';
        $activity_log->action = 'delete';
        // $activity_log->userid = auth()->user()->id;
        $activity_log->save();
        
        return response()->json(['success' => 'Record has been deleted'], 200);
    }
}
