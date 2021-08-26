<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
use App\Traits\ImageUpload;
trait checkUpload{
      use ImageUpload;
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function DBimageUpload($file,$model,$id,$filename="images"){
        $model="App\Models\\".$model;
        $image=$model::where('id',$id)->first();
        $imageName="";
         if(empty($file)){
               $imageName=$image->image;
         }else{
              $file=$file;
              $imageName=$this->UploadImage($file,$filename);
         }
         return $imageName;
	}
      public function DBimageUploadwithColumn($file,$model,$id,$filename="images",$condition_column,$column){
            $model="App\Models\\".$model;
            $image=$model::where($condition_column,$id)->first();
            $imageName="";
             if(empty($file)){
                   $imageName=$image->$column;
             }else{
                  $file=$file;
                  $imageName=$this->UploadImage($file,$filename);
             }
             return $imageName;
          }
          public function DBimageUpload_for_update($file,$model,$id,$filename="images"){
            $model="App\Models\\".$model;
            $image=$model::where('id',$id)->first();
            // dd($image);
            $imageName="";
             if(empty($file)){
                   $imageName=$image->image;
                      //      dd($imageName);
    
             }else{
                  $file=$file;
                  $imageName=$this->UploadImage($file,$filename);
             }
             return $imageName;
          }
     
}