<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST, PUT");

class Rest extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
		// $this->load->helper('url');
		$this->load->library('upload');  
		$this->load->model('ixt_models');
		$this->load->helper('url_helper');
		$this->load->library('user_agent');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('excel');
        $this->load->helper('file');
		$this->load->model('queries_trend');
		// $this->load->database('ixt_v2');
    }
    
    /*
	check wheter this session is allowed
    */
    
    public function Index()
	{
		$this->load->view('common/api');
	}

	public function session_check($user_type_parent){
		if( (!$this->session->userdata('logged_in')) || ( $this->session->userdata('user_type_parent') > $user_type_parent) ){
			redirect('login');
		}else{
			$data['email']=$this->session->userdata('email');
			$data['user_cu_id']=$this->session->userdata('user_cu_id');
			$data['user_type_parent']=$this->session->userdata('user_type_parent');
			return $data;
		}
    }
    
    /*
	##################################################################
	# Master Main Menu
	##################################################################
	*/
	

	// Used to Count ticket per CU 
	public function getTicketCu(){
			// Query ticket per CU ID
			$date = DATE("Y-m-d");
			$time = DATE("H:i:s");

			// $data = $this->queries_trend->getDataCustomer();
			// In Queue = 2
			// In Progress = 4,5,6
			// Failed = 10,11,12,13,14,16 
			// Finish = 9

		$query = $this->db->query("SELECT (SELECT COUNT(CASE WHEN ( (p_isat_jabo_ticket.t_status < 4 OR p_isat_jabo_ticket.t_status = 17 OR p_isat_jabo_ticket.t_status = 18 OR p_isat_jabo_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_isat_jabo_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_isat_jabo_ticket.t_req_type) +
		(SELECT COUNT(CASE WHEN ( (p_xl_jabo2_ticket.t_status < 4 OR p_xl_jabo2_ticket.t_status = 17 OR p_xl_jabo2_ticket.t_status = 18 OR p_xl_jabo2_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_xl_jabo2_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_xl_jabo2_ticket.t_req_type) +
        (SELECT COUNT(CASE WHEN ( (p_xl_jabo1_ticket.t_status < 4 OR p_xl_jabo1_ticket.t_status = 17 OR p_xl_jabo1_ticket.t_status = 18 OR p_xl_jabo1_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_xl_jabo1_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_xl_jabo1_ticket.t_req_type) + 
        (SELECT COUNT(CASE WHEN ( (p_xl_cj_ticket.t_status < 4 OR p_xl_cj_ticket.t_status = 17 OR p_xl_cj_ticket.t_status = 18 OR p_xl_cj_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_xl_cj_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_xl_cj_ticket.t_req_type) +
        (SELECT COUNT(CASE WHEN ( (p_tsel_sgut_ticket.t_status < 4 OR p_tsel_sgut_ticket.t_status = 17 OR p_tsel_sgut_ticket.t_status = 18 OR p_tsel_sgut_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_tsel_sgut_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_tsel_sgut_ticket.t_req_type) +
        (SELECT COUNT(CASE WHEN ( (p_tsel_steng_ticket.t_status < 4 OR p_tsel_steng_ticket.t_status = 17 OR p_tsel_steng_ticket.t_status = 18 OR p_tsel_steng_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_tsel_steng_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_tsel_steng_ticket.t_req_type) +
        (SELECT COUNT(CASE WHEN ( (p_tsel_kal_ticket.t_status < 4 OR p_tsel_kal_ticket.t_status = 17 OR p_tsel_kal_ticket.t_status = 18 OR p_tsel_kal_ticket.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) FROM p_tsel_kal_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_tsel_kal_ticket.t_req_type) AS InQueue,
        (SELECT COUNT( * ) FROM `p_xl_cj_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
		(SELECT COUNT( * ) FROM `p_xl_jabo1_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_xl_jabo2_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) + 
        (SELECT COUNT( * ) FROM `p_isat_jabo_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_kal_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_sgut_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_steng_ticket` WHERE `t_status` IN ( 4, 5, 6 ) AND DATE( t_open_time ) = DATE( NOW( ) )) AS Progress,
		(SELECT COUNT( * ) FROM `p_xl_cj_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) +
		(SELECT COUNT( * ) FROM `p_xl_jabo1_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_xl_jabo2_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) + 
        (SELECT COUNT( * ) FROM `p_isat_jabo_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_kal_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_sgut_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) +
		(SELECT COUNT( * ) FROM `p_tsel_steng_ticket` WHERE `t_status` =9 AND DATE( t_closed_time ) = DATE( NOW( ) )) AS Finish,
		(SELECT COUNT( * ) FROM `p_xl_cj_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
		(SELECT COUNT( * ) FROM `p_xl_jabo1_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_xl_jabo2_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) + 
        (SELECT COUNT( * ) FROM `p_isat_jabo_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_kal_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
        (SELECT COUNT( * ) FROM `p_tsel_sgut_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) +
		(SELECT COUNT( * ) FROM `p_tsel_steng_ticket` WHERE `t_status` IN ( 10, 11, 12, 13, 14, 16 ) AND DATE( t_open_time ) = DATE( NOW( ) )) AS Failed")->result_array();
		
		foreach ($query as $key => $value) {
			print_r (json_encode(array_values($value)));	
		}
	}

	// Used to count (done ticket) per Customer Project

	public function getTicketCustomer(){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
			$query = "SELECT
				(SELECT COUNT( * ) FROM  `p_xl_cj_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Central,
				(SELECT COUNT( * ) FROM  `p_xl_jabo1_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Jabo1,
				(SELECT COUNT( * ) FROM  `p_xl_jabo2_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Jabo2,
				(SELECT COUNT( * ) FROM  `p_isat_jabo_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Jabo,
				(SELECT COUNT( * ) FROM  `p_tsel_kal_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Kalimantan,
				(SELECT COUNT( * ) FROM  `p_tsel_sgut_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Sumbagut,
				(SELECT COUNT( * ) FROM  `p_tsel_steng_ticket` WHERE  `t_status` =9 AND t_closed_time = '".$date."' ) AS Sumbagteng";

			echo $query;
				// print_r(json_encode($query));
				
	}

	//Used to count all ticket reserved by all user

	public function getUserAchievement(){
		$query = $this->db->query("SELECT CONCAT(user_fname,' ', user_lname) As Username, ixt_user_type.user_owner As Role,
		COUNT( case when t_closed_time > curdate() - interval 1 day THEN 1 END ) as today,
		COUNT( case when t_closed_time > curdate() - interval 7 day THEN 1 END ) as weekly,	
		COUNT( case when t_closed_time > curdate() - interval 1 month THEN 1 END ) as monthly,
		COUNT( case when t_closed_time > curdate() - interval 1 year THEN 1 END ) as yearly
		FROM 
			( select * from p_xl_cj_ticket 
             	LEFT JOIN ixt_user ON p_xl_cj_ticket.t_closed_by = ixt_user.user_id
			union 
			select * from p_xl_jabo1_ticket 
             	LEFT JOIN ixt_user ON p_xl_jabo1_ticket.t_closed_by = ixt_user.user_id
			union 
			select * from p_xl_jabo2_ticket
             	LEFT JOIN ixt_user ON p_xl_jabo2_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_isat_jabo_ticket
             	LEFT JOIN ixt_user ON p_isat_jabo_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_kal_ticket
             	LEFT JOIN ixt_user ON p_tsel_kal_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_sgut_ticket
             	LEFT JOIN ixt_user ON p_tsel_sgut_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_steng_ticket
             	LEFT JOIN ixt_user ON p_tsel_steng_ticket.t_closed_by = ixt_user.user_id
			)A 
		LEFT JOIN m_event_type ON A.t_req_type = m_event_type.ev_type
		LEFT JOIN ixt_user_type ON m_event_type.ev_user_type_target = ixt_user_type.user_type
		WHERE t_status = 9 AND m_event_type.ev_user_type_target = 6
		GROUP BY t_closed_by")->result();

		
		print_r(json_encode($query));
    }

	// Used to get total ticket of each event type

	public function getTotalActivity(){
		$test = [];
		$query = $this->db->query("SELECT ev_activity AS Activity, COUNT( ev_activity ) AS Total
		FROM m_event_type
			LEFT JOIN (

			select t_id, t_req_type, t_status, t_closed_time
			from p_xl_cj_ticket 
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_xl_jabo1_ticket
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_xl_jabo2_ticket
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_isat_jabo_ticket
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_tsel_sgut_ticket
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_tsel_steng_ticket
			union all 
			select t_id, t_req_type, t_status, t_closed_time
			from p_tsel_kal_ticket
			) t ON t.t_req_type = m_event_type.ev_type
				WHERE t.t_status =9 AND Date(t_closed_time) = Date (now()) AND ev_activity='Request Integration'
			; ")->result_array();

			
			print_r(json_encode($query));
	}

	public function passingProjectAchievementQueue($customer, $project){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
		$query = $this->db->query("SELECT (
			SELECT COUNT(CASE WHEN ( (`p_".$customer."_".$project."_ticket`.t_status < 4 OR `p_".$customer."_".$project."_ticket`.t_status = 17 OR `p_".$customer."_".$project."_ticket`.t_status = 18 OR `p_".$customer."_".$project."_ticket`.t_status = 19) AND (m_event_type.ev_user_type_target IN (6, 12)) ) THEN 1 ELSE NULL END) 
			FROM `p_".$customer."_".$project."_ticket` LEFT JOIN m_event_type ON m_event_type.ev_type=`p_".$customer."_".$project."_ticket`.t_req_type) AS Queue; ")->result_array();

		return $query;

	}

	public function getProjectAchievementQueue(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passingProjectAchievementQueue($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passingProjectAchievementQueue($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passingProjectAchievementQueue($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passingProjectAchievementQueue($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 'Queue');
		print_r(json_encode($ids));
	}

	public function passingProjectAchievementDone($customer, $project){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
		$query = $this->db->query("SELECT (

			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =9 AND Date(t_closed_time) = Date (now())
			) AS done, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =10 AND Date(t_open_time) = Date (now())
			) AS Incomplete, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =2 AND Date(t_open_time) = Date (now())
			) AS New; ")->result_array();

		return $query;

	}

	public function getProjectAchievementDone(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passingProjectAchievementDone($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passingProjectAchievementDone($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passingProjectAchievementDone($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passingProjectAchievementDone($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 'done');
		print_r(json_encode($ids));
	}

	public function passingProjectAchievementInc($customer, $project){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
		$query = $this->db->query("SELECT (

		SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =9 AND Date(t_closed_time) = Date (now())
			) AS done, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =10 AND Date(t_open_time) = Date (now())
			) AS Incomplete, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =2 AND Date(t_open_time) = Date (now())
			) AS New; ")->result_array();

		return $query;

	}

	public function getProjectAchievementInc(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passingProjectAchievementInc($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passingProjectAchievementInc($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passingProjectAchievementInc($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passingProjectAchievementInc($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r($test);
		$ids= array_column($test, 'Incomplete');
		print_r(json_encode($ids));
	}

	public function passingProjectAchievementNew($customer, $project){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
		$query = $this->db->query("SELECT (

		SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =9 AND Date(t_closed_time) = Date (now())
			) AS done, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =10 AND Date(t_open_time) = Date (now())
			) AS Incomplete, (
			
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =2 AND Date(t_open_time) = Date (now())
			) AS New; ")->result_array();
		return $query;

	}

	public function getProjectAchievementNew(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passingProjectAchievementNew($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passingProjectAchievementNew($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passingProjectAchievementNew($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passingProjectAchievementNew($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 'New');
		print_r(json_encode($ids));
	}
	
	public function passingCustomer($customer, $project){

		$total_ticket = $this->queries_trend->ticketHelper();
		foreach ($total_ticket as $key => $value) {
			$tot = $value['Total'];
		}
		$query = $this->db->query("SELECT ROUND((SUM(t_status=9)/".$tot.")*100) AS $customer FROM `p_".$customer."_".$project."_ticket`; ")->result_array();

		return $query;
	}

	public function test(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passingCustomer($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passingCustomer($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passingCustomer($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passingCustomer($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		$final = array();

		// array_walk_recursive($test, function($item, $key) use (&$final){
		// 	$final[$key] = isset($final[$key]) ?  $item + $final[$key] : $item;
			
		// });
		// print_r(json_encode($final));
		// print_r(json_encode($test));
		$temp = [];
		foreach ($test as  $value)
		{
			foreach($value as $k => $v){
				$temp[$k] = (empty($temp[$k]) ? 0: $temp[$k]);
				$temp[$k] += $v;
			}
		}
		$new_temp = [];

		foreach ($temp as $key => $value) {
			$new_temp[] = [$key => $value];
		}
		print_r(json_encode($new_temp));
	}

	//Used to count number of ticket reserved by TAC today's OFF 
	public function UserUnShift(){
		$query = '';
		$sumArray = array();
		$arr = array();
		$data = $this->queries_trend->getDataCustomer();
		unset($data[1]);
		unset($data[2]);
		unset($data[3]);
		unset($data[4]);
		unset($data[5]);
		unset($data[6]);
		unset($data[7]);

		// print_r($data);
		foreach ($data as $key => $value) {
			$customer = $value['Customer'];
			$project  = $value['Project'];

			if ($query == '') {
				$query = $query . "SELECT (SELECT COUNT( * ) FROM  `p_".$customer."_".$project."_shift_schedule` LEFT JOIN m_shift_schedule ON `p_".$customer."_".$project."_shift_schedule`.shift_id = m_shift_schedule.shift_id WHERE `p_".$customer."_".$project."_shift_schedule`.shift_date = DATE(NOW()) AND TIME(NOW()) NOT BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time)";
			} else {
				$query = $query . "+ (SELECT COUNT( * ) FROM  `p_".$customer."_".$project."_shift_schedule` LEFT JOIN m_shift_schedule ON `p_".$customer."_".$project."_shift_schedule`.shift_id = m_shift_schedule.shift_id WHERE `p_".$customer."_".$project."_shift_schedule`.shift_date = DATE(NOW()) AND TIME(NOW()) NOT BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time) ";
			}
		}
		$query_binding = $query . "AS total";
		// echo $query_binding;
		$temp = $this->db->query($query_binding)->result_array();
		print_r(json_encode($temp));
	}


	//Used to count number of ticket reserved by TAC today's shift 
	public function UserOnShift(){
		$query = '';
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			$customer = $value['Customer'];
			$project  = $value['Project'];

			if ($query == '') {
				$query = $query . "SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			} else {
				$query = $query . " UNION ALL SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			}
		}
		$query_final = "SELECT CONCAT(ixt_user.user_fname, ' ', ixt_user.user_lname) as Name,
		CASE WHEN 
			SUM( helper.total ) <2 THEN  'Idle'
		WHEN SUM( helper.total ) =2 THEN  'Busy'
		WHEN SUM( helper.total ) >2 THEN  'Overload'
		END AS test
			FROM (" . $query . ") as helper 
			LEFT JOIN ixt_user on ixt_user.user_id=helper.user_id
			GROUP BY helper.user_id";
			// echo $query_final;
		$temp = $this->db->query($query_final)->result_array();
		print_r(json_encode($temp));
		
	}

	//Used to make percentage User On Shift  
	public function UserOnShiftIndicator(){
		$query = '';
		$key_idle = 0;
		$key_busy = 0;
		$key_overload = 0;
		$res = [];
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			$customer = $value['Customer'];
			$project  = $value['Project'];

			if ($query == '') {
				$query = $query . "SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			} else {
				$query = $query . " UNION ALL SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			}
		}
		$query_final = "SELECT CONCAT(ixt_user.user_fname, ixt_user.user_lname) as Name,
		CASE 
		WHEN SUM( helper.total ) =2 THEN  'Busy'
		WHEN SUM( helper.total ) <2 THEN  'Idle'
		WHEN SUM( helper.total ) >2 THEN  'Overload'
		END AS status
			FROM (" . $query . ") as helper 
			LEFT JOIN ixt_user on ixt_user.user_id=helper.user_id
			GROUP BY helper.user_id ORDER BY status ASC";
		$temp = $this->db->query($query_final)->result_array();
		$statuses = array_count_values(array_column($temp, 'status'));
		
		$new_temp = [];

		foreach ($statuses as $key => $value) {
			$new_temp[] = [$key => $value];
		}
		// $t = sort($new_temp);
		print_r(json_encode($new_temp));

	}

	//Used to make Total User On Shift  
	public function UserOnBoard(){
		$query = '';
		$sumArray = array();
		$arr = array();
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		unset($data[5]);
		unset($data[6]);
	
		$query = "SELECT COUNT( user_id ) total
			FROM  `p_isat_jabo_shift_schedule` 
			LEFT JOIN m_shift_schedule ON p_isat_jabo_shift_schedule.shift_id = m_shift_schedule.shift_id
			WHERE  `shift_date` = DATE( NOW( ) ) 
			AND TIME( NOW( ) ) 
			BETWEEN m_shift_schedule.start_time
			AND m_shift_schedule.end_time

			UNION ALL SELECT COUNT( user_id ) total
			FROM  `p_xl_jabo2_shift_schedule` 
			LEFT JOIN m_shift_schedule ON p_xl_jabo2_shift_schedule.shift_id = m_shift_schedule.shift_id
			WHERE  `shift_date` = DATE( NOW( ) ) 
			AND TIME( NOW( ) ) 
			BETWEEN m_shift_schedule.start_time
			AND m_shift_schedule.end_time

			UNION ALL SELECT COUNT( user_id ) total
			FROM  `p_tsel_sgut_shift_schedule` 
			LEFT JOIN m_shift_schedule ON p_tsel_sgut_shift_schedule.shift_id = m_shift_schedule.shift_id
			WHERE  `shift_date` = DATE( NOW( ) ) 
			AND TIME( NOW( ) ) 
			BETWEEN m_shift_schedule.start_time
			AND m_shift_schedule.end_time";

		// echo $query;
		
		$temp = $this->db->query($query)->result_array();
		foreach ($temp as $index => $val) {
			foreach ($val as $i => $v) {
				// $res[$i]+=$v;
				array_key_exists( $i, $sumArray ) ? $sumArray[$i] += $v : $sumArray[$i] = $v;
			}			
		}
		array_push($arr, $sumArray);
		print_r(json_encode($arr));
		

	}

	public function count_project_queue(){
		/*Return Todays Target & Todays Achieve*/
		$query="SELECT COUNT(CASE WHEN (p_isat_jabo_ticket.t_status > 8) AND (m_event_type.ev_user_type_target IN (6, 12, 14)) THEN 1 ELSE NULL END) as finish FROM p_isat_jabo_ticket LEFT JOIN m_event_type ON m_event_type.ev_type=p_isat_jabo_ticket.t_req_type;";
		
		$test = $this->db->query($query)->result_array();

		print_r(json_encode($test));
	}

	public function view_project_average_done(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passing_done($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passing_done($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passing_done($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passing_done($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 'done');
		print_r(json_encode($ids));
	}	

	public function passing_done($customer, $project){
		$query = $this->db->query("SELECT IFNULL(COUNT(CASE WHEN (p_".$customer."_".$project."_m_site_data.m_date_target=DATE(NOW()) AND p_".$customer."_".$project."_ticket.t_status=9 ) THEN 1 ELSE NULL END),0) as done FROM p_".$customer."_".$project."_m_site_data LEFT JOIN p_".$customer."_".$project."_ticket_last_row_ingroup ON p_".$customer."_".$project."_ticket_last_row_ingroup.t_m_id=p_".$customer."_".$project."_m_site_data.m_id AND p_".$customer."_".$project."_ticket_last_row_ingroup.t_req_type='04_int_finish_ack' LEFT JOIN p_".$customer."_".$project."_ticket ON p_".$customer."_".$project."_ticket.t_id=p_".$customer."_".$project."_ticket_last_row_ingroup.t_id WHERE p_".$customer."_".$project."_m_site_data.m_status=1; ")->result_array();
		return $query;
	}

	public function view_project_average_ongoing(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passing_ongoing($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passing_ongoing($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passing_ongoing($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passing_ongoing($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 't_open');
		print_r(json_encode($ids));
	}	

	public function passing_ongoing($customer, $project){
		$query=$this->db->query("SELECT m_event_type.ev_activity, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status < 4 THEN 1 ELSE NULL END) as t_buffer, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status > 3 THEN 1 ELSE NULL END) as t_open FROM m_event_project_setting 
			LEFT JOIN m_event_type ON m_event_project_setting.ev_type=m_event_type.ev_type
			LEFT JOIN p_".$customer."_".$project."_ticket ON p_".$customer."_".$project."_ticket.t_req_type=m_event_project_setting.ev_type
			WHERE  m_event_project_setting.cust_id='".$customer."' AND m_event_project_setting.project_id='".$project."' AND m_event_project_setting.ev_active=1 AND m_event_type.ev_user_type_target=6 AND m_event_type.ev_object='TICKET' AND p_".$customer."_".$project."_ticket.t_status < 9;")->result_array();

		return $query;
	}

	public function view_project_average_queue(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passing_queue($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passing_queue($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passing_queue($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passing_queue($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r(json_encode($test));
		$ids= array_column($test, 't_buffer');
		print_r(json_encode($ids));
	}	

	public function passing_queue($customer, $project){
		$query=$this->db->query("SELECT m_event_type.ev_activity, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status < 4 THEN 1 ELSE NULL END) as t_buffer, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status > 3 THEN 1 ELSE NULL END) as t_open FROM m_event_project_setting 
			LEFT JOIN m_event_type ON m_event_project_setting.ev_type=m_event_type.ev_type
			LEFT JOIN p_".$customer."_".$project."_ticket ON p_".$customer."_".$project."_ticket.t_req_type=m_event_project_setting.ev_type
			WHERE  m_event_project_setting.cust_id='".$customer."' AND m_event_project_setting.project_id='".$project."' AND m_event_project_setting.ev_active=1 AND m_event_type.ev_user_type_target=6 AND m_event_type.ev_object='TICKET' AND p_".$customer."_".$project."_ticket.t_status < 9;")->result_array();

		return $query;
	}

	public function passing_incomplete($customer, $project){
		$date = DATE("Y-m-d");
		$time = DATE("H:i:s");
		$query = $this->db->query("SELECT (
	
			SELECT COUNT( * ) 
			FROM  `p_".$customer."_".$project."_ticket` 
			WHERE  `t_status` =10 AND Date(t_open_time) = Date (now())
			) AS Incomplete")->result_array();

		return $query;

	}

	public function view_project_average_incomplete(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->passing_incomplete($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->passing_incomplete($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->passing_incomplete($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->passing_incomplete($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		// print_r($test);
		$ids= array_column($test, 'Incomplete');
		print_r(json_encode($ids));
	}

	public function UserActive(){
		$query = '';
		$key_idle = 0;
		$key_busy = 0;
		$key_overload = 0;
		$sumArray = array();
		$arr = array();
		$res = [];
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			$customer = $value['Customer'];
			$project  = $value['Project'];

			if ($query == '') {
				$query = $query . "SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			} else {
				$query = $query . " UNION ALL SELECT p_".$customer."_".$project."_shift_schedule.user_id,
				COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_og_served_by=p_".$customer."_".$project."_shift_schedule.user_id THEN 1 ELSE NULL END) AS total
				FROM p_".$customer."_".$project."_shift_schedule 
				LEFT JOIN m_shift_schedule ON m_shift_schedule.shift_id = p_".$customer."_".$project."_shift_schedule.shift_id
				LEFT JOIN p_".$customer."_".$project."_ticket ON (DATE(p_".$customer."_".$project."_ticket.t_input_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR DATE(p_".$customer."_".$project."_ticket.t_open_time) = p_".$customer."_".$project."_shift_schedule.shift_date
					OR	DATE(p_".$customer."_".$project."_ticket.t_closed_time) = p_".$customer."_".$project."_shift_schedule.shift_date)
				WHERE p_".$customer."_".$project."_shift_schedule.shift_date = DATE(NOW()) 
				AND TIME(NOW()) BETWEEN m_shift_schedule.start_time AND m_shift_schedule.end_time
				AND m_shift_schedule.cu_id='EID' 
				GROUP BY p_".$customer."_".$project."_shift_schedule.user_id ";
			}
		}
		$query_final = "SELECT CONCAT(ixt_user.user_fname, ixt_user.user_lname) as Name,
		CASE WHEN 
			SUM( helper.total ) <2 THEN  'total'
		WHEN SUM( helper.total ) =2 THEN  'total'
		WHEN SUM( helper.total ) >2 THEN  'total'
		END AS status
			FROM (" . $query . ") as helper 
			LEFT JOIN ixt_user on ixt_user.user_id=helper.user_id
			GROUP BY helper.user_id";
		$temp = $this->db->query($query_final)->result_array();
		$statuses = array_count_values(array_column($temp, 'status'));
		
		$new_temp = [];

		foreach ($statuses as $key => $value) {
			$new_temp[] = [$key => $value];
		}
		print_r(json_encode($new_temp));
		// print_r($new_temp);
	}

	public function upper_queue(){
		$arr = array();
		$test = array();
		$res = array();	
		$data = $this->queries_trend->getDataCustomer();
		unset($data[4]);
		foreach ($data as $key => $value) {
			if ($this->pass_upper_queue($value['Customer'], $value['Project']) != null) {
				for($i=0; $i<=count($this->pass_upper_queue($value['Customer'], $value['Project'])); $i++){
					if(!empty( $this->pass_upper_queue($value['Customer'], $value['Project'])[$i])) {
						array_push($test, $this->pass_upper_queue($value['Customer'], $value['Project'])[$i]);
					}
				}
			}
		}

		print_r(json_encode($test));
		// $ids= array_column($test, 't_buffer');
		// print_r(json_encode($ids));
	}

	public function passing_upper_queue($customer, $project){
		$query=$this->db->query("SELECT CONCAT(cust_id,' ', project_id) as customer, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status < 4 THEN 1 ELSE NULL END) as in_queue, COUNT(CASE WHEN p_".$customer."_".$project."_ticket.t_status > 3 THEN 1 ELSE NULL END) as on_going FROM m_event_project_setting 
			LEFT JOIN m_event_type ON m_event_project_setting.ev_type=m_event_type.ev_type
			LEFT JOIN p_".$customer."_".$project."_ticket ON p_".$customer."_".$project."_ticket.t_req_type=m_event_project_setting.ev_type
			WHERE  m_event_project_setting.cust_id='".$customer."' AND m_event_project_setting.project_id='".$project."' AND m_event_project_setting.ev_active=1 AND m_event_type.ev_user_type_target=6 AND m_event_type.ev_object='TICKET' AND p_".$customer."_".$project."_ticket.t_status < 9;")->result_array();

		return $query;
	}

	public function pass_upper_queue($customer, $project){
		$query=$this->db->query("SELECT
				'".$project."' AS customer,
				COUNT(
				CASE WHEN p_".$customer."_".$project."_ticket.t_status = 2
				OR p_".$customer."_".$project."_ticket.t_status = 3 THEN 1 ELSE NULL END
				) AS in_queue,
				COUNT(
				CASE WHEN p_".$customer."_".$project."_ticket.t_status > 3 THEN 1 ELSE NULL END
				) AS on_going
			FROM
			p_".$customer."_".$project."_ticket
				LEFT JOIN m_event_project_setting ON p_".$customer."_".$project."_ticket.t_req_type = m_event_project_setting.ev_type
				LEFT JOIN m_event_type ON m_event_project_setting.ev_type = m_event_type.ev_type
			WHERE
				m_event_project_setting.cust_id = '".$customer."'
				AND m_event_project_setting.project_id = '".$project."'
				AND m_event_project_setting.ev_active = 1
				AND m_event_type.ev_user_type_target = 6
				AND (
				m_event_type.ev_object = 'TICKET'
				OR m_event_type.ev_object = 'ROUTER'
				)
				AND p_".$customer."_".$project."_ticket.t_status < 9
			")->result_array();
		return $query;
	}

	public function getUserAchievementPersonal(){
		$query = $this->db->query("SELECT CONCAT(user_fname,' ', user_lname) As Username, ixt_user_type.user_owner As Role,
		COUNT( case when t_closed_time > curdate() - interval 1 day THEN 1 END ) as today,
		COUNT( case when t_closed_time > curdate() - interval 7 day THEN 1 END ) as weekly,	
		COUNT( case when t_closed_time > curdate() - interval 1 month THEN 1 END ) as monthly,
		COUNT( case when t_closed_time > curdate() - interval 1 year THEN 1 END ) as yearly
		FROM 
			( select * from p_xl_cj_ticket 
             	LEFT JOIN ixt_user ON p_xl_cj_ticket.t_closed_by = ixt_user.user_id
			union 
			select * from p_xl_jabo1_ticket 
             	LEFT JOIN ixt_user ON p_xl_jabo1_ticket.t_closed_by = ixt_user.user_id
			union 
			select * from p_xl_jabo2_ticket
             	LEFT JOIN ixt_user ON p_xl_jabo2_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_isat_jabo_ticket
             	LEFT JOIN ixt_user ON p_isat_jabo_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_kal_ticket
             	LEFT JOIN ixt_user ON p_tsel_kal_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_sgut_ticket
             	LEFT JOIN ixt_user ON p_tsel_sgut_ticket.t_closed_by = ixt_user.user_id
            union
            select * from p_tsel_steng_ticket
             	LEFT JOIN ixt_user ON p_tsel_steng_ticket.t_closed_by = ixt_user.user_id
			)A 
		LEFT JOIN m_event_type ON A.t_req_type = m_event_type.ev_type
		LEFT JOIN ixt_user_type ON m_event_type.ev_user_type_target = ixt_user_type.user_type
		WHERE t_status = 9 AND m_event_type.ev_user_type_target = 6
		GROUP BY t_closed_by")->result();

		$chunk = array_chunk($query, 12);
		print_r(json_encode($chunk));
	}
	
	public function login_auth(){
		$response = array("error" => FALSE);
		$email = $this->input->get('email');
		$password = md5($this->input->get('password'));
		   
		$user = $this->queries_trend->dashboard_login($email, $password);

		// print_r($user);

		if ($user != false) {
			// user is found

			foreach ($user as $key => $data) {
				if($data["user_status"]==1){
					$response["error"] = FALSE;
					$response["user"]["user_id"] = $data["user_id"];
					$response["user"]["user_fname"] = $data["user_fname"];
					$response["user"]["user_lname"] = $data["user_lname"];
					$response["user"]["user_contact"] = $data["user_contact"];
					$response["user"]["user_cu_id"] = $data["user_cu_id"];
					$response["user"]["user_cu_name"] = $data["user_cu_name"];
					$response["user"]["user_asp_id"] = $data["user_asp_id"];
					$response["user"]["user_asp_name"] = $data["user_asp_name"];
					$response["user"]["user_cust_id"] = $data["user_cust_id"];
					$response["user"]["user_project_id"] = $data["user_project_id"];
					$response["user"]["user_join_date"] = $data["user_join_date"];
					$response["user"]["user_last_activity"] = $data["user_last_activity"];
					$response["user"]["user_status"] = $data["user_status"];
					$response["user"]["user_type"] = $data["user_type"];
					$response["user"]["user_prev"] = $data["user_prev"];
					echo json_encode($response);
				
				} else if($data["user_status"]==0){
					$response["error"] = TRUE;
					$response["error_msg"] = "Your Account is inactive, Please Contact Your Manager";
					echo json_encode($response);
				}
			}

		} else {
			// user is not found with the credentials
			$response["error"] = TRUE;
			$response["error_msg"] = "Wrong email or password entered! Please try again!";
			echo json_encode($response);
		}
	}

}