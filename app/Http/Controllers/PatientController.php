<?php

namespace App\Http\Controllers;

use App\Patient;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use App\ActivityLog;
use Auth;

class PatientController extends Controller
{
    
    public function index()
    {
        $patients = Patient::all();

        return response()->json(['patients' => $patients], 200);
    }

   
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {   

        $rules = [
            'lastname.required' => 'Please enter lastname',
            'firstname.required' => 'Please enter firstname',
            'birthdate.required' => 'Please enter birthdate',
            // 'weight.required' => 'Please enter weight',
            // 'weight.numeric' => 'Please enter a valid value',
            // 'weight.between' => 'Please enter a valid value',
            // 'landline.numeric' => 'Please enter a valid value',
            // 'mobile.numeric' => 'Please enter a valid value',
            'province.required' => 'Please select province',
            'city.required' => 'Please select city',
            'barangay.required' => 'Please select barangay',
        ];

        $valid_fields = [
            'lastname' => 'required',
            'firstname' => 'required',
            'birthdate' => 'required',
            // 'weight' => 'required|numeric|between:0,999.99',
            'province' => 'required',
            'city' => 'required',
            'barangay' => 'required',
        ];

        // if($request->get('landline'))
        // {
        //     $valid_fields['landline'] = 'numeric';
        // }
        // if($request->get('mobile'))
        // {
        //     $valid_fields['mobile'] = 'numeric';
        // }
        if($request->get('email'))
        {
            $valid_fields['email'] = 'email';
        }

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 401);
        }

        $patient = new Patient();
        $patient->lastname = $request->get('lastname');
        $patient->firstname = $request->get('firstname');
        $patient->middlename = $request->get('middlename');
        $patient->birthdate = Carbon::parse($request->get('birthdate'))->format('y-m-d');
        // $patient->weight = $request->get('weight');
        $patient->gender = $request->get('gender');
        $patient->age = $request->get('age');
        $patient->civilstatus = $request->get('civilstatus');
        $patient->landline = $request->get('landline');
        $patient->mobile = $request->get('mobile');
        $patient->email = $request->get('email');
        $patient->address = $request->get('address');
        $patient->province = $request->get('province');
        $patient->city = $request->get('city');
        $patient->barangay = $request->get('barangay');
        $patient->save();

        //PUSHER - send data/message if patients is created
        // event(new EventNotification('create-patient', 'patients'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $patient->id;
        $activity_log->table_name = 'patients';
        $activity_log->description = 'Create Patient';
        $activity_log->action = 'create';
        $activity_log->userid = auth('api')->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has successfully added', 'patientid' => $patient->id], 200);
    }

    
    public function show(Patient $patient)
    {
        //
    }

    
    public function edit($patientid)
    {
        $patient = Patient::find($patientid);

        //  //if record is empty then display error page
        //  if(empty($patient->id))
        //  {
        //      return abort(404, 'Not Found');
        //  }
 

        $birthdate = Carbon::parse($patient->birthdate)->format('Y-m-d');
        return response()->json(['patient' => $patient, 'birthdate' => $birthdate], 200);
    }

    
    public function update(Request $request, $patientid)
    {
        $rules = [
            'lastname.required' => 'Please enter lastname',
            'firstname.required' => 'Please enter firstname',
            'birthdate.required' => 'Please enter birthdate',
            'age.numeric' => 'Please enter a valid value',
            // 'weight.required' => 'Please enter weight',
            // 'weight.numeric' => 'Please enter a valid value',
            // 'weight.between' => 'Please enter a valid value',
            // 'landline.numeric' => 'Please enter a valid value',
            // 'mobile.numeric' => 'Please enter a valid value',
            'province.required' => 'Please select province',
            'city.required' => 'Please select city',
            'barangay.required' => 'Please select barangay',
        ];

        $valid_fields = [
            'lastname' => 'required',
            'firstname' => 'required',
            'birthdate' => 'required',
            'age' => 'sometimes|numeric',
            // 'weight' => 'required|numeric|between:0,999.99',
            'province' => 'required',
            'city' => 'required',
            'barangay' => 'required',
        ];

        // if($request->get('landline'))
        // {
        //     $valid_fields['landline'] = 'numeric';
        // }
        // if($request->get('mobile'))
        // {
        //     $valid_fields['mobile'] = 'numeric';
        // }
        if($request->get('email'))
        {
            $valid_fields['email'] = 'email';
        }

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 200);
        }

        $patient = Patient::find($patientid);

        //if record is empty then display error page
        // if(empty($patient->id))
        // {
        //     return abort(404, 'Not Found');
        // }

        $patient->lastname = $request->get('lastname');
        $patient->firstname = $request->get('firstname');
        $patient->middlename = $request->get('middlename');
        $patient->birthdate = Carbon::parse($request->get('birthdate'))->format('y-m-d');
        // $patient->weight = $request->get('weight');
        $patient->gender = $request->get('gender');
        $patient->age = $request->get('age');
        $patient->civilstatus = $request->get('civilstatus');
        $patient->landline = $request->get('landline');
        $patient->mobile = $request->get('mobile');
        $patient->email = $request->get('email');
        $patient->address = $request->get('address');
        $patient->province = $request->get('province');
        $patient->city = $request->get('city');
        $patient->barangay = $request->get('barangay');
        $patient->save();

        //PUSHER - send data/message if patients is updated
        // event(new EventNotification('edit-patient', 'patients'));


        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $patient->id;
        $activity_log->table_name = 'patients';
        $activity_log->description = 'Update Patient';
        $activity_log->action = 'create';
        $activity_log->userid = auth('api')->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has been updated']);
    }

    
    public function delete(Request $request)
    {   
        
        $patient = Patient::find($request->get('patientid'));

        //if record is empty then display error page
        if(empty($patient->id))
        {
            return abort(404, 'Not Found');
        }

        $patient->delete();

        //PUSHER - send data/message if patients is deleted
        // event(new EventNotification('delete-patient', 'patients'));

        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $patient->id;
        $activity_log->table_name = 'patients';
        $activity_log->description = 'Delete Patient';
        $activity_log->action = 'delete';
        $activity_log->userid = auth('api')->user()->id;
        $activity_log->save();

        return response()->json(['success' => 'Record has been deleted'], 200);
    }
}
