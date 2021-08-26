<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
class TestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function sidebarpermission()
    {
        $a=(Auth::user()->role === 0)?true:false;
        print_r($a);exit;
        return (Auth::user()->role === 0)?true:false;
        }
}
