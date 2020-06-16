<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.ipc_vpc_approvers";
    protected $primaryKey = 'id';
    public $timestamps = false;
}
