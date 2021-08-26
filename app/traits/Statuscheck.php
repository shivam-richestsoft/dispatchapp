<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
  
trait Statuscheck{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function checkStatus($status){
          $return;
		   if($status==1){
               $return=true;
           }else{
               $return=false;
           }
           return $return;
		
	}
  
}