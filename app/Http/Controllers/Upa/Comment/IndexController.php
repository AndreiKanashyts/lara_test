<?php

namespace App\Http\Controllers\Upa\Comment;

use App\Http\Controllers\Controller;
use Auth;
//use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke()
    {
        $auth_operator = [
            'IdOperator' => Auth::user()->idoperator,
            'SystemUser' => Auth::user()->system_user,
            'LoginOperator' => Auth::user()->email,
            'Role' => Auth::user()->role
        ];

        return view('upa.comments.index', [
            'auth_operator' => $auth_operator
        ]);
    }
}
