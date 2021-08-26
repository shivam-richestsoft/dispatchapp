<div class="col-sm-12">
 <button class="btn btn-outline-success float-right" id="permissions"><i class="fas fa-arrow-alt-circle-left"> Back</i></button>
</div> 
<script>
 jQuery("#permissions").on("click",(e)=>{
     $.ajax({
       type:"get",
       url:"{{url('/')}}/admin/permissions/back",
       success: function(data){
        $('#permissions_data').empty().html(data);
       }
     })
 })  
</script>