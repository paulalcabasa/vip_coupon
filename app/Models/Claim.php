<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Claim extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_claims";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function getClaims($params){
        $sql = "SELECT cl.id,
                    vch.id voucher_no,
                    vch.voucher_code,
                    cp.id coupon_id,
                    cp.dealer_id,
                    vch.expiration_date,
                    to_char(cl.creation_date,'MM/DD/YYYY') claim_date,
                    dlr.account_name,
                    ct.name coupon_type,
                    cl.customer_name,
                    cl.cs_number,
                    cl.plate_no,
                    cl.service_invoice_number,
                    to_char(cl.service_date,'MM/DD/YYYY') service_date,
                    cl.service_date raw_service_date,
                    cl.amount
                FROM ipc.ipc_vpc_claims cl 
                    LEFT JOIN ipc.ipc_vpc_vouchers vch
                        ON vch.id = cl.voucher_id
                    LEFT JOIN ipc.ipc_vpc_coupons cp
                        ON cp.id = vch.coupon_id
                    LEFT JOIN ipc_portal.dealers dlr
                        ON dlr.id = cp.dealer_id
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id
                WHERE trunc(cl.creation_date) BETWEEN :start_date AND :end_date
                    AND cp.dealer_id = :dealer_id
                    AND cp.coupon_type_id = :coupon_type
                    AND nvl(cp.vehicle_type,1) = nvl(:vehicle_type,1)
                    AND cl.voucher_id NOT IN (
                        SELECT pl.voucher_id
                        FROM ipc.ipc_vpc_claim_headers  ph 
                            INNER JOIN ipc.ipc_vpc_claim_lines pl
                                ON ph.id = pl.claim_header_id
                        WHERE ph.status IN(1,2)
                    )";
        $query = DB::select($sql, $params);
        return $query;
    }
}
