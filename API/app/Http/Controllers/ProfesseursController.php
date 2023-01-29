<?php

namespace App\Http\Controllers;

use App\models\User;
use App\models\Event;
use App\models\Module;
use App\models\Filiere;
use App\models\Matiere;
use App\models\Etudiant;
use App\models\Professeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfesseursController extends Controller
{

   public function __construct(){
      $this->middleware('auth');
  }


    public function index()
    {

        if(auth()->user()->role == "administrateur"){
            return view('prof.index',[
                'users'=>User::all(),
                'professeurs'=>Professeur::all(),
                'etudiants'=>Etudiant::all()
            ]);
        }
        return back();
    }


    public function create()
    {
        if(auth()->user()->role == "administrateur"){
            $users = [];
            $profs=User::where('role', '=', 'professeur')->get();
            $etudiants=User::where('role', '=', 'etudiant')->get();
            $professeurs = Professeur::all();
            $etuds = Etudiant::all();
            foreach($profs as $prof){
                $exist = 0;
                foreach($professeurs as $professeur){
                    if($professeur['id'] == $prof['id']){
                        $exist++;
                        break;
                    }
                }
                if(!$exist)
                    array_push($users, $prof);
            }
            foreach($etudiants as $etudiant){
                $exist = 0;
                foreach($etuds as $etud){
                    if($etud['id_etudiant'] == $etudiant['id']){
                        $exist++;
                        break;
                    }
                }
                if(!$exist)
                    array_push($users, $etudiant);
            }
            return view('prof.create')->with('users',$users);
        }
        return back();
    }

    public function store(Request $request)
    {
        $user = User::find($request->id_user);
        if($request->role == "etudiant"){
            Etudiant::Insert([
                'id_etudiant'=> $request->id_user,
                'nom'=> $user['nom'],
                'prenom'=> $user['prenom'],
                'telephone'=> $request->telephone,
                'email'=> $user['email'],
                'photo'=> $user['photo'],
                'cin'=> $request->cin,
                'cne'=> $request->cne,
                'sexe'=> $request->sexe,
                'id_filiere'=> $request->filiere
            ]);
        }
        else{
            Professeur::Insert([
                'id'=> $request->id_user,
                'nom'=> $user['nom'],
                'prenom'=> $user['prenom'],
                'telephone'=> $request->telephone,
                'email'=> $user['email'],
                'photo'=> $user['photo'],
                'cin'=> $request->cin
            ]);
        }
        return back();
    }


    public function edit($id)
    {
        if(auth()->user()->role == "administrateur"){
            $professeurs = Professeur::find($id);
            return view('prof.edit')->with('professeurs',$professeurs);
        }
        return back();
    }


    public function update(Request $request, $id)
    {
        $this->validateRequest($request,$id);

        $professeurs = Professeur::find($id);

        if($request->hasFile('photo')){

            $fileNameToStore = $this->handleImageUpload($request);
            Storage::delete('public/professeurs/'.$professeurs->photo);
        }else{
            $fileNameToStore = '';
        }

        $this->setUser($request, $professeurs ,$fileNameToStore);
        return back();    }


    public function destroy($id)
    {

        $professeurs = Professeur::find($id);
        $professeurs->delete();
        return back();
    }




    private function validateRequest(Request $request, $id)
    {
        $this->validate($request,[
            'prenom'   =>  'required|min:3',
            'nom'    =>  'required|min:3',
            'cin'    =>  'required',
            'telephone'     =>  'required',
            'email'        =>  'required|email|unique:professeurs,email,'.($id ? : '' ).'|min:7',
            'photo'      =>  'required'
        ]);
    }


    private function setUser(Request $request , Professeur $professeurs , $fileNameToStore){
        $professeurs->prenom = $request->input('prenom');
        $professeurs->nom = $request->input('nom');
        $professeurs->cin = $request->input('cin');
        $professeurs->telephone = $request->input('telephone');
        $professeurs->email = $request->input('email');

        if($request->hasFile('photo')){
            $professeurs->photo = $fileNameToStore;
        }
        $professeurs->save();
    }


    public function handleImageUpload(Request $request){
        if( $request->hasFile('photo') ){


            $filenameWithExt = $request->file('photo')->getClientOriginalName();

            $filename = pathInfo($filenameWithExt,PATHINFO_FILENAME);

            $extension = $request->file('photo')->getClientOriginalExtension();


            $fileNameToStore = $filename.'_'.time().'.'.$extension;

            $path = $request->file('photo')->move('professeurs/' , $fileNameToStore);
        }
        return $fileNameToStore;
    }

    public function get_filieres(){
        if(auth()->user()->role == "professeur"){
            $id_prof = auth()->user()->id;
            $date = date('Y-m');
            $filieres = Filiere::select('id_filiere', 'libelle')->distinct()->where('id_professeur', '=', $id_prof)->get();
            if($filieres){
                for($i=0; $i<count($filieres); $i++){
                    $emplois = [];
                    $ids_matiere_filiere = Filiere::select('id', 'id_matiere')->where('id_filiere', '=', $filieres[$i]['id_filiere'])->get();
                    foreach($ids_matiere_filiere as $id_matiere_filiere){
                        $matiere = Matiere::where('id', '=', $id_matiere_filiere['id_matiere'])->first();
                        $events = Event::where('id_module_filiere', '=', $id_matiere_filiere['id'])
                                ->where('start', 'LIKE', $date."%")->get();
                        if(count($events)){
                            foreach($events as $event){
                                $event->matiere = $matiere['libelle'];
                                array_push($emplois, $event);
                            }
                        }
                    }
                    $filieres[$i]->emplois = $emplois;
                }
            }
            return view("prof.emploi", compact('filieres'));
        }
        return back();
    }

    public function get_emploi(Request $request){
        // $id_prof = Auth::user()->id;
        $id_prof = auth()->user()->id;
        $date = $request->date;
        $filieres = Filiere::select('id_filiere', 'libelle')->distinct()->where('id_professeur', '=', $id_prof)->get();
        if($filieres){
            for($i=0; $i<count($filieres); $i++){
                $emplois = [];
                $ids_matiere_filiere = Filiere::select('id', 'id_matiere')->where('id_filiere', '=', $filieres[$i]['id_filiere'])->get();
                foreach($ids_matiere_filiere as $id_matiere_filiere){
                    $matiere = Matiere::where('id', '=', $id_matiere_filiere['id_matiere'])->first();
                    $events = Event::where('id_module_filiere', '=', $id_matiere_filiere['id'])
                            ->where('start', 'LIKE', $date."%")->get();
                    if(count($events)){
                        foreach($events as $event){
                            $event->matiere = $matiere['libelle'];
                            array_push($emplois, $event);
                        }
                    }
                }
                $filieres[$i]->emplois = $emplois;
            }
        }
        return $filieres;
    }

    //Coordinateur
    public function get_filieres_coord(){
        if(auth()->user()->role == "coord"){
            $id_prof = auth()->user()->id;
            $date = date('Y-m');
            $filieres = Filiere::select('id_filiere', 'libelle')->distinct()->where('coord', '=', $id_prof)->get();

            $emplois = [];

            $results = Event::where('id_filiere', $filieres[0]['id_filiere'])
                        ->where('start', 'LIKE', $date."%")->get();
            if($results){
                foreach($results as $result){
                    array_push($emplois, $result);
                }
            }

            if(count($emplois)){
                for($i = 0; $i<count($emplois); $i++){
                    $matiere_filiere = Filiere::select('id_professeur', 'id_matiere')
                                        ->where('id', '=', $emplois[$i]['id_module_filiere'])->first();
                                        // return($module_filiere);
                    $prof = Professeur::select('nom', 'prenom')->where('id', '=', $matiere_filiere['id_professeur'])->first();
                    $matiere = Matiere::select('libelle')->where('id', '=', $matiere_filiere['id_matiere'])->first();
                    $nomProf = $prof['prenom']." ".$prof['nom'];
                    $libelle = $matiere['libelle'];
                    $emplois[$i]->prof = $nomProf;
                    $emplois[$i]->matiere = $libelle;
                }
            }

            // return $filieres;
            return view("prof.emploi_coord", compact('filieres', 'emplois'));
        }
        return back();
    }


}
