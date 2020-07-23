<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Report extends Model
{
    public function getVoucherSummary($params){
        $sql = "SELECT vch.id voucher_id,
                        lpad(vch.control_number,6,0) voucher_no,
                        vch.voucher_code,
                        vch.amount,
                        cp.id coupon_id,
                        dlr.account_name,
                        coupon_status.status coupon_status,
                        usr.first_name || ' ' || usr.last_name coupon_requested_by,
                        -- customer claims
                        customer_claim.customer_name,
                        customer_claim.service_invoice_number,
                        to_char(vch.creation_date,'MM/DD/YYYY') creation_date,
                        to_char(cp.creation_date,'MM/DD/YYYY') coupon_request_date,
                        to_char(customer_claim.service_date,'MM/DD/YYYY') service_date,
                        to_char(vch.expiration_date,'MM/DD/YYYY') expiration_date,
                        customer_claim.amount claimed_amount,
                        payment.amount paid_amount,
                        ct.name coupon_type,
                        cp.vehicle_type
                FROM ipc.ipc_vpc_vouchers vch
                    LEFT JOIN ipc.ipc_vpc_coupons cp
                        ON cp.id = vch.coupon_id
                    LEFT JOIN ipc_portal.dealers dlr
                        ON dlr.id = cp.dealer_id
                    LEFT JOIN ipc.ipc_vpc_status coupon_status
                        ON coupon_status.id = cp.status
                    LEFT JOIN ipc_vpc_users_v usr
                        ON usr.user_id = cp.created_by
                        AND usr.user_source_id = cp.create_user_source
                    LEFT JOIN ipc.ipc_vpc_claims customer_claim
                        ON customer_claim.voucher_id = vch.id
                    LEFT JOIN ipc.ipc_vpc_coupon_types ct
                        ON ct.id = cp.coupon_type_id 
                    LEFT JOIN 
                    (
                        SELECT ch.id,
                                    cl.voucher_code,
                                    cl.voucher_id,
                                    cl.amount,
                                    cl.cs_number,
                                    cl.claim_id
                        FROM  ipc.ipc_vpc_claim_headers ch
                            INNER JOIN ipc.ipc_vpc_claim_lines cl
                                ON cl.claim_header_id = ch.id
                        WHERE ch.status = 2
                    ) payment
                        ON payment.voucher_id = vch.id
                WHERE trunc(vch.creation_date) BETWEEN :voucher_start_date AND :voucher_end_date
                    AND nvl(cp.vehicle_type, 1) = nvl(:vehicle_type, 1)
                    AND cp.coupon_type_id = :coupon_type
                    AND CASE WHEN :dealer_id IS NOT NULL THEN cp.dealer_id ELSE 1 END  = nvl(:dealer_id,1)";
        $query = DB::select($sql, $params);
        return $query;
    }
}
