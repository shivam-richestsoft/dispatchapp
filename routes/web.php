<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Models\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [AdminController::class,'Login']);
Route::post('/login',[AdminController::class,'Login']);
Route::get('/agency',[AdminController::class,'AgencyLogin']);
Route::post('/agency',[AdminController::class,'AgencyLogin']);
Route::group(['prefix'=>'admin'], function() {
Route::middleware([
    'prefix'=>'AuthCheck'
])->group(function(){
      

          
          Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });

               

        Route::get('/home', [AdminController::class,'home']);
        Route::get('/profile', [AdminController::class,'adminProfile']);
        Route::get('/staff', [AdminController::class,'staffList']);
        Route::get('/revenue', [AdminController::class,'adminRevenue']);
        Route::get('/staffList/fetch_data', [AdminController::class,'fetchStaffList']);
        Route::post('/staff/toggleStatus',[AdminController::class,'toggleStaffStatus']);
        Route::get('/staffList/search',[AdminController::class,'fetchStaffList']);
        Route::post('/staff/add',[AdminController::class,'addStaff']);
        Route::post('/save/staff',[AdminController::class,'saveStaff'] );


    
        
        // Route::get('/dark', function () {
        //     return view('dashboard.dashboard_dark');
        // });
        
        // Route::get('/light', function () {
        //     return view('dashboard.dashboard_light');
        // });
        
        // Route::get('/custom', function () {
        //     return view('dashboard.dashboard_custom');
        // });

        Route::post('/updateProfile', [AdminController::class,'updateProfile'] )->name('admin.updateProfile');
      
       
        Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    
       
    Route::get('/dark', function () {
        return view('dashboard.dashboard_dark');
    });
    
    Route::get('/light', function () {
        return view('dashboard.dashboard_light');
    });
    
    Route::get('/custom', function () {
        return view('dashboard.dashboard_custom');
    });

    Route::get('/logout',[AdminController::class,'Logout']);

    //******************************************Permissions*********************************************//
    Route::get('/permissions',[AdminController::class,'viewpermission']);
    Route::get('/permissions/update',[AdminController::class,'ViewUpdatePermission']);
    Route::get('/permissions/detail',[AdminController::class,'permissiondetailview']);
    Route::post('/permissions/update/{id}',[AdminController::class,'UpdatePermission']);
    Route::get('/permissions/back',[AdminController::class,'permissionback']);
    Route::get('/permissions/views/fetch_data', [AdminController::class,'fetchpermission']);
    Route::get('/permissions/search',[AdminController::class,'fetchpermission']);
    Route::Delete('/permissions/delete',[AdminController::class,'deletepermission']);
    Route::post('/permissions/add',[AdminController::class,'AddPermission']);
    Route::post('/permissions/togglestatus',[AdminController::class,'TogglePermissionSatus']);
    Route::get('/permissionLevels',[AdminController::class,'viewPermissionLevel']);
    Route::post('/permissionLevel/add',[AdminController::class,'addPermissionLevel']);
    Route::get('/permissionLevel/update',[AdminController::class,'viewUpdatePermissionLevel']);
    Route::post('/permissionLevel/update/{id}',[AdminController::class,'updatePermissionLevel']);
    

    });
});






