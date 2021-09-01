<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::post('send-otp', [UserController::class, 'sendOtp']);
Route::post('forgot-password', [UserController::class, 'forgotPassword']);

Route::post('verify-otp', [UserController::class, 'verifyOtp']);


//to show error when user not logged in --- used in middleware(Authenticate)
Route::get('login-check', [UserController::class, 'loginCheck'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('change-password', [UserController::class, 'changePassword']);

    Route::post('add-driver', [UserController::class, 'addDriver']);

    //general
    Route::get('get-profile', [UserController::class, 'getProfile']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);

    Route::post('update-driver-profile', [UserController::class, 'updateDriverProfile']);

    Route::get('driver-list', [UserController::class, 'driverList']);

    Route::post('assign-event', [EventController::class, 'assignEvent']);




    // Route::post('additional-info',[UC::class, 'additionalInfo']);

    // //lists
    // Route::get('language-list',[UC::class, 'languageList']);
    // Route::get('skills-list',[UC::class, 'skillsList']);
    // Route::get('expertise-list',[UC::class, 'expertiseList']);

    // //Adding project for business
    // Route::post('add-projects',[UC::class, 'addProjects']);
    // Route::post('update-project',[UC::class, 'updateProject']);
    // Route::post('delete-project',[UC::class, 'deleteProject']);
    // Route::get('projects-list',[UC::class, 'projectsListing']);

    // //Adding employment for indvidual
    // Route::post('add-employment',[UC::class, 'addEmployment']);
    // Route::post('update-employment',[UC::class, 'updateEmployment']);
    // Route::post('delete-employment',[UC::class, 'deleteEmployment']);
    // Route::get('employment-list',[UC::class, 'employmentsListing']);

    // //social media
    // Route::post('add-social',[UC::class, 'addSocial']);
    // Route::get('get-social',[UC::class, 'getSocial']);

    // // logout current device
    // Route::get('logout',[UC::class, 'logout']);

    // //logout all devices
    // Route::get('logout-all',[UC::class, 'logoutAll']);

    // //search
    // Route::post('search-user',[UC::class, 'searchUser']);

    // //friend request
    // Route::post('send-request',[UC::class, 'sendRequest']);
    // Route::post('delete-request',[UC::class, 'deleteRequest']);
    // Route::get('pending-request',[UC::class, 'pendingRequest']);
    // Route::get('sent-request',[UC::class, 'sentRequest']);
    // Route::post('accept-request',[UC::class, 'acceptRequest']);
    // Route::post('cancel-request',[UC::class, 'cancelRequest']);
    // Route::post('unfriend',[UC::class, 'unfriend']);

    // //get additiona details i.e emploment history or prjoject history and social links
    // Route::post('get-additional-details',[UC::class, 'getAdditionalDetails']);

    // //notifications
    // Route::get('notifications',[UC::class, 'notifications']);

    // //post
    // Route::post('post-something',[UC::class, 'postSomething']);

    // //post
    // Route::get('friends-list',[UC::class, 'friendsList']);

    // //timeline
    // Route::get('timeline',[UC::class, 'timeline']);

    // //Get Post 
    // Route::get('getpost',[UC::class,'getpost']);
    // Route::post('getfile',[UC::class,'GetFile']);
    // //Notification Onscreen 
    // Route::get('onScreenNotification',[UC::class,'onScreenNotification']);

    // //Read Notification
    // Route::post('readnotificaton',[UC::class,'readnotificaton']);

    // //Like and Dislike
    // Route::post('likedislike',[UC::class,'LikeAndDislike']);
    // Route::post('singlelikedislike',[UC::class,'FilesLikeAndDislike']);

    // //Thumbs Up AND Thumbs Down
    // Route::post('thumbsupdown',[UC::class,'ThumbsUpandDown']);
    // Route::post('singlethumbsupdown',[UC::class,'FilesThumbsUpandDown']);

    // //Reaction Listing
    // Route::post('likelisting',[UC::class,'LikeListing']);
    // Route::post('singlelikelisting',[UC::class,'SingleLikeListing']);

    // //Reaction
    // Route::post('postreaction',[UC::class,'PostReact']);
    // Route::post('singlepostreaction',[UC::class,'FileReact']);

    // //Reaction Listing
    // Route::post('reactionlisting',[UC::class,'ReactionListing']);
    // Route::post('singlereactionlisting',[UC::class,'SingleReactionListing']);

    // //Comments
    // Route::post('postcomment',[UC::class,'PostComment']);
    // Route::post('postcommentreply',[UC::class,'PostCommentReply']);

    // //Comments Listing
    // Route::post('postcommentlisting',[UC::class,'PostCommentListing']);
    // Route::post('postcommentreplylisting',[UC::class,'PostCommentReplyListing']);

    // //File Comments
    // Route::post('filecomment',[UC::class,'FileComment']);
    // Route::post('filecommentlisting',[UC::class,'FileCommentListing']);

    // //File Comments Reply
    // Route::post('filecommentreply',[UC::class,'FileCommentReply']);
    // Route::post('filecommentreplylisting',[UC::class,'FileCommentReplyListing']);

    // //Post View Count Increment
    // Route::post('postviewed',[UC::class,'PostViewCount']);
    // Route::post('fileviewed',[UC::class,'FileViewCount']);
});
