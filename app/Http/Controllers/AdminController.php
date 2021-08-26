<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Management;
use App\Models\Permissions as PD;
use App\Models\Agency;
use App\Models\Admin;
use App\Models\CoinHistory;
use App\Models\FinanceData;
use App\Models\WithdrawHistory;
use App\Models\Streamer as St;
use App\Models\PermissionLevel;

use App\Http\Requests\permissions;
use App\Http\Requests\StaffRequest;
use App\Http\Requests\AgencyRequest;
use App\Http\Requests\Streamer;
use App\Models\Transaction;
use App\Traits\ImageUpload;
use App\Traits\Statuscheck;
use App\Traits\togglestatus;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;


use Validator;
use DB;

class AdminController extends Controller
{
    use ImageUpload;
    use Statuscheck;
    use togglestatus;
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = validator()->make($request->all(), [
                "email" => "required|email",
                "password" => "required",
            ]);
            if ($validator->fails()) {
                return back()->withInput($request->input())->with($validator->errors());
            }
            $credentials = $request->only('email', 'password');
            $remember = true;
            $check = User::where('email', $request->email)->where('role_id', 0)->first();
            if ($check) {
                if (Auth::attempt($credentials, $remember)) {
                    Auth::logoutOtherDevices($request->password);
                    //   $request->session()->regenerate();
                    return redirect("admin/home");
                } else {
                    return back()->with('error', "Incorrect email/password.");
                }
            } else {
                return back()->with('error', "Incorrect email/password.");
            }
        } else {
            return view('admin.login');
        }
    }
    public function Logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'you Logged out');
    }


    //******************************************* Permissions *****************************************//
    public function viewpermission()
    {

        $data = $this->PermissionData();
        return view('admin.permissions.create', compact('data'));
    }
    public function ViewUpdatePermission(request $request)
    {
        $permission = PD::where('id', $request->id)->first();
        return view('admin.permissions.includes.update', compact('permission'))->render();
    }
    public function UpdatePermission(permissions $request)
    {
        $update = PD::where('id', $request->id)->update([
            "title" => $request->title,
            "description" => $request->description ?? " "
        ]);
        return response()->json(redirect()->back()->with('success', 'Permission is Updated Successfully'));
    }
    public function AddPermission(permissions $request)
    {
        $insert = new PD;
        $insert->title = $request->title;
        $insert->description = $request->description ?? " ";
        $insert->created_by_id = Auth::user()->id;
        $insert->save();
        return response()->json(redirect()->back()->with('success', 'New Permission is added Successfully'));
    }
    public function permissiondetailview(request $request)
    {
        $permission = PD::where('id', $request->id)->first();
        return view('admin.permissions.includes.detailview', compact('permission'))->render();
    }
    public function permissionback()
    {
        $data = $this->PermissionData();
        return view('admin.permissions.main', compact('data'))->render();
    }
    public function TogglePermissionSatus(request $request)
    {
        $check_status = PD::where('id', $request->id)->first();
        $status = $this->checkStatus($check_status->status);

        $get_Result = $this->toggleStatusDB($status, 'Permissions', $request->id);
        if ($request->input('search')) {
            $data = PD::where('title', 'like', '%' . $request->search . '%')->orderBy('id', 'DESC')->paginate(15);
            return view('admin.permissions.includes.view', compact('data'))->render();
        } else {
            $data = $this->PermissionData();
            return view('admin.permissions.includes.view', compact('data'))->render();
        }
    }
    public function fetchpermission(request $request)
    {
        if ($request->ajax()) {
            if ($request->input('search')) {
                $data = PD::where('title', 'like', '%' . $request->search . '%')->orderBy('id', 'DESC')->paginate(15);
                return view('admin.permissions.includes.view', compact('data'))->render();
            } else {
                $data = $this->PermissionData();
                return view('admin.permissions.includes.view', compact('data'))->render();
            }
        }
    }


    public function deletepermission(request $request)
    {
        $delete = PD::where('id', $request->id)->delete();
        $data = $this->PermissionData();
        return view('admin.permissions.includes.view', compact('data'))->render();
    }
    public function PermissionData()
    {
        return PD::orderBy('id', 'DESC')->paginate(15);
    }
    //************************************ Permission End *********************************************//
    //******************************************************* Agency **************************************//
    public function ViewAgency()
    {
        $data = $this->AgencyData();
        return view('admin.agency.create', compact('data'));
    }
    public function ViewUpdateAgency(request $request)
    {
        $agency = Admin::where('id', $request->id)->where('role', 2)->first();
        return view('admin.agency.includes.update', compact('agency'))->render();
    }
    public function AgencyBack()
    {
        $data = $this->AgencyData();
        return view('admin.agency.main', compact('data'))->render();
    }
    public function AgencyDetailsView(request $request)
    {
        $agency = Admin::where('id', $request->id)->where('role', 2)->first();
        return view('admin.agency.includes.detailview', compact('agency'))->render();
    }
    public function DeleteAgency(request $request)
    {
        $delete = Admin::where('id', $request->id)->delete();
        $data = $this->AgencyData();
        return view('admin.agency.includes.view', compact('data'));
    }
    public function ToggleAgencySatus(request $request)
    {
        $check_status = Admin::where('id', $request->id)->first();
        $status = $this->checkStatus($check_status->status);

        $get_Result = $this->toggleStatusDB($status, 'Admin', $request->id);
        if ($request->input('search')) {
            $data = Admin::where('id', '!=', Auth::user()->id)->where('role', 2)->where('name', 'like', '%' . $request->search . '%')->orderBy('id', 'DESC')->paginate(15);
            return view('admin.agency.includes.view', compact('data'))->render();
        } else {
            $data = $this->AgencyData();
            return view('admin.agency.includes.view', compact('data'))->render();
        }
    }
    public function AddAgency(AgencyRequest $request)
    {
        $image_name = $this->UploadImage($request->file('image'), 'images');
        $insert = new Admin;
        $insert->agency_code = strtoupper(substr(uniqid(), -7));
        $insert->name = $request->name;
        $insert->email = $request->email;
        $insert->phone_number = $request->phone_number;
        $insert->country_code = $request->country_code;
        $insert->country = $request->country;
        $insert->state = $request->state;
        $insert->city = $request->city;
        $insert->address = $request->address;
        $insert->license_id = $request->license_id;
        $insert->license_file = $image_name;
        $insert->legal_advisor = $request->legal_advisor;
        $insert->business_type = $request->business_type;
        $insert->password = Hash::make($request->password);
        $insert->created_by_id = Auth::user()->id;
        $insert->role = 2;
        $insert->save();
        return response()->json(redirect()->back()->with('success', 'New Agency is added Successfully'));
    }
    public function UpdateAgency(AgencyRequest $request)
    {
        $image_name = $this->DBimageUpload($request->file('image'), 'Admin', $request->id, 'license_file', 'images');
        $update = Admin::where('id', $request->id)->where('role', 2)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'license_id' => $request->license_id,
            'license_file' => $image_name,
            'legal_advisor' => $request->legal_advisor,
            'business_type' => $request->business_type,
        ]);
        return response()->json(redirect()->back()->with('success', 'Agency is Updated Successfully'));
    }

    public function FetchAgency(request $request)
    {
        if ($request->ajax()) {
            if ($request->input('search')) {
                $data = Admin::where('id', '!=', Auth::user()->id)->where('role', 2)->where('name', 'like', '%' . $request->search . '%')->orderBy('id', 'DESC')->paginate(15);
                return view('admin.agency.includes.view', compact('data'))->render();
            } else {
                $data = $this->AgencyData();
                return view('admin.agency.includes.view', compact('data'))->render();
            }
        }
    }
    public function AgencyData()
    {
        return Admin::where('id', '!=', Auth::user()->id)->where('role', 2)->orderBy('id', 'DESC')->paginate(15);
    }
    //****************************************** Agency End ****************************************//
    //******************************************Staff  **********************************************//
    public function RemoveStaff()
    {
        $delete = Admin::where('id', $request->id)->delete();
        $data = $this->StaffData();
        return view('admin.staff.includes.view', compact('data'))->render();
    }
    public function DetailViewStaff(request $request)
    {
        $staff = Admin::where('id', $request->id)->where('role', 1)->first();
        return view('admin.staff.includes.detailview', compact('staff'))->render();
    }
    public function ViewUpdateStaff(request $request)
    {
        $staff = Admin::where('id', $request->id)->where('role', 1)->first();
        return view('admin.staff.includes.update', compact('staff'))->render();
    }
    public function UpdateStaff(StaffRequest $request)
    {
        $update = Admin::where('id', $request->id)->where('role', 1)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone,
            'email' => $request->email,
            'country_code' => $request->country_code
        ]);
        return response()->json(redirect()->back()->with('success', 'Staff is Updated Successfully'));
    }
    public function StaffBack()
    {
        $data = $this->StaffData();
        $permissions = PD::where('status', 1)->select('id', 'title')->get();
        return view('admin.staff.main', compact('data', 'permissions'))->render();
    }
    public function StaffData()
    {
        return Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
    }

    //****************************************** Staff End *******************************************//
    //****************************************** Streamer *******************************************//
    public function DetailViewStreamer(request $request)
    {
        $streamer = User::where('id', $request->id)->where('role_id', 2)->first();
        return view('admin.streamers.includes.detailview1', compact('streamer'))->render();
    }

    public function streamerProfile(Request $request)
    {
        $streamer = St::where('id', $request->id)->where('role_id', 2)->first();
        return view('admin.revenue.includes.streamer_profile', compact('streamer'))->render();
    }

    public function StreamerBack()
    {
        if (Auth::user()->role != User::ROLE_ADMIN) {
   
            $agency = Agency::find(Auth::user()->id);
            $data =  $agency->streamers()->paginate(15);
        
        return view('admin.streamers.main', compact('data'));
        } else {
            $data = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
            return view('admin.streamers.main', compact('data'))->render();
        }
        
    }
    public function adminProfile()
    {
        $admin = Admin::where('id', Auth::user()->id)->first();
        return view('admin.profile', compact('admin'));
    }

    public function staffList(Request $request)
    {

        $permissions = PermissionLevel::where('status', 1)->select('id', 'title')->get();
        $data = Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
        return view('admin.staff.create', compact('data', 'permissions'));
    }

    public function home()
    {
        $staff = Admin::all();
        $data = [];
        $data['steamer'] = User::where('role_id', 2)->count();
        $data['staff'] = Admin::where('role', 1)->count();
        $data['agency'] = Admin::where('role', 2)->count();
        $data['permission'] = PD::count();
        return view('admin.home', compact('staff', 'data'));
    }

    public function updateProfile(Request $request)
    {
        // $admin = Admin::where('email', $request->email)->first();
        $admin = Admin::where('id', Auth::user()->id)->first();
        $validator = validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|unique:admin,email,' . $admin->id . 'id',
            'phone_number' => 'required|digits_between:10,15|unique:admin,phone_number,' . $admin->id . 'id',
            // 'password' => 'required|min:6|required_with:confirm_password',
            // 'confirm_password' => 'required_with:password|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 422);
        }
        $staff->first_name = $request->first_name ?? $admin->first_name;
        $staff->last_name = $request->last_name ?? $admin->last_name;
        $staff->email = $request->email ?? $admin->email;
        $staff->phone_number = $request->phone_number ?? $admin->phone_number;
        // $staff->password = Hash::make($request->password)??$staff->password;
        // $url ='admin/profile'.'#update_profile';
        if ($staff->save()) {

            return response()->json(redirect()->back()->with('success', 'Profile updated successfully.'));
        } else {

            return response()->json(redirect()->back()->with('error', "Some error occured! Please try again."));
        }
    }

    public function fetchStaffList(request $request)
    {
        if ($request->ajax()) {
            if ($request->input('search')) {
                $data = Admin::where('first_name', 'like', '%' . $request->search . '%')->where('role', 1)->orderBy('last_name', 'DESC')->paginate(15);
                return view('admin.staff.includes.view', compact('data'))->render();
            } else {
                $data = Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
                return view('admin.staff.includes.view', compact('data'))->render();
            }
        }
    }

    public function toggleStaffStatus(Request $request)
    {
        $check_status = Admin::where('id', $request->id)->where('role', 1)->first();
        $status = $this->checkStatus($check_status->status);
        $get_Result = $this->toggleStatusDB($status, 'Admin', $request->id);
        if ($request->input('search')) {
            $data = Admin::where('first_name', 'like', '%' . $request->search . '%')->where('role', 1)->orderBy('id', 'DESC')->paginate(15);
            return view('admin.staff.includes.view', compact('data'))->render();
        } else {
            $data = Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
            return view('admin.staff.includes.view', compact('data'))->render();
        }
    }

    public function searchStaff(Request $request)
    {
        if ($request->ajax()) {
            if ($request->input('search')) {
                $data = Admin::where('first_name', 'like', '%' . $request->search . '%')->where('role', 1)->orderBy('last_name', 'DESC')->paginate(15);
                return view('admin.staff.includes.view', compact('data'))->render();
            } else {
                $data = Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
                return view('admin.staff.includes.view', compact('data'))->render();
            }
        }
    }
    public function deleteStaff(request $request)
    {
        $delete = Admin::where('id', $request->id)->delete();
        $data = Admin::orderBy('id', 'DESC')->where('role', 1)->paginate(15);
        return view('admin.staff.includes.view', compact('data'))->render();
    }

    public function addStaff(StaffRequest $request)
    {
        $staff = new Admin;
        $staff->first_name = $request->first_name;
        $staff->last_name = $request->last_name;
        $staff->phone_number = $request->phone;
        $staff->country_code = $request->country_code;
        $staff->email = $request->email;
        $staff->role = 1;
        $staff->password = Hash::make('password');
        $staff->created_by_id = Auth::user()->id;
        if(is_array($request->user_permission)){
            $request->user_permission=implode(",",$request->user_permission);
        }
        $staff->permission_level = $request->user_permission;
        $staff->save();
        return redirect()->back()->with('success', 'Staff profile created succesfully.');
    }

    public function addStreamer(Request $request)
    {
  
        $image_name = $this->UploadImage($request->file('profile_image'), 'images');
       
        $streamer = new User;
        $streamer->firstName = $request->first_name;
        $streamer->lastName = $request->last_name;
        $streamer->phone = $request->phone;
        $streamer->gender = $request->gender;
        $streamer->gender = $request->email;
        $streamer->dob = $request->streamer_dob;
        $streamer->profilePics = $image_name;
        $streamer->latitude = "";
        $streamer->longitude = "";
        $streamer->streamer_code = strtoupper(substr(uniqid(), -7));
        $streamer->role_id = 2;
        $streamer->email = $request->email;
        $streamer->created_by_id = Auth::user()->id;
        $streamer->save();
        return redirect()->back()->with('success', 'Streamer profile created succesfully.');
        // response()->json(redirect()->back()->with('success', 'Streamer profile created succesfully.'));
    }

    public function streamerList()
    {
        $loggedInUser = Auth::user();
        if($loggedInUser->role == User::ROLE_ADMIN){
            $data = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
        }else{
            $agency = Agency::find($loggedInUser->id);
            $data =  $agency->streamers()->paginate(15);
        }
        
        return view('admin.streamers.create', compact('data'));
    }

    public function FetchStreamerList(request $request)
    {
       
        if ($request->ajax()) {
            $loggedInUser = Auth::user();
            if ($request->input('search')) {
                if($loggedInUser->role == User::ROLE_ADMIN){
                $data = User::where('firstName', 'like', '%' . $request->search . '%')->where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
                }else{
                    $agency = Agency::find($loggedInUser->id);
                    $data =  $agency->streamers()->where('firstName', 'like', '%' . $request->search . '%')->paginate(15);
                }
                return view('admin.streamers.includes.view', compact('data'))->render();
            } else {
                if($loggedInUser->role == User::ROLE_ADMIN){

                $data = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
                }else{
                    $agency = Agency::find($loggedInUser->id);
                    $data =  $agency->streamers()->paginate(15);
                }
                return view('admin.streamers.includes.view', compact('data'))->render();
            }
        }
    }


    public function toggleStreamerStatus(Request $request)
    {
        $check_status = User::where('id', $request->id)->first();
        $status = $this->checkStatus($check_status->enabled);
        $get_Result = $this->toggleStatusDB($status, 'User', $request->id, 'enabled');
        if ($request->input('search')) {
            $data = User::where('firstName', 'like', '%' . $request->search . '%')->where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
            return view('admin.streamers.includes.view', compact('data'))->render();
        } else {
            $data = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
            return view('admin.streamers.includes.view', compact('data'))->render();
        }
    }

    public function deleteStreamer(request $request)
    {
        $delete = User::where('id', $request->id)->where('role_id', 2)->delete();
        $data = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(15);
        return view('admin.streamers.includes.view', compact('data'))->render();
    }

    public function ViewUpdateStreamer(request $request)
    {
        $streamer = User::where('id', $request->id)->where('role_id', 2)->first();
        // return $streamer;
        return view('admin.streamers.includes.update', compact('streamer'))->render();
    }
    public function UpdateStreamer(Request $request)
    {
        if ($request->hasFile('profile_image')) {
            $image_name = $this->UploadImage($request->file('profile_image'), 'images');
            $update =  User::where('id', $request->id)->where('role_id', 2)->update(['profilePics' => $image_name]);
        }

        $update =  User::where('id', $request->id)->where('role_id', 2)->update([
            'firstName' => $request->first_name,
            'lastName' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->streamer_dob
        ]);
        return response()->json(redirect()->back()->with('success', 'Streamer profile Updated Successfully'));
    }
    // public function searchStreamer(Request $request){
    //     if($request->ajax())
    //     {
    //        if($request->input('search')){
    //           $data=User::where('first_name','like','%'.$request->search.'%')->orderBy('last_name','DESC')->paginate(15);
    //           return view('admin.streamers.includes.view', compact('data'))->render();
    //        }else{
    //           $data=User::where('role_id', 2)->orderBy('id','DESC')->paginate(15);
    //           return view('admin.streamers.includes.view', compact('data'))->render();
    //        }
    //     }
    // }

    public function agencyHome()
    {

        $agency = Agency::find(3);

        $streamers = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(7);
        return view('admin.agency.dashboard', compact('streamers', 'agency'));
    }

    public function agencyProfile(Request $request)
    {
        $agency = Agency::where('id', $request->id)->where('role', 2)->first();
        $data = St::where('role_id', 2)->where('agency_id', $request->id)->orderBy('id', 'DESC')->paginate(15);
        return view('admin.revenue.includes.agency_profile', compact('agency', 'data'))->render();
    }

    public function sidebarAgencyProfile(Request $request)
    {
        $agency = Agency::where('id', $request->id)->where('role', 2)->first();
        return view('admin.agency.includes.agency_profile', compact('agency'))->render();
    }
    
    public function adminRevenue(Request $request)
    {

        $streamersRevenue = CoinHistory::whereIn('type', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->where('transaction_type', 1)->sum('coins');

        $agencies = Agency::where('role', 2)->get();
        $agenciesRevenue = 0;
        foreach ($agencies as $agency) {
            $agenciesRevenue = $agenciesRevenue + $agency->agencyIncome();
        }
        // $agency = Agency::find(3);
        $streamers = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate(7);
        return view('admin.revenue.dashboard', compact('streamers', 'streamersRevenue', 'agenciesRevenue'));
    }

    public function revenueStreamerList()
    {
        $streamers = St::where('role_id', 2)->orderBy('id', 'DESC')->paginate(7);
        return view('admin.revenue.includes.streamers', compact('streamers'))->render();
    }
    public function ajaxGridFatchData(request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->type)) {
                $datas = Transaction::where(['type' => $request->type, 'user_id' => $request->user_id])->orderBy('id', 'DESC')->paginate(5);
            } else {
                $datas = Transaction::where(['user_id' => $request->user_id])->orderBy('id', 'DESC')->paginate(5);
            }
            // /$datas = Transaction::where('type', $request->type)->orderBy('id', 'DESC')->paginate(5);
            return view('admin.revenue.includes._table_pagination', compact('datas'))->render();
        }
    }

    public function fetchRevenueStreamer(request $request)
    {
        if ($request->ajax()) {
            if ($request->input('search')) {
                $streamers = St::where('role_id', 2)->orderBy('id', 'DESC')->paginate(7);
                return view('admin.revenue.includes.streamers', compact('streamers'))->render();
            } else {
                $streamers = St::where('role_id', 2)->orderBy('id', 'DESC')->paginate(7);
                return view('admin.revenue.includes.streamers', compact('streamers'))->render();
            }
        }
    }

    public function revenueAgencyList()
    {

        $agencies = Agency::where('role', 2)->paginate(7);
        return view('admin.revenue.includes.agencies', compact('agencies'))->render();
    }

    public function TabsFilter(request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->type)) {
                $datas = Transaction::where(['type' => $request->type, 'user_id' => $request->user_id])->orderBy('id', 'DESC')->paginate(5);
            } else {
                $datas = Transaction::where(['user_id' => $request->user_id])->orderBy('id', 'DESC')->paginate(5);
            }
            return view('admin.revenue.includes._table_pagination', compact('datas'))->render();
        }
    }

    public function revenueAgencyStreamers(Request $request)
    {
        $data = St::where('role_id', 2)->where('agency_id', $request->id)->orderBy('id', 'DESC')->paginate(15);
        return view('admin.revenue.includes.agency_streamers', compact('data'));
    }

    public function withdrawList(){
        $data = WithdrawHistory::paginate(10);
        // return $data;
        return view('admin.withdraw.create', compact('data'));
    }

    public function withdrawListFilter(request $request)
    {
        if ($request->ajax()) {
         
            if (!empty($request->type)) {
                $data = WithdrawHistory::where('status',$request->type)->paginate(10);
            } else {
                $data = WithdrawHistory::paginate(10);
            }
            return view('admin.withdraw.includes._table_pagination', compact('data'))->render();
        }
    }

    public function toggleWithdrawStatus(Request $request)
    {
      
        $withdrawRequest = WithdrawHistory::where('id', $request->id)->update(['status'=> $request->status]);
           
            $data = WithdrawHistory::paginate(10);
            return view('admin.withdraw.includes._table_pagination', compact('data'))->render();
        
    }

    public function viewPermissionLevel()
    {

        $data = PermissionLevel::orderBy('id', 'DESC')->paginate(15);
        $permissions = PD::get();
        return view('admin.permissionLevel.create', compact('data', 'permissions'))->render();
    }

    public function addPermissionLevel(Request $request)
    {
        $permissions = "";
        $request->validate([
            'permission_level_title' => 'bail|required|string|max:255',
        ]);
        if($request->filled('permission_ids')){
        $permissions =  implode(",",$request->permission_ids);
        }
        $insert = new PermissionLevel;
        $insert->title = $request->permission_level_title;
        $insert->permission_ids = $permissions;
        $insert->save();
        return redirect()->back()->with('success', 'Record created successfully.');
    }

    public function ViewUpdatePermissionLevel(request $request)
    {
        $permission = PermissionLevel::where('id', $request->id)->first();
        return view('admin.permissionLevel.includes.update', compact('permission'))->render();
    }

    public function updatePermissionLevel(Request $request)
    {
      
        $request->validate([
            'permission_level_title' => 'bail|required|string|max:255',
        ]);
        $update = PermissionLevel::where('id', $request->id)->update([
            "title" => $request->permission_level_title,
        ]);
        return response()->json(redirect()->back()->with('success', 'Record Updated Successfully'));
    }
}
