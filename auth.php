<?php 
    define('BASE_DIR', "/var/www/crono/");
    error_reporting( E_ALL | E_STRICT );
    ini_set( 'display_errors', 'On' );
    ini_set("max_execution_time", 30); 
    ini_set('session.gc_maxlifetime', 43200);
    ini_set('session.cookie_lifetime', 0);
    session_set_cookie_params(0);
//  ini_set('session.gc_maxlifetime', 108000);
    ini_set("session.use_cookies", 1 ); // session.use_only_cookies = 1
    ini_set("session.use_trans_sid", "off"); // dont show session id to user
    if( session_id() == '' ){ session_start(); }

    ini_set("file_uploads", 1);
    ini_set("upload_tmp_dir", "/tmp");
    ini_set("upload_max_filesize", "10M");
    ini_set("max_file_uploads", 3);

    header("Access-Control-Allow-Origin:*"); 

    define('REDIRECT_URL', "http://DOMAIN/auth.php" ); 
    define('CLIENT_ID', "CLIENT_ID" ); 
    define('CLIENT_SECRET', "CLIENT_SECRET" ); 
    define('SCOPE', "user:read user:write calendar:read calendar:write patients:read patients:write patients:summary:read patients:summary:write billing:read billing:write clinical:read clinical:write labs:read labs:write messages:read messages:write settings:read settings:write tasks:read tasks:write" ); 
    define('SCOPE_LITE', "calendar:read calendar:write patients:read patients:write clinical:read clinical:write"); 
    define('AUTH_URL', "https://drchrono.com/o/authorize/");
    define('TOKEN_URL', "https://drchrono.com/o/token/"); 
    define('REVOKE_URL', "https://drchrono.com/o/revoke_token/");

    function curl_query( $url="", $params=array() ){ 
        $headers = array( 
            "Content-type: application/x-www-form-urlencoded" 
        ); 
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_VERBOSE, true );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );  
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers ); 
        curl_setopt( $ch, CURLOPT_POST, 1 ) ;
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params ) ); 
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
        $Result = curl_exec($ch);  
        $info = curl_getinfo( $ch ); 
        curl_close( $ch );  
        $json = json_decode( $Result, 1, 1024 ); 
        return $json; 
    } 
    if( isset( $argv[1] ) && $argv[1] == "refresh" ){ 
        $filename = BASE_DIR ."token.json";
        $fp = @fopen( $filename, "r" ); 
        $token = array(); 
        if( $fp ){
            $token = @fread( $fp, filesize($filename) ); 
            $token = json_decode( $token, 1, 1024 ); 
            if( isset( $token['access_token'] ) ){ $_SESSION['token'] = $token['access_token']; }
            if( isset( $token['refresh_token'] ) ){ $_SESSION['refresh'] = $token['refresh_token']; } 
            @fclose( $fp ); 
        }
        echo "token info: ". var_export( $token, 1 ) ."\n"; 
        $params = array( 
            'client_id'=> CLIENT_ID, 
            'client_secret'=> CLIENT_SECRET,
            'token'=> $_SESSION['token'] 
        );
        echo "request ". var_export( $params, 1 ) ."\n"; 
        $token_data = curl_query(
            REVOKE_URL, 
            $params 
        ); 
        echo "token_data: ". var_export( $token_data ) ."\n";
        if( isset( $token_data['access_token'] ) ){
            $fp = @fopen( BASE_DIR ."token.json", "w");
            if( $fp ){
                @fwrite( $fp, json_encode( $token_data ) ); 
                @fclose( $fp ); 
            } 
            $_SESSION['token'] = $token_data['access_token']; 
            $_SESSION['refresh'] = $token_data['refresh_token']; 
            $fp = @fopen( BASE_DIR ."token.json", "w");
            if( $fp ){
                @fwrite( $fp, json_encode( $token_data ) ); 
                @fclose( $fp ); 
            } 
        } 
        else { 
            echo "some error: ". var_export( $token_data, 1 ) ."\n";
        }
    } 
    else { 
            $authURL = AUTH_URL .'?redirect_uri='. urlencode( REDIRECT_URL ) .'&response_type=code&client_id='. CLIENT_ID .'&scope='. urlencode( SCOPE );
            if( !isset( $_GET['code'] ) ){ 
              header('Location: '. $authURL); 
            }
            else { 
                $token_data = curl_query( 
                    TOKEN_URL, 
                    array(
                        'code'=> $_GET['code'], 
                        'redirect_uri'=> REDIRECT_URL, 
                        'grant_type'=> "authorization_code", 
                        'client_id'=> CLIENT_ID, 
                        'client_secret'=> CLIENT_SECRET,
                        'scope'=> SCOPE
                    ) 
                );
                if( isset( $token_data['access_token'] ) ){
                    $fp = @fopen( BASE_DIR ."token.json", "w");
                    if( $fp ){
                        @fwrite( $fp, json_encode( $token_data ) ); 
                        @fclose( $fp ); 
                    } 
                    $_SESSION['token'] = $token_data['access_token']; 
                    $_SESSION['refresh'] = $token_data['refresh_token'];
                } 
                else {
                    var_dump( $token_data );
                }
            } 
    }
    define('TOKEN', isset( $_SESSION['token'] ) ? $_SESSION['token'] : "" ); 
    var_dump( $_SESSION );
















