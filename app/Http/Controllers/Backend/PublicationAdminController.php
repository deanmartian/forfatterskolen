<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\Request;

class PublicationAdminController extends Controller
{
    public function index()
    {
        $publications = Publication::with('user')->orderBy('created_at', 'desc')->paginate(25);
        return view('backend.publications.index', compact('publications'));
    }

    public function show($id)
    {
        $publication = Publication::with('user')->findOrFail($id);
        return view('backend.publications.show', compact('publication'));
    }

    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);
        $publication->delete();
        return redirect()->route('admin.publications.index')->with('success', 'Publikasjon slettet.');
    }
}
