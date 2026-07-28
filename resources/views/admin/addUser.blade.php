@extends('admin.index')
@section('title', 'Add User')

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

      <div class="form-section-title">Personal Information</div>
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

      <div class="form-section-title mt-3">Department &amp; Access</div>
      <div class="row">
        <div class="form-group col-md-12">
          <label>Department <span class="text-danger">*</span></label>
          <select name="department_id" class="form-control" required>
            <option value="">Select a department&hellip;</option>
            @foreach($departments as $department)
              <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
              </option>
            @endforeach
          </select>
          @if($departments->isEmpty())
            <small class="text-danger d-block mt-1">
              No departments exist yet — <a href="{{ route('admin.departments') }}">create one first</a>.
            </small>
          @endif
        </div>
      </div>

      <label class="mb-2 d-block" style="font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;">
        Role <span class="text-muted" style="text-transform:none;font-weight:400;">(pick one or more)</span>
      </label>
      <div class="option-grid mb-3">
        @foreach($roles as $role)
          <label class="option-card">
            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
              {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
            <div class="oc-icon"><i class="mdi {{ \App\Support\RoleCatalog::icon($role->name) }}"></i></div>
            <div>
              <div class="oc-title">{{ \App\Support\RoleCatalog::label($role->name) }}</div>
              <div class="oc-desc">{{ \App\Support\RoleCatalog::description($role->name) ?? 'Custom role.' }}</div>
            </div>
          </label>
        @endforeach
      </div>

      <details class="advanced-toggle" {{ old('permissions') ? 'open' : '' }}>
        <summary><i class="mdi mdi-chevron-right"></i>Advanced: grant individual permissions</summary>
        <p class="text-muted mb-2" style="font-size:12px;">Optional — on top of whatever the roles above already grant.</p>
        <div class="option-grid">
          @foreach($permissions as $permission)
            <label class="option-card">
              <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
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
