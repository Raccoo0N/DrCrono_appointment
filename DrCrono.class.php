<?php
// 
// https://drchrono.com/api-management/
// 
	define('REDIRECT_URL', "http://DOMAIN/auth.php" ); 
    define('CLIENT_ID', "CLIENT_ID" ); 
    define('CLIENT_SECRET', "CLIENT_SECRET" ); 
    define('SCOPE', "user:read user:write calendar:read calendar:write patients:read patients:write patients:summary:read patients:summary:write billing:read billing:write clinical:read clinical:write labs:read labs:write messages:read messages:write settings:read settings:write tasks:read tasks:write" ); 
    define('SCOPE_LITE', "calendar:read calendar:write patients:read patients:write clinical:read clinical:write"); 
    define('AUTH_URL', "https://drchrono.com/o/authorize/");
    define('TOKEN_URL', "https://drchrono.com/o/token/"); 
    define('API_URL', "https://drchrono.com/api/"); 
    define('USER_AGENT', "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)"); 
    define('REVOKE_URL', "https://drchrono.com/o/revoke_token/");

	class DrCrono { 
		public $user;
        public $password;
        public $clientId; 
        public $clientSecret;
        public $apiUrl; 
        public $authUrl; 
        public $tokenUrl; 
        public $userAgent; 
//
//===================================
		public function __construct( $d=array() ){
			$this->user = "USER_LOGIN"; 
			$this->password = "USER_PASSWORD"; 
			$this->clientId = CLIENT_ID; 
			$this->clientSecret = CLIENT_SECRET; 
			$this->apiUrl = API_URL;  
			$this->authUrl = AUTH_URL; 
			$this->tokenUrl = TOKEN_URL; 
			$this->userAgent = USER_AGENT; 
		}
//		
//===================================
        public function log( $text="" ){
        	$fp = @fopen(BASE_DIR."/logs/crono/". date("Y-m-d") .".log", "a");	
            if( $fp ){ 
            	@fwrite( $fp, $text ); 
            	@fclose( $fp );
            }
        } 
//		
//===================================
        private static function _prepare( $value="" ){
			$value = strval($value);
			$value = stripslashes($value);
			$value = str_ireplace(array("\0", "\a", "\b", "\v", "\e", "\f"), ' ', $value);
			$value = htmlspecialchars_decode($value, ENT_QUOTES);	
			return $value;
		}
//		
//===================================
        public static function text( $value="", $default="" ){
			$value = self::_prepare($value);
			$value = str_ireplace(array("\t"), ' ', $value);			
			$value = preg_replace(array(
				'@<\!--.*?-->@s',
				'@\/\*(.*?)\*\/@sm',
				'@<([\?\%]) .*? \\1>@sx',
				'@<\!\[CDATA\[.*?\]\]>@sx',
				'@<\!\[.*?\]>.*?<\!\[.*?\]>@sx',	
				'@\s--.*@',
				'@<script[^>]*?>.*?</script>@si',
				'@<style[^>]*?>.*?</style>@siU', 
				'@<[\/\!]*?[^<>]*?>@si',			
			), ' ', $value);		
			$value = strip_tags($value); 		
			$value = str_replace(array('/*', '*/', ' --', '#__'), ' ', $value); 
			$value = preg_replace('/[ ]+/', ' ', $value);			
			$value = trim($value);
			$value = htmlspecialchars($value, ENT_QUOTES);	
			return (strlen($value) == 0) ? $default : $value;
		} 
// 
//===================================
		public function exec( $url="", $params=array(), $method="GET", $log=false ){ 
	        $headers = array( 
	            "Content-type: application/json", 
	            "Accept: application/json",  
	            "Authorization: Bearer ". TOKEN  
	        ); 
	        $fields = array( );  
	        $endpoint = API_URL . $url . ( $method == "GET" ? '?'. http_build_query( $params ) : '' );

	        $ch = curl_init( $endpoint ); 
	        if( $log ){ 
	        	var_dump( $endpoint ); 
	        }
	        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE);
	        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	        curl_setopt( $ch, CURLOPT_HEADER, 0 );
	        curl_setopt( $ch, CURLOPT_VERBOSE, $log ? true : false );
	        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true ); 
	        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); 
	        if( in_array( $method, array( "POST", "PUT", "DELETE" ) ) ){ 
	        	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
	            curl_setopt( $ch, CURLOPT_POST, 1 ) ;
	            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $params ) ); 
	        }
	        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	        $Result = curl_exec($ch);  
	        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		    $headers = substr($Result, 0, $header_size);
		    $body = substr($Result, $header_size);
	        $info = curl_getinfo( $ch ); 
	        $status_code = $info['http_code']; 
	        if( $log ){ 
	        	echo "status_code: ". $status_code ."\n"; 
	        	echo "HEADERS: ". var_export( $headers, 1 ) ."\n";
	        }
	        curl_close( $ch );  
	        $json = json_decode( $Result, 1, 1024 ); 
	        
	        return $json; 
	    } 
// 
//===================================
		public function get_token( $post=array() ){ 
			return array();
		}
// 
//===================================
		public function refresh_token( $post=array() ){
			return array(); 
		}
// 
//===================================
		public function GetDoctors( $post=array(), $log=false ){ 
			$params = array(); 
			$doctors = $this->exec( "doctors", $params, "GET", $log ); 
			/*
			$results = array(
                array( 
                    'id'=> '', 
                    'first_name'=> "", 
                    "last_name"=> "", 
                    "email"=> "", 
                    "specialty"=> "", 
                    "job_title"=> "", 
                    "suffix"=> NULL, 
                    "website"=> NULL, 
                    "home_phone"=> "", 
                    "office_phone"=> "", 
                    "cell_phone"=> "", 
                    "country"=> "US", 
                    "timezone"=> "US/Eastern", 
                    "npi_number"=> "", 
                    "group_npi_number"=> NULL, 
                    "practice_group"=> '', 
                    "practice_group_name"=> "", 
                    "profile_picture"=> "", 
                    "is_account_suspended"=> false  
                ) 
            ); 
            */
            return ( $doctors && isset( $doctors['results'] ) ) ? $doctors['results'] : false;
		}
// 
//===================================
		public function GetOffices( $post=array(), $log=false ){
			$params = array(); 
			$offices = $this->exec( "offices", $params, "GET", $log ); 
			/*
			$results = array(
                 array( 
                    "id"=> '', 
                    "name"=> "Primary Office", 
                    "exam_rooms"=> array( 
                        array( 
                            "index"=> 1, 
                            "name"=> "Exam 1", 
                            "online_scheduling"=> false
                        ), 
                        array(
                            "index"=> 2, 
                            "name"=> "Exam 2", 
                            "online_scheduling"=> false 
                        ), 
                        array(
                            "index"=> 3, 
                            "name"=> "Exam 3", 
                            "online_scheduling"=> false
                        ), 
                        array(
                            "index"=> 4, 
                            "name"=> "Exam 4", 
                            "online_scheduling"=> false
                        )
                    ),
                    "start_time"=> "00:00:00", 
                    "end_time"=> "00:00:00", 
                    "address"=> "", 
                    "city"=> NULL, 
                    "state"=> "TX", 
                    "zip_code"=> NULL, 
                    "country"=> "US", 
                    "online_scheduling"=> false, 
                    "phone_number"=> "", 
                    "doctor"=> '', 
                    "archived"=> false, 
                    "fax_number"=> NULL, 
                    "tax_id_number_professional"=> "" 
                )
            );
			*/
			return ( $offices && isset( $offices['results'] ) ) ? $offices['results'] : false;
		}
// 
//===================================

// 
//===================================
		public function CreatePatient( $post=array(), $log=false ){ 
			$params = array(
				'doctor'=> isset( $post['doctor'] ) ? (int)$post['doctor'] : 0, 
				'gender'=> isset( $post['gender'] ) ? self::text( $post['gender'] ) : "", 
				'first_name'=> isset( $post['first_name'] ) ? self::text( $post['first_name'] ) : "", 
				'last_name'=> isset( $post['last_name'] ) ? self::text( $post['last_name'] ) : "", 
				'date_of_birth'=> isset( $post['date_of_birth'] ) ? $post['date_of_birth'] : "", 
				'cell_phone'=> isset( $post['cell_phone'] ) ? $post['cell_phone'] : "", 
				'home_phone'=> isset( $post['home_phone'] ) ? $post['home_phone'] : "", 
				'email'=> isset( $post['email'] ) ? $post['email'] : ""  
			); 
			if( $log ){
				echo "create patient: ". var_export( $params, 1 ) ."\n"; 
			}
			$patient = $this->exec( "patients", $params, "POST", $log ); 
			/*
			$results = array (
				'id' => '',
				'chart_id' => '',
				'first_name' => '',
				'middle_name' => NULL,
				'last_name' => '',
				'nick_name' => '',
				'date_of_birth' => '',
				'gender' => 'Male',
				'social_security_number' => '',
				'race' => 'blank',
				'ethnicity' => 'blank',
				'preferred_language' => 'blank',
				'patient_photo' => NULL,
				'patient_photo_date' => NULL,
				'custom_demographics' => array () 
			);
			*/
    		return ( $patient && isset( $patient['results'][0] ) ) ? $patient['results'][0] : $patient; 
		} 
// 
//===================================
		public function GetPatient( $post=array(), $log=false ){ 
			$params = array(  ); 
			$gender = isset( $post['gender'] ) ? self::text( $post['gender'] ) : ""; 
			if( $gender ){ $params['gender'] = $gender; } 
			$first_name = isset( $post['first_name'] ) ? self::text( $post['first_name'] ) : ""; 
			if( $first_name ){ $params['first_name'] = $first_name; }
			$last_name = isset( $post['last_name'] ) ? self::text( $post['last_name'] ) : ""; 
			if( $last_name ){ $params['last_name'] = $last_name; }
			$date_of_birth = isset( $post['date_of_birth'] ) ? $post['date_of_birth'] : "";
			if( $date_of_birth ){ $params['date_of_birth'] = $date_of_birth; } 
			$cell_phone = isset( $post['cell_phone'] ) ? $post['cell_phone'] : ""; 
			if( $cell_phone ){ $params['cell_phone'] = $cell_phone; } 
			$home_phone = isset( $post['home_phone'] ) ? $post['home_phone'] : ""; 
			if( $home_phone ){ $params['home_phone'] = $home_phone; } 
			$email = isset( $post['email'] ) ? $post['email'] : ""; 
			if( $email ){ $params['email'] = $email; }  
			if( $log ){
				echo "get patient: ". var_export( $params, 1 ) ."\n"; 
			}
			$patient = $this->exec( "patients", $params, "GET", $log ); 
			return( $patient && isset( $patient['results'][0] ) ) ? $patient['results'][0] : false;  
		}
// 
//===================================
		public function GetPatients( $post=array(), $log=false ){ 
			$params = array(); 
			$patients = $this->exec( "patients", $params, "GET", $log ); 
			/* 
			$results = array(
                array(
                    "id"=> '', 
                    "chart_id"=> "", 
                    "first_name"=> "", 
                    "middle_name"=> NULL, 
                    "last_name"=> "", 
                    "nick_name"=> "", 
                    "date_of_birth"=> "", 
                    "gender"=> "Female", 
                    "social_security_number"=> "", 
                    "race"=> "white", 
                    "ethnicity"=> "not_hispanic", 
                    "preferred_language"=> "eng", 
                    "patient_photo"=> "", 
                    "patient_photo_date"=> NULL, 
                    "patient_payment_profile"=> "Cash", 
                    "patient_status"=> "A", 
                    "home_phone"=> "", 
                    "cell_phone"=> "", 
                    "office_phone"=> "", 
                    "email"=> "", 
                    "address"=> "", 
                    "city"=> "", 
                    "state"=> "California", 
                    "zip_code"=> "", 
                    "emergency_contact_name"=> "", 
                    "emergency_contact_phone"=> "", 
                    "emergency_contact_relation"=> NULL, 
                    "employer"=> "", 
                    "employer_address"=> "", 
                    "employer_city"=> "", 
                    "employer_state"=> "", 
                    "employer_zip_code"=> "", 
                    "disable_sms_messages"=> false, 
                    "doctor"=> '', 
                    "primary_care_physician"=> "", 
                    "date_of_first_appointment"=> "", 
                    "date_of_last_appointment"=> "", 
                    "offices"=> array( 
                        ''
                    ), 
                    "default_pharmacy"=> "", 
                    "referring_source"=> NULL, 
                    "copay"=> "", 
                    "responsible_party_name"=> NULL, 
                    "responsible_party_relation"=> NULL, 
                    "responsible_party_phone"=> NULL, 
                    "responsible_party_email"=> NULL, 
                    "preferred_pharmacies"=> array(), 
                    "updated_at"=> "", 
                    "created_at"=> "2023-06-19T22:09:34" 
                ) 
            );
			*/
    		return ( $patients && isset( $patients['results'] ) ) ? $patients['results'] : false; 
		} 
// 
//===================================
		public function UpdatePatient( $post=array() ){ 
			return array(); 
		} 	
// 
//===================================
		public function CreateAppointment( $post=array(), $log=false ){ 
			$params = array(
				'doctor'=> isset( $post['doctor'] ) ? (int)$post['doctor'] : 0, 
				'patient'=> isset( $post['patient'] ) ? (int)$post['patient'] : 0, 
				'duration'=> isset( $post['duration'] ) ? (int)$post['duration'] : 15, 
				'office'=> isset( $post['office'] ) ? (int)$post['office'] : 1, 
				'exam_room'=> isset( $post['exam_room'] ) ? (int)$post['exam_room'] : 1, 
				'scheduled_time'=> isset( $post['scheduled_time'] ) ? $post['scheduled_time'] : "2023-06-30T10:00:00", 
				'notes'=> isset( $post['nodes'] ) ? DrCrono::text( $post['notes'] ) : "This is test appointment from web-interface", 
				'color'=> isset( $post['color'] ) ? $post['color'] : "#DEDEDE", 
				'reason'=> isset( $post['reason'] ) ? $post['reason'] : "Counseling"
			); 
			if( $log ){
				echo "create appointment: ". var_export( $params, 1 ) ."\n"; 
			}
			$appointment = $this->exec( "appointments", $params, "POST", $log ); 
			/*
			$result = array (
			  'allow_overlapping' => false,
			  'appt_is_break' => false,
			  'base_recurring_appointment' => NULL,
			  'billing_status' => '',
			  'billing_provider' => NULL,
			  'billing_notes' => 
			  array (),
			  'clinical_note' => NULL,
			  'cloned_from' => NULL,
			  'color' => '#DEDEDE',
			  'created_at' => '2023-06-24T22:31:17',
			  'custom_fields' => 
			  array (),
			  'custom_vitals' => 
			  array (),
			  'deleted_flag' => false,
			  'doctor' => 362213,
			  'duration' => 15,
			  'exam_room' => 1,
			  'first_billed_date' => '2023-06-30T10:00:00',
			  'first_edi_date' => '',
			  'icd9_codes' => 
			  array (),
			  'icd10_codes' => 
			  array (),
			  'id' => '',
			  'ins1_status' => '',
			  'ins2_status' => '',
			  'is_walk_in' => false,
			  'is_virtual_base' => false,
			  'last_billed_date' => '2023-06-30T10:00:00',
			  'last_edi_date' => '',
			  'notes' => 'This is test appointmen from web-interface',
			  'office' => 1,
			  'patient' => 1,
			  'primary_insurer_payer_id' => '',
			  'primary_insurer_name' => '',
			  'primary_insurance_id_number' => '',
			  'profile' => NULL,
			  'reason' => 'Vaccination',
			  'recurring_appointment' => false,
			  'reminders' => 
			  array (),
			  'secondary_insurer_payer_id' => '',
			  'secondary_insurer_name' => '',
			  'secondary_insurance_id_number' => '',
			  'scheduled_time' => '2023-06-30T10:00:00',
			  'status' => '',
			  'status_transitions' => 
			  array (
			    0 => 
			    array (
			      'appointment' => 1,
			      'datetime' => '2023-06-24T22:31:18',
			      'from_status' => '',
			      'to_status' => '',
			    ),
			  ),
			  'supervising_provider' => NULL,
			  'vitals' => 
			  array (
			    'height' => NULL,
			    'weight' => NULL,
			    'bmi' => NULL,
			    'blood_pressure_1' => NULL,
			    'blood_pressure_2' => NULL,
			    'temperature' => NULL,
			    'pulse' => NULL,
			    'respiratory_rate' => NULL,
			    'oxygen_saturation' => NULL,
			    'pain' => NULL,
			    'smoking_status' => 'blank',
			    'head_circumference' => NULL,
			    'head_circumference_units' => 'inches',
			    'height_units' => 'inches',
			    'temperature_units' => 'f',
			    'weight_units' => 'lbs',
			  ),
			  'updated_at' => '2023-06-24T22:31:17',
			  'extended_updated_at' => '2023-06-24T22:31:18',
			  'payment_profile' => 'Cash',
			  'resubmit_claim_original_id' => NULL,
			  'is_telehealth' => false,
			  'telehealth_url' => NULL,
			);
			*/
    		return ( $appointment && isset( $appointment['results'][0] ) ) ? $appointment['results'][0] : $appointment; 
		}
// 
//===================================
		public function CheckDates( $post=array(), $log=false ){
			$params = array(); 
			$date = isset( $post['date'] ) ? $post['date'] : ""; 
			if( $date ){ $params['date'] = $date; }
			$appointments = $this->exec( "appointments", $params, "GET", $log ); 

			if( $appointments && isset( $appointments['results'] ) ){ 
				$return = array(); 
				foreach( $appointments['results'] as $row ){ 
					$nd = explode('T', $row['scheduled_time']); 
					$dt = explode("-", $nd[0]); 
					$tm = explode(":", $nd[1]); 
					$tst = mktime( $tm[0], $tm[1], $tm[2], $dt[1], $dt[2], $dt[0] );
					array_push( $return, date("H:i", $tst) ); 
					$slashes = floor( $row['duration'] / 15 ); 
					for( $i=0; $i<$slashes; $i++ ){
						array_push( $return, date("H:i", $tst+900) ); 
					}
				} 
				return $return; 
			}

			return $appointments;
		}
// 
//===================================
		public function getAppointment( $post=array() ){ 
			$response = array();
    		return $response; 
		} 
// 
//===================================
		public function GetAppointments( $post=array(), $log=false ){ 
			$params = array(); 
			$range = isset( $post['range'] ) ? $post['range'] : ''; // "2023-06-24/2023-06-25"
			if( $range ){ $params['date_range'] = $range; } 
			$since = isset( $post['since'] ) ? $post['since'] : ""; 
			if( $since ){ $params['since'] = $since; }
			$date = isset( $post['date'] ) ? $post['date'] : ""; 
			if( $date ){ $params['date'] = $date; }

			$appointments = $this->exec( "appointments", $params, "GET", $log );
			/*
			$results = array(
                array( 
                    "allow_overlapping"=> true,  
                    "appt_is_break"=> false, 
                    "base_recurring_appointment"=> NULL, 
                    "billing_status"=> "", 
                    "billing_provider"=> NULL,  
                    "billing_notes"=> array(), 
                    "cloned_from"=> NULL, 
                    "color"=> "#E9B96E",  
                    "created_at"=> "2023-06-19T22:09:40", 
                    "deleted_flag"=> false,  
                    "doctor"=> 1, 
                    "duration"=> 75, 
                    "exam_room"=> 1,  
                    "first_billed_date"=> "2023-06-24T10:00:00",  
                    "first_edi_date"=> "",  
                    "icd9_codes"=> array( 
                        "724.4", "739.1", "739.2" 
                    ), 
                    "icd10_codes"=> array(), 
                    "id"=> "257781246",  
                    "ins1_status"=> "",  
                    "ins2_status"=> "",  
                    "is_walk_in"=> false,  
                    "is_virtual_base"=> false, 
                    "last_billed_date"=> "2023-06-24T10:00:00", 
                    "last_edi_date"=> "",  
                    "notes"=> "", 
                    "office"=> 1,  
                    "patient"=> 1, 
                    "primary_insurer_payer_id"=> "",  
                    "primary_insurer_name"=> "",  
                    "primary_insurance_id_number"=> "", 
                    "profile"=> NULL,  
                    "reason"=> "New Patient / Initial Visit", 
                    "recurring_appointment"=> false, 
                    "secondary_insurer_payer_id"=> "",  
                    "secondary_insurer_name"=> "", 
                    "secondary_insurance_id_number"=> "", 
                    "scheduled_time"=> "2023-06-24T10:00:00", 
                    "status"=> "",  
                    "supervising_provider"=> NULL,  
                    "updated_at"=> "2023-06-19T22:09:46", 
                    "payment_profile"=> "Insurance", 
                    "resubmit_claim_original_id"=> NULL, 
                    "is_telehealth"=> false, 
                    "telehealth_url"=> NULL 
                )
            );
			*/
    		return ( $appointments && isset( $appointments['results'] ) ) ? $appointments['results'] : $appointments;
		} 
// 
//===================================
		public function updateAppointment( $post=array() ){ 
			return array(); 
		}
// 
//===================================
		public function deleteAppointment( $post=array() ){ 
			return array(); 
		}
// 
//===================================
		public function GetProviders( $post=array() ){
			return array(); 
		}
// 
//===================================
		public function GetPractices( $post=array() ){
			return array(); 
		}
//
//===================================
		public function GetServiceLocations( $post=array() ){
			return array(); 
		}
// 
//===================================
		public function GetProcedureCodes( $post=array() ){
			return array(); 
		}
//
//===================================
		
//
//===================================

//
//===================================		
	}
//
//
//
