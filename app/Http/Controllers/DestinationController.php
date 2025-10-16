<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $parentDestinations = Destination::whereNull('parent_id')->withCount('children')->latest()->get();

        $childDestinationsQuery = Destination::whereNotNull('parent_id')->with('parent')->latest();

        if ($request->filled('search')) {
            $childDestinationsQuery->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('filter_parent')) {
            $parent = Destination::where('slug', $request->filter_parent)->first();

            if ($parent) {
                $childDestinationsQuery->where('parent_id', $parent->id);
            }
        }

        $childDestinations = $childDestinationsQuery->paginate(10);

        return view('admin.destinations.index', [
            'parentDestinations' => $parentDestinations,
            'childDestinations' => $childDestinations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations',
            'parent_id' => 'nullable|exists:destinations,id',
            'description' => 'required|string',
            'address' => 'nullable|string|max:255',
            'hero_image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $coordinates = ['latitude' => null, 'longitude' => null];
        if (!empty($validated['address'])) {
            $coordinates = $this->getCoordinatesFromAddress($validated['address']);
        }

        $imagePath = null;
        if ($request->hasFile('hero_image_url')) {
            $imagePath = $request->file('hero_image_url')->store('destinations', 'public');
        }

        Destination::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'],
            'description' => $validated['description'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'hero_image_url' => $imagePath,
        ]);

        return redirect()->route('admin.managements.destinations.index')->with('success', 'Destinasi berhasil ditambahkan!');
    }

    public function update(Request $request, Destination $destination)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:destinations,name,' . $destination->id,
            'parent_id' => 'nullable|exists:destinations,id',
            'description' => 'required|string',
            'address' => 'nullable|string|max:255',
            'hero_image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $coordinates = ['latitude' => $destination->latitude, 'longitude' => $destination->longitude];
        if (!empty($validated['address'])) {
            $coordinates = $this->getCoordinatesFromAddress($validated['address']);
        }

        $imagePath = $destination->hero_image_url;
        if ($request->hasFile('hero_image_url')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('hero_image_url')->store('destinations', 'public');
        }

        $destination->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'],
            'description' => $validated['description'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'hero_image_url' => $imagePath,
        ]);

        return redirect()->route('admin.managements.destinations.index')->with('success', 'Destinasi berhasil diperbarui!');
    }

    public function destroy(Destination $destination)
    {
        if ($destination->hero_image_url && Storage::disk('public')->exists($destination->hero_image_url)) {
            Storage::disk('public')->delete($destination->hero_image_url);
        }

        $destination->delete();

        return redirect()->route('admin.managements.destinations.index')->with('success', 'Destinasi berhasil dihapus!');
    }

    public function geocodeAddress(Request $request)
    {
        $request->validate(['address' => 'required|string']);

        $coordinates = $this->getCoordinatesFromAddress($request->address);

        if ($coordinates['latitude'] && $coordinates['longitude']) {
            return response()->json($coordinates);
        }

        return response()->json(['error' => 'Alamat tidak ditemukan.'], 404);
    }

    private function getCoordinatesFromAddress(string $address): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Travora/1.0 (sgpserver66@gmail.com)', 
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $address,
            'format' => 'json',
            'limit' => 1,
        ]);

        if ($response->successful() && count($response->json()) > 0) {
            $location = $response->json()[0];
            return ['latitude' => $location['lat'], 'longitude' => $location['lon']];
        }
        return ['latitude' => null, 'longitude' => null];
    }
}
