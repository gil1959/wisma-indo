<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('partner.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],

            'address'      => ['nullable', 'string'],
            'full_address' => ['nullable', 'string'],
            'sub_district' => ['nullable', 'string', 'max:120'],

            // rekening partner
            'partner_bank_name'           => ['nullable', 'string', 'max:100'],
            'partner_bank_account_number' => ['nullable', 'string', 'max:50'],
            'partner_bank_account_holder' => ['nullable', 'string', 'max:100'],
        ]);

        $user->fill($validated);
        $user->save();

        return back()->with('success', 'Profile partner berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai.');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
