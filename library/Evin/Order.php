<?php

/*
 * @author: Evin Weissenberg 2012
 *
 */

include 'Evin/Utility.php';
include 'Evin/Stripe.php';
include 'Evin/Authorize.php';
include 'Evin/Crypt.php';
include 'Evin/GEO.php';
include 'Evin/Customer.php';
include '/var/www/html/meundies.com/zend/application/models/MongoDB.php';


class Evin_Order extends Evin_Utility {

    //customer id
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
    // public $amount = null;
    // CA 8.75
    public $tax = 0.0875;
    public $tax_amount = null;
    public $grand_total = null;
    public $shipping = null;
    public $one_off_amount = null;

    //order items information
    public $product_ids = null;
    public $notes = null;
    public $order_ip = null;
    public $product_object = null;

    //order configuration
    public $is_recurring = 0; // 1=y,2=n
    public $recurring_interval = 30; // in days. 30,90 days default
    public $is_parent_order = 1;
    public $parent_order_id = null;
    public $preceding_order_id = null;
    public $order_type = 'init'; // init or recur
    public $is_recurring_active = 0; // 0=n,1=y
    public $next_billing_date = null;

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
    //
    public $qty;
    public $qtyIteration;

    //Charge card
    public $one_off;


    function __construct() {

        Evin_Utility::db_connection();

    }

    function checkIfRequestEmpty() {

        if (!isset($_REQUEST['bill_first_name']) || $_REQUEST['bill_first_name'] == '') {

            throw new Exception('Error: Billing first name is empty.');

        } elseif (!isset($_REQUEST['bill_last_name']) || $_REQUEST['bill_last_name'] == '') {

            throw new Exception('Error: Billing last name is empty.');

        }
        elseif (!isset($_REQUEST['bill_address']) || $_REQUEST['bill_address'] == '') {

            throw new Exception('Error: Billing address is empty.');

        }
        elseif (!isset($_REQUEST['bill_city']) || $_REQUEST['bill_city'] == '') {

            throw new Exception('Error: Billing city is empty.');

        }
        elseif (!isset($_REQUEST['bill_state']) || $_REQUEST['bill_state'] == '') {

            throw new Exception('Error: Billing state is empty.');

        }
        elseif (!isset($_REQUEST['bill_zip']) || $_REQUEST['bill_zip'] == '') {

            throw new Exception('Error: Billing zip is empty.');

        }
        elseif (!isset($_REQUEST['bill_country']) || $_REQUEST['bill_country'] == '') {

            throw new Exception('Error: Billing country is empty.');

        }
        elseif (!isset($_REQUEST['offer_id']) || $_REQUEST['offer_id'] == '') {

            throw new Exception('Error: Offer information not found.');

        }
        elseif (!isset($_REQUEST['phone']) || $_REQUEST['phone'] == '') {

            throw new Exception('Error: Phone is empty.');

        }
        elseif (!isset($_REQUEST['email']) || $_REQUEST['email'] == '') {

            throw new Exception('Error: Email is empty.');

        }
        elseif (!isset($_REQUEST['cc']) || $_REQUEST['cc'] == '') {

            throw new Exception('Error: Credit card number is empty.');

        }
        elseif (!isset($_REQUEST['cc_type']) || $_REQUEST['cc_type'] == '') {

            throw new Exception('Error: Credit card type is empty.');

        }
        elseif (!isset($_REQUEST['cc_exp_month']) || $_REQUEST['cc_exp_month'] == '') {

            throw new Exception('Error: Credit card expiration month date is empty.');

        }
        elseif (!isset($_REQUEST['cc_exp_year']) || $_REQUEST['cc_exp_year'] == '') {

            throw new Exception('Error: Credit card expiration year date is empty.');

        }

        elseif (!isset($_REQUEST['cc_cvv']) || $_REQUEST['cc_cvv'] == '') {

            throw new Exception('Error: Credit card cvv is empty.');

        }
        elseif (!isset($_REQUEST['curr']) || $_REQUEST['curr'] == '') {

            throw new Exception('Error: Currency not specified.');

        } else {

            return true;
        }
    }

    function setSanitizeRequestData() {

        //$this->offer_id = $this->lower_case($this->db_sanitize($_REQUEST['offer_id']));
        $this->bill_first_name = $this->lower_case($this->db_sanitize($_REQUEST['bill_first_name']));
        $this->bill_last_name = $this->lower_case($this->db_sanitize($_REQUEST['bill_last_name']));
        $this->bill_address = $this->lower_case($this->db_sanitize($_REQUEST['bill_address']));
        $this->bill_city = $this->lower_case($this->db_sanitize($_REQUEST['bill_city']));
        $this->bill_state = $this->lower_case($this->db_sanitize($_REQUEST['bill_state']));
        $this->bill_zip = $this->lower_case($this->db_sanitize($_REQUEST['bill_zip']));
        $this->bill_country = $this->upper_case($this->db_sanitize($_REQUEST['bill_country']));

        $this->same_as_billing = $this->db_sanitize($_REQUEST['same_as_billing']);

        if ($this->same_as_billing != 1) {

            $this->ship_first_name = $this->lower_case($this->db_sanitize($_REQUEST['ship_first_name']));
            $this->ship_last_name = $this->lower_case($this->db_sanitize($_REQUEST['ship_last_name']));
            $this->ship_address = $this->lower_case($this->db_sanitize($_REQUEST['ship_address']));
            $this->ship_city = $this->lower_case($this->db_sanitize($_REQUEST['ship_city']));
            $this->ship_state = $this->lower_case($this->db_sanitize($_REQUEST['ship_state']));
            $this->ship_zip = $this->lower_case($this->db_sanitize($_REQUEST['ship_zip']));
            $this->ship_country = $this->upper_case($this->db_sanitize($_REQUEST['ship_country']));
        }

        else {

            $this->ship_first_name = $this->lower_case($this->db_sanitize($_REQUEST['bill_first_name']));
            $this->ship_last_name = $this->lower_case($this->db_sanitize($_REQUEST['bill_last_name']));
            $this->ship_address = $this->lower_case($this->db_sanitize($_REQUEST['bill_address']));
            $this->ship_city = $this->lower_case($this->db_sanitize($_REQUEST['bill_city']));
            $this->ship_state = $this->lower_case($this->db_sanitize($_REQUEST['bill_state']));
            $this->ship_zip = $this->lower_case($this->db_sanitize($_REQUEST['bill_zip']));
            $this->ship_country = $this->upper_case($this->db_sanitize($_REQUEST['bill_country']));

        }

        $this->notes = $this->lower_case($this->db_sanitize($_REQUEST['n']));
        $this->phone = $this->phone_normalize($this->db_sanitize($_REQUEST['phone']));
        $this->email = $this->db_sanitize($_REQUEST['email']);
        $this->order_ip = $_SERVER['SERVER_ADDR'];
        $this->browser = $_SERVER['HTTP_USER_AGENT'];
        $this->browser_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->request_time = $_SERVER['REQUEST_TIME'];

        if (!isset($_SERVER['HTTP_REFERER'])) {

            $this->referral_url = '';

        } else {

            $this->referral_url = $_SERVER['HTTP_REFERER'];

        }

        if (!isset($_SERVER['aff_id'])) {

            $this->referral_url = '';

        } else {

            $this->aff_id = $this->db_sanitize($_REQUEST['aff_id']);

        }

        $this->currency = $this->db_sanitize($_REQUEST['curr']);

        /*
         * Credit Card Data setting and encryption.
         */

        $encrypt = new Evin_Crypt();

        $this->cc = $encrypt->encode($_REQUEST['cc']);
        $this->cc_type = $this->db_sanitize($_REQUEST['cc_type']);
        $this->cvv = $encrypt->encode($_REQUEST['cvv']);
        $this->exp_month = $this->db_sanitize($_REQUEST['exp_month']);
        $this->exp_year = $this->db_sanitize($_REQUEST['exp_year']);


        /*
        * Recurring rules (Next billing day logic)
        */

        if ($_REQUEST['is_recurring'] == 1) {

            $this->is_recurring = (int)$this->db_sanitize($_REQUEST['is_recurring']);
            $this->is_recurring_active = 1;

            try {
                // 30 interval handler
                if ($this->recurring_interval == 30) {


                    if (date('d') > 20) {

                        $next_next_month = date("m", strtotime("+2 months"));
                        $day = '06'; //6th of every month is billing for recurring orders of the previous month between 1-20 after 20th next billing cycle.

                        //If this month is december the next month will have different year. This handles that
                        if (date('m') == 12) {

                            $year = date("y", strtotime("+1 year"));

                        } else {

                            $year = date('y');

                        }

                        $this->next_billing_date = $next_next_month . $day . $year;

                    } else {

                        $next_month = date("m", strtotime("+1 months"));
                        $day = '06'; //6th of every month is billing for recurring orders of the previous month between 1-20 after 20th next billing cycle.

                        //If this month is december the next month will have different year. This handles that
                        if (date('m') == 12) {

                            $year = date("y", strtotime("+1 year"));

                        } else {

                            $year = date('y');

                        }

                        $this->next_billing_date = $next_month . $day . $year;

                    }


                } elseif ($this->recurring_interval == 90) {

                    //Q1 - Jan 6th
                    //Q2 - April 6th
                    //Q3 - July 6th
                    //Q4 - October 6th

                    // 90 intervals there is no trailing courtesy rule
                    if (date('m') == 1 || date('m') == 2 || date('m') == 3) {

                        $this->next_billing_date = '0406' . date('y');

                    } elseif (date('m') == 4 || date('m') == 5 || date('m') == 6) {

                        $this->next_billing_date = '0706' . date('y');

                    } elseif (date('m') == 7 || date('m') == 8 || date('m') == 9) {

                        $this->next_billing_date = '1006' . date('y');

                    } elseif (date('m') == 10 || date('m') == 11 || date('m') == 12) {

                        $next_year = date("y", strtotime("+1 year"));

                        $this->next_billing_date = '0106' . $next_year;

                    }

                }

            } catch (Exception $e) {

                echo $e->getMessage();

            }

            echo '<h3>NEXT BILL DATE</h3>';
            echo $this->next_billing_date;
            echo'<br/>';

            echo $year_from_now = strtotime("050612 +1 year");
            echo "<br/>" . $print = date('m-d-y', $year_from_now);

            echo '<br/>';
            echo '<br/>';
        }

        /*
        * Product IDS process and summation
        */

        //-------qty----------
        $this->qty = $this->db_sanitize($_REQUEST['qty']);
        $arrayed_qty = explode(',', $this->qty);
        //qty initial iteration value
        $qty_i = 0;
        //-----------------

        $this->product_object = $product_object = array();
        $this->product_ids = $this->db_sanitize($_REQUEST['product_ids']);
        $arrayed_ids = explode(',', $this->product_ids);
        // product sum initial value
        $product_sum = 00.00;
        foreach ($arrayed_ids as $sku) {

            $qty = $arrayed_qty[$qty_i];

            $model = new Application_Models_MongoDB();
            $model->sku = $sku;
            $p_object = $model->getProductBySKU();
            array_push($p_object, $product_object);
            $product_sum = ($p_object['price'] * $qty) + $product_sum;
            print_r($p_object);
            $this->product_object = $product_object;

            $qty_i++;

        }

        echo $product_sum = number_format($product_sum, 2);

        /*
        * Product IDS process and summation end
        */

        $this->recurring_interval = $this->db_sanitize($_REQUEST['recurring_interval']);
        $this->shipping = $this->db_sanitize($_REQUEST['shipping']);
        // $this->amount = $this->db_sanitize($_REQUEST['amount']);

        if ($this->bill_state == 'ca') {

            $this->tax_amount = $product_sum * $this->tax;

            $this->grand_total = number_format($product_sum + $this->shipping + $this->tax_amount, 2);

        } else {

            $this->grand_total = $product_sum + $this->shipping;

        }

        //Discount
        if ($this->is_discount == 1) {

            $this->grand_total - $this->is_discount_value;

        }
        //Promo Code
        if ($this->is_promo_code == 1) {

            $this->grand_total - $this->is_promo_value;
        }

        if ($this->is_upsell == 1) {

            $this->product_ids = ',' . $this->upsell_product_ids;

        }

        $this->payment_service = $this->db_sanitize($_REQUEST['payment_service']);
        $this->timestamp = new MongoTimestamp();
        $this->php_timestamp = time();
        $this->php_date = date('dmy');
        $this->date = new MongoDate();
        //$this->internal_recur_order_process = $this->db_sanitize($_REQUEST['internal_recur_order_process']);

        //Set geo properties
        $geo = new Evin_Geo();
        $geo->address = $this->bill_zip;
        $geo->requestGeo();
        //set
        $this->loc = array('lon' => $geo->lon, 'lat' => $geo->lat);
        //$this->lon = $geo->lon;
        //$this->lat = $geo->lat;

        /*
         * Date Time Assignment
         */

        $d = date('mdy');
        $t = time();

        /*
         * Create Order ID
         */

        $this->order_id = 'M' . strtoupper(Evin_Utility::genRandomString());


        /*
         * Set PHP time
         *
         */
        $this->php_date = $d;

        $this->one_off = $_REQUEST['one_off'];

        return $this;
    }

    function chargeCard() {

        try {

            print_r('<pre>');

            //@todo decrypt CC info for payment process
            $crypt = new Evin_Crypt();
            $cc = $crypt->decode($this->cc);

            $p = new Evin_Authorize('4bvC73F7', '427De3sVrs378S2N');
            $p->setTransaction($cc, $this->exp_month . $this->exp_year, $this->grand_total);
            $p->setParameter('x_email', $this->email);
            $p->setParameter("x_duplicate_window", 180);
            $p->setParameter("x_cust_id", $this->email);
            //$p->setParameter("x_transaction_id", 'wrwrw');
            $p->setParameter("x_customer_ip", $_SERVER['REMOTE_ADDR']);
            $p->setParameter("x_email_customer", false); // false for testing
            $p->setParameter("x_first_name", $this->bill_first_name);
            $p->setParameter("x_last_name", $this->bill_last_name);
            $p->setParameter("x_address", $this->bill_address);
            $p->setParameter("x_city", $this->bill_city);
            $p->setParameter("x_state", $this->bill_state);
            $p->setParameter("x_zip", $this->bill_zip);
            $p->setParameter("x_phone", $this->phone);
            $p->setParameter("x_ship_to_first_name", $this->ship_first_name);
            $p->setParameter("x_ship_to_last_name", $this->ship_last_name);
            $p->setParameter("x_ship_to_address", $this->ship_address);
            $p->setParameter("x_ship_to_city", $this->ship_city);
            $p->setParameter("x_ship_to_state", $this->ship_state);
            $p->setParameter("x_ship_to_zip", $this->ship_zip);
            $p->setParameter("x_description", 'Order Id: ' . $this->order_id);
            $p->setParameter("x_tax", $this->tax_amount);
            //$p->setParameter("")
            $p->process();

            if ($p->isApproved()) {

                $this->order_status = 'Approved';

            } else {

                $this->order_status = 'Declined';

            }

            /*
             * Prepare gateway response object to append into order object under property payment_service_response
             */
            $explode = explode('|', $p->response);
            $this->payment_service_response = $explode;
            print_r($p);


        } catch (Exception $e) {

            echo $e->getMessage();
        }

        return $this;
    }


    function saveOrderIntoMongoDB() {

        /*
        * Save order into mongoDB
        */

        $m = new Mongo();
        $db = $m->selectDB('meundies');
        $collection = $db->selectCollection('orders');
        $cursor = $collection->insert($this);


        if ($cursor) {

            return TRUE;

        } else {

            return FALSE;

        }
    }

    function createNewCustomer() {

        /*
         * Customer Object
         */

        $customer_object = new Evin_Customer();

        /*
         * Subscription ID will be first order ever placed by customer.
         * Customer Cannot have more than 1 subscription ID.
         */

        if ($this->one_off == 1) {
        }

        $customer_object->hash_password = Evin_Utility::createRandomTempPassword($this->email);
        $customer_object->subscription_id = $this->order_id;

        $customer_object->bill_first_name = $this->bill_first_name;
        $customer_object->bill_last_name = $this->bill_last_name;
        $customer_object->bill_address = $this->bill_address;
        $customer_object->bill_city = $this->bill_city;
        $customer_object->bill_state = $this->bill_state;
        $customer_object->bill_zip = $this->bill_zip;
        $customer_object->bill_country = $this->bill_country;

        $customer_object->ship_first_name = $this->ship_first_name;
        $customer_object->ship_last_name = $this->ship_last_name;
        $customer_object->ship_address = $this->ship_address;
        $customer_object->ship_city = $this->ship_city;
        $customer_object->ship_state = $this->ship_state;
        $customer_object->ship_zip = $this->ship_zip;
        $customer_object->ship_country = $this->ship_country;

        $customer_object->cc_type = $this->cc_type;
        $customer_object->cc = $this->cc;
        $customer_object->exp_month = $this->exp_month;
        $customer_object->exp_year = $this->exp_year;
        $customer_object->cvv = $this->cvv;
        $customer_object->cc_type = $this->cc_type;

        $customer_object->phone = $this->phone;
        $customer_object->email = $this->email;

        /*
         * Create Customer Model
         */

        $model = new Application_Models_MongoDB();
        $model->customer_object = $customer_object;
        $model->createCustomer();
    }


    function createTestURI() {

        $o['bill_first_name'] = 'John';
        $o['bill_last_name'] = 'Doe';
        $o['bill_address'] = '8923 Green St';
        $o['bill_city'] = 'Los Angeles';
        $o['bill_state'] = 'CA';
        $o['bill_zip'] = '90210';
        $o['bill_country'] = 'US';
        $o['same_as_billing'] = '1';
        $o['ship_first_name'] = 'Hillary';
        $o['ship_last_name'] = 'Jones';
        $o['ship_address'] = '2394 Pelican Creek';
        $o['ship_city'] = 'Austin';
        $o['ship_state'] = 'TX';
        $o['ship_zip'] = '78261';
        $o['ship_country'] = 'US';
        $o['phone'] = '3108929208';
        $o['email'] = 'evin@meundies.com';
        $o['cc_type'] = 'visa';
        $o['cc'] = '4444333322221111';
        $o['cvv'] = '453';
        $o['exp_month'] = '03';
        $o['exp_year'] = '14';
        $o['amount'] = '25.00';
        $o['product_ids'] = 'W100-PU-L,W400-PU-M';
        $o['n'] = 'this is a note';
        $o['order_ip'] = '929.34.23.2';
        $o['is_recurring'] = '1';
        $o['recurring_interval'] = '90';
        $o['curr'] = 'USD';
        $o['payment_service'] = 'authorize';
        $o['shipping'] = '9.99';
        $o['qty'] = '5,10';
        $o['one_off'] = 1;

        $build = http_build_query($o);
        return $build;
    }


    function emailOrderConfirmation() {

        /*
        * Email Order Confirmation
        */

        $message = "<head>

    <link rel='stylesheet' href='/var/www/html/meundies.com/zend/public/email_templates/style.css'>

<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<style type='text/css'>
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
</style>
</head>
<table width='600' border='0' align='center' cellpadding='3' cellspacing='3'>
  <tr>
    <td colspan='2'><h2>ORDER CONFIRMATION</h2></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><h3><strong>Billing Information</strong></h3></td>
    <td><h3><strong>Shipping Information</strong></h3></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Name: Jon</td>
    <td>Name: jon</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDDD'>Address: 3453 green st</td>
    <td bgcolor='#DDDDDD'>Address:  3453 green st</td>
  </tr>
  <tr>
    <td>City: Los Angeles</td>
    <td>City: Los Anegles</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDDD'>State: CA</td>
    <td bgcolor='#DDDDDD'>State: CA</td>
  </tr>
  <tr>
    <td>Zip: 90058</td>
    <td>Zip: 90058</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDDD'>Country: USA</td>
    <td bgcolor='#DDDDDD'>Country: USA</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td bgcolor='#DDDDDD'>Email: test@test.com</td>
    <td bgcolor='#DDDDDD'>&nbsp;</td>
  </tr>
  <tr>
    <td>Phone: 3102356659</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><h3>Order Summary</h3></td>
    <td><h3>&nbsp;</h3></td>
  </tr>
  <tr>
    <td colspan='2'><table width='600' border='0'>
        <tr>
          <td bgcolor='#DDDDDD'>Customer#</td>
          <td bgcolor='#DDDDDD'>64654</td>
        </tr>
        <tr>
          <td width='155'>Order#</td>
          <td width='435'>3453335</td>
        </tr>
        <tr>
          <td bgcolor='#DDDDDD'>Shipping Method:</td>
          <td bgcolor='#DDDDDD'>Standard</td>
        </tr>
        <tr>
          <td>Items:</td>
          <td>$34.00</td>
        </tr>
        <tr>
          <td bgcolor='#DDDDDD'>Shipping &amp; Handling:</td>
          <td bgcolor='#DDDDDD'>$7.20</td>
        </tr>
        <tr>
          <td>Tax:</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><strong>Grand Total:</strong></td>
          <td><p><strong>$1205.00</strong></p></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2'><a href='#'>customer login</a> | <a href='#'>support</a> | <a href='#'>print invoice</a></td>
  </tr>
</table>";

    }

}

?>