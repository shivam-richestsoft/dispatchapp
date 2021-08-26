<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Paginate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Arr;


//models
use App\Models\User;
use App\Models\AppLogin;
use App\Models\EmploymentHistory as EH;
use App\Models\Project;
use App\Models\Socialmedia;
use App\Models\Language;
use App\Models\Skill;
use App\Models\UserSkill;
use App\Models\PasswordReset;
use App\Models\Expertise;
use App\Models\FriendRequest;
use App\Models\FriendList;
use App\Models\Post;
use App\Models\Notification;
use App\Models\File;
use App\Models\Favourite;
use App\Models\Thumbsup;
use App\Models\SinglePostFav;
use App\Models\SinglePostThumbs;
use App\Models\FileReaction;
use App\Models\PostReaction;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\FileComment;
use App\Models\FileCommentReply;
use App\Models\PostViews;
use App\Models\FileViews;

//additional
use DB;
use Carbon\Carbon;
use Validator;
use Session;

//traits
use App\Traits\ApiResponser;
use App\Traits\ImageUpload;
use App\Traits\Email;
use App\Traits\Togglestatus;

//requests
use App\Http\Requests\UserRequest;
use App\Http\Requests\ProjectRequest;
use App\Http\Requests\EmploymentHistoryRequest;
use App\Http\Requests\FriendRequestValidate;
use App\Http\Requests\SearchUserRequest;
use App\Http\Requests\readnotification;
use App\Http\Requests\Postid;
use App\Http\Requests\SingleLikeThumbs;
use App\Http\Requests\ReactionPost;
use App\Http\Requests\ReactionFile;
use App\Http\Requests\CommentPost;
use App\Http\Requests\PostCommentReply;
use App\Http\Requests\FieldIdCheck;
use App\Http\Requests\PostIdCheck;
//events
use App\Events\Notify;


class UserController extends Controller
{   
    const BUSINESS_ROLE_TRUE = 1;
    const BUSINESS_ROLE_FALSE = 0;

    const USER_ROLE_TRUE = 1;  
    const USER_ROLE_FALSE = 0;  

    const FIRST_LOGIN_TRUE = 1;  
    const FIRST_LOGIN_FALSE = 0;  

    const REQUEST_ACCEPTED = 1;
    const REQUEST_REJECTED = 2;
    const REQUEST_PENDING = 0;

    use ApiResponser;
    use ImageUpload;    
    use Email;
    use Togglestatus;      
    
// showing error when user not logged in    
public function loginCheck(request $r){
    try {
        return $this->success(false,'Please login to access this page',403);
    } catch (\Exception $e) {
        return $this->error('Please check your fields');
    }
}
//logout current device    
public function logout(request $r)
{
    try {
        $r->user()->currentAccessToken()->delete();
        return $this->success(true,'Successfully loggged out');
    } catch (\Exception $e) {
        return $this->error('Please check your fields');
    }
}
// logout all device
public function logoutAll()
{
    try {
        auth()->user()->tokens()->delete();
        return $this->success(true,'Successfully loggged out all devices');
    } catch (\Exception $e) {
        return $this->error('Please check your fields');
    }
}

// register for business and individual
public function register(Request $r){
   // die;
    try {
        $business_role = self::BUSINESS_ROLE_FALSE;
        $user_role = self::USER_ROLE_FALSE;
        !empty($r->business_name)?$business_role = self::BUSINESS_ROLE_TRUE:$user_role = self::USER_ROLE_TRUE;
        $register = User::create(['name'=>$r->name,'email'=>$r->email,'phone'=>$r->phone,'business_name'=>$r->business_name,
        'is_individual'=>$user_role,'is_business'=>$business_role,'password'=>Hash::make($r->password)]);

        return $this->success(true,'Registration successful');          
    } catch (\Throwable $e) {
        return $this->error($e->getMessage());
    }
}
//addtional profile details for individual and businesss
public function additionalInfo(request $r){
    try {
        $user = auth()->user();
        
        $business_role = self::BUSINESS_ROLE_FALSE;
        $user_role = self::USER_ROLE_FALSE;
        !empty($r->business_name)?$business_role = self::BUSINESS_ROLE_TRUE:$user_role = self::USER_ROLE_TRUE;
        if ($r->hasFile('profile_image')) {
            $profile_image = $this->UploadImage($r->profile_image);
        }
        if ($r->hasFile('business_image')) {
            $business_image = $this->UploadImage($r->business_image);
        }
        if ($r->hasFile('cover_image')) {
            $cover_image = $this->UploadImage($r->cover_image);
        }
        
        $register = User::where('id',$user->id)->update(['specialization'=>$r->specialization,'business_intro'=>$r->business_intro,'expertise'=>$r->expertise,'work'=>$r->work,
        'profile_image'=>$profile_image??$user->profile_image,'business_image'=>$business_image??$user->business_image,'cover_image'=>$cover_image??$user->cover_image,'address'=>$r->address,
        'latitude'=>$r->latitude,'longitude'=>$r->longitude,'language_ids'=>$r->language]);
 
        !empty($r->skill)?UserSkill::updateOrCreate(['user_id'=>$user->id],['user_id'=>$user->id,'skill_ids'=>$r->skill]):'';
        return $this->success(true,'General Information Updated Successfully');          

    } catch (\Throwable $e) {
        return $this->error($e->getMessage());
    }
}
//update profile for both users
public function updateProfile(UserRequest $r){
    try {
        $user = Auth::user(); 
        $update = User::where('id',$user->id)
        ->update(['name'=>$r->name,'email'=>$r->email,
        'phone'=>$r->phone,'business_name'=>$r->business_name]);
        return $this->success(true,'Profile updated successfully');          
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//login for both
public function login(request $r){
   try {
    $login = $r->validate([
        'email' => 'required',
        'password' => 'required',
    ]);
    if (Auth::attempt($login)) {
        $user = auth()->user();
        $first_time_login = false;
        if (auth()->user()->first_time_login == self::FIRST_LOGIN_FALSE) {
            $first_time_login = true;
            $user->update(['first_time_login'=>self::FIRST_LOGIN_TRUE]);
        }
        $token = $user->createToken('API Token')->plainTextToken;
            $profileDetails['is_business'] = $business_status??false;
            $profileDetails['is_individual'] = $user_status??false;
            $business_status = false;
            $user_status = false;
            $user->is_business == 1?$business_status = true: $user_status = true;
            $list = [];
            $list['token'] =  $token;
            $list['first_time_login'] =  $first_time_login;
            $list['name'] =  $user->name;
            $list['profile_image'] =  $user->profile_image;
            $list['is_business'] = $business_status??false;
            $list['is_individual'] = $user_status??false;
            return $this->successWithData($list); 
    }else{
        return $this->error('Invalid credentials');
    }
   } catch (\Throwable $e) {
    return $this->error($e->getMessage());  
   }
}
//get all profile including additional info of both users
public function getProfile(request $r){
    
    $profileDetails = [];
    $languages = [];
    $skills = [];
   try {
    $data = User::profileResponse(auth()->user()->id);
    if (!empty($data)) {
        return $this->successWithData($data); 
    }else {
        return $this->success(false); 
    }
   } catch (\Throwable $e) {
    return $this->error($e->getMessage());  
   }
}
//fetch language list
public function languageList(request $r){
    
   try {
    $languages = Language::select('id','name')->get();
    if ($languages->isNotEmpty()) {
        return $this->successWithData($languages); 
    }else {
        return $this->success(false,'No language found'); 
    }
   } catch (\Throwable $e) {
    return $this->error($e->getMessage());  
   }
}
//fetch skills list
public function skillsList(request $r){
    
    try {

     $skills = Skill::select('id','name')->get();

     if ($skills->isNotEmpty()) {
         return $this->successWithData($skills); 
     }else {
         return $this->success(false,'No skills found'); 
     }
    } catch (\Throwable $e) {
     return $this->error($e->getMessage());  
    }

 }
 //fetching specialization list
 public function expertiseList(request $r){
    
    try {

     $expertise = Expertise::select('id','name')->get();

     if ($expertise->isNotEmpty()) {
         return $this->successWithData($expertise); 
     }else {
         return $this->success(false,'No skills found'); 
     }
    } catch (\Throwable $e) {
     return $this->error($e->getMessage());  
    }

 }
// forget password 
public function forgotPassword(request $r){
    $v = Validator::make ( $r->input (),
    [
         'email' => 'required|email|max:255',                         
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
   try {
    $user = User::where('email',$r->email)->first();
    if (!empty($user)) {
        $token = Str::random(20);
        $updated = PasswordReset::updateOrCreate(['email'=>$r->email],['email'=>$r->email,'token'=>$token]);
        if ($this->sendResetEmail($user,$token)) {
            return $this->success(true,'Password reset link has been sent to your registered email id.'); 
        } else {
            return $this->success(false);  
        }   
    } else {
        return $this->success(false,'This email is not registered yet'); 
    }
   } catch (\Throwable $e) {
    return $this->error($e->getMessage());       
 }
    
}
// changePassword
public function changePassword(request $r){
    $v = Validator::make ( $r->input (),
    [
        'token' => 'required',
        'password' => 'required',                     
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
        $token = $r->token;
        $email = PasswordReset::where('token',$r->token)->pluck('email')->first();
        if (empty($email)) {
            return $this->success(false,'You have not requested for password change'); 
        }else {
            $updated = User::where('email',$email)->update(['password'=>md5($r->password)]);
            if ($updated) {
                PasswordReset::where('token',$r->token)->delete();
                return $this->success(true,'Password changed successfully'); 
            }else {
                return $this->success(false);   
            }
        }
}
//send invitation
public function invite(request $r){
    $v = Validator::make ( $r->input (),
    [
        'email_id' => 'required',          
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
    try {
        $userId = auth()->user()->id; 
        $username = User::where('id',$userId)->pluck('name')->first();

        $email_ids = explode(',',$r->email_id);

        foreach ($email_ids as $email_id ) {
            $this->inviteMail($username,$email_id,$r->message??'');
        }
        return $this->success(true,'Invitation sent'); 
    } catch (\Throwable $e) {
        return $this->error($e->getMessage());
 }
}
public function addProjects(ProjectRequest $r){
    try {
            $userId = auth()->user()->id; 
            $current_project = $r['current_project']??0;
            $project = Project::create([
                'user_id'=>$userId,
                'title'=>$r['title'],
                'from_date'=>$r['from_date'],
                'to_date'=>$r['to_date'],
                'job_type'=>$r['job_type'],
                'address'=>$r['address'],
                'latitude'=>$r['latitude'],
                'longitude'=>$r['longitude'],
                'description'=>$r['description'],
                'current_project'=>$current_project,
                "raw_from_date"=> $r['raw_from_date'],
                "raw_to_date"=> $r['raw_to_date'],
                
            ]);
        return $this->success(true,'Project added successfuly');           
    } catch (\Throwable $e) {
        return $e->getMessage();
        // DB::rollback();
        return $this->error('Please check your fields');
    }
}
//update project 
public function updateProject(request $r){
    $v = Validator::make ( $r->input (),
    [
         'project_id' => 'required',
         'from_date' => 'required|date_format:m/Y|before:today|',
         'to_date' => 'sometimes|date_format:m/Y|before:today|after:from_date|nullable',
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
    try {
            $data = [
                'title'=>$r->title,
                'from_date'=>$r->from_date,
                'to_date'=>$r->to_date,
                'job_type'=>$r->job_type,
                'address'=>$r->address,
                'latitude'=>$r->latitude,
                'longitude'=>$r->longitude,
                'description'=>$r->description,
                'current_project'=>$r->current_project,
                "raw_from_date"=> $r->raw_from_date,
                "raw_to_date"=> $r->raw_to_date,
            ];

            $project = Project::where('id',$r->project_id)->update($data);

 
            return $this->success(true,'Project details updated');         
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//delete project
public function deleteProject(request $r){
    $v = Validator::make ( $r->input (),
    [
         'project_id' => 'required',
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
    try {
        $project = Project::where('id',$r->project_id)->delete();
        if ($project) {
            return $this->success(true,'Project deleted successfully');           
        }else {
            return $this->success(false);         
        }
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//projects list
public function projectsListing(){
    try {
        $data = Project::ProjectsResponse(Auth::user()->id);
        if(!empty($data)){
            return $this->successWithData($data);           
        }else{
            return $this->successWithData([],'No project found');         
        }
    } catch (\Throwable $e) {
        return $this->error('Something went wrong!');
    }
}
//add employment
public function addEmployment(EmploymentHistoryRequest $r){
    try {
        $userId = Auth::user()->id;
        $current_job_status = EH::where([['current_job',1],['user_id',$userId]])->get();
        // dd($current_job_status);
        if ($current_job_status->isNotEmpty() && $r->current_job==1) {
            return $this->success(false,'Sorry! you can only select one employment as current job');                            
        }

            $current_job = $r['current_job']??0;
            $project = EH::create([
                'user_id'=>$userId,
                'company_name'=>$r['company_name'],
                'from_date'=>$r['from_date'],
                'to_date'=>$r['to_date'],
                "raw_from_date"=> $r['raw_from_date'],
                "raw_to_date"=> $r['raw_to_date'],
                'position'=>$r['position'],
                'address'=>$r['address'],
               'latitude'=>$r['latitude'],
               'longitude'=>$r['longitude'],
                'description'=>$r['description'],
                'current_job'=>$current_job
            ]);
        
        return $this->success(true,'Employment details Added'); 
    } catch (\Throwable $e) {
        return $e->getMessage();
       // return $this->error('Please check your fields');
    }
}
//update employment
public function updateEmployment(request $r){
    $v = Validator::make ( $r->input (),
    [
        'emp_id' => 'required', 
        'from_date' => 'required|date_format:m/Y|before:today|',
        'to_date' => 'sometimes|date_format:m/Y|before:today|after:from_date|nullable',
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
    try {
        $employment_details = EH::where('id',$r->emp_id)->first();
            $employment_details = EH::where('id',$r->emp_id)->update([
            'company_name'=>$r->company_name,
            'from_date'=>$r->from_date,
            'to_date'=>$r->to_date,
            'position'=>$r->position,
            'address'=>$r->address,
            'latitude'=>$r->latitude,
            'longitude'=>$r->longitude,
            'description'=>$r->description,
            'current_job'=>$r->current_job,
            "raw_from_date"=> $r->raw_from_date,
            "raw_to_date"=> $r->raw_to_date,
            ]);
            return $this->success(true,'Employment details updated');         

    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//employment list
public function employmentsListing(request $r){
    try {
        $data  = EH::EmploymentsResponse(Auth::user()->id);
        if(!empty($data)){  
            return $this->successWithData($data,'Employment details fetched'); 
        }else {
            return $this->successWithData([],'No data found');              
        }
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
   
}
//delete employment
public function deleteEmployment(request $r){
    $v = Validator::make ( $r->input (),
    [
         'emp_id' => 'required',
    ] );
    if ($v->fails ()) {
        return $this->validation($v);
    }
    try {
        $employment = EH::where('id',$r->emp_id)->delete();
        return $this->success(true,'Employment deleted successfully');           
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//add social media account
public function addSocial(request $r){
    try {
        $userId = auth()->user()->id; 
        $project = SocialMedia::updateOrCreate(['user_id'=>$userId],['user_id'=>$userId,
        'fb'=>$r->facebook,'twitter'=>$r->twitter,'youtube'=>$r->youtube,'linkedIn'=>$r->linkedIn]);
        return $this->success(true,'Social media account added successfuly');           
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}
//getting social accounts
public function getSocial(request $r){
    
    try {
        $data = $this->getUserSocial(Auth::user()->id);
        return $this->successWithData($data); 
    } catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }

 }
//search for users     
public function searchUser(SearchUserRequest $r){
    try {
        $user_id = Auth::user()->id;
       $search_result = User::where([['name','LIKE','%'.$r->search.'%'],['id','!=',$user_id]])
       ->orWhere([['email','LIKE','%'.$r->search.'%'],['id','!=',$user_id]])
       ->orWhere([['phone','LIKE','%'.$r->search.'%'],['id','!=',$user_id]])
       ->orWhere([['business_name','LIKE','%'.$r->search.'%'],['id','!=',$user_id]])->get();
       if ($search_result->IsNotEmpty()) {
            foreach ($search_result as $key => $value) {
                //get profile details of searched users
                $data[] = User::profileResponse($value->id,$user_id);
                    }
            return $this->successWithData($data); 
        } else {
            return $this->successWithData([],'No data found');              
        }
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//send friend request
public function sendRequest(FriendRequestValidate $r){
    try {
        $user_id = Auth::user()->id;
        $request=FriendRequest::where('user_id',$user_id)->where('to_id',$r->id)->where('status',self::REQUEST_PENDING)->first();
        // $request = FriendRequest::where([['user_id',$user_id],['to_id',$r->id]])->where('status',self::REQUEST_PENDING)->first();
        if (!empty($request)) { 
            return $this->success(true,'Friend request already sent'); 
        } else {
            $request = new FriendRequest;
            $request->user_id = $user_id;
            $request->to_id = $r->id;
            $request->save();
            $lastId = $request->id;
            $name=Auth::user()->name;
            if(Auth::user()->is_business==1){
                $name=Auth::user()->business_name;
            }
            $notify_data = [$user_id,$r->id,'New Friend Request!',$name.' would like to link-up with you',$lastId,'App\Models\FriendRequest'];
            event(new Notify($notify_data));
            return $this->success(true,'Friend request sent successfully');    
        }
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//cancel friend request
public function deleteRequest(FriendRequestValidate $r){
    try {
        $user_id = Auth::user()->id;
        $delete_request = FriendRequest::where([['user_id',$user_id],['to_id',$r->id]])->delete();
        $deletenotif=Notification::where([['user_id',$user_id],['to_user',$r->id]])->delete();
        return $this->success(true,'Friend request cancelled'); 
    }catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//show pending requests
public function pendingRequest(request $r){
    try {
        $user_id = Auth::user()->id;
        $requests = FriendRequest::where('to_id',$user_id)->where('status',0)->get();
        $data = [];
        foreach ($requests as $key => $value) {
            $data[] = User::profileResponse($value->user_id,);
        }
        if (!empty($data)) {
            return $this->successWithData($data); 
        } else {
            return $this->successWithData($data,'No requests available'); 
        }
    } catch (\Throwable $e) {
        return $this->error($e->getMessage());
    }

 }
//show my sent requests
public function sentRequest(request $r){
    
    try {
        $user_id = Auth::user()->id;
        $requests = FriendRequest::where('user_id',$user_id)->get();
        foreach ($requests as $key => $value) {
            $data[] = User::profileResponse($value->user_id);
        }
        if (!empty($data)) {
            return $this->successWithData($data ); 
        } else {
            return $this->success(true,'No requests available');    
        }
    } catch (\Throwable $e) {
        return $this->error($e->getMessage());
    }

 }
// accept request 
public function acceptRequest(FriendRequestValidate $r){
    try {
        $user_id = Auth::user()->id;
        
        $friend_list = new FriendList;
        $friend_list->user_id = $user_id;
        $friend_list->to_id =  $r->id;
        $saved = $friend_list->save();
        if ($saved) {
            //change status of request
            $accepted = FriendRequest::where('user_id',$r->id)->delete();
            $model_id = $friend_list->id;
            //delete notification for pending request
            Notification::where('user_id',$r->id)->delete();
            $name=Auth::user()->name;
            if(Auth::user()->is_business==1){
                $name=Auth::user()->business_name;
            }
            $notify_data = [$user_id,$r->id,'Your friend request has been accepted!',Auth::user()->name.' has accepted your link-up request',$model_id,'App\Models\FriendList'];
            event(new Notify($notify_data));
            return $this->success(true,'Friend request accepted');    
        }
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
// decline link up request 
public function cancelRequest(FriendRequestValidate $r){
    try {
         
        $cancel = FriendRequest::where('user_id',$r->id)->delete();
        //delete notification for pending request
        Notification::where('user_id',$r->id)->delete();
        return $this->success(true,'Friend request declined successfully');    
        
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//unlink connection between two
public function unfriend(FriendRequestValidate $r){
    try {
        $user_id = Auth::user()->id;
        
        FriendList::where('user_id',$r->id)->orWhere('to_id',$r->id)->delete();
        $user = User::where('id',$r->id)->select('name')->first();
        return $this->success(true,'You and '.$user->name.' are no more friends');    
        
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}

//get all details --- complete profile, employment or project history with just user id
public function getAdditionalDetails(request $r){
    try {
        $user_id = $r->id;
        $user = User::where('id',$user_id)->select('is_business')->first();
        $data = [];
        $data['profile_details'] = User::profileResponse($user_id,Auth::user()->id);
        $data['social_details'] = $this->getUserSocial($user_id);
        if ($user->is_business==0) {
            $data['employment_details'] = EH::EmploymentsResponse($user_id);
        } else {
            $data['project_details'] = Project::ProjectsResponse($user_id);
        }
        return $this->successWithData($data); 
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//get notifications for user
public function notifications(request $r){
    try {
        if (!empty($r->id)) {
            Notification::where('id',$r->id)->delete();
        }
        $paginator = Notification::where('to_user',Auth::user()->id)->paginate(20);
        $isReadCheck=Notification::where('to_user',Auth::user()->id)->where('is_read',0)->count();
        $collection=[
            "is_new"=>!empty($isReadCheck)?true:false
        ];
        return $this->customPaginator($paginator,$collection);
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//make notification as is readed
public function isRead(request $r){
    $notifications = Notification::where('to_user',Auth::user()->id)->update(['is_read',1]);
}
//used to post anything on timeline video,picture,text
public function postSomething(request $r){
    try {
    DB::beginTransaction();
    $post = new Post;
    $post->user_id = Auth::user()->id;
    $post->visibility = $r->visibility;
    $post->text = $r->text;
    $post->posted_at = now();
    $post->save();
     if($r->images>0){
         for($i=0;$i<$r->images;$i++){
           
            $file_name=$this->UploadImage($r->file('image_'.strval($i)));
            $extension =$r->file('image_'.strval($i))->getClientOriginalExtension();
            $file_size =$r->file('image_'.strval($i))->getSize();
            $file = new File;
            $file->file_name = $file_name;
            $file->model_id = $post->id;
            $file->model_type = 'App/Models/Post';
            $file->extension = $extension;
            $file->file_size = $file_size;
            $file->save();
             
         }
     }
     if($r->videos>0){
        for($i=0;$i<$r->videos;$i++){
            $file_name=$this->UploadImage($r->file('video_'.strval($i)));
            $extension = $r->file('video_'.strval($i))->getClientOriginalExtension();
            $file_size = $r->file('video_'.strval($i))->getSize();
            $file = new File;
            $file->file_name = $file_name;
            $file->model_id = $post->id;
            $file->model_type = 'App/Models/Post';
            $file->extension = $extension;
            $file->file_size = $file_size;
            $file->save();
         }
     }
     $data=Post::select('posts.id as post_id','posts.text as post_text','posts.created_at as postdate','users.name as user_name','users.profile_image as profile','users.is_business','users.business_name','users.business_image','users.specialization','users.work')->
     Join('users','users.id','=','posts.user_id')->where('posts.id',$post->id)->first();
    DB::commit();
    return $this->success($data->postdata(),'Post added successfully');    
    } catch (\Throwable $th) {
        DB::rollBack();
        return $this->error($th->getMessage());
    }
}
public function timeline(){
    $posts = Auth::user()->posts;
    foreach ($posts as $post) {
       $data[] = $post->jsonData();
    }
    dd($data);
}
//get friends list
 public function friendsList(){
    $user_id = Auth::user()->id;
    $friends = FriendList::where('user_id',$user_id)->orWhere('to_id',$user_id)->get();
    foreach ($friends as $key => $value) {
        $profiles[] = User::profileResponse($value->to_id);
    }
    return $this->successWithData($profiles); 
 }

// get post 
public function getpost(){
    try {
        $user_id = Auth::user()->id;
        $data=FriendList::select('posts.id as post_id','posts.text as post_text','posts.created_at as postdate','posts.view_count','users.name as user_name','users.profile_image as profile','users.is_business','users.business_name','users.business_image','users.specialization','users.work','users.id as user_id')->
        join('posts', function ($join) {
            $join->on('posts.user_id', '=', 'friend_lists.user_id')->orOn('posts.user_id', '=', 'friend_lists.to_id');
        })->
        Join('users','users.id','=','posts.user_id')
        ->where('friend_lists.user_id',$user_id)
        ->orwhere('friend_lists.to_id',$user_id)
        // ->orWhere('friend_lists.user_id',$user_id)
        ->orderBy('posts.id','DESC')
        ->distinct('posts.id')
        ->paginate(15);
        return $this->customPaginator($data);
        $posts=[];
        $count=0;
        foreach($data as $value){
            array_push($posts,$value->json());
            $count++;
        }
        return $this->success($posts,$count.' posts');
     }catch (\Throwable $e) {
        return $this->error('Please check your fields');
    }
}   
//get file
public function GetFile(request $request){
    $data=File::select('files.id as file_id','files.file_name','files.extension','users.id as user_id','users.name as user_name','posts.id as post_id')->Join('posts','posts.id','=','files.model_id')->Join('users','users.id','=','posts.user_id')->where('files.model_id',$request->post_id)->paginate(15);
    return $this->customPaginator($data);
}
//On Screen Notification
public function onScreenNotification(){
    try {
        $user_id = Auth::user()->id;
        $data=Notification::where('is_read',0)->where('to_user',$user_id)->orderBy('notifications.id','DESC')->limit(5)->get();
        $count=Notification::where('to_user',$user_id)->count();
        $notification=[];
        foreach($data as $value){
            array_push($notification,$value->jsonData());
        }
        return $this->withsuccess($notification,$count); 
    } catch (\Throwable $th) {
        return $this->error($th->getMessage());
    }
}
//Read Notification
public function readnotificaton(request $request){
    try{
        $user_id = Auth::user()->id;
        $update=Notification::where('to_user',$user_id)->update([
           "is_read"=>1
        ]);
        if($update){
            return $this->success(true,'All Notifcations are readed',200);
        }else{
            return $this->success(false,'Oops! something went wrong',400);
        }
    }catch(\Throwable $th){
        return $this->error($th->getMessage());
    }
}
//post like and dislike 
public function LikeAndDislike(Postid $request){
    try{
   $message=$this->toggledata('Favourite',$request->post_id,$request->likecheck);
   return $this->success(true,$message,200);
   }catch(\Throwable $th){
       return $this->error($th->getMessage());
   }
}

//post thumbsup and down 
public function ThumbsUpandDown(Postid $request){
    try{
       $message=$this->toggledata('Thumbsup',$request->post_id,$request->likecheck);
       return $this->success(true,$message,200);
       }catch(\Throwable $th){
           return $this->error($th->getMessage());
       }
}

//post single files like
public function FilesLikeAndDislike(SingleLikeThumbs $request){
    try{
        $message=$this->singletoggledata('SinglePostFav',$request->post_id,$request->file_id,$request->likecheck);
        return $this->success(true,$message,200);
        }catch(\Throwable $th){
            return $this->error($th->getMessage());
        }
}

//post single thumbs like
public function FilesThumbsUpandDown(SingleLikeThumbs $request){
    try{
        $message=$this->singletoggledata('SinglePostThumbs',$request->post_id,$request->file_id,$request->likecheck);
        return $this->success(true,$message,200);
        }catch(\Throwable $th){
            return $this->error($th->getMessage());
        }
}
//post reaction
public function PostReact(ReactionPost $request){
  try{
    $message=$this->PostReaction($request->post_id,$request->reaction);
    return $this->success(true,$message,200);
  }catch(\Throwable $th){
      return $this->error($th->getMessage());
  }
}
//file Reaction
public function FileReact(ReactionFile $request){
    try{
        $message=$this->FileReaction($request->post_id,$request->file_id,$request->reaction);
       
        return $this->success(true,$message,200);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//Post Comment
public function PostComment(CommentPost $request){
    try{
        $comment=new Comment;
        $comment->post_id=$request->post_id;
        $comment->user_id=Auth::user()->id;
        $comment->comment=$request->comment;
        $comment->save();
        $data['post_id']=$request->post_id;
        $data['comment_id']=$comment->id;
        $data['comment']=$request->comment;
        $user=User::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->where('id',Auth::user()->id)->first();
        
        $is_individual=1;
        $image=$user->profile_image;
        if($user->is_business == 1)
        {
            $is_individual=0;
            $image=$user->business_image;
        }
        $data['is_individual']=$is_individual;
        $data['image']=$image??"";
        $data['reply']=0;
        $data['date']=$comment->created_at;
        $message=$data;
        return $this->successWithData($message);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//File Comment 
public function FileComment(request $request){
     try{
        $comment=new FileComment;
        $comment->post_id=$request->post_id;
        $comment->user_id=Auth::user()->id;
        $comment->file_id=$request->file_id;
        $comment->comment=$request->comment;
        $comment->save();
        $data=[];
        $data['post_id']=$request->post_id;
        $data['file_id']=$request->file_id;
        $data['comment_id']=$comment->id;
        $data['comment']=$request->comment;
        $user=User::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->where('id',Auth::user()->id)->first();
        
        $is_individual=1;
        $image=$user->profile_image;
        if($user->is_business == 1)
        {
            $is_individual=0;
            $image=$user->business_image;
        }
        $data['is_individual']=$is_individual;
        $data['image']=$image??"";
        $data['reply']=0;
        $data['date']=$comment->created_at;
        $message=$data;
        return $this->successWithData($message);
     }catch(\Throwable $th){
        return $this->error($th->getMessage());
     }
}
//Post Comment Reply
public function PostCommentReply(PostCommentReply $request){
    try{
        $reply=new Reply;
        $reply->post_id=$request->post_id;
        $reply->user_id=Auth::user()->id;
        $reply->comment_id=$request->comment_id;
        $reply->comment=$request->comment;
        $reply->save();
        $data=[];
        $data['post_id']=$request->post_id;
        $data['reply_id']=$reply->id;
        $data['comment_id']=$request->comment_id;
        $data['comment']=$reply->comment;
        $user=User::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->where('id',Auth::user()->id)->first();

        $is_individual=1;
        $image=$user->profile_image;
        if($user->is_business == 1)
        {
            $is_individual=0;
            $image=$user->business_image;
        }
        $data['is_individual']=$is_individual;
        $data['image']=$image??"";
        $data['date']=$user->created_at;
        $message=$data;
        return $this->successWithData($message);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//Post Comment Listing
public function PostCommentListing(PostIdCheck $request){
     try{
        $data=Comment:: select('users.id as u_id','users.profile_image','users.business_image','users.is_business','comment.*')->Join('users','users.id','=','comment.user_id')->where('post_id',$request->post_id)->orderBy('comment.created_at','DESC')->paginate(5);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//Post Comment Reply Listing
public function PostCommentReplyListing(request $request){
    try{
       $data=Reply::select('users.id as u_id','users.profile_image','users.business_image','users.is_business','reply.*')->Join('users','users.id','=','reply.user_id')->where('post_id',$request->post_id)->where('comment_id',$request->comment_id)->orderBy('reply.created_at','DESC')->paginate(3);
       return $this->customPaginator($data);
    }catch(\Throwable $th){
        return $this->error($th->getMessage());
    }
}
//File Comment Reply
public function FileCommentReply(request $request){
    try{
       $reply=new FileCommentReply;
       $reply->post_id=$request->post_id;
       $reply->user_id=Auth::user()->id;
       $reply->file_id=$request->file_id;
       $reply->comment_id=$request->comment_id;
       $reply->comment=$request->comment;
       $reply->save();
       $data=[];
       $data['post_id']=$request->post_id;
       $data['file_id']=$request->file_id;
       $data['reply_id']=$reply->id;
       $data['comment_id']=$request->comment_id;
       $data['comment']=$request->comment;
       $user=User::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->where('id',Auth::user()->id)->first();
       
       $is_individual=1;
       $image=$user->profile_image;
       if($user->is_business == 1)
       {
           $is_individual=0;
           $image=$user->business_image;
       }
       $data['is_individual']=$is_individual;
       $data['image']=$image??"";
       $data['date']=$reply->created_at;
       $message=$data;
       return $this->successWithData($message);
    }catch(\Throwable $th){
       return $this->error($th->getMessage());
    }
}
//File Comment Listing
public function FileCommentListing(request $request){
    try{
        $data=FileComment::select('users.id as u_id','users.profile_image','users.business_image','users.is_business','file_comment.*')->Join('users','users.id','=','file_comment.user_id')->where('file_id',$request->file_id)->where('post_id',$request->post_id)->orderBy('file_comment.created_at','DESC')->paginate(5);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//File comment Reply Listing
public function FileCommentReplyListing(request $request){
    try{
        $data=FileCommentReply::select('users.id as u_id','users.profile_image','users.business_image','users.is_business','comment_file_reply.*')->Join('users','users.id','=','comment_file_reply.user_id')->where('file_id',$request->file_id)->where('post_id',$request->post_id)->where('comment_id',$request->comment_id)->orderBy('comment_file_reply.created_at','DESC')->paginate(3);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}

//Post Like Listing
public function LikeListing(PostIdCheck $request){
    try{
        $data=Thumbsup::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->Join('users','users.id','=','thumbsup.user_id')->where('post_id',$request->post_id)->paginate(15);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//Post Reaction Listing
public function ReactionListing(PostIdCheck $request){
    try{
        $data=PostReaction::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->Join('users','users.id','=','post_reaction.user_id')->where('post_id',$request->post_id)->paginate(15);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}

//File Reaction Listing
public function SingleReactionListing(request $request){
    try{
        $data=FileReaction::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->Join('users','users.id','=','file_reaction.user_id')->where('file_id',$request->file_id)->paginate(15);
        return $this->customPaginator($data);
      }catch(\Throwable $th){
          return $this->error($th->getMessage());
      }
}
//File Like Listing
public function SingleLikeListing(request $request){
    try{
        $data=SinglePostThumbs::select('users.name','users.business_name','users.profile_image','users.business_image','users.is_business')->Join('users','users.id','=','singlepostthumbs.user_id')->where('singlepostthumbs.file_id',$request->file_id)->paginate(15);
        return $this->customPaginator($data);
    }catch(\Throwable $th){
        return $this->error($th->getMessage());
    }
}

//Post view Count Increments
public function PostViewCount(request $request){
    try{
        $message="Post is Already Viewed";
       $check=PostViews::where('post_id',$request->post_id)->where('user_id',Auth::user()->id)->first();
       if(!$check){
         $view=new PostViews;
         $view->user_id=Auth::user()->id;
         $view->post_id=$request->post_id;
         if($view->save()){
            $post_count=Post::where('id',$request->post_id)->update([
                'view_count'=> DB::raw('view_count+1'), 
              ]);
              $message="Post Viewed";
         }
       }
       return $this->success(true,$message,200);
    }catch(\Throwable $th){
       return $this->error($th->getMessage());
    }
 }
 public function FileViewCount(request $request){
     try{
         $message="Post is Already Viewed";
        $check=FileViews::where('post_id',$request->post_id)->where('file_id',$request->file_id)->where('user_id',Auth::user()->id)->first();
        if(!$check){
          $view=new FileViews;
          $view->user_id=Auth::user()->id;
          $view->post_id=$request->post_id;
          $view->file_id=$request->file_id;
          if($view->save()){
            $post_count=File::where('id',$request->file_id)->update([
                'view_count'=> DB::raw('view_count+1'), 
              ]);
              $message="Post Viewed";
          }
        }
        return $this->success(true,$message,200);
     }catch(\Throwable $th){
        return $this->error($th->getMessage());
     }
  }
//********************************************************Custom Helper Function ************************************************//
//Get User Socials
public function getUserSocial($user_id){
            $socialmedia = SocialMedia::where('user_id',$user_id)->select('fb as facebook','twitter','youtube','linkedIn')->first();
            $data = [
                'facebook' => $socialmedia->facebook??'',
                'twitter' => $socialmedia->twitter??'',
                'youtube' => $socialmedia->youtube??'',
                'linkedIn' => $socialmedia->linkedIn??'',
            ];
            return $data; 
    }

public function toggledata($model,$post_id,$check){
        $user_id = Auth::user()->id;
        $like=$this->toggleLike($check,$model,'user_id','post_id',$user_id,$post_id);
        $message="Dislike";
        if($like){
        $message="Like";
        }
        return $message;
}
public function singletoggledata($model,$post_id,$file_id,$check){
    $user_id = Auth::user()->id;
    $like=$this->singletoggleLike($check,$model,'user_id','post_id','file_id',$user_id,$post_id,$file_id);
    $message="Dislike";
    if($like){
    $message="Like";
    }
    return $message;
}
public function PostReaction($post_id,$reaction){
   $user_id=Auth::user()->id;
   $check=PostReaction::where('user_id',$user_id)->where('post_id',$post_id)->first();
   if($check){
    if($check->reaction!=$reaction){
        $update=PostReaction::where('user_id',$user_id)->where('post_id',$post_id)->update([
            'reaction'=>$reaction
        ]);
        if($update){
            return "Thankyou for your reaction";
        }
    }else{
        return "reaction is already given by you";
    }
   }else{
    $postreaction=new PostReaction;
    $postreaction->user_id=$user_id;
    $postreaction->reaction=$reaction;
    $postreaction->post_id=$post_id;
    $postreaction->save();
    return "Thankyou for your reaction";
   }
}
public function FileReaction($post_id,$file_id,$reaction){
    $user_id=Auth::user()->id;
    $check=FileReaction::where('user_id',$user_id)->where('file_id',$file_id)->where('post_id',$post_id)->first();
    if($check){
     if($check->reaction!=$reaction){
         $update=FileReaction::where('user_id',$user_id)->where('post_id',$post_id)->where('file_id',$file_id)->update([
             'reaction'=>$reaction
         ]);
         if($update){
             return "Thankyou for your reaction";
         }
     }else{
         return "reaction is already given by you";
     }
    }else{
     $filereaction=new FileReaction;
     $filereaction->user_id=$user_id;
     $filereaction->reaction=$reaction;
     $filereaction->file_id=$file_id;
     $filereaction->post_id=$post_id;
     $filereaction->save();
     return "Thankyou for your reaction";
    }
 }
}




