<?php

namespace App\Http\Controllers;

use App\Car;
use App\Http\Requests\CarRequest;
use App\Sale;
use App\User;
use Illuminate\Support\Facades\Validator;
use http\Env\Response;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(){
        $user = User::find(auth()->user()->id);

        $vendedor = array();
        $comprador = array();
        $roles = $this->role($user);

        foreach ($roles as $role){
            if($role == 'Vendedor'){
                $vendedor = $user->cars;
            }else if($role == 'Comprador'){
                $sales = $user->sales;

                foreach ($sales as $sale){
                    array_push($comprador, $sale->car);
                }
            }else if($role == 'Admin'){
                $cars = Car::all();
                $res = [];

                foreach ($cars as $car){
                    $car->seller;
                    if(Sale::where('car_id', $car->id)->first() != null){
                        $car->sale->buyer;
                        array_push($res, $car);
                    }else{
                        array_push($res, $car);
                    }

                }

                return response()->json($res, 200);
            }
        }

        return response()->json(['Vendedor' => $vendedor,
                                'Comprador' => $comprador]);
    }

    public function show(Car $car){
        return $car;
    }

    public function store(Request $request){

        if($request->user()->AuthorizeRoles(['Vendedor'])){

            $validator = $this->carValidation($request->all());

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()->all()]);
            }

            $car = new Car();
            $car->user_id = auth()->user()->id;
            $car->enrollment = $request->input('enrollment');
            $car->brand = $request->input('brand');
            $car->model =$request->input('model');
            $car->color =$request->input('color');
            $car->description =$request->input('description');
            $car->price =$request->input('price');
            $car->state = 'En venta';
            $car->save();

            return response()->json(['message' => 'Carro añadido correctamente',
                'data' => $car], 201);
        }else{
            return response()->json(['message' => 'Solo los vendedores pueden añadir autos'], 401);
        }
    }

    public function update(Request $request, Car $car){

        if($car->user_id == auth()->user()->id && $request->user()->AuthorizeRoles(['Vendedor'])){


            return response()->json(['message' => 'Actualización correcta',
                'data' => $car], 200);
        }else{
            return response()->json(['message' => 'No eres propietario del auto'], 401);
        }
    }

    public function delete(Car $car, Request $request){

        if($car->user_id == auth()->user()->id && $request->user()->AuthorizeRoles(['Vendedor'])){
            $car->delete();

            return response()->json(['message' => 'Carro borrado exitosamente'], 200);
        }else{
            return response()->json(['message' => 'No eres propietario del auto'], 401);
        }
    }

    protected function role($user){
        return $user->roles->flatten()->pluck('name')->unique();
    }

    protected function carValidation($request){
        $rules = ['enrollment' => ['required', 'unique:cars'],
                    'brand' => ['required', 'string'],
                    'model' => ['required', 'string'],
                    'color' => ['required', 'string'],
                    'description' => ['required', 'max:255'],
                    'price' => ['required', 'integer']];

        return Validator::make($request, $rules);

    }
}
