<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display user's favorites
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'all');

        $query = $user->favorites()->with('favoriteable');

        if ($type !== 'all') {
            $modelClass = 'App\\Models\\' . ucfirst($type);
            $query->where('favoriteable_type', $modelClass);
        }

        $favorites = $query->latest()->paginate(20);

        return view('user.favorites', compact('favorites', 'type'));
    }

    /**
     * Toggle favorite status
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|in:store,coupon,deal,product',
            'id' => 'required|integer|exists:' . $request->type . 's,id'
        ]);

        $user = Auth::user();
        $modelClass = 'App\\Models\\' . ucfirst($request->type);
        
        $favorite = Favorite::where([
            'user_id' => $user->id,
            'favoriteable_type' => $modelClass,
            'favoriteable_id' => $request->id
        ])->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoriteable_type' => $modelClass,
                'favoriteable_id' => $request->id
            ]);
            $isFavorited = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'favorited' => $isFavorited,
                'message' => $isFavorited ? 'Added to favorites' : 'Removed from favorites'
            ]);
        }

        return back()->with('success', $isFavorited ? 'Added to favorites' : 'Removed from favorites');
    }

    /**
     * Remove from favorites
     */
    public function remove(Request $request, Favorite $favorite)
    {
        if ($favorite->user_id !== Auth::id()) {
            abort(403);
        }

        $favorite->delete();

        return back()->with('success', 'Removed from favorites');
    }
}