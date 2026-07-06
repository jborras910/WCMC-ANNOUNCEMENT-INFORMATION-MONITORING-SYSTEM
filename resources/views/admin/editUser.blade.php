@extends('admin.index')
@section('title', 'Edit User')

@section('content')

@if(session('success'))
<script>
  const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000 });
  Toast.fire({ icon:'success', title:"{{ session('success') }}" });
</script>
@elseif(session('error'))
<script>Swal.fire({ title:'Error!', text:"{{ session('error') }}", icon:'error' });</script>
@endif

<div class="page-card">
  <div class="page-card-header">
    <h5><i class="mdi mdi-account-edit mr-2 text-primary"></i>Edit User</h5>
    <div class="d-flex align-items-center">
      <span class="text-muted small mr-3">
        <i class="mdi mdi-calendar-outline mr-1"></i>
        Created {{ $user->created_at->timezone('Asia/Manila')->format('M j, Y') }}
      </span>
      <a href="{{ route('admin.users') }}" class="btn btn-secondary btn-sm">
        <i class="mdi mdi-arrow-left mr-1"></i>Back
      </a>
    </div>
  </div>

  <div class="page-card-body">
    <form method="post" action="{{ route('admin.updateUserPost', ['user' => $user]) }}">
      @csrf @method('put')

      <div class="form-section-title">Account Status &amp; Access</div>
      <div class="row">
        <div class="form-group col-md-4">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="Active"   {{ $user->status === 'Active'   ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ $user->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="form-group col-md-4">
          <label>Department</label>
          <input type="text" name="department" class="form-control" placeholder="e.g. IT, HR" value="{{ $user->department }}">
        </div>
        <div class="form-group col-md-4">
          <label>Role</label>
          <select name="role" class="form-control">
            <option value="faculty"      {{ $user->role === 'faculty'      ? 'selected' : '' }}>Faculty</option>
            <option value="admin"    {{ $user->role === 'admin'    ? 'selected' : '' }}>Admin</option>
            <option value="master_admin" {{ $user->role === 'master_admin' ? 'selected' : '' }}>Master Admin</option>
          </select>
        </div>
      </div>

      <div class="form-section-title mt-3">Personal Information</div>
      <div class="row">
        <div class="form-group col-md-4">
          <label>First Name <span class="text-danger">*</span></label>
          <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
        </div>
        <div class="form-group col-md-4">
          <label>Last Name <span class="text-danger">*</span></label>
          <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
        </div>
        <div class="form-group col-md-4">
          <label>Middle Name</label>
          <input type="text" name="middle_name" class="form-control" value="{{ $user->middle_name }}">
        </div>
        <div class="form-group col-md-6">
          <label>Email Address <span class="text-danger">*</span></label>
          <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="form-group col-md-6">
          <label>Username</label>
          <input type="text" name="username" class="form-control" value="{{ $user->username }}">
        </div>
      </div>

      <div class="form-section-title mt-3">Change Password</div>
      <div class="row">
        <div class="form-group col-md-6">
          <label>New Password <span class="text-muted" style="text-transform:none;font-weight:400;">(leave blank to keep current)</span></label>
          <input type="password" name="password" class="form-control" placeholder="••••••••">
        </div>
      </div>

      <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
        <button type="submit" class="btn btn-primary">
          <i class="mdi mdi-content-save"></i> Save Changes
        </button>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
          <i class="mdi mdi-close"></i> Cancel
        </a>
      </div>
    </form>
  </div>
</div>

@endsection
