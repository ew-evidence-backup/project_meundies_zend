<?php
/**
 * Author: Evin Weissenberg
 * Email: ecw.technology@gmail.com
 * Website: http://www.evinw.com
 * Date: 5/1/12
 * Time: 2:57 PM
 */
class Evin_Email {

    public $to;
    public $from;
    public $subject;
    public $message;
    public $footer;
    public $cc;
    public $bcc;


    function orderConfirmation() {



        $this->message = '';
        $this->sendEmail();


    }


    function orderReturned() {


    }


    function orderDeclined() {


    }


    function orderRMA() {


    }


    function orderShipped() {


    }


    function sendEmail() {

        $to = $this->to;
        $subject = $this->subject;
        $message = $this->message;
        $headers = "From: " . $this->from . "\r\n";
        $headers .= "Reply-To: $this->from\r\n";
        $headers .= "Return-Path: $this->from\r\n";
        $headers .= "CC: $this->cc\r\n";
        $headers .= "BCC: $this->bcc\r\n";

        if (mail($to, $subject, $message, $headers)) {
            echo "Email Sent!";
        }
        else {
            echo "Email Failed! Notify " . 'evin@meundies.com' . "";
        }

    }

}
