<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>@yield('title', 'Custom Auth Laravel')</title>

    <style>
      * {
  margin: 0;
  padding: 0;
  transition-timing-function: ease-in !important;

}

</style>

       <!--font awesome icon-->
       <script
       src="https://kit.fontawesome.com/6cea1e7bdb.js"
       crossorigin="anonymous"
     ></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/base/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">


    <script src="{{ asset('sweetAlert/sweetAlert.js') }}"></script>

  </head>
  <body>

    <div class="container-scroller">

      <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

        <div class="navbar-brand-wrapper d-flex justify-content-center align-items-center">
          <div
            class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100"
          >
            <a class="navbar-brand brand-logo"
              ><img style="width: 70%; height: auto;"  src="https://www.worldcitimedicalcenter.com/frontend/wcmc-logo.png" alt="logo"
            />



          </a>

            <button
              class="navbar-toggler navbar-toggler align-self-center "
              type="button"
              data-toggle="minimize"
            >


              <span class="mdi mdi-sort-variant"></span>
            </button>
          </div>




        </div>
        <div
          class="navbar-menu-wrapper d-flex align-items-center justify-content-end"
        >

          <ul class="navbar-nav mr-lg-4 w-100">
            <li class="nav-item nav-search d-none d-lg-block w-100">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="search">
                    <i class="mdi mdi-magnify"></i>
                  </span>
                </div>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Search now"
                  aria-label="search"
                  aria-describedby="search"
                />
              </div>
            </li>


          </ul>

          <ul class="navbar-nav navbar-nav-right">

            <li class="nav-item nav-profile dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown"
                id="profileDropdown"
              >
                <img class="border" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQm0FWrcJLDYw6_WHnt7Wgyqcm91PlSlb3EGXdwHlg2UUj4cua583wsckrfxi6ipl7vWsg&usqp=CAU" alt="profile" />
                <span class="nav-profile-name">{{Auth()->user()->email}}</span>
              </a>
              <div
                class="dropdown-menu dropdown-menu-right navbar-dropdown"
                aria-labelledby="profileDropdown"
              >
                <a href="{{route('logout')}}" class="dropdown-item">
                  <i class="mdi mdi-logout text-primary"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
          <button
            class="navbar-toggler navbar-toggler-right d-lg-none align-self-center"
            type="button"
            data-toggle="offcanvas"
          >
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>

      <div class="container-fluid page-body-wrapper">
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-home menu-icon"></i>
                <span  class="menu-title">Dashboard</span>
              </a>
            </li>
            @if(Auth()->user()->role ==='admin')
            <li class="nav-item">
              <a class="nav-link" href="{{route('admin.users')}}">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Users</span>
              </a>
            </li>
            @endif


            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.activity') }}">
                <i class="mdi mdi-circle-outline menu-icon"></i>
                <span  class="menu-title">Activity logs</span>
              </a>
            </li>

          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
          </div>
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div
              class="d-sm-flex justify-content-center justify-content-sm-between"
            >
              <span
                class="text-muted text-center text-sm-left d-block d-sm-inline-block"
                >Copyright ©
                <a
                  > World Citi Medical Center </a
                >2024</span
              >

            </div>
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <script src="{{ asset('vendors/base/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/data-table.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/jquery.cookie.js') }}" type="text/javascript"></script>


    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>




  </body>
</html>
