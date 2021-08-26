<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
  
trait togglestatus{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
   
    public function toggleStatusDB($status,$model,$id,$column="status"){
        $model="App\Models\\".$model;
        if($status){
            $model::where('id',$id)->update([
              $column=>0
           ]);
        }else{
            $model::where('id',$id)->update([
              $column=>1
           ]);
        }
        return true;
     }
     public function toggleDBfeatured($status,$model,$id){
      $model="App\Models\\".$model;
     
      if($status){
          $model::where('id',$id)->update([
            "featured"=>0
         ]);
      }else{
          $model::where('id',$id)->update([
            "featured"=>1
         ]);
      }
      return true;
     
   }
}