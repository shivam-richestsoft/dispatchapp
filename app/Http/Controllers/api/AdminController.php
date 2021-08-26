<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Management;
use App\Models\Permissions as PD;
use App\Models\Agency;
use App\Models\Admin;
use App\Models\CoinHistory;
use App\Models\FinanceData;
use App\Models\Streamer as St;

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
    public function withDrawRequest(){
        return 'test';
    }
}
