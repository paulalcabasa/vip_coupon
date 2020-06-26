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

    public function get(){
        $sql = "SELECT pr.id,
                        pr.purpose,
                        lower(st.status) status,
                        st.id status_id,
                        pr.require_cs_no_flag
                FROM ipc.ipc_vpc_purposes pr
                    LEFT JOIN ipc.ipc_vpc_status st
                        ON st.id = pr.status
                WHERE 1 = 1";
        $query = DB::select($sql);
        return $query;
    }
    
}
