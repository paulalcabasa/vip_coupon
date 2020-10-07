<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DealerApprover extends Model
{
    protected $connection = "oracle";
    protected $table = "ipc.IPC_VPC_DEALER_APPROVERS";
    protected $primaryKey = 'id';
    protected $timestamp = false;
}
