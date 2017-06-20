<?php
include_once('lib/config.php');
include_once('lib/DataContext.php');
include_once('lib/phpmailer/PHPMailerAutoload.php');
include_once('lib/functions.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if(empty($_POST['name'])  || empty($_POST['message']) || empty($_POST['emails'])){
	exit("Invalid request");	
}

$name = urldecode($_POST['name']);
$sender = 'jumphoops@schoolprograms.heart.org'; //$_POST['sender'];
$message = urldecode($_POST['message']);
$emails = $_POST['emails'];

$title = $_POST['title'];
$choice = $_POST['choice'];
$character = $_POST['character'];
$sid = $_POST['sid'];
$eid = $_POST['eid'];

$uid = uniqid();
$image = base64_decode(str_replace("data:image/png;base64,","",$_POST['image']));	
$url = "/shares/email".$sid.substr($uid,0,3).time().".png";
$path = dirname(__FILE__).$url;
file_put_contents($path,$image);

$fnames = explode(" ", $name);

$db = new DataContext();

$share_url = sprintf(
  "http://heartdev.convio.net/site/TR/Jump/General?px=%d&pg=personal&fr_id=%d&s_src=ecard&s_subsrc=email&ecard_linktrack=[aid]",
  $sid,
  $eid
  );

//echo $_POST['image'];
//exit();
// render email
ob_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml"  style="background-color:transparent;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AHA Zoo Crew</title>
<style type="text/css">
/* Client-specific Styles */
#outlook a {
	padding: 0;
} /* Force Outlook to
provide a "view in browser" menu link. */
body {
	width: 100% !important;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
	margin: 0;
	padding: 0;
}
/* Prevent Webkit and Windows Mobile platforms
from changing default font sizes, while not
breaking desktop design. */
.ExternalClass {
	width: 100%;
} /* Force Hotmail to
display emails at full width */
.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
	line-height: 100%;
} /* Force
Hotmail to display normal line spacing.  More on
that:
http://www.emailonacid.com/forum/viewthread/43/ */
#backgroundTable {
	margin: 0;
	padding: 0;
	width: 100% !important;
	line-height: 100% !important;
}
img {
	outline: none;
	text-decoration: none;
	border: none;
	-ms-interpolation-mode: bicubic;
}
a img {
	border: none;
}
.image_fix {
	display: block;
}
p {
	margin: 0px 0px !important;
}
table td {
	border-collapse: collapse;
}
table {
	border-collapse: collapse;
	mso-table-lspace: 0pt;
	mso-table-rspace: 0pt;
}
/*a {color: #e95353;text-decoration:
none;text-decoration:none!important;}*/
		/*STYLES*/
table[class=full] {
	width: 100%;
	clear: both;
}

/*################################################*/
		/*IPAD STYLES*/
		/*################################################*/
@media only screen and (max-width: 640px) {
a[href^="tel"], a[href^="sms"] {
	text-decoration: none;
	color: #ffffff; /* or whatever your want */
	pointer-events: none;
	cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	text-decoration: default;
	color: #ffffff !important;
	pointer-events: auto;
	cursor: default;
}
table[class=devicewidth] {
	width: 700px!important;
	text-align: center!important;
}
table[class=devicewidthinner] {
	width: 420px!important;
	text-align: center!important;
}
table[class="sthide"] {
	display: none!important;
}
img[class="bigimage"] {
	width: 700px!important;
}
img[class="col2img"] {
	width: 420px!important;
	height: 258px!important;
}
img[class="image-banner"] {
	width: 700px!important;
	height: 106px!important;
}
td[class="menu"] {
	text-align: center !important;
	padding: 0 0 10px 0 !important;
}
td[class="logo"] {
	padding: 10px 0 5px 0!important;
	margin: 0 auto !important;
}
img[class="logo"] {
	padding: 0!important;
	margin: 0 auto !important;
}
}

/*##############################################*/
		/*IPHONE STYLES*/
		/*##############################################*/
@media only screen and (max-width: 480px) {
a[href^="tel"], a[href^="sms"] {
	text-decoration: none;
	color: #ffffff; /* or whatever your want */
	pointer-events: none;
	cursor: default;
}
.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
	text-decoration: default;
	color: #ffffff !important;
	pointer-events: auto;
	cursor: default;
}
table[class=devicewidth] {
	width: 280px!important;
	text-align: center!important;
}
table[class=devicewidthinner] {
	width: 260px!important;
	text-align: center!important;
}
table[class="sthide"] {
	display: none!important;
}
img[class="bigimage"] {
	width: 280px!important;
}
img[class="col2img"] {
	width: 260px!important;
	height: 160px!important;
}
img[class="image-banner"] {
	width: 280px!important;
	height: 68px!important;
}
}
</style>
</head>

<body  style="background-color:transparent;">
<div style="font-size:1px;color:#ffffff;line-height:1px;mso-line-height-rule:exactly;display:none;max-width:0px;max-height:0px;opacity:0;overflow:hidden;mso-hide:all;" > I'm doing an  important event at my
  school for the American Heart Association and
  raising money to help others with special hearts.
  Will you help me by making a donation
  today? </div>
<div class="block"> 
  <!-- image + text -->
  <table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="bigimage">
    <tbody>
      <tr>
        <td><table bgcolor="#ffffff" width="580" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" modulebg="edit">
            <tbody>
              <tr>
                <td><table width="580" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
                    <tbody>
                      <tr> 
                        <!-- start of image -->
                        <td align="center"><a href="<?php echo $share_url; ?>"><img  style="display:block; border:none; outline:none; text-decoration:none;" src="<?php echo Config::$base_url; ?>/emails/footer.png" class="bigimage" width="469" border="0" height="" alt="American Heart Association"></a></td>
                      </tr>
                      <!-- end of image --> 
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- Spacing -->
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="block"> 
  <!-- Full + text -->
  <table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fullimage">
    <tbody>
      <tr>
        <td><table bgcolor="#ffffff" width="580" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" modulebg="edit">
            <tbody>
              <tr>
                <td width="100%" height="20"></td>
              </tr>
              <tr>
                <td><table width="540" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidthinner">
                    <tbody>
                      <!-- title -->
                      <tr>
                        <td style="font-family: Helvetica,
arial, sans-serif; font-size: 24px; color:
#115f31; text-align:left;line-height: 28px;
text-align:center;" st-title="rightimage-title"> A Personal message from
                          <?php 	echo $fnames[0];
														?>
                          </td>
                      </tr>
                      <!-- end of title --> 
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- personal link -->
                      <tr>
                        <td style="text-align:center" st-title="rightimage-title"><a style="text-decoration:underline; font-family: Helvetica,
arial, sans-serif; font-size: 18px; color:
#115f31; text-align:left;line-height: 28px;
text-align:center;" href="<?php echo $share_url; ?>">Visit my page to donate</a></td>
                      </tr>
                      <!-- end of personal link --> 
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- content -->
                      <tr>
                        <td style="font-family: Helvetica,
arial, sans-serif; font-size: 13px; color:
#666666; text-align:left;line-height: 24px;" st-content="rightimage-paragraph"><?php
				$urls = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
				$message = preg_replace($urls, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $message);
				$message = str_replace("\\'","'", $message);
				$message = str_replace("\r","", $message);
				$message = str_replace("\n\n","</p><br><p>", $message);

				echo $message;
															
				echo "<br><br>Thank you in advance,<br>".$name."<br><br>";
														?></td>
                      </tr>
                      <!-- end of content --
												<!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- button -->
                      <tr>
                        <td><table height="30" align="center" valign="middle" border="0" cellpadding="0" cellspacing="0" class="tablet-button" st-button="edit">
                            <tbody>
                              <tr>
                                <td width="auto" align="center" valign="middle" height="30" style="
background-color:#ff9610;
border-top-left-radius:4px;
border-bottom-left-radius:4px;border-top-right-radius:4px;
border-bottom-right-radius:4px; background-clip:
padding-box;font-size:32px; font-family:Helvetica,
arial, sans-serif; text-align:center; 
color:#ffffff; font-weight: 300;
padding-bottom:8px; padding-left:32px;
padding-right:32px; padding-top:8px;"><span style="color: #ffffff;
font-weight: 300;"> <a href="<?php echo $share_url; ?>" style="color: #ffffff;
text-align:center;text-decoration:
none;">Donate</a> </span></td>
                              </tr>
                              <tr>
                                <td width="100%" height="20"></td>
                              </tr>
                              <!-- Spacing --> 
                              <!-- start of image -->
                              <tr>
                                <td align="center"><a href="<?php echo $share_url; ?>"><img src="<?php echo Config::$base_url.$url; ?>" width="90%" alt="My Scare Squad Ecard" style="border: 2px solid #115f31; box-shadow: 1px 1px 6px rgba(0, 0, 0, 0.2); display: block; height: auto; max-width: 388px; text-decoration:none;"></a></td>
                              </tr>
                              <!-- end of image --> 
                              <!-- Spacing -->
                              <tr>
                                <td width="100%" height="20"></td>
                              </tr>
                              <!-- Spacing -->
                              
                            </tbody>
                          </table></td>
                      </tr>
                      <!-- /button --> 
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- Spacing -->
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
</div>
<div class="block"> 
  <!-- image + text -->
  <table width="100%"  cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="bigimage">
    <tbody>
      <tr>
        <td><table bgcolor="#ffffff" width="580" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth" modulebg="edit">
            <tbody>
              <tr>
                <td><table width="580" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
                    <tbody>
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- Spacing -->
                      <tr> 
                        <!-- start of image -->
                        <td align="center"></td>
                      </tr>
                      <!-- end of image --> 
                      
                      <!-- Spacing -->
                      <tr>
                        <td width="100%" height="20"></td>
                      </tr>
                      <!-- Spacing -->
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
</div>
<img alt="" src="http://hearttools.heart.org/aha_ym18/emailopen_record.php?open_email=[aid]" width="1" height="1" border="0" style="width:1px; height:1px;"/>
</body>
</html>
<?php
	$body = ob_get_clean();



	// get team id	
	$tid = getTeamID ($eid, $sid);
	

	
	/*** Sending email ********/
	$mail = new PHPMailer();
	
	$mail->From = $sender;
	$mail->FromName = $name;	
	$mail->Subject = "Check out my life-saving e-card!"; //.$title;
	$mail->IsHTML(true);
	$mail->WordWrap = 50;

	$mail->setFrom('jumphoops@schoolprograms.heart.org', $name);
	$mail->addReplyTo('jumphoops@schoolprograms.heart.org', 'Zoo Crew');

	// Adding domain key
	$mail->DKIM_domain = 'schoolprograms.heart.org';
	$mail->DKIM_private = dirname(__FILE__).'/schoolprograms.txt';
	$mail->DKIM_selector = 'zurigroup'; //Prefix for the DKIM selector
	$mail->DKIM_passphrase = ''; //leave blank if no Passphrase
	$mail->DKIM_identity = $mail->From; 

	$mail->AddCustomHeader('Return-path:jumphoops@schoolprograms.heart.org');
	
	/* 
	echo '<p>$mail->From = ' .$mail->From. '</p>';
	echo '<p>$mail->FromName = ' .$mail->FromName. '</p>';
	echo '<p>$mail->Body = ' .$mail->Body. '</p>';
	echo '<p>$mail->Subject = ' .$mail->Subject. '</p>';
	echo '<p>$mail->IsHTML(true) = ' .$mail->IsHTML(true). '</p>';
	echo '<p>$mail->WordWrap = ' .$mail->WordWrap. '</p>';
	*/
	

	foreach ($emails as $address) {
		
		$mail->AddAddress($address);
		
		//creating activity record
		$activity = new Activity();
		$activity->type = Config::$ACTIVITY_EMAIL;
		$activity->supporter_id = $sid;
		$activity->event_id = $eid;
		$activity->team_id = (!empty($tid))? $tid : 0;
		$activity->character = $character;
		$activity->choice = $choice;
		$activity->created = "now";
		
		$res = $db->Save($activity);		
		$res = $db->Submit();
		
		$mail->Body = str_replace("[aid]",$activity->id, $body);
	
		$res = $mail->Send();
		if($res) {
			echo $address." :send\n";
		}
		else{
			echo $address." :failed\n";
			$db->Delete($activity);
		}
		
		$mail->ClearAddresses();
	}
	
	// record challange
	updateChallange($eid, $sid, $choice, false);
		
?>
