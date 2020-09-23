<?php

namespace App\Http\Controllers;

use App\Car;
use App\Sale;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function statistics(Request $request){


        if($request->user()->AuthorizeRoles(['Admin'])){

            $tcars = Car::where('state', 'En venta')->count();
            $tcomp = $this->total_users_role('comprador');
            $tvend = $this->total_users_role('vendedor');
            $mvend = $this->bestvend();
            $ventas_por_mes = Sale::whereBetween('created_at', ['2020-09-01', '2020-09-30'])->get();

            return response()->json(['compradores_afiliados'=>$tcomp,
                                    'vendedores_afiliados'=> $tvend,
                                    'carros_en_venta'=>$tcars,
                                    'Mejor_Vendedor'=> User::find($mvend),
                                    'ventas_por_mes' => $ventas_por_mes->count()]);
        }else if($request->user()->AuthorizeRoles(['Vendedor'])){
            $user = User::find(auth()->user()->id);
            $cars = Car::where([['user_id', auth()->user()->id], ['state', 'Vendido']])->get();
            $totalvendido = 0;

            foreach ($cars as $car) {
                $totalvendido = $totalvendido + $car->price;
            }

            return response()->json(['total_carros' => $user->cars->count(),
                                    'carros vendidos' => $cars->count(),
                                    'total_ventas' => $totalvendido
                                    ]);
        }else{
            return response()->json(['message'=>'No tienes acceso a las estadisticas'], 401);
        }
    }

    protected function total_users_role($name_role){
        $cont = 0;
        $users = User::all();

        foreach ($users as $user){
            foreach ($user->roles as $role){
                if(strtolower($role->name) == $name_role){
                    $cont++;
                }
            }
        }
        return $cont;
    }

    public function bestvend(){
        $best=0;
        $bestm=0;
        $id_v=0;

        foreach (User::all() as $user){
            foreach ($user->cars as $car){
                if($car->state == 'Vendido' && $this->registrovendido($car->id)){
                    $best++;
                }
            }

            if($bestm < $best){
                $bestm = $best;
                $id_v = $user->id;
            }

            $best=0;
        }
        return $id_v;
    }

    protected function registrovendido($car_id){
        $sale = Sale::where('car_id', $car_id)->get();

        if($sale != null){
            return true;
        }else{
            return false;
        }
    }
}
