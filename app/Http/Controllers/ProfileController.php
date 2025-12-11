<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Show profile edit form (owner view, cashier view handled elsewhere)
     */
    public function edit()
    {
        $user = Auth::user();

        // If user is cashier, you said you already have a cashier view; keep owner path here
        if ($user->role->name === 'cashier') {
            return view('cashier.profile-edit', ['user' => $user]);
        }

        return view('owner.profile.edit', ['user' => $user]);
    }

    /**
     * Update basic profile information (name, phone)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('owner.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update email (OWNER ONLY)
     */
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can update email.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->email = $validated['email'];
        $user->save();

        return redirect()->route('owner.profile.edit')
            ->with('success', 'Email updated successfully.');
    }

    /**
     * Update password (OWNER ONLY) - requires current password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can update password.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('owner.profile.edit')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Upload/update profile photo (OWNER ONLY)
     * Stores real image in DB (BLOB) with MIME type and timestamp
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can update profile photo.');
        }

        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:4096'], // 4MB
        ]);

        $file   = $validated['photo'];
        $binary = file_get_contents($file->getRealPath());
        $mime   = $file->getMimeType() ?: 'image/jpeg';

        $user->profile_image = $binary;
        $user->profile_image_mime = $mime;
        $user->profile_image_updated_at = now();
        $user->save();

        return redirect()->route('owner.profile.edit')
            ->with('success', 'Profile photo updated.');
    }

    /**
     * Delete profile photo (OWNER ONLY)
     */
    public function deletePhoto(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can delete profile photo.');
        }

        $user->profile_image = null;
        $user->profile_image_mime = null;
        $user->profile_image_updated_at = null;
        $user->save();

        return redirect()->route('owner.profile.edit')
            ->with('success', 'Profile photo removed.');
    }

    /**
     * Stream avatar image from DB (OWNER ONLY)
     * Returns 1x1 transparent PNG if missing
     */
    public function avatar(): Response
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can access avatar.');
        }

        if (!$user->profile_image) {
            $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5YII=');
            return response($png, 200, [
                'Content-Type'   => 'image/png',
                'Cache-Control'  => 'no-cache, no-store, must-revalidate',
            ]);
        }

        $headers = [
            'Content-Type'  => $user->profile_image_mime ?: 'image/jpeg',
            'Cache-Control' => 'private, max-age=0, no-cache',
        ];
        if ($user->profile_image_updated_at) {
            $headers['Last-Modified'] = $user->profile_image_updated_at->toRfc7231String();
        }

        return response($user->profile_image, 200, $headers);
    }

    /**
     * Delete account (OWNER ONLY)
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name !== 'owner' && !$user->isOwner()) {
            abort(403, 'Only business owners can delete their account.');
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}