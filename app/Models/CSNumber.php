<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;


class CSNumber extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_cs_numbers";
    protected $primaryKey = 'cs_number_id';
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    public function batch_insert($params){
        $this->insert($params);
    }
}
