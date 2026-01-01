<?php

namespace App\Http\Controllers;

use App\Models\DistrictModel;
use App\Models\ProvinceModel;
use App\Models\VillageModel;
use App\Models\WardModel;
use Illuminate\Http\Request;

class GeometryController extends Controller
{
    public function get_geometry(Request $request){
        if ($request->provinceid){
            $data = $this->districts_list($request);
        }
        if ($request->districtid){
            $data =  $this->wards_list($request);
        }
        if ($request->wardid){
            $data =  $this->villages_list($request);
        }
        return ['status' => 'success', 'data' => $data];
    }
    //List of cities
    public function provinces_list(){
        $cities = ProvinceModel::orderBy('name','ASC')->get();
        return ['status' => 'success', 'data' => $cities];
    }

    //List of districts
    public function districts_list(Request $request){
        $districts = DistrictModel::select('districtid','name')
            ->where('provinceid', $request->provinceid)
            ->orderBy('name','ASC')
            ->get();
        return $districts;
    }

    //List of wards
    public function wards_list(Request $request){
        $wards = WardModel::select('wardid','name')
            ->where('districtid', $request->districtid)
            ->orderBy('name','ASC')
            ->get();
        return $wards;
    }

    //List of villages
    public function villages_list(Request $request){
        $wards = VillageModel::select('villageid','name')
            ->where('wardid', $request->wardid)
            ->orderBy('name','ASC')
            ->get();
        return $wards;
    }
}
