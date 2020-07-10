<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_claims";
    protected $primaryKey = 'id';
    public $timestamps = false;

    
}
