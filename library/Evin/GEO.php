<?php

/*
 * GEO
 */

class Evin_Geo {

    private $end_point = 'http://maps.googleapis.com/maps/geo';
    public $address = null;
    public $lon = null;
    public $lat = null;

    function requestGeo() {

        $q = "$this->end_point?q=$this->address&output=json&sensor=true_or_false&key=AIzaSyAUW2QBXn3_q8CYGkWgnb06nmXfkjrFlkc";
        $u = new Evin_Utility();
        $r = $u->curl_get($q, '');

        $decode = json_decode($r, true);

        echo '<h1>GEO</h1>';

        $this->lon = $decode['Placemark'][0]['Point']['coordinates']['0'];
        $this->lat = $decode['Placemark'][0]['Point']['coordinates']['1'];

        return $decode;
    }


}

?>