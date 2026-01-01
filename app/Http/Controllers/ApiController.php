<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ApiController extends Controller
{
    public function checkMaDongBo(Request $request)
    {
        // Fetch 100 rows from table1 in db1
        $rowsDb1 = DB::connection('db1')->table('suutranb')
            ->select('ma_dong_bo')
            ->limit(100)
            ->get();

        // Extract `ma_dong_bo` values as an array
        $maDongBoDb1 = $rowsDb1->pluck('ma_dong_bo')->toArray();

        // Fetch matching rows from table2 in db2
        $rowsDb2 = DB::connection('db2')->table('suutranb')
            ->whereIn('ma_dong_bo', $maDongBoDb1)
            ->select('ma_dong_bo')
            ->get();

        // Extract `ma_dong_bo` from db2
        $maDongBoDb2 = $rowsDb2->pluck('ma_dong_bo')->toArray();

        // Identify missing `ma_dong_bo` values
        $missingMaDongBo = array_diff($maDongBoDb1, $maDongBoDb2);

        // Format response
        return response()->json([
            'db1_count' => count($rowsDb1),
            'db2_count' => count($rowsDb2),
            'missing_count' => count($missingMaDongBo),
            'missing_ma_dong_bo' => array_values($missingMaDongBo), // reindex array for JSON response
        ]);
    }
}

