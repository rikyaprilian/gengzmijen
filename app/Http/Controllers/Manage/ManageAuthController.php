<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageAuthController extends Controller
{
    public function status()
    {
        return response()->json([
            'success' => true,
            'editMode' => session('portal_edit_mode', false),
        ]);
    }

    public function login(Request $request)
    {
        // sementara
        session([
            'portal_edit_mode' => true
        ]);

        return response()->json([
            'success' => true,
            'editMode' => true
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('portal_edit_mode');

        return response()->json([
            'success' => true,
            'editMode' => false
        ]);
    }
}