<?php

class Evin_Utility {

    //Generic Application Information
    public static $siteTitle = "CRM";
    public static $author = "Evin Weissenberg";
    public static $address = "Los Angeles, Ca";
    //Application Parameters
    public static $domain_production = "meundies.com";
    public static $domain_staging = "meundies-staging.com";
    public static $startTime;
    public static $endTime;
    public static $css_path = "";
    public static $css_mobile_path = "";
    public static $javascript_path = "";
    public static $logo = "./logo.png";
    public static $max_upload = "5242880"; // 5mb
    public static $time_zone = "America/Los_Angeles";
    //Generic Static Keywords
    public static $keyword1 = "";
    public static $keyword2 = "";
    public static $keyword3 = "";

    //Production Database Connection Credentials
    public static $host = 'localhost';
    public static $username = 'ec2-user';
    public static $password = 'b00gers';
    public static $databaseName = 'meundies';

    function __construct() {
        //session_start();
        define('CRM_VERSION', '1.0');
        date_default_timezone_set(self::$time_zone);
        ini_set('auto_detect_line_endings', TRUE);
        ini_set('memory_limit', '8000M');
//        if (!ini_get('safe_mode')) {
//            set_time_limit(25);
//        }

        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        //mb_internal_encoding("UTF-8");
        //$this->development_environment();
    }


    public static function db_connection() {
        mysql_connect(self::$host, self::$username, self::$password) or die(mysql_error());
        mysql_select_db(self::$databaseName) or die(mysql_error());
        return TRUE;
    }

    public static function db_connection_staging() {
        mysql_connect(self::$host, self::$username, self::$password) or die(mysql_error());
        mysql_select_db(self::$databaseName) or die(mysql_error());
        return TRUE;
    }

    public static function db_connection3() {
        mysql_connect('production.c7vy7hhu73sm.us-east-1.rds.amazonaws.com', 'evin', 'evin337887!!') or die(mysql_error());
        mysql_select_db('old_meundies') or die(mysql_error());
        return TRUE;
    }

    function db_query($query) {

        $q = $query;
        $r = mysql_query($q);
        $d = mysql_fetch_assoc($r);
        return $d;
    }


    function db_query_loop($query) {

        $data = array();
        $q = "$query";
        $result = mysql_query($q);
        while ($db_data = mysql_fetch_assoc($result)) {
            array_push($data, $db_data);
        }
        return $data;
    }

    function db_update_query($t, $k, $new_value, $old_value) {

        $run = mysql_query("UPDATE $t SET $k='$new_value' WHERE $k='$old_value'");
        return $run;
    }

    function db_query_insert($t, $k = array(), $v = array()) {

        $key_explode = implode(",", $k);
        $value_explode = implode(",", $v);

        $run = mysql_query("INSERT INTO $t ($key_explode) VALUES ($value_explode)");
        if ($t == NULL || $k == NULL || $v == null) {
            echo $this->db_json_response("801", "Insert Failed.");
        }
        return $run;
    }

    function db_json_response($error, $message, $data = array()) {
        // Example $error = 1 (Error exist) or $error = 0 (No error found)
        // If There is an Error show this response
        // If the data argument is available use it as part of the response
        if ($data != NULL) {
            $response = array("Error" => "$error", "Message" => "$message", "Data" => $data);
            $json_response = json_encode($response);
            return $json_response;
        }
        else {
            // If there is no data array available use this general response
            $response = array("Error" => "$error", "Message" => "$message");
            $json_response = json_encode($response);
            return $json_response;
        }
    }

    function db_sanitize($var) {
        $sanitized = mysql_real_escape_string($var);
        return $sanitized;
    }


    function debug($debugVar) {
        print('<pre>');
        print_r($debugVar);
        print('</pre>');
    }

    public static function startTime() {

        $time = microtime(true);
        return $time;

    }


    public static function development_environment($time) {


        if ($_SERVER['APPLICATION_ENV'] == 'staging') {

            echo "<div style='background-color: red; color: white; font-weight: bolder; padding: 10px;'>STAGING ENVIRONMENT" . "</div>";


            echo "<div class='debug'>Page Load: " . "Time Elapsed: " . (microtime(true) - $time) . "s" . "</div>";

            echo "<style type='text/css'>

            .debug {font-size: 12px; background-color: black; color: white; font-family: arial, Helvetica,sans-serif;  padding: 10px;}
            .debug_request {font-size: 12px; background-color: black; grey: white; font-family: arial, Helvetica,sans-serif;  padding: 10px;}


            </style>";

            //self::debug($_SERVER);
            print_r('<pre style="background-color: #566D7E; color: white; padding: 10px; font-weight: bolder;">');
            print_r($_REQUEST);
            print_r('</pre>');


            error_reporting(E_ALL);
            ini_set("display_errors", 1);
            echo "<div class='debug'>" ." Author: Evin Weissenberg 2011 - " . date('Y') . ". http://www.evinw.com</div>";
            echo "<div class='debug'>PHP Version: " . PHP_VERSION . "</div>";
            echo "<div class='debug'>Server Time Zone: " . date_default_timezone_get() . "</div>";

            foreach ($_SERVER as $key => $value) {


                echo "<div class='debug'>$key : " . $value . "</div>";

            }

            return TRUE;
        }

        else {

            $production = self::production_environment();

            return $production;

        }

    }


    function production_environment() {

        error_reporting(0);

    }

    function dirPath($file) {

        echo dirname(__FILE__) . DIRECTORY_SEPARATOR . "$file";

    }

    function webPath($file) {

        echo self::$domain_production . dirname($_SERVER['PHP_SELF']) . DIRECTORY_SEPARATOR . "$file";

    }

    function headBlock($title, $description, $keywords) {

        echo "
                            <meta content='text/html; charset=utf-8' http-equiv='Content-Type'>
                            <title>{$title}</title>
                            <meta content='" . self::$author . "' name='author'>
                            <meta content='{$description}' name='Description'>
                            <meta content='{self::keyword1},{self::keyword2},{self::keyword3}{$keywords}' name='Keywords'>
                            <meta content='" . self::$address . "' name='Geography'>
                            <meta content='English, Spanish, French, German, Italian, Japanese, Chinese' name='Language'>
                            <meta content='never' http-equiv='Expires'>
                            <meta content='7 days' name='Revisit-After'>
                            <meta content='Global' name='distribution'>
                            <meta content='INDEX,FOLLOW' name='Robots'>
                            <meta content='USA' name='country'>
                            <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
                            <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js'></script>
                         ";
    }

    static public function curl_post($domain_url, $post_params) {

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

    static public function curl_get($domain_url, $post_params) {

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

    static function css() {

        $browser = $_SERVER['HTTP_USER_AGENT'];
        $find = strstr($browser, "Mobile");

        if ($find == TRUE) {
            echo "<link rel='stylesheet' href='./library/css/mobile.css'>";
        }
        else
        {
            echo "<link rel='stylesheet' href='./library/css/style.css'>";
        }


    }

    static function js() {

        //echo "<script type='text/javascript' src='./library/js/main.js'></script>";
        //echo "<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'></script>";
    }

    function seo() {

    }

    function generateRandomFilename($salt, $ext) {

        $dateTime = date('Y-m-d-H:i:s');
        $random = rand(1000, 1000000);
        $saltAndPepper = $salt;
        $combo = $dateTime . $random . $saltAndPepper;
        $hash = md5($combo);
        $generate = date('Y-m-d') . $hash . $ext;

        return $generate;
    }

    function email($email, $subject, $message, $cc, $bcc) {

        $to = "$email";
        $subject = "$subject";
        $message = "$message";
        $headers = "From: " . self::$siteTitle . "\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Return-Path: $email\r\n";
        $headers .= "CC: $cc\r\n";
        $headers .= "BCC: $bcc\r\n";

        if (mail($to, $subject, $message, $headers)) {
            echo "Email Sent!";
        }
        else {
            echo "Email Failed! Notify " . self::$siteTitle . "";
        }

    }

    function checkLogin() {

        self::db_connection();
        $username = mysql_real_escape_string($_REQUEST['username']);
        $password = mysql_real_escape_string($_REQUEST['password']);

        $query = "SELECT * from client_area WHERE username = '$username' and password = '$password'";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);

        if ($row == TRUE) {

            echo $_SESSION['username'] = $username;

        }
        else
        {
            echo $this->systemErrorMessage("Username and or password combination was not found in our records.");
            session_destroy();
            die();

        }

    }

    public static function todays_date() {
        $date = date('Y-m-d');
        return $date;
    }

    function jsonApiResponse($error, $message, $data = "") {

        // Example $error = 1 (Error exist) or $error = 0 (No error found)
        // If There is an Error show this response
        // If the data argument is available use it as part of the response

        if ($data != NULL) {


            $response = array("Error" => "$error", "Message" => "$message", "Data" => $data);
            $responseJson = json_encode($response);

            return $responseJson;

        }
        else {

            // If there is no data argument available use this general response
            $response = array("Error" => "$error", "Message" => "$message");
            $responseJson = json_encode($response);

            return $responseJson;

        }
    }

    function openWriteToFile($file, $string) {

        $f = $file;
        $fh = fopen($f, 'w') or die("Failed to open file.");
        $stringData = $string;
        fwrite($fh, $stringData);
        fclose($fh);

    }

    public static function globalHeader() {

        $title = self::$siteTitle;
        $header = "<div class='heading'><h1>$title</h1></div>";
        return $header;

    }

    public static function globalFooter() {

        $copyright = "Copyright © " . date('Y') . " " . self::$siteTitle;
        $title = self::$siteTitle;
        $footer = "<div class='footer'>$title $copyright</div>";
        return $footer;
    }

    function mySQLRealEscape($variable) {

        $clean = mysql_real_escape_string($variable);

        return $clean;

    }

    public static function logEvent($event_message) {

        $myFile = self::$event_log;
        $fh = fopen($myFile, 'a') or die("can't open file");
        $stringData = "$event_message," . date('m/d/y,h:m:s,a,') . self::$time_zone . "\n";
        fwrite($fh, $stringData);
        fclose($fh);

    }

    function systemErrorMessage($message_data) {

        $message = "<div style='background-color: red; color: white; padding: 10px; font-weight: bold; text-transform: uppercase'>$message_data</div>";

        return $message;

    }

    // Upload Multiple
    function uploadMultipleImage($fieldname, $maxsize, $extensions, $uploadpath, $index, $ref_name = false) {
        $upload_name = $_FILES[$fieldname]['name'][$index];
        $max_file_size_in_bytes = $maxsize; //max size
        $extension_whitelist = $extensions; //allows extensions list
        // checking extensions
        $file_extension = trim(strtolower(end(explode(".", $upload_name))));
        $is_valid_extension = false;
        if (in_array($file_extension, $extension_whitelist)) {
            $is_valid_extension = true;
        }
        if (!$is_valid_extension) {
            echo '{"error":"true", "htmlcontent":"Uploaded file Extension is not valid."}';
            exit(0);
        }
        // file size check
        $file_size = @filesize($_FILES[$fieldname]["tmp_name"][$index]);
        if ($file_size > $max_file_size_in_bytes) {
            echo '{"error":"true", "htmlcontent":"File Exceeds maximum limit"}';
            exit(0);
        }
        if (isset($upload_name)) {
            if ($_FILES[$fieldname]["error"][$index] > 0) {
                echo '{"error":"true", "htmlcontent":"' . $_FILES[$fieldname]['error'][$index] . '"}';
                exit(0);
            }
        }
        //$file_name = time().$upload_name;
        if ($ref_name == false) {
            $file_name = time() . $upload_name;
        }
        else {
            $file_name = $ref_name;
        }
        if (move_uploaded_file($_FILES[$fieldname]["tmp_name"][$index], $uploadpath . $file_name)) {
            return $file_name;
        }
        else {
            echo '{"error":"true", "htmlcontent":"Sorry unable to upload your files"}';
            exit(0);
        }
    }

    //Method to upload single image
    function uploadFiles($fieldname, $maxsize, $extensions, $uploadpath, $ref_name = false) {
        $upload_name = $_FILES[$fieldname]['name'];
        $max_file_size_in_bytes = $maxsize; //max size
        $extension_whitelist = $extensions; //allows extensions list
        // checking extensions
        $file_extension = strtolower(end(explode(".", $upload_name)));
        if (!in_array($file_extension, $extension_whitelist)) {
            echo '{"error":"true", "htmlcontent":"Uploaded file Extension is not valid."}';
            exit(0);
        }

        // file size check
        $file_size = @filesize($_FILES[$fieldname]["tmp_name"]);
        if ($file_size > $max_file_size_in_bytes) {
            echo '{"error":"true", "htmlcontent":"File Exceeds maximum limit"}';
            exit(0);
        }
        if (isset($upload_name)) {
            if ($_FILES[$fieldname]["error"] > 0) {
                echo '{"error":"true", "htmlcontent":"' . $_FILES[$fieldname]['error'] . '"}';
                exit(0);
            }
        }
        if ($ref_name == false) {
            $file_name = rand(1, 99999) . time() . str_replace(" ", "_", $upload_name);
        }
        else {
            $file_name = $ref_name;
        }
        if (move_uploaded_file($_FILES[$fieldname]["tmp_name"], $uploadpath . $file_name)) {
            return $file_name;
        }
        else {
            echo '{"error":"true", "htmlcontent":"Sorry unable to upload your file, Please try after some time."}';
            exit(0);
        }
    }

    //Upload single file any extensions
    public static function uploadFile($fieldname, $uploadpath) {
        $file_name = time() . $_FILES[$fieldname]['name'];
        if (move_uploaded_file($_FILES[$fieldname]["tmp_name"], $uploadpath . $file_name)) {
            return $file_name;
        }
        else {
            //            echo '{"error":"true", "htmlcontent":"Sorry unable to upload your file, Please try after some time."}';
            //            exit(0);
        }
    }

    //Upload multiple files with any extensions
    function uploadMultipleFile($fieldname, $uploadpath, $index) {
        $file_name = time() . $_FILES[$fieldname]['name'][$index];
        if (move_uploaded_file($_FILES[$fieldname]["tmp_name"][$index], $uploadpath . $file_name)) {
            return $file_name;
        }
        else {
            echo '{"error":"true", "htmlcontent":"Sorry unable to upload your pictures"}';
            exit(0);
        }
    }

    function lower_case($str) {
        $lower = strtolower($str);
        return $lower;
    }

    function phone_normalize($phone) {
        $remove_dashes = str_replace("-", "", $phone);
        $remove_dots = str_replace(".", "", $remove_dashes);
        $remove_right_brace = str_replace("(", "", $remove_dots);
        $remove_left_brace = str_replace(")", "", $remove_right_brace);
        $remove_commas = str_replace(",", "", $remove_left_brace);
        $remove_plus = str_replace("+", "", $remove_commas);
        $remove_forward_slash = str_replace("/", "", $remove_plus);
        $remove_spaces = str_replace("", "", $remove_forward_slash);

        return $remove_spaces;
    }

    function upper_case($str) {

        $upper = strtoupper($str);
        return $upper;

    }

    public static function genRandomString() {

        $length = 9;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        return $string;
    }

    public static function createRandomTempPassword($string) {

        $temp_password = substr(md5($string), -8);

        return $temp_password;

    }


    public static function debugEnvironment($env) {

        if ($env == 'meundies-staging.com') {

            //show debug stuff

        }

    }
}

?>