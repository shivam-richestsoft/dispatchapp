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
use App\Models\Otp;
use Exception;

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
            return $this->success('Please login to access this page', 403);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    //logout current device    
    public function logout(request $r)
    {
        try {
            $r->user()->currentAccessToken()->delete();
            return $this->success('Successfully loggged out');
        } catch (\Exception $e) {
            return $this->error('Please check your fields');
        }
    }
    // logout all device
    public function logoutAll()
    {
        try {
            auth()->user()->tokens()->delete();
            return $this->success('Successfully loggged out from all devices');
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
            return $this->success('Registration successful');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    //update profile for both users
    public function updateProfile(request $r)
    {
        try {

            $user = auth()->user();
            $user->update([
                'name' => $r->name ?? $user->name,
                'phone' => $r->phone ?? $user->phone,
            ]);
            return $this->success('Profile updated successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
    //login 
    public function login(request $r)
    {
        try {
            $v = Validator::make(
                $r->input(),
                [
                    'phone' => 'required|numeric',
                    'password' => 'required|min:4|max:20',
                ]
            );
            if ($v->fails()) {
                return $this->validation($v);
            }

            $user = User::where('phone', '=', $r->phone)->first();
            if (!$user) {
                throw new Exception("Invalid phone or password");
            }
            if (!Hash::check($r->password, $user->password)) {
                throw new Exception("Invalid phone or password");
            }
            //Genrate API Auth token
            $token = $user->createToken('API Token')->plainTextToken;

            $data = [];
            $data['token'] =  $token;
            $data['user_data'] =  $user->jsonData($token);
            return $this->successWithData($data, "Login successfull");
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
    //get all profile including additional info of both users
    public function getProfile(request $r)
    {
        return $this->successWithData(auth()->user()->jsonData());
    }

    //sendOtp  
    public function sendOtp(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'phone' => 'required|numeric',
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            DB::beginTransaction();

            $user = User::where('phone', $r->phone)->first();
            if (empty($user)) {
                throw new Exception("This number is not registered yet");
            }
            // $otp = Str::random(4);
            $otp = 1234;;

            Otp::where('phone', $r->phone)
                ->delete();

            $otpModel = new Otp();
            $otpModel->phone = $user->phone;
            $otpModel->otp = $otp;
            $otpModel->created_by_id = $user->id;
            if (!$otpModel->save()) {
                throw new Exception("Invalid phone or password");
            }

            DB::commit();

            return $this->success('OTP has been sent to your registered phone number.');

            //For email case
            // $updated = PasswordReset::updateOrCreate(['email' => $r->email], ['email' => $r->email, 'token' => $token]);
            // if ($this->sendResetEmail($user, $token)) {
            //     return $this->success(true, 'Password reset link has been sent to your registered email id.');
            // } else {
            //     return $this->success(false);
            // }

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    //verifyOtp  
    public function verifyOtp(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'phone' => 'required|numeric',
                'otp' => 'required'

            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {

            $user = User::where('phone', $r->phone)->first();
            if (empty($user)) {
                throw new Exception("This number is not registered yet");
            }

            $otp = Otp::where(['phone' => $r->phone, 'otp' => $r->otp])
                ->first();
            if (empty($otp)) {
                throw new Exception("No otp found");
            }
            $otp->delete();
            return $this->success("OTP verified successfully");
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }



    //forgotPassword  
    public function forgotPassword(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'phone' => 'required|numeric',
                'otp' => 'required'

            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {

            $user = User::where('phone', $r->phone)->first();
            if (empty($user)) {
                throw new Exception("This number is not registered yet");
            }

            $otp = Otp::where(['phone' => $r->phone, 'otp' => $r->otp])
                ->first();
            if (empty($otp)) {
                throw new Exception("No otp found");
            }

            //Genrate API Auth token
            $token = $user->createToken('API Token')->plainTextToken;
            $data = [];
            $data['token'] =  $token;
            $data['user_data'] =  $user->jsonData($token);

            $otp->delete();
            return $this->successWithData($data, "Login successfull");
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
                'password' => 'required|min:4|max:20'
            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            auth()->user()->update(['password' => Hash::make($r->password)]);
            return $this->success('Password changed successfull');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }

    // add-driver
    public function addDriver(request $r)
    {
        $v = Validator::make(
            $r->input(),
            [
                'name' => 'required|string|max:15',
                'phone' => 'required|numeric',
                'password' => 'required|min:4|max:20',
                'color_code' => 'required|string|max:15',
                'have_app' => 'required|numeric|max:1',

            ]
        );
        if ($v->fails()) {
            return $this->validation($v);
        }
        try {
            User::create(
                [
                    'name' => $r->name,
                    'phone' => $r->phone,
                    'color_code' => $r->color_code,
                    'password' => Hash::make($r->password),
                    'created_by_id' => auth()->user()->id
                ]
            );

            return $this->success('Driver added successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }


    //update driver profile 
    public function updateDriverProfile(request $r)
    {
        try {
            $v = Validator::make(
                $r->input(),
                [
                    'driver_id' => 'required|numeric',
                ]
            );
            if ($v->fails()) {
                return $this->validation($v);
            }

            $user = User::where('id',$r->driver_id)->first();
            if(empty($user)){
                throw new Exception("No driver found");
            }
            $user->update([
                'name' => $r->name ?? $user->name,
                'phone' => $r->phone ?? $user->phone,
                'color_code' => $r->color_code ?? $user->color_code,
            ]);
            return $this->success('Driver Profile updated successfully');
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }


}
