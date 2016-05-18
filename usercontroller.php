<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');

		$this->load->model('Common_model');
		$this->load->model('Login_model');
		$this->load->model('Settings_model');
		$this->load->model('Contact_model');
		$this->load->model('act_model');
		$this->load->model('Action_model');
		$this->load->model('User_model');
		$this->load->model('Admin_model');
		$this->load->model('Order_model');
		$this->load->model('Order2_model');
		$this->load->model('Pesapal_model');
		$this->Login_model->validateLogin();
	}

	public function index()	{

		$header_data = array();

	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');
		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/dashboard',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}


	public function orders_backup(){


		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		//$this->Contact_model->updateContactReader($id);
		$userId = $this->session->userdata('id');

		$header_data['orders_view'] = $this->User_model->getOrderdataByUserId($userId);

		$header_data['orderStatus'] = $this->Settings_model->orderStatus('');

		if(isset($_POST['trackingId'])){
			$header_data['trackingId'] = $_POST['trackingId'];
		}else {
			$header_data['trackingId'] = '';
		}


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/orders',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}


	public function orderdetails($id){

		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		//$this->Contact_model->updateOrder($id);

		$orderdata = $this->Contact_model->orderdataById($id);
		if($orderdata){
			$header_data['orders_data'] = $orderdata;
			$header_data['product_data'] = $this->Contact_model->product_dataByOrderId($id);
			$header_data['customer_data'] = $this->Contact_model->customer_dataById($orderdata->customer_id);
			$header_data['receiver_data'] = $this->Contact_model->receiver_dataById($orderdata->receiver_id);
			$header_data['Order_Status'] = $this->Contact_model->ChangeStatusListByOrderId($orderdata->id);
	  		$header_data['Transaction'] = $this->User_model->getTransactionByOrderId($orderdata->id);
		}else{
			$header_data['orders_data'] = '';
		}



		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/order_details',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function payNow($orderId){


		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');
	    $userId = $this->session->userdata('id');

	    $header_data['balance'] = $this->User_model->getBalanceByUserId($userId);
		$header_data['orderId'] = $orderId;
	    $header_data['UserDetails'] = $this->User_model->getUserById($userId);
	    $header_data['orderdata'] = $this->User_model->orderdataById($orderId);
	    $orderDetail = $header_data['orderdata'];
	  	$header_data['Balance'] = $this->Admin_model->getUserBalanceById($orderDetail[0]->customer_id);
	  	$header_data['Transaction'] = $this->User_model->getTransactionByOrderId($orderId);

	    if(isset($_POST['payNowBalance'])){
			$paid = $this->User_model->payNowByUser($orderId);
			if($paid){
				$header_data['success_message'] = 'Your payment is success.';
			}else{
				$header_data['error_message'] = 'Your payment is not success. please try again.';
			}
		}


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/payNow',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);

	}


	public function pay_with_balance($orderId){

		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$userId = $this->session->userdata('id');

		$header_data['current_balance'] = $this->User_model->UpdateBlanace($orderId);
		$header_data['amount'] = $_POST['amount'];
		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/BalanceUpdate',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);




	}


	public function orders(){


		$header_data = array();

		// For Order
		$session_data = $this->session->userdata('logged_in');
		$header_data['session_data'] = $session_data;
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');


		//$this->Contact_model->updateContactReader($id);
		$userId = $this->session->userdata('id');

		$header_data['orders_view'] = $this->User_model->getOrderdataByUserId($userId);
		$header_data['user_DTL'] = $this->User_model->getUserById($userId);

		$header_data['OutgoingOrderSea'] = $this->User_model->getOutgoingOrderSeaShipByUserId($userId);
		$header_data['OutgoingOrderAir'] = $this->User_model->getOutgoingOrderAirShipByUserId($userId);
		$header_data['processOrder'] = $this->User_model->getProcessOrderByUserId($userId);
		$header_data['invoeceOrder'] = $this->User_model->getInvoeceOrderByUserId($userId);
		$header_data['DeliveredOrderAirShip'] = $this->User_model->getDeliveredOrderAirShpByUserId($userId);
		$header_data['DeliveredOrderSeaShip'] = $this->User_model->getProcessOrderSeaShpByUserId($userId);

		$header_data['logedUser'] = $this->Order_model->getuserByID($userId);

		$header_data['orderStatus'] = $this->Settings_model->orderStatus('');

		if(isset($_POST['trackingId'])){
			$header_data['trackingId'] = $_POST['trackingId'];
		}else {
			$header_data['trackingId'] = '';
		}


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/orderNew',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function completeOrder()	{


		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');
	    $userId = $this->session->userdata('id');

	    if(isset($_POST['submitaorder'])){
			$coustomarId = $this->User_model->insertTempOrdersFromHome($userId);
		}


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/completeOrder',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function accountbalance(){


		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');
	    $userId = $this->session->userdata('id');

	    $header_data['balance'] = $this->User_model->getBalanceByUserId($userId);


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/AccountBalance',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);

	}

	public function fundAccountbalance(){


		$header_data = array();
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');
	    $userId = $this->session->userdata('id');


		$header_data['balance'] = $this->User_model->getBalanceByUserId($userId);
	    $header_data['UserDetails'] = $this->User_model->getUserById($userId);
		 //$header_data['UserID'] = $userId;




		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/fundAccountbalance',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function USAaddress(){


		$header_data = array();

		$userId = $this->session->userdata('id');

		if(isset($_POST['saveChange'])){
			$update = $this->User_model->editUserParsonalDTl($userId);
			if($update){
				$header_data['success_message'] ="Your information is Updated.";
			}else{
				$header_data['error_message'] ="Sorry Your information is not update. Plase try again .";
			}

		}


		$header_data['UserDetails'] = $this->Order_model->getuserByID($userId);

	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/USAaddress',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function shipping_calculator()	{

		$header_data = array();

		//var_dump($this->session->userdata());
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$header_data['name'] = $this->session->userdata('name');
		$header_data['email'] = $this->session->userdata('email');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/shipping_calculator',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	public function shipping_calculator2()	{


		$header_data = array();

		//var_dump($this->session->userdata());
	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$header_data['name'] = $this->session->userdata('name');
		$header_data['email'] = $this->session->userdata('email');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/_shipping_calculator',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}
	public function refer_friend()	{


		$header_data = array();

		$loginUserId = $this->session->userdata('id');

		//print_r($loginUserId);

		//var_dump($this->session->userdata());
		$usertaka = $this->User_model->currentBalanceAff($loginUserId);
		$header_data['currentBalancePoints'] = $usertaka;

		$refEarn = $this->User_model->refferEarn($loginUserId);
		$header_data['allReffer'] = $refEarn;

	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$header_data['name'] = $this->session->userdata('name');
		$header_data['email'] = $this->session->userdata('email');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/refer_friend',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}


	public function pesapal_payment()	{

		$header_data = array();

	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$header_data['name'] = $this->session->userdata('name');
		$header_data['email'] = $this->session->userdata('email');

		$header_data['pesapal_payment'] = $this->Pesapal_model->pespal_payment();


		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/pesapal',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}


public function pesapal_response()	{

		$header_data = array();

	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$header_data['name'] = $this->session->userdata('name');
		$header_data['email'] = $this->session->userdata('email');


		$header_data['pesapal_response'] = $this->Pesapal_model->pespal_response();

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/paypal_thankyou',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}

	function paypal_payment(){

		 	$header_data = array();
			$header_data['amount'] = $_POST['amount'];
			$header_data['payment_for'] = $_POST['payment_for'];
			$userId = $this->session->userdata('id');
			$InvID = $_POST['invoice_id'];

			$header_data['UserDetails'] = $this->Order_model->getuserByID($userId);
			$header_data['InvID'] = $InvID;
		 	$header_data['paypal'] = $this->Pesapal_model->paypal_payment($InvID);

			$output=$this->load->view('user/paypal',$header_data,true);

			$this->output->set_output($output);

	}

	function paypal_response(){

		 	$header_data = array();
			$userId = $this->session->userdata('id');
		 	$header_data['paypal_response'] = $this->Pesapal_model->paypal_response();

		 $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/paypal_thankyou',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);

	}


	public function shipping_calculator_result(){

		$header_data = array();

		if (isset($_POST['sea_shipping'])){
			$width = $_POST['width'];
			$height = $_POST['height'];
			$length =  $_POST['length'];
			$finalwidth = 0;
			$finallength = 0;
			$finalheight = 0;
			$i=0;
			for($i==0;$i<sizeof($width);$i++){
				$finalwidth = $finalwidth + $width[$i];
			}

			$j=0;
			for($j==0;$j<sizeof($length);$j++){
				$finallength = $finallength + $length[$j];
			}

			$k=0;
			for($k==0;$k<sizeof($height);$k++){
				$finalheight = $finalheight + $height[$k];
			}

			$cubic_footage = $finalwidth * $finalheight * $finallength / 1728;
			$Sea_shipping_quote = $cubic_footage * 35;
			$Sea_shipping_quote = round($Sea_shipping_quote,2);
			$header_data['Sea_shipping_quote'] = $Sea_shipping_quote;
			}

			if (isset($_POST['air_shipping'])){

			$width = $_POST['width'];
			$height = $_POST['height'];
			$length =  $_POST['length'];
			$finalwidth = 0;
			$finallength = 0;
			$finalheight = 0;
			$i=0;
			for($i==0;$i<sizeof($width);$i++){
				$finalwidth = $finalwidth + $width[$i];
			}

			$j=0;
			for($j==0;$j<sizeof($length);$j++){
				$finallength = $finallength + $length[$j];
			}

			$k=0;
			for($k==0;$k<sizeof($height);$k++){
				$finalheight = $finalheight + $height[$k];
			}
			$acc_weight =  $_POST['weight'];
			$volumedivic_weight = $finalwidth * $finallength * $finalheight / 166;
			if ($volumedivic_weight > $acc_weight){
			$air_shipping_cost = $volumedivic_weight * 13.66;
			}else {
			$air_shipping_cost = $acc_weight * 13.66;
			}
			$air_shipping_cost = round($air_shipping_cost,2);
			$header_data['air_shipping_cost'] = $air_shipping_cost;
		}



	    $header_data['main_menu'] = $this->Settings_model->createMainMenu('Home');

		$output = $this->load->view('user/common/header',$header_data,true);
		$output .= $this->load->view('user/common/main_nav',$header_data,true);

		$output.=$this->load->view('user/shipping_calculator_result',$header_data,true);

		$output .= $this->load->view('user/common/footer',$header_data,true);

		$this->output->set_output($output);


	}


}
