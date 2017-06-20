document.addEventListener("DOMContentLoaded", function() {
	var el = document.getElementById('email-form'),
		 elements;

	if (el) {
		elements = el.getElementsByTagName('INPUT');
		for (var i = 0; i < elements.length; i++) {
			elements[i].oninvalid = function(e) {
					e.target.setCustomValidity('');
					if (!e.target.validity.valid) {
						if (e.target.id == 'participant-name') {
							e.target.setCustomValidity('Oops! Please fill in your name.');
						} else if (e.target.required && e.target.value === '') {
							e.target.setCustomValidity('Whoops! Please enter at least one email address to send your ecard.');
						} else {
							e.target.setCustomValidity('Oops! This email address doesn\'t look right. Please check your friends\' email address(es)');
						}
					}
			};
			elements[i].oninput = function(e) {
				e.target.setCustomValidity('');
			};
		}
	}
});

(function($){
	var $gradient_text = $('.main h2, .step-counter');

	if (Modernizr.backgroundcliptext) {
		$gradient_text.addClass('gradient-text');
	} else {
		$gradient_text.pxgradient({
			step: 10,
			colors: ["#6153a3","#ff0076"],
			dir: "y"
		});
	}
})(jQuery);

function animateObject($object, bounces) {
	if (typeof $object != 'undefined') {
		var _trans = .5;
		var _timer = _trans;
		var _bounces = 2;
		var _bounce = bounces || _bounces;
		var _fallHeight = 0;
		var _endY = 10;
		var _offset = 20;
		var _exaggeration = .2;
		var _gravity = 1; //0 - 1;
		var $c = $object;
		var $p = $object.closest('.container-animate');

		$object.show().addClass('animating');

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
					$object.removeClass('animating');

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
	var section_complete = true;
	var skip_upload = false;
	var challenge_text = '';
	var challenge_id = 1;

	var PF = {};

	// current state of the widget
	PF.current = {
		music: true,
		hasImage : true,
		preview :{
			bg: null,
			img: null,
			cutout: null,
			print_img: null,
			print_cutout: null,
			emailbg:null,
			emailfg: null,
			left:0,
			top:0,
			width:0,
			height:0,
			done: false
		},
		pos: {a:0, b:0, x:0, y:0},
		character: null,
		crop: null,
		cropped: null,
		viewport: {
			width: 0,
			height: 0
		}
	};

	// configurations
	PF.config = {
		base : 'http://localhost/ahazoocrew/app/',
	};

	// dynamic vaiables
	PF.vars = {
		// preview canvas
		pcanvas : null,
		pcontext: null,
		// email preview
		ecanvas: null,
		econtext: null,
		uploadCrop: null,
		printImage: null
	};

	// initialize widget
	PF.init = function () {
		// initialise canvases
		PF.vars.pcanvas = document.getElementById('preview_canvas');
		PF.vars.pcontext = PF.vars.pcanvas.getContext('2d');

		PF.vars.ecanvas = document.getElementById('email_canvas');
		PF.vars.econtext = PF.vars.ecanvas.getContext('2d');

		PF.vars.printcanvas = document.getElementById('print_canvas');
		PF.vars.printcontext = PF.vars.printcanvas.getContext('2d');
	}

	PF.getImage = function () {
		var query = Modernizr.mq('(min-width: 480px)'),
				boundry,
				character = PF.current.character,
				ufile =$('#button-file').get(0).files[0],
				ufname = ufile.name,
				fparts = ufname.split('.'),
				fext = fparts[fparts.length-1].toLowerCase();

		// check extention
		if (fext != 'jpg' && fext != 'png' && fext != 'gif' && fext != 'jpeg') {
			alert('Invalid image format : '+fext);
			return;
		} else {
			PF.current.hasImage = true;
			section_complete = true;
		}

		if (query) {
			boundry = {
				width: 324,
				height: 375
			};

			PF.current.viewport.width = 180;
			PF.current.viewport.height = 180;
		} else {
			boundry = {
				width: 324,
				height: 375
			};
			switch (character) {
				case 'blaster' :
					PF.current.viewport.width = 118;
					PF.current.viewport.height = 136;
					break;
				case 'charger' :
					PF.current.viewport.width = 118;
					PF.current.viewport.height =135 ;
					break;
				case 'disco' :
					PF.current.viewport.width = 123;
					PF.current.viewport.height = 139;
					break;
				case 'finster' :
					PF.current.viewport.width = 123;
					PF.current.viewport.height = 139;
					break;
				case 'jax' :
					PF.current.viewport.width = 122;
					PF.current.viewport.height = 141;
					break;
				case 'petunia' :
					PF.current.viewport.width = 121;
					PF.current.viewport.height = 141;
					break;
				case 'rocky' :
					PF.current.viewport.width = 121;
					PF.current.viewport.height = 141;
					break;
			}
		}
		// clear the crop widget
		$('#upload-crop').croppie('destroy');

		// initialize the crop widget
		PF.vars.uploadCrop = $('#upload-crop').croppie({
			characterImg:  PF.current.crop.src,
			viewport: {
				width: PF.current.viewport.width,
				height: PF.current.viewport.height,
				type: 'circle'
			},
			boundary: boundry,
			exif: true
		});
		// draw uploaded file on a temparary canvas
		var reader = new FileReader();
		reader.onload = function(e) {
			// attach the uploaded photo to the crop widget
			PF.vars.uploadCrop.croppie('bind', {
				url: e.target.result
			});
		}
		reader.readAsDataURL(ufile);

		//scroll to the crop widget section
		setTimeout(function() {
			section_complete = true;
			$('.button-next').trigger('click');
		}, 100);
	}

	// redraw on preview canvas
	PF.updatePreview = function () {
		var preview_x = 0;
		var preview_y = 0;
		var preview_h = 0;
		var preview_w = 0;
		var print_x = 0;
		var print_y = 0;
		var print_h = 0;
		var print_w = 0;

		function updateCanvases() {
			if (!PF.current.preview.done) {
				// update preview images
				$('.canvas-preview').html('<img src="'+PF.vars.pcanvas.toDataURL()+'">');
				// update email image
				PF.vars.econtext.drawImage( PF.vars.pcanvas, 0,0);
			}
		}

		// clear the canvases
		PF.vars.pcontext.clearRect(0, 0, 324, 375)
		PF.vars.printcontext.clearRect(0, 0, 500, 387)

		if (PF.current.hasImage) {
			// draw the character image on preview canvas
			PF.vars.pcontext.drawImage(PF.current.preview.cutout, 0, 0);

			// draw the cropped uploaded image to preview canvas
			preview_w = 180;
			preview_h = 180;
			preview_x = 74;
			preview_y= 162;
			PF.vars.pcontext.drawImage(PF.current.cropped, preview_x, preview_y, preview_w, preview_h)
			
			// draw the character image on print canvas
			PF.vars.printcontext.drawImage(PF.current.preview.print_cutout, 0, 0);
			// draw the cropped uploaded image to print canvas
			print_w = 198;
			print_h = 198;
			print_x = 151;
			print_y= 141;
			PF.vars.printcontext.drawImage(PF.current.cropped, print_x, print_y, print_w, print_h)

			// pause to let the crop function catch up
			setTimeout(function() {
				// draw the cropped image onto the  canvas
				updateCanvases();
			}, 500)

		} else {
			PF.vars.pcontext.drawImage(PF.current.preview.img, 0, 0);
			PF.vars.printcontext.drawImage(PF.current.preview.print_img, 0, 0);
			updateCanvases();
		}
	}

	// email submit form
	PF.submitForm = function () {
		var $participant_name = $('#participant-name'),
			 participant_name = $participant_name.val(),
			 frm,
			 ewin,
			 eml,
			 $loader = $('.ajax-loader-screen, .ajax-loader');

		$loader.removeClass('visuallyhidden');

		// proceed to send emails
		str = {
			name :encodeURIComponent(participant_name),
			message : encodeURIComponent($('#message').val()),
			sid : sup_id,
			eid : event_id,
			title : challenge_text,
			choice: challenge_text,
			character: $('input[name="monster"]:checked').val(),
			emails : [],
			image : PF.vars.ecanvas.toDataURL()
		};

		$.each($('.pf_email'), function () {
			var val = $.trim($(this).val());

			if (!val) {
				return;
			}
			str.emails.push($(this).val());
		});

		frm = '<form id="email_form" method="post" action="email.php">';
		for (k in str) {
			if (k == 'emails') {
				for (i = 0; i < str[k].length; i++) {
					frm += '<input type="hidden" name="emails[]" value="'+str[k][i]+'" />';
				}
			} else {
				frm += '<input type="hidden" name="'+k+'" value="'+str[k]+'" />';
			}
		}
		frm += '</form>';

		$('#email_iframe').remove();
		$(document.body).append('<iframe id="email_iframe" frameborder="0" style="width:1px; height:1px"></iframe>');
		ewin = document.getElementById('email_iframe');

		var inter = window.setInterval(function() {
			var eml = (ewin.contentWindow || ewin.contentDocument);

			if (eml.document.readyState === 'complete') {
				window.clearInterval(inter);
				// grab the content of the iframe here
				$(eml.document.body).append(frm);
				$(eml.body).append(frm);
				console.log(eml);
				eml.document.getElementById('email_form').submit();
				$loader.addClass('visuallyhidden');
				section_complete = true;
				$('.button-next').trigger('click');
			}
		}, 100);
	}

	function resetChoices() {
		$('input[type="radio"]').prop('checked', false);
	}

	function resetShare() {
		$('#email-form').find('input[type="email"]').val('');
	}

	function error(message) {
		var $error_message = $('.message-error'),
			 $error_message_inner = $error_message.find('.message-inner'),
			 $error_message_text = $error_message.find('.message-text'),
			 $error_message_close = $error_message.find('.message-close') ;

		$error_message_text.html(message);
		$error_message.addClass('message-show');

		$error_message.add($error_message_inner, $error_message_text, $error_message_close).unbind().on('click', function() {
			$error_message.removeClass('message-show');
			$error_message_text.text('');
		});
	}

	function getCropResult() {
		// get the cropped image and send to the preview canvas
		PF.vars.uploadCrop.croppie('result', {
			type: 'canvas',
			size: {
				width: PF.current.viewport.width,
				height: PF.current.viewport.height
			}
		}).then(function (resp) {
			PF.current.cropped = new Image();
			PF.current.cropped.onload = function () {
				PF.updatePreview();
			};
			PF.current.cropped.src = resp;
		});
	}

	function keyboard() {
		var $step_active = $('.step-active'),
				$focus_reset = $step_active.find('.focus-reset'),
				$tabbables = $step_active.find(':tabbable'),
				$tabbable_first = $tabbables.not('.focus-reset').filter(':first'),
				$tabbable_last = $tabbables.filter(':last');

		$focus_reset.unbind().on('click', function() {
			return false;
		}).trigger('focus');
		$step_active.on('keydown', function(e) {
			if (e.keyCode == $.ui.keyCode.TAB) {
				if (e.target == $tabbable_last.get(0)) {
					$focus_reset.remove();
					$tabbable_first.focus();
					e.preventDefault();
				}
			} else if (e.keyCode == $.ui.keyCode.ENTER) {
				$(e.target).prop('checked', true).trigger('click');
			}
		});
	}

	function resetBackgroundHeights() {
		$('.bg-grass').css('height', '');
		$('.bgs-mountain').css('bottom', '');
		$('.bg-tree-bottom-left').css('bottom', '');
	}

	function newBackgroundHeights() {
		var query = Modernizr.mq('(min-width: 1024px)');
		var content_height = ($('.monsters').height() + $('#section-character').find('.nav').height());
		var multiplier = query ? 0.8 : 0.9;
		var bottom_adjust = query ? 20 : 0;

		content_height = content_height * multiplier;

		if (!query && $('body').is('.scene-characters')) {
			$('.bg-grass').css('height', function() {
				return content_height;
			});

			$('.bgs-mountain').css('bottom', function() {
				return content_height * 0.975 - bottom_adjust;
			});

			$('.bg-tree-bottom-left').css('bottom', function() {
				return content_height * 0.95 - bottom_adjust;
			});
		} else {
			resetBackgroundHeights();
		}
	}

	function navigation() {
		var $step_active = $('.step-active'),
			 $button_prev = $('.button-prev'),
			 $button_next = $('.button-next'),
			 $document = $('html, body');
			 $page_heading = $('.ir-header-h1');

		$('.button-next').unbind().on('click', function() {
			var $main = $('.main'),
				 $step_active = $('.step-active'),
				 step_active_ht = $step_active.height(),
				 $step_next = $step_active.next('.step'),
				 step_next_ht = 0;

			if ($step_active.is('#section-crop, #section-message')) {
				section_complete = true;
			} else if ($step_active.is('#section-character') && $('input[name="monster"]:checked').length > 0) {
				section_complete = true;
			} else if ($step_active.is('#section-share') && !section_complete) {
				// submit the email form
				$('#email-form').find('[type="submit"]').trigger('click');
			}

			if (section_complete) {
				if ($step_active.is('#section-crop')) {
					getCropResult();
				}
				if ($step_next.length > 0) {
					if ($step_next.is('#section-upload')) {
						PF.current.preview.done = false;
					}
					$main.height(step_active_ht);
					 step_next_ht = $step_next.height();
					$step_active.animate({
						left: '-100%',
						right: '100%'
					}, 500, 'easeOutExpo').removeClass('step-active');
					$main.height(step_next_ht);
					$document.animate({
						scrollTop: 0
					}, '100');
					$step_next.animate({
						left: 0,
						right: 0
					}, 800, 'easeOutBack', function() {
						$(this).addClass('step-active');
						if ($step_next.is('#section-character')) {
							$('body').addClass('scene-characters');
							newBackgroundHeights();
							animateMonsters();
							$(window).on('debouncedresize', function( event ) {
								newBackgroundHeights();
							});
						} else {
							$('body').removeClass('scene-characters');
							resetBackgroundHeights();
						}
						$document.animate({
							scrollTop: 0
						}, {
							duration: 'slow',
							complete: function() {
								if ($step_next.is('#section-crop')) {
									$document.animate({
										scrollTop: $("#upload-crop").offset().top
									}, '100');
								}
							}
						});
						animateObject($page_heading, 1);
						section_complete = false;
						navigation();
						keyboard();
					});
				}
			} else {
				if ($step_active.is('#section-character')) {
					error('Whoops! You forgot to choose your Scare Squad monster.');
				} else if ($step_active.is('#section-upload')) {
					error('Oops! You forgot to upload you photo. Click the <strong>SKIP STEP</strong> button if you don\'t want to upload your photo.');
				}
			}
			return false;
		});

		$('.button-prev').unbind().on('click', function() {
			var $main = $('.main'),
				 $step_active = $('.step-active'),
				 step_active_ht = $step_active.height();
				 $step_prev = $step_active.prev('.step'),
				 step_prev_ht = 0;

			if (skip_upload && $step_active.is('#section-message')) {
				$step_prev = $('#section-upload');
				skip_upload = false;
			} else {
				$step_prev = $step_active.prev('.step');
			}
			if ($step_prev.length > 0) {
				if ($step_prev.is('#section-upload')) {
					PF.current.preview.done = false;
				}
				$main.height(step_active_ht);
				step_prev_ht = $step_prev.height();
				$step_active.animate({
					left: '100%',
					right: '-100%'
				}, 700, 'easeOutBack').removeClass('step-active');
				$main.height(step_prev_ht);
				$document.animate({
					scrollTop: 0
				}, '100');
				$step_prev.animate({
					left: 0,
					right: 0
				}, 800, 'easeOutBack', function() {
					if ($step_prev.is('#section-character')) {
						$('body').addClass('scene-characters');
						newBackgroundHeights();
						animateMonsters();
						if ($('input[name="monster"]:checked').length > 0) {
							section_complete = true;
						}
					} else {
						$('body').removeClass('scene-characters');
						resetBackgroundHeights();
					}
					animateObject($page_heading, 1);
					$(this).addClass('step-active');
					navigation();
					keyboard();
				});
			}
			section_complete = true;
		});

		$('.button-skip-upload').unbind().on('click', function() {
			var $main = $('.main'),
				 $step_active = $('.step-active'),
				 step_active_ht = $step_active.height(),
				 $step_next = $step_active.next('.step').next('.step'),
				 step_next_ht = 0;

			if ($step_next.length > 0) {
				PF.current.hasImage = false;
				skip_upload = true;
				PF.updatePreview();
				$main.height(step_active_ht);
				step_next_ht = $step_next.height();
				$step_active.animate({
					left: '-100%',
					right: '100%'
				}, 800, 'easeOutExpo').removeClass('step-active');
				$main.height(step_next_ht);
				$document.animate({
					scrollTop: 0
				}, '100');
				$step_next.animate({
					left: 0,
					right: 0
				}, 800, 'easeOutBack', function() {
					animateObject($page_heading, 1);
					$(this).addClass('step-active');
					navigation();
					keyboard();
				});
			}
			section_complete = false;
			return false;
		});

		$('.button-preview').unbind().on('click', function() {
			getCropResult();
			$(this).addClass('hidden');
			$('#upload-crop').addClass('hidden');
			$('#section-crop').find('.canvas-preview, .button-crop').removeClass('hidden');
			$(window).trigger('debouncedresize');
			setTimeout(function() {
				PF.current.preview.done = true;
			}, 2000);
		});

		$('.button-crop').unbind().on('click', function() {
			PF.current.preview.done = false;
			$(this).addClass('hidden');
			$('#section-crop')
				.find('.canvas-preview').addClass('hidden').end()
				.find('.button-preview').removeClass('hidden');
			$('#upload-crop').removeClass('hidden');
			$(window).trigger('debouncedresize');
		});

		$('.button-send-more').unbind().on('click', function() {
			resetShare();
			$('.button-prev').trigger('click');
			section_complete = false;
			return false;
		});
	}

	function challenge() {
		var  user_messages = [
			/* default */ "I'm doing an important event at my school for the American Heart Association and raising money to help others with special hearts. Will you help me by making a donation today?\n\nI'm also learning how to take care of my own heart by making healthy choices. Please join me and make a donation so you too can be a heart hero like me! Oh, and check out the eCard I made for you of my favorite Monster.",
			/* activity */ "I'm doing an important event at my school for the American Heart Association and raising money to help others with special hearts. Will you help me by making a donation today?\n\nI'm also learning how to take care of my own heart by making healthy choices. In fact, I'm taking a challenge today to be physically active for at least 60 minutes a day and I challenge you to do the same! Please join my challenge and make a donation so you too can be a heart hero like me! Oh, and check out the eCard I made for you of my favorite Monster.",
			/* water */ "I'm doing an  important event at my school for the American Heart Association and raising money to help others with special hearts. Will you help me by making a donation today?\n\nI'm also learning how to take care of my own heart by making healthy choices. In fact, I'm taking a challenge today to choose water over sugary beverages and I challenge you to do the same! Please join my challenge and make a donation so you too can be a heart hero like me! Oh, and check out the eCard I made for you of my favorite Monster.",
			/* veggies */ "I'm doing an important event at my school for the American Heart Association and raising money to help others with special hearts. Will you help me by making a donation today?\n\nI'm also learning how to take care of my own heart by making healthy choices. I'm also learning how to take care of my own heart by making healthy choices. In fact, I'm taking a challenge today to eat at least one fruit or veggie at every meal and I challenge you to do the same! Please join my challenge and make a donation so you too can be a heart hero like me! Oh, and check out the eCard I made for you of my favorite Monster.",
		]

		$.ajax({
			dataType: 'json',
			data: 'key=6Mwqh5dFV39HLDq7',
			url: 'http://hearttools.heart.org/aha_ym18/api/student/' + event_id + '/' + sup_id
		})
		.done(function(data) {
			if (data.challenges) {
				if (data.challenges.current !== '' && data.challenges.current !== null && data.challenges.text !== '' && data.challenges.text !== null) {
					challenge_id = parseInt(data.challenges.current);
					challenge_text = 'You\'ve taken the challenge to ' + data.challenges.text + '!';
				}
			}
			console.log(data);
		})
		.fail(function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
			console.log(textStatus);
			console.log(errorThrown);
		})
		.always(function() {
			var $challenge_text = $('#challenge-text');
			var $message = $('textarea[name="message"]');

			if (challenge_text === '') {
				$('body').addClass('no-challenge')
				$challenge_text.text('Take the healthy heart challenge today!');
				$message.val(user_messages[0]);
			} else {
				$challenge_text.text(challenge_text);
				$message.val(user_messages[challenge_id]);
			}
		});
	}

	function music() {
		var $button_music = $('.button-music');

		if (PF.current.music) {
			$button_music.not('.button-music-mute').removeClass('button-music-play').addClass('button-music-mute').text('Mute Music');
		} else {
			$button_music.not('.button-music-play').removeClass('button-music-mute').addClass('button-music-play').text('Play Music');
		}
		$button_music.unbind().on('click', function() {
			var $this = $(this),
				 player = document.getElementById('music');

			if ($this.is('.button-music-mute')) {
				player.pause();
				$this.removeClass('button-music-mute').addClass('button-music-play').text('Play Music');
			} else if ($this.is('.button-music-play')) {
				player.play();
				$this.removeClass('button-music-play').addClass('button-music-mute').text('Mute Music');
			}
		});
	}

	function animateMonsters() {
		var speed = 100;
		var timer = setInterval(doAnimate, speed);
		var $monsters =  $('.monster').find('figure');
		var length = $monsters.length;
		var index = 0;

		function doAnimate() {
			animateObject($monsters.eq(index, 2));
			index++;
			 if (index >= length) {
				 clearInterval(timer);
			}
		}
	}

	function characters() {
		$('.monster').on('mouseover', function() {
			animateObject($(this).find('figure'), 2);
		});

		$('input[name="monster"]').unbind().on('click', function() {
			var $this= $(this),
				 character = $this.val(),
				 img_src = 'img/' + $this.attr('data-img'),
				 cutout_img_src = 'img/' + $this.attr('data-cutout'),
				 cutout_zoom_img_src = 'img/' + $this.attr('data-cutout-zoom');
				 print_img_src = 'img/' + $this.attr('data-print'),
				 cutout_print_img_src = 'img/' + $this.attr('data-print-cutout'),

			animateObject($this.siblings('label').find('figure'), 2);

			section_complete = true;
			$('.character-selected').html('<img src="' + img_src + '" alt="selected character">');
			PF.current.character = character;
			PF.current.preview.img = new Image();
			PF.current.preview.img.src = img_src;
			PF.current.preview.cutout = new Image();
			PF.current.preview.cutout.src = cutout_img_src;
			PF.current.crop = new Image();
			PF.current.crop.src = cutout_zoom_img_src;
			PF.current.preview.print_img = new Image();
			PF.current.preview.print_img.src = print_img_src;
			PF.current.preview.print_cutout = new Image();
			PF.current.preview.print_cutout.src = cutout_print_img_src;
		});
	}

	function photo() {
		var $button_file = $('#button-file');

		$button_file.on('change', function() {
			PF.getImage();
		});
		$('.button-find').unbind().on('click', function() {
			$button_file.trigger('click');
			return false;
		});
	}

	function message() {
		$('textarea[name="message"]').on('change', function() {
			if ($(this).val() !== '') {
				section_complete = true;
			} else {
				section_complete = false;
			}
		});
	}

	function email() {
		$('#participant-name').val(sup_name);

		$('#email-form').unbind().on('submit', function(e) {
			e.preventDefault();
			if ($(this)[0].checkValidity()) {
				if(validateDupEmails()) {
					PF.submitForm();
				}
				else{
					error('Please remove duplicate email addresses before sending');
				}
			}
		});
	}

	function facebook() {
		$('.button-facebook').on('click', function() {
			var frm, w;

			$('#share_form').remove();

			frm = '<form id="share_form" method="post" action="post.php">';
			frm += '<input type="hidden" name="sid" value="' + sup_id + '">';
			frm += '<input type="hidden" name="eid" value="' + event_id + '">';
			frm += '<input type="hidden" name="title" value="' + challenge_text.replace(/You/, 'I') + '">';
			frm += '<input type="hidden" name="message" value="' + encodeURIComponent($('#message').val()) + '">';
			frm += '<input type="hidden" name="character" value="' + $('input[name="monster"]:checked').val() + '">';
			frm += '<input type="hidden" name="image" value="' + PF.vars.ecanvas.toDataURL() + '">';
			frm += "</form>";

			w = window.open('about:blank','Popup_Window','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=600');
			w.document.write(frm);
			w.document.close();

			w.document.forms[0].submit();
		});
	}

	function goToPrintEcard() {
		var	$print_message = $('.message-print'),
				$continue_button = $print_message.find('.button-open-print-window');

		$continue_button.on('click', function() {
			var user_name = $('#participant-name').val();

			$print_message.removeClass('message-show message-done')
				.find('.message-waiting').removeClass('hidden').end()
				.find('.message-ready').addClass('hidden').end()
				.find('.fa').attr('class', 'fa fa-spin fa-meh-o');

			window.open('http://hearttools.heart.org/aha_ym18/print-ecard.php?name=' + user_name + '&challenge=' +  challenge_text.replace(/You/, 'I') + '&image=' + PF.vars.printImage);
		});

		$('.button-print-ecard').on('click', function() {
		// Keep secret click element in case we need to hide print button
		//$('#section-congratulations').find('.ir-header-h2').on('click', function() {
			// Let the user know we're saving the image
			$print_message.addClass('message-show');

			// Save ecard image to server for the print page to use
			$.ajax({
				type: "POST",
				url: "save-ecard.php",
				data: {
					 image: PF.vars.printcanvas.toDataURL(),
					 sid: sup_id
				}
			}).done(function(img) {
				console.log(img);

				if (img !== null) {
					// Cache the saved image
					PF.vars.printImage = 'prints/' + img;
					 $('<img/>')[0].src = PF.vars.printImage;
					 // Let the user know that the image has been saved
					 $print_message.find('.fa').attr('class', 'fa fa-smile-o');
					 setTimeout(function() {
						$print_message.addClass('message-done')
							.find('.message-waiting').addClass('hidden').end()
							.find('.message-ready').removeClass('hidden');
					 }, 1000);
				} else {
					// ????
				}
			});
		});
	}

	PF.init();
	resetChoices();
	resetShare();
	keyboard();
	navigation();
	challenge();
	music();
	characters();
	photo();
	message();
	email();
	facebook();
	goToPrintEcard();

	/** Initialize duplicate email validation ****/
	$('.pf_email').blur(validateDupEmails);
});

$(window).load(function() {
	var query = Modernizr.mq('(min-width: 905px)');

	function splash() {
		var $steps = $('.steps li');

		function contentHeight() {
			var $step_1 = $('.step-1');

			$('.main').height($step_1.height());
			$step_1.addClass('step-float');

			$(window).on('debouncedresize', function( event ) {
				$('.main').height(function() {
					return $('.step-active').height();
				});
			});
		}

		if (query) {
			$('body').removeClass('invisible');
			setTimeout(function() {
				$('.ir-header-h1').animate({
					'top': 0
				}, 500, 'easeOutBounce', function() {
					animateObject($(this), 2);
					$('.steps li:nth-child(1)').animate({
						'top': 0
					}, 400, 'easeOutBounce', function() {
						animateObject($(this), 1);
						$('.steps li:nth-child(2)').animate({
							'top': 0
						}, 400, 'easeOutBounce', function() {
							animateObject($(this), 1);
							$('.steps li:nth-child(3)').animate({
								'top': 0
							}, 400, 'easeOutBounce', function() {
								animateObject($(this), 1);
								$('.steps li:nth-child(4)').animate({
									'top': 0
								}, 400, 'easeOutBounce', function() {
									animateObject($(this), 1);
									$steps.addClass('step-show');
									contentHeight();
								});
							});
						});
					});
				});
			}, 1000);
		} else {
			$('body').removeClass('invisible');
			$steps.addClass('step-show');
			$('.ir-header-h1').animate({
				'top': 0
			}, 1000, 'easeOutBounce', function() {
				contentHeight();
			});
		}
	}
	splash() ;
});


/******** Duplicate Email Validation ******/
function validateDupEmails () {

	var eml = {};
	var res = true;

	for(k = 0 ; k < $('.pf_email').size(); k++) {
	//$.each( $('.pf_email'), function () {
		em = $('.pf_email').eq(k).val().toLowerCase();



		if(em != '') {

			if(!eml[em] ) {
				eml[em] = 1;
			}
			else{
				// display error message
				eid = $('.pf_email')[k].id+"_error";

				if($('#'+eid).size() == 0) {

					$('.pf_email').eq(k).after('<div style="background-color: red; border-radius: 0 0 3px 3px;    color: white;    font-size: 14px;    padding: 5px;  position: absolute;" id="'+eid+'">Oops, you have already entered that email address. </div>');

					$('.pf_email').eq(k).keydown(function(e){  $('#'+this.id+"_error").remove(); });
				}


				res = false;
			}
		}
	}

	return res;
}
