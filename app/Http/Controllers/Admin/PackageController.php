<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packagesQuery = Package::with(['partner', 'destination'])->latest();

        if ($request->filled('search')) {
            $packagesQuery->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('filter_destination')) {
            $packagesQuery->where('destination_id', $request->filter_destination);
        }
        if ($request->filled('filter_status')) {
            $packagesQuery->where('status', $request->filter_status);
        }

        $packages = $packagesQuery->paginate(10);
        $destinations = Destination::whereNotNull('parent_id')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $partners = User::where('role', 'partner')->orderBy('name')->get();

        return view('admin.packages.index', [
            'packages' => $packages,
            'destinations' => $destinations,
            'categories' => $categories,
            'partners' => $partners,
        ]);
    }

    public function updateStatus(Request $request, Package $package)
    {
        $validated = $request->validate([
            'status' => 'required|in:publish,pending,draft,rejected',
        ]);

        $package->update(['status' => $validated['status']]);

        return back()->with('success', 'Status paket berhasil diperbarui!');
    }
}
