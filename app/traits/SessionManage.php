<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Str;

use App\Models\CustomSession;

trait SessionManage{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function ManageSession(){
        $data=CustomSession::where('user_session',Session::get('GuestUser.id'))->first();
        if(empty($data)){
            $dummy_session=Str::random(64);
            session([
            "GuestUser" => [
                "id"=>$dummy_session,
                "User"=>"Guest"
                        ]
                ]);
           
            $insert=new CustomSession;
            $insert->user_session=Session::get('GuestUser.id');
            $insert->name="Guest";
            $insert->save();
        }
	}
  
}