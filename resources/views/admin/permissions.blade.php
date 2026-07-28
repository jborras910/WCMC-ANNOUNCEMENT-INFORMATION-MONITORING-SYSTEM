@extends('admin.index')
@section('title', 'Permissions')

@section('content')

<style>
  .rbac-tabs { display: flex; gap: 8px; margin-bottom: 18px; }
  .rbac-intro { font-size: 13px; color: var(--text-muted); margin: -8px 0 20px; max-width: 720px; }

  .oc-icon {
    width: 34px; height: 34px; border-radius: 7px; background: #eef2ff; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
  }
  .oc-icon i { font-size: 16px; color: var(--primary); }
  .oc-title { font-weight: 600; font-size: 13px; color: var(--text); }
  .oc-desc { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; line-height: 1.4; }

  .perm-row { display: flex; align-items: center; gap: 12px; padding: 12px 4px; border-bottom: 1px solid var(--border); }
  .perm-row:last-child { border-bottom: none; }
  .perm-row .perm-usage { font-size: 11px; color: var(--text-muted); white-space: nowrap; }
</style>

@if(session('success'))
<script>
  const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
  Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
</script>
@elseif(session('error'))
<script>
  Swal.fire({ title: 'Error!', text: "{{ session('error') }}", icon: 'error' });
</script>
@endif

<div class="rbac-tabs">
  <a href="{{ route('admin.roles') }}" class="btn btn-sm {{ request()->routeIs('admin.roles') ? 'btn-primary' : 'btn-secondary' }}">
    <i class="mdi mdi-shield-account mr-1"></i>Roles
  </a>
  <a href="{{ route('admin.permissions') }}" class="btn btn-sm {{ request()->routeIs('admin.permissions') ? 'btn-primary' : 'btn-secondary' }}">
    <i class="mdi mdi-key mr-1"></i>Permissions
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-key mr-1 text-primary"></i> Permissions
      </h5>
    </div>
    <p class="rbac-intro">
      The individual abilities that <a href="{{ route('admin.roles') }}">roles</a> (and users, directly)
      are built from. You'll only need to add one here if a role needs to grant an
      ability that doesn't exist yet — otherwise just manage roles.
    </p>

    <div class="mb-3">
      @forelse($permissions as $permission)
      <div class="perm-row">
        <div class="oc-icon"><i class="mdi {{ \App\Support\PermissionCatalog::icon($permission->name) }}"></i></div>
        <div class="flex-grow-1">
          <div class="oc-title">{{ \App\Support\PermissionCatalog::label($permission->name) }}</div>
          <div class="oc-desc">
            {{ \App\Support\PermissionCatalog::description($permission->name) ?? 'Custom permission — ' . $permission->name }}
          </div>
        </div>
        <div class="perm-usage">used by {{ $permission->roles_count }} role{{ $permission->roles_count === 1 ? '' : 's' }}</div>
        <form method="post" action="{{ route('admin.permissions.destroy', ['permission' => $permission]) }}">
          @csrf @method('delete')
          <button type="submit" class="btn btn-sm btn-danger text-white"
            onclick="return confirm('Delete permission &quot;{{ $permission->name }}&quot;? It will be removed from every role and user that has it.')">
            <i class="mdi mdi-delete"></i>
          </button>
        </form>
      </div>
      @empty
        <p class="text-muted text-center mb-0">No permissions yet.</p>
      @endforelse
    </div>

    <div class="form-section-title mt-3">Add Permission</div>
    <form method="post" action="{{ route('admin.permissions.store') }}" class="form-inline">
      @csrf
      <input type="text" name="name" class="form-control form-control-sm mr-2" placeholder="e.g. edit-slides" required>
      <button type="submit" class="btn btn-sm btn-primary"><i class="mdi mdi-plus mr-1"></i>Add Permission</button>
    </form>
    <p class="text-muted mt-2 mb-0" style="font-size:11.5px;">
      Use short, kebab-case names (letters, numbers, and hyphens) — your app code checks for this exact string.
    </p>
  </div>
</div>

@endsection
