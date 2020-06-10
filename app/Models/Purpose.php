<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Service\PurposeService;
use DB;

class Purpose extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_purposes";
    protected $primaryKey = 'id';
    
    public function getActive(){
        $sql = "SELECT pr.id,
                        pr.purpose,
                        pr.require_cs_no_flag
                FROM ipc.ipc_vpc_purposes pr
                WHERE pr.status = 10";
        $query = DB::select($sql);
        return $query;
    }
    
}
