@extends('admin.layout.template')
@section('contents')

        <!-- Info boxes (optional) -->
       @include('includes.info_box')
        <!-- /.Info boxes-->

        <!-- Graph section (optional) -->
        @include('includes.graph')
        <!-- /.Graph section -->

        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-8">
            <!-- MAP & BOX PANE (optional) -->
            @include('includes.map')
            <!-- /.MAP & BOX PANE -->
            <div class="row">

              <div class="col-md-6">
                <!-- DIRECT CHAT  (optional) -->
                @include('includes.chat')
                <!--/.direct-chat -->
              </div>
              <!-- /.col -->

              <!-- User section (optional) -->
              <div class="col-md-6">
                <!-- USERS LIST -->
                @include('includes.users')
                <!-- /.USERS LIST -->
              </div>
               <!-- /.User section -->
               
              <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- TABLE: LATEST ORDERS (optional) -->
            @include('includes.orders')
            <!-- /.TABLE: LATEST ORDERS -->
            
      
          </div>
          <!-- /.col -->

           <!-- Info Boxes Style 2 (optional) -->
          <div class="col-md-4">
            @include('includes.info_box1')
            @include('includes.info_box2')
            @include('includes.info_box3')    
          </div>
          <!-- /.col -->
          <!-- /.Info Boxes Style 2 (optional) -->

        </div>
        <!-- /.row -->
@endsection

