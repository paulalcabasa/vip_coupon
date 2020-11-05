<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SerialNumber extends Model
{

    protected $connection = "oracle";
    protected $table = "IPC_VPC_SERIAL_MASTER";
    protected $primaryKey = 'inventory_item_id';
    public $timestamps = false;

    public function getInvalidCsNumbers($csNumbers){
        $isExist = DB::connection('oracle')->table('IPC_VPC_SERIAL_MASTER')
                    ->whereIn('serial_number', $csNumbers)
                    ->select('serial_number')
                    ->get()
                    ->toArray();
        $isExist = collect($isExist)->pluck('serial_number')->toArray();
        return array_diff($csNumbers, $isExist);
    }

    


}
