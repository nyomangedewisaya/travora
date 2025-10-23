<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccommodationController extends Controller
{
    private $validTypes = ['hotel', 'villa', 'homestay'];
    private $validStatuses = ['pending', 'publish', 'rejected', 'draft'];

    public function index(Request $request)
    {
        $accommodationsQuery = Accommodation::with(['partner', 'destination.parent', 'media'])->withCount('rooms');

        if ($request->filled('search')) {
            $accommodationsQuery->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('filter_destination')) {
            $dest = Destination::where('slug', $request->filter_destination)->first();
            if ($dest) {
                $accommodationsQuery->where('destination_id', $dest->id);
            }
        }
        if ($request->filled('filter_type')) {
            $accommodationsQuery->where('type', $request->filter_type);
        }
        if ($request->filled('filter_status') && in_array($request->filter_status, $this->validStatuses)) {
            $accommodationsQuery->where('status', $request->filter_status);
        }

        $sortBy = $request->input('sort_by', 'default');
        $direction = $request->input('direction', 'desc');
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $accommodationsQuery->orderBy('name', $direction);
                break;
            case 'status':
                $accommodationsQuery->orderBy('status', $direction);
                break;
            default:
                $accommodationsQuery->orderBy('created_at', $direction);
                break;
        }

        $perPage = $request->input('perPage', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }
        $accommodations = $accommodationsQuery->paginate($perPage);
        $destinations = Destination::whereNotNull('parent_id')->orderBy('name')->get();
        $partners = User::where('role', 'partner')->orderBy('name')->get();

        return view('admin.accommodations.index', [
            'accommodations' => $accommodations,
            'destinations' => $destinations,
            'partners' => $partners,
            'types' => $this->validTypes,
            'statuses' => $this->validStatuses,
            'requestInput' => $request->all(),
            'perPage' => $perPage,
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
            'is_verified' => 'nullable|string|in:1',
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
            'is_verified' => $request->input('is_verified') === '1',
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
            'is_verified' => 'nullable|string|in:1',
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
            'is_verified' => $request->input('is_verified') === '1',
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

    public function updateVerificationStatus(Accommodation $accommodation)
    {
        $accommodation->update(['is_verified' => !$accommodation->is_verified]);

        $message = $accommodation->is_verified ? 'Akomodasi berhasil diverifikasi!' : 'Verifikasi akomodasi berhasil dibatalkan!';

        return back()->with('success', $message);
    }

    public function indexRooms(Request $request, ?Accommodation $accommodation = null)
    {
        $rooms = null;
        $accommodations = null;
        $perPage = $request->input('perPage', 10); // Perbaikan 3: Pindahkan $perPage ke atas
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        // STATE 2: Akomodasi SUDAH dipilih
        if ($accommodation) {
            $selectedAccommodation = $accommodation->load('destination');
            $roomsQuery = $selectedAccommodation->rooms()->with('media')->latest();

            if ($request->filled('search_room')) {
                $roomsQuery->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search_room . '%')->orWhere('room_number', 'like', '%' . $request->search_room . '%');
                });
            }

            $rooms = $roomsQuery->paginate($perPage);

            // STATE 1: Akomodasi BELUM dipilih
        } else {
            $accommodationsQuery = Accommodation::with(['media', 'destination.parent', 'rooms'])
                ->withCount('rooms')
                ->latest();

            if ($request->filled('search_acc')) {
                $accommodationsQuery->where('name', 'like', '%' . $request->search_acc . '%');
            }

            $accommodations = $accommodationsQuery->paginate(9); // Gunakan pagination terpisah untuk grid
        }

        return view('admin.accommodations.rooms', [
            'accommodations' => $accommodations,
            'selectedAccommodation' => $accommodation,
            'rooms' => $rooms,
            'requestInput' => $request->all(),
            'perPage' => $perPage, // $perPage sekarang selalu terdefinisi
        ]);
    }

    public function destroyRoom(AccommodationRoom $room)
    {
        if ($room->media->isNotEmpty()) {
            foreach ($room->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
        }

        $room->delete();

        return back()->with('success', 'Data kamar berhasil dihapus!');
    }
}
