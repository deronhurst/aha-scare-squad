<?php
$name = $_GET['name'];
$challenge = $_GET['challenge'];
$image = $_GET['image'];
?>

<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>The Scare Squad</title>
		<meta name="description" content="Get ready for some heart pumping fun.  Tell your friends and family that you are making a difference. Send a personalized lifesaving e-card and take the challenge to improve your own heart health today!">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="https://fonts.googleapis.com/css?family=Cabin:600|Luckiest+Guy" rel="stylesheet">
		<link rel="stylesheet" href="css/normalize.css">
		<link rel="stylesheet" href="css/main.css">

		<script src="js/vendor/modernizr-custom.js"></script>
	</head>

	<body class="page-print-ecard">
		<div class="print-controls">
			<button type="button" class="button-purple button-print">Print Photo</button>
		</div>

		<div class="content" role="content">
			<h3 id="student-name" class="student-name"><?php echo $name; ?></h3>
			<p id="challenge-text" class="challenge-text"><?php echo $challenge; ?></p>
			<img id="ecard" class="ecard" src="<?php echo $image; ?>">
		</div>

		<canvas id="print-canvas" class="visuallyhidden print-canvas" width="" height="200"></canvas>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		 
			ga('create', 'UA-34030359-1', 'auto');
			ga('send', 'pageview');		 
		</script>


		<script   src="https://code.jquery.com/jquery-2.2.4.min.js"   crossorigin="anonymous"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-2.2.4.min.js"><\/script>')</script>
		<script>
			(function($) {
				var canvas = document.getElementById('print-canvas');
				var context = canvas.getContext('2d');

				var sources = {
					bg_page: 'img/bg-print-page.png',
					ecard: document.getElementById('ecard').src,
				};

				// Fill student name and challenge
				$('#student-name').text("<?php echo $name; ?>".replace(/[^\w\s]/gi, ' '));
				$('#challange-text').text("<?php echo $challenge; ?>")
				
				// Fill student name and challenge on canvas
				function loadText() {
					var	max_width,
							line_height,
							x,
							y,
							text;

					function wrapText(context, text, x, y, maxWidth, lineHeight) {
						var words = text.split(' ');
						var line = '';

						for(var n = 0; n < words.length; n++) {
							var test_line = line + words[n] + ' ';
							var metrics = context.measureText(test_line);
							var test_width = metrics.width;
							if (test_width > max_width && n > 0) {
								context.textAlign="center";
								context.fillText(line, x, y);
								line = words[n] + ' ';
								y += line_height;
							}
							else {
								line = test_line;
							}
						}
						context.textAlign="center";
						context.fillText(line, x, y);
					}

					text =$('#student-name').text();
					context.font ='46px Luckiest Guy';
					context.fillStyle = '#00';
					var text_width = context.measureText(text).width;
					x = (canvas.width - text_width) / 2 + 200;
					y = 350;
					context.fillText(text, x, y);

					text = $('#challange-text').text();
					context.font ='40px Luckiest Guy';
					context.fillStyle = '#ffffff';
					context.shadowColor = 'rgba(0, 0, 0, 0.8)';
					context.shadowOffsetX = 0; 
					context.shadowOffsetY = 1; 
					context.shadowBlur = 4;
					max_width = canvas.width - (90 * 2);
					line_height = 46;
					x = (canvas.width - max_width) + 325;
					y = 808;
					wrapText(context, text, x, y, max_width, line_height);
				}

				// Cache canvas images 
				function loadImages(sources, callback) {
					var images = {};
					var loadedImages = 0;
					var numImages = 0;
					// get num of sources
					for(var src in sources) {
						numImages++;
					}
					for(var src in sources) {
						images[src] = new Image();
						images[src].onload = function() {
							if(++loadedImages >= numImages) {
								callback(images);
								loadText();
							}
						};
						images[src].src = sources[src];
					}
				}

				// Draw backgroung and ecard images on canvas
				loadImages(sources, function(images) {
					canvas.width = 1000;
					canvas.height = 920;
					context.drawImage(images.bg_page, 0, 0, canvas.width, canvas.height);
					context.drawImage(images.ecard, (canvas.width / 2 - images.ecard.width / 2 + 60), (canvas.height - images.ecard.height - 110), 403, 312);
				});

				$('.button-print').on('click', function() {
					window.print();
				});

			})(jQuery);
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
