<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardOrtuController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $orangTua = $user->orangTua;
        $siswa    = $orangTua?->siswa;

        return view('ortu.dashboard', compact('orangTua', 'siswa'));
    }
}