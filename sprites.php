<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>The Scare Squad</title>
		<meta name="description" content="Get ready for some heart pumping fun.  Tell your friends and family that you are making a difference. Send a personalized lifesaving e-card and take the challenge to improve your own heart health today!">
		<meta name=" viewport" content="width=device-width, initial-scale=1">

		<script src="https://use.fontawesome.com/43d7641a74.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Luckiest+Guy" rel="stylesheet">
		<link rel="stylesheet" href="css/normalize.css">
				<link rel="stylesheet" href="css/main.css">

	</head>

	<body>

<ol class="monsters">
	<li id="petunia" class="monster monster-petunia">
		<input type="radio" class=" visuallyhidden" id="monster-1" name="monster" value="petunia" data-img="monster-petunia.png" data-cutout="monster-petunia-cutout.png" data-cutout-zoom="monster-petunia-cutout-zoom.png" required>
		<label for="monster-1" class="label-radio">
			<figure>
				<img src="img/petunia/monster-petunia-thumb.png">
				<figcaption class="visuallyhidden">Petunia</figcaption>
			</figure>			
		</label>
	</li>
	<li id="rocky" class="monster monster-rocky">
		<input type="radio" class=" visuallyhidden" id="monster-2" name="monster" value="rocky" data-img="monster-rocky.png" data-cutout="monster-rocky-cutout.png" data-cutout-zoom="monster-rocky-cutout-zoom.png" required>
		<label for="monster-2" class="label-radio">			
			<figure>
				<img src="img/rocky/monster-rocky-thumb.png">
				<figcaption class="visuallyhidden">Rocky</figcaption>
			</figure>
		</label>
	</li>
	<li  id="finster" class="monster monster-finster">
		<input type="radio" class=" visuallyhidden" id="monster-3" name="monster" value="finster" data-img="monster-finster.png" data-cutout="monster-finster-cutout.png" data-cutout-zoom="monster-finster-cutout-zoom.png" required>
		<label for="monster-3" class="label-radio">			
			<figure>
				<img src="img/finster/monster-finster-thumb.png">
				<figcaption class="visuallyhidden">Finster</figcaption>
			</figure>
		</label>
	</li>
	<li  id="jax" class="monster monster-jax">
		<input type="radio" class=" visuallyhidden" id="monster-4" name="monster" value="jax" data-img="monster-jax.png" data-cutout="monster-jax-cutout.png" data-cutout-zoom="monster-jax-cutout-zoom.png" required>
		<label for="monster-4" class="label-radio">			
			<figure>
				<img src="img/jax/monster-jax-thumb.png">
				<figcaption class="visuallyhidden">Jax</figcaption>
			</figure>
		</label>
	</li>
	<li  id="disco" class="monster monster-disco">
		<input type="radio" class=" visuallyhidden" id="monster-5" name="monster" value="disco" data-img="monster-disco.png" data-cutout="monster-disco-cutout.png" data-cutout-zoom="monster-disco-cutout-zoom.png" required>
		<label for="monster-5" class="label-radio">			
			<figure>
				<img src="img/disco/monster-disco-thumb.png">
				<figcaption class="visuallyhidden">Disco</figcaption>
			</figure>
		</label>
	</li>
	<li id="blaster" class="monster monster-blaster">
		<input type="radio" class=" visuallyhidden" id="monster-6" name="monster" value="blaster" data-img="monster-blaster.png" data-cutout="monster-blaster-cutout.png" data-cutout-zoom="monster-v-cutout-zoom.png" required>
		<label for="monster-6" class="label-radio">			
			<figure>
				<img src="img/blaster/monster-blaster-thumb.png">
				<figcaption class="visuallyhidden">Blaster</figcaption>
			</figure>
		</label>
	</li>
	<li id="charger" class="monster monster-charger">
		<input type="radio" class=" visuallyhidden" id="monster-7" name="monster" value="charger" data-img="monster-charger.png" data-cutout="monster-charger-cutout.png" data-cutout-zoom="monster-charger-cutout-zoom.png" required>
		<label for="monster-7" class="label-radio">			
			<figure>
				<img src="img/charger/monster-charger-thumb.png">
				<figcaption class="visuallyhidden">Charger</figcaption>
			</figure>
		</label>
	</li>
</ol>


		<script   src="https://code.jquery.com/jquery-2.2.4.min.js"   crossorigin="anonymous"></script>
		<script>window.jQuery || document.write('<script src="js/vendor/jquery-2.2.4.min.js"><\/script>')</script>
		<script   src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"  crossorigin="anonymous"></script>
		<script>window.jQuery.ui || document.write('<script src="js/vendor/jquery-ui.min.js"><\/script>')</script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js"></script>

		<script src="js/plugins.js"></script>
		<!--<script src="js/main.js"></script>-->

<script>
	function animateMonster($monster) {
		if (typeof $monster != 'undefined') {
			var _trans = .5;
			var _timer = _trans;
			var _bounces = 2;
			var _bounce = _bounces;
			var _fallHeight = 0;
			var _endY = 10;
			var _offset = 20;
			var _exaggeration = .2;
			var _gravity = 1; //0 - 1;
			var $c = $monster;
			var $p = $(".monsters");

			$monster.show().addClass('animating');

			falling();

			$c.click(function() {
				replayBounce();
			});

			function replayBounce(){
				_bounce = _bounces;
				_fallHeight = 0;
				_trans = _timer / ((_bounces - _bounce) + 1);
				_offset = ((((1 / _bounces) / _bounces) * _bounce) * (_bounce / 2)) * _exaggeration;
				squash_n_stretch();
			}

			function falling() {
				_offset = ((((1 / _bounces) / _bounces) * _bounce) * (_bounce / 2)) * _exaggeration;
				if (_offset > 1.5) _offset=1.5;
				if (_offset < .1) _offset = .1;
				TweenLite.to($p,_trans,{css:{top:_endY},overwrite:false,ease:Quad.easeIn,onComplete:squash_n_stretch});
				TweenLite.to($c,_trans,{css:{scaleY:1 + _offset,scaleX:1 - _offset},ease:Quad.easeOut});
			}

			function squash_n_stretch() {
				if (_bounce == 1) {
					TweenLite.to($c,.1,{css:{scaleY:1 - _offset,scaleX:1 + _offset, transformOrigin:"50% 100%"},ease:Quad.easeOut, onComplete:rising});
				} else {
					TweenLite.to($c,_trans / 2,{css:{scaleY:1 - _offset,scaleX:1 + _offset, transformOrigin:"50% 100%"},ease:Quad.easeOut, onComplete:rising});
				}
			}

			function rising() {
				_fallHeight = _fallHeight + ((((_endY - _fallHeight) * _gravity) / (_bounce)));
				if (_bounce == 1) {
					_fallHeight = _endY;
				}

				TweenLite.to($c,_trans / 2,{css:{scaleY:1 + _offset,scaleX:1 - _offset, transformOrigin:"50% 100%"},ease:Quad.easeIn, onComplete:normalize});
				TweenLite.to($p,_trans,{css:{top:_fallHeight+"px"},overwrite:false,ease:Quad.easeOut,  delay:_trans / 4});

			}

			function normalize(){
				if (_bounce == 1) {
					TweenLite.to($c,.2,{css:{scaleY:1,scaleX:1},ease:Quad.easeInOut});
						$monster.removeClass('animating');

				} else {
					TweenLite.to($c,_trans / 2,{css:{scaleY:1,scaleX:1},ease:Quad.easeInOut, onComplete:next_bounce});
				}
			}

			function next_bounce() {
				_bounce--;
				if (_bounce > 0) {
					falling();
					_trans = _trans * (_bounce / _bounces);
					if (_trans < _timer / _bounces) {
						_trans = _timer / _bounces;
					}
				}
			}
		}
	}

	$(document).ready(function() {

		$('.monster').on('click', function() {
			animateMonster($(this).find('figure'));
		});






		/*
var imgHeight = 180;
var numImgs = 7;
var cont = 0;
var img = $('#container').find('img');

var animation = setInterval( moveSprite,100);

function moveSprite(){
		img.css('margin-top', -1 * (cont*imgHeight));

		cont++;
		if(cont == numImgs){
				clearInterval(animation);
		}
}
*/
/*
						$("#blaster")
						.mouseover(function() {
							if ($(this).hasClass("animate")) {
							 $(this).animateSprite('resume');
							} else {
							$(this).animateSprite({
								fps: 10,
								totalFrames: 3,
								loop: true
							}).addClass("animate");
							}
						})
						.mouseout(function(){
							 $(this).animateSprite('stop').removeClass("animate");
						});
*/

	})
</script>

	</body>
</html>
