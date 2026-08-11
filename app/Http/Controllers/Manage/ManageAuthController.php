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
        $request->validate([
            'security_code' => 'required|string',
        ]);

        $setting = \App\Models\PortalSetting::first();
        // $validCode = $setting ? $setting->security_code : 'gass';
        $validCode = $setting ? $setting->security_code : 'gass';
        

        if ($request->input('security_code') !== $validCode) {
            print_r($setting);
            return response()->json([
                'success' => false,
                'message' => 'Kode Edit / Security Code tidak valid.',
            ], 401);
        }

        session([
            'portal_edit_mode' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil masuk ke Edit Mode.',
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