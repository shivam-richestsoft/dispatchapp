<?php
  
namespace App\Traits;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
  
trait ImageUpload{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function DBimageUpload($file,$model,$id,$column,$filename="images"){
      $model="App\Models\\".$model;
      $image=$model::where('id',$id)->first();
      $imageName="";
       if(empty($file)){
             $imageName=$image->$column;
       }else{
            $file=$file;
            $imageName=$this->UploadImage($file,$filename);
       }
       return $imageName;
}
    public function UploadImage($file,$fileName){
		$imageName =Str::random(10).'.'.$file->extension();
		$destinationPath = public_path($fileName);
		$file->move($destinationPath,$imageName);
		return $imageName;
	}
  
}