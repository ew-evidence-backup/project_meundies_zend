<?php

include '/var/www/html/meundies.com/zend/application/models/MySQL_Database.php';


class Evin_Reports_COHORT {

    public $paid;
    public $kipped;
    public $paused;
    public $cancelled;
    public $date_range_from;
    public $date_range_to;
    public $todayDate;

    function getGeneralReport() {

        $array = array();

        $model = new Application_Models_MySQL_Database();
        $model->db = 3; //old db
        //print_r('<pre>');

        //        print_r($active = $balance_due = $model->getGeneralCOHORTNumbersActive());
        //        print_r($bought = $balance_due = $model->getGeneralCOHORTNumbersBought());
        //        print_r($paused = $balance_due = $model->getGeneralCOHORTNumbersPaused());
        //        print_r($trail = $balance_due = $model->getGeneralCOHORTNumbersTrail());
        //        print_r($cancelled = $balance_due = $model->getGeneralCOHORTNumbersCancelled());
        //        print_r($skipped = $balance_due = $model->getGeneralCOHORTNumbersSkipped());
        $all = $model->getGeneralCOHORTNumbers();
        $orders = $model->getGeneralCOHORTNumbersOrders();
        echo '<div style="float: left;">';
        echo '<table align="left">';
        echo"<br/>";

        echo '<tr style="font-weight: bolder; text-transform: capitalize"><td colspan="3">SUBSCRIPTION TABLE</td></tr>';

        echo '<tr style="font-weight: bolder;"><td>Month</td><td>Subscriptions</td><td>State</td></tr>';

        foreach ($all as $array) {

            echo '<tr>';
            echo '<td>';
            echo $array['month'];
            echo '</td>';


            echo '<td>';
            echo $array['count(id)'];
            echo '</td>';


            echo '<td>';

            if ($array['state'] == 'cancelled') {

                echo '<div style="color:red; font-weight:bolder;">' . $array['state'] . "</div>";

            } elseif ($array['state'] == 'bought') {

                echo '<div style="color:green; font-weight:bolder;">' . $array['state'] . "</div>";

            }

            else {

                echo '<div style="color:black;">' . $array['state'] . "</div>";

            }


            echo '</td>';


        }

        echo '<tr>

        <td></td>

        </tr>';

        echo '<table align="right" bgcolor="#cccccc" class="dropShadow">';

        echo '<tr style="font-weight: bolder; text-transform: capitalize"><td colspan="3">ORDERS TABLE</td></tr>';

        echo '<tr style="font-weight: bolder;"><td>Month</td><td>Subscriptions (Unique Emails)</td><td>State</td></tr>';

        foreach ($orders as $array2) {

            echo '<tr>';
            echo '<td>';
            echo $array2['month'];
            echo '</td>';


            echo '<td>';
            echo $array2['count(email)'];
            echo '</td>';


            echo '<td>';



            if ($array2['state'] == 'canceled') {

                echo '<div style="color:red; font-weight:bolder;">' . $array2['state'] . "</div>";



            }   elseif($array2['state'] == 'complete'){

                echo '<div style="color:green; font-weight:bolder;">' . $array2['state'] . "</div>";

            }

            else {

                echo '<div style="color:black;">' . $array2['state'] . "</div>";

            }



            echo '</td>';


        }

        echo '<tr>

        <td></td>

        </tr>';

        echo '</table>';
        echo '</div>';


        return true;

    }

    function getDate() {

        $today = date('mdy');
        return $today;

    }

}


?>