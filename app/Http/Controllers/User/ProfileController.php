<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Display user dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'favorite_stores' => $user->favorites()->where('favoriteable_type', 'App\Models\Store')->count(),
            'favorite_coupons' => $user->favorites()->where('favoriteable_type', 'App\Models\Coupon')->count(),
            'favorite_deals' => $user->favorites()->where('favoriteable_type', 'App\Models\Deal')->count(),
            'favorite_products' => $user->favorites()->where('favoriteable_type', 'App\Models\Product')->count(),
            'total_clicks' => \App\Models\CouponClick::where('user_id', $user->id)->count() + 
                           \App\Models\AffiliateClick::where('user_id', $user->id)->count(),
        ];

        // Get recent activities
        $recentCouponClicks = \App\Models\CouponClick::with('coupon')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentAffiliateClicks = \App\Models\AffiliateClick::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('user', 'stats', 'recentCouponClicks', 'recentAffiliateClicks'));
    }

    /**
     * Show the profile form
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $user->fill($request->only(['name', 'email', 'phone', 'date_of_birth', 'gender', 'bio']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->hasMedia('avatar')) {
                $user->clearMediaCollection('avatar');
            }
            $user->addMediaFromRequest('avatar')
                 ->toMediaCollection('avatar');
        }

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Rules\Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}