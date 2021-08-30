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

//events
use App\Events\Notify;


class UserController extends Controller
{
    use ApiResponser;
    use ImageUpload;
    use Email;
    use Togglestatus;

    // showing error when user not logged in    
    public function loginCheck(request $r)
    {
        try {
            return $this->success(false, 'Please login to access this page', 403);
        } catch (\Exception $e) {
            return $this->error('Please check your fields');
        }
    }
    //logout current device    
    public function logout(request $r)
    {
        try {
            $r->user()->currentAccessToken()->delete();
            return $this->success(true, 'Successfully loggged out');
        } catch (\Exception $e) {
            return $this->error('Please check your fields');
        }
    }
    // logout all device
    public function logoutAll()
    {
        try {
            auth()->user()->tokens()->delete();
            return $this->success(true, 'Successfully loggged out from all devices');
        } catch (\Exception $e) {
            return $this->error('Please check your fields');
        }
    }

    // register for business and individual
    public function register(Request $r)
    {
        try {
            $register = User::create(
                [
                    'name' => $r->name,
                    'email' => $r->email,
                    'phone' => $r->phone,
                    'password' => Hash::make($r->password)
                ]
            );
            return $this->success(true, 'Registration successful');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    //update profile for both users
    public function updateProfile(UserRequest $r)
    {
        try {
            $user = Auth::user();
            $update = User::where('id', $user->id)
                ->update([
                    'name' => $r->name, 'email' => $r->email,
                    'phone' => $r->phone, 'business_name' => $r->business_name
                ]);
            return $this->success(true, 'Profile updated successfully');
        } catch (\Throwable $e) {
            return $this->error('Please check your fields');
        }
    }
    //login for both
    public function login(request $r)
    {
        try {
            $login = $r->validate([
                'email' => 'required',
                'password' => 'required',
            ]);
            $credentials = $r->only('email', 'password');
            $remember = false;
            if (Auth::attempt($credentials,$remember)) {
                $user = auth()->user();

              //  $token = $user->createToken('API Token')->plainTextToken;
               // print_r($token);
               // die('in');
               // $first_time_login = false;
                // if (auth()->user()->first_time_login == self::FIRST_LOGIN_FALSE) {
                //     $first_time_login = true;
                //     $user->update(['first_time_login' => self::FIRST_LOGIN_TRUE]);
                // }
                $token = $user->createToken('API Token')->plainTextToken;
                $profileDetails['is_business'] = $business_status ?? false;
                $profileDetails['is_individual'] = $user_status ?? false;
                $business_status = false;
                $user_status = false;
                $user->is_business == 1 ? $business_status = true : $user_status = true;
                $list = [];
                $list['token'] =  $token;
              //  $list['first_time_login'] =  $first_time_login;
                $list['name'] =  $user->name;
                $list['profile_image'] =  $user->profile_image;
                $list['is_business'] = $business_status ?? false;
                $list['is_individual'] = $user_status ?? false;
                return $this->successWithData($list);
            } else {
                return $this->error('Invalid credentials');
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
    //get all profile including additional info of both users
    public function getProfile(request $r)
    {

        $profileDetails = [];
        $languages = [];
        $skills = [];
        try {
            $data = User::profileResponse(auth()->user()->id);
            if (!empty($data)) {
                return $this->successWithData($data);
            } else {
                return $this->success(false);
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // forget password 
    public function forgotPassword(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'email' => 'required|email|max:255',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            $user = User::where('email', $r->email)->first();
            if (!empty($user)) {
                $token = Str::random(20);
                $updated = PasswordReset::updateOrCreate(['email' => $r->email], ['email' => $r->email, 'token' => $token]);
                if ($this->sendResetEmail($user, $token)) {
                    return $this->success(true, 'Password reset link has been sent to your registered email id.');
                } else {
                    return $this->success(false);
                }
            } else {
                return $this->success(false, 'This email is not registered yet');
            }
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
    // changePassword
    public function changePassword(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'token' => 'required',
                'password' => 'required',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        $token = $r->token;
        $email = PasswordReset::where('token', $r->token)->pluck('email')->first();
        if (empty($email)) {
            return $this->success(false, 'You have not requested for password change');
        } else {
            $updated = User::where('email', $email)->update(['password' => md5($r->password)]);
            if ($updated) {
                PasswordReset::where('token', $r->token)->delete();
                return $this->success(true, 'Password changed successfully');
            } else {
                return $this->success(false);
            }
        }
    }
}
