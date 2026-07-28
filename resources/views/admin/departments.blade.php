@extends('admin.index')
@section('title', 'Departments')

@section('content')

<style>
  .dept-link {
    font-family: 'Courier New', monospace; font-size: 12px; background: var(--bg);
    border: 1px solid var(--border); border-radius: 6px; padding: 4px 8px;
    display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted);
  }
  .dept-link:hover { color: var(--primary); }
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

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-domain mr-1 text-primary"></i> Departments
      </h5>
      <button type="button" class="btn btn-primary btn-sm js-open-modal" data-modal="#createDepartmentModal">
        <i class="mdi mdi-plus mr-1"></i>Add Department
      </button>
    </div>
    <p class="text-muted mb-3" style="font-size:13px;max-width:720px;">
      Every user belongs to a department, and slides they upload are automatically tagged with it.
      Each department also gets its own public display screen — share that link with the team that
      owns it (e.g. Marketing) so they only see their own videos looping there.
    </p>

    <div class="table-responsive">
      <table class="table table-hover table-bordered bg-white">
        <thead class="thead-dark">
          <tr>
            <th>Department</th>
            <th class="text-center" style="width:90px">Users</th>
            <th class="text-center" style="width:90px">Slides</th>
            <th>Display Link</th>
            <th class="text-center" style="width:140px">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($departments as $department)
          <tr>
            <td class="font-weight-bold">{{ $department->name }}</td>
            <td class="text-center">{{ $department->users_count }}</td>
            <td class="text-center">{{ $department->slides_count }}</td>
            <td>
              <a class="dept-link" href="{{ route('display.welcome', ['department' => $department]) }}" target="_blank">
                <i class="mdi mdi-open-in-new"></i>{{ route('display.welcome', ['department' => $department]) }}
              </a>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-primary btn-sm js-open-modal" data-modal="#editDepartmentModal{{ $department->id }}">
                <i class="mdi mdi-pencil"></i>
              </button>
              <button type="button" class="btn btn-danger btn-sm text-white js-open-modal" data-modal="#deleteDepartmentModal{{ $department->id }}">
                <i class="mdi mdi-delete"></i>
              </button>
            </td>
          </tr>

          <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
              <form method="post" action="{{ route('admin.departments.update', ['department' => $department]) }}">
                @csrf @method('put')
                <div class="modal-content">
                  <div class="modal-header">
                    <h6 class="modal-title"><i class="mdi mdi-domain mr-1 text-primary"></i>Rename Department</h6>
                    <button type="button" class="close js-close-modal"><span>&times;</span></button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group mb-0">
                      <label>Department name</label>
                      <input type="text" name="name" class="form-control" value="{{ $department->name }}" required>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="modal fade" id="deleteDepartmentModal{{ $department->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h6 class="modal-title text-danger"><i class="mdi mdi-alert-circle mr-1"></i>Delete "{{ $department->name }}"?</h6>
                  <button type="button" class="close js-close-modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                  @if($department->users_count || $department->slides_count)
                    This department still has {{ $department->users_count }} user(s) and
                    {{ $department->slides_count }} slide(s). Reassign or remove those first.
                  @else
                    This can't be undone.
                  @endif
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
                  @unless($department->users_count || $department->slides_count)
                    <form method="post" action="{{ route('admin.departments.destroy', ['department' => $department]) }}">
                      @csrf @method('delete')
                      <button type="submit" class="btn btn-danger btn-sm text-white">Delete</button>
                    </form>
                  @endunless
                </div>
              </div>
            </div>
          </div>
          @empty
            <tr><td colspan="5" class="text-muted text-center">No departments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Create department modal --}}
<div class="modal fade" id="createDepartmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <form method="post" action="{{ route('admin.departments.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title"><i class="mdi mdi-domain-plus mr-1 text-primary"></i>Add Department</h6>
          <button type="button" class="close js-close-modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-0">
            <label>Department name</label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Marketing" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm js-close-modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Create Department</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
  $(document).on('click', '.js-open-modal', function () {
    $($(this).data('modal')).modal('show');
  });
  $(document).on('click', '.js-close-modal', function () {
    $(this).closest('.modal').modal('hide');
  });
});
</script>
@endsection
