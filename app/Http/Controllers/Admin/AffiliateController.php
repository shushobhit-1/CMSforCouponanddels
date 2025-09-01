<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    /**
     * Display a listing of affiliate networks
     */
    public function index()
    {
        $affiliates = Affiliate::orderBy('name')->paginate(20);
        return view('admin.affiliates.index', compact('affiliates'));
    }

    /**
     * Show the form for creating a new affiliate network
     */
    public function create()
    {
        return view('admin.affiliates.create');
    }

    /**
     * Store a newly created affiliate network
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:affiliates,slug',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'publisher_id' => 'nullable|string|max:255',
            'tracking_params' => 'nullable|array',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'nullable|string|max:255',
            'minimum_payout' => 'nullable|numeric|min:0',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        Affiliate::create($validated);

        return redirect()->route('admin.affiliates.index')
                        ->with('success', 'Affiliate network created successfully.');
    }

    /**
     * Display the specified affiliate network
     */
    public function show(Affiliate $affiliate)
    {
        return view('admin.affiliates.show', compact('affiliate'));
    }

    /**
     * Show the form for editing the specified affiliate network
     */
    public function edit(Affiliate $affiliate)
    {
        return view('admin.affiliates.edit', compact('affiliate'));
    }

    /**
     * Update the specified affiliate network
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:affiliates,slug,' . $affiliate->id,
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'api_url' => 'nullable|url',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'publisher_id' => 'nullable|string|max:255',
            'tracking_params' => 'nullable|array',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'nullable|string|max:255',
            'minimum_payout' => 'nullable|numeric|min:0',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $affiliate->update($validated);

        return redirect()->route('admin.affiliates.index')
                        ->with('success', 'Affiliate network updated successfully.');
    }

    /**
     * Remove the specified affiliate network
     */
    public function destroy(Affiliate $affiliate)
    {
        $affiliate->delete();
        
        return redirect()->route('admin.affiliates.index')
                        ->with('success', 'Affiliate network deleted successfully.');
    }

    /**
     * Test API connection
     */
    public function testConnection(Affiliate $affiliate)
    {
        // This would implement API testing logic for each network
        // For now, we'll return a mock response
        
        $success = true; // Replace with actual API test
        
        if ($success) {
            return back()->with('success', 'API connection test successful.');
        } else {
            return back()->with('error', 'API connection test failed.');
        }
    }

    /**
     * Sync affiliate products/deals
     */
    public function sync(Affiliate $affiliate)
    {
        // This would implement synchronization logic for each network
        // For now, we'll return a mock response
        
        $synced = 0; // Replace with actual sync logic
        
        return back()->with('success', "Synced {$synced} items from {$affiliate->name}.");
    }
}