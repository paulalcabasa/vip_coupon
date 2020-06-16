<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Promo extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_promos";
    protected $primaryKey = 'id';

    public function getActive(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.coupon_expiry_date,
                        prm.effective_date_from,
                        prm.effective_date_to
                FROM ipc.ipc_vpc_promos prm
                WHERE prm.effective_date_from <= sysdate
                    AND prm.effective_date_to >= sysdate";
        $query = DB::select($sql);
        return $query;
    }
}
