@extends('admin.layout.template')
@section('contents')
 <!-- Info boxes (optional) -->
 @include('admin.layout.includes.info_box')
        <!-- /.Info boxes-->
        {{--
      <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#staff_list" data-toggle="tab">Staff</a></li>
                  <li class="nav-item"><a class="nav-link" href="#agency_list" data-toggle="tab">Agency</a></li>
                  <li class="nav-item"><a class="nav-link" href="#finance" data-toggle="tab">Finance</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="staff_list">

                  <div class="card">
              <div class="card-header">
                <h3 class="card-title">Staff Management</h3>
              </div>
              <!-- /.card-header -->
             <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Staff ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($staff as $key => $value )
                      
                    
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$value->staff_id}}</td>
                      <td>{{$value->first_name}}</td>
                      <td>
                      {{$value->email}}
                      </td>
                      <td>{{$value->phone_number}}</td>
                    </tr>

                    @endforeach
                
                  
                  </tbody>
                </table>
              </div> 
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
              </div>
            </div>
            <!-- /.card -->
         
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="agency_list">
                  <div class="card">
              <div class="card-header">
                <h3 class="card-title">Staff Management</h3>
              </div>
              <!-- /.card-header -->
             <div class="card-body">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Staff ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($staff as $key => $value )
                      
                    
                    <tr>
                      <td>{{$key+1}}</td>
                      <td>{{$value->staff_id}}</td>
                      <td>{{$value->first_name}}</td>
                      <td>
                      {{$value->email}}
                      </td>
                      <td>{{$value->phone_number}}</td>
                    </tr>

                    @endforeach
                
                  
                  </tbody>
                </table>
              </div> 
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                <ul class="pagination pagination-sm m-0 float-right">
                  <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
              </div>
            </div>
                  </div>
                  <!-- /.tab-pane -->

                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
            --}}
@endsection
