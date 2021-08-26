<div class="card">
              <div class="card-header">
                <h3 class="card-title">Permissions</h3>
              </div>
              <!-- /.card-header -->
             <div class="card-body">
                <table class="table table-bordered">
                  @if(count($data)>0)
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>ID</th>
                      <th>Permissions</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  @endif
                  <tbody>
                        @forelse($data as $key=>$value)
                           <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$value->id}}</td>
                                <td id="title_{{$value->id}}">{{$value->title}}</td>
                                <td>@if($value->status==1)
                                 <button data-id="{{$value->id}}" class="disable_enable btn btn-success btn-xs" onclick="toggleDisableEnable(this)">Enable</button>
                                 @else
                                 <button data-id="{{$value->id}}" class="disable_enable btn btn-danger btn-xs" onclick="toggleDisableEnable(this)">Disable</button>
                                 @endif
                                </td>
                                <td>
                                <button data-id="{{$value->id}}" class="btn btn-outline-success btn-xs view">View</button>
                                <button data-id="{{$value->id}}" class="btn btn-outline-success btn-xs update">Update</button>
                                <button data-id="{{$value->id}}" class="btn btn-danger btn-xs remove">Remove</button>
                                </td>
                           </tr>
                        @empty
                          <center> <h3> No Permission Availabale </h3> </center>
                        @endforelse
                  </tbody>
                </table>
              </div> 
              {{$data->links()}}
              {{--
              @if($data->previousPageUrl() != null)
                 <a href="{{$data->previousPageUrl()}}" class="pagination prev_btn pull-left"><i class="fa fa-chevron-left"></i> Previous</a>
              @endif

              @if($data->nextPageUrl() != null)
                  <a href="{{$data->nextPageUrl()}}" class="pagination prev_btn pull-right">Next <i class="fa fa-chevron-right"></i> </a>
              @endif
              --}}
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
<script>
 function toggleDisableEnable(e){
      var id=e.getAttribute('data-id');
      var data;
        if(document.querySelector("#search").value!=""){
          var search=document.querySelector("#search").value;
           data={id:id,'search':search};
        }else{
           data={id:id};
        }
        ajax("post","{{url('/')}}/admin/permissions/togglestatus",data,'view');
  }   
jQuery(".remove").on("click",(e)=>{
    let id=e.target.dataset.id;
    let title=document.querySelector(`#title_${id}`).innerText;
    let con = confirm(`Are you sure you want to remove ${title} permission`);
    if(con){
      ajax("Delete","{{url('/')}}/admin/permissions/delete",{ id:id },'view');
    }
   });      
 jQuery(".view").on("click",(e)=>{
     let id=e.target.dataset.id;
     ajax("get","{{url('/')}}/admin/permissions/detail",{ id:id },'data');
 })   
 jQuery(".update").on("click",(e)=>{
     let id=e.target.dataset.id;
     ajax("get","{{url('/')}}/admin/permissions/update",{ id:id },'data');
 })   
  </script>
 