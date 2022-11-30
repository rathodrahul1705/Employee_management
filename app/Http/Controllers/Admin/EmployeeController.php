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
            $filename_ext = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filename_ext, PATHINFO_FILENAME);
            $ext = $request->file('photo')->getClientOriginalExtension();
            $filename_store = $filename.'_'.time().'.'.$ext;

            $image = $request->file('photo');
            $image_resize = Image::make($image->getRealPath());              
            $image_resize->resize(300, 300);
            $image_resize->save(public_path(DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'employee_photos'.DIRECTORY_SEPARATOR.$filename_store));
            $employeeDetails['photo'] = $filename_store;
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
            $filename_ext = $request->file('photo')->getClientOriginalName();
            $filename = pathinfo($filename_ext, PATHINFO_FILENAME);
            $ext = $request->file('photo')->getClientOriginalExtension();
            $filename_store = $filename.'_'.time().'.'.$ext;
            $image = $request->file('photo');
            $image_resize = Image::make($image->getRealPath());              
            $image_resize->resize(300, 300);
            $image_resize->save(public_path(DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'employee_photos'.DIRECTORY_SEPARATOR.$filename_store));
            $employeeDetails['photo'] = $filename_store;
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
        set_time_limit(3000); 

        /* connect to gmail with your credentials */
        $hostname = '{imap.gmail.com:993/ssl/imap/notls}INBOX';
        $username = 'sd9@consultlane.com'; 
        $password = 'Behonest123@';

        /* try to connect */
        $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
        // $read = imap_search($inbox, 'ALL');
        $read = imap_search($inbox, 'FROM "sd9@consultlane.com"');

        if($read) {
            rsort($read);
            foreach($read as $email_number) 
            {
                $overview = imap_fetch_overview($inbox,$email_number,0);
                $mailbody = imap_fetchbody($inbox,$email_number,2);
                $structure = imap_fetchstructure($inbox, $email_number);
                if(isset($structure->parts) && count($structure->parts)) 
                {
                    for($i = 0; $i < count($structure->parts); $i++) 
                    {
                        if($overview[$i]->subject == "Employee Details")
                        {
                            print_r($mailbody);exit;
                            $attachments[$i] = array(
                            'mailbody' => false,
                            );
                        }

                    }
                }

            }
        } 
        imap_close($inbox);
    }
}
