<?php
/**
 * Author: Evin Weissenberg
 * Email: ecw.technology@gmail.com
 * Website: http://www.evinw.com
 * Date: 4/20/12
 * Time: 4:10 PM
 */

include '/var/www/html/meundies.com/zend/application/models/MongoDB.php';

class Evin_Authentication {

    public $email;
    public $password;
    public $sizes;
    public $categories;
	
	
    public $password_hash;
    public $salt;


    function customerLogin() {
	    session_save_path('/tmp');
        ini_set('session.gc_probability', 1);
        if (!isset($_SESSION)) {
         session_start();
        }
		$_SESSION['categories'] = $_POST['categories'];
		$_SESSION['sizes'] = $_POST['sizes'];
		$_SESSION['colors'] = $_POST['colors'];
		$_SESSION['gender'] = $_POST['gender'];
       
        $this->email    = $_REQUEST['email'];
        $this->password = $_REQUEST['password'];

        $this->password_hash = $this->createPasswordHash($this->password);

        $model = new Application_Models_MongoDB();
        $match = $model->loginCustomer($this->email, $this->password_hash);

        try {


            if ($match == false) {

                throw new Exception('Username and or password did not match!');

            } elseif ($match == true) {

                print_r($match);
				$_SESSION['user'] = $match;

            }


        }


        catch (Exception $e) {

            echo $e->getMessage();

        }


    }


    function customerLogout() {
	    session_save_path('/tmp');
        ini_set('session.gc_probability', 1);
        if (!isset($_SESSION)) {
         session_start();
        }
      $_SESSION = array();
    }


    function customerDeactivateAccount() {


    }


    function forgetPassword() {


    }

    function forgotUsername() {


    }

    function adminLogin() {


    }

    /*
     * Create hash for passwords
     */

    function createPasswordHash($password) {

        $hash = hash_hmac('sha256', $password, '10Evin0LosAngeles0CA01');

        return $hash;

    }

    /*
     * Create new customer
     */

    function createCustomer() {

        session_save_path('/tmp');
        ini_set('session.gc_probability', 1);
        if (!isset($_SESSION)) {
         session_start();
        }
		$_SESSION['categories'] = $_POST['categories'];
		$_SESSION['sizes'] = $_POST['sizes'];
		$_SESSION['colors'] = $_POST['colors'];
		$_SESSION['gender'] = $_POST['gender'];
        /*
        * Parameters
        */

        $email = $_REQUEST['email'];
        $password_hash = $this->createPasswordHash($_REQUEST['password']);

        /*
        * Mongo model
        */
        
		
        $model = new Application_Models_MongoDB();
        $model->createNewCustomer($email, $password_hash);

        /*
        * Grab newly created customer and return customer object
        * Auto login customer
        */
        
        $customer_object = $model->getCustomerByEmail($email);
        $_SESSION['user'] = $customer_object;
        return $customer_object;

    }
}
