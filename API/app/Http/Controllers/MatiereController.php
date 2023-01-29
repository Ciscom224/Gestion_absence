<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matiere;

class MatiereController extends Controller
{
    public function index(){
        if(auth()->user()->role == "administrateur"){
            $matieres = Matiere::select('id', 'libelle')->get();
            return view('matieres.index', compact('matieres'));
        }
        return back();
    }

    public function destroy($id){
        Matiere::where('id', '=', $id)->delete();
        $matieres = Matiere::select('id', 'libelle')->get();
        return Redirect('/matieres');
    }

    public function create()
    {
        if(auth()->user()->role == "administrateur"){
            return view('matieres.create');
        }
        return back();
    }

    public function store(Request $request){
        Matiere::create(['libelle' => $request->libelle]);
        return MatiereController::index();
    }

    public function edit($id)
    {
        if(auth()->user()->role == "administrateur"){
            $matiere = Matiere::find($id);
            return view('matieres.edit')->with('matiere',$matiere);
        }
        return back();
    }

    public function update(Request $request, $id)
    {
        Matiere::where('id', '=', $id)->update(['libelle' => $request->libelle]);
        $matieres = Matiere::select('id', 'libelle')->get();
        return Redirect('/matieres');
    }
}
