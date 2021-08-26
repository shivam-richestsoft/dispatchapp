<?php
  
namespace App\Traits;

use Illuminate\Support\Facades\Mail;

trait Email{
  
    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function inviteMail($username,$to,$msg)
    {
        $link = 'http://167.172.209.57/poolsMagnic/signup';

        $sendMail = Mail::send('invite-template', ['username' => $username,'msg' => $msg,'link' => $link], function ($m) use($to) {
            $m->from('admin@poolsmagnic.com', 'Poolsmagnic');
            $m->to($to)->subject('Invitation');
        });

      return 1;
    }

    public function sendResetEmail($user,$token)
    {
        $link = 'http://167.172.209.57/poolsMagnic/change-password?token=' . $token ;

        $sendMail = Mail::send('reset-password-template', ['user' => $user->name,'link' => $link], function ($m) use ($user) {
            $m->from('admin@poolsmagnic.com', 'Poolsmagnic');

            $m->to($user->email, $user->name)->subject('Password reset link!');
        });

      return 1;
    }
  
}