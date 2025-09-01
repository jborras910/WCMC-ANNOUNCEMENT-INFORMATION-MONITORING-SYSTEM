<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page not found</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="{{ asset('sweetAlert/sweetAlert.js') }}"></script>
</head>
<body>

<script>
    // Function to display the alert
    function showAlert() {
      Swal.fire({
        title: "Invalid!",
        text: "Page not found",
        icon: "error",
      }).then((result) => {
        // Check if the user clicked "Okay"
        if (result.isConfirmed) {
          // Redirect the user to a specific route after clicking "Okay"
          window.location.href = "{{ route('admin.dashboard') }}";
        }
      });
    }

    // Call the function to display the alert
    showAlert();
  </script>


{{--
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
</body>
</html>

