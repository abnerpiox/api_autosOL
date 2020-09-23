<?php

namespace App\Http\Controllers;

use App\Car;
use App\Sale;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Psy\Input\CodeArgument;
use function Symfony\Component\String\s;

class SaleController extends Controller
{
    public function index(Request $request){
        if($request->user()->AuthorizeRoles(['Comprador'])){
            return response()->json(Car::where('state', 'En venta')->get(), 200);
        }else{
            return response()->json(['message' => 'Solo los compradores pueden ver los autos en venta'], 200);
        }

    }

    public function show(Sale $sale, Request $request){

        if($request->user()->AuthorizeRoles(['Comprador']) && $sale->user_id == auth()->user()->id){
            return response()->json($sale, 200);
        }else{
            return response()->json(['message'=>'No puedes ver esta compra'], 401);
        }

    }

    public function store(Request $request){


        if($request->user()->AuthorizeRoles(['Comprador'])) {
            $validator = $this->SaleValidate($request->all());

            if($validator->fails()){
                return response()->json(['errors' => $validator->errors()->all()]);
            }

            $car = Car::find($request->input('car_id'));

            $sale = new Sale();
            $sale->user_id = auth()->user()->id;
            $sale->car_id = $car->id;
            $sale->total = $car->price;
            $sale->save();

            $car->update(['state' => 'Vendido']);

            return response()->json(['message' => 'Compra añadida',
                'data' => $sale], 201);
        }else{
            return response()->json(['message' => 'Solo un comprador puede realizar una compra'], 401);
        }
    }

    public function update(Request $request, Sale $sale){
        $sale->update($request->all());

        return response()->json(['message' => 'Venta actualizada',
                                 'data'=>$sale], 200);
    }

    public function delete(Sale $sale){
        $sale->delete();

        return response()->json(['message' => 'Registro borrado con exito',
                                 'data' => $sale], 200);
    }

    protected function SaleValidate($request){

        return Validator::make($request, ['car_id' => ['required', 'integer', 'unique:sales', 'exists:cars,id']]);
    }
}
