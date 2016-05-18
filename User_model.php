<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model {
	public function __construct(){
		parent::__construct();
	}

	public function currentBalanceAff($loginUserId){
		$this->db->where('user_id', $loginUserId);
		$queryResult = $this->db->get('affiliate')->row();
		$userBalancePoints = $queryResult->amount;
		return $userBalancePoints;
	}

	public function refferEarn($loginUserId){
		$queryResult = 	$this->db->query("SELECT firstname, lastname, email, phone
						FROM user
                        INNER JOIN
                        affiliate
                        ON user.id = affiliate.user_id
                        WHERE affiliate.refferd_by = '".$loginUserId."'");
		$allReffer = $queryResult->result();
		return $allReffer;
	}


	function orderdataById($id){
		$this->db->where('id', $id);
		$result = $this->db->get('orders')->result();
		if($result !=''){
			return $result;
		}else return false;
	}

	function getOrderdataByUserId($id){
		$this->db->where('customer_id', $id);
		$result = $this->db->get('orders')->result();
		if($result !=''){
			return $result;
		}else return false;

	}

	function getBalanceByUserId($id){
		$this->db->where('user_id', $id);
		$result = $this->db->get('balance')->row();
		if($result !=''){
			return $result;
		}else return false;

	}

	function getUserById($id){
		$this->db->where('id', $id);
		$result = $this->db->get('user')->row();
		if($result){
			return $result;
		}else return false;

	}

	function getTransactionByOrderId($id){
		$this->db->where('order_id', $id);
		$result = $this->db->get('transaction')->row();
		if($result){
			return $result;
		}else return false;

	}


	////$$$$$####@@@@@@@@  Process Order $#####@@@@@@@/
	function getProcessOrderByUserId($id){
		$current_status = array('Order Received','Order Processed');

		$this->db->where('customer_id', $id);
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}


	//#@########## Invoice Order @@@@@@@@@@@#########//

	function getInvoeceOrderByUserId($id){
		$current_status = array('Invoice Generated / Updated','Order Shipped');

		$this->db->where('customer_id', $id);
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}


	//#@########## Out Going Order @@@@@@@@@@@#########//

	function getOutgoingOrderSeaShipByUserId($id){
		$current_status = array('Order Shipped', 'Order in Europe or Middle East', 'Order arrived in Kenya', 'Order sorting', 'Ready for Delivery');

		$this->db->where('customer_id', $id);
		$this->db->where('shipping_type', 'Sea Shipping');
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}

	function getOutgoingOrderAirShipByUserId($id){
		$current_status = array('Order Shipped', 'Order in Europe or Middle East', 'Order arrived in Kenya', 'Order sorting', 'Ready for Delivery');

		$this->db->where('customer_id', $id);
		$this->db->where('shipping_type', 'Air Shipping');
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}



	////@@@@@@@############# Older Order ##########///@@@@@@@@@////////
	function getDeliveredOrderAirShpByUserId($id){
		$current_status = array('Dispatched / out for delivery', 'Delivered');

		$this->db->where('customer_id', $id);
		$this->db->where('shipping_type', 'Air Shipping');
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}

	function getProcessOrderSeaShpByUserId($id){
		$current_status = array('Dispatched / out for delivery', 'Delivered');

		$this->db->where('customer_id', $id);
		$this->db->where('shipping_type', 'Sea Shipping');
		$this->db->where_in('current_status', $current_status);

		$result= $this->db->get('orders')->result();


		if($result !=''){
			return $result;
		}else return false;

	}



	//@@@@@@@@@@@## Creat a new Order @@@@@@@@@@////


	function insertTempOrderUserDTL(){
		$this->load->model('order2_model');

		$id = $this->session->userdata('id');

		$this->db->where('id', $id);
		$userDtl= $this->db->get('user')->result();
		//var_dump($userDtl);
		//echo $userDtl->firstname.'dfgdfg';
		$session_id = $this->order2_model->getTempSessionID();

		$insertArray = array();
		$insertArray['session_id'] = $session_id;
		//$insertArray['cust_firstname'] = $userDtl->firstname;
		//$insertArray['cust_lastname'] = $userDtl->lastname;
		//$insertArray['cust_phone'] = $userDtl->phone;
		//$insertArray['cust_email'] = $userDtl->email;
		$insertArray['order_date'] = date('Y-m-d H:i:s');
		$insertArray['insert_cust_info'] = 1;

		$this->db->where('session_id', $session_id);
		$res = $this->db->get('temp_order')->row();
		if($res){
			if($this->db->update('temp_order', $insertArray)){
				return true;
			}else{
				return false;
			}
		} else {
			if($this->db->insert('temp_order', $insertArray)) {
				return true;
			}else{
				return false;
			}
		}
	}


	function insertTempOrderLocatType(){

		$this->load->model('order2_model');
		$session_id = $this->order2_model->getTempSessionID();

		$insertArray = array();
		$insertArray['session_id'] = $session_id;

		if($_POST['locationAnother'] !=''){
			$insertArray['other_locate_area'] = trim($_POST['locationAnother']);
		}

		$insertArray['locate_area'] = trim($_POST['locate_area']);


		$insertArray['shipping_type'] = trim($_POST['shipping_type']);
		$insertArray['insert_order_info'] = 1;

		$this->db->where('session_id', $session_id);
		$res = $this->db->get('temp_order')->row();

		if($res){
			$this->db->where('session_id', $session_id);
			if($this->db->update('temp_order', $insertArray)){return '1';}else{return '0';}
		} else {
			if($this->db->insert('temp_order', $insertArray)){return '1';}else{return '0';}
		}
	}


	function insertTempOrderItem(){

		$this->load->model('order2_model');
		$session_id = $this->order2_model->getTempSessionID();
		$this->db->where('session_id', $session_id);
		$res = $this->db->get('temp_order')->row();
		if($res){

			$this->db->where('session_id', $session_id);
			$this->db->delete('temp_order_item');

			$item_name = $_POST['item_name'];
			$item_url = $_POST['item_url'];
			$description = $_POST['description'];
			$quantity = $_POST['quantity'];
			$weight = $_POST['weight'];
			$value = $_POST['value'];

			foreach($item_name as $key => $val){
				if($val != ''){
					$insertArray = array();
					$insertArray['session_id'] = $session_id;
					$insertArray['item_name'] = $val;
					$insertArray['item_url'] = $item_url[$key];
					$insertArray['description'] = $description[$key];
					$insertArray['quantity'] = $quantity[$key];
					$insertArray['weight'] = $weight[$key];
					$insertArray['value'] = $value[$key];
					$this->db->insert('temp_order_item', $insertArray);
				}
			}

			$updateOrderArray = array();
			$updateOrderArray['insert_item_info'] = 1;
			$this->db->where('session_id', $session_id);
			$this->db->update('temp_order', $updateOrderArray);
			echo 1;
		}
	}



	function editUserParsonalDTl($id){
		$firstname = $_POST['firstname'];
		$firstname = str_split($firstname);

		$lastname = $_POST['lastname'];
		$lastname = str_split($lastname);

		$suit = $firstname['0'].''.$lastname['0'];

		$insert_array =  array();

		//$insert_array['firstname'] = trim($this->input->post('firstname'));
		//$insert_array['lastname'] = trim($this->input->post('lastname'));
		$insert_array['phone'] = trim($this->input->post('phone'));
		//$insert_array['suit'] = trim($this->input->post('suit'));
		$insert_array['street'] = trim($this->input->post('street'));
		$insert_array['state'] = trim($this->input->post('state'));
		$insert_array['city'] = trim($this->input->post('city'));
		$insert_array['postal_code'] = trim($this->input->post('postal_code'));
		$insert_array['country'] = trim($this->input->post('country'));


		$this->db->where('id', $id);
		$update = $this->db->update('user', $insert_array);

		if($update){
			return true;
		}else return false;

	}

	function getTempSessionID(){
		$this->load->library('session');
		$session_id = $this->session->userdata('sessionid');

		if($session_id == ''){
			$session_id = session_id();
			$newdata = array('sessionid' => $session_id);
			$this->session->set_userdata($newdata);
		}
		return $session_id;
	}



	function insertTempOrdersFromHome($userId){
		$this->db->where('id', $userId);
		$userdata = $this->db->get('user')->row();
		$session_id = $this->getTempSessionID();



		/*$name = explode(' ', $_POST['name']);

		if( sizeof($name)>1 ){
			$nameLength = sizeof($name) - 1;
			$lastName = $name[$nameLength];
			unset($name[$nameLength]);
		} else {
			$lastName = '';
		}

		$firstName = implode(' ', $name);	*/



		$insertArray = array();
		$insertArray['session_id'] = $session_id;
		$insertArray['cust_firstname'] = $userdata->firstname;
		$insertArray['cust_lastname'] = $userdata->lastname;
		$insertArray['cust_phone'] = $userdata->phone;
		$insertArray['cust_email'] = $userdata->email;
		$insertArray['order_date'] = date('Y-m-d H:i:s');

		$insertArray['locate_area'] = $_POST['locate_area'];
		if($_POST['locate_area'] == 'Other'){
			$insertArray['other_locate_area'] = $_POST['other_locate_area'];
		}

		$insertArray['shipping_type'] = $_POST['shipping_type'];

		$insertArray['insert_cust_info'] = 1;
		$insertArray['insert_order_info'] = 1;
		//$insertArray['insert_item_info'] = 1;

		$this->db->where('session_id', $session_id);
		$res = $this->db->get('temp_order')->row();

		if($res){
			$this->db->where('session_id', $session_id);
			if($this->db->update('temp_order', $insertArray)){
				//echo "Update";
			}else {//echo "Not Update";
			}
		} else {
			if($this->db->insert('temp_order', $insertArray)){
				//echo "Insert";
			}else { //echo "Not Insert";
			}
		}

	}

	function UpdateBlanace($orderID){


		$userId = $this->session->userdata('id');
		$Balance = $this->Admin_model->getUserBalanceById($userId);

		$amount = $_POST['amount'];
		$invID = $_POST['invoice_id'];
		$payment_for = $_POST['payment_for'];

			$date = date("Y-m-d H:m:i");
		 	 $userId = $this->session->userdata('id');
		   	$insertArray = array();
			$insertArray['user_id'] = $userId;
			$insertArray['date'] = $date;
			$insertArray['payment_for'] = $payment_for;
			$insertArray['invoice_id'] = $invID;
			$insertArray['amount'] = $amount;
			$insertArray['payment_method'] = 'Account Balance';
			$insertArray['order_id'] = $orderID;

			$insert_transaction = $this->db->insert('transaction', $insertArray);

		$updateBlance = array();
		$current_balance = $Balance->amount - $amount;
		$updateBlance['amount'] = $current_balance;
		$this->db->where('user_id', $userId);
		$this->db->update('balance', $updateBlance);

		$UpdateOrderArray = array();
		$UpdateOrderArray['payment_status'] = "Paid";


		$this->db->where('tracking_id',$invID);
		$this->db->update('orders', $UpdateOrderArray);

		return $current_balance;


	}



	function payNowByUser($orderID){
		$loginUserDetail = $this->session->userdata;
		$orderdata = $this->orderdataById($orderID);
		$user_id = $loginUserDetail['id'];
		$Balance = $this->Admin_model->getUserBalanceById($user_id);
		$Transaction = $this->Admin_model->getTransactionByIdtracking_id($orderID);

		$TransactionArray = array();

		$TransactionArray['payment_for'] = 'Account Balance';

		$updateBlance = array();
		$current_balance = $Balance->amount - $orderdata->amount;
		$updateBlance['amount'] = $current_balance;
		//exit();
		$this->db->where('user_id', $user_id);
		if($this->db->update('balance', $updateBlance)){

			$updateArray = array();
			$updateArray['payment_status'] = 'Paid';

			$this->db->where('id', $orderID);
			if($this->db->update('orders', $updateArray)){

				$this->db->where('order_id', $orderID);
				if($this->db->update('transaction', $TransactionArray)){
					return true;
				} else  return false;

			} else  return false;

		} else  return false;

	}




}
