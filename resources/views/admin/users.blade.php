@extends('admin.index')
@section('title', 'Users')

@section('content')

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
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-account-multiple mr-1 text-primary"></i> Users
      </h5>
      <a href="{{ route('admin.addUser') }}" class="btn btn-primary btn-sm">
        <i class="mdi mdi-plus mr-1"></i>Add User
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered bg-white" id="dataTable">
        <thead class="thead-dark">
          <tr>
            <th style="width:50px">#</th>
            <th>Name</th>
            <th>Username</th>
            <th>Department</th>
            <th>Role</th>
            <th class="text-center" style="width:110px">Status</th>
            <th class="text-center" style="width:120px">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $index => $user)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-capitalize">
              {{ $user->first_name }}
              {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }}
            </td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->department ?? '—' }}</td>
            <td>
              <span class="badge badge-{{ $user->role === 'master_admin' ? 'danger' : 'secondary' }}">
                {{ $user->role ?: 'user' }}
              </span>
            </td>
            <td class="text-center">
              <span class="badge-pill-status {{ $user->status === 'Active' ? 's-published' : 's-rejected' }}">
                {{ $user->status ?? 'Inactive' }}
              </span>
            </td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-sm btn-success text-white dropdown-toggle" type="button"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="mdi mdi-cog mr-1"></i>Action
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="{{ route('user.edit', ['user' => $user]) }}">
                    <i class="mdi mdi-pencil mr-2 text-primary"></i>Edit
                  </a>
                  <a class="dropdown-item text-danger" href="#"
                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user->id }}">
                    <i class="mdi mdi-delete mr-2"></i>Delete
                  </a>
                </div>
              </div>

              <form method="post" action="{{ route('deleteUser.destroy', ['user' => $user]) }}">
                @csrf @method('delete')
                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h6 class="modal-title text-danger">
                          <i class="mdi mdi-alert-circle mr-1"></i>Confirm Delete
                        </h6>
                        <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to delete
                        <strong class="text-capitalize">{{ $user->first_name }} {{ $user->last_name }}</strong>?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm text-white">Delete</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#dataTable').DataTable({ responsive: true });
  });
</script>
@endsection
