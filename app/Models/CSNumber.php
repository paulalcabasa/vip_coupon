<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;


class CSNumber extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_cs_numbers";
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function batchInsert($params){
        $this->insert($params);
    }

    public function getByDenomination($denominationId){
        return $this
            ->where('denomination_id', $denominationId)
            ->select('cs_number')
            ->get();
    }

    public function deleteByDenomination($denominationId){
        $this
            ->where('denomination_id', $denominationId)
            ->delete();
    }
}
