<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $parentDestinationsQuery = Destination::whereNull('parent_id')->withCount('children');

        if ($request->filled('parent_search')) {
            $parentDestinationsQuery->where('name', 'like', '%' . $request->parent_search . '%');
        }

        $parentSort = $request->input('parent_sort', 'default');
        $parentDirection = $request->input('parent_direction', 'desc');
        if (!in_array($parentDirection, ['asc', 'desc'])) {
            $parentDirection = 'desc';
        }

        switch ($parentSort) {
            case 'name':
                $parentDestinationsQuery->orderBy('name', $parentDirection);
                break;
            case 'children_count':
                $parentDestinationsQuery->orderBy('children_count', $parentDirection);
                break;
            default:
                $parentDestinationsQuery->orderBy('created_at', $parentDirection);
                break;
        }

        $parentDestinations = $parentDestinationsQuery->get();
        $childDestinationsQuery = Destination::whereNotNull('parent_id')->with('parent');

        if ($request->filled('child_search')) {
            $childDestinationsQuery->where('name', 'like', '%' . $request->child_search . '%');
        }

        if ($request->filled('filter_parent')) {
            $parent = Destination::where('slug', $request->filter_parent)->first();
            if ($parent) {
                $childDestinationsQuery->where('parent_id', $parent->id);
            }
        }

        $childSort = $request->input('child_sort', 'default');
        $childDirection = $request->input('child_direction', 'desc');
        if (!in_array($childDirection, ['asc', 'desc'])) {
            $childDirection = 'desc';
        }

        switch ($childSort) {
            case 'name':
                $childDestinationsQuery->orderBy('name', $childDirection);
                break;
            default:
                $childDestinationsQuery->orderBy('created_at', $childDirection);
                break;
        }

        $perPage = $request->input('perPage', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }
        $childDestinations = $childDestinationsQuery->paginate($perPage);

        return view('admin.destinations.index', [
            'parentDestinations' => $parentDestinations,
            'childDestinations' => $childDestinations,
            'requestInput' => $request->all(),
            'perPage' => $perPage
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
