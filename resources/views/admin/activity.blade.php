@extends('admin.index')
@section('title', 'Home Page')


@section('content')


<style>
    .icon{

        font-size: 18px !important;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between flex-wrap">
            <div class="d-flex align-items-end flex-wrap">
                <h2>Activity logs</h2>
            </div>

            <div class="d-flex justify-content-between align-items-end flex-wrap">
                <button class="btn btn-secondary text-light mr-1" onclick="printTable()"><i class="fa-solid fa-print mr-2 icon"></i>Print</button>
                <button style="background-color: #1D6F42;" class="btn  text-light" onclick="exportToExcel()"><i class="fa-solid fa-file-excel mr-2 icon"></i>Export to Excel</button>

            </div>
        </div>
        <hr>
        <form method="GET" action="{{ route('filter') }}">
            @csrf
            <div class="d-flex flex-row bd-highlight align-items-end flex-wrap">
                <div class="mr-1">
                    <label for="">Start date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="mr-2">
                    <label for="">End date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                </div>
                <div class="">
                    <button type="submit" style="font-size:15px; padding:9px;" class="btn btn-primary text-light">Filter</button>
                    <a href="{{route('admin.activity')}}" type="button" style="font-size:15px; padding:9px;" class="btn btn-primary text-light">Reset</a>
                </div>
            </div>
        </form>
        <div class="table-responsive pt-3">
            <table class="table bg-light table-bordered table-striped" id="dataTable">
                <thead class="thead-dark" style="text-transform: uppercase;">
                    <tr>
                        <th>Date and Time</th>
                        <th>Activity</th>
                        <th>Email</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($Activity_logs as $index => $Activity_log)
                    @if ($Activity_log->email == Auth()->user()->email || Auth()->user()->role === 'master admin')
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

<!-- for excel report -->
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

//     function printTable() {
//     var printWindow = window.open('', '_blank');
//     printWindow.document.write('<html><head><title>.</title><style>@media print {table {font-size: 9pt; border-collapse: collapse;} table, th, td {border: 1px solid black;} table td, table th {text-align: left;}}</style></head><body>');
//     printWindow.document.write(document.getElementById('dataTable').outerHTML);
//     printWindow.document.write('</body></html>');
//     printWindow.document.close();
//     printWindow.print();
//     printWindow.close();
// }







function printTable() {
    // Temporarily disable pagination
    var table = $('#dataTable').DataTable();
    var oldPagination = table.page.len();
    table.page.len(-1).draw();

    // Clone the table
    var clonedTable = $('#dataTable').clone();

    // Open a new window
    var printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>.</title><style>@media print {table {font-size: 9pt; border-collapse: collapse;} table, th, td {border: 1px solid black;} table td, table th {text-align: left;}}</style></head><body>');

    // Append the cloned table to the new window
    printWindow.document.write('<h3>Report {{Auth()->user()->first_name." ".Auth()->user()->last_name}}</h3>');
    printWindow.document.write(clonedTable[0].outerHTML);

    printWindow.document.write('</body></html>');
    printWindow.document.close();

    // Re-enable pagination
    table.page.len(oldPagination).draw();

    // Print and close the window
    printWindow.print();
    printWindow.close();
}



















</script>

@endsection
