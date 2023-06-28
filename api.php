<?php 
	define('BASE_DIR', "/var/www/crono/");
    error_reporting( E_ALL | E_STRICT );
    ini_set( 'display_errors', 'On' );
    ini_set("max_execution_time", 30); 
    ini_set('session.gc_maxlifetime', 43200);
    ini_set('session.cookie_lifetime', 0);
    session_set_cookie_params(0); 
    ini_set("session.use_cookies", 1 ); 
    ini_set("session.use_trans_sid", "off");  
    if( session_id() == '' ){ session_start(); }

    ini_set("file_uploads", 1);
    ini_set("upload_tmp_dir", "/tmp");
    ini_set("upload_max_filesize", "10M");
    ini_set("max_file_uploads", 3);

    header("Access-Control-Allow-Origin:*"); 

    if( !isset( $_SESSION['token'] ) ){ 
        $filename = BASE_DIR ."token.json";
        $fp = @fopen( $filename, "r" ); 
        if( $fp ){
            $token = @fread( $fp, filesize($filename) ); 
            $token = json_decode( $token, 1, 1024 ); 
            var_dump( $token );
            if( isset( $token['access_token'] ) ){ $_SESSION['token'] = $token['access_token']; }
            if( isset( $token['refresh_token'] ) ){ $_SESSION['refresh'] = $token['refresh_token']; }
        }
    } 

    define('TOKEN', $_SESSION['token'] ? $_SESSION['token'] : "" ); 
    define('REFRESH', $_SESSION['refresh'] ? $_SESSION['refresh'] : "" ); 

    //echo "SESSION: ". var_export( $_SESSION, 1 ) ."\n"; 
    
    require_once BASE_DIR ."DrCrono.class.php";
    $Kareo = new DrCrono(); 

    $post = $_REQUEST; 
	$input = json_decode( file_get_contents('php://input'), 1, 1024 ); 
	$return = array('error'=>1, 'msg'=>"UNAUTHORIZED"); 

	define('CONTROLLER', ( isset( $post['controller'] ) ? preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $post['controller'] ) : "" ) ); 
	define('ACTION', ( isset( $post['action'] ) ? preg_replace('/[^A-Za-z0-9\-\_\.]/', '', $post['action'] ) : "" ) ); 

	switch( CONTROLLER ){
		case "patients": 
			switch( ACTION ){
				case "create": 
					$ex = $Kareo->GetPatient( $post ); 
					if( !$ex ){ 
						$patient = $Kareo->CreatePatient( $post ); 
						$return = $patient && isset( $patient['id'] ) ? 
								array( 'success'=>$patient['id'], 'data'=>$patient ) : 
								array( 'error'=>1, 'data'=>$patient, 'post'=>$post ); 
					} 
					else { 
						$return = array( 'success'=>$ex['id'], 'data'=>$ex ); 
					}
					break; 
			}
			break; 
		case "appointments": 
			switch( ACTION ){ 
				case "check": 
					$date = isset( $post['date'] ) ? explode("T", $post['date']) : date("Y-m-d"); 
					$appointments = $Kareo->CheckDates( array('date'=> $date[0] ) ); 
					$return = array('success'=>1, 'data'=>$appointments);
					break; 
				case "create": 
					$appointment = $Kareo->CreateAppointment( $post );
					$return = $appointment && isset( $appointment['id'] ) ? 
							array( 'success'=>$appointment['id'], 'data'=>$appointment ) : 
							array( 'error'=>1, 'data'=>$appointment, 'post'=>$post ); 
					break;  
			}
			break; 
	}

	echo json_encode( $return );
	exit(); 



