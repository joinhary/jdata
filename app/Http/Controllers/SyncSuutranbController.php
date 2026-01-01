<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncSuutranbController extends Controller
{
    public function sync(Request $request)
    {
        $data = $request->all();

        // Check if `ma_dong_bo` exists in the target database
        $existingRecord = DB::connection('mysql_target')
            ->table('suutranb')
            ->where('ma_dong_bo', $data['ma_dong_bo'])
            ->first();

        if ($existingRecord) {
            // Compare and update only if data is different
            $differences = array_diff_assoc($data, (array) $existingRecord);
            
            if (!empty($differences)) {
                DB::connection('mysql_target')
                    ->table('suutranb')
                    ->where('ma_dong_bo', $data['ma_dong_bo'])
                    ->update($data);

                return response()->json(['message' => 'Record updated'], 200);
            } else {
                return response()->json(['message' => 'No changes detected'], 200);
            }
        } else {
            // Insert new record if `ma_dong_bo` is not found
            DB::connection('mysql_target')
                ->table('suutranb')
                ->insert($data);

            return response()->json(['message' => 'New record inserted'], 201);
        }
    }
}
