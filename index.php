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

    date_default_timezone_set('Europe/Moscow');
    //header("Content-Type: text/html; charset=utf-8");
    header("Access-Control-Allow-Origin:*");
    //header("Access-Control-Allow-Credentials=true"); 

    if( !isset( $_SESSION['token'] ) ){ 
        $filename = BASE_DIR ."token.json";
        $fp = @fopen( $filename, "r" ); 
        if( $fp ){
            $token = @fread( $fp, filesize($filename) ); 
            $token = json_decode( $token, 1, 1024 ); 
            //var_dump( $token );
            if( isset( $token['access_token'] ) ){ $_SESSION['token'] = $token['access_token']; }
            if( isset( $token['refresh_token'] ) ){ $_SESSION['refresh'] = $token['refresh_token']; }
        }
    }

    define('TOKEN', $_SESSION['token'] ? $_SESSION['token'] : "" ); 
    define('REFRESH', $_SESSION['refresh'] ? $_SESSION['refresh'] : "" ); 
    
    require_once BASE_DIR ."DrCrono.class.php";
    $Kareo = new DrCrono(); 

    //echo "get offices\n"; 
    $offices = $Kareo->GetOffices( array() ); 
    //echo "result: ". var_export( $offices, 1 ) ."\n"; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title></title> 
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" /> 
    <link rel="shortcut icon" href="img/favicon.png" type="image/png" />
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <meta name="keywords" content="" />
    <meta name="description" content="" /> 
    <meta name="author" content="All Women" />  
    <link rel="stylesheet" href="/reset.css?">   
    <script type="text/javascript" src="/jquery.js?"></script> 
    <style type="text/css"> 
        /*--- reset ---*/
        html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, input, textarea{ margin:0; padding:0; border:0; font-weight:normal; vertical-align:top; background:transparent; outline:none; }
        html{ padding:0px; margin:0px auto; width:100%; height:100%; text-align:center; vertical-align:top; font-size:14px; font-weight:200; line-height:1.25em; font-family: Helvetica,Arial,Sans-Serif; background:#fff; color:#000; box-sizing: border-box; }
        body{ padding:0px; margin:0px auto; min-width:100%; height:100%; text-align:center; vertical-align:top; font-size:14px; font-weight:200; line-height:1.25em; font-family: Helvetica,Arial,Sans-Serif; background:#fff; color:#000; box-sizing: border-box; overflow-y:auto; overflow-x:hidden; }
        table{ display:table; padding:0px; margin:0px auto; border-spacing:0px; border-collapse:collapse; width:100%; height:100%; border:solid 0px #000; }
        td{ padding:0px; margin:0px; border:solid 0px #000; vertical-align:middle; text-align:left; }
        img{ height:auto; margin:0px auto; vertical-align:top; text-decoration:none; border:solid 0px #000; zoom:1; }
        div{ display:block; padding:0px; margin:0px; vertical-align:top; text-align:left; box-sizing:border-box; }
        a{ font:normal 14px/1.5em Helvetica,Arial,Sans-Serif; color:#fff; text-decoration:underline; vertical-align:baseline; cursor:pointer; }
        a:link{ color:#aaa; }
        a:visited{ color:#aaa; }
        a:hover{ color:#666; text-decoration:none; }
        a:focus{ color:color:#666; }
        a:active{ color:#333; text-decoration:none; }
        b{ font-weight:bold; }
        i{ font-style:italic; }
        blockquote, 
        q{ quotes:none; }
        ol, 
        ul, 
        li{ list-style:none; /*display:inline-block;*/ }
        div.input{ }
        input[type="text"], input[type="password"], input[type="email"], input[type="date"], textarea{ display:block; width:100%; outline:none; resize:none; padding:10px 10px; background:transparent; color:#333; font-size:14px; text-align:left; box-sizing:border-box; border:solid 1px rgb(34, 36, 39)/*#070c14*/; min-height:40px; border-radius:6px; }
        input[type="text"]:focus{ outline:none; }
        input[type="password"]:focus{ outline:none; }
        select:focus{ outline:none; } 
        select{ outline:none; display:block; width:100%; height:30px; border: 1px solid rgb(223, 224, 226); background:#fff; border-radius:3px; }
        option:focus{ outline:none; }
        input[type="button"]{ border:solid 0px #000; cursor:pointer; padding-top:0px !important; outline:none !important; }
        input[type="submit"]{ border:solid 0px #000; outline:none !important;  cursor:pointer; }
        input[type="radio"]{ background-color:#fff; border:solid 0px #000; margin:0px 5px 0px 0px; padding:0px; }
        input[type="checkbox"]{ background-color:#fff; border:solid 0px #000; margin:0px; padding:0px; }
        textarea{ outline:none; resize:none; }
        span{ vertical-align:baseline; }
        button, 
        button:hover, 
        button:active{ outline:none !important; }
        label{ cursor:pointer; color:#333; font-size:14px; } 

        #root{ display:block; text-align:center; width:100%; height:100%; padding:0px; margin:0px; position:relative; background:#dedede; } 
        #calendar_wrapper{ display:flex; flex-flow:row nowrap; align-items:center; justify-content:center; width:100%; height:100%; padding:0px; margin:0px; }
        
        *[data-bounce="bounce"]{ transition-duration: .5s; } 

        #calendar{ width:700px; height:auto; padding:0px; margin:0px; background:#fff; }
        #calendar table{ display:table; width:100%; padding:0px; margin:0px auto; border:solid 1px #ccc; border-collapse:collapse; }
        #calendar table td{ text-align:center; vertical-align:top; width:100px; height:100px; border:solid 1px #ccc; padding:0px; cursor:default; background:#eee; }
        #calendar table th{ text-align:center; vertical-align:middle; width:100px; height:50px; border:0; border-bottom:solid 1px #ccc; padding:0px; font-weight:bold; font-size:14px; color:#777; cursor:default; } 
        #calendar .active{ cursor:pointer; background:#fff; }
        #calendar h3{ text-align:right; font-size:16px; color:#999; padding:5px; margin:0px 0px 10px 0px; cursor:default; }
        #calendar .active h3{ color:#333; cursor:pointer; } 
        #calendar .label{ padding:2px; margin:0px 5px 5px 5px; color:#fff; background:#999; text-align:center; font-size:10px; cursor:default; } 
        #date_selector{ display:flex; flex-flow:row nowrap; align-items:center; justify-content:space-between; }
        #date_selector button{ width:15%; height:50px; padding:0px 0px; background:transparent; color:#777; font-size:16px; margin:0; border:0; border-bottom:solid 5px transparent; border-bottom:solid 5px transparent; cursor:pointer; }
        #date_selector button.active, 
        #date_selector button:hover{ color:#000; border-color:#000; }

        #appointment_wrapper{ display:none; width:700px; height:auto; padding:0px; margin:0px; background:#fff; position:relative; }
        #appointment_wrapper h2{ height:50px; display:flex; flex-flow:row nowrap; align-items:center; justify-content:start; padding:0px 15px; margin:0px; border-bottom:solid 1px #ccc; color:#333; font-size:18px; } 
        #appointment_wrapper .wrapper{ padding:15px; }
        #appointment_wrapper .wrapper button{ height:30px; padding:0px 20px; background:#dedede; border:0; cursor:pointer; margin:0px; color:#333; font-size:14px; }
        #appointment_wrapper .wrapper button:hover{ border-radius:10px; }
        #appointment_wrapper .subwrapper{ display:flex; flex-flow:row nowrap; align-items:center; padding:0px; margin:0px 0px 15px 0px; }
        #appointment_wrapper .subwrapper .left{ width:30%; flex-grow:0; flex-shrink:0; color:#999; font-size:14px; }
        #appointment_wrapper .subwrapper .right{ width:70%; flex-grow:1; flex-shrink:1; } 
        #appointment_wrapper .subwrapper input[type="text"]{ height:30px; border:solid 1px #ccc; }
        #appointment_wrapper .subwrapper textarea{ border:solid 1px #ccc; }
        #appointment_wrapper .buttons{ display:flex; flex-flow:row nowrap; align-items:center; justify-content:start; padding:15px; margin:0px; border-top:solid 1px #ccc; color:#333; font-size:18px; } 
        #appointment_wrapper .buttons button{ height:35px; background:#dedede; color:#000; font-size:16px; border:0; cursor:pointer; padding:0px 20px; margin:0px 10px 0px 0px; }
        #appointment_wrapper .buttons button:hover{ border-radius:10px; }

        #patient_wrapper{ display:none; width:700px; padding:0px; margin:0px; background:#fff; position:relative; } 
        #patient_wrapper h2{ height:50px; display:flex; flex-flow:row nowrap; align-items:center; justify-content:start; padding:0px 15px; margin:0px; border-bottom:solid 1px #ccc; color:#333; font-size:18px; } 
        #patient_wrapper .buttons{ display:flex; flex-flow:row nowrap; align-items:center; justify-content:start; padding:15px; margin:0px; border-top:solid 1px #ccc; color:#333; font-size:18px; } 
        #patient_wrapper .buttons button{ height:35px; background:#dedede; color:#000; font-size:16px; border:0; cursor:pointer; padding:0px 20px; margin:0px 10px 0px 0px; }
        #patient_wrapper .buttons button:hover{ border-radius:10px; }
        #patient_wrapper .wrapper{ padding:15px; }
        #patient_wrapper .subwrapper{ display:flex; flex-flow:row nowrap; align-items:center; padding:0px; margin:0px 0px 15px 0px; }
        #patient_wrapper .subwrapper .left{ width:30%; flex-grow:0; flex-shrink:0; color:#999; font-size:14px; }
        #patient_wrapper .subwrapper .right{ width:70%; flex-grow:1; flex-shrink:1; } 
        
        input[type="text"], 
        input[type="date"], 
        input[type="email"], 
        input[type="phone"]{ height:30px; border:solid 1px #ccc; } 
        #notify{ display:none; position:fixed; top:0; left:0; right:0; z-index:999; padding:25px; text-align:left; color:#000; font-size:16px; line-height:1.5em; background:#fff; cursor:pointer; }
        #notify.error{ color:red; }
        .overlay{ display:none; position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.5); }
        .disabled .overlay{ display:block; }
    </style> 
</head>
<body data-token="<?= TOKEN; ?>">
    <div id="root"> 
        <div id="calendar_wrapper">
            <div id="calendar">
                <div id="date_selector"> </div>
                <table> <tr> <th>Mon</th> <th>Tue</th> <th>Wed</th> <th>Thu</th> <th>Fri</th> <th>Sat</th> <th>Sun</th> </tr> </table>
            </div>

            <div id="appointment_wrapper">
                <h2>New Appointment</h2>
                <div class="wrapper">
                    <div class="subwrapper">
                        <div class="left">
                            <button id="set_patient" data-bounce="bounce">Add Patient</button> 
                            <span id="patient_label" style="display:none;">Patient</span>
                        </div> 
                        <div class="right"><input type="text" value="" id="appointment_patient" readonly="" /></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Visit Reason</div>
                        <div class="right">
                            <select id="appointment_reason">
                                <option disabled="" value="0">- Select a Visit Reason -</option>
                                <option value="Counseling">Counseling</option>
                                <option value="f/u HM">f/u HM</option>
                                <option value="Office Visit">Office Visit</option>
                                <option value="Other">Other</option>
                                <option value="Pap">Pap</option>
                                <option value="Paperwork exam">Paperwork exam</option>
                                <option value="Paperwork only">Paperwork only</option>
                                <option value="Results review">Results review</option>
                                <option value="Sick Visit">Sick Visit</option>
                                <option value="Sick Visit f/u">Sick Visit f/u</option>
                                <option value="Testing">Testing</option>
                                <option value="Travel visit">Travel visit</option>
                                <option value="Vaccination">Vaccination</option>
                                <option value="Well-Child">Well-Child</option>
                                <option value="Well-Exam">Well-Exam</option>
                            </select>
                        </div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Start</div>
                        <div class="right"><input type="date" required="" readonly="" id="appointment_start_date" value="" /></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Time</div>
                        <div class="right">
                            <select required="" id="appointment_time"> </select>
                        </div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Duration</div>
                        <div class="right" style="display: flex; align-items: center;">
                            <input required="" style="margin-right: 0.5rem; margin-bottom: 0px;" id="appointment_duration" type="number" min="15" step="15" max="120" value="15"> mins
                        </div>
                    </div>
                    <div class="subwrapper"> 
                        <div class="left">Periodic</div>
                        <div class="right">
                            <select required="" id="appointment_periodic"><option value="does-not-repeat">Does not repeat</option></select>
                        </div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Appointment Mode</div>
                        <div class="right">
                            <label style="margin-right:15px;"><input type="radio" name="appointmentMode" value="In-Office" checked=""> In Office</label>
                            <label><input type="radio" name="appointmentMode" value="Telehealth">Telehealth</label>
                        </div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Service Location</div>
                        <div class="right">
                            <select required="" id="appointment_location">
                                <option disabled="" value="">- Exam room -</option> 
                                <?php 
                                    if( $offices && isset( $offices[0]['exam_rooms'] ) ){
                                        foreach( $offices[0]['exam_rooms'] as $room ){
                                            echo '<option value="'. $room['index'] .'">'. $offices[0]['name'] ." ". $room['name'] .'</option>'; 
                                        }
                                    }
                                ?> 
                            </select> 
                        </div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Comment</div>
                        <div class="right"><textarea placeholder="Add in a chief complaint or any note related to this appointment..." id="appointment_comment"></textarea></div>
                    </div>
                </div>
                <div class="buttons">
                    <button data-bounce="bounce" id="confirm_appointment">Confirm</button>
                    <button data-bounce="bounce" class="cancel_form">Cancel</button>
                </div>
                <div class="overlay"></div> 
            </div> 

            <div id="patient_wrapper">
                <h2>New Patient</h2>
                <div class="wrapper">
                    <div class="subwrapper">
                        <div class="left">First Name (Required)</div>
                        <div class="right"><input type="text" required="" value="" id="patient_firstname" /></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Last Name (Required)</div>
                        <div class="right"><input type="text" required="" value="" id="patient_lastname" ></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Email (Required)</div>
                        <div class="right"><input type="email" required="" value="" id="patient_email" ></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">DOB (Required)</div>
                        <div class="right"><input required="" placeholder="mm/dd/yyyy" type="date" id="patient_birth" /></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Sex (Required)</div>
                        <div class="right">
                            <select required="" id="patient_sex">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option> 
                            </select>
                        </div> 
                    </div>
                    <div class="subwrapper">
                        <div class="left">Mobile Phone</div>
                        <div class="right"><input placeholder="(___)___-____" required="required" value="" id="patient_phone" type="phone" maxlength="13" /></div>
                    </div>
                    <div class="subwrapper">
                        <div class="left">Home Phone</div>
                        <div class="right"><input placeholder="(___)___-____" required="required" value="" id="patient_hphone" type="phone" maxlength="13" /></div>
                    </div>
                </div>
                <div class="buttons">
                    <button data-bounce="bounce" id="confirm_patient">Confirm</button>
                    <button data-bounce="bounce" class="cancel_form">Cancel</button> 
                </div>
                <div class="overlay"></div>
            </div> 

        </div>
    </div>

    <div id="notify" onclick="$(this).hide().html('').removeClass('active');"></div>

<script>
    function Sheduler(){ 
        return {
            api_url: "", 
            start_hour: 8, 
            start_mins: 0, 
            end_hour: 18, 
            end_mins: 0, 
            init: function(){ 
                var $this=this; 
                console.log('init sheduler'); 
                $this.draw_buttons(); 
                $this.draw_month(); 
                $this.bind(); 
            }, 
            notify: function( $text, $error ){
                var $this=this; 
                $('#notify').html($text).show(); 
                if( $error ){ $('#notify').addClass('error'); } 
                setTimeout( function(){
                    $('#notify').fadeOut(500, function(){
                        $(this).html("").removeClass('error');
                    });
                }, 5000);
            },
            bind: function(){
                $this=this;
                console.log('bind events'); 
                $('#date_selector button').off().on('click', function(){
                    var $self=$(this); 
                    if( !$self.hasClass('active') ){
                        var $month = +$self.attr('data-month'); 
                        var $year = +$self.attr('data-year'); 
                        var $day = +$self.attr('data-day');
                        $('#date_selector button').removeClass('active'); 
                        $self.addClass('active'); 
                        $this.draw_month( $year, $month ); 
                    }
                }); 
                $('#calendar table td.active').off().on('click', function(){
                    var $self=$(this); 
                    var $year = +$self.attr('data-year'); 
                    var $month = +$self.attr('data-month')+1; 
                    var $day = +$self.attr('data-day'); 
                    var $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; 
                    var $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']; 
                    var $date = ""+ $year +"-"+ ( $month < 10 ? "0"+$month : $month ) +"-"+ ( $day < 10 ? "0"+$day : $day ); 
                    console.log("create appointment for date: "+ $date );
                    var $app_date = document.querySelector('#appointment_start_date'); 
                    if( $app_date ){ $app_date.value = $date; }
                    $this.check_date( $date );
                    var $day_name = $days[ new Date( $year, $month, $day ).getDay() ];
                    var $periodic = '<option value="does-not-repeat">Does not repeat</option>'+
                                    '<option value="weekly">Weekly on '+ $day_name +'</option>'+
                                    '<option value="biweekly">Every other week on '+ $day_name +'</option>'+
                                    '<option value="monthly">Monthly on 2nd '+ $day_name +'</option>'+ 
                                    '<option value="annually">Annually on 2nd '+ $day_name +' of '+ ( $months[ $month ] ) +'</option>'+
                                    '<option value="new-custom">Custom</option>';
                    $('#appointment_periodic').html( $periodic ); 
                    $('#calendar').hide(); 
                    $('#appointment_wrapper').show(); 
                    $('#appointment_wrapper .buttons').show();
                }); 
                $('#set_patient').off().on('click', function(){ 
                    var $self = $(this); 
                    if( !$self.attr('disabled') ){ 
                        $('#calendar').hide(); 
                        $('#appointment_wrapper').hide(); 
                        $('#patient_wrapper').show(); 
                    }
                }); 
                $('#patient_firstname').off().on('change', function(){
                    var $self = $(this); 
                    var $fn = $self.val(); 
                    var $ln =  $('#patient_lastname').val(); 
                    $('#appointment_patient').val( $fn +" "+ $ln );
                });
                $('#patient_lastname').off().on('change', function(){
                    var $self = $(this); 
                    var $fn = $('#patient_firstname').val(); 
                    var $ln = $self.val(); 
                    $('#appointment_patient').val( $fn +" "+ $ln );
                }); 
                $('input[type="phone"]').off().on('keyup', function(){
                    var $self=$(this); 
                    var $text=$self.val(); 
                    $text = $text.replace(/[^0-9]/gi, '');
                    $text = $text.split(''); 
                    var $new_line = ''; 
                    for( var $i=0; $i<$text.length; $i++ ){ 
                        $new_line += ( !$i ? "(" : "" ) + $text[$i] +( $i == 2 ? ")" : "" )+ ( $i == 5 ? "-" : "" ); 
                    } 
                    $self.val( $new_line );
                }); 
                $('#confirm_patient').off().on('click', function(){
                    $this.confirm_patient();
                });
                $('#confirm_appointment').off().on('click', function(){
                    if( $('#set_patient').attr('data-id') ){
                        $this.confirm_appointment();
                    } 
                    else {
                        $this.notify("Not enough params", 1); 
                    }
                }); 
                $('.cancel_form').off().on('click', function(){ 
                    $this.cancel(); 
                });
            }, 
            days: function( $y, $m ){
                return new Date( $y, $m, 0 ).getDate();     // month from 1
            }, 
            first_day: function( $y, $m ){                  // month from zero
                var $a = new Date( $y, $m, 1).getDay();       // 0-sun, 1-mon, etc 
                return $a ? $a : 7;
            }, 
            draw_month: function( $y, $m ){
                var $this=this; 
                var $year = $y ? $y : new Date().getFullYear(); 
                var $month = $m ? $m : new Date().getMonth(); 
                console.log("draw month "+ $month +" for year "+ $year); 

                var $wrap = $('#calendar table'); 
                $('td', $wrap).remove(); 
                
                var $cur_day = new Date( new Date().getFullYear(), new Date().getMonth(), new Date().getDate() ).getTime(); 
                var $first_day = $this.first_day( $year, $month ); 
                var $num_days = $this.days( $year, +$month+1 );
                console.log("first day of month "+ $first_day +"; total days in month "+ $num_days +"; month length: "+ $num_days);
                var $first_date = new Date( $year, $month, 1 ).getTime(); 
                
                var $done = false; 
                var $iteration = 1; 
                var $cur_date = 0;
                while( !$done ){ 
                    var $tmps = '<tr>';
                    for( var $i=1; $i<8; $i++ ){ 
                        if( !$cur_date && $i == $first_day ){ $cur_date = 1; }
                        var $past = $cur_date ? ( new Date( $year, $month, $cur_date ).getTime() < $cur_day ) : true; 
                        $tmps += '<td class="'+ ( !$past && $cur_date <= $num_days ? 'active' : '' ) +'" data-year="'+ $year +'" data-month="'+ $month +'" data-day="'+ $cur_date +'">';
                        if( $cur_date &&  $cur_date <= $num_days ){ 
                            $tmps += '<h3>'+ $cur_date +'</h3>'; 
                            $cur_date += 1; 
                            //if( !$past ){ $tmps += '<div class="label" title="unavailable">7:00 AM</div>'; }
                        }
                        $tmps += '</td>'; 
                    } 
                    $tmps += '</tr>';
                    $wrap.append( $tmps );
                    $iteration += 1; 
                    if( $iteration >= 10 ){ $done = true; } 
                    if( $cur_date > $num_days ){ $done = true; }
                }
                $('#date_selector button').removeClass('active'); 
                $('#date_selector button[data-month="'+ $month +'"]').addClass('active'); 
                $this.bind(); 
            }, 
            draw_buttons: function( $y, $m ){
                var $this=this; 
                console.log("draw month selector");
                var $year = $y ? $y : new Date().getFullYear(); 
                var $month = $m ? $m : new Date().getMonth(); 
                //$this.draw_month( $year, $month );
                var $wrap = $('#date_selector'); 
                $wrap.html("");
                var $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                for( var $i=0; $i<6; $i++ ){ 
                    var $tmps = '<button data-year="'+ $year +'" data-month="'+ $month +'" class="'+ ( !$i ? 'active' : '' ) +'">'+ $months[ $month ] +' '+ $year +'</button>'; 
                    $month += 1; $month = $month > 11 ? 0 : $month; 
                    $year = $month ? $year : $year +1; 
                    $wrap.append( $tmps );
                } 
                $this.bind(); 
            }, 
            draw_time: function( $ex ){
                var $this=this; 
                $('#appointment_time').html(); 
                var $bizy = $ex ? $ex : []; 
                var $done = false;  
                var $h = $this.start_hour; 
                var $m = $this.start_mins;
                while( !$done ){
                    var $time = ( $h < 10 ? "0"+$h : $h ) +":"+ ( $m < 10 ? "0"+$m : $m ); 
                    var $disabled = false; 
                    if( $bizy && $bizy.length ){
                        for( var $i=0; $i<$bizy.length; $i++ ){ 
                            if( $bizy[$i] == $time ){ $disabled = true; } 
                        } 
                    }
                    var $tmps = '<option value="'+ $time +'" '+ ( $disabled ? 'disabled="disabled"' : '' ) +'>'+ $time +'</option>'; 
                    $('#appointment_time').append( $tmps ); 
                    $m += 15; if( $m >= 60 ){ $m = 0; $h += 1; } 
                    if( $h >= $this.end_hour && $m >= $this.end_mins ){ $done = true; }
                }
            },
            check_date: function( $date ){
                var $this=this; 
                console.log("check date: "+ $date); 
                $('#appointment_wrapper').addClass('disabled');
                $.ajax({
                    url: "/api.php", type:"post", method:"post", data:{ controller: "appointments", action: "check", date: $date }, 
                    success: function($r){ 
                        var $data = JSON.parse( $r );
                        console.log( $data ); 
                        if( $data && $data.data ){ 
                            $this.draw_time( $data.data );
                        }
                        $('#appointment_wrapper').removeClass('disabled');
                    }, 
                    error: function( $r ){ 
                        console.error( $r ); 
                        $('#appointment_wrapper').removeClass('disabled'); 
                        $this.notify("Error captured", 1);
                    }
                });
            },
            confirm_appointment: function(){
                var $this=this; 
                var $obj = { 
                    controller: "appointments", 
                    action: "create", 
                    doctor: 362213, 
                    patient: $('#set_patient').attr('data-id'),   // API result
                    reason: $('#appointment_reason').val(), 
                    scheduled_time: $('#appointment_start_date').val() +"T"+ $('#appointment_time').val(), 
                    duration: $('#appointment_duration').val(), 
                    mode: $('input[name="appointmentMode"]:checked').val(), 
                    office: 385283, 
                    exam_room: $('#appointment_location').val(), 
                    notes: $('#appointment_comment').val(), 
                    color: '#dedede' 
                } 
                console.log("confirm appointment: ", $obj); 
                $('#appointment_wrapper').addClass('disabled'); 
                $.ajax({
                    url: "/api.php", type:"post", method:"post", data: $obj, 
                    success: function($r){ 
                        var $data = JSON.parse( $r );
                        console.log( $data ); 
                        if( $data && $data.success ){ 
                            $('#appointment_wrapper .buttons').hide();
                            $this.notify("Appointment "+ $data.success +" successfully created");
                        }
                        $('#appointment_wrapper').removeClass('disabled');
                    }, 
                    error: function( $r ){ 
                        console.error( $r ); 
                        $('#appointment_wrapper').removeClass('disabled'); 
                        $this.notify("Error captured", 1);
                    }
                });
            }, 
            confirm_patient: function(){
                var $this=this; 
                var $obj = { 
                    controller: "patients", 
                    action: "create", 
                    doctor: 362213, 
                    first_name: $('#patient_firstname').val(), 
                    last_name: $('#patient_lastname').val(), 
                    email: $('#patient_email').val(), 
                    date_of_birth: $('#patient_birth').val(), 
                    gender: $('#patient_sex').val(), 
                    cell_phone: $('#patient_phone').val(), 
                    home_phone: $('#patient_hphone').val() 
                } 
                $('#patient_wrapper').addClass('disabled');
                console.log("confirm patient: ", $obj); 
                $.ajax({
                    url: "/api.php", type:"post", method:"post", data: $obj, 
                    success: function($r){ 
                        var $data = JSON.parse( $r );
                        console.log( $data ); 
                        if( $data && $data.data ){ 
                            if( $data.data.id ){ 
                                $('#set_patient').attr('data-id', $data.data.id ); 
                            }
                            var $patient = ""; 
                            $('#appointment_patient').val(''); 
                            if( $data.data.first_name ){ $patient += $data.data.first_name; }
                            if( $data.data.last_name ){ $patient += ( $patient ? " " : '' ) + $data.data.last_name; } 
                            $('#appointment_patient').val( $patient );
                            $('#set_patient').hide(); 
                            $('#patient_label').show();
                            $('#patient_wrapper').hide(); 
                            $('#appointment_wrapper').show(); 
                        }
                        $('#patient_wrapper').removeClass('disabled');
                    }, 
                    error: function( $r ){ 
                        console.error( $r ); 
                        $('#patient_wrapper').removeClass('disabled'); 
                        $this.notify("Error captured", 1);
                    }
                });
            },
            cancel: function(){
                var $this=this; 
                $('#calendar').show(); 
                $('#appointment_wrapper').hide(); 
                $('#patient_wrapper').hide(); 
            }
        }
    }

    $(document).ready(function(){
        window.$sheduler = new Sheduler(); 
        $sheduler.init(); 
        <?php if( !isset( $offices[0]['exam_rooms'] ) ){ ?>
            $sheduler.notify("API unavailable. Try to refresh token.", 1);
        <?php } ?>
    });
</script>

</body>
</html>
