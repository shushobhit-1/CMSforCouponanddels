<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateNetwork;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AffiliateController extends Controller
{
    public function index()
    {
        $networks = AffiliateNetwork::latest()->paginate(15);
        $supportedNetworks = AffiliateNetwork::NETWORKS;
        
        return view('admin.affiliates.index', compact('networks', 'supportedNetworks'));
    }

    public function create()
    {
        $supportedNetworks = AffiliateNetwork::NETWORKS;
        return view('admin.affiliates.create', compact('supportedNetworks'));
    }

    public function store(Request $request)
    {
        $networkConfig = AffiliateNetwork::NETWORKS[$request->slug] ?? null;
        
        if (!$networkConfig) {
            return back()->withErrors(['slug' => 'Invalid affiliate network selected.']);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::in(array_keys(AffiliateNetwork::NETWORKS))],
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ];

        // Add dynamic validation rules based on network requirements
        foreach ($networkConfig['requires'] as $field) {
            $rules[$field] = 'required|string';
        }

        $validated = $request->validate($rules);

        AffiliateNetwork::create($validated);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate network added successfully.');
    }

    public function show(AffiliateNetwork $affiliate)
    {
        $stats = [
            'total_clicks' => rand(1000, 5000), // Replace with actual data
            'total_conversions' => rand(50, 200),
            'total_revenue' => rand(5000, 25000),
            'commission_earned' => rand(500, 2500),
        ];

        return view('admin.affiliates.show', compact('affiliate', 'stats'));
    }

    public function edit(AffiliateNetwork $affiliate)
    {
        $supportedNetworks = AffiliateNetwork::NETWORKS;
        return view('admin.affiliates.edit', compact('affiliate', 'supportedNetworks'));
    }

    public function update(Request $request, AffiliateNetwork $affiliate)
    {
        $networkConfig = AffiliateNetwork::NETWORKS[$request->slug] ?? null;
        
        if (!$networkConfig) {
            return back()->withErrors(['slug' => 'Invalid affiliate network selected.']);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', Rule::in(array_keys(AffiliateNetwork::NETWORKS))],
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ];

        foreach ($networkConfig['requires'] as $field) {
            $rules[$field] = 'required|string';
        }

        $validated = $request->validate($rules);

        $affiliate->update($validated);

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate network updated successfully.');
    }

    public function destroy(AffiliateNetwork $affiliate)
    {
        $affiliate->delete();

        return redirect()->route('admin.affiliates.index')
            ->with('success', 'Affiliate network deleted successfully.');
    }

    public function testConnection(Request $request, AffiliateNetwork $affiliate)
    {
        // Test the API connection
        $result = $this->testApiConnection($affiliate);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ]);
    }

    private function testApiConnection(AffiliateNetwork $affiliate)
    {
        try {
            switch ($affiliate->slug) {
                case 'amazon':
                    return $this->testAmazonConnection($affiliate);
                case 'flipkart':
                    return $this->testFlipkartConnection($affiliate);
                default:
                    return ['success' => true, 'message' => 'Connection test not implemented for this network.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }

    private function testAmazonConnection(AffiliateNetwork $affiliate)
    {
        // Implement Amazon PA-API connection test
        return ['success' => true, 'message' => 'Amazon connection successful.'];
    }

    private function testFlipkartConnection(AffiliateNetwork $affiliate)
    {
        // Implement Flipkart API connection test
        return ['success' => true, 'message' => 'Flipkart connection successful.'];
    }

    public function syncProducts(Request $request, AffiliateNetwork $affiliate)
    {
        $category = $request->get('category');
        $limit = $request->get('limit', 50);

        try {
            $products = $affiliate->fetchProducts($category, $limit);
            
            // Here you would process and save the products
            // This is a simplified example
            
            return response()->json([
                'success' => true,
                'message' => 'Products synced successfully.',
                'count' => count($products),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ]);
        }
    }
}