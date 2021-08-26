<div class="card">
              <div class="card-header">
                <h3 class="card-title">Permissions</h3>
                @include('admin.permissions.includes.back')
              </div>
              <!-- /.card-header -->
             <div class="card-body">
              
                      <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label">Title</label>
                        <div class="col-sm-10">
                          <input type="text" value="{{$permission->title}}" class="form-control" id="title" readonly name="title" placeholder="Permission Title">
                        </div>
                      </div>
                      {{--
                      <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" readonly name="description" style="resize:none;">{{$permission->description}}</textarea>
                          </div>
                      </div>--}}
                </div>
</div>

