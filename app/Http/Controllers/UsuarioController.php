<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuarios;
use Carbon\Carbon;
use App\Models\partidas;
class UsuarioCONtroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $usuarios = array();
        $usuarios_db = usuarios::all()->where('Acepto',$request->input('Acepto'));
        foreach ($usuarios_db as $user) {
            $user = usuarios::find($user->id);
            $partidas = partidas::where("idJugador", "=", $user->id)->get();
            array_push($usuarios,array(
                "user"=>$user,
                "cantidad_partidas"=>count($partidas) 
            ));
            usort($usuarios, function ($jugadorA, $jugadorB) {
                return $jugadorB['cantidad_partidas'] <=> $jugadorA['cantidad_partidas'];
            });
            $usuarios = array_slice($usuarios,0,10);
        }
        return $usuarios;
        // $usuarios = usuarios::all()->where('Acepto',$request->input('Acepto'))->take(10)->
        // return $usuarios;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usuarios_db = usuarios::all();

        // $usuarios = usuarios::all()->where('fechaRegistro',$request->input('initial_date'));
        $start_date = Carbon::parse(request()->input('initial_date'))->toDateTimeString();
        $end_date = Carbon::parse(request()->input('end_date'))->toDateTimeString();
        $usuarios = usuarios::where('Nombre', 'like' ,$request->input('letter_search').'%')->whereBetween
        ('fechaRegistro',[$start_date,$end_date])->get();
        $procentaje = 100/count($usuarios_db) * count($usuarios);
        return "EL porcentaje es : " . $procentaje . "%";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display a listing of the resource.
     **@param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function winners($id)
    {
        $usuarios = array();
        $usuarios_db = usuarios::all()->where('idDisfraz',$id);
        foreach ($usuarios_db as $user) {
            $user = usuarios::find($user->id);
            $partidas = partidas::where("idJugador", "=", $user->id)->get();
            $scores = array();
            $max_score = 0;
            foreach ($partidas as $partida) {
                array_push($scores,array(
                    "score" => $partida->puntos
                ));
                usort($scores, function ($scoreA, $scoreB) {
                    return $scoreB['score'] <=> $scoreA['score'];
                });
                $max_score = $scores[0]['score'];
            }
            array_push($usuarios,array(
                "user"=>$user,
                "max_score"=> $max_score
            ));
            usort($usuarios, function ($jugadorA, $jugadorB) {
                return $jugadorB['max_score'] <=> $jugadorA['max_score'];
            });
            $usuarios = array_slice($usuarios,0,10);
        }
        return $usuarios;
    }
}
