<?php

include '/var/www/html/meundies.com/zend/application/models/MongoDB.php';

class Evin_Product {

    public $product_id;
    public $product_name;
    public $product_description;
    public $product_price;
    public $product_color;
    public $product_size;
    public $product_sku;
    public $product_gender;
    public $product_category;
    public $product_date;

    function setProperties() {

        $this->product_id = Evin_Utility::genRandomString();
        $this->product_name = $_REQUEST['name'];
        $this->product_description = $_REQUEST['description'];
        $this->product_price = $_REQUEST['price'];
        $this->product_color = $_REQUEST['color'];
        $this->product_size = $_REQUEST['size'];
        $this->product_sku = $_REQUEST['sku'];
        $this->product_gender = $_REQUEST['gender'];
        $this->product_category = $_REQUEST['category'];
        $this->product_date = new MongoTimestamp();

    }

    function createProduct() {

        $model = new Application_Models_MongoDB();
        $model->product_object = $this;
        $model->createProduct();

    }


}

?>