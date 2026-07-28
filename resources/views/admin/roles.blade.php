@extends('admin.index')
@section('title', 'Roles')

@section('content')

<style>
  .rbac-tabs { display: flex; gap: 8px; margin-bottom: 18px; }

  .rbac-intro { font-size: 13px; color: var(--text-muted); margin: -8px 0 20px; max-width: 720px; }

  .role-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px; }

  .role-tile {
    border: 1.5px solid var(--border); border-radius: 10px; background: #fff;
    padding: 16px; display: flex; flex-direction: column; gap: 10px;
  }
  .role-tile-head { display: flex; align-items: center; gap: 12px; }
  .role-tile-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 19px;
  }
  .role-tile-title { font-weight: 700; font-size: 14.5px; color: var(--text); text-transform: capitalize; }
  .role-tile-desc { font-size: 12.5px; color: var(--text-muted); line-height: 1.5; min-height: 32px; }
  .role-tile-perms { display: flex; flex-wrap: wrap; gap: 5px; }
  .role-tile-footer { display: flex; gap: 8px; margin-top: auto; padding-top: 4px; }
  .role-tile-footer .btn { flex: 1; }

  /* Selectable option cards used inside the role modals */
  .option-card {
    display: flex; align-items: flex-start; gap: 10px;
    border: 1.5px solid var(--border); border-radius: 8px; padding: 10px 12px;
    cursor: pointer; transition: border-color .15s ease, background .15s ease; background: #fff;
  }
  .option-card:hover { border-color: var(--primary); }
  .option-card.is-checked { border-color: var(--primary); background: rgba(37,99,235,0.05); }
  .option-card input { margin-top: 3px; flex-shrink: 0; }
  .option-card .oc-icon {
    width: 30px; height: 30px; border-radius: 7px; background: #eef2ff; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
  }
  .option-card .oc-icon i { font-size: 15px; color: var(--primary); }
  .option-card .oc-title { font-weight: 600; font-size: 13px; color: var(--text); }
  .option-card .oc-desc { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; line-height: 1.4; }
  .option-list { display: flex; flex-direction: column; gap: 8px; max-height: 340px; overflow-y: auto; padding-right: 4px; }

  .select-all-link { font-size: 12px; font-weight: 600; cursor: pointer; }
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

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-shield-account mr-1 text-primary"></i> Roles
      </h5>
      <button type="button" class="btn btn-primary btn-sm js-open-modal" data-modal="#createRoleModal">
        <i class="mdi mdi-plus mr-1"></i>Add Role
      </button>
    </div>
    <p class="rbac-intro">
      A role bundles a set of permissions together (e.g. "faculty" can manage slides).
      Assign roles to users on the Users page — you can also grant a user extra
      permissions individually, on top of their role. Need a new ability first?
      Add it on the <a href="{{ route('admin.permissions') }}">Permissions</a> page.
    </p>

    <div class="role-grid">
      @foreach($roles as $role)
      <div class="role-tile">
        <div class="role-tile-head">
          <div class="role-tile-icon" style="background: rgba(0,0,0,0.05);">
            <i class="mdi {{ \App\Support\RoleCatalog::icon($role->name) }} text-{{ \App\Support\RoleCatalog::color($role->name) }}"></i>
          </div>
          <div>
            <div class="role-tile-title">{{ \App\Support\RoleCatalog::label($role->name) }}</div>
            <div class="text-muted" style="font-size:11px;">{{ $role->permissions->count() }} permission{{ $role->permissions->count() === 1 ? '' : 's' }}</div>
          </div>
        </div>

        <div class="role-tile-desc">{{ \App\Support\RoleCatalog::description($role->name) ?? 'Custom role.' }}</div>

        <div class="role-tile-perms">
          @forelse($role->permissions as $permission)
            <span class="badge badge-secondary" title="{{ \App\Support\PermissionCatalog::description($permission->name) }}">
              {{ \App\Support\PermissionCatalog::label($permission->name) }}
            </span>
          @empty
            <span class="text-muted" style="font-size:12px;">No permissions assigned yet.</span>
          @endforelse
        </div>

        <div class="role-tile-footer">
          <button type="button" class="btn btn-primary btn-sm js-open-modal" data-modal="#editRoleModal{{ $role->id }}">
            <i class="mdi mdi-pencil"></i> Permissions
          </button>

          @if($role->name !== \App\User::ROLE_MASTER_ADMIN)
            <button type="button" class="btn btn-danger btn-sm text-white js-open-modal" data-modal="#deleteRoleModal{{ $role->id }}">
              <i class="mdi mdi-delete"></i>
            </button>
          @else
            <button type="button" class="btn btn-secondary btn-sm" disabled title="The master_admin role is protected and can't be deleted.">
              <i class="mdi mdi-lock"></i>
            </button>
          @endif
        </div>
      </div>

      {{-- Delete confirmation modal --}}
      @if($role->name !== \App\User::ROLE_MASTER_ADMIN)
      <div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h6 class="modal-title text-danger"><i class="mdi mdi-alert-circle mr-1"></i>Delete "{{ \App\Support\RoleCatalog::label($role->name) }}"?</h6>
              <button type="button" class="close js-close-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
              Users currently assigned to this role will lose the permissions it grants.
              This can't be undone.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
              <form method="post" action="{{ route('admin.roles.destroy', ['role' => $role]) }}">
                @csrf @method('delete')
                <button type="submit" class="btn btn-danger btn-sm text-white">Delete Role</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      @endif

      {{-- Edit role permissions modal --}}
      <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
          <form method="post" action="{{ route('admin.roles.update', ['role' => $role]) }}">
            @csrf @method('put')
            <div class="modal-content">
              <div class="modal-header">
                <h6 class="modal-title">
                  <i class="mdi {{ \App\Support\RoleCatalog::icon($role->name) }} mr-1 text-{{ \App\Support\RoleCatalog::color($role->name) }}"></i>
                  {{ \App\Support\RoleCatalog::label($role->name) }} permissions
                </h6>
                <button type="button" class="close js-close-modal"><span>&times;</span></button>
              </div>
              <div class="modal-body">
                @if($permissions->count())
                  <div class="d-flex justify-content-end mb-2">
                    <a href="#" class="select-all-link text-primary mr-3 js-select-all" data-scope="#editRoleModal{{ $role->id }}">Select all</a>
                    <a href="#" class="select-all-link text-muted js-select-none" data-scope="#editRoleModal{{ $role->id }}">Select none</a>
                  </div>
                  <div class="option-list">
                    @foreach($permissions as $permission)
                      <label class="option-card">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                          {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
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
                @else
                  <p class="text-muted mb-0">No permissions exist yet. <a href="{{ route('admin.permissions') }}">Add one</a> first.</p>
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Create role modal --}}
<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="{{ route('admin.roles.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><i class="mdi mdi-shield-plus mr-1 text-primary"></i>Add Role</h6>
          <button type="button" class="close js-close-modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Role name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. registrar" required>
          </div>

          <label class="mb-2 d-block">Permissions</label>
          @if($permissions->count())
            <div class="d-flex justify-content-end mb-2">
              <a href="#" class="select-all-link text-primary mr-3 js-select-all" data-scope="#createRoleModal">Select all</a>
              <a href="#" class="select-all-link text-muted js-select-none" data-scope="#createRoleModal">Select none</a>
            </div>
            <div class="option-list">
              @foreach($permissions as $permission)
                <label class="option-card">
                  <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
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
          @else
            <p class="text-muted mb-0">No permissions exist yet. <a href="{{ route('admin.permissions') }}">Add one</a> first.</p>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Create Role</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
  // Open/close modals explicitly — more reliable than relying on
  // data-toggle/data-bs-toggle auto-init, which this template's bundled
  // Bootstrap build doesn't register for every component.
  $(document).on('click', '.js-open-modal', function () {
    $($(this).data('modal')).modal('show');
  });
  $(document).on('click', '.js-close-modal', function () {
    $(this).closest('.modal').modal('hide');
  });

  // Highlight selected option cards
  $(document).on('change', '.option-card input', function () {
    $(this).closest('.option-card').toggleClass('is-checked', this.checked);
  });
  $('.option-card input:checked').closest('.option-card').addClass('is-checked');

  $(document).on('click', '.js-select-all, .js-select-none', function (e) {
    e.preventDefault();
    var checked = $(this).hasClass('js-select-all');
    $($(this).data('scope')).find('.option-card input').prop('checked', checked).trigger('change');
  });
});
</script>
@endsection
