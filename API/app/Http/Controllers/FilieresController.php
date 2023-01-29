<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Filiere;
use App\models\Module;
use App\models\Matiere;
use App\models\Professeur;
use App\models\Event;
use App\models\User;
use App\models\Module_matiere;

class FilieresController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public static function getfilieresByProf($id_prof){
        $date = date('Y-m-d');
        $hours = date('H');
        $minutes = date('i');
        $secondes = date('s');
        $totalSecondes = $hours*3600 + $minutes*60 + $secondes;
        $first = 28800;
        $second = 36000;
        $third = 46800;
        $fourth = 54000;

        $date_debut = 0;
        $totalFirst = $totalSecondes - $first;
        $totalSecond = $totalSecondes - $second;
        $totalThird = $totalSecondes - $third;
        $totalFourth = $totalSecondes - $fourth;
    
        if($totalFirst <= 5460 && $totalFirst >=0){
            $date_debut = $date.' 09:00:00';
        }
        else if($totalSecond <= 5460 && $totalSecond >=0){
            $date_debut = $date.' 11:00:00';
        }
        else if($totalThird <= 5460 && $totalThird >=0){
            $date_debut = $date.' 14:00:00';
        }
        else if($totalFourth <= 5460 && $totalFourth >=0){
            $date_debut = $date.' 16:00:00';
        }
        // Tous les emplois correspondants à la date locale (+1)
        $emplois = Event::where('start', '=', $date_debut)->get();
        // La liste des emplois correspondants au prof 103 (Afficher a select tag)
        $filieres = [];
        foreach($emplois as $emploi){
            // Les modules filieres correspondants à le prof 103 (id session)
            $matiere_filieres = Filiere::where('id', '=', $emploi['id_module_filiere'])->where('id_professeur', '=', $id_prof)->get();
            foreach($matiere_filieres as $matiere_filiere){

                // Extraction du filiere
                $id_filiere = $matiere_filiere['id_filiere'];
                $filiere = $matiere_filiere['libelle'];

                // Extraction du module
                $id_matiere = $matiere_filiere['id_matiere'];
                $matiere = Matiere::where('id', '=', $id_matiere)->first();                
                
                // l'ajoute de la filiere et du module à la liste des emplois;
                $list = array('id_filiere'=>$id_filiere ,'filiere'=>$filiere, 'matiere'=>$matiere['libelle']);
                array_push($filieres, $list);
                // array_push($filieres, 'f': $filiere['libelle']);
            }
        }
        $infos = [
            'filieres'=>$filieres,
            'date_debut'=>$date_debut
        ];
        return $infos;
    }
    
    public function index()
    {
        if(auth()->user()->role == "administrateur"){
            // return Module_matiere::all();
            $ids_filieres = Filiere::select('id_filiere')->distinct()->get();
            
            $filieres = [];
            
            if(count($ids_filieres)){
                foreach($ids_filieres as $id){
                    $filiere = Filiere::select('id_filiere', 'libelle', 'niveau', 'coord')->where('id_filiere', '=', $id['id_filiere'])->first();
                    $ids = Filiere::select('id')->where('id_filiere', '=', $id['id_filiere'])->get();
                    $ids_matieres = Filiere::select('id_matiere')->where('id_filiere', '=', $id['id_filiere'])->get();
                    $ids_professeurs = Filiere::select('id_professeur')->where('id_filiere', '=', $id['id_filiere'])->get();
                    $matieres_and_profs = [];
                    if(count($ids_matieres) && (count($ids_matieres) == count($ids_professeurs))){
                        for($i=0; $i<count($ids_matieres); $i++){
                            // $module_matiere = Module_matiere::where('id', '=', $ids_modules_matieres[$i]['id_matiere'])->first();
                            // if($module_matiere){
                            // return $module_matiere;
                            // $module = Module::select('libelle')->where('id', $module_matiere['id_module'])->first();
                            $matiere = Matiere::select('libelle')->where('id', $ids_matieres[$i]['id_matiere'])->first();
                            $professeur = Professeur::select('nom', 'prenom')->where('id', '=', $ids_professeurs[$i]['id_professeur'])->first();
                            
                            $tmp = [];
                            array_push($tmp, $ids_matieres[$i]['id_matiere']);
                            array_push($tmp, $matiere['libelle']);
                            array_push($tmp, $professeur);
                            array_push($matieres_and_profs, $tmp);
                            
                        }
                    }
                    
                    $filiere['matieres_and_profs'] = $matieres_and_profs;

                    $coord = Professeur::select('nom', 'prenom')->where('id', $filiere['coord'])->first();
                    $filiere['coord'] = $coord['nom'].' '.$coord['prenom'];
                    array_push($filieres, $filiere);
                }
            }
            return view('filieres.index',['filieres'=>$filieres]);
        }
        return back();
    }

    public function create()
    {
        if(auth()->user()->role == "administrateur"){
            $modules = Module::select('id', 'libelle')->get();
            if(count($modules)){
                for($i=0; $i<count($modules); $i++){
                    $module_matieres = Module_matiere::where('id_module', $modules[$i]['id'])->get();
                    $matieres = [];
                    if(count($module_matieres)){
                        foreach($module_matieres as $module_matiere){
                            $id = $module_matiere['id_matiere'];
                            $matiere_info = Matiere::where('id', $id)->first();
                            $matiere = [
                                'id_module_matiere' => $module_matiere['id'],
                                'libelle' => $matiere_info['libelle']
                            ];
                            array_push($matieres, $matiere);
                            $matiere = [];
                        }
                        $modules[$i]['matieres'] = $matieres;
                    }
                    
                }
            }
            $professeurs = Professeur::select('id', 'nom', 'prenom')->get();
            $users_coords = User::select('id')->where('role', 'coord')->get();
            $users_coords_ids = [];
            foreach($users_coords as $user_coord){
                array_push($users_coords_ids, $user_coord['id']);
            }
            $coords = Professeur::select('id', 'nom', 'prenom')->whereIn('id', $users_coords_ids)->get();
            
            return view('filieres.create', compact('modules', 'professeurs', 'coords'));
        }
        return back();
    }

   
    public function store(Request $request)
    {
        // return $request;
        
        $libelle = $request->libelle;
        $niveau = $request->niveau;
        $modules_matieres = $request->modules;
        $professeurs = $request->professeurs;
        $coord = $request->coord;
        
        //random
        $id_filiere = time();

        $modules = [];
        $new_professeurs = [];
        $new_modules_matieres = [];
        for($i=(count($modules_matieres)-1); $i>=0; $i--){
            $id_module_matiere = $modules_matieres[$i];
            $matiere = Module_matiere::select('id_matiere')->where('id', $id_module_matiere)->first();
            if(!in_array($matiere['id_matiere'], $modules)){
                array_push($modules, $matiere['id_matiere']);
                array_push($new_professeurs, $professeurs[$i]);
                array_push($new_modules_matieres, $modules_matieres[$i]);
            }
        }

        $nbr_modules_professeurs = count($modules);
        $filiere = [];
        for($i = 0; $i<$nbr_modules_professeurs; $i++){
            array_push($filiere, 
                        [
                            'id_filiere'=> $id_filiere, 
                            'libelle'=>$libelle,
                            'niveau'=>$niveau, 
                            'id_matiere'=> $modules[$i],
                            'id_module_matiere' => $new_modules_matieres[$i],
                            'id_professeur'=> $new_professeurs[$i], 
                            'coord'=> $coord
                        ]
                    );
        }
        
        Filiere::Insert($filiere);
      
        
        return redirect('/filieres')->with('message','le filiere a été créée!');
    }

   

   
    public function destroy($id)
    {
        
        Filiere::where('id_filiere', '=', $id)->delete();
        return redirect('/filieres')->with('message','La filiere sélectionnée a été supprimée!');
    }

    public function edit($id){
        if(auth()->user()->role == "administrateur"){
            $filiere = Filiere::where('id_filiere', '=', $id)->first();
            return view('filieres.edit', compact('filiere'));
        }
        return back();
    }

    public function update(Request $request){
        Filiere::where('id_filiere', '=', $request->id)->update(['libelle'=> $request->libelle, 'niveau'=> $request->niveau]);
        
        return redirect('/filieres');
    }

    public function delete_matiere_filiere($id_filiere, $id_matiere){
        // Module_matiere::where('id', $id)->delete();
        Filiere::where('id_filiere', $id_filiere)->where('id_matiere', $id_matiere)->delete();
        return redirect("/filieres");
    }
}
