<!DOCTYPE html>
<html lang="en">
@include('templates.header')
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="{{asset('img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
  </div> -->

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">

    <!-- Left navbar links -->
   @include('admin.layout.includes.menubar')
  <!-- Left navbar links -->

    <!-- Right navbar links (optional) -->
    @include('templates.top_right_navbar')
     <!-- /.Right navbar links  -->

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{url('/admin/home')}}" class="brand-link">
      <!-- <img src="{{asset('img/AdminLTELogo.png')}}" alt="Glitter Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light">Anaheim</span>
      
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

      <!-- Sidebar user panel (optional) -->
      @include('admin.layout.includes.user_panel')
      <!-- /.Sidebar user panel -->

      <!-- SidebarSearch Form (optional) -->
      @include('templates.sidebar_search')
      <!-- /.SidebarSearch Form  -->

    @include('admin.layout.includes.sidebar')
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">
            @if (Auth::user()->role == 0)
              @if (Request::path() == 'admin/revenue')
              Revenue Dashboard
              @else
              Admin Dashboard
              @endif
            @else
            Agency Dashoard
            @endif
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Admin Home</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

     @include('templates.flash_message')
       
          @yield('contents')
        
        

        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

@include('admin.layout.includes.footer')

</body>
</html>
