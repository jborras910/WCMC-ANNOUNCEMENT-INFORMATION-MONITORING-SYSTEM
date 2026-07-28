@extends('admin.index')
@section('title', 'Edit User')

@section('content')

<style>
  .option-card {
    display: flex; align-items: flex-start; gap: 10px;
    border: 1.5px solid var(--border); border-radius: 8px; padding: 10px 12px;
    cursor: pointer; transition: border-color .15s ease, background .15s ease; background: #fff;
  }
  .option-card:hover { border-color: var(--primary); }
  .option-card.is-checked { border-color: var(--primary); background: rgba(37,99,235,0.05); }
  .option-card input { margin-top: 3px; flex-shrink: 0; }
  .option-card .oc-icon {
    width: 32px; height: 32px; border-radius: 7px; background: #eef2ff; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
  }
  .option-card .oc-icon i { font-size: 16px; color: var(--primary); }
  .option-card .oc-title { font-weight: 600; font-size: 13px; color: var(--text); }
  .option-card .oc-desc { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; line-height: 1.4; }
  .option-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; }

  .advanced-toggle summary {
    cursor: pointer; font-size: 12px; font-weight: 600; color: var(--primary);
    list-style: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 12px;
  }
  .advanced-toggle summary::-webkit-details-marker { display: none; }
  .advanced-toggle summary .mdi { transition: transform .15s ease; }
  .advanced-toggle[open] summary .mdi { transform: rotate(90deg); }
</style>

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

      <div class="form-section-title">Personal Information</div>
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

      <div class="form-section-title mt-3">Account Status</div>
      <div class="row">
        <div class="form-group col-md-4">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="Active"   {{ $user->status === 'Active'   ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ $user->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="form-group col-md-8">
          <label>Department <span class="text-danger">*</span></label>
          <select name="department_id" class="form-control" required>
            <option value="">Select a department&hellip;</option>
            @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ $user->department_id === $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-section-title mt-3">Access</div>
      <label class="mb-2 d-block" style="font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
        Role <span class="text-muted" style="text-transform:none;font-weight:400;">(pick one or more)</span>
      </label>
      <div class="option-grid mb-3">
        @foreach($roles as $role)
          <label class="option-card">
            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
              {{ $user->hasRole($role->name) ? 'checked' : '' }}>
            <div class="oc-icon"><i class="mdi {{ \App\Support\RoleCatalog::icon($role->name) }}"></i></div>
            <div>
              <div class="oc-title">{{ \App\Support\RoleCatalog::label($role->name) }}</div>
              <div class="oc-desc">{{ \App\Support\RoleCatalog::description($role->name) ?? 'Custom role.' }}</div>
            </div>
          </label>
        @endforeach
      </div>

      <details class="advanced-toggle" {{ $user->getDirectPermissions()->count() ? 'open' : '' }}>
        <summary><i class="mdi mdi-chevron-right"></i>Advanced: individual permissions</summary>
        <p class="text-muted mb-2" style="font-size:12px;">Optional — on top of whatever the roles above already grant.</p>
        <div class="option-grid">
          @foreach($permissions as $permission)
            <label class="option-card">
              <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                {{ $user->hasDirectPermission($permission->name) ? 'checked' : '' }}>
              <div class="oc-icon"><i class="mdi {{ \App\Support\PermissionCatalog::icon($permission->name) }}"></i></div>
              <div>
                <div class="oc-title">{{ \App\Support\PermissionCatalog::label($permission->name) }}</div>
                @if(\App\Support\PermissionCatalog::description($permission->name))
                  <div class="oc-desc">{{ \App\Support\PermissionCatalog::description($permission->name) }}</div>
                @endif
              </div>
            </label>
          @endforeach
        </div>
      </details>

      <div style="display:flex;align-items:center;gap:10px;margin-top:20px;">
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

@section('scripts')
<script>
$(document).ready(function () {
  $(document).on('change', '.option-card input', function () {
    $(this).closest('.option-card').toggleClass('is-checked', this.checked);
  });
  $('.option-card input:checked').closest('.option-card').addClass('is-checked');
});
</script>
@endsection
