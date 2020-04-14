<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;


class CSNumber extends Model
{
    protected $connection = "oracle";
    protected $table = "apps.mtl_serial_numbers";

    public function getCSNumbers($csNumber)
    {
        $sql = "SELECT serial_number
                FROM mtl_serial_numbers
                WHERE 1 = 1
                AND c_attribute30 is null
                AND serial_number like '".$csNumber."%'
                AND rownum <= 10";
        
      //  $query = DB::select($sql);
        $query = DB::connection('oracle')->select($sql);

        return $query;
    }
}
