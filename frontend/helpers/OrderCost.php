<?php

namespace frontend\helpers;

use common\models\Order;

class OrderCost
{
    public static function calculateOrderCost(Order $order, $numOfOrders, $index)
    {
        if (!$order->isRealized()) {
            return;
        }
        $cost = $order->getPriceWithPack();
        $deliveryCost = round(
            $order->restaurants->delivery_price / $numOfOrders,
            2,
            PHP_ROUND_HALF_DOWN
        );
        if ($numOfOrders == $index + 1) {
            $sumPreviousDeliveryCost = round(
                $deliveryCost * ($numOfOrders - 1),
                2,
                PHP_ROUND_HALF_DOWN
            );
            $deliveryCost = $order->restaurants->delivery_price - $sumPreviousDeliveryCost;
        }
        return $cost + $deliveryCost;
    }
}
