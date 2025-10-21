<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccomodationController extends Controller
{
    private $validTypes = ['hotel', 'villa', 'homestay'];
    private $validStatuses = ['pending', 'publish', 'rejected', 'draft'];

    public function index(Request $request)
    {
        $accommodationsQuery = Accommodation::with(['partner', 'destination', 'media'])->latest();

        if ($request->filled('search')) {
            $accommodationsQuery->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('filter_destination')) {
            $accommodationsQuery->where('destination_id', $request->filter_destination);
        }
        if ($request->filled('filter_type')) {
            $accommodationsQuery->where('type', $request->filter_type);
        }

        $accommodations = $accommodationsQuery->paginate(10);
        $destinations = Destination::whereNotNull('parent_id')->orderBy('name')->get();
        $partners = User::where('role', 'partner')->orderBy('name')->get();

        return view('admin.accommodations.index', [
            'accommodations' => $accommodations,
            'destinations' => $destinations,
            'partners' => $partners,
            'types' => $this->validTypes,
            'statuses' => $this->validStatuses 
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:accommodations',
            'partner_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'type' => ['required', Rule::in($this->validTypes)],
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', Rule::in($this->validStatuses)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_verified' => 'nullable|boolean',
        ]);

        $accommodation = Accommodation::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'partner_id' => $validated['partner_id'],
            'destination_id' => $validated['destination_id'],
            'type' => $validated['type'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'is_verified' => $request->boolean('is_verified'),
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('accommodations', 'public');
            $accommodation->media()->create(['file_path' => $path, 'type' => 'image']);
        }

        return redirect()->route('admin.managements.accommodations.index')->with('success', 'Akomodasi berhasil ditambahkan!');
    }

    public function update(Request $request, Accommodation $accommodation)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255|unique:accommodations,name,' . $accommodation->id,
            'partner_id' => 'required|exists:users,id',
            'destination_id' => 'required|exists:destinations,id',
            'type' => ['required', Rule::in($this->validTypes)],
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', Rule::in($this->validStatuses)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_verified' => 'nullable|boolean',
        ]);

        $accommodation->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'partner_id' => $validated['partner_id'],
            'destination_id' => $validated['destination_id'],
            'type' => $validated['type'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'is_verified' => $request->boolean('is_verified'),
        ]);

        if ($request->hasFile('image')) {
            $existingMedia = $accommodation->media()->first();
            if ($existingMedia) {
                Storage::disk('public')->delete($existingMedia->file_path);
                $existingMedia->delete();
            }
            $path = $request->file('image')->store('accommodations', 'public');
            $accommodation->media()->create(['file_path' => $path, 'type' => 'image']);
        }

        return redirect()->route('admin.managements.accommodations.index')->with('success', 'Akomodasi berhasil diperbarui!');
    }

    public function destroy(Accommodation $accommodation)
    {
       $existingMedia = $accommodation->media()->first();
        if ($existingMedia) {
            Storage::disk('public')->delete($existingMedia->file_path);
            $existingMedia->delete();
        }
        $accommodation->delete();
       return redirect()->route('admin.managements.accommodations.index')->with('success', 'Akomodasi berhasil dihapus!');
    }

    public function updateStatus(Request $request, Accommodation $accommodation)
    {
        $validated = $request->validate([
            'status' => 'required|in:publish,pending,draft,rejected',
        ]);

        $accommodation->update(['status' => $validated['status']]);

        return back()->with('success', 'Status akomodasi berhasil diperbarui!');
    }
}
