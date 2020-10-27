<?php

namespace App\Http\Controllers;
use Yajra\Address\Entities\City;
use Yajra\Address\Entities\Region;
use Yajra\Address\Entities\Province;
use Yajra\Address\Entities\Barangay;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function provinces()
    {
        $provinces = Province::all()->sortBy('name')->values();

        return response()->json(['provinces' => $provinces], 200);
    }

    public function cities($province_id)
    {   
        
        $cities = City::where('province_id', $province_id)->get()->sortBy('name')->values();

        return response()->json(['cities' => $cities], 200);
        
    }

    public function barangays($city_id)
    {
        $barangays = Barangay::where('city_id', $city_id)->get()->sortBy('name')->values();

        return response()->json(['barangays' => $barangays], 200);

    }
}
