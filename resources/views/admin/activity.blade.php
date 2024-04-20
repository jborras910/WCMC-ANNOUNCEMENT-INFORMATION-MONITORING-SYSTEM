@extends('admin.index')
@section('title', 'Home Page')


@section('content')

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="d-flex align-items-end flex-wrap">
                <h2>Activity logs</h2>
            </div>
            <div class="d-flex justify-content-between align-items-end flex-wrap">
                <button style="background-color: #1D6F42;" class="btn  text-light" onclick="exportToExcel()" class="btn btn-success text-light">Export to excell</button>
            </div>
        </div>
        <div class="table-responsive pt-3">
            <table class="table bg-light table-bordered table-striped" id="dataTable" >
                <thead>
                    <tr>
                        <th>Date and Time</th>
                        <th>activity</th>
                        <th>Email</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Activity_logs as $index => $Activity_log)
                    @if ($Activity_log->email == Auth()->user()->email)
                        <tr>
                            <td>{{ $Activity_log->created_at->timezone('Asia/Manila')->format('F j, Y, g:i a') }}</td>
                            <td>{{ $Activity_log->activity }}</td>
                            <td>{{ $Activity_log->email }}</td>
                            <td>{{ $Activity_log->name }}</td>
                        </tr>
                    @elseif(Auth()->user()->role === 'admin')
                        <tr>
                            <td>{{ $Activity_log->created_at->timezone('Asia/Manila')->format('F j, Y, g:i a') }}</td>
                            <td>{{ $Activity_log->activity }}</td>
                            <td>{{ $Activity_log->email }}</td>
                            <td>{{ $Activity_log->name }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

{{-- jQuery --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- for excell report -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>



<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });



    function exportToExcel() {
    // Temporarily disable pagination
    var table = $('#dataTable').DataTable();
    var oldPagination = table.page.len();
    table.page.len(-1).draw();

    // Select the table
    var table = document.getElementById('dataTable');

    // Convert the table to a worksheet
    var ws = XLSX.utils.table_to_sheet(table);

    // Create a new workbook
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

    // Save the workbook as an Excel file
    var fileName = 'report.xlsx';

    try {
        // Try to write the file
        XLSX.writeFile(wb, fileName);

        // Display success toast
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Download Successfully"
        });
    } catch (error) {
        // Display error message
        alert('Download failed: ' + error);
    } finally {
        // Re-enable pagination
        table.page.len(oldPagination).draw();
    }
}

</script>

@endsection
