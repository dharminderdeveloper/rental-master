<?php
class CF_AR_Email{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	
	public function __construct(){
		add_action('wp_ajax_nopriv_application_form_ajax',array($this,'application_form_ajax'));
		add_action('wp_ajax_application_form_ajax',array($this,'application_form_ajax'));
	}
	
	public function application_form_ajax(){
		if($_REQUEST){
			$act	=	$_REQUEST['act'];
			/*if($act=='applynow'){
				$dob   	= trim($_REQUEST['dob']);
				$comm     = trim($_REQUEST['comm']);
				$fname		= trim($_REQUEST['fname']);
				$lname 	= trim($_REQUEST['lname']);
				$email    = trim($_REQUEST['email']);
				$rname    = trim($_REQUEST['rname']);
				$pphone   = trim($_REQUEST['pphone']);
				$rphone   = trim($_REQUEST['rphone']);
				$remail   = trim($_REQUEST['remail']);
				$cworpho 	= trim($_REQUEST['cworpho']);
				$paddres  = trim($_REQUEST['paddres']);
				$caddres	= trim($_REQUEST['caddres']);
				$chomphon	= trim($_REQUEST['chomphon']);
				$montbegn = trim($_REQUEST['montbegn']);
				$laplifor = trim($_REQUEST['laplifor']);
				$bedromned= trim($_REQUEST['bedromned']);
				$from     = $fname." ".$lname;
				$comm_str = implode(",",$comm);
				$to 		= get_option("admin_email");
				$headers 	= array(
								"Reply-To: ".$from."  <".$email." >",
								"From: ".$from."<myname@example.com>"
							);
				$msg	=	CF_AR_Email::abr_email_template_1($fname,$lname,$dob,$caddres,$chomphon,$cworpho,$email,$rname,$rphone,$remail,$laplifor,$comm_str,$montbegn,$bedromned,$paddres,$pphone);
				CF_AR_Email::abr_send_email($to,$from,'Application Form',$msg,$headers);
				$response=array(
					'response'	=>1,
					'message'		=>'Email sent successfully',
				);
			}*/
			if($act=='favemail'){
				$sname 		=	$_POST['sname'];
				$sname		=	$sname?sanitize_text_field($sname):$sname='';
				$semail		=	$_POST['semail'];
				$semail		=	$semail?sanitize_email($semail):$semail='';
				$message 	=	$_POST['message'];
				$message	=	$message?$message:$message='';
				$remail1  	= 	$_POST['remail1'];
				$remail1	=	$remail1?sanitize_email($remail1):$remail1='';
				$remail2  	= 	$_POST['remail2'];
				$remail2	=	$remail2?sanitize_email($remail2):$remail2='';
				$remail3  	= 	$_POST['remail3'];
				$remail3	=	$remail3?sanitize_email($remail3):$remail3='';
				$remail4  	=	 $_POST['remail4'];
				$remail4	=	$remail4?sanitize_email($remail4):$remail4='';
				$remail5  	= 	$_POST['remail5'];
				$remail5	=	$remail5?sanitize_email($remail5):$remail5='';
				$email_subject	=	$_POST['email_subject'];
				$email_subject	=	$email_subject?sanitize_text_field($email_subject):$email_subject='';
				
				$admin	= get_option("admin_email");
				
				add_filter('wp_mail_content_type',array($this,'abr_email_content_type'));
				
				$headers = array(
								"Reply-To: ".$sname."  <".$semail." >",
								"From: ".$sname."<myname@example.com>"
							);
				$multiple_recipients = array($admin,$remail1,$remail2,$remail3,$remail4,$remail5,$semail);
				$msg	=	CF_AR_Email::abr_email_template_2($message,$semail,$sname);
				$mail	=	wp_mail($multiple_recipients,$email_subject,$msg,$headers);
				
				remove_filter('wp_mail_content_type',array($this,'abr_email_content_type'));
				
				if($mail){
					$response=array(
						'response'	=>	1,
						'message'=>'Email sent successfully',
					);
				}
				else{
					$response=array(
						'response'	=>	0,
						'message'=>'Email could not sent !',
					);
				}
			}
		}
		echo json_encode($response);
		die();
	}
	
	public function abr_email_content_type( $content_type){
		return 'text/html';
	}
	

	public function abr_email_template_2($message,$semail,$sname){
		ob_start();
		$o = ob_get_contents();
		?>
       <html><head><title>Email favorites to your friends</title></head><body>
		<div style="background:#444">
			<div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;	moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545; line-height:1.5em;">
				<h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;
				border-bottom:1px solid #bbb">
					Email favorites to your friends
				</h1>
				<p> Hello: <?php echo $sname; ?></p>
				<div style="text-align:left; border-top:1px solid #eee;padding:5px 0 0 0;">
					<?php echo $message; ?>
					<a href="<?php echo get_site_url(); ?>" ><h4><?php echo get_site_url(); ?></h4></a>
					<p style="text-align:left">Warm regards,<br><?php echo $semail; ?></p>
				</div>
			</div>
		</div>
		</body></html>
        <?php
		$o=ob_get_contents();
		ob_end_clean();
		return $o;
	}
	
	/*public function abr_email_template_1($fname,$lname,$dob,$caddres,$chomphon,$cworpho,$email,$rname,$rphone,$remail,$laplifor,$comm,$montbegn,$bedromned,$paddres,$pphone){
		ob_start();
		$o = ob_get_contents();
		?><html><head><title>Application</title></head>
			<body>
				<div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;	moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">
				<h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
				Application</h1>
				<p>
				<ul style="list-style:none">
				  <li><strong> Name</strong>:<?php echo $fname .'&nbsp;'.$lname; ?></li>
                 <li><strong>Date of Birth</strong>:<?php echo $dob; ?></li>
                 <li><strong>Current Address</strong>:<?php echo $caddres; ?></li>
                 <li><strong>Current Home Phone</strong>:<?php echo $chomphon; ?></li>
                 <li><strong>Current Work Phone</strong>:<?php echo $cworpho; ?></li>
                 <li><strong>Email</strong>:<?php echo $email; ?></li>
                 <li><strong>Roommate's Name</strong>:<?php echo $rname; ?></li>
                 <li><strong>Roommate's Phone</strong>:<?php echo $rphone; ?></li>
                 <li><strong>Roommate's Email</strong>:<?php echo $remail; ?></li>
                </ul>
                 <ul style="list-style: none;">
					 <li><strong>Location / Property Applying For</strong>:<?php echo $laplifor; ?></li>
					 <li><strong>Units you are interested in</strong>:<?php echo $comm; ?></li>
					 <li><strong>Lease Would Begin On</strong>:<?php echo $montbegn; ?></li>
					 <li><strong>Number of Bedrooms Needed</strong>:<?php echo $bedromned; ?></li>
					 <li><strong>Permanent Address</strong>:<?php echo $paddres; ?></li>
					 <li><strong>Permanent Phone</strong>:<?php echo $pphone; ?></li>
                 </ul>
				</p>
				<p style="text-align:left">Warm regards,<br><?php echo $fname .'&nbsp;'.$lname;?></p>
			</div>
		</div>
	</body></html>
	<?php
		$o = ob_get_contents();
		ob_end_clean();
		return $o;
	}*/
	
}
add_action('plugins_loaded',array('CF_AR_Email','init'));