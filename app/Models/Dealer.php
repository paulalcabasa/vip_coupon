<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $table = "ipc_portal.dealers";
    protected $connection = "oracle";
    protected $primaryKey = 'id';
}
