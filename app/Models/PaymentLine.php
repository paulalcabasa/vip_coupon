<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
