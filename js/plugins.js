// Avoid `console` errors in browsers that lack a console.
(function() {
		var method;
		var noop = function () {};
		var methods = [
				'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
				'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
				'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
				'timeline', 'timelineEnd', 'timeStamp', 'trace', 'warn'
		];
		var length = methods.length;
		var console = (window.console = window.console || {});

		while (length--) {
				method = methods[length];

				// Only stub undefined methods.
				if (!console[method]) {
						console[method] = noop;
				}
		}
}());

/*
 * jQuery spritely 0.6.7
 * http://spritely.net/
 *
 * Documentation:
 * http://spritely.net/documentation/
 *
 * Copyright 2010-2011, Peter Chater, Artlogic Media Ltd, http://www.artlogic.net/
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 */

(function($) {
    $._spritely = {
        // shared methods and variables used by spritely plugin
        instances: {},
        animate: function(options) {
            var el = $(options.el);
            var el_id = el.attr('id');
            if (!$._spritely.instances[el_id]) {
                return this;
            }
            options = $.extend(options, $._spritely.instances[el_id] || {});
            if (options.type == 'sprite' && options.fps) {
                if (options.play_frames && !$._spritely.instances[el_id]['remaining_frames']) {
                    $._spritely.instances[el_id]['remaining_frames'] = options.play_frames + 1;
                } else if (options.do_once && !$._spritely.instances[el_id]['remaining_frames']) {
                    $._spritely.instances[el_id]['remaining_frames'] = options.no_of_frames;
                }
                var frames;
                var animate = function(el) {
                    var w = options.width, h = options.height;
                    if (!frames) {
                        frames = [];
                        total = 0
                        for (var i = 0; i < options.no_of_frames; i ++) {
                            frames[frames.length] = (0 - total);
                            total += w;
                        }
                    }
                    if ($._spritely.instances[el_id]['current_frame'] == 0) {
                        if (options.on_first_frame) {
                            options.on_first_frame(el);
                        }
                    } else if ($._spritely.instances[el_id]['current_frame'] == frames.length - 1) {
                        if (options.on_last_frame) {
                            options.on_last_frame(el);
                        }
                    }
                    if (options.on_frame && options.on_frame[$._spritely.instances[el_id]['current_frame']]) {
                        options.on_frame[$._spritely.instances[el_id]['current_frame']](el);
                    }
                    if (options.rewind == true) {
                        if ($._spritely.instances[el_id]['current_frame'] <= 0) {
                            $._spritely.instances[el_id]['current_frame'] = frames.length - 1;
                        } else {
                            $._spritely.instances[el_id]['current_frame'] = $._spritely.instances[el_id]['current_frame'] - 1;
                        };
                    } else {
                        if ($._spritely.instances[el_id]['current_frame'] >= frames.length - 1) {
                            $._spritely.instances[el_id]['current_frame'] = 0;
                        } else {
                            $._spritely.instances[el_id]['current_frame'] = $._spritely.instances[el_id]['current_frame'] + 1;
                        }
                    }

                    var yPos = $._spritely.getBgY(el);
                    el.css('background-position', frames[$._spritely.instances[el_id]['current_frame']] + 'px ' + yPos);
                    if (options.bounce && options.bounce[0] > 0 && options.bounce[1] > 0) {
                        var ud = options.bounce[0]; // up-down
                        var lr = options.bounce[1]; // left-right
                        var ms = options.bounce[2]; // milliseconds
                        el
                            .animate({top: '+=' + ud + 'px', left: '-=' + lr + 'px'}, ms)
                            .animate({top: '-=' + ud + 'px', left: '+=' + lr + 'px'}, ms);
                    }
                }
                if ($._spritely.instances[el_id]['remaining_frames'] && $._spritely.instances[el_id]['remaining_frames'] > 0) {
                    $._spritely.instances[el_id]['remaining_frames'] --;
                    if ($._spritely.instances[el_id]['remaining_frames'] == 0) {
                        $._spritely.instances[el_id]['remaining_frames'] = -1;
                        delete $._spritely.instances[el_id]['remaining_frames'];
                        return this;
                    } else {
                        animate(el);
                    }
                } else if ($._spritely.instances[el_id]['remaining_frames'] != -1) {
                    animate(el);
                }
            } else if (options.type == 'pan') {
                if (!$._spritely.instances[el_id]['_stopped']) {

                    // As we pan, reduce the offset to the smallest possible
                    // value to ease the load on the browser. This step is
                    // skipped if the image hasn't loaded yet.
                    var speed = options.speed || 1,
                        start_x = $._spritely.instances[el_id]['l'] || parseInt($._spritely.getBgX(el).replace('px', ''), 10) || 0,
                        start_y = $._spritely.instances[el_id]['t'] || parseInt($._spritely.getBgY(el).replace('px', ''), 10) || 0;

                    if (options.do_once && !$._spritely.instances[el_id].remaining_frames || $._spritely.instances[el_id].remaining_frames <= 0) {
                        switch(options.dir) {
                            case 'up':
                            case 'down':
                                $._spritely.instances[el_id].remaining_frames = Math.floor((options.img_height || 0) / speed);
                                break;
                            case 'left':
                            case 'right':
                                $._spritely.instances[el_id].remaining_frames = Math.floor((options.img_width || 0) / speed);
                                break;
                        }
                        $._spritely.instances[el_id].remaining_frames++;
                    } else if (options.do_once) {
                        $._spritely.instances[el_id].remaining_frames--;
                    }

                    switch (options.dir) {

                        case 'up':
                            speed *= -1;
                        case 'down':
                            if (!$._spritely.instances[el_id]['l'])
                                $._spritely.instances[el_id]['l'] = start_x;
                            $._spritely.instances[el_id]['t'] = start_y + speed;
                            if (options.img_height)
                                $._spritely.instances[el_id]['t'] %= options.img_height;
                            break;

                        case 'left':
                            speed *= -1;
                        case 'right':
                            if (!$._spritely.instances[el_id]['t'])
                                $._spritely.instances[el_id]['t'] = start_y;
                            $._spritely.instances[el_id]['l'] = start_x + speed;
                            if (options.img_width)
                                $._spritely.instances[el_id]['l'] %= options.img_width;
                            break;

                    }

                    // When assembling the background-position string, care must be taken
                    // to ensure correct formatting.
                    var bg_left = $._spritely.instances[el_id]['l'].toString();
                    if (bg_left.indexOf('%') == -1) {
                        bg_left += 'px ';
                    } else {
                        bg_left += ' ';
                    }

                    var bg_top = $._spritely.instances[el_id]['t'].toString();
                    if (bg_top.indexOf('%') == -1) {
                        bg_top += 'px ';
                    } else {
                        bg_top += ' ';
                    }

                    $(el).css('background-position', bg_left + bg_top);

                    if (options.do_once && !$._spritely.instances[el_id].remaining_frames) {
                        return this;
                    }
                }
            }
            $._spritely.instances[el_id]['options'] = options;
            $._spritely.instances[el_id]['timeout'] = window.setTimeout(function() {
                $._spritely.animate(options);
            }, parseInt(1000 / options.fps));
        },
        randomIntBetween: function(lower, higher) {
            return parseInt(rand_no = Math.floor((higher - (lower - 1)) * Math.random()) + lower);
        },
        getBgUseXY: (function() {
            try {
                return typeof $('body').css('background-position-x') == 'string';
            } catch(e) {
                return false;
            }
        })(),
        getBgY: function(el) {
            if ($._spritely.getBgUseXY) {
                return $(el).css('background-position-y') || '0';
            } else {
                return ($(el).css('background-position') || ' ').split(' ')[1];
            }
        },
        getBgX: function(el) {
            if ($._spritely.getBgUseXY) {
                return $(el).css('background-position-x') || '0';
            } else {
                return ($(el).css('background-position') || ' ').split(' ')[0];
            }
        },
        get_rel_pos: function(pos, w) {
            // return the position of an item relative to a background
            // image of width given by w
            var r = pos;
            if (pos < 0) {
                while (r < 0) {
                    r += w;
                }
            } else {
                while (r > w) {
                    r -= w;
                }
            }
            return r;
        },

        _spStrip: function(s, chars) {
            // Strip any character in 'chars' from the beginning or end of
            // 'str'. Like Python's .strip() method, or jQuery's $.trim()
            // function (but allowing you to specify the characters).
            while (s.length) {
                var i, sr, nos = false, noe = false;
                for (i=0;i<chars.length;i++) {
                    var ss = s.slice(0, 1);
                    sr = s.slice(1);
                    if (chars.indexOf(ss) > -1)
                        s = sr;
                    else
                        nos = true;
                }
                for (i=0;i<chars.length;i++) {
                    var se = s.slice(-1);
                    sr = s.slice(0, -1);
                    if (chars.indexOf(se) > -1)
                        s = sr;
                    else
                        noe = true;
                }
                if (nos && noe)
                    return s;
            }
            return '';
        }
    };
    $.fn.extend({

        spritely: function(options) {

            var $this = $(this),
                el_id = $this.attr('id'),
                
                options = $.extend({
                    type: 'sprite',
                    do_once: false,
                    width: null,
                    height: null,
                    img_width: 0,
                    img_height: 0,
                    fps: 12,
                    no_of_frames: 2,
                    play_frames: 0
                }, options || {}),

                background_image = (new Image()),
                background_image_src = $._spritely._spStrip($this.css('background-image') || '', 'url("); ');

                if (!$._spritely.instances[el_id]) {
                    if (options.start_at_frame) {
                        $._spritely.instances[el_id] = {current_frame: options.start_at_frame - 1};
                    } else {
                        $._spritely.instances[el_id] = {current_frame: -1};
                    }
                }

                $._spritely.instances[el_id]['type'] = options.type;
                $._spritely.instances[el_id]['depth'] = options.depth;

                options.el = $this;
                options.width = options.width || $this.width() || 100;
                options.height = options.height || $this.height() || 100;

            background_image.onload = function() {

                options.img_width = background_image.width;
                options.img_height = background_image.height;

                options.img = background_image;
                var get_rate = function() {
                    return parseInt(1000 / options.fps);
                }

                if (!options.do_once) {
                    setTimeout(function() {
                        $._spritely.animate(options);
                    }, get_rate(options.fps));
                } else {
                    setTimeout(function() {
                        $._spritely.animate(options);
                    }, 0);
                }

            }

            background_image.src = background_image_src;

            return this;

        },

        sprite: function(options) {
            var options = $.extend({
                type: 'sprite',
                bounce: [0, 0, 1000] // up-down, left-right, milliseconds
            }, options || {});
            return $(this).spritely(options);
        },
        pan: function(options) {
            var options = $.extend({
                type: 'pan',
                dir: 'left',
                continuous: true,
                speed: 1 // 1 pixel per frame
            }, options || {});
            return $(this).spritely(options);
        },
        flyToTap: function(options) {
            var options = $.extend({
                el_to_move: null,
                type: 'moveToTap',
                ms: 1000, // milliseconds
                do_once: true
            }, options || {});
            if (options.el_to_move) {
                $(options.el_to_move).active();
            }
            if ($._spritely.activeSprite) {
                if (window.Touch) { // iphone method see http://cubiq.org/remove-onclick-delay-on-webkit-for-iphone/9 or http://www.nimblekit.com/tutorials.html for clues...
                    $(this)[0].ontouchstart = function(e) {
                        var el_to_move = $._spritely.activeSprite;
                        var touch = e.touches[0];
                        var t = touch.pageY - (el_to_move.height() / 2);
                        var l = touch.pageX - (el_to_move.width() / 2);
                        el_to_move.animate({
                            top: t + 'px',
                            left: l + 'px'
                        }, 1000);
                    };
                } else {
                    $(this).click(function(e) {
                        var el_to_move = $._spritely.activeSprite;
                        $(el_to_move).stop(true);
                        var w = el_to_move.width();
                        var h = el_to_move.height();
                        var l = e.pageX - (w / 2);
                        var t = e.pageY - (h / 2);
                        el_to_move.animate({
                            top: t + 'px',
                            left: l + 'px'
                        }, 1000);
                    });
                }
            }
            return this;
        },
        // isDraggable requires jQuery ui
        isDraggable: function(options) {
            if ((!$(this).draggable)) {
                //console.log('To use the isDraggable method you need to load jquery-ui.js');
                return this;
            }
            var options = $.extend({
                type: 'isDraggable',
                start: null,
                stop: null,
                drag: null
            }, options || {});
            var el_id = $(this).attr('id');
            if (!$._spritely.instances[el_id]) {
                return this;
            }
            $._spritely.instances[el_id].isDraggableOptions = options;
            $(this).draggable({
                start: function() {
                    var el_id = $(this).attr('id');
                    $._spritely.instances[el_id].stop_random = true;
                    $(this).stop(true);
                    if ($._spritely.instances[el_id].isDraggableOptions.start) {
                        $._spritely.instances[el_id].isDraggableOptions.start(this);
                    }
                },
                drag: options.drag,
                stop: function() {
                    var el_id = $(this).attr('id');
                    $._spritely.instances[el_id].stop_random = false;
                    if ($._spritely.instances[el_id].isDraggableOptions.stop) {
                        $._spritely.instances[el_id].isDraggableOptions.stop(this);
                    }
                }
            });
            return this;
        },
        active: function() {
            // the active sprite
            $._spritely.activeSprite = this;
            return this;
        },
        activeOnClick: function() {
            // make this the active script if clicked...
            var el = $(this);
            if (window.Touch) { // iphone method see http://cubiq.org/remove-onclick-delay-on-webkit-for-iphone/9 or http://www.nimblekit.com/tutorials.html for clues...
                el[0].ontouchstart = function(e) {
                    $._spritely.activeSprite = el;
                };
            } else {
                el.click(function(e) {
                    $._spritely.activeSprite = el;
                });
            }
            return this;
        },
        spRandom: function(options) {
            var options = $.extend({
                top: 50,
                left: 50,
                right: 290,
                bottom: 320,
                speed: 4000,
                pause: 0
            }, options || {});
            var el_id = $(this).attr('id');
            if (!$._spritely.instances[el_id]) {
                return this;
            }
            if (!$._spritely.instances[el_id].stop_random) {
                var r = $._spritely.randomIntBetween;
                var t = r(options.top, options.bottom);
                var l = r(options.left, options.right);
                $('#' + el_id).animate({
                    top: t + 'px',
                    left: l + 'px'
                }, options.speed)
            }
            window.setTimeout(function() {
                $('#' + el_id).spRandom(options);
            }, options.speed + options.pause)
            return this;
        },
        makeAbsolute: function() {
            // remove an element from its current position in the DOM and
            // position it absolutely, appended to the body tag.
            return this.each(function() {
                var el = $(this);
                var pos = el.position();
                el.css({position: "absolute", marginLeft: 0, marginTop: 0, top: pos.top, left: pos.left })
                    .remove()
                    .appendTo("body");
            });

        },
        spSet: function(prop_name, prop_value) {
            var el_id = $(this).attr('id');
            $._spritely.instances[el_id][prop_name] = prop_value;
            return this;
        },
        spGet: function(prop_name, prop_value) {
            var el_id = $(this).attr('id');
            return $._spritely.instances[el_id][prop_name];
        },
        spStop: function(bool) {
            this.each(function() {
                var $this = $(this),
                    el_id = $this.attr('id');
                if ($._spritely.instances[el_id]['options']['fps']) {
                    $._spritely.instances[el_id]['_last_fps'] = $._spritely.instances[el_id]['options']['fps'];
                }
                if ($._spritely.instances[el_id]['type'] == 'sprite') {
                    $this.spSet('fps', 0);
                }
                $._spritely.instances[el_id]['_stopped'] = true;
                $._spritely.instances[el_id]['_stopped_f1'] = bool;
                if (bool) {
                    // set background image position to 0
                    var bp_top = $._spritely.getBgY($(this));
                    $this.css('background-position', '0 ' + bp_top);
                }
            });
            return this;
        },
        spStart: function() {
            $(this).each(function() {
                var el_id = $(this).attr('id');
                var fps = $._spritely.instances[el_id]['_last_fps'] || 12;
                if ($._spritely.instances[el_id]['type'] == 'sprite') {
                    $(this).spSet('fps', fps);
                }
                $._spritely.instances[el_id]['_stopped'] = false;
            });
            return this;
        },
        spToggle: function() {
            var el_id = $(this).attr('id');
            var stopped = $._spritely.instances[el_id]['_stopped'] || false;
            var stopped_f1 = $._spritely.instances[el_id]['_stopped_f1'] || false;
            if (stopped) {
                $(this).spStart();
            } else {
                $(this).spStop(stopped_f1);
            }
            return this;
        },
        fps: function(fps) {
            $(this).each(function() {
                $(this).spSet('fps', fps);
            });
            return this;
        },
        goToFrame: function(n) {
            var el_id = $(this).attr('id');
            if ($._spritely.instances && $._spritely.instances[el_id]) {
                $._spritely.instances[el_id]['current_frame'] = n - 1;
            }
            return this;
        },
        spSpeed: function(speed) {
            $(this).each(function() {
                $(this).spSet('speed', speed);
            });
            return this;
        },
        spRelSpeed: function(speed) {
            $(this).each(function() {
                var rel_depth = $(this).spGet('depth') / 100;
                $(this).spSet('speed', speed * rel_depth);
            });
            return this;
        },
        spChangeDir: function(dir) {
            $(this).each(function() {
                $(this).spSet('dir', dir);
            });
            return this;
        },
        spState: function(n) {
            $(this).each(function() {
                // change state of a sprite, where state is the vertical
                // position of the background image (e.g. frames row)
                var yPos = ((n - 1) * $(this).height()) + 'px';
                var xPos = $._spritely.getBgX($(this));
                var bp = xPos + ' -' + yPos;
                $(this).css('background-position', bp);
            });
            return this;
        },
        lockTo: function(el, options) {
            $(this).each(function() {
                var el_id = $(this).attr('id');
                if (!$._spritely.instances[el_id]) {
                    return this;
                }
                $._spritely.instances[el_id]['locked_el'] = $(this);
                $._spritely.instances[el_id]['lock_to'] = $(el);
                $._spritely.instances[el_id]['lock_to_options'] = options;
                $._spritely.instances[el_id]['interval'] = window.setInterval(function() {
                    if ($._spritely.instances[el_id]['lock_to']) {
                        var locked_el = $._spritely.instances[el_id]['locked_el'];
                        var locked_to_el = $._spritely.instances[el_id]['lock_to'];
                        var locked_to_options = $._spritely.instances[el_id]['lock_to_options'];
                        var locked_to_el_w = locked_to_options.bg_img_width;
                        var locked_to_el_h = locked_to_el.height();
                        var locked_to_el_y = $._spritely.getBgY(locked_to_el);
                        var locked_to_el_x = $._spritely.getBgX(locked_to_el);
                        var el_l = (parseInt(locked_to_el_x) + parseInt(locked_to_options['left']));
                        var el_t = (parseInt(locked_to_el_y) + parseInt(locked_to_options['top']));
                        el_l = $._spritely.get_rel_pos(el_l, locked_to_el_w);
                        $(locked_el).css({
                            'top': el_t + 'px',
                            'left': el_l + 'px'
                        });
                    }
                }, options.interval || 20);
            });
            return this;
        },
        destroy: function() {
            var el = $(this);
            var el_id = $(this).attr('id');
            if ($._spritely.instances[el_id] && $._spritely.instances[el_id]['timeout']){
                window.clearTimeout($._spritely.instances[el_id]['timeout']);
            }
            if ($._spritely.instances[el_id] && $._spritely.instances[el_id]['interval']) {
                window.clearInterval($._spritely.instances[el_id]['interval']);
            }
            delete $._spritely.instances[el_id]
            return this;
        }
    })
})(jQuery);
// Stop IE6 re-loading background images continuously
try {
  document.execCommand("BackgroundImageCache", false, true);
} catch(err) {} 


/*! jqueryanimatesprite - v1.3.5 - 2014-10-17
* http://blaiprat.github.io/jquery.animateSprite/
* Copyright (c) 2014 blai Pratdesaba; Licensed MIT */
(function(t,i,n){"use strict";var e=function(i){return this.each(function(){var e=t(this),a=e.data("animateSprite"),r=function(t){var i=e.css("background-image").replace(/url\((['"])?(.*?)\1\)/gi,"$2"),n=new Image;n.onload=function(){var i=n.width,e=n.height;t(i,e)},n.src=i};a||(e.data("animateSprite",{settings:t.extend({width:e.width(),height:e.height(),totalFrames:!1,columns:!1,fps:12,complete:function(){},loop:!1,autoplay:!0},i),currentFrame:0,controlAnimation:function(){var t=function(t,i){return t++,t>=i?this.settings.loop===!0?(t=0,a.controlTimer()):this.settings.complete():a.controlTimer(),t};if(this.settings.animations===n)e.animateSprite("frame",this.currentFrame),this.currentFrame=t.call(this,this.currentFrame,this.settings.totalFrames);else{if(this.currentAnimation===n)for(var i in this.settings.animations){this.currentAnimation=this.settings.animations[i];break}var r=this.currentAnimation[this.currentFrame];e.animateSprite("frame",r),this.currentFrame=t.call(this,this.currentFrame,this.currentAnimation.length)}},controlTimer:function(){var t=1e3/a.settings.fps;a.settings.duration!==n&&(t=a.settings.duration/a.settings.totalFrames),a.interval=setTimeout(function(){a.controlAnimation()},t)}}),a=e.data("animateSprite"),a.settings.columns?a.settings.autoplay&&a.controlTimer():r(function(t,i){if(a.settings.columns=Math.round(t/a.settings.width),!a.settings.totalFrames){var n=Math.round(i/a.settings.height);a.settings.totalFrames=a.settings.columns*n}a.settings.autoplay&&a.controlTimer()}))})},a=function(i){return this.each(function(){if(t(this).data("animateSprite")!==n){var e=t(this),a=e.data("animateSprite"),r=Math.floor(i/a.settings.columns),s=i%a.settings.columns;e.css("background-position",-a.settings.width*s+"px "+-a.settings.height*r+"px")}})},r=function(){return this.each(function(){var i=t(this),n=i.data("animateSprite");clearTimeout(n.interval)})},s=function(){return this.each(function(){var i=t(this),n=i.data("animateSprite");i.animateSprite("stopAnimation"),n.controlTimer()})},o=function(){return this.each(function(){var i=t(this),n=i.data("animateSprite");i.animateSprite("stopAnimation"),n.currentFrame=0,n.controlTimer()})},m=function(i){return this.each(function(){var n=t(this),e=n.data("animateSprite");"string"==typeof i?(n.animateSprite("stopAnimation"),e.settings.animations[i]!==e.currentAnimation&&(e.currentFrame=0,e.currentAnimation=e.settings.animations[i]),e.controlTimer()):(n.animateSprite("stopAnimation"),e.controlTimer())})},c=function(i){return this.each(function(){var n=t(this),e=n.data("animateSprite");e.settings.fps=i})},u={init:e,frame:a,stop:r,resume:s,restart:o,play:m,stopAnimation:r,resumeAnimation:s,restartAnimation:o,fps:c};t.fn.animateSprite=function(i){return u[i]?u[i].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof i&&i?(t.error("Method "+i+" does not exist on jQuery.animateSprite"),n):u.init.apply(this,arguments)}})(jQuery,window);


/**
 * @author Sergey Nikitin (mrnix@ya.ru)
 * @author Andrew Lechev
 * @author Vasily Mamaevsky
 *
 * inspired by and used http://www.artlebedev.ru/tools/technogrette/js/gradient-text/
 * @link mrnix.ru/pxgradient
 * @version 1.0.2
 * @date 30.03.2012
 * @requires jQuery
 *
 * Description:
 * pxgradient is a jQuery plugin that paints text in gradient colors by pixel
 * 
 * Usage:
 * $(selector).pxgradient(options);
 * 
 * options is an object contents configuraton paramenters:
 * {Number} step - Step Color. The smaller the number, the greater the load. Default: 10
 * {Array}  colors - array of hex colors. Default: ["#ffcc00", "#cc0000", "#000000"];
 * {String} dir - gradient direction. "x" - horizontal,  "y" - vertical
 */
(function($){

  $('head').append('<style type="text/css">.sn-pxg .pxg-set{user-select:none;-moz-user-select:none;-webkit-user-select:none;}.sn-pxg span.pxg-source{position:relative;display:inline-block;z-index:2;}.sn-pxg U.pxg-set,.sn-pxg U.pxg-set S,.sn-pxg U.pxg-set S B{left:0;right:0;top:0;bottom:0;height:inherit;width:inherit;position:absolute;display:inline-block;text-decoration:none;font-weight:inherit;}.sn-pxg U.pxg-set S{overflow:hidden;}.sn-pxg U.pxg-set{text-decoration:none;z-index:1;display:inline-block;position:relative;}</style>')

  $.fn.pxgradient=function(options){
    
    var options = $.extend({
      step: 10,
      colors: ["#ffcc00","#cc0000","#000000"],
      dir: "y"
    }, options);

    options.RGBcolors = [];

    for(var i=0;i<options.colors.length;i++){
      options.RGBcolors[i] = hex2Rgb(options.colors[i]);
    }

    return this.each(function(i,e){

      var pxg = $(e);

      if(!pxg.hasClass("sn-pxg")) {

        var pxg_source = pxg.html();
        pxg
          .html('<span class="pxg-source" style="visibility: hidden;">'+pxg_source+'</span>')
          .append('<u class="pxg-set"></u>');

        
        var pxg_set = pxg.find(".pxg-set");
        var pxg_text = pxg.find(".pxg-source");
        var pxg_w = pxg_text.innerWidth();
        var pxg_h = pxg_text.innerHeight();

        pxg_text.hide();
        pxg.addClass("sn-pxg");

        if ( options.dir == "x" ) { var blocksize = pxg_w; }
        else if ( options.dir =="y" ) { var blocksize = pxg_h; }

        var fullsteps = Math.floor( blocksize/options.step );
        var allsteps  = fullsteps;
        var laststep  = ( blocksize-( fullsteps * options.step) );
        
        if( laststep>0 ) { allsteps++; }

        pxg_set.css({ width: pxg_w, height: pxg_h });

        var offleft = 0;
        var pxg_set_html = '';

        if(options.dir == "x"){
          for(var i=0; i<allsteps; i++){
            var color = getColor ( offleft, blocksize );
            pxg_set_html += '<s style="height:'+pxg_h+'px;width:'+options.step+'px;left:'+offleft+'px;color:'+color+'"><b style="left:-'+offleft+'px;width:'+pxg_w+'px;height:'+pxg_h+'px;">'+pxg_source+'</b></s>';
            offleft = offleft + options.step;
          }
        }
        
        else if(options.dir=="y"){
          for(var i=0; i<allsteps; i++){
            var color=getColor(offleft,blocksize);
            pxg_set_html += '<s style="width:'+pxg_w+'px;height:'+options.step+'px;top:'+offleft+'px;color:'+color+'"><b style="top:-'+offleft+'px;height:'+pxg_w+'px;height:'+pxg_h+'px;">'+pxg_source+'</b></s>';
            offleft = offleft + options.step;
          }
        }

        pxg_set.append(pxg_set_html);
      }
    });


    /**
     * @authors Andrew Lechev and Vasily Mamaevsky
     * Convert HEX to RGB.
     * @param {String} hex
     * @return {Array}
     */
    function hex2Rgb(hex){
      if('#'==hex.substr(0,1)){
        hex=hex.substr(1);
      }
      if (3==hex.length){
        hex=hex.substr(0,1)+hex.substr(0,1)+hex.substr(1,1)+hex.substr(1,1)+hex.substr(2,1)+hex.substr(2,1);
      }
      return [parseInt(hex.substr(0,2),16),parseInt(hex.substr(2,2),16),parseInt(hex.substr(4,2),16)];
    }

    /**
     * @authors Andrew Lechev and Vasily Mamaevsky
     * Convert RGB to HEX.
     * @param {String} hex
     * @return {Array}
     */
    function rgb2Hex(rgb){
      var s = '0123456789abcdef';
      return '#'+s.charAt(parseInt(rgb[0]/16))+s.charAt(rgb[0]%16)+s.charAt(parseInt(rgb[1]/16))+s.charAt(rgb[1]%16)+s.charAt(parseInt(rgb[2]/16))+s.charAt(rgb[2]%16);
    }


    /**
     * @authors Andrew Lechev and Vasily Mamaevsky
     * Convert px to color
     */
    function getColor(off,blocksize){
      var fLeft=(off>0)?(off/blocksize):0;
      for (var i=0;i<options.colors.length;i++){
        fStopPosition=(i/(options.colors.length-1));
        fLastPosition=(i>0)?((i-1)/(options.colors.length-1)):0;
        if(fLeft==fStopPosition){
          return options.colors[i];
        }
        else if(fLeft<fStopPosition){
          fCurrentStop=(fLeft-fLastPosition)/(fStopPosition-fLastPosition);
          return getMidColor(options.RGBcolors[i-1],options.RGBcolors[i],fCurrentStop);
        }
      }
      return options.colors[options.colors.length-1];
    }
    
    /**
     * @authors Andrew Lechev and Vasily Mamaevsky
     * Get middle color from start to end
     */
    function getMidColor(aStart,aEnd,fMidStop){
      var aRGBColor=[];
      for (var i=0;i<3;i++){
        aRGBColor[i]=aStart[i]+Math.round((aEnd[i]-aStart[i])*fMidStop)
      }
      return rgb2Hex(aRGBColor)
    }
  }

})(jQuery);

/*
 * debouncedresize: special jQuery event that happens once after a window resize
 *
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery-smartresize
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 *
 * This saved you an hour of work?
 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
 */
(function($) {

var $event = $.event,
	$special,
	resizeTimeout;

$special = $event.special.debouncedresize = {
	setup: function() {
		$( this ).on( "resize", $special.handler );
	},
	teardown: function() {
		$( this ).off( "resize", $special.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments,
			dispatch = function() {
				// set correct event type
				event.type = "debouncedresize";
				$event.dispatch.apply( context, args );
			};

		if ( resizeTimeout ) {
			clearTimeout( resizeTimeout );
		}

		execAsap ?
			dispatch() :
			resizeTimeout = setTimeout( dispatch, $special.threshold );
	},
	threshold: 150
};

})(jQuery);

/*
 * throttledresize: special jQuery event that happens at a reduced rate compared to "resize"
 *
 * latest version and complete README available on Github:
 * https://github.com/louisremi/jquery-smartresize
 *
 * Copyright 2012 @louis_remi
 * Licensed under the MIT license.
 *
 * This saved you an hour of work?
 * Send me music http://www.amazon.co.uk/wishlist/HNTU0468LQON
 */
(function($) {

var $event = $.event,
	$special,
	dummy = {_:0},
	frame = 0,
	wasResized, animRunning;

$special = $event.special.throttledresize = {
	setup: function() {
		$( this ).on( "resize", $special.handler );
	},
	teardown: function() {
		$( this ).off( "resize", $special.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments;

		wasResized = true;

		if ( !animRunning ) {
			setInterval(function(){
				frame++;

				if ( frame > $special.threshold && wasResized || execAsap ) {
					// set correct event type
					event.type = "throttledresize";
					$event.dispatch.apply( context, args );
					wasResized = false;
					frame = 0;
				}
				if ( frame > 9 ) {
					$(dummy).stop();
					animRunning = false;
					frame = 0;
				}
			}, 30);
			animRunning = true;
		}
	},
	threshold: 0
};

})(jQuery);

/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 *
 * Open source under the BSD License.
 *
 * Copyright © 2008 George McGinley Smith
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

/*
 *
 * TERMS OF USE - EASING EQUATIONS
 *
 * Open source under the BSD License.
 *
 * Copyright © 2001 Robert Penner
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * Neither the name of the author nor the names of contributors may be used to endorse
 * or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

 // webkitdragdrop.js v1.0, Mon May 15 2010
//
// Copyright (c) 2010 Tommaso Buvoli (http://www.tommasobuvoli.com)
// No Extra Libraries are required, simply download this file, add it to your pages!
//
// To See this library in action, grab an ipad and head over to http://www.gotproject.com
// webkitdragdrop is freely distributable under the terms of an MIT-style license.


//Description
// Because this library was designed to run without requiring any other libraries, several basic helper functions were implemented
// 6 helper functons in this webkit_tools class have been taked directly from Prototype 1.6.1 (http://prototypejs.org/) (c) 2005-2009 Sam Stephenson
/*
var webkit_tools =
{
	//$ function - simply a more robust getElementById

	$:function(e)
	{
		if(typeof(e) == 'string')
		{
			return document.getElementById(e);
		}
		return e;
	},

	//extend function - copies the values of b into a (Shallow copy)

	extend:function(a,b)
	{
		for (var key in b)
		{
			a[key] = b[key];
		}
		return a;
	},

	//empty function - used as defaut for events

	empty:function()
	{

	},

	//remove null values from an array

	compact:function(a)
	{
		var b = []
		var l = a.length;
		for(var i = 0; i < l; i ++)
		{
			if(a[i] !== null)
			{
				b.push(a[i]);
			}
		}
		return b;
	},

	//DESCRIPTION
	//	This function was taken from the internet (http://robertnyman.com/2006/04/24/get-the-rendered-style-of-an-element/) and returns
	//	the computed style of an element independantly from the browser
	//INPUT
	//	oELM (DOM ELEMENT) element whose style should be extracted
	//	strCssRule element

	getCalculatedStyle:function(oElm, strCssRule)
	{
		var strValue = "";
		if(document.defaultView && document.defaultView.getComputedStyle){
			strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
		}
		else if(oElm.currentStyle){
			strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
				return p1.toUpperCase();
			});
			strValue = oElm.currentStyle[strCssRule];
		}
		return strValue;
	},

	//bindAsEventListener function - used to bind events

	bindAsEventListener:function(f,object)
	{
				var __method = f;
				return function(event) {
						__method.call(object, event || window.event);
				};
		},

		//cumulative offset - courtesy of Prototype (http://www.prototypejs.org)

		cumulativeOffset:function(element)
		{
			var valueT = 0, valueL = 0;
			do {
				valueT += element.offsetTop  || 0;
				valueL += element.offsetLeft || 0;
				if (element.offsetParent == document.body)
					if (element.style.position == 'absolute') break;

				element = element.offsetParent;
			} while (element);

			return {left : valueL, top : valueT};
		},

		//getDimensions - courtesy of Prototype (http://www.prototypejs.org)

	getDimensions: function(element)
	{
			var display = element.style.display;
			if (display != 'none' && display != null) // Safari bug
				return {width: element.offsetWidth, height: element.offsetHeight};

			var els = element.style;
			var originalVisibility = els.visibility;
			var originalPosition = els.position;
			var originalDisplay = els.display;
			els.visibility = 'hidden';
			if (originalPosition != 'fixed') // Switching fixed to absolute causes issues in Safari
				els.position = 'absolute';
			els.display = 'block';
			var originalWidth = element.clientWidth;
			var originalHeight = element.clientHeight;
			els.display = originalDisplay;
			els.position = originalPosition;
			els.visibility = originalVisibility;
			return {width: originalWidth, height: originalHeight};
	},

	//hasClassName - courtesy of Prototype (http://www.prototypejs.org)

	hasClassName: function(element, className)
	{
		var elementClassName = element.className;
		return (elementClassName.length > 0 && (elementClassName == className ||
		new RegExp("(^|\\s)" + className + "(\\s|$)").test(elementClassName)));
		},

	//addClassName - courtesy of Prototype (http://www.prototypejs.org)

	addClassName: function(element, className)
	{
		if (!this.hasClassName(element, className))
			element.className += (element.className ? ' ' : '') + className;
		return element;
	},

	//removeClassName - courtesy of Prototype (http://www.prototypejs.org)

	removeClassName: function(element, className)
	{
		element.className = this.strip(element.className.replace(new RegExp("(^|\\s+)" + className + "(\\s+|$)"), ' '));
		return element;
	},

	//strip - courtesy of Prototype (http://www.prototypejs.org)

	strip:function(s)
	{
			return s.replace(/^\s+/, '').replace(/\s+$/, '');
		}

}

//Description
// Droppable fire events when a draggable is dropped on them

var webkit_droppables = function()
{
	this.initialize = function()
	{
		this.droppables = [];
		this.droppableRegions = [];
	}

	this.add = function(root, instance_props)
	{
		root = webkit_tools.$(root);
		var default_props = {accept : [], hoverClass : null, onDrop : webkit_tools.empty, onOver : webkit_tools.empty, onOut : webkit_tools.empty};
		default_props = webkit_tools.extend(default_props, instance_props || {});
		this.droppables.push({r : root, p : default_props});
	}

	this.remove = function(root)
	{
		root = webkit_tools.$(root);
		var d = this.droppables;
		var i = d.length;
		while(i--)
		{
			if(d[i].r == root)
			{
				d[i] = null;
				this.droppables = webkit_tools.compact(d);
				return true;
			}
		}
		return false;
	}

	//calculate position and size of all droppables

	this.prepare = function()
	{
		var d = this.droppables;
		var i = d.length;
		var dR = [];
		var r = null;

		while(i--)
		{
			r = d[i].r;
			if(r.style.display != 'none')
			{
				dR.push({i : i, size : webkit_tools.getDimensions(r), offset : webkit_tools.cumulativeOffset(r)})
			}
		}

		this.droppableRegions = dR;
	}

	this.finalize = function(x,y,r,e)
	{
		var indices = this.isOver(x,y);
		var index = this.maxZIndex(indices);
		var over = this.process(index,r);
		if(over)
		{
			this.drop(index, r,e);
		}
		this.process(-1,r);
		return over;
	}

	this.check = function(x,y,r)
	{
		var indices = this.isOver(x,y);
		var index = this.maxZIndex(indices);
		return this.process(index,r);
	}

	this.isOver = function(x, y)
	{
		var dR = this.droppableRegions;
		var i = dR.length;
		var active = [];
		var r = 0;
		var maxX = 0;
		var minX = 0;
		var maxY = 0;
		var minY = 0;

		while(i--)
		{
			r = dR[i];

			minY = r.offset.top;
			maxY = minY + r.size.height;

			if((y > minY) && (y < maxY))
			{
				minX = r.offset.left;
				maxX = minX + r.size.width;

				if((x > minX) && (x < maxX))
				{
					active.push(r.i);
				}
			}
		}

		return active;
	}

	this.maxZIndex = function(indices)
	{
		var d = this.droppables;
		var l = indices.length;
		var index = -1;

		var maxZ = -100000000;
		var curZ = 0;

		while(l--)
		{
			curZ = parseInt(d[indices[l]].r.style.zIndex || 0);
			if(curZ > maxZ)
			{
				maxZ = curZ;
				index = indices[l];
			}
		}

		return index;
	}

	this.process = function(index, draggableRoot)
	{
		//only perform update if a change has occured
		if(this.lastIndex != index)
		{
			//remove previous
			if(this.lastIndex != null)
			{
				var d = this.droppables[this.lastIndex]
				var p = d.p;
				var r = d.r;

				if(p.hoverClass)
				{
					webkit_tools.removeClassName(r,p.hoverClass);
				}
				p.onOut();
				this.lastIndex = null;
				this.lastOutput = false;
			}

			//add new
			if(index != -1)
			{
				var d = this.droppables[index]
				var p = d.p;
				var r = d.r;

				if(this.hasClassNames(draggableRoot, p.accept))
				{
					if(p.hoverClass)
					{
						webkit_tools.addClassName(r,p.hoverClass);
					}
					p.onOver();
					this.lastIndex = index;
					this.lastOutput = true;
				}
			}
		}
		return this.lastOutput;
	}

	this.drop = function(index, r, e)
	{
		if(index != -1)
		{
			this.droppables[index].p.onDrop(r,e);
		}
	}

	this.hasClassNames = function(r, names)
	{
		var l = names.length;
		if(l == 0){return true}
		while(l--)
		{
			if(webkit_tools.hasClassName(r,names[l]))
			{
				return true;
			}
		}
		return false;
	}

	this.initialize();
}

webkit_drop = new webkit_droppables();

//Description
//webkit draggable - allows users to drag elements with their hands

var webkit_draggable = function(r, ip)
{
	this.initialize = function(root, instance_props)
	{
		this.root = webkit_tools.$(root);
		var default_props = {scroll : false, revert : false, handle : this.root, zIndex : 1000, onStart : webkit_tools.empty, onDrag:webkit_tools.empty, onEnd : webkit_tools.empty};

		this.p = webkit_tools.extend(default_props, instance_props || {});
		default_props.handle = webkit_tools.$(default_props.handle);
		this.prepare();
		this.bindEvents();
	}

	this.prepare = function()
	{
		var rs = this.root.style;

		//set position
		if(webkit_tools.getCalculatedStyle(this.root,'position') != 'absolute')
		{
			rs.position = 'relative';
		}

		//set top, right, bottom, left
		rs.top = rs.top || '0px';
		rs.left = rs.left || '0px';
		rs.right = "";
		rs.bottom = "";

		//set zindex;
		rs.zIndex = rs.zIndex || '0';
	}

	this.bindEvents = function()
	{
		var handle = this.p.handle;

		this.ts = webkit_tools.bindAsEventListener(this.touchStart, this);
		this.tm = webkit_tools.bindAsEventListener(this.touchMove, this);
		this.te = webkit_tools.bindAsEventListener(this.touchEnd, this);

		handle.addEventListener("touchstart", this.ts, false);
		handle.addEventListener("touchmove", this.tm, false);
		handle.addEventListener("touchend", this.te, false);
	}

	this.destroy = function()
	{
		var handle = this.p.handle;

		handle.removeEventListener("touchstart", this.ts);
		handle.removeEventListener("touchmove", this.tm);
		handle.removeEventListener("touchend", this.te);
	}

	this.set = function(key, value)
	{
		this.p[key] = value;
	}

	this.touchStart = function(event)
	{
		//prepare needed variables
		var p = this.p;
		var r = this.root;
		var rs = r.style;
		var t = event.targetTouches[0];

		//get position of touch
		touchX = t.pageX;
		touchY = t.pageY;

		//set base values for position of root
		rs.top = this.root.style.top || '0px';
		rs.left = this.root.style.left || '0px';
		rs.bottom = null;
		rs.right = null;

		var rootP = webkit_tools.cumulativeOffset(r);
		var cp = this.getPosition();

		//save event properties
		p.rx = cp.x;
		p.ry = cp.y;
		p.tx = touchX;
		p.ty = touchY;
		p.z = parseInt(this.root.style.zIndex);

		//boost zIndex
		rs.zIndex = p.zIndex;
		webkit_drop.prepare();
		p.onStart({x:t.pageX, y: t.pageY});
	}

	this.touchMove = function(event)
	{
		event.preventDefault();
		event.stopPropagation();

		//prepare needed variables
		var p = this.p;
		var r = this.root;
		var rs = r.style;
		var t = event.targetTouches[0];
		if(t == null){return}

		var curX = t.pageX;
		var curY = t.pageY;

		var delX = curX - p.tx;
		var delY = curY - p.ty;

		rs.left = p.rx + delX + 'px';
		rs.top  = p.ry + delY + 'px';

		//scroll window
		if(p.scroll)
		{
			s = this.getScroll(curX, curY);
			if((s[0] != 0) || (s[1] != 0))
			{
				window.scrollTo(window.scrollX + s[0], window.scrollY + s[1]);
			}
		}

		//check droppables
		webkit_drop.check(curX, curY, r);

		//save position for touchEnd
		this.lastCurX = curX;
		this.lastCurY = curY;

		this.p.onDrag({x:t.pageX, y: t.pageY});
	}

	this.touchEnd = function(event)
	{
		var r = this.root;
		var p = this.p;
		var dropped = webkit_drop.finalize(this.lastCurX, this.lastCurY, r, event);

		if(((p.revert) && (!dropped)) || (p.revert === 'always'))
		{
			//revert root
			var rs = r.style;
			rs.top = (p.ry + 'px');
			rs.left = (p.rx + 'px');
		}

		r.style.zIndex = this.p.z;
		this.p.onEnd({y:p.ry, x:p.rx});
	}

	this.getPosition = function()
	{
		var rs = this.root.style;
		return {x : parseInt(rs.left || 0), y : parseInt(rs.top  || 0)}
	}

	this.getScroll = function(pX, pY)
	{
		//read window variables
		var sX = window.scrollX;
		var sY = window.scrollY;

		var wX = window.innerWidth;
		var wY = window.innerHeight;

		//set contants
		var scroll_amount = 10; //how many pixels to scroll
		var scroll_sensitivity = 100; //how many pixels from border to start scrolling from.

		var delX = 0;
		var delY = 0;

		//process vertical y scroll
		if(pY - sY < scroll_sensitivity)
		{
			delY = -scroll_amount;
		}
		else
		if((sY + wY) - pY < scroll_sensitivity)
		{
			delY = scroll_amount;
		}

		//process horizontal x scroll
		if(pX - sX < scroll_sensitivity)
		{
			delX = -scroll_amount;
		}
		else
		if((sX + wX) - pX < scroll_sensitivity)
		{
			delX = scroll_amount;
		}

		return [delX, delY]
	}

	//contructor
	this.initialize(r, ip);
}

//Description
//webkit_click class. manages click events for draggables

var webkit_click = function(r, ip)
{
	this.initialize = function(root, instance_props)
	{
		var default_props = {onClick : webkit_tools.empty};

		this.root = webkit_tools.$(root);
		this.p = webkit_tools.extend(default_props, instance_props || {});
		this.bindEvents();
	}

	this.bindEvents = function()
	{
		var root = this.root;

		//bind events to local scope
		this.ts = webkit_tools.bindAsEventListener(this.touchStart,this);
		this.tm = webkit_tools.bindAsEventListener(this.touchMove,this);
		this.te = webkit_tools.bindAsEventListener(this.touchEnd,this);

		//add Listeners
		root.addEventListener("touchstart", this.ts, false);
		root.addEventListener("touchmove", this.tm, false);
		root.addEventListener("touchend", this.te, false);

		this.bound = true;
	}

	this.touchStart = function()
	{
		this.moved = false;
		if(this.bound == false)
		{
			this.root.addEventListener("touchmove", this.tm, false);
			this.bound = true;
		}
	}

	this.touchMove = function()
	{
		this.moved = true;
		this.root.removeEventListener("touchmove", this.tm);
		this.bound = false;
	}

	this.touchEnd = function()
	{
		if(this.moved == false)
		{
			this.p.onClick();
		}
	}

	this.setEvent = function(f)
	{
		if(typeof(f) == 'function')
		{
			this.p.onClick = f;
		}
	}

	this.unbind = function()
	{
		var root = this.root;
		root.removeEventListener("touchstart", this.ts);
		root.removeEventListener("touchmove", this.tm);
		root.removeEventListener("touchend", this.te);
	}

	//call constructor
	this.initialize(r, ip);
}
*/

/*************************
 * Croppie
 * Copyright 2016
 * Foliotek
 * Version: 2.1.1
 *************************/
!function(e,t){"function"==typeof define&&define.amd?define(["exports"],t):t("object"==typeof exports&&"string"!=typeof exports.nodeName?exports:e.commonJsStrict={})}(this,function(exports){function e(e){if(e in z)return e;for(var t=e[0].toUpperCase()+e.slice(1),n=W.length;n--;)if(e=W[n]+t,e in z)return e}function t(e,t){e=e||{};for(var n in t)t[n]&&t[n].constructor&&t[n].constructor===Object?(e[n]=e[n]||{},arguments.callee(e[n],t[n])):e[n]=t[n];return e}function n(e,t,n){var i;return function(){var o=this,r=arguments,a=function(){i=null,n||e.apply(o,r)},s=n&&!i;clearTimeout(i),i=setTimeout(a,t),s&&e.apply(o,r)}}function i(e){if("createEvent"in document){var t=document.createEvent("HTMLEvents");t.initEvent("change",!1,!0),e.dispatchEvent(t)}else e.fireEvent("onchange")}function o(e,t,n){if("string"==typeof t){var i=t;t={},t[i]=n}for(var o in t)e.style[o]=t[o]}function r(e,t){e.classList?e.classList.add(t):e.className+=" "+t}function a(e,t){e.classList?e.classList.remove(t):e.className=e.className.replace(t,"")}function s(e,t){var n,i=t||new Image;return i.src===e?n=new Promise(function(e){e(i)}):(n=new Promise(function(t){"http"===e.substring(0,4).toLowerCase()&&i.setAttribute("crossOrigin","anonymous"),i.onload=function(){setTimeout(function(){t(i)},1)}}),i.src=e),i.style.opacity=0,n}function l(e,t){window.EXIF||t(0),EXIF.getData(e,function(){var e=EXIF.getTag(this,"Orientation");t(e)})}function u(e,t,n){var i=t.width,o=t.height,r=e.getContext("2d");switch(e.width=t.width,e.height=t.height,r.save(),n){case 2:r.translate(i,0),r.scale(-1,1);break;case 3:r.translate(i,o),r.rotate(180*Math.PI/180);break;case 4:r.translate(0,o),r.scale(1,-1);break;case 5:e.width=o,e.height=i,r.rotate(90*Math.PI/180),r.scale(1,-1);break;case 6:e.width=o,e.height=i,r.rotate(90*Math.PI/180),r.translate(0,-o);break;case 7:e.width=o,e.height=i,r.rotate(-90*Math.PI/180),r.translate(-i,o),r.scale(1,-1);break;case 8:e.width=o,e.height=i,r.translate(0,i),r.rotate(-90*Math.PI/180)}r.drawImage(t,0,0,i,o),r.restore()}function c(){var e,t,n,i,a=this,s="croppie-container",l=a.options.viewport.type?"cr-vp-"+a.options.viewport.type:null;a.options.useCanvas=a.options.enableOrientation||h.call(a),a.data={},a.elements={},e=a.elements.boundary=document.createElement("div"),n=a.elements.viewport=document.createElement("div"),t=a.elements.img=document.createElement("img"),i=a.elements.overlay=document.createElement("div"),a.options.useCanvas?(a.elements.canvas=document.createElement("canvas"),a.elements.preview=a.elements.canvas):a.elements.preview=a.elements.img,r(e,"cr-boundary"),o(e,{width:a.options.boundary.width+"px",height:a.options.boundary.height+"px"}),r(n,"cr-viewport"),l&&r(n,l),o(n,{width:a.options.viewport.width+"px",height:a.options.viewport.height+"px"}),r(a.elements.preview,"cr-image"),r(i,"cr-overlay"),a.element.appendChild(e),e.appendChild(a.elements.preview),e.appendChild(n),e.appendChild(i),r(a.element,s),a.options.customClass&&r(a.element,a.options.customClass),g.call(this),a.options.enableZoom&&m.call(a)}function h(){return(this.options.enableExif||this.options.exif)&&window.EXIF}function p(e){this.options.enableZoom&&(this.elements.zoomer.value=X(e,4))}function m(){function e(){d.call(n,{value:parseFloat(o.value),origin:new D(n.elements.preview),viewportRect:n.elements.viewport.getBoundingClientRect(),transform:N.parse(n.elements.preview)})}function t(t){var i,o;i=t.wheelDelta?t.wheelDelta/1200:t.deltaY?t.deltaY/1060:t.detail?t.detail/-60:0,o=n._currentZoom+i,t.preventDefault(),p.call(n,o),e()}var n=this,i=n.elements.zoomerWrap=document.createElement("div"),o=n.elements.zoomer=document.createElement("input");r(i,"cr-slider-wrap"),r(o,"cr-slider"),o.type="range",o.step="0.01",o.value=1,o.style.display=n.options.showZoomer?"":"none",n.element.appendChild(i),i.appendChild(o),n._currentZoom=1,n.elements.zoomer.addEventListener("input",e),n.elements.zoomer.addEventListener("change",e),n.options.mouseWheelZoom&&(n.elements.boundary.addEventListener("mousewheel",t),n.elements.boundary.addEventListener("DOMMouseScroll",t))}function d(e){var t=this,n=e?e.transform:N.parse(t.elements.preview),i=e?e.viewportRect:t.elements.viewport.getBoundingClientRect(),r=e?e.origin:new D(t.elements.preview);if(t._currentZoom=e?e.value:t._currentZoom,n.scale=t._currentZoom,t.options.enforceBoundary){var a=f.call(t,i),s=a.translate,l=a.origin;n.x>=s.maxX&&(r.x=l.minX,n.x=s.maxX),n.x<=s.minX&&(r.x=l.maxX,n.x=s.minX),n.y>=s.maxY&&(r.y=l.minY,n.y=s.maxY),n.y<=s.minY&&(r.y=l.maxY,n.y=s.minY)}var u={};u[P]=n.toString(),u[S]=r.toString(),o(t.elements.preview,u),H.call(t),y.call(t)}function f(e){var t=this,n=t._currentZoom,i=e.width,o=e.height,r=t.options.boundary.width/2,a=t.options.boundary.height/2,s=t.elements.preview.getBoundingClientRect(),l=s.width,u=s.height,c=i/2,h=o/2,p=-1*(c/n-r),m=p-(l*(1/n)-i*(1/n)),d=-1*(h/n-a),f=d-(u*(1/n)-o*(1/n)),v=1/n*c,g=l*(1/n)-v,w=1/n*h,y=u*(1/n)-w;return{translate:{maxX:p,minX:m,maxY:d,minY:f},origin:{maxX:g,minX:v,maxY:y,minY:w}}}function v(){var e=this,t=e._currentZoom,n=e.elements.preview.getBoundingClientRect(),i=e.elements.viewport.getBoundingClientRect(),r=N.parse(e.elements.preview.style[P]),a=new D(e.elements.preview),s=i.top-n.top+i.height/2,l=i.left-n.left+i.width/2,u={},c={};u.y=s/t,u.x=l/t,c.y=(u.y-a.y)*(1-t),c.x=(u.x-a.x)*(1-t),r.x-=c.x,r.y-=c.y;var h={};h[S]=u.x+"px "+u.y+"px",h[P]=r.toString(),o(e.elements.preview,h)}function g(){function e(e){if(e.preventDefault(),!c){if(c=!0,r=e.pageX,a=e.pageY,e.touches){var i=e.touches[0];r=i.pageX,a=i.pageY}transform=N.parse(u.elements.preview),window.addEventListener("mousemove",t),window.addEventListener("touchmove",t),window.addEventListener("mouseup",n),window.addEventListener("touchend",n),document.body.style[O]="none",l=u.elements.viewport.getBoundingClientRect()}}function t(e){e.preventDefault();var t=e.pageX,n=e.pageY;if(e.touches){var c=e.touches[0];t=c.pageX,n=c.pageY}var h=t-r,m=n-a,d=u.elements.preview.getBoundingClientRect(),f=transform.y+m,v=transform.x+h,g={};if("touchmove"==e.type&&e.touches.length>1){var y=e.touches[0],x=e.touches[1],b=Math.sqrt((y.pageX-x.pageX)*(y.pageX-x.pageX)+(y.pageY-x.pageY)*(y.pageY-x.pageY));s||(s=b/u._currentZoom);var C=b/s;return p.call(u,C),void i(u.elements.zoomer)}u.options.enforceBoundary?(l.top>d.top+m&&l.bottom<d.bottom+m&&(transform.y=f),l.left>d.left+h&&l.right<d.right+h&&(transform.x=v)):(transform.y=f,transform.x=v),g[P]=transform.toString(),o(u.elements.preview,g),w.call(u),a=n,r=t}function n(){c=!1,window.removeEventListener("mousemove",t),window.removeEventListener("touchmove",t),window.removeEventListener("mouseup",n),window.removeEventListener("touchend",n),document.body.style[O]="",v.call(u),y.call(u),s=0}var r,a,s,l,u=this,c=!1;u.elements.overlay.addEventListener("mousedown",e),u.elements.overlay.addEventListener("touchstart",e)}function w(){var e=this,t=e.elements.boundary.getBoundingClientRect(),n=e.elements.preview.getBoundingClientRect();o(e.elements.overlay,{width:n.width+"px",height:n.height+"px",top:n.top-t.top+"px",left:n.left-t.left+"px"})}function y(){var e=this;x.call(e)&&e.options.update.call(e,e.get())}function x(){return this.elements.preview.offsetHeight>0&&this.elements.preview.offsetWidth>0}function b(){var e,t,n,r,a,s=this,l=0,u=1.5,c=1,h={},m=s.elements.preview,d=s.elements.zoomer,f=new N(0,0,c),g=new D,y=x.call(s);y&&!s.data.bound&&(s.data.bound=!0,h[P]=f.toString(),h[S]=g.toString(),h.opacity=1,o(m,h),e=m.getBoundingClientRect(),t=s.elements.viewport.getBoundingClientRect(),n=s.elements.boundary.getBoundingClientRect(),s._originalImageWidth=e.width,s._originalImageHeight=e.height,s.options.enableZoom&&(s.options.enforceBoundary&&(r=t.width/e.width,a=t.height/e.height,l=Math.max(r,a)),l>=u&&(u=l+1),d.min=X(l,4),d.max=X(u,4),c=Math.max(n.width/e.width,n.height/e.height),p.call(s,c),i(d)),s._currentZoom=f.scale=c,h[P]=f.toString(),o(m,h),s.data.points.length?C.call(s,s.data.points):E.call(s),v.call(s),w.call(s))}function C(e){if(4!=e.length)throw"Croppie - Invalid number of points supplied: "+e;var t=this,n=e[2]-e[0],i=t.elements.viewport.getBoundingClientRect(),r=t.elements.boundary.getBoundingClientRect(),a={left:i.left-r.left,top:i.top-r.top},s=i.width/n,l=e[1],u=e[0],c=-1*e[1]+a.top,h=-1*e[0]+a.left,m={};m[S]=u+"px "+l+"px",m[P]=new N(h,c,s).toString(),o(t.elements.preview,m),p.call(t,s),t._currentZoom=s}function E(){var e=this,t=e.elements.preview.getBoundingClientRect(),n=e.elements.viewport.getBoundingClientRect(),i=e.elements.boundary.getBoundingClientRect(),r=n.left-i.left,a=n.top-i.top,s=r-(t.width-n.width)/2,l=a-(t.height-n.height)/2,u=new N(s,l,e._currentZoom);o(e.elements.preview,P,u.toString())}function _(e){var t=this,n=t.elements.canvas,i=t.elements.img,o=n.getContext("2d"),r=h.call(t),e=t.options.enableOrientation&&e;o.clearRect(0,0,n.width,n.height),n.width=i.width,n.height=i.height,r?l(i,function(t){u(n,i,parseInt(t)),e&&u(n,i,e)}):e&&u(n,i,e)}function I(e){var t=e.points,n=document.createElement("div"),i=document.createElement("img"),a=t[2]-t[0],s=t[3]-t[1];return r(n,"croppie-result"),n.appendChild(i),o(i,{left:-1*t[0]+"px",top:-1*t[1]+"px"}),i.src=e.url,o(n,{width:a+"px",height:s+"px"}),n}function R(e,t){var n=t.points,i=n[0],o=n[1],r=n[2]-n[0],a=n[3]-n[1],s=t.circle,l=document.createElement("canvas"),u=l.getContext("2d"),c=r,h=a;return t.outputWidth&&t.outputHeight&&(c=t.outputWidth,h=t.outputHeight),l.width=c,l.height=h,u.drawImage(e,i,o,r,a,0,0,c,h),s&&(u.fillStyle="#fff",u.globalCompositeOperation="destination-in",u.beginPath(),u.arc(c/2,h/2,c/2,0,2*Math.PI,!0),u.closePath(),u.fill()),l.toDataURL(t.format,t.quality)}function B(e,t){var n,i=this,o=[];if("string"==typeof e)n=e,e={};else if(Array.isArray(e))o=e.slice();else{if("undefined"==typeof e&&i.data.url)return b.call(i),y.call(i),null;n=e.url,o=e.points||[]}i.data.bound=!1,i.data.url=n||i.data.url,i.data.points=(o||i.data.points).map(function(e){return parseFloat(e)});var r=s(n,i.elements.img);return r.then(function(){i.options.useCanvas&&(i.elements.img.exifdata=null,_.call(i,e.orientation||1)),b.call(i),y.call(i),t&&t()}),r}function X(e,t){return parseFloat(e).toFixed(t||0)}function Z(){var e=this,t=e.elements.preview.getBoundingClientRect(),n=e.elements.viewport.getBoundingClientRect(),i=n.left-t.left,o=n.top-t.top,r=i+n.width,a=o+n.height,s=e._currentZoom;(s===1/0||isNaN(s))&&(s=1);var l=e.options.enforceBoundary?0:Number.NEGATIVE_INFINITY;return i=Math.max(l,i/s),o=Math.max(l,o/s),r=Math.max(l,r/s),a=Math.max(l,a/s),{points:[X(i),X(o),X(r),X(a)],zoom:s}}function L(e){var n,i=this,o=Z.call(i),r=t(k,t({},e)),a="string"==typeof e?e:r.type||"viewport",s=r.size,l=r.format,u=r.quality,c=i.elements.viewport.getBoundingClientRect(),h=c.width/c.height;return"viewport"===s?(o.outputWidth=c.width,o.outputHeight=c.height):"object"==typeof s&&(s.width&&s.height?(o.outputWidth=s.width,o.outputHeight=s.height):s.width?(o.outputWidth=s.width,o.outputHeight=s.width/h):s.height&&(o.outputWidth=s.height*h,o.outputHeight=s.height)),q.indexOf(l)>-1&&(o.format="image/"+l,o.quality=u),o.circle="circle"===i.options.viewport.type,o.url=i.data.url,n=new Promise(function(e){e("canvas"===a?R.call(i,i.elements.preview,o):I.call(i,o))})}function Y(){b.call(this)}function j(e){if(!this.options.useCanvas)throw"Croppie: Cannot rotate without enableOrientation";var t=this,n=t.elements.canvas,i=(t.elements.img,document.createElement("canvas")),o=1;i.width=n.width,i.height=n.height;var r=i.getContext("2d");r.drawImage(n,0,0),(90===e||-270===e)&&(o=6),(-90===e||270===e)&&(o=8),(180===e||-180===e)&&(o=3),u(n,i,o),d.call(t)}function F(){var e=this;e.element.removeChild(e.elements.boundary),a(e.element,"croppie-container"),e.options.enableZoom&&e.element.removeChild(e.elements.zoomerWrap),delete e.elements}function M(e,n){if(this.element=e,this.options=t(t({},M.defaults),n),c.call(this),this.options.url){var i={url:this.options.url,points:this.options.points};delete this.options.url,delete this.options.points,B.call(this,i)}}"function"!=typeof Promise&&!function(e){function t(e,t){return function(){e.apply(t,arguments)}}function n(e){if("object"!=typeof this)throw new TypeError("Promises must be constructed via new");if("function"!=typeof e)throw new TypeError("not a function");this._state=null,this._value=null,this._deferreds=[],l(e,t(o,this),t(r,this))}function i(e){var t=this;return null===this._state?void this._deferreds.push(e):void c(function(){var n=t._state?e.onFulfilled:e.onRejected;if(null===n)return void(t._state?e.resolve:e.reject)(t._value);var i;try{i=n(t._value)}catch(o){return void e.reject(o)}e.resolve(i)})}function o(e){try{if(e===this)throw new TypeError("A promise cannot be resolved with itself.");if(e&&("object"==typeof e||"function"==typeof e)){var n=e.then;if("function"==typeof n)return void l(t(n,e),t(o,this),t(r,this))}this._state=!0,this._value=e,a.call(this)}catch(i){r.call(this,i)}}function r(e){this._state=!1,this._value=e,a.call(this)}function a(){for(var e=0,t=this._deferreds.length;t>e;e++)i.call(this,this._deferreds[e]);this._deferreds=null}function s(e,t,n,i){this.onFulfilled="function"==typeof e?e:null,this.onRejected="function"==typeof t?t:null,this.resolve=n,this.reject=i}function l(e,t,n){var i=!1;try{e(function(e){i||(i=!0,t(e))},function(e){i||(i=!0,n(e))})}catch(o){if(i)return;i=!0,n(o)}}var u=setTimeout,c="function"==typeof setImmediate&&setImmediate||function(e){u(e,1)},h=Array.isArray||function(e){return"[object Array]"===Object.prototype.toString.call(e)};n.prototype["catch"]=function(e){return this.then(null,e)},n.prototype.then=function(e,t){var o=this;return new n(function(n,r){i.call(o,new s(e,t,n,r))})},n.all=function(){var e=Array.prototype.slice.call(1===arguments.length&&h(arguments[0])?arguments[0]:arguments);return new n(function(t,n){function i(r,a){try{if(a&&("object"==typeof a||"function"==typeof a)){var s=a.then;if("function"==typeof s)return void s.call(a,function(e){i(r,e)},n)}e[r]=a,0===--o&&t(e)}catch(l){n(l)}}if(0===e.length)return t([]);for(var o=e.length,r=0;r<e.length;r++)i(r,e[r])})},n.resolve=function(e){return e&&"object"==typeof e&&e.constructor===n?e:new n(function(t){t(e)})},n.reject=function(e){return new n(function(t,n){n(e)})},n.race=function(e){return new n(function(t,n){for(var i=0,o=e.length;o>i;i++)e[i].then(t,n)})},n._setImmediateFn=function(e){c=e},"undefined"!=typeof module&&module.exports?module.exports=n:e.Promise||(e.Promise=n)}(this);var S,P,O,W=["Webkit","Moz","ms"],z=document.createElement("div").style;P=e("transform"),S=e("transformOrigin"),O=e("userSelect");var T="translate3d",A=", 0px",N=function(e,t,n){this.x=parseFloat(e),this.y=parseFloat(t),this.scale=parseFloat(n)};N.parse=function(e){return e.style?N.parse(e.style[P]):e.indexOf("matrix")>-1||e.indexOf("none")>-1?N.fromMatrix(e):N.fromString(e)},N.fromMatrix=function(e){var t=e.substring(7).split(",");return t.length&&"none"!==e||(t=[1,0,0,1,0,0]),new N(parseInt(t[4],10),parseInt(t[5],10),parseFloat(t[0]))},N.fromString=function(e){var t=e.split(") "),n=t[0].substring(T.length+1).split(","),i=t.length>1?t[1].substring(6):1,o=n.length>1?n[0]:0,r=n.length>1?n[1]:0;return new N(o,r,i)},N.prototype.toString=function(){return T+"("+this.x+"px, "+this.y+"px"+A+") scale("+this.scale+")"};var D=function(e){if(!e||!e.style[S])return this.x=0,void(this.y=0);var t=e.style[S].split(" ");this.x=parseFloat(t[0]),this.y=parseFloat(t[1])};D.prototype.toString=function(){return this.x+"px "+this.y+"px"};var H=n(w,500),k={type:"canvas",format:"png",quality:1},q=["jpeg","webp","png"];if(window.jQuery){var $=window.jQuery;$.fn.croppie=function(e){var t=typeof e;if("string"===t){var n=Array.prototype.slice.call(arguments,1),i=$(this).data("croppie");return"get"===e?i.get():"result"===e?i.result.apply(i,n):this.each(function(){var t=$(this).data("croppie");if(t){var i=t[e];if(!$.isFunction(i))throw"Croppie "+e+" method not found";i.apply(t,n),"destroy"===e&&$(this).removeData("croppie")}})}return this.each(function(){var t=new M(this,e);$(this).data("croppie",t)})}}M.defaults={viewport:{width:100,height:100,type:"square"},boundary:{width:300,height:300},orientationControls:{enabled:!0,leftClass:"",rightClass:""},customClass:"",showZoomer:!0,enableZoom:!0,mouseWheelZoom:!0,enableExif:!1,enforceBoundary:!0,enableOrientation:!1,update:function(){}},t(M.prototype,{bind:function(e,t){return B.call(this,e,t)},get:function(){return Z.call(this)},result:function(e){return L.call(this,e)},refresh:function(){return Y.call(this)},setZoom:function(e){p.call(this,e),i(this.elements.zoomer)},rotate:function(e){j.call(this,e)},destroy:function(){return F.call(this)}}),exports.Croppie=window.Croppie=M,"object"==typeof module&&module.exports&&(module.exports=M)});

/**********************
https://github.com/exif-js/exif-jsFcr
**********************/
(function() {

		var debug = false;

		var root = this;

		var EXIF = function(obj) {
				if (obj instanceof EXIF) return obj;
				if (!(this instanceof EXIF)) return new EXIF(obj);
				this.EXIFwrapped = obj;
		};

		if (typeof exports !== 'undefined') {
				if (typeof module !== 'undefined' && module.exports) {
						exports = module.exports = EXIF;
				}
				exports.EXIF = EXIF;
		} else {
				root.EXIF = EXIF;
		}

		var ExifTags = EXIF.Tags = {

				// version tags
				0x9000 : "ExifVersion",             // EXIF version
				0xA000 : "FlashpixVersion",         // Flashpix format version

				// colorspace tags
				0xA001 : "ColorSpace",              // Color space information tag

				// image configuration
				0xA002 : "PixelXDimension",         // Valid width of meaningful image
				0xA003 : "PixelYDimension",         // Valid height of meaningful image
				0x9101 : "ComponentsConfiguration", // Information about channels
				0x9102 : "CompressedBitsPerPixel",  // Compressed bits per pixel

				// user information
				0x927C : "MakerNote",               // Any desired information written by the manufacturer
				0x9286 : "UserComment",             // Comments by user

				// related file
				0xA004 : "RelatedSoundFile",        // Name of related sound file

				// date and time
				0x9003 : "DateTimeOriginal",        // Date and time when the original image was generated
				0x9004 : "DateTimeDigitized",       // Date and time when the image was stored digitally
				0x9290 : "SubsecTime",              // Fractions of seconds for DateTime
				0x9291 : "SubsecTimeOriginal",      // Fractions of seconds for DateTimeOriginal
				0x9292 : "SubsecTimeDigitized",     // Fractions of seconds for DateTimeDigitized

				// picture-taking conditions
				0x829A : "ExposureTime",            // Exposure time (in seconds)
				0x829D : "FNumber",                 // F number
				0x8822 : "ExposureProgram",         // Exposure program
				0x8824 : "SpectralSensitivity",     // Spectral sensitivity
				0x8827 : "ISOSpeedRatings",         // ISO speed rating
				0x8828 : "OECF",                    // Optoelectric conversion factor
				0x9201 : "ShutterSpeedValue",       // Shutter speed
				0x9202 : "ApertureValue",           // Lens aperture
				0x9203 : "BrightnessValue",         // Value of brightness
				0x9204 : "ExposureBias",            // Exposure bias
				0x9205 : "MaxApertureValue",        // Smallest F number of lens
				0x9206 : "SubjectDistance",         // Distance to subject in meters
				0x9207 : "MeteringMode",            // Metering mode
				0x9208 : "LightSource",             // Kind of light source
				0x9209 : "Flash",                   // Flash status
				0x9214 : "SubjectArea",             // Location and area of main subject
				0x920A : "FocalLength",             // Focal length of the lens in mm
				0xA20B : "FlashEnergy",             // Strobe energy in BCPS
				0xA20C : "SpatialFrequencyResponse",    //
				0xA20E : "FocalPlaneXResolution",   // Number of pixels in width direction per FocalPlaneResolutionUnit
				0xA20F : "FocalPlaneYResolution",   // Number of pixels in height direction per FocalPlaneResolutionUnit
				0xA210 : "FocalPlaneResolutionUnit",    // Unit for measuring FocalPlaneXResolution and FocalPlaneYResolution
				0xA214 : "SubjectLocation",         // Location of subject in image
				0xA215 : "ExposureIndex",           // Exposure index selected on camera
				0xA217 : "SensingMethod",           // Image sensor type
				0xA300 : "FileSource",              // Image source (3 == DSC)
				0xA301 : "SceneType",               // Scene type (1 == directly photographed)
				0xA302 : "CFAPattern",              // Color filter array geometric pattern
				0xA401 : "CustomRendered",          // Special processing
				0xA402 : "ExposureMode",            // Exposure mode
				0xA403 : "WhiteBalance",            // 1 = auto white balance, 2 = manual
				0xA404 : "DigitalZoomRation",       // Digital zoom ratio
				0xA405 : "FocalLengthIn35mmFilm",   // Equivalent foacl length assuming 35mm film camera (in mm)
				0xA406 : "SceneCaptureType",        // Type of scene
				0xA407 : "GainControl",             // Degree of overall image gain adjustment
				0xA408 : "Contrast",                // Direction of contrast processing applied by camera
				0xA409 : "Saturation",              // Direction of saturation processing applied by camera
				0xA40A : "Sharpness",               // Direction of sharpness processing applied by camera
				0xA40B : "DeviceSettingDescription",    //
				0xA40C : "SubjectDistanceRange",    // Distance to subject

				// other tags
				0xA005 : "InteroperabilityIFDPointer",
				0xA420 : "ImageUniqueID"            // Identifier assigned uniquely to each image
		};

		var TiffTags = EXIF.TiffTags = {
				0x0100 : "ImageWidth",
				0x0101 : "ImageHeight",
				0x8769 : "ExifIFDPointer",
				0x8825 : "GPSInfoIFDPointer",
				0xA005 : "InteroperabilityIFDPointer",
				0x0102 : "BitsPerSample",
				0x0103 : "Compression",
				0x0106 : "PhotometricInterpretation",
				0x0112 : "Orientation",
				0x0115 : "SamplesPerPixel",
				0x011C : "PlanarConfiguration",
				0x0212 : "YCbCrSubSampling",
				0x0213 : "YCbCrPositioning",
				0x011A : "XResolution",
				0x011B : "YResolution",
				0x0128 : "ResolutionUnit",
				0x0111 : "StripOffsets",
				0x0116 : "RowsPerStrip",
				0x0117 : "StripByteCounts",
				0x0201 : "JPEGInterchangeFormat",
				0x0202 : "JPEGInterchangeFormatLength",
				0x012D : "TransferFunction",
				0x013E : "WhitePoint",
				0x013F : "PrimaryChromaticities",
				0x0211 : "YCbCrCoefficients",
				0x0214 : "ReferenceBlackWhite",
				0x0132 : "DateTime",
				0x010E : "ImageDescription",
				0x010F : "Make",
				0x0110 : "Model",
				0x0131 : "Software",
				0x013B : "Artist",
				0x8298 : "Copyright"
		};

		var GPSTags = EXIF.GPSTags = {
				0x0000 : "GPSVersionID",
				0x0001 : "GPSLatitudeRef",
				0x0002 : "GPSLatitude",
				0x0003 : "GPSLongitudeRef",
				0x0004 : "GPSLongitude",
				0x0005 : "GPSAltitudeRef",
				0x0006 : "GPSAltitude",
				0x0007 : "GPSTimeStamp",
				0x0008 : "GPSSatellites",
				0x0009 : "GPSStatus",
				0x000A : "GPSMeasureMode",
				0x000B : "GPSDOP",
				0x000C : "GPSSpeedRef",
				0x000D : "GPSSpeed",
				0x000E : "GPSTrackRef",
				0x000F : "GPSTrack",
				0x0010 : "GPSImgDirectionRef",
				0x0011 : "GPSImgDirection",
				0x0012 : "GPSMapDatum",
				0x0013 : "GPSDestLatitudeRef",
				0x0014 : "GPSDestLatitude",
				0x0015 : "GPSDestLongitudeRef",
				0x0016 : "GPSDestLongitude",
				0x0017 : "GPSDestBearingRef",
				0x0018 : "GPSDestBearing",
				0x0019 : "GPSDestDistanceRef",
				0x001A : "GPSDestDistance",
				0x001B : "GPSProcessingMethod",
				0x001C : "GPSAreaInformation",
				0x001D : "GPSDateStamp",
				0x001E : "GPSDifferential"
		};

		var StringValues = EXIF.StringValues = {
				ExposureProgram : {
						0 : "Not defined",
						1 : "Manual",
						2 : "Normal program",
						3 : "Aperture priority",
						4 : "Shutter priority",
						5 : "Creative program",
						6 : "Action program",
						7 : "Portrait mode",
						8 : "Landscape mode"
				},
				MeteringMode : {
						0 : "Unknown",
						1 : "Average",
						2 : "CenterWeightedAverage",
						3 : "Spot",
						4 : "MultiSpot",
						5 : "Pattern",
						6 : "Partial",
						255 : "Other"
				},
				LightSource : {
						0 : "Unknown",
						1 : "Daylight",
						2 : "Fluorescent",
						3 : "Tungsten (incandescent light)",
						4 : "Flash",
						9 : "Fine weather",
						10 : "Cloudy weather",
						11 : "Shade",
						12 : "Daylight fluorescent (D 5700 - 7100K)",
						13 : "Day white fluorescent (N 4600 - 5400K)",
						14 : "Cool white fluorescent (W 3900 - 4500K)",
						15 : "White fluorescent (WW 3200 - 3700K)",
						17 : "Standard light A",
						18 : "Standard light B",
						19 : "Standard light C",
						20 : "D55",
						21 : "D65",
						22 : "D75",
						23 : "D50",
						24 : "ISO studio tungsten",
						255 : "Other"
				},
				Flash : {
						0x0000 : "Flash did not fire",
						0x0001 : "Flash fired",
						0x0005 : "Strobe return light not detected",
						0x0007 : "Strobe return light detected",
						0x0009 : "Flash fired, compulsory flash mode",
						0x000D : "Flash fired, compulsory flash mode, return light not detected",
						0x000F : "Flash fired, compulsory flash mode, return light detected",
						0x0010 : "Flash did not fire, compulsory flash mode",
						0x0018 : "Flash did not fire, auto mode",
						0x0019 : "Flash fired, auto mode",
						0x001D : "Flash fired, auto mode, return light not detected",
						0x001F : "Flash fired, auto mode, return light detected",
						0x0020 : "No flash function",
						0x0041 : "Flash fired, red-eye reduction mode",
						0x0045 : "Flash fired, red-eye reduction mode, return light not detected",
						0x0047 : "Flash fired, red-eye reduction mode, return light detected",
						0x0049 : "Flash fired, compulsory flash mode, red-eye reduction mode",
						0x004D : "Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected",
						0x004F : "Flash fired, compulsory flash mode, red-eye reduction mode, return light detected",
						0x0059 : "Flash fired, auto mode, red-eye reduction mode",
						0x005D : "Flash fired, auto mode, return light not detected, red-eye reduction mode",
						0x005F : "Flash fired, auto mode, return light detected, red-eye reduction mode"
				},
				SensingMethod : {
						1 : "Not defined",
						2 : "One-chip color area sensor",
						3 : "Two-chip color area sensor",
						4 : "Three-chip color area sensor",
						5 : "Color sequential area sensor",
						7 : "Trilinear sensor",
						8 : "Color sequential linear sensor"
				},
				SceneCaptureType : {
						0 : "Standard",
						1 : "Landscape",
						2 : "Portrait",
						3 : "Night scene"
				},
				SceneType : {
						1 : "Directly photographed"
				},
				CustomRendered : {
						0 : "Normal process",
						1 : "Custom process"
				},
				WhiteBalance : {
						0 : "Auto white balance",
						1 : "Manual white balance"
				},
				GainControl : {
						0 : "None",
						1 : "Low gain up",
						2 : "High gain up",
						3 : "Low gain down",
						4 : "High gain down"
				},
				Contrast : {
						0 : "Normal",
						1 : "Soft",
						2 : "Hard"
				},
				Saturation : {
						0 : "Normal",
						1 : "Low saturation",
						2 : "High saturation"
				},
				Sharpness : {
						0 : "Normal",
						1 : "Soft",
						2 : "Hard"
				},
				SubjectDistanceRange : {
						0 : "Unknown",
						1 : "Macro",
						2 : "Close view",
						3 : "Distant view"
				},
				FileSource : {
						3 : "DSC"
				},

				Components : {
						0 : "",
						1 : "Y",
						2 : "Cb",
						3 : "Cr",
						4 : "R",
						5 : "G",
						6 : "B"
				}
		};

		function addEvent(element, event, handler) {
				if (element.addEventListener) {
						element.addEventListener(event, handler, false);
				} else if (element.attachEvent) {
						element.attachEvent("on" + event, handler);
				}
		}

		function imageHasData(img) {
				return !!(img.exifdata);
		}


		function base64ToArrayBuffer(base64, contentType) {
				contentType = contentType || base64.match(/^data\:([^\;]+)\;base64,/mi)[1] || ''; // e.g. 'data:image/jpeg;base64,...' => 'image/jpeg'
				base64 = base64.replace(/^data\:([^\;]+)\;base64,/gmi, '');
				var binary = atob(base64);
				var len = binary.length;
				var buffer = new ArrayBuffer(len);
				var view = new Uint8Array(buffer);
				for (var i = 0; i < len; i++) {
						view[i] = binary.charCodeAt(i);
				}
				return buffer;
		}

		function objectURLToBlob(url, callback) {
				var http = new XMLHttpRequest();
				http.open("GET", url, true);
				http.responseType = "blob";
				http.onload = function(e) {
						if (this.status == 200 || this.status === 0) {
								callback(this.response);
						}
				};
				http.send();
		}

		function getImageData(img, callback) {
				function handleBinaryFile(binFile) {
						var data = findEXIFinJPEG(binFile);
						var iptcdata = findIPTCinJPEG(binFile);
						img.exifdata = data || {};
						img.iptcdata = iptcdata || {};
						if (callback) {
								callback.call(img);
						}
				}

				if (img.src) {
						if (/^data\:/i.test(img.src)) { // Data URI
								var arrayBuffer = base64ToArrayBuffer(img.src);
								handleBinaryFile(arrayBuffer);

						} else if (/^blob\:/i.test(img.src)) { // Object URL
								var fileReader = new FileReader();
								fileReader.onload = function(e) {
										handleBinaryFile(e.target.result);
								};
								objectURLToBlob(img.src, function (blob) {
										fileReader.readAsArrayBuffer(blob);
								});
						} else {
								var http = new XMLHttpRequest();
								http.onload = function() {
										if (this.status == 200 || this.status === 0) {
												handleBinaryFile(http.response);
										} else {
												throw "Could not load image";
										}
										http = null;
								};
								http.open("GET", img.src, true);
								http.responseType = "arraybuffer";
								http.send(null);
						}
				} else if (window.FileReader && (img instanceof window.Blob || img instanceof window.File)) {
						var fileReader = new FileReader();
						fileReader.onload = function(e) {
								if (debug) console.log("Got file of length " + e.target.result.byteLength);
								handleBinaryFile(e.target.result);
						};

						fileReader.readAsArrayBuffer(img);
				}
		}

		function findEXIFinJPEG(file) {
				var dataView = new DataView(file);

				if (debug) console.log("Got file of length " + file.byteLength);
				if ((dataView.getUint8(0) != 0xFF) || (dataView.getUint8(1) != 0xD8)) {
						if (debug) console.log("Not a valid JPEG");
						return false; // not a valid jpeg
				}

				var offset = 2,
						length = file.byteLength,
						marker;

				while (offset < length) {
						if (dataView.getUint8(offset) != 0xFF) {
								if (debug) console.log("Not a valid marker at offset " + offset + ", found: " + dataView.getUint8(offset));
								return false; // not a valid marker, something is wrong
						}

						marker = dataView.getUint8(offset + 1);
						if (debug) console.log(marker);

						// we could implement handling for other markers here,
						// but we're only looking for 0xFFE1 for EXIF data

						if (marker == 225) {
								if (debug) console.log("Found 0xFFE1 marker");

								return readEXIFData(dataView, offset + 4, dataView.getUint16(offset + 2) - 2);

								// offset += 2 + file.getShortAt(offset+2, true);

						} else {
								offset += 2 + dataView.getUint16(offset+2);
						}

				}

		}

		function findIPTCinJPEG(file) {
				var dataView = new DataView(file);

				if (debug) console.log("Got file of length " + file.byteLength);
				if ((dataView.getUint8(0) != 0xFF) || (dataView.getUint8(1) != 0xD8)) {
						if (debug) console.log("Not a valid JPEG");
						return false; // not a valid jpeg
				}

				var offset = 2,
						length = file.byteLength;


				var isFieldSegmentStart = function(dataView, offset){
						return (
								dataView.getUint8(offset) === 0x38 &&
								dataView.getUint8(offset+1) === 0x42 &&
								dataView.getUint8(offset+2) === 0x49 &&
								dataView.getUint8(offset+3) === 0x4D &&
								dataView.getUint8(offset+4) === 0x04 &&
								dataView.getUint8(offset+5) === 0x04
						);
				};

				while (offset < length) {

						if ( isFieldSegmentStart(dataView, offset )){

								// Get the length of the name header (which is padded to an even number of bytes)
								var nameHeaderLength = dataView.getUint8(offset+7);
								if(nameHeaderLength % 2 !== 0) nameHeaderLength += 1;
								// Check for pre photoshop 6 format
								if(nameHeaderLength === 0) {
										// Always 4
										nameHeaderLength = 4;
								}

								var startOffset = offset + 8 + nameHeaderLength;
								var sectionLength = dataView.getUint16(offset + 6 + nameHeaderLength);

								return readIPTCData(file, startOffset, sectionLength);

								break;

						}


						// Not the marker, continue searching
						offset++;

				}

		}
		var IptcFieldMap = {
				0x78 : 'caption',
				0x6E : 'credit',
				0x19 : 'keywords',
				0x37 : 'dateCreated',
				0x50 : 'byline',
				0x55 : 'bylineTitle',
				0x7A : 'captionWriter',
				0x69 : 'headline',
				0x74 : 'copyright',
				0x0F : 'category'
		};
		function readIPTCData(file, startOffset, sectionLength){
				var dataView = new DataView(file);
				var data = {};
				var fieldValue, fieldName, dataSize, segmentType, segmentSize;
				var segmentStartPos = startOffset;
				while(segmentStartPos < startOffset+sectionLength) {
						if(dataView.getUint8(segmentStartPos) === 0x1C && dataView.getUint8(segmentStartPos+1) === 0x02){
								segmentType = dataView.getUint8(segmentStartPos+2);
								if(segmentType in IptcFieldMap) {
										dataSize = dataView.getInt16(segmentStartPos+3);
										segmentSize = dataSize + 5;
										fieldName = IptcFieldMap[segmentType];
										fieldValue = getStringFromDB(dataView, segmentStartPos+5, dataSize);
										// Check if we already stored a value with this name
										if(data.hasOwnProperty(fieldName)) {
												// Value already stored with this name, create multivalue field
												if(data[fieldName] instanceof Array) {
														data[fieldName].push(fieldValue);
												}
												else {
														data[fieldName] = [data[fieldName], fieldValue];
												}
										}
										else {
												data[fieldName] = fieldValue;
										}
								}

						}
						segmentStartPos++;
				}
				return data;
		}



		function readTags(file, tiffStart, dirStart, strings, bigEnd) {
				var entries = file.getUint16(dirStart, !bigEnd),
						tags = {},
						entryOffset, tag,
						i;

				for (i=0;i<entries;i++) {
						entryOffset = dirStart + i*12 + 2;
						tag = strings[file.getUint16(entryOffset, !bigEnd)];
						if (!tag && debug) console.log("Unknown tag: " + file.getUint16(entryOffset, !bigEnd));
						tags[tag] = readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd);
				}
				return tags;
		}


		function readTagValue(file, entryOffset, tiffStart, dirStart, bigEnd) {
				var type = file.getUint16(entryOffset+2, !bigEnd),
						numValues = file.getUint32(entryOffset+4, !bigEnd),
						valueOffset = file.getUint32(entryOffset+8, !bigEnd) + tiffStart,
						offset,
						vals, val, n,
						numerator, denominator;

				switch (type) {
						case 1: // byte, 8-bit unsigned int
						case 7: // undefined, 8-bit byte, value depending on field
								if (numValues == 1) {
										return file.getUint8(entryOffset + 8, !bigEnd);
								} else {
										offset = numValues > 4 ? valueOffset : (entryOffset + 8);
										vals = [];
										for (n=0;n<numValues;n++) {
												vals[n] = file.getUint8(offset + n);
										}
										return vals;
								}

						case 2: // ascii, 8-bit byte
								offset = numValues > 4 ? valueOffset : (entryOffset + 8);
								return getStringFromDB(file, offset, numValues-1);

						case 3: // short, 16 bit int
								if (numValues == 1) {
										return file.getUint16(entryOffset + 8, !bigEnd);
								} else {
										offset = numValues > 2 ? valueOffset : (entryOffset + 8);
										vals = [];
										for (n=0;n<numValues;n++) {
												vals[n] = file.getUint16(offset + 2*n, !bigEnd);
										}
										return vals;
								}

						case 4: // long, 32 bit int
								if (numValues == 1) {
										return file.getUint32(entryOffset + 8, !bigEnd);
								} else {
										vals = [];
										for (n=0;n<numValues;n++) {
												vals[n] = file.getUint32(valueOffset + 4*n, !bigEnd);
										}
										return vals;
								}

						case 5:    // rational = two long values, first is numerator, second is denominator
								if (numValues == 1) {
										numerator = file.getUint32(valueOffset, !bigEnd);
										denominator = file.getUint32(valueOffset+4, !bigEnd);
										val = new Number(numerator / denominator);
										val.numerator = numerator;
										val.denominator = denominator;
										return val;
								} else {
										vals = [];
										for (n=0;n<numValues;n++) {
												numerator = file.getUint32(valueOffset + 8*n, !bigEnd);
												denominator = file.getUint32(valueOffset+4 + 8*n, !bigEnd);
												vals[n] = new Number(numerator / denominator);
												vals[n].numerator = numerator;
												vals[n].denominator = denominator;
										}
										return vals;
								}

						case 9: // slong, 32 bit signed int
								if (numValues == 1) {
										return file.getInt32(entryOffset + 8, !bigEnd);
								} else {
										vals = [];
										for (n=0;n<numValues;n++) {
												vals[n] = file.getInt32(valueOffset + 4*n, !bigEnd);
										}
										return vals;
								}

						case 10: // signed rational, two slongs, first is numerator, second is denominator
								if (numValues == 1) {
										return file.getInt32(valueOffset, !bigEnd) / file.getInt32(valueOffset+4, !bigEnd);
								} else {
										vals = [];
										for (n=0;n<numValues;n++) {
												vals[n] = file.getInt32(valueOffset + 8*n, !bigEnd) / file.getInt32(valueOffset+4 + 8*n, !bigEnd);
										}
										return vals;
								}
				}
		}

		function getStringFromDB(buffer, start, length) {
				var outstr = "";
				for (n = start; n < start+length; n++) {
						outstr += String.fromCharCode(buffer.getUint8(n));
				}
				return outstr;
		}

		function readEXIFData(file, start) {
				if (getStringFromDB(file, start, 4) != "Exif") {
						if (debug) console.log("Not valid EXIF data! " + getStringFromDB(file, start, 4));
						return false;
				}

				var bigEnd,
						tags, tag,
						exifData, gpsData,
						tiffOffset = start + 6;

				// test for TIFF validity and endianness
				if (file.getUint16(tiffOffset) == 0x4949) {
						bigEnd = false;
				} else if (file.getUint16(tiffOffset) == 0x4D4D) {
						bigEnd = true;
				} else {
						if (debug) console.log("Not valid TIFF data! (no 0x4949 or 0x4D4D)");
						return false;
				}

				if (file.getUint16(tiffOffset+2, !bigEnd) != 0x002A) {
						if (debug) console.log("Not valid TIFF data! (no 0x002A)");
						return false;
				}

				var firstIFDOffset = file.getUint32(tiffOffset+4, !bigEnd);

				if (firstIFDOffset < 0x00000008) {
						if (debug) console.log("Not valid TIFF data! (First offset less than 8)", file.getUint32(tiffOffset+4, !bigEnd));
						return false;
				}

				tags = readTags(file, tiffOffset, tiffOffset + firstIFDOffset, TiffTags, bigEnd);

				if (tags.ExifIFDPointer) {
						exifData = readTags(file, tiffOffset, tiffOffset + tags.ExifIFDPointer, ExifTags, bigEnd);
						for (tag in exifData) {
								switch (tag) {
										case "LightSource" :
										case "Flash" :
										case "MeteringMode" :
										case "ExposureProgram" :
										case "SensingMethod" :
										case "SceneCaptureType" :
										case "SceneType" :
										case "CustomRendered" :
										case "WhiteBalance" :
										case "GainControl" :
										case "Contrast" :
										case "Saturation" :
										case "Sharpness" :
										case "SubjectDistanceRange" :
										case "FileSource" :
												exifData[tag] = StringValues[tag][exifData[tag]];
												break;

										case "ExifVersion" :
										case "FlashpixVersion" :
												exifData[tag] = String.fromCharCode(exifData[tag][0], exifData[tag][1], exifData[tag][2], exifData[tag][3]);
												break;

										case "ComponentsConfiguration" :
												exifData[tag] =
														StringValues.Components[exifData[tag][0]] +
														StringValues.Components[exifData[tag][1]] +
														StringValues.Components[exifData[tag][2]] +
														StringValues.Components[exifData[tag][3]];
												break;
								}
								tags[tag] = exifData[tag];
						}
				}

				if (tags.GPSInfoIFDPointer) {
						gpsData = readTags(file, tiffOffset, tiffOffset + tags.GPSInfoIFDPointer, GPSTags, bigEnd);
						for (tag in gpsData) {
								switch (tag) {
										case "GPSVersionID" :
												gpsData[tag] = gpsData[tag][0] +
														"." + gpsData[tag][1] +
														"." + gpsData[tag][2] +
														"." + gpsData[tag][3];
												break;
								}
								tags[tag] = gpsData[tag];
						}
				}

				return tags;
		}

		EXIF.getData = function(img, callback) {
				if ((img instanceof Image || img instanceof HTMLImageElement) && !img.complete) return false;

				if (!imageHasData(img)) {
						getImageData(img, callback);
				} else {
						if (callback) {
								callback.call(img);
						}
				}
				return true;
		}

		EXIF.getTag = function(img, tag) {
				if (!imageHasData(img)) return;
				return img.exifdata[tag];
		}

		EXIF.getAllTags = function(img) {
				if (!imageHasData(img)) return {};
				var a,
						data = img.exifdata,
						tags = {};
				for (a in data) {
						if (data.hasOwnProperty(a)) {
								tags[a] = data[a];
						}
				}
				return tags;
		}

		EXIF.pretty = function(img) {
				if (!imageHasData(img)) return "";
				var a,
						data = img.exifdata,
						strPretty = "";
				for (a in data) {
						if (data.hasOwnProperty(a)) {
								if (typeof data[a] == "object") {
										if (data[a] instanceof Number) {
												strPretty += a + " : " + data[a] + " [" + data[a].numerator + "/" + data[a].denominator + "]\r\n";
										} else {
												strPretty += a + " : [" + data[a].length + " values]\r\n";
										}
								} else {
										strPretty += a + " : " + data[a] + "\r\n";
								}
						}
				}
				return strPretty;
		}

		EXIF.readFromBinaryFile = function(file) {
				return findEXIFinJPEG(file);
		}

		if (typeof define === 'function' && define.amd) {
				define('exif-js', [], function() {
						return EXIF;
				});
		}
}.call(this));

/*
TABGuard v1.0

jQuery UI Tabbing plugin.
http://spirytoos.blogspot.com.au/

Copyright (c) 2013 Tomasz Egiert

https://github.com/spirytoos/TABGuard/blob/master/LICENSE.md

Project site: http://tomaszegiert.seowebsolutions.com.au/tabguard/index.htm
Github site: https://github.com/spirytoos
 */
/*
(function($){var defaults={deactivate:false};var pluginName='tabGuard';$.fn[pluginName]=function(options){var options=$.extend({},defaults,options);return this.each(function(){var $this=$(this);if(options.deactivate){$this.off('.'+pluginName);return}$this.on('keydown.'+pluginName,function(e){if(e.keyCode===9){var tabbables=$this.find(':tabbable'),first=tabbables.filter(':first'),last=tabbables.filter(':last'),focusedElement=$(e.target),isFirstInFocus=(first.get(0)===focusedElement.get(0)),isLastInFocus=(last.get(0)===focusedElement.get(0));var tabbingForward=!e.shiftKey;if(!isFirstInFocus&&!isLastInFocus&&focusedElement.is(':radio')){var radioGroupName=focusedElement.attr('name');if(tabbingForward){if(last.is(':radio')&&last.attr('name')===radioGroupName){isLastInFocus=true}}else{if(first.is(':radio')&&first.attr('name')===radioGroupName){isFirstInFocus=true}}}if(tabbingForward){if(isLastInFocus){first.focus();e.preventDefault()}}else{if(isFirstInFocus){last.focus();e.preventDefault()}}}})})}})(jQuery);
*/
/*

TABGuard v1.0

jQuery UI Tabbing plugin.
http://spirytoos.blogspot.com.au/

Copyright (c) 2013 Tomasz Egiert

https://github.com/spirytoos/TABGuard/blob/master/LICENSE.md

Project site: http://tomaszegiert.seowebsolutions.com.au/tabguard/index.htm
Github site: https://github.com/spirytoos

 */

	(function($)
	{
		var defaults = {
			deactivate: false
		};

		var pluginName = 'tabGuard';

		$.fn[pluginName] = function(options)
		{
			var options = $.extend( {}, defaults, options );
			
			return this.each(function()
			{

				var $this = $(this);

				if (options.deactivate) {
					// Remove the events added by this plugin
					$this.off('.' + pluginName);
					return;
				}

				$this.on('keydown.' + pluginName, function(e) {

					// Make sure we're tabbing and hat our focused element is still focused
					if (e.keyCode === 9)
					{
						var tabbables = $this.find(':tabbable'),
							first = tabbables.filter(':first'),
							last  = tabbables.filter(':last'),
							focusedElement = $(e.target),

							isFirstInFocus = (first.get(0) === focusedElement.get(0)),
							isLastInFocus = (last.get(0) === focusedElement.get(0));

						// Check tab+shift
						var tabbingForward = !e.shiftKey;

						// Special case: radio buttons
						// input[type=radio] are always, according to jQuery ui, :tabbable.
						// If you've selected a radio input and press tab,
						// you will be tabbed to the next input and *not* to the next radio button
						//
						// Here we check if the active element is a radio and if the first/last is
						if (!isFirstInFocus && !isLastInFocus && focusedElement.is(':radio'))
						{
							var radioGroupName = focusedElement.attr('name');
							// If the focused element is a radio button
							if (tabbingForward)
							{
								if (last.is(':radio') && last.attr('name') === radioGroupName)
								{
									// the last one belongs to the same radio group as the focused one
									isLastInFocus = true;
								}
							} else
							{
								if (first.is(':radio') && first.attr('name') === radioGroupName)
								{
									// the first one belongs to the same radio group as the focused one
									isFirstInFocus = true;
								}
							}
						}

						if(tabbingForward)
						{
							if(isLastInFocus)
							{
								first.focus();
								e.preventDefault();
							}
						}
						else
						{
							if(isFirstInFocus)
							{
								last.focus();
								e.preventDefault();
							}
						}
					}
				});
			});
		};

	})(jQuery);

