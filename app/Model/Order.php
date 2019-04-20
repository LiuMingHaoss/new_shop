<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table='p_order';
    public $timestamps=false;
    public static function order_sn(){
        $order_sn=time().'_liu_'.rand(10000,99999);
        return $order_sn;
    }
}
