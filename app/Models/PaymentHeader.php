<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
