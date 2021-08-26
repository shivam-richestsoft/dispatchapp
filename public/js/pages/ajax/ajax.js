const base_url=$('meta[name="base-url"]').attr('content');
$("#create_permissions").on('submit',(e)=>{
    e.preventDefault();
    const data=getformdata("create_permissions");
    permissionAjax('post','/admin/permissions/add',data);
});

/***********************************Add Staff*********************************************/
 $("#add_staff").on('submit',(e)=>{
    e.preventDefault();
    const data=getformdata("add_staff");
    addStaffProfile('post','/admin/staff/add',data);
});
/***********************************Update Profile **************************************/
 // 
 $("#admin_profile_update").on('submit',(e)=>{
    e.preventDefault();
    const data=getformdata("admin_profile_update");
    updateAdminProfile('post','/admin/updateProfile',data);
});


    // 
/***********************************Add Streamer*********************************************/
$("#create_streamer #update_streamer").on('submit',(e)=>{
    e.preventDefault();
    // console.log(this.id);
    // let streamer_id = document.querySelector("#streamer_id").value;
    let data = new FormData();
    data.append('first_name',document.querySelector("#first_name").value);
    data.append('last_name',document.querySelector("#last_name").value);
    data.append('email',document.querySelector("#email").value);
    data.append('phone',document.querySelector("#phone").value);
    data.append('gender',document.querySelector('input[name="gender"]:checked').value);
    data.append('streamer_dob',document.querySelector("#streamer_dob").value);
    if ($('#profile_image')[0].files.length > 0) {
        data.append('profile_image', $('#profile_image')[0].files[0]);
    }
    data.append('_token',$('meta[name="csrf-token"]').attr('content'));
 
    // if (this.id == 'create_streamer') {
        addStreamerProfile('post','/admin/streamer/add',data);
    // }else{
    //     updateStreamerProfile('post','/admin/streamer/update/', streamer_id ,data);
    // }
    
});

$("#create_agency").on("submit",(e)=>{
    e.preventDefault();
    // const data=getformdata("create_agency");
  
    let data = new FormData();
    data.append('name',document.querySelector("#name").value);
    data.append('email',document.querySelector("#email").value);
    data.append('country_code',document.querySelector("#country_code").value);
    data.append('phone_number',document.querySelector("#phone_number").value);
    data.append('country',document.querySelector("#country").value);
    data.append('state',document.querySelector("#state").value);
    data.append('city',document.querySelector("#city").value);
    data.append('address',document.querySelector("#address").value);
    data.append('license_id',document.querySelector("#license_id").value);
    if ($('#license_file')[0].files.length > 0) {
        data.append('image', $('#license_file')[0].files[0]);
    }
    data.append('legal_advisor',document.querySelector("#legal_advisor").value);
    data.append('business_type',document.querySelector("#business_type").value);
    data.append('password',document.querySelector("#password").value);
    data.append('confirm_password',document.querySelector("#confirm_password").value);
    data.append('_token',$('meta[name="csrf-token"]').attr('content'));
    AgencyAjax('post','/admin/agency/add',data);
});
//******************************* Ajax ******************************************//
function permissionAjax(type,path,data){
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        header:{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        },
        data:data,
        success:function(data){
            removemessage('error');
            window.location.reload();
        },
        error:function(data){
            errormessage('error_title',data.responseJSON.errors.title);
            errormessage('error_description',data.responseJSON.errors.description);
        }
      });
}  
function AgencyAjax(type,path,data){
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        dataType: "JSON",       
       xhr: function() {
             myXhr = $.ajaxSettings.xhr();
             return myXhr;
       },
       cache: false,
       contentType: false,
       processData: false,
       data:data,
        success:function(data){
            removemessage('error');
            window.location.reload();
        },
        error:function(data){
            errormessage('error_name',data.responseJSON.errors.name);
            errormessage('error_email',data.responseJSON.errors.email);
            errormessage('error_country_code',data.responseJSON.errors.country_code);
            errormessage('error_phone_number',data.responseJSON.errors.phone_number);
            errormessage('error_country',data.responseJSON.errors.country);
            errormessage('error_state',data.responseJSON.errors.state);
            errormessage('error_city',data.responseJSON.errors.city);
            errormessage('error_address',data.responseJSON.errors.address);
            errormessage('error_license_id',data.responseJSON.errors.license_id);
            errormessage('error_license_file',data.responseJSON.errors.license_file);
            errormessage('error_legal_advisor',data.responseJSON.errors.legal_advisor);
            errormessage('error_business_type',data.responseJSON.errors.business_type);
            errormessage('error_password',data.responseJSON.errors.password);
            errormessage('error_confirm_password',data.responseJSON.errors.confirm_password);
        }
      });
}  

    // const data=getformdata("admin_profile_update");
  
    // updateAdminProfile('post','/admin/updateProfile',data);

function updateAdminProfile(type,path,data){
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        dataType: "JSON",       
       xhr: function() {
             myXhr = $.ajaxSettings.xhr();
             return myXhr;
       },
       cache: false,
       contentType: false,
       processData: false,
        data:data,
        success:function(data){
            removemessage('error');
            // location.reload();
        },
        error:function(data){
            errormessage('first_name_error',data.responseJSON.errors.first_name);
            errormessage('last_name_error',data.responseJSON.errors.last_name);
            errormessage('email_error',data.responseJSON.errors.email);
            errormessage('phone_number_error',data.responseJSON.errors.phone_number);
            errormessage('password_error',data.responseJSON.errors.password);
            errormessage('confirm_password_error',data.responseJSON.errors.confirm_password);
        }
      });
} 
/*************************************************************************/
function addStaffProfile(type,path,data){
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        header:{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        },
        data:data,
        success:function(data){
            removemessage('error');
            window.location.reload();
        },
        error:function(data){
            errormessage('error_first_name',data.responseJSON.errors.first_name);
            errormessage('error_last_name',data.responseJSON.errors.last_name);
            errormessage('error_email',data.responseJSON.errors.email);
            errormessage('error_phone',data.responseJSON.errors.phone);
            errormessage('error_country_code',data.responseJSON.errors.country_code);
          
        }
      });
} 
/*************************************************************************/
function addStreamerProfile(type,path,data){
    console.log(data);
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        dataType: "JSON",       
        xhr: function() {
              myXhr = $.ajaxSettings.xhr();
              return myXhr;
        },
        cache: false,
        contentType: false,
        processData: false,
        data:data,
        success:function(data){
            removemessage('error');
            window.location.reload();
        },
        error:function(data){
            errormessage('error_first_name',data.responseJSON.errors.firstName);
            errormessage('error_last_name',data.responseJSON.errors.lastName);
            errormessage('error_email',data.responseJSON.errors.email);
            errormessage('error_phone',data.responseJSON.errors.phone);
            errormessage('error_gender',data.responseJSON.errors.gender);
            errormessage('error_streamer_dob',data.responseJSON.errors.streamer_dob);
            errormessage('error_profile_image',data.responseJSON.errors.profile_image);
            // errormessage('error_country_code',data.responseJSON.errors.country_code);
            
        }
      });
} 
//**********************************Streamer dropdown Filter **************************//
function ajaxStreamerTabs(type,path,data,query,selected_tab){
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        header:{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        },
        data:data,
        success:function(data){
            $(`${query}`).empty().html(data);
            document.querySelector(".selected_heading").innerText=selected_tab;
        }
      });
}

//**********************************Withdraw request dropdown Filter **************************//
function ajaxWithdrawTabs(type,path,data,query,selected_tab){
    let selected_heading_text = selected_tab+' Transactions';
    $.ajax({
        type:type,
        url:`${base_url}${path}`,
        header:{
          'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
        },
        data:data,
        success:function(data){
            $(`${query}`).empty().html(data);
            document.querySelector(".selected_heading").innerText= selected_heading_text;
        }
      });
}

/*************************************************************************/
// function updateStreamerProfile(type,path,id,data){
//     console.log(data);
//     $.ajax({
//         type:type,
//         url:`${base_url}${path}`+id,
//         dataType: "JSON",       
//         xhr: function() {
//               myXhr = $.ajaxSettings.xhr();
//               return myXhr;
//         },
//         cache: false,
//         contentType: false,
//         processData: false,
//         data:data,
//         success:function(data){
//             removemessage('error');
//             // window.location.reload();
//         },
//         error:function(data){
//             errormessage('error_first_name',data.responseJSON.errors.firstName);
//             errormessage('error_last_name',data.responseJSON.errors.lastName);
//             errormessage('error_email',data.responseJSON.errors.email);
//             errormessage('error_phone',data.responseJSON.errors.phone);
//             errormessage('error_gender',data.responseJSON.errors.gender);
//             errormessage('error_streamer_dob',data.responseJSON.errors.streamer_dob);
//             errormessage('error_profile_image',data.responseJSON.errors.profile_image);
//             // errormessage('error_country_code',data.responseJSON.errors.country_code);
            
//         }
//       });
// } 
//***************************** Ajax Helper Functions ***************************//
function ajax(type='get',url,data,id){
    $.ajax({
        type: type,
        url: url,
        data: data,
        headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        success: function(data){
          $(`#${id}`).empty().html(data);
        }
       });
}
//*****************************Helper function **********************************//
function getformdata(id){
    let form = document.querySelector(`#${id}`);
    let data = new FormData(form);
    var object={};
    for (let [key, value] of data) {
        object[key]=value;
    }
    var FormValuedata=object;
    return FormValuedata;
   }


function  returnresult(data){
    return data;
}
function errormessage(id,value){
    if(value){
        document.querySelector(`#${id}`).innerText=value;
      }else{
        document.querySelector(`#${id}`).innerText="";
      }
}
function removemessage(value){
    $(`.${value}`).empty();
}
function appendData(data,id,name){
   return data.append(name,document.querySelector(`#${id}`));
}

//*****************************Revenue Section **********************************//


    jQuery("#info_box_3, #back_to_streamers").on("click",(e)=>{
    // console.log('hi')
    $.ajax({
    url: `${base_url}`+'/admin/revenue/streamers',
    type: "get", 
    success: function(response, textStatus, jqXHR) {
        // $("#infobox_section").css("display", "none");
        // $("#search_section").show();
        $('#main_contents').empty().html(response);

    	// console.log(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
		console.log(jqXHR);
      	console.log(textStatus);
      	console.log(errorThrown);
    }
});
});

function ajaxStreamerProfile(type='get',url,data,id ){
    $.ajax({
        type: type,
        url: url,
        data: data,
        headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        success: function(data){
          $(`#${id}`).empty().html(data);
        }
       });
}


function ajaxAgencyProfile(type='get',url,data,id ){
    $.ajax({
        type: type,
        url: url,
        data: data,
        headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
        success: function(data){
          $(`#${id}`).empty().html(data);
        }
       });         
}

// function ajaxAgencyStreamers(type='get',url,data,id ){
//     $.ajax({
//         type: type,
//         url: url,
//         data: data,
//         headers: {
//              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//          },
//         success: function(data){
         

//           $(`#${id}`).empty().html(data);
//         }
//        });
// }

jQuery("#info_box_2").on("click",(e)=>{
    // console.log('hi')
    $.ajax({
    url: `${base_url}`+'/admin/revenue/agencies',
    type: "get", 
    success: function(response, textStatus, jqXHR) {
        $('#main_contents').empty().html(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
		console.log(jqXHR);
      	console.log(textStatus);
      	console.log(errorThrown);
    }
});
});