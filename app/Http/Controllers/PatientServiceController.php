<?php

namespace App\Http\Controllers;

use App\PatientService;
use App\PatientServiceItem;
use App\Service;
use App\ServiceProcedure;
use App\Patient;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Auth;
use DB;
use App\ActivityLog;

class PatientServiceController extends Controller
{
 
    public function index()
    {   
        $patientservices =  DB::table('patient_services')
        //  ->join('patients', 'patient_services.patientid', '=', 'patients.id')
         ->select('patient_services.id', DB::raw("DATE_FORMAT(patient_services.docdate, '%m/%d/%Y') as docdate"), 'patient_services.or_number', 'patient_services.name', 'patient_services.cancelled')
         ->orderBy('patient_services.id', 'Asc')
         ->get();

        return response()->json(['patient_services' => $patientservices], 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        // return response()->json(Auth::user(), 200);

        $rules = [
            'patient.required' => 'Please select patient',
            'organization.required' => 'Please enter organization name',
            'docdate.required' => 'Please enter document date',
            'docdate.date' => 'Please enter a valid date',
            'service_procedures.required' => 'Please select at least 1 service'
        ];

        $valid_fields = [
            'docdate' => 'required|date',
            'service_procedures' => 'required'
        ];

        //if service type is individual or group
        if($request->get('type') == 'individual')
        {
            $valid_fields['patient'] = 'required';
        }
        else
        {
            $valid_fields['organization'] = 'required';
        }

        $validator = Validator::make($request->all(), $valid_fields, $rules);

        if($validator->fails())
        {
            return response()->json($validator->errors(), 200);
        }

        $patient = Patient::find($request->get('patient'));

        $name;
        //if record is empty then display error page
        if($request->get('type') == 'individual')
        {   
            $name = $patient->lastname . ', ' . $patient->firstname . ' ' . $patient->middlename;

            if(empty($patient->id))
            {
                return abort(404, 'Not Found');
            }
        }
        else
        {
            $name = $request->get('organization');
        }

        $patientservice  = new PatientService();
        $patientservice->type = $request->get('type');
        $patientservice->patientid = $request->get('patient');
        $patientservice->name = $name;
        $patientservice->docdate = Carbon::parse($request->get('docdate'))->format('y-m-d');
        $patientservice->bloodpressure = $request->get('bloodpressure');
        $patientservice->temperature = $request->get('temperature');
        $patientservice->weight = $request->get('weight');
        $patientservice->or_number = $request->get('or_number');
        $patientservice->note = $request->get('note');
        $patientservice->grand_total = $request->get('grand_total');
        $patientservice->status = 'O'; //status Open
        $patientservice->cancelled = 'N'; //cancelled (No)
        // $patientservice->save();

        $service_procedures = $request->get('service_procedures');

        foreach($service_procedures as $key => $item)
        {   
            // return response()->json($key, 200);
            $serviceitem = new PatientServiceItem();
            $serviceitem->psid = $patientservice->id;
            $serviceitem->serviceid = $item['service_id'];
            $serviceitem->procedureid = $item['procedure_id'];
            $serviceitem->status = "pending";
            $serviceitem->price = $item['price'];
            $serviceitem->discount = $item['discount'];
            $serviceitem->discount_amt = $item['discount_amt'];
            $serviceitem->total_amount = $item['total_amount'];
            // $serviceitem->save();
        }

        //PUSHER - send data/message if patient services is created
        // event(new EventNotification('create-patient-services', 'patient_services'));

        //Activity Log
        $activity_log = new ActivityLog();
        $activity_log->object_id = $patientservice->id;
        $activity_log->table_name = 'patient_services';
        $activity_log->description = 'Create Patient Services';
        $activity_log->action = 'create';
        $activity_log->userid = auth('api')->user()->id;
        // $activity_log->save();

        return response()->json(['success' => 'Record has successfully added'], 200);
    }


    public function show(PatientService $patientService)
    {
        //
    }


    public function edit($psid)
    {   
        $patientservice = PatientService::find($psid);

        //if record is empty then display error page
        // if(empty($patientservice->id))
        // {
        //     return abort(404, 'Not Found');
        // }

        $patientserviceitems =  DB::table('patient_service_items')
                 ->join('services', 'patient_service_items.serviceid', '=', 'services.id')
                 ->join('service_procedures', 'patient_service_items.procedureid', '=', 'service_procedures.id')
                 ->join('patient_services', 'patient_service_items.psid', '=', 'patient_services.id')
                 ->join('patients', 'patient_services.patientid', '=', 'patients.id')
                 ->select('patient_service_items.id', 'services.service', 'patient_service_items.price', 'patient_service_items.discount', 'service_procedures.procedure',
                          'patient_service_items.discount_amt', 'patient_service_items.total_amount', 'patient_service_items.status', 'patient_services.docdate', 'patient_services.type')
                //  ->whereIn('services.service', $services)
                 ->where('patient_service_items.psid', '=', $psid)
                 ->orderBy('patient_service_items.id', 'Asc')
                 ->get();
        
        return response()->json(['patientservice' => $patientservice, 'patientserviceitems' => $patientserviceitems], 200);

    }


    public function update(Request $request, PatientService $patientService)
    {
        //
    }


    public function destroy(PatientService $patientService)
    {
        //
    }
}
