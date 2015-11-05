<?php


/*
 * Customer
 *
 */

class Evin_Customer {

    public $hash_password;
    public $php_date;
    public $subscription_id;
    public $created_timestamp;
    public $bill_first_name;
    public $bill_last_name;
    public $bill_address;
    public $bill_city;
    public $bill_state;
    public $bill_zip;
    public $bill_country;

    public $same_as_billing = 1;

    public $ship_first_name;
    public $ship_last_name;
    public $ship_address;
    public $ship_city;
    public $ship_state;
    public $ship_zip;
    public $ship_country;

    public $phone;
    public $email;

    //credit card information
    public $cc_type;
    public $cc;
    public $cvv;
    public $exp_month;
    public $exp_year;

}


?>