<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PaymentHeader extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_payment_headers";
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id';

    public function insertHeader($params){
        return $this->insertGetId($params);
    }

    public function getHeaders(){
        $sql = "SELECT ph.id,
                    st.status,
                    ph.creation_date,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status
                FROM ipc.ipc_vpc_payment_headers ph
                LEFT JOIN ipc.ipc_vpc_status st
                    ON ph.status = st.id
                LEFT JOIN apps.ipc_vpc_users_v usr
                    ON usr.user_id = ph.created_by
                    AND usr.user_source_id = ph.create_user_source";
        $query = DB::select($sql);
        return $query;
    }

    public function get($paymentHeaderId){
        $sql = "SELECT ph.id,
                    st.status,
                    ph.creation_date,
                    usr.first_name || ' ' || usr.last_name created_by,
                    TRIM(TO_CHAR(ph.creation_date, 'Month')) || ' ' ||  TO_CHAR(ph.creation_date,'DD, YYYY') date_created,
                    lower(st.status) status
                FROM ipc.ipc_vpc_payment_headers ph
                LEFT JOIN ipc.ipc_vpc_status st
                    ON ph.status = st.id
                LEFT JOIN apps.ipc_vpc_users_v usr
                    ON usr.user_id = ph.created_by
                    AND usr.user_source_id = ph.create_user_source
                WHERE ph.id = :payment_header_id";
        $query = DB::select($sql, ['payment_header_id' => $paymentHeaderId]);
        return !empty($query) ? $query[0] : $query;
    }
}
