<?php
  
namespace App\Traits;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

trait sendmail{
public function mailer($email,$file,$to,$subject){
    Mail::send($file, ['email' => $email], function ($m) use ($email)
    {
        $m->from($email, 'silviaberlain');
        $m->to('silviaberlain@gmail.com')
        ->subject('Send Mail');
    });
    return true;
}
}