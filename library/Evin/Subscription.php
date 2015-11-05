<?php

include 'Evin/Utility.php';

class Evin_Subscription extends Evin_Utility {



    function getOrdersForRecurring30() {

        $m = Application_Models_MongoDB::mConnect();
        $db = $m->selectDB('meundies');
        $collection = $db->selectCollection('orders');
        $query = $collection->find(array(

            'is_recurring' => '1',
            'status' => 'approved',
            'interval' => $this->interval

        ));


        foreach ($query as $key=>$value){

            $o = new Evin_Order();
            $o->internal_recur_order_process = 1;
            $o->createOrder($query);

        }

    }

    function getOrdersForRecurring90() {

        $m = Application_Models_MongoDB::mConnect();
        $db = $m->selectDB('meundies');
        $collection = $db->selectCollection('orders');
        $query = $collection->find(array(

            'is_recurring' => '1',
            'status' => 'approved',
            'interval' => $this->interval

        ));


        foreach ($query as $key=>$value){

            $o = new Evin_Order();
            $o->internal_recur_order_process = 1;
            $o->createOrder($query);

        }

    }


}

$cron = new Evin_Subscription();
$cron->getOrdersForRecurring30();

?>