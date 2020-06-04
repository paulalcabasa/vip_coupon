<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PaymentLine extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_payment_lines";
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id';

    public function insertLine($params){
        $this->insert($params);
    }

    public function batchInsert($params){
        $this->insert($params);
    }

    public function getClaimedVouchers($voucherCodes){
        $query = DB::connection('oracle')->table('ipc.ipc_vpc_vouchers vch')
                    ->leftJoin('ipc.ipc_vpc_payment_lines pl', 'pl.voucher_code','=','vch.voucher_code')
                    ->leftJoin('ipc.ipc_vpc_payment_headers ph', 'ph.id','=','pl.payment_header_id')
                    ->whereIn('vch.voucher_code', $voucherCodes)
                    ->whereNotIn('ph.status', [4]) // approved
                    ->select('vch.voucher_code')
                    ->get();
        $codes = collect($query)->pluck('voucher_code')->toArray();
        return $codes;
    }

    public function get($paymentHeaderId){
        $sql = "SELECT pl.id,
                        pl.voucher_code,
                        nvl(vch.cs_number,pl.cs_number) cs_number,
                        vch.amount,
                        vch.coupon_id coupon_no,
                        pl.service_invoice_no,
                        to_char(pl.service_date,'mm/dd/yyyy') service_date,
                        pl.dealer_code,
                        pl.customer_name
                FROM ipc.ipc_vpc_payment_lines pl
                    LEFT JOIN ipc.ipc_vpc_vouchers vch
                        ON pl.voucher_code = vch.voucher_code
                    
                WHERE pl.payment_header_id = :payment_header_id";
        $query = DB::select($sql, ['payment_header_id' => $paymentHeaderId]);
        return $query;
    }
}
