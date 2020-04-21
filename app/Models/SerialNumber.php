<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SerialNumber extends Model
{
    public function getInvalidCsNumbers($csNumbers){
        $isExist = DB::connection('oracle')->table('mtl_serial_numbers')
                    ->whereIn('serial_number', $csNumbers)
                    ->select('serial_number')
                    ->get()
                    ->toArray();
        $isExist = collect($isExist)->pluck('serial_number')->toArray();
        return array_diff($csNumbers, $isExist);
    }
}
