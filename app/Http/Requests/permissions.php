<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class permissions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->id){
            return [
                "title"=>"required|string|min:1|max:100|unique:permissions,title,".$this->id.'id',
                "description"=>"string|max:10000"
            ];
        }else{
            return [
                "title"=>"required|string|min:1|max:100|unique:permissions,title",
                "description"=>"string|max:10000"
            ];
        }
        
    }
    public function message(){
        return [
            "title.unique"=>'The permission is already Defined'
        ];
    }
}
