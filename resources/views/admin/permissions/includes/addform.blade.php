<form class="form-horizontal" action="{{url('/')}}/admin/permissions/add" id="create_permissions" method="post">
                      @csrf
                      <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label">Title</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="title" name="title" placeholder="Permission Title">
                          <div class="error" id="error_title"></div>
                        </div>
                      </div>
                      <!-- <div class="form-group row">
                        <label for="first_name" class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="description" name="description" style="resize:none;"></textarea>
                            <div class="error" id="error_description"></div>
                          </div>
                      </div> -->
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-success">Submit</button>
                          <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                      </div>
                    </form>