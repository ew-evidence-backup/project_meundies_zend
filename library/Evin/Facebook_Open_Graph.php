<?php


class facebook {
    var $api_key;
    var $user_id;
    var $string;

    function __construct() {
        $this->call_url = 'https://graph.facebook.com/';
        $this->debug = false;
    }

    public function set_debug() { //override to debug
        $this->debug = true;
    }

    private function inner_debug($data, $function) {
        //used to easily display all the data from a
        //returned request from this class
        //so a user can visualize what data they have
        if ($this->debug == true) { //outputs the array for testing
            echo 'START: function <b>' . $function . '</b> data <br>';
            echo 'key => item <br>';
            foreach ($data as $key => $item) {
                echo $key . ' => ' . $item . '<br>';
            }
            echo 'END: function <b>' . $function . '</b> data <br><br>';
        }

    }

    function set_data($access_token, $user_id = null) {
        $this->access_token = $access_token;

        if (!is_null($user_id)) {
            $this->user_id = $user_id;
        } else {
            $this->user_id = null;
        }

        $this->call_with_token = true;
    }

    private function get_facebook_data($string) {
        //this will just retreive the data

        //see if url is formatted properly at end!

        if ($this->call_with_token == true) {
            //if we have a valid access token, than we can tag it on
            $string = $string . '?access_token=' . $this->access_token;
        }

        if ($this->debug == true) {
            echo '<b>facebook graph string</b>: ' . $string . '<br><br>';
        }

        $content = file_get_contents($string);
        $json = json_decode($content, true);
        return $json;
    }

    /***********************************************
    FUNCTIONS TO WORK WITH FACEBOOK
     ***********************************************/
    //gets users basic profile information
    function get_user($user_id) {
        //fields from: http://developers.facebook.com/docs/reference/api/user

        //build string
        $string = $this->call_url . $user_id;

        //get data
        $data = $this->get_facebook_data($string);

        $this->inner_debug($data, 'get_user');

        return $data;
    }

    function get_user_statuses($user_id) {
        //this will get the most recent user status update
        $string = $this->call_url . $user_id . '/statuses';

        //get data
        $data = $this->get_facebook_data($string);

        return $data;
    }

    //publishes to the users stream
    function stream_publish($publish_to, $message, $picture = null, $link = null, $name = null, $caption = null, $description = null, $source = null, $actions = null, $privacy = null, $targeting = null) {
        //http://developers.facebook.com/docs/reference/api/post
        //NOTE: its down a ways on the page where it says "publishing"
        if ($this->call_with_token != true) {
            //we do not have a access token
            echo 'Facebook Class Error - no OAUTH access token, set_data function must be set!<br>';
            exit;
        }

        $publish['access_token'] = $this->access_token;

        if (!is_null($message))
            $publish['message'] = $message;
        if (!is_null($picture))
            $publish['picture'] = $picture;
        if (!is_null($link))
            $publish['link'] = $link;
        if (!is_null($name))
            $publish['name'] = $name;
        if (!is_null($caption))
            $publish['caption'] = $caption;
        if (!is_null($description))
            $publish['description'] = $description;
        if (!is_null($source))
            $publish['source'] = $source;
        if (!is_null($actions))
            $publish['actions'] = $actions;
        if (!is_null($privacy))
            $publish['privacy'] = $privacy;
        if (!is_null($targeting))
            $publish['targeting'] = $targeting;

        if ($this->debug == true) {
            foreach ($publish as $key => $out) {
                echo 'PUBLISH' . $key . ' -' . $out . '<br>';
            }
        }

        $url = $this->call_url . $publish_to . '/feed';

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => http_build_query($publish),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => true
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result, true);
        return $json['id']; //return the id of the post
    }

    /*********************************************
    -----------specific functions-------------
    to do small simple tasks but wrapped in
    a simple function instead of having to call
    the specific part of the array
     *******************************************/

    //gets the fan count of a page
    function get_fan_count($page_id) {
        $array = $this->get_user($page_id);
        return $array['likes'];
    }

    //get the last status update of a page
    function get_last_status_update($page_id) {
        $array = $this->get_user_statuses($page_id);
        return $array['data'][0]['message'];
    }
}


?>