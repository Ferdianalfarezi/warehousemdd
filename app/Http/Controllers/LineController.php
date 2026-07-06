<?php

namespace App\Http\Controllers;

use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LineController extends Controller
{
    // Data-nya sedikit & jarang berubah -> render langsung via Blade, bukan AJAX table
    public function index()
    {
        return view('lines.index', [
            'lines' => Line::withCount('barangs')->orderBy('nama_line')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $line = Line::create($request->only('nama_line', 'mesin'));

        return response()->json(['success' => true, 'message' => 'Line berhasil ditambahkan!', 'data' => $line]);
    }

    public function update(Request $request, Line $line)
    {
        $validator = Validator::make($request->all(), $this->rules($line->id));
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $line->update($request->only('nama_line', 'mesin'));

        return response()->json(['success' => true, 'message' => 'Line berhasil diupdate!', 'data' => $line]);
    }

    public function destroy(Line $line)
    {
        if ($line->barangs()->exists()) {
            return response()->json(['success' => false, 'message' => 'Line masih dipakai oleh barang, tidak bisa dihapus!'], 422);
        }

        $line->delete();

        return response()->json(['success' => true, 'message' => 'Line berhasil dihapus!']);
    }

    private function rules(?int $ignoreId = null): array
    {
        $unique = $ignoreId ? "unique:lines,nama_line,{$ignoreId}" : 'unique:lines,nama_line';

        return [
            'nama_line' => "required|string|max:255|{$unique}",
            'mesin'     => 'nullable|string|max:255',
        ];
    }
}