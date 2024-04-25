<?php

namespace App\Http\Controllers\api\V1;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrdersRequest;
use App\Http\Requests\UpdateOrdersRequest;
use App\Http\Resources\V1\OrdersCollection;
use App\Http\Resources\V1\ProductsCartResource;
use App\Http\Resources\V1\UserOrderResource;
use App\Models\Order;
use App\Models\Product;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return  $this->sentErrorResponse("unauthorized",401);
        }
        if($user->roles === "1"){
            $orders = Order::paginate(10);
            return $this->sentSuccessResponse(new OrdersCollection($orders));
        }
        return  $this->sentErrorResponse("Account not admin");
    }
    public function getDetailOrder()
    {
        $user = auth()->user();
        if (!$user) {
            return  $this->sentErrorResponse("unauthorized",401);
        }
        $orders = Order::where("id_user",$user->id)->first();
        return $this->sentSuccessResponse(new UserOrderResource($orders));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrdersRequest $request)
    {
        $user = auth()->user();
        if (!$user) {
            return  $this->sentErrorResponse("unauthorized",401);
        }
        $ordersUser = Order::where("id_user","=",$user->id)->get();
        $isEmpty = empty($ordersUser->where("status_order","=",0)->first());
        if(!$isEmpty){
            return $this->sentErrorResponse("You need remove order before or payment",422);
        }
        $request->all();
        $order = new Order;
        $order->full_name = $request->fullName;
        $order->address = $request->address;
        $order->phone_number = $request->phone_number;
        $order->status_order = "0";
        $order->id_user =  $user->id;
        $cartProducts = collect();
        if(empty($request->products)){
            $cartProducts = [];
        }else{
            foreach (array_keys($request->products) as $id){
                $product = Product::findOrFail($id);
                if(empty($product)){
                   break;
                }else{
                    $productRes = new ProductsCartResource($product);
                    $product->quantity =$request->products[$id];
                    $cartProducts->push($product);
                }
            }
        }
        $order->products_cart = json_encode($cartProducts);
       // $order->save();
        return $this->sentSuccessResponse("add successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $orders)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrdersRequest $request, Order $orders)
    {
        $user = auth()->user();
        if (!$user) {
            return  $this->sentErrorResponse("unauthorized",401);
        }
        return $this->sentSuccessResponse("updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
            $user = auth()->user();
            if (!$user) {
                return  $this->sentErrorResponse("unauthorized",401);
            }
            $order->delete();
            return $this->sentSuccessResponse("delete successfully");

    }
}
