<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\AccommodationRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccommodationRoomController extends Controller
{
    public function index(Request $request)
    {
        $selectedAccommodation = null;
        $rooms = null;
        $accommodations = null;

        // STATE 2: Menampilkan Kamar dari Akomodasi yang Dipilih
        if ($request->has('accommodation')) {
            $selectedAccommodation = Accommodation::where('slug', $request->accommodation)
                                        ->with('destination')
                                        ->firstOrFail();
            
            $roomsQuery = $selectedAccommodation->rooms()->with('media')->latest();

            // Filter pencarian untuk kamar
            if ($request->filled('search_room')) {
                $roomsQuery->where('name', 'like', '%' . $request->search_room . '%');
            }

            $rooms = $roomsQuery->paginate(10);
        
        // STATE 1: Menampilkan Daftar Akomodasi untuk Dipilih
        } else {
            $accommodationsQuery = Accommodation::with('media', 'destination.parent')->latest();

            // Filter pencarian untuk akomodasi
            if ($request->filled('search_acc')) {
                $accommodationsQuery->where('name', 'like', '%' . $request->search_acc . '%');
            }
            
            $accommodations = $accommodationsQuery->paginate(9); // Kelipatan 3 untuk grid
        }

        return view('admin.accommodations.rooms', [
            'accommodations' => $accommodations,
            'selectedAccommodation' => $selectedAccommodation,
            'rooms' => $rooms,
            'requestInput' => $request->all(),
        ]);
    }

    /**
     * Hapus data kamar (HANYA HAPUS).
     */
    public function destroy(AccommodationRoom $room) // Asumsi $id adalah primary key
    {
        // Hapus media terkait (jika ada)
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
