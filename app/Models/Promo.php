<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Promo extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_promos";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getActive(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        prm.coupon_expiry_date,
                        prm.effective_date_from,
                        prm.effective_date_to
                FROM ipc.ipc_vpc_promos prm
                WHERE SYSDATE BETWEEN prm.effective_date_from  AND prm.effective_date_to";
        $query = DB::select($sql);
        return $query;
    }

    public function get(){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        to_char(prm.coupon_expiry_date, 'MM/DD/YYYY') coupon_expiry_date,
                        to_char(prm.effective_date_from, 'MM/DD/YYYY') effective_date_from,
                        to_char(prm.effective_date_to, 'MM/DD/YYYY') effective_date_to,
                        CASE WHEN (SYSDATE BETWEEN prm.effective_date_from  AND prm.effective_date_TO) THEN 'active' ELSE 'inactive' END status
                FROM ipc.ipc_vpc_promos prm";
        $query = DB::select($sql);
        return $query;
    }

    public function getById($promo_id){
        $sql = "SELECT  prm.id,
                        prm.promo_name,
                        to_char(prm.coupon_expiry_date,'YYYY-MM-DD') coupon_expiry_date_orig,
                        to_char(prm.effective_date_from,'YYYY-MM-DD') effective_date_from_orig,
                        to_char(prm.effective_date_to,'YYYY-MM-DD') effective_date_to_orig,
                        trim(to_char(prm.coupon_expiry_date, 'Month')) || ' ' || to_char(prm.coupon_expiry_date, 'D, YYYY') coupon_expiry_date,
                        trim(to_char(prm.effective_date_from, 'Month')) || ' ' || to_char(prm.effective_date_from, 'D, YYYY') effective_date_from,
                        trim(to_char(prm.effective_date_to, 'Month')) || ' ' || to_char(prm.effective_date_to, 'D, YYYY') effective_date_to,
                        CASE WHEN (prm.effective_date_from >= sysdate AND prm.effective_date_to <= sysdate) THEN 'active' ELSE 'inactive' END status
                FROM ipc.ipc_vpc_promos prm
                WHERE prm.id = :promo_id";
        $query = DB::select($sql,['promo_id' => $promo_id]);
        
        return $query[0];
    }
}
