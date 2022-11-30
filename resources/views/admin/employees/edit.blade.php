@extends('layouts.app')        

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Update Employee</h1>
            </div>
            <!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Update Employee
                    </li>
                </ol>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 mx-auto">
                <div class="card card-primary">
                    <div class="card-header">
                        <h5 class="text-center mt-2">Add new employee</h5>
                    </div>
                    @include('messages.alerts')
                    <form action="{{ route('admin.employees.update', $employee->id)  }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                    <div class="card-body">
                        
                        <input type="hidden" name="user_id" value="{{$employee->user_id}}">
                            <fieldset>
                                <div class="form-group">
                                    <label for="">First Name</label>
                                    <input type="text" value ="{{$employee->first_name}}" name="first_name" value="{{ old('first_name') }}" class="form-control">
                                    @error('first_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Last Name</label>
                                    <input type="text"  value ="{{$employee->last_name}}" name="last_name" value="{{ old('last_name') }}" class="form-control">
                                    @error('last_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="text" name="email" value ="{{$employee->user->email}}" value="{{ old('email') }}" class="form-control">
                                    @error('email')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="text" value ="{{$employee->dob}}"  name="dob" id="dob" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Gender</label>
                                    <select name="sex" class="form-control">
                                        <option hidden disabled selected value> -- select an option -- </option>
                                        @foreach ($gender as $sex)
                                        <option value="{{$sex}}"  {{ $employee->sex == $sex ? "selected" : "" }}>{{$sex}}</option>
                                         @endforeach
                                    </select>
                                    @error('sex')
                                        <div class="text-danger">
                                            Please select an valid option
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="join_date">Join Date</label>
                                    <input type="text" value ="{{$employee->join_date}}" name="join_date" id="join_date" class="form-control">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">Designation</label>
                                        <select name="desg" class="form-control">
                                            <option hidden disabled selected value> -- select an option -- </option>
                                            @foreach ($desgs as $desg)
                                                <option value="{{ $desg }}" {{ $employee->desg == $desg ? "selected" : "" }}>
                                                    {{ $desg }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('desg')
                                        <div class="text-danger">
                                            Please select an valid option
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="">Department</label>
                                        <select name="department_id" class="form-control">
                                            <option hidden disabled selected value> -- select an option -- </option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? "selected" : "" }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department')
                                        <div class="text-danger">
                                            Please select a valid option
                                        </div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Employee profile</label>
                                    <input type="file" name="photo" class="form-control-file">
                                    @error('photo')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </fieldset>
                            
                        
                    </div>
                    <div class="card-footer text-center">
                        <button type="submit" class="btn btn-flat btn-primary" style="width: 40%; font-size:1.3rem">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->

@endsection

@section('extra-js')
<script>
    $().ready(function() {
        if('{{ old('dob') }}') {
            const dob = moment('{{ old('dob') }}', 'DD-MM-YYYY');
            const join_date = moment('{{ old('join_date') }}', 'DD-MM-YYYY');
            console.log('{{ old('dob') }}')
            $('#dob').daterangepicker({
                "startDate": dob,
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
            $('#join_date').daterangepicker({
                "startDate": join_date,
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
        } else {
            $('#dob').daterangepicker({
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
            $('#join_date').daterangepicker({
                "singleDatePicker": true,
                "showDropdowns": true,
                "locale": {
                    "format": "DD-MM-YYYY"
                }
            });
        }
        
    });
</script>
@endsection