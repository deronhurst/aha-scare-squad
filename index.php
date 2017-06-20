<?php
include_once('lib/config.php');
include_once('lib/DataContext.php');

$eid = (!empty($_GET['sid'])) ? $_GET['eid'] : '';
$sid = (!empty($_GET['sid'])) ? $_GET['sid'] : '';
$name = (!empty($_GET['name'])) ? $_GET['name'] : '';

if(empty($sid)){
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>The Scare Squad</title>
<style>
body {
	font-family: Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
	padding: 0;
	margin: 0;
	color: #333;
}
.page {
	text-align: center;
	padding: 2rem;
}
</style>
</head>

<body>
<div class="page">Please go back to your HQ and refresh to start again. <br>
  <br>
  <br>
  <a href="javascript:window.close()" style="color:#fff; background-color:#6ea32e; border-radius:5px; padding:0.5rem 1rem; text-decoration:none; font-weight:bold;">Close</a> </div>
</body>
</html>

<?php
	exit();
}

$db = new DataContext();

$activity = new Activity();
$activity->type = Config::$ACTIVITY_LOGIN;
$activity->supporter_id = $sid;
$activity->event_id = $eid;
$activity->created = "now";

$db->Save($activity);
$db->Submit();
?>

<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>The Scare Squad</title>
		<meta name="description" content="Get ready for some heart pumping fun.  Tell your friends and family that you are making a difference. Send a personalized lifesaving e-card and take the challenge to improve your own heart health today!">
		<meta name=" viewport" content="width=device-width, initial-scale=1">

		<script src="https://use.fontawesome.com/43d7641a74.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Cabin:600|Luckiest+Guy" rel="stylesheet">
		<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/main.css">
		<!-- These elements break flow if stylesheets are not loaded -->
		<style>
			.ir-header-h1 {
				display: none;
			}

			nav {
				display: none;
			}
		</style>
		<!-- IE 9 gradient fix-->
		<!--[if gte IE 9]>
			<style type="text/css">
				.gradient {
					 filter: none;
				}
			</style>
		<![endif]-->
		<script src="js/vendor/modernizr-custom.js"></script>
	</head>

	<body class="invisible">
		<header>
			<h1 class=" visuallyhidden">Welcome to the Scare Squad</h1>
			<img src="img/ir-header-h1.png" class="ir ir-header ir-header-h1" width="577" height="79" alt="header text replacement image">
			<div class="branding">
				<a href="http://www.heart.org/HEARTORG/" target="_blank"><img src="img/logo-aha.png" width="150" height="83" alt="American Heart Association logo"></a>
			</div>
		</header>

		<div class="main" role="main">
			<section class="step step-1 step-first step-welcome step-active"  id="section-welcome">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2 id="challenge-text" class="challenge-text"></h2>
					<p>Send a personalized livesaving Ecard and tell your friends and family you are making a difference. heart health today!</p>
					<ol class="clearfix steps container-animate">
						<li>
							<div class="step-inner">
								<span class="step-counter">1</span><span class="text step-choose">Choose <strong>Your</strong> Character</span>
							</div>
						</li>
						<li>
							<div class="step-inner">
								<span class="step-counter">2</span><span class="text step-upload">Upload <strong>Your</strong> Photo</span>
							</div>
						</li>
						<li>
							<div class="step-inner">
								<span class="step-counter">3</span><span class="text step-message">Add <strong>Your</strong> Message</span>
							</div>
						</li>
						<li>
							<div class="step-inner">
								<span class="step-counter">4</span><span class="text step-share">Share <strong>Your</strong> Card</span>
							</div>
						</li>
					</ol>
					<button type="button" class="button-purple button-music button-music-mute">Mute Music</button>
					<audio id="music"  autoplay loop>
						<source src="music/It-Takes-Heart-Song.ogg">
						<source src="music/It-Takes-Heart-Song.mp3">
					</audio>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next button-only">Next</button>
				</nav>				
			</section>

			<section class="step step-wrap-header step-stack-nav-buttons step-2 step-character" id="section-character">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="visuallyhidden preload">
					<img src="img/monster-petunia-sprite.png">
					<img src="img/monster-rocky-sprite.png">
					<img src="img/monster-finster-sprite.png">
					<img src="img/monster-jax-sprite.png">
					<img src="img/monster-disco-sprite.png">
					<img src="img/monster-blaster-sprite.png">
					<img src="img/monster-charger-sprite.png">
				</div>
				<div class="content" role="content">
					<h2>1. Choose Your Monster!</h2>
					<p>Your first step is to create a character that looks just like you. It is easy by using the options below!</p>
					<ol class="container-animate monsters">
						<li id="petunia" class="monster monster-petunia">
							<input type="radio" class=" visuallyhidden" id="monster-1" name="monster" value="petunia" data-img="monster-petunia.png" data-cutout="monster-petunia-cutout.png" data-cutout-zoom="monster-petunia-cutout.png" data-print="monster-petunia-print.png" data-print-cutout="monster-petunia-print-cutout.png" required>
							<label for="monster-1" class="label-radio">
								<figure>
									<img src="img/monster-petunia-thumb.png">
									<figcaption class="visuallyhidden">Petunia</figcaption>
								</figure>			
							</label>
						</li>
						<li id="rocky" class="monster monster-rocky">
							<input type="radio" class=" visuallyhidden" id="monster-2" name="monster" value="rocky" data-img="monster-rocky.png" data-cutout="monster-rocky-cutout.png" data-cutout-zoom="monster-rocky-cutout.png" data-print="monster-rocky-print.png" data-print-cutout="monster-rocky-print-cutout.png" required>
							<label for="monster-2" class="label-radio">			
								<figure>
									<img src="img/monster-rocky-thumb.png">
									<figcaption class="visuallyhidden">Rocky</figcaption>
								</figure>
							</label>
						</li>
						<li  id="finster" class="monster monster-finster">
							<input type="radio" class=" visuallyhidden" id="monster-3" name="monster" value="finster" data-img="monster-finster.png" data-cutout="monster-finster-cutout.png" data-cutout-zoom="monster-finster-cutout.png" data-print="monster-finster-print.png" data-print-cutout="monster-finster-print-cutout.png" required>
							<label for="monster-3" class="label-radio">			
								<figure>
									<img src="img/monster-finster-thumb.png">
									<figcaption class="visuallyhidden">Finster</figcaption>
								</figure>
							</label>
						</li>
						<li  id="jax" class="monster monster-jax">
							<input type="radio" class=" visuallyhidden" id="monster-4" name="monster" value="jax" data-img="monster-jax.png" data-cutout="monster-jax-cutout.png" data-cutout-zoom="monster-jax-cutout.png" data-print="monster-jax-print.png" data-print-cutout="monster-jax-print-cutout.png" required>
							<label for="monster-4" class="label-radio">			
								<figure>
									<img src="img/monster-jax-thumb.png">
									<figcaption class="visuallyhidden">Jax</figcaption>
								</figure>
							</label>
						</li>
						<li  id="disco" class="monster monster-disco">
							<input type="radio" class=" visuallyhidden" id="monster-5" name="monster" value="disco" data-img="monster-disco.png" data-cutout="monster-disco-cutout.png" data-cutout-zoom="monster-disco-cutout.png" data-print="monster-disco-print.png" data-print-cutout="monster-disco-print-cutout.png">
							<label for="monster-5" class="label-radio">			
								<figure>
									<img src="img/monster-disco-thumb.png">
									<figcaption class="visuallyhidden">Disco</figcaption>
								</figure>
							</label>
						</li>
						<li id="blaster" class="monster monster-blaster">
							<input type="radio" class=" visuallyhidden" id="monster-6" name="monster" value="blaster" data-img="monster-blaster.png" data-cutout="monster-blaster-cutout.png" data-cutout-zoom="monster-blaster-cutout.png" data-print="monster-blaster-print.png" data-print-cutout="monster-blaster-print-cutout.png">
							<label for="monster-6" class="label-radio">			
								<figure>
									<img src="img/monster-blaster-thumb.png">
									<figcaption class="visuallyhidden">Blaster</figcaption>
								</figure>
							</label>
						</li>
						<li id="charger" class="monster monster-charger">
							<input type="radio" class=" visuallyhidden" id="monster-7" name="monster" value="charger" data-img="monster-charger.png" data-cutout="monster-charger-cutout.png" data-cutout-zoom="monster-charger-cutout.png" data-print="monster-charger-print.png" data-print-cutout="monster-charger-print-cutout.png">
							<label for="monster-7" class="label-radio">			
								<figure>
									<img src="img/monster-charger-thumb.png">
									<figcaption class="visuallyhidden">Charger</figcaption>
								</figure>
							</label>
						</li>
					</ol>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next">Next</button>
					<button type="button" class="gradient button-light-purple button-prev">Back</button>
				</nav>				
			</section>

			<section class="step step-wrap-header step-stack-nav-buttons step-has-card step-3 step-upload" id="section-upload">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2>2. Upload Your Photo</h2>
					<div class="flex-outer">
						<div class="flex-inner">
							<p>Make sure your photo is a close-up of your face, and you are looking straight ahead. Ask an adult for help.</p>
							<p>Ask your parent or teacher to help you find a JPG, PNG or GIF file.</p>
							<h3>Examples</h3>
							<ol class="clearfix photos flex">
								<li class="photo">
									<figure>
										<img src="img/photo-1.jpg" width="115" height="115" alt="acceptable photo">
										<figcaption>Like this!</figcaption>
									</figure>
								</li>
								<li class="photo">
									<figure class="not-this">
										<img src="img/photo-2.jpg" width="115" height="115" alt="unacceptable photo">
										<figcaption>Not this</figcaption>
									</figure>
								</li>
								<li class="photo">
									<figure class="not-this">
										<img src="img/photo-3.jpg" width="115" height="115" alt="unacceptable photo">
										<figcaption>Not this</figcaption>
									</figure>
								</li>
								<li class="photo">
									<figure class="not-this">
										<img src="img/photo-4.jpg" width="115" height="115" alt="unacceptable photo">
										<figcaption>Not this</figcaption>
									</figure>
								</li>
							</ol>
							<div class="photo-buttons">
								<button type="button" class="gradient button-purple button-find">Find Photo</button>
								<label class=" visuallyhidden" for="button-file">Find Photo</label>
								<input type="file" id="button-file" value="upload" tabindex="-1">
								<button type="button" class="gradient button-purple button-skip button-skip-upload">Skip Step</button>
							</div>
						</div>
					</div>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next">Next</button>
					<button type="button" class="gradient button-light-purple button-prev">Back</button>
				</nav>				
			</section>

			<section class="step step-wrap-header step-stack-nav-buttons step-4 step-crop" id="section-crop">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2>2. Crop Your Photo</h2>
					<div class="flex-outer">
						<div id="upload-crop"></div>
						<div class="hidden canvas-preview">
							<img src="img/background-blank.png" width="359" height="359" alt="preview placeholder">
						</div>
						<div class="crop-buttons">
							<button type="button" class="button button-purple button-preview">Preview Photo</button>
							<button type="button" class="hidden button button-purple button-crop">Crop Photo</button>
						</div>
					</div>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next">Next</button>
					<button type="button" class="gradient button-light-purple button-prev">Back</button>
				</nav>				
			</section>

			<section class="step step-wrap-header step-stack-nav-buttons step-5 step-message" id="section-message">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2>3. Add Your Message</h2>
					<div class="flex-outer">
						<div class="canvas-preview">
							<img src="img/background-blank.png" width="359" height="359" alt="preview placeholder">
						</div>
						<div class="work-area">
							<p>Tell your friends and family that you are raising money to help save lives. You can use the message below, but we suggest you write your own message and tell them why it is important to you.</p>
							<label class=" visuallyhidden" for="message">Your Message</label>
							<textarea id="message" name="message" class="textarea-message" placeholder="Type your message here"></textarea>
						</div>
					</div>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next">Next</button>
					<button type="button" class="gradient button-light-purple button-prev">Back</button>
				</nav>				
			</section>

			<section id="section-share" class="step step-wrap-header step-stack-nav-buttons step-5 step-share">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2>4. Share Your Card</h2>
					<form id="email-form">
						<div class="upper">
							<div class="input-container">
								<button class="button-facebook" type="button">Share on Facebook</button>
							</div>
							<div class="input-container">
								<label for="participant-name">Your Name:</label>
								<input type="text" name="participant-name" id="participant-name" value="" required="required" autocomplete="off">
							</div>
						</div>
						<h3>Email Family and Friends:</h3>
						<ol class="flex email-addresses">
							<li>
								<label class=" visuallyhidden" for="email-address-1">Email Address 1</label>
								<input type="email" class="pf_email" placeholder="Email 1" id="email-address-1" name="email-address-1" required="required" autocomplete="off">
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-2">Email Address 2</label>
								<input type="email" class="pf_email" placeholder="Email 2" id="email-address-2" name="email-address-2" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-3">Email Address 3</label>
								<input type="email" class="pf_email" placeholder="Email 3" id="email-address-3" name="email-address-3" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-4">Email Address 4</label>
								<input type="email" class="pf_email" placeholder="Email 4" id="email-address-4" name="email-address-4" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-5">Email Address 5</label>
								<input type="email" class="pf_email" placeholder="Email 5" id="email-address-5" name="email-address-5" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-6">Email Address 6</label>
								<input type="email" class="pf_email" placeholder="Email 6" id="email-address-6" name="email-address-6" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-7">Email Address 7</label>
								<input type="email" class="pf_email" placeholder="Email 7" id="email-address-7" name="email-address-7" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-8">Email Address 8</label>
								<input type="email" class="pf_email" placeholder="Email 8" id="email-address-8" name="email-address-8" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-9">Email Address 9</label>
								<input type="email" class="pf_email" placeholder="Email 9" id="email-address-9" name="email-address-9" autocomplete="off" >
							</li>
							<li>
								<label class=" visuallyhidden" for="email-address-10">Email Address 10</label>
								<input type="email" class="pf_email" placeholder="Email 10" id="email-address-10" name="email-address-10" autocomplete="off" >
							</li>
						</ol>
						<button class=" visuallyhidden" type="submit">Submit</button>
				</form>
				</div>
				<nav class="nav">
					<button type="button" class="gradient button-pink button-next">Next</button>
					<button type="button" class="gradient button-light-purple button-prev">Back</button>
				</nav>				
			</section>

			<section class="step step-stack-nav-buttons step-6 step-last step-congratulations" id="section-congratulations">
				<a href="#" class=" visuallyhidden focus-reset" data-role="tab-control"></a>
				<div class="content" role="content">
					<h2>Congratulations!</h2>
					<p>Your ecard has been sent successfully!</p>
					<div class="clearfix re-engage-buttons">
						<button class="gradient button-pink button-send-more">Send More</button>
						<button class="button-facebook">Share on Facebook</button>
					</div>
					<button type="button" class="button-purple button-print-ecard">Print Your Monster for Your School!</button>
				</div>
			</section>

		</div>

		<div class="bgs">
			<div class="bg bg-tree-top-left"></div>
			<div class="bg bg-tree-top-right"></div>
			<div class="bg bg-leaves bg-leaves-left"></div>
			<div class="bg bg-leaves bg-leaves-right"></div>
			<div class="bg bg-tree-bottom-left"></div>
			<div class="bg bg-water"></div>
			<div class="bg bg-grass"></div>
			<div class="bgs-clouds">
				<div class="bg bg-cloud-small bg-cloud-small-right"></div>
				<div class="bg  bg-cloud-small bg-cloud-small-left"></div>
				<div class="bg bg-cloud-medium"></div>
			</div>
			<div class="bg bg-cloud-large"></div>
			<div class="bgs-mountain">
				<div class="bg bg-mountain-small"></div>
				<div class="bg bg-mountain-medium"></div>
				<div class="bg bg-mountain-large"></div>
			</div>
			<div class="bg bg-sky"></div>
		 </div>

		 <div class=" visuallyhidden canvases">
			<canvas id="preview_canvas" width="324" height="375"></canvas>
			<canvas id="email_canvas" width="324" height="375"></canvas>
			<canvas id="print_canvas" width="500" height="387"></canvas>
		 </div>

		 <div class="message message-error">
			<div class="message-inner">
				<div class="message-text"></div>
				<div class="message-close"><img src="img/button-close.png" width="29" height="29" alt="close popup button"></div>
			</div>
		 </div>

		<div class="message message-alert message-print">
			<div class="message-inner">
				<div class="message-text">
					<div class="message-waiting">
						<p>Please hang on while we save your ecard.</p>
						<p>This shouldn't take long!</p>
						<div class="wrapper-loader">
							<i class="fa fa-spin fa-meh-o" aria-hidden="true"></i>
						</div>									
					</div>
					<div class="hidden message-ready">
						<p>OK. All set!</p>
						<button type="button" class="gradient button-purple button-continue button-open-print-window">Continue</button>									
					</div>
				</div>
			</div>
		</div>

		<footer class=" visuallyhidden">
			<p><small>&copy; Copyright 2017</small></p>
		</footer>

		<script   src="https://code.jquery.com/jquery-2.2.4.min.js"   crossorigin="anonymous"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-2.2.4.min.js"><\/script>')</script>
		<script   src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"  crossorigin="anonymous"></script>
		<script>window.jQuery.ui || document.write('<script src="js/vendor/jquery-ui.min.js"><\/script>')</script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js"></script>

		<script src="js/plugins.js"></script>
		<script src="js/main.js"></script>
		<script>
			var event_id = '<?php echo $eid; ?>';
			var sup_id = '<?php echo $sid; ?>';
			var sup_name = '<?php echo $name; ?>'.replace(/[^\w\s]/gi, ' ');
		</script>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-34030359-1', 'auto');
			ga('send', 'pageview');			
		</script>

	</body>
</html>
