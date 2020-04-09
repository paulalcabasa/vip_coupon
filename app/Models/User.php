<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class User extends Model
{
    //
    public function authenticate($credentials){
        $sql = "SELECT pt.employee_id,
                    emt.employee_no,
                    pt.password,
                    pit.first_name,
                    pit.middle_name,
                    pit.last_name
                FROM ipc_central.employee_masterfile_tab emt
                    INNER JOIN ipc_central.password_tab pt
                            ON emt.id = pt.employee_id
                    INNER JOIN ipc_central.personal_information_tab pit
                            ON pit.employee_id = emt.id
                WHERE 1 = 1
                    AND emt.status_id IN(1, 2, 3, 4)
                    AND emt.employee_no = :employee_no
                    AND pt.password = :password
                ORDER BY pt.employee_id DESC";
        $query = DB::select($sql,$credentials);
        return !empty($query) ? $query[0] : $query;
    }
}
