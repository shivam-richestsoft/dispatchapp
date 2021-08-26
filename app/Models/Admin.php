<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use DB;


class Admin extends Authenticatable
{
  // use HasFactory;
  protected $table = "admin";
  public $timestamp = true;



  public function getPermission()
  {
    return Permissions::get()->pluck("title")->toArray();
  }


  public function adminLiveVideoRevenue()
  {


    $income = DB::table('common_coins_history')->where('type', 1)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminPrivateAudioRevenue()
  {

    $income = DB::table('common_coins_history')->where('type', 2)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminPrivateVideoRevenue()
  {

    $income = DB::table('common_coins_history')->where('type', 3)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminLiveVideoGiftRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 4)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminSpinRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 5)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminPrivateStoryRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 6)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminPrivateChatRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 7)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminBuyCoins()
  {
    $income = DB::table('common_coins_history')->where('type', 8)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminChatGiftRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 9)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminVideoGiftRevenue()
  {
    $income = DB::table('common_coins_history')->where('type', 10)->where('transaction_type', 1)->sum('coins');
    return $income;
  }

  public function adminTotalRevenue()
  {
    $income = DB::table('common_coins_history')->whereIn('type', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->where('transaction_type', 1)->sum('coins');
    return $income;
  }
}
