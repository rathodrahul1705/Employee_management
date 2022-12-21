<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Role;
use App\Employee;
use App\Department;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeExport;
use Webklex\PHPIMAP\ClientManager;
use Webklex\PHPIMAP\Client;


use function Ramsey\Uuid\v1;

class EmployeeController extends Controller
{
    public function index() {
        $data = [
            'employees' => Employee::all()
        ];
        return view('admin.employees.index')->with($data);
    }
    public function create() {
        $data = [
            'departments' => Department::all(),
            'desgs' => ['Manager', 'Assistant Manager', 'Deputy Manager', 'Clerk']
        ];
        return view('admin.employees.create')->with($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'sex' => 'required',
            'desg' => 'required',
            'department_id' => 'required',
            'email' => 'required|email',
            'photo' => 'image|nullable',
            'password' => 'required|confirmed|min:6'
        ]);
        $user = User::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $employeeRole = Role::where('name', 'employee')->first();
        $user->roles()->attach($employeeRole);
        $employeeDetails = [
            'user_id' => $user->id, 
            'first_name' => $request->first_name, 
            'last_name' => $request->last_name,
            'sex' => $request->sex, 
            'dob' => $request->dob, 
            'join_date' => $request->join_date,
            'desg' => $request->desg, 
            'department_id' => $request->department_id, 
            'photo'  => 'user.png'
        ];
        // Photo upload
        if ($request->hasFile('photo')) {
            $fileName = 'profile' . time() . '.' . $request->file('photo')->extension();
            $result = $request->file('photo')->move(public_path('img'), $fileName);
            $filepath = $fileName;
            $employeeDetails['photo'] = $filepath;

        }
        
        Employee::create($employeeDetails);
        
        $request->session()->flash('success', 'Employee has been successfully added');
        return back();
    }

    public function destroy($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        $user = User::findOrFail($employee->user_id);
        $employee->delete();
        $user->roles()->detach();
        $user->delete();
        request()->session()->flash('success', 'Employee record has been successfully deleted');
        return back();
    }

    public function employeeProfile($employee_id) {
        $employee = Employee::findOrFail($employee_id);
        // dd($employee);
        return view('admin.employees.profile')->with('employee', $employee);
    }

    public function employeeEdit($employee_id)
    {
        // $employee = Employee::findOrFail($employee_id);
        $employee = Employee::with('user', 'department')->where('id',$employee_id)->first();
        $departments = Department::all();
        $desgs = ['Manager', 'Assistant Manager', 'Deputy Manager', 'Clerk'];
        $gender = ['Male', 'Female'];
        return view('admin.employees.edit',compact('departments','desgs','gender','employee'));
    }

      public function update(Request $request,$employee_id) {
        // dd($request->all());
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'sex' => 'required',
            'desg' => 'required',
            'department_id' => 'required',
        ]);
        $employeeDetails = [
            'user_id' => $request->user_id, 
            'first_name' => $request->first_name, 
            'last_name' => $request->last_name,
            'sex' => $request->sex, 
            'dob' => $request->dob, 
            'join_date' => $request->join_date,
            'desg' => $request->desg, 
            'department_id' => $request->department_id, 
            'photo'  => 'user.png'
        ];
        // Photo upload
        if ($request->hasFile('photo')) {
            $fileName = 'profile' . time() . '.' . $request->file('photo')->extension();
            $result = $request->file('photo')->move(public_path('img'), $fileName);
            $filepath = $fileName;
            $employeeDetails['photo'] = $filepath;
        }
        Employee::find($employee_id)->update($employeeDetails);        
        $request->session()->flash('success', 'Employee has been successfully updated');
        return back();
    }

    public function fileInport(Request $request)
    {
        $importexcel =  Excel::import(new EmployeeImport, $request->file('file')->store('temp'));
        $request->session()->flash('success', 'Employee imported successfully');
        return back();
    }

    public function fileExport(Request $Request)
    {
        return Excel::download(new EmployeeExport, 'employee-collection.xlsx');
    }

    public function mail()
    {

          $scriptUrl = "https://script.google.com/macros/s/AKfycbxN5Lrghl1XX2nDzpAFIXqDuem6vo6AmChaddw_cXD4FcSd1YIQTXnmsJfzYMKS4xNt/exec";
          $limit  = 10; 
          $offset = 0; 

          $data = array(
             "action" => "inboxList",
             "limit"  => $limit,
             "offset" => $offset
          );

          $ch = curl_init($scriptUrl);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          $result = curl_exec($ch);
          $result = json_decode($result, true);
          $finalMailArray =[];
          if($result['status'] == 'success'){
            foreach($result['data'] as $inbox){
                 if($inbox['subject'] == "Employee Details")
                 {
                    $finalMailArray[] = $inbox;
                 }
            }

          }
          return view('admin.mail.index',compact('finalMailArray'));
    }

    public function mailbody(Request $request,$id)
    {
        $scriptUrl = "https://script.google.com/macros/s/AKfycbxN5Lrghl1XX2nDzpAFIXqDuem6vo6AmChaddw_cXD4FcSd1YIQTXnmsJfzYMKS4xNt/exec";
        $data = array(
        "action" => "inboxRead",
        "id"  => $id,
        );

        $ch = curl_init($scriptUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = json_decode($result, true);
        $data = $result['data'];
        return view('admin.mail.mailbody',compact('data'));
    }
}
