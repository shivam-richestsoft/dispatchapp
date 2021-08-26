<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
  
trait keyANDObject{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function getKeys($array){
        $key_array=[];
        foreach($array as $key=>$value){
              array_push($key_array,$key);
        }
        return $key_array;
     }
     
    public function getValue($array){
        $value_array=[];
        foreach($array as $key=>$value){
              array_push($value_array,$value);
        }
        return $value_array;
    }
  
}