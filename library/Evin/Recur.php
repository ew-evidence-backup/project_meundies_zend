<?php

include 'Evin/Authorize.php';
include 'Evin/Utility.php';

class Evin_Recur {

    //public $customer_id = null;
    public $is_customer_logged_in = 0;

    //customer information
    public $bill_first_name = null;
    public $bill_last_name = null;
    public $bill_address = null;
    public $bill_city = null;
    public $bill_state = null;
    public $bill_zip = null;
    public $bill_country = null;

    public $same_as_billing = 1;

    public $ship_first_name = null;
    public $ship_last_name = null;
    public $ship_address = null;
    public $ship_city = null;
    public $ship_state = null;
    public $ship_zip = null;
    public $ship_country = null;

    public $phone = null;
    public $email = null;

    //credit card information
    public $cc_type = null;
    public $cc = null;
    public $cvv = null;
    public $exp_month = null;
    public $exp_year = null;

    // Order Amount
    public $amount = null;
    // CA 8.56
    public $tax = 0.0875;
    public $tax_amount = null;
    public $grand_total = null;
    public $shipping = null;

    //order items information
    public $product_ids = null;
    public $notes = null;
    public $order_ip = null;

    //order configuration
    public $is_recurring = 0; // 1=y,2=n
    public $recurring_interval = 30; // in days. 30,90 days default
    public $is_parent_order = 1;
    public $parent_order_id = null;
    public $preceding_order_id = null;
    public $order_type = 'init'; // init or recur
    public $is_recurring_active = 0; // 0=no, 1=yes, 2=paused

    //recur states


    //public $internal_recur_order_process = 0; //is this a recurring order or initial order?


    //analytics information
    public $referral_url = null;
    public $aff_id = null;
    public $browser = null;
    public $browser_language = null;
    public $currency = null;
    public $request_time = null;

    //payments
    public $payment_service = null;
    public $payment_service_response = null;

    //order
    public $order_id = null;
    public $order_status = null;

    //Discounts
    public $is_discount = 0;
    public $is_discount_value = 00.00;
    public $is_promo_code = 0;
    public $is_promo_value = 00.00;

    //Upsale
    public $is_upsell = 0;
    public $upsell_product_ids = 0;

    public $date = null;
    public $php_date = null;
    public $timestamp = null;
    public $php_timestamp = null;

    //geo
    public $loc = array();

//    public $lon = null;
//    public $lat = null;


    function createRecurOrder() {

        try {

            $model = new Application_Models_MongoDB();
            $model->order_id = $this->order_id;
            $obj = $model->getOrderById();

            //Set object to send to mongoDB
            $this->bill_first_name = $obj['bill_first_name'];
            $this->bill_last_name = $obj['bill_last_name'];
            $this->bill_first_name = $obj['bill_first_name'];
            $this->bill_address = $obj['bill_address'];
            $this->bill_city = $obj['bill_city'];
            $this->bill_state = $obj['bill_state'];
            $this->bill_zip = $obj['bill_zip'];
            $this->bill_country = $obj['bill_country'];
            $this->same_as_billing = $obj['same_as_billing'];
            $this->ship_first_name = $obj['ship_first_name'];
            $this->ship_last_name = $obj['ship_first_name'];
            $this->ship_address = $obj['ship_address'];
            $this->ship_city = $obj['ship_address'];
            $this->ship_state = $obj['ship_state'];
            $this->ship_zip = $obj['ship_zip'];
            $this->ship_country = $obj['ship_country'];
            $this->phone = $obj['phone'];
            $this->email = $obj['email'];
            $this->cc_type = $obj['cc_type'];
            $this->cc = $obj['cc'];
            $this->cvv = $obj['cvv'];
            $this->exp_month = $obj['exp_month'];
            $this->exp_year = $obj['exp_year'];
            $this->amount = $obj['amount'];
            $this->tax = $obj['tax'];
            $this->tax_amount = $obj['tax_amount'];
            $this->grand_total = $obj['grand_total'];
            $this->shipping = $obj['shipping'];
            $this->product_ids = $obj['product_ids'];
            $this->notes = $obj['notes'];
            $this->order_ip = $obj['order_ip'];
            $this->is_recurring = 1;
            $this->recurring_interval = $obj['recurring_interval'];
            $this->is_parent_order = 0;
            $this->parent_order_id = $obj['order_id'];
            $this->preceding_order_id = $model->order_id;
            $this->order_type = 'recur';
            $this->is_recurring_active = $obj['order_ip'];
            $this->referral_url = $obj['referral_url'];
            $this->aff_id = $obj['aff_id'];
            $this->browser = $obj['browser'];
            $this->browser = $obj['browser_language'];
            $this->currency = $obj['currency'];
            $this->request_time = $_SERVER['REQUEST_TIME'];
            $this->payment_service = $obj['payment_service'];
            $this->order_ip = $obj['order_ip'];


            /*
             * Create New Order ID
             */
            $this->order_id = 'MR' . strtoupper(Evin_Utility::genRandomString());

            $this->is_discount = $obj['is_discount'];
            $this->is_discount_value = $obj['is_discount_value'];
            $this->is_promo_code = $obj['is_promo_code'];
            $this->is_promo_value = $obj['is_promo_value'];
            $this->is_upsell = $obj['is_upsell'];
            $this->upsell_product_ids = $obj['upsell_product_ids'];
            $this->date = new MongoDate();
            $this->php_date = date('mdy');
            $this->timestamp = new MongoTimestamp();
            $this->php_timestamp = time();

            $this->loc = array('lon' => $obj['long'], 'lat' => $obj['lat']);
            //$this->lon = $obj['long'];
            //$this->lat = $obj['lat'];

            $p = new Evin_Authorize('4bvC73F7', '427De3sVrs378S2N');

            /*
             * @todo decrypt cc
             */

            $p->setTransaction($obj['cc'], $obj['exp_month'] . $obj['exp_year'], $obj['grand_total']);
            $p->setParameter('x_email', $obj['email']);
            $p->setParameter("x_duplicate_window", 180);
            $p->setParameter("x_cust_id", $this->email);
            //$p->setParameter("x_transaction_id", 'wrwrw');
            $p->setParameter("x_customer_ip", $_SERVER['REMOTE_ADDR']);
            $p->setParameter("x_email_customer", true); // false for testing
            $p->setParameter("x_first_name", $obj['bill_first_name']);
            $p->setParameter("x_last_name", $obj['bill_last_name']);
            $p->setParameter("x_address", $obj['bill_address']);
            $p->setParameter("x_city", $obj['bill_city']);
            $p->setParameter("x_state", $obj['bill_state']);
            $p->setParameter("x_zip", $obj['bill_zip']);
            $p->setParameter("x_phone", $obj['bill_country']);
            $p->setParameter("x_ship_to_first_name", $obj['ship_first_name']);
            $p->setParameter("x_ship_to_last_name", $obj['ship_last_name']);
            $p->setParameter("x_ship_to_address", $obj['ship_address']);
            $p->setParameter("x_ship_to_city", $obj['ship_city']);
            $p->setParameter("x_ship_to_state", $obj['ship_state']);
            $p->setParameter("x_ship_to_zip", $obj['ship_zip']);
            $p->setParameter("x_description", 'Order Id: re,' . $obj['order_id']);
            $p->setParameter("x_tax", $obj['tax_amount']);
            //$p->setParameter("")
            $p->process();

            if ($p->isApproved()) {

                $this->order_status = 'Approved';
                $this->is_recurring_active = 1;

            } else {

                $this->order_status = 'Declined';
                $this->is_recurring_active = 0;

            }

            $explode = explode('|', $p->response);

            $this->payment_service_response = $explode;
            print_r($p);

            //todo save into mongoDB
            $m = new Mongo();
            $db = $m->selectDB('meundies');
            $collection = $db->selectCollection('orders');
            $collection->insert($this);

            return $obj;

            //$p = new Evin_Authorize();
            //$charge $p->chargeCreditCard();

        } catch (Exception $e) {

            echo $e->getMessage();
        }

    }


    function cron30days() {


    }

    function cron90days() {


    }
}

?>