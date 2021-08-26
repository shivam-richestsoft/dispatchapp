<div class="card" id="data">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#view" data-toggle="tab">Permissions List</a></li>
                  <li class="nav-item"><a class="nav-link" href="#profile" data-toggle="tab">Add Permissions</a></li>
                  <li class="nav-item search-right">
                   <div>
                      <div class="input-group" data-widget="sidebar-search">
                       <input class="form-control form-control-sidebar" id="search" type="search" placeholder="Search" aria-label="Search">
                      </div>
                   </div>
                  </li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane" id="profile">
                     @include('admin.permissions.includes.addform')
                  </div>
                  <div class="active tab-pane" id="view">
                     @include('admin.permissions.includes.view')
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
      </div>
      <script>
$(document).on('click', '.pagination a', function(event){
 event.preventDefault(); 
 console.log('ok');
 var page = $(this).attr('href').split('page=')[1];
 var sendurl=$(this).attr('href');
 fetch_data(page,sendurl);
});
function fetch_data(page,url)
{
  let make_url="";
  let value=document.querySelector("#search").value;
  if(url.indexOf('search')> -1){
    make_url="{{url('/')}}/admin/permissions/search?page="+page;
    var data={'search':value};
  }else{
    make_url="{{url('/')}}/admin/permissions/views/fetch_data?page="+page;
    var data="";
  }
 $.ajax({
  url:make_url,
  data:data,
  success:function(data)
  {
   $('#view').empty().html(data);
  }
 });
}
document.querySelector("#search").addEventListener("keyup",(e)=>{
  fetch_data(1,"{{url('/')}}/admin/permissions/search");
     });
</script>