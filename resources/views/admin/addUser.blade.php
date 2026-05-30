@extends('admin.index')
@section('title', 'Add User')

@section('content')

@if(session('status'))
<script>Swal.fire({ title:'Error!', text:"{{ session('status') }}", icon:'error' });</script>
@endif
@error('email')
<script>Swal.fire({ title:'Invalid!', text:"{{ $message }}", icon:'error' });</script>
@enderror
@error('password')
<script>Swal.fire({ title:'Invalid!', text:"{{ $message }}", icon:'error' });</script>
@enderror

<div class="page-card">
  <div class="page-card-header">
    <h5><i class="mdi mdi-account-plus mr-2 text-primary"></i>Add New User</h5>
    <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">
      <i class="mdi mdi-arrow-left mr-1"></i>Back to Users
    </a>
  </div>

  <div class="page-card-body">
    <form method="post" action="{{ route('admin.addUserPost') }}">
      @csrf

      <div class="form-section-title">Account Information</div>
      <div class="row">
        <div class="form-group col-md-6">
          <label>Department</label>
          <input type="text" name="department" class="form-control" placeholder="e.g. IT, HR, Finance" value="{{ old('department') }}">
        </div>
        <div class="form-group col-md-6">
          <label>Role</label>
          <select name="role" class="form-control">
            <option value="faculty" {{ old('role') === 'faculty' ? 'selected' : '' }}>Faculty</option>
            <option value="master_admin" {{ old('role') === 'master_admin' ? 'selected' : '' }}>Master Admin</option>
          </select>
        </div>
      </div>

      <div class="form-section-title mt-3">Personal Information</div>
      <div class="row">
        <div class="form-group col-md-4">
          <label>First Name <span class="text-danger">*</span></label>
          <input type="text" name="first_name" class="form-control" placeholder="First name" value="{{ old('first_name') }}" required>
        </div>
        <div class="form-group col-md-4">
          <label>Last Name <span class="text-danger">*</span></label>
          <input type="text" name="last_name" class="form-control" placeholder="Last name" value="{{ old('last_name') }}" required>
        </div>
        <div class="form-group col-md-4">
          <label>Middle Name</label>
          <input type="text" name="middle_name" class="form-control" placeholder="Middle name" value="{{ old('middle_name') }}">
        </div>
        <div class="form-group col-md-12">
          <label>Email Address <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" placeholder="email@example.com" value="{{ old('email') }}" required>
        </div>
      </div>

      <div class="form-section-title mt-3">Login Credentials</div>
      <div class="row">
        <div class="form-group col-md-6">
          <label>Username <span class="text-danger">*</span></label>
          <input type="text" name="username" class="form-control" placeholder="username" value="{{ old('username') }}" required>
        </div>
        <div class="form-group col-md-6">
          <label>Password <span class="text-danger">*</span></label>
          <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">
          <i class="mdi mdi-check"></i> Create User
        </button>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
          <i class="mdi mdi-close"></i> Cancel
        </a>
      </div>
    </form>
  </div>
</div>

@endsection
