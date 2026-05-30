@extends('admin.index')
@section('title', 'Activity Logs')

@section('content')

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 font-weight-bold">
        <i class="mdi mdi-format-list-bulleted mr-1 text-primary"></i> Activity Logs
      </h5>
      <div>
        <button class="btn btn-sm btn-secondary mr-1" onclick="printTable()">
          <i class="fa-solid fa-print mr-1"></i>Print
        </button>
        <button class="btn btn-sm text-white" style="background-color:#1D6F42;" onclick="exportToExcel()">
          <i class="fa-solid fa-file-excel mr-1"></i>Export to Excel
        </button>
      </div>
    </div>

    <form method="GET" action="{{ route('filter') }}" class="mb-3">
      <div class="d-flex flex-wrap align-items-end">
        <div class="mr-2 mb-2">
          <label class="small mb-1">Start Date</label>
          <input type="date" name="start_date" class="form-control form-control-sm" value="{{ old('start_date') }}">
        </div>
        <div class="mr-2 mb-2">
          <label class="small mb-1">End Date</label>
          <input type="date" name="end_date" class="form-control form-control-sm" value="{{ old('end_date') }}">
        </div>
        <div class="mb-2">
          <button type="submit" class="btn btn-primary btn-sm mr-1">Filter</button>
          <a href="{{ route('admin.activity') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-hover table-bordered bg-white" id="dataTable">
        <thead class="thead-dark">
          <tr>
            <th>Date &amp; Time</th>
            <th>Activity</th>
            <th>Email</th>
            <th>Name</th>
          </tr>
        </thead>
        <tbody>
          @foreach($Activity_logs as $log)
            @if($log->email == Auth()->user()->email || Auth()->user()->role === 'master_admin')
            <tr>
              <td>{{ $log->created_at->timezone('Asia/Manila')->format('M j, Y g:i a') }}</td>
              <td>{{ $log->activity }}</td>
              <td>{{ $log->email }}</td>
              <td>{{ $log->name }}</td>
            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
  $(document).ready(function () {
    $('#dataTable').DataTable({ responsive: true, order: [[0, 'desc']] });
  });

  function exportToExcel() {
    var table = $('#dataTable').DataTable();
    var oldLen = table.page.len();
    table.page.len(-1).draw();

    var ws = XLSX.utils.table_to_sheet(document.getElementById('dataTable'));
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Activity Logs');
    XLSX.writeFile(wb, 'activity_logs.xlsx');

    table.page.len(oldLen).draw();

    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    Toast.fire({ icon: 'success', title: 'Downloaded successfully' });
  }

  function printTable() {
    var table = $('#dataTable').DataTable();
    var oldLen = table.page.len();
    table.page.len(-1).draw();

    var cloned = $('#dataTable').clone()[0].outerHTML;
    var win = window.open('', '_blank');
    win.document.write('<html><head><title>Activity Logs</title><style>table{border-collapse:collapse;width:100%;font-size:10pt;}th,td{border:1px solid #000;padding:6px;text-align:left;}</style></head><body>');
    win.document.write('<h3>Activity Logs — {{ Auth()->user()->first_name }} {{ Auth()->user()->last_name }}</h3>');
    win.document.write(cloned);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
    win.close();

    table.page.len(oldLen).draw();
  }
</script>
@endsection
