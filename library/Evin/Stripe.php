<?php

/*
 * @author Evin Weissenberg
 *
 *  ERROR CODES
 *
 *  200 OK - Everything worked as expected.
    400 Bad Request - Often missing a required parameter.
    401 Unauthorized - No valid API key provided.
    402 Request Failed - Parameters were valid but request failed.
    404 Not Found - The requested item doesn't exist.
    500, 502, 503, 504 Server errors - something went wrong on Stripe's end.
 *
 */

include 'Stripe/lib/Stripe.php';

class Evin_Stripe {

    //API Request Location
    private $http =  'https://';
    private $end_point = 'api.stripe.com';
    //API authentication
    private $key_live = null;
    private $key_test = 'SCqpjAg1WY93d94lofORY1tyyr82BJDU';
    private $key_test_publishable = 'pk_zlrU0lACndtWlF9sPSqtt9KjBpfbr';

    //Charge details
    private $amount = null;
    private $currency = 'usd';
    private $customer = null;
    private $card = array();
    private $description = null;

    function __construct() {

        Stripe::setApiKey($this->key_test);

    }

    function chargeCreditCard($order_object) {

        $order = (array)$order_object;

        $this->card['card'] = $order['cc'];
        $this->card['card']['exp_month'] = $order['exp_month'];
        $this->card['card']['exp_year'] = $order['exp_year'];
        $this->card['card']['cvc'] = $order['cvv'];
        $this->card['card']['name'] = $order['bill_first_name'].' '.$order['bill_last_name'];
        $this->card['address_line1'] = $order['bill_address'];
        //$this->card['address_line2'] = $order['null'];
        $this->card['address_zip'] = $order['bill_zip'];
        $this->card['address_state'] = $order['bill_state'];
        $this->card['address_country'] = $order['bill_country'];
        $this->card['amount'] = $order['amount'];
        $this->card['currency'] = $order['currency'];

        $charge = Stripe_Charge::create($this->card);

        print_r('<pre>');
        print_r($order_object);
        print_r($order);
        print_r($charge);

        //echo $l = $this->http . $this->key_test.'@'.$this->end_point . "/v1/charges/$q_string";
        //$r = $this->curl_get($l, '');

        return $charge;

    }

    function curl_post($domain_url, $post_params) {

        $url = $domain_url;
        $params = $post_params;

        $user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    function curl_get($domain_url, $post_params) {

        $url = $domain_url;
        $params = $post_params;

        $user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}

?>