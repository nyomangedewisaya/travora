<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    private $validStatuses = ['pending', 'publish', 'draft', 'rejected'];

    public function index(Request $request)
    {
        $packagesQuery = Package::with(['partner', 'destination.parent', 'category', 'media']); // Eager load relasi

        if ($request->filled('search')) {
            $packagesQuery->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('filter_destination')) {
            $destination = Destination::where('slug', $request->filter_destination)->first();
            if ($destination) {
                $packagesQuery->where('destination_id', $destination->id);
            }
        }

        if ($request->filled('filter_category')) {
            $category = Category::where('slug', $request->filter_category)->first();
            if ($category) {
                $packagesQuery->where('category_id', $category->id);
            }
        }

        if ($request->filled('filter_status') && in_array($request->filter_status, $this->validStatuses)) {
            $packagesQuery->where('status', $request->filter_status);
        }

        $sortBy = $request->input('sort_by', 'default');
        $direction = $request->input('direction', 'desc');
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $packagesQuery->orderBy('name', $direction);
                break;
            case 'status':
                $packagesQuery->orderBy('status', $direction);
                break;
            case 'price':
                $packagesQuery->orderBy('price', $direction);
                break;
            default:
                // Default = created_at desc (terbaru)
                $packagesQuery->orderBy('created_at', $direction);
                break;
        }

        // Pagination
        $perPage = $request->input('perPage', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }
        $packages = $packagesQuery->paginate($perPage);
        $destinations = Destination::whereNotNull('parent_id')->orderBy('name')->get(); // Hanya anak
        $categories = Category::orderBy('name')->get();
        $partners = User::where('role', 'partner')->orderBy('name')->get();

        return view('admin.packages.index', [
            'packages' => $packages,
            'destinations' => $destinations,
            'categories' => $categories,
            'partners' => $partners,
            'statuses' => $this->validStatuses, // Kirim status valid
            'requestInput' => $request->all(), // Kirim semua input untuk retain value
            'perPage' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages',
            'partner_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'category_id' => 'required|exists:categories,id',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'status' => 'required|in:pending,publish,draft,rejected',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $package = Package::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'partner_id' => $validated['partner_id'],
            'destination_id' => $validated['destination_id'],
            'category_id' => $validated['category_id'],
            'duration_days' => $validated['duration_days'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages', 'public');
            $package->media()->create([
                'file_path' => $path,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.managements.packages.index')->with('success', 'Paket wisata berhasil ditambahkan!');
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,' . $package->id,
            'partner_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'category_id' => 'required|exists:categories,id',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'status' => 'required|in:pending,publish,draft,rejected',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $package->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'partner_id' => $validated['partner_id'],
            'destination_id' => $validated['destination_id'],
            'category_id' => $validated['category_id'],
            'duration_days' => $validated['duration_days'],
            'price' => $validated['price'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('image')) {
            $existingMedia = $package->media()->first();
            if ($existingMedia) {
                Storage::disk('public')->delete($existingMedia->file_path);
                $existingMedia->delete();
            }

            $path = $request->file('image')->store('packages', 'public');
            $package->media()->create([
                'file_path' => $path,
                'type' => 'image',
            ]);
        }

        return redirect()->route('admin.managements.packages.index')->with('success', 'Paket wisata berhasil diperbarui!');
    }

    public function destroy(Package $package)
    {
        $existingMedia = $package->media()->first();
        if ($existingMedia) {
            Storage::disk('public')->delete($existingMedia->file_path);
            $existingMedia->delete();
        }

        $package->delete();

        return redirect()->route('admin.managements.packages.index')->with('success', 'Paket wisata berhasil dihapus!');
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
