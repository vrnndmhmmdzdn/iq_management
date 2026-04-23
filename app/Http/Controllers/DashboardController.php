<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // return match(true) {
        //     $user->hasRole('admin') => redirect()->route('admin.dashboard'),
        //     $user->hasRole('guru')  => redirect()->route('guru.dashboard'),
        //     $user->hasRole('ortu')  => redirect()->route('ortu.dashboard'),
        //     default                 => abort(403),
        // };
        if ($user->hasRole('admin') || $user->hasRole('guru')) {
            return redirect('/admin');
        }

        if ($user->hasRole('ortu')) {
            return redirect()->route('ortu.dashboard');
        }

        abort(403);
    }
}