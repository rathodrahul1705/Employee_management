<?php

namespace App\Imports;

use App\Employee;
use App\User;
use App\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $lastinsertid = new User([
            "name" => $row[0].' '.$row[1],
            "email" => $row[2],
            "password" => Hash::make($row[8])
        ]);
        $lastinsertid->save();

        if($lastinsertid)
        {
            $employeeRole = Role::where('name', 'employee')->first();
            $lastinsertid->roles()->attach($employeeRole);
            $employeeDetails = [
                'user_id' => $lastinsertid->id, 
                'first_name' => $row[0], 
                'last_name' => $row[1],
                'sex' => $row[4], 
                'dob' => $row[3], 
                'join_date' => $row[5],
                'desg' => $row[6], 
                'department_id' =>1, 
                'photo'  => 'user.png'
            ];
        }
        return Employee::create($employeeDetails);       
    }
}
