<?php

namespace App\Traits;

/*
|--------------------------------------------------------------------------
| Api Responser Trait
|--------------------------------------------------------------------------
|
| This trait will be used for any response we sent to clients.
|
*/

trait ApiResponser
{
	protected function success(string $message = 'Oops! something went wrong' , int $status = 200)
	{
        $response = ["status" =>  $status, "message" => $message];
        return response()->json($response, $status, $headers = [], $options = JSON_PRETTY_PRINT);
	}

    protected function successWithData($data = [], string $message = 'Data fetched',$options=[],int $status = 200)
	{
        $response = ["status" =>  $status,  "message" => $message,'data'=>array_merge($data,$options)];
        return response()->json($response, $status, $headers = [], $options = JSON_PRETTY_PRINT);
	}

	protected function error(string $message = '', int $status = 400 , $success = false)
	{
        $response = ["status" =>  $status, "message" => $message];
        return response()->json($response,200, $headers = [], $options = JSON_PRETTY_PRINT);
	}

	protected function validation($v){
		$error_description = "";
                foreach ( $v->messages ()
                    ->all () as $error_message ) {
                    $error_description .= $error_message . " ";
	}
    return $this->error($error_description);

	
	}
    protected function withsuccess($data = [],int $count=0, string $message = 'Data fetched', $success = true, int $status = 200)
	{
        $response = ["status" =>  $status, "success" => $success, "message" => $message,'data'=>$data ,'total'=>$count];
        return response()->json($response, $status, $headers = [], $options = JSON_PRETTY_PRINT);
	}
	public function customPaginator($paginator,$options=[]){
        
        $list=[];
            foreach($paginator->items() as $data){
                $list[]=$data->jsonData();
            }

            $pagination=[
                "list"=>$list,
                "per_page"=>$paginator->perPage(),
                "total"=>$paginator->total(),
                "current_page"=>$paginator->currentPage(),
                "last_page"=>$paginator->lastPage(),
				//"next_page"=>$paginator->nextPageUrl()
            ];

			$response = array_merge(["status" =>  200,"message" => 'Data fatched successfuly'],$pagination,$options);
			return response()->json($response, 200, $headers = [], $options = JSON_PRETTY_PRINT);

	}

}
