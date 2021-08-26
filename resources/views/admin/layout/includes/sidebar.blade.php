  <!-- Sidebar Menu -->
  <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            
        <li class="nav-item">
              <li class="nav-item">
                <a href="{{url('/admin/home')}}" class="nav-link">
                  <i class="fa fa-home nav-icon"></i>
                  <p>Home</p>
                </a>
              </li>
            
          
              <li class="nav-item">
                <a href="{{url('/admin/profile')}}" class="nav-link">
                  <i class="fas fa-id-badge nav-icon"></i>
                  <p>Profile</p>
                </a>
              </li>
            </ul>
          </li>
     
          
          <li class="nav-item">
              <a href="{{url('/admin/permissions')}}" class="nav-link">
                <i class="fa fa-user-circle nav-icon"></i>
                <p>Permissions</p>
              </a>
          </li>
          
          <li class="nav-item">
            <a href="{{url('/admin/logout')}}" class="nav-link">
              <i class="fas fa-sign-out-alt nav-icon"></i>
              <p>
               Logout
              </p>
            </a>
          </li>

         
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
      <script>
        $(document).ready(function(){
    var fullpath =window.location.pathname;
    // console.log(fullpath);
    var filename=fullpath.replace(/^.*[\\\/]/, '');
    var last="{{url('/')}}/admin/"+filename;
    var currentLink=$('a[href="' + last + '"]'); //Selects the proper a tag
    currentLink.addClass("active");
    });
        </script>