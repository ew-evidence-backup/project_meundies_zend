<?php

/*
 * @author Evin Weissenberg 2012 | ecw.technology@gmail.com
 * Data grid generator for mongoDB
 *
 */

include '/var/www/html/meundies.com/zend/application/models/MongoDB.php';

class Evin_Data_Layer_Management {

    public $db;
    public $collection;
    public $fields = array();
    public $title;
    public $sum;
    public $remove_field1;
    public $remove_field2;
    public $remove_field3;


    function renderView() {

        $model = new Application_Models_MongoDB();
        $stuff = $model->renderViewModel($this->db, $this->collection);

        echo '<table>';
        $create_page = $this->collection . '_create.php';
        $search_page = $this->collection . '_search.php';

        echo "<tr><td><h1 style='text-transform: capitalize'>$this->title</h1></td></tr>";
        echo "<tr><td><h3 style='text-transform: capitalize'><a href=$create_page>Create</a></h3></td><td colspan='50px'><h3 style='text-transform: capitalize'><a href=$search_page>Search</a> </h3</td></tr>";

        /*
         * render keys or fields
         */

        //print_r($stuff);

        foreach ($stuff as $array) {

            echo '<tr style="font-weight: bolder; text-transform: uppercase">';
            foreach ($array as $key => $value) {
                echo '<td>';
                //Remove underscores from keys
                $clean_key = str_replace('_', ' ', $key);
                echo $clean_key;
                echo '</td>';

            }

            echo '<td></td>';
            echo '<td></td>';

            echo '</tr>';
            echo '<tr><td colspan="100%"><hr/></td></tr>';
            break;
        }

        /*
        * Render data
        */
        $count = 0;
        $sum = 0;
        $edit_page = $this->collection . '_edit.php';
        $delete_page = $this->collection . '_delete.php';
        foreach ($stuff as $array) {

            echo '<tr>';

            foreach ($array as $key => $value) {

                if(isset($this->sum) && $key == $this->sum){


                    echo '<td style="color: green;">';
                    echo '$'.$value;
                    echo '</td>';

                }else{

                    echo '<td>';
                    echo $value;
                    echo '</td>';

                }

            }

            if (isset($this->sum)) {

                $sum = $array[$this->sum] + $sum;

            }

            $count++;

            echo"<td><a href=$edit_page?id=$array[_id]>edit</a></td>";
            echo"<td><a href=$delete_page?id=$array[_id]>delete</a></td>";
            echo '</tr>';
        }
        echo "<tr><td colspan='100%'><hr/></td></tr>";

        if (isset($this->sum)) {

            $sum_formatted = number_format($sum,2);
            $sum_formatted = '| SUM: '.'$'.$sum_formatted;

        }

        echo "<tr><td style='color: #008000; text-transform: capitalize; font-weight: bold;'>Count: $count $sum_formatted</td>";
        echo '</table>';
    }

    function renderSearch() {


    }


    function renderEdit() {


    }


    function renderDelete() {


    }

}

?>