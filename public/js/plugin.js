/*---------------------------------------------------------------------
	File Name: plugin.js
---------------------------------------------------------------------*/

/* Avoid `console` errors in browsers that lack a console.
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
(function () {
	var method;
	var noop = function () { };
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


/* JQuery meanMenu v2.0.8
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/*!
* jQuery meanMenu v2.0.8
* @Copyright (C) 2012-2014 Chris Wharton @ MeanThemes (https://github.com/meanthemes/meanMenu)
*
*/
/*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
* HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
* INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
* FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
* OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
* COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
* BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
* DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
*
* You should have received a copy of the GNU General Public License
* along with this program. If not, see <http://gnu.org/licenses/>.
*
* Find more information at http://www.meanthemes.com/plugins/meanmenu/
*
*/
(function ($) {
	"use strict";
	$.fn.meanmenu = function (options) {
		var defaults = {
			meanMenuTarget: jQuery(this), // Target the current HTML markup you wish to replace
			meanMenuContainer: '.menu-area', // Choose where meanmenu will be placed within the HTML
			meanMenuClose: "X", // single character you want to represent the close menu button
			meanMenuCloseSize: "25px", // set font size of close button
			meanMenuOpen: "<span /><span /><span />", // text/markup you want when menu is closed
			meanRevealPosition: "", // left right or center positions
			meanRevealPositionDistance: "0", // Tweak the position of the menu
			meanRevealColour: "", // override CSS colours for the reveal background
			meanScreenWidth: "767", // set the screen width you want meanmenu to kick in at
			meanNavPush: "", // set a height here in px, em or % if you want to budge your layout now the navigation is missing.
			meanShowChildren: true, // true to show children in the menu, false to hide them
			meanExpandableChildren: true, // true to allow expand/collapse children
			meanExpand: "+", // single character you want to represent the expand for ULs
			meanContract: "-", // single character you want to represent the contract for ULs
			meanRemoveAttrs: false, // true to remove classes and IDs, false to keep them
			onePage: false, // set to true for one page sites
			meanDisplay: "block", // override display method for table cell based layouts e.g. table-cell
			removeElements: "" // set to hide page elements
		};
		options = $.extend(defaults, options);

		// get browser width
		var currentWidth = window.innerWidth || document.documentElement.clientWidth;

		return this.each(function () {
			var meanMenu = options.meanMenuTarget;
			var meanContainer = options.meanMenuContainer;
			var meanMenuClose = options.meanMenuClose;
			var meanMenuCloseSize = options.meanMenuCloseSize;
			var meanMenuOpen = options.meanMenuOpen;
			var meanRevealPosition = options.meanRevealPosition;
			var meanRevealPositionDistance = options.meanRevealPositionDistance;
			var meanRevealColour = options.meanRevealColour;
			var meanScreenWidth = options.meanScreenWidth;
			var meanNavPush = options.meanNavPush;
			var meanRevealClass = ".meanmenu-reveal";
			var meanShowChildren = options.meanShowChildren;
			var meanExpandableChildren = options.meanExpandableChildren;
			var meanExpand = options.meanExpand;
			var meanContract = options.meanContract;
			var meanRemoveAttrs = options.meanRemoveAttrs;
			var onePage = options.onePage;
			var meanDisplay = options.meanDisplay;
			var removeElements = options.removeElements;

			//detect known mobile/tablet usage
			var isMobile = false;
			if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/Windows Phone/i))) {
				isMobile = true;
			}

			if ((navigator.userAgent.match(/MSIE 8/i)) || (navigator.userAgent.match(/MSIE 7/i))) {
				// add scrollbar for IE7 & 8 to stop breaking resize function on small content sites
				jQuery('html').css("overflow-y", "scroll");
			}

			var meanRevealPos = "";
			var meanCentered = function () {
				if (meanRevealPosition === "center") {
					var newWidth = window.innerWidth || document.documentElement.clientWidth;
					var meanCenter = ((newWidth / 2) - 22) + "px";
					meanRevealPos = "left:" + meanCenter + ";right:auto;";

					if (!isMobile) {
						jQuery('.meanmenu-reveal').css("left", meanCenter);
					} else {
						jQuery('.meanmenu-reveal').animate({
							left: meanCenter
						});
					}
				}
			};

			var menuOn = false;
			var meanMenuExist = false;


			if (meanRevealPosition === "right") {
				meanRevealPos = "right:" + meanRevealPositionDistance + ";left:auto;";
			}
			if (meanRevealPosition === "left") {
				meanRevealPos = "left:" + meanRevealPositionDistance + ";right:auto;";
			}
			// run center function
			meanCentered();

			// set all styles for mean-reveal
			var $navreveal = "";

			var meanInner = function () {
				// get last class name
				if (jQuery($navreveal).is(".meanmenu-reveal.meanclose")) {
					$navreveal.html(meanMenuClose);
				} else {
					$navreveal.html(meanMenuOpen);
				}
			};

			// re-instate original nav (and call this on window.width functions)
			var meanOriginal = function () {
				jQuery('.mean-bar,.mean-push').remove();
				jQuery(meanContainer).removeClass("mean-container");
				jQuery(meanMenu).css('display', meanDisplay);
				menuOn = false;
				meanMenuExist = false;
				jQuery(removeElements).removeClass('mean-remove');
			};

			// navigation reveal
			var showMeanMenu = function () {
				var meanStyles = "background:" + meanRevealColour + ";color:" + meanRevealColour + ";" + meanRevealPos;
				if (currentWidth <= meanScreenWidth) {
					jQuery(removeElements).addClass('mean-remove');
					meanMenuExist = true;
					// add class to body so we don't need to worry about media queries here, all CSS is wrapped in '.mean-container'
					jQuery(meanContainer).addClass("mean-container");
					jQuery('.mean-container').prepend('<div class="mean-bar"><a href="#nav" class="meanmenu-reveal" style="' + meanStyles + '">Show Navigation</a><nav class="mean-nav"></nav></div>');

					//push meanMenu navigation into .mean-nav
					var meanMenuContents = jQuery(meanMenu).html();
					jQuery('.mean-nav').html(meanMenuContents);

					// remove all classes from EVERYTHING inside meanmenu nav
					if (meanRemoveAttrs) {
						jQuery('nav.mean-nav ul, nav.mean-nav ul *').each(function () {
							// First check if this has mean-remove class
							if (jQuery(this).is('.mean-remove')) {
								jQuery(this).attr('class', 'mean-remove');
							} else {
								jQuery(this).removeAttr("class");
							}
							jQuery(this).removeAttr("id");
						});
					}

					// push in a holder div (this can be used if removal of nav is causing layout issues)
					jQuery(meanMenu).before('<div class="mean-push" />');
					jQuery('.mean-push').css("margin-top", meanNavPush);

					// hide current navigation and reveal mean nav link
					jQuery(meanMenu).hide();
					jQuery(".meanmenu-reveal").show();

					// turn 'X' on or off
					jQuery(meanRevealClass).html(meanMenuOpen);
					$navreveal = jQuery(meanRevealClass);

					//hide mean-nav ul
					jQuery('.mean-nav ul').hide();

					// hide sub nav
					if (meanShowChildren) {
						// allow expandable sub nav(s)
						if (meanExpandableChildren) {
							jQuery('.mean-nav ul ul').each(function () {
								if (jQuery(this).children().length) {
									jQuery(this, 'li:first').parent().append('<a class="mean-expand" href="#" style="font-size: ' + meanMenuCloseSize + '">' + meanExpand + '</a>');
								}
							});
							jQuery('.mean-expand').on("click", function (e) {
								e.preventDefault();
								if (jQuery(this).hasClass("mean-clicked")) {
									jQuery(this).text(meanExpand);
									jQuery(this).prev('ul').slideUp(300, function () { });
								} else {
									jQuery(this).text(meanContract);
									jQuery(this).prev('ul').slideDown(300, function () { });
								}
								jQuery(this).toggleClass("mean-clicked");
							});
						} else {
							jQuery('.mean-nav ul ul').show();
						}
					} else {
						jQuery('.mean-nav ul ul').hide();
					}

					// add last class to tidy up borders
					jQuery('.mean-nav ul li').last().addClass('mean-last');
					$navreveal.removeClass("meanclose");
					jQuery($navreveal).click(function (e) {
						e.preventDefault();
						if (menuOn === false) {
							$navreveal.css("text-align", "center");
							$navreveal.css("text-indent", "0");
							$navreveal.css("font-size", meanMenuCloseSize);
							jQuery('.mean-nav ul:first').slideDown();
							menuOn = true;
						} else {
							jQuery('.mean-nav ul:first').slideUp();
							menuOn = false;
						}
						$navreveal.toggleClass("meanclose");
						meanInner();
						jQuery(removeElements).addClass('mean-remove');
					});

					// for one page websites, reset all variables...
					if (onePage) {
						jQuery('.mean-nav ul > li > a:first-child').on("click", function () {
							jQuery('.mean-nav ul:first').slideUp();
							menuOn = false;
							jQuery($navreveal).toggleClass("meanclose").html(meanMenuOpen);
						});
					}
				} else {
					meanOriginal();
				}
			};

			if (!isMobile) {
				// reset menu on resize above meanScreenWidth
				jQuery(window).resize(function () {
					currentWidth = window.innerWidth || document.documentElement.clientWidth;
					if (currentWidth > meanScreenWidth) {
						meanOriginal();
					} else {
						meanOriginal();
					}
					if (currentWidth <= meanScreenWidth) {
						showMeanMenu();
						meanCentered();
					} else {
						meanOriginal();
					}
				});
			}

			jQuery(window).resize(function () {
				// get browser width
				currentWidth = window.innerWidth || document.documentElement.clientWidth;

				if (!isMobile) {
					meanOriginal();
					if (currentWidth <= meanScreenWidth) {
						showMeanMenu();
						meanCentered();
					}
				} else {
					meanCentered();
					if (currentWidth <= meanScreenWidth) {
						if (meanMenuExist === false) {
							showMeanMenu();
						}
					} else {
						meanOriginal();
					}
				}
			});

			// run main menuMenu function on load
			showMeanMenu();
		});
	};
})(jQuery);


/* MapIt
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/**
 * MapIt
 *
 * @copyright Copyright 2013, Dimitris Krestos
 * @license   Apache License, Version 2.0 (http://www.opensource.org/licenses/apache2.0.php)
 * @link      http://vdw.staytuned.gr
 * @version   v0.3.0
 */

!function ($, window, undefined) { "use strict"; $.fn.mapit = function (options) { var defaults = { latitude: 37.970996, longitude: 23.730542, zoom: 16, type: "ROADMAP", scrollwheel: !1, marker: { latitude: 37.970996, longitude: 23.730542, icon: "", title: "", open: !1, center: !0 }, address: "", styles: "GRAYSCALE", locations: [], origins: [] }, options = $.extend(defaults, options); $(this).each(function () { var $this = $(this), directionsDisplay = new google.maps.DirectionsRenderer, mapOptions = { scrollwheel: options.scrollwheel, scaleControl: !1, center: options.marker.center ? new google.maps.LatLng(options.marker.latitude, options.marker.longitude) : new google.maps.LatLng(options.latitude, options.longitude), zoom: options.zoom, mapTypeId: eval("google.maps.MapTypeId." + options.type) }, map = new google.maps.Map(document.getElementById($this.attr("id")), mapOptions); if (directionsDisplay.setMap(map), options.styles) { var GRAYSCALE_style = [{ featureType: "all", elementType: "all", stylers: [{ saturation: -100 }] }], MIDNIGHT_style = [{ featureType: "water", stylers: [{ color: "#021019" }] }, { featureType: "landscape", stylers: [{ color: "#08304b" }] }, { featureType: "poi", elementType: "geometry", stylers: [{ color: "#0c4152" }, { lightness: 5 }] }, { featureType: "road.highway", elementType: "geometry.fill", stylers: [{ color: "#000000" }] }, { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#0b434f" }, { lightness: 25 }] }, { featureType: "road.arterial", elementType: "geometry.fill", stylers: [{ color: "#000000" }] }, { featureType: "road.arterial", elementType: "geometry.stroke", stylers: [{ color: "#0b3d51" }, { lightness: 16 }] }, { featureType: "road.local", elementType: "geometry", stylers: [{ color: "#000000" }] }, { elementType: "labels.text.fill", stylers: [{ color: "#ffffff" }] }, { elementType: "labels.text.stroke", stylers: [{ color: "#000000" }, { lightness: 13 }] }, { featureType: "transit", stylers: [{ color: "#146474" }] }, { featureType: "administrative", elementType: "geometry.fill", stylers: [{ color: "#000000" }] }, { featureType: "administrative", elementType: "geometry.stroke", stylers: [{ color: "#144b53" }, { lightness: 14 }, { weight: 1.4 }] }], BLUE_style = [{ featureType: "water", stylers: [{ color: "#46bcec" }, { visibility: "on" }] }, { featureType: "landscape", stylers: [{ color: "#f2f2f2" }] }, { featureType: "road", stylers: [{ saturation: -100 }, { lightness: 45 }] }, { featureType: "road.highway", stylers: [{ visibility: "simplified" }] }, { featureType: "road.arterial", elementType: "labels.icon", stylers: [{ visibility: "off" }] }, { featureType: "administrative", elementType: "labels.text.fill", stylers: [{ color: "#444444" }] }, { featureType: "transit", stylers: [{ visibility: "off" }] }, { featureType: "poi", stylers: [{ visibility: "off" }] }], mapType = new google.maps.StyledMapType(eval(options.styles + "_style"), { name: options.styles }); map.mapTypes.set(options.styles, mapType), map.setMapTypeId(options.styles) } var home = new google.maps.Marker({ map: map, position: new google.maps.LatLng(options.marker.latitude, options.marker.longitude), icon: new google.maps.MarkerImage(options.marker.icon), title: options.marker.title }), info = new google.maps.InfoWindow({ content: options.address }); options.marker.open ? info.open(map, home) : google.maps.event.addListener(home, "click", function () { info.open(map, home) }); var infowindow = new google.maps.InfoWindow, marker, i, markers = []; for (i = 0; i < options.locations.length; i++)marker = new google.maps.Marker({ position: new google.maps.LatLng(options.locations[i][0], options.locations[i][1]), map: map, icon: new google.maps.MarkerImage(options.locations[i][2] || options.marker.icon), title: options.locations[i][3] }), markers.push(marker), google.maps.event.addListener(marker, "click", function (e, o) { return function () { infowindow.setContent(options.locations[o][4]), infowindow.open(map, e) } }(marker, i)); var directionsService = new google.maps.DirectionsService; $this.on("route", function (e, o) { var t = { origin: new google.maps.LatLng(options.origins[o][0], options.origins[o][1]), destination: new google.maps.LatLng(options.marker.latitude, options.marker.longitude), travelMode: google.maps.TravelMode.DRIVING }; directionsService.route(t, function (e, o) { o == google.maps.DirectionsStatus.OK && directionsDisplay.setDirections(e) }) }), $this.on("hide_all", function () { for (var e = 0; e < options.locations.length; e++)markers[e].setVisible(!1) }), $this.on("show", function (e, o) { $this.trigger("hide_all"), $this.trigger("reset"); for (var t = new google.maps.LatLngBounds, i = 0; i < options.locations.length; i++)options.locations[i][6] == o && markers[i].setVisible(!0), t.extend(markers[i].position); map.fitBounds(t) }), $this.on("hide", function (e, o) { for (var t = 0; t < options.locations.length; t++)options.locations[t][6] == o && markers[t].setVisible(!1) }), $this.on("clear", function () { if (markers) for (var e = 0; e < markers.length; e++)markers[e].setMap(null) }), $this.on("reset", function () { map.setCenter(new google.maps.LatLng(options.latitude, options.longitude), options.zoom) }), $this.trigger("hide_all") }) }, $(document).ready(function () { $('[data-toggle="mapit"]').mapit() }) }(jQuery);



/* Owl Carousel v2.3.4
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/**
 * Owl Carousel v2.3.4
 * Copyright 2013-2018 David Deutsch
 * Licensed under: SEE LICENSE IN https://github.com/OwlCarousel2/OwlCarousel2/blob/master/LICENSE
 */
!function (a, b, c, d) { function e(b, c) { this.settings = null, this.options = a.extend({}, e.Defaults, c), this.$element = a(b), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = { time: null, target: null, pointer: null, stage: { start: null, current: null }, direction: null }, this._states = { current: {}, tags: { initializing: ["busy"], animating: ["busy"], dragging: ["interacting"] } }, a.each(["onResize", "onThrottledResize"], a.proxy(function (b, c) { this._handlers[c] = a.proxy(this[c], this) }, this)), a.each(e.Plugins, a.proxy(function (a, b) { this._plugins[a.charAt(0).toLowerCase() + a.slice(1)] = new b(this) }, this)), a.each(e.Workers, a.proxy(function (b, c) { this._pipe.push({ filter: c.filter, run: a.proxy(c.run, this) }) }, this)), this.setup(), this.initialize() } e.Defaults = { items: 3, loop: !1, center: !1, rewind: !1, checkVisibility: !0, mouseDrag: !0, touchDrag: !0, pullDrag: !0, freeDrag: !1, margin: 0, stagePadding: 0, merge: !1, mergeFit: !0, autoWidth: !1, startPosition: 0, rtl: !1, smartSpeed: 250, fluidSpeed: !1, dragEndSpeed: !1, responsive: {}, responsiveRefreshRate: 200, responsiveBaseElement: b, fallbackEasing: "swing", info: !1, nestedItemSelector: !1, itemElement: "div", stageElement: "div", refreshClass: "owl-refresh", loadedClass: "owl-loaded", loadingClass: "owl-loading", rtlClass: "owl-rtl", responsiveClass: "owl-responsive", dragClass: "owl-drag", itemClass: "owl-item", stageClass: "owl-stage", stageOuterClass: "owl-stage-outer", grabClass: "owl-grab" }, e.Width = { Default: "default", Inner: "inner", Outer: "outer" }, e.Type = { Event: "event", State: "state" }, e.Plugins = {}, e.Workers = [{ filter: ["width", "settings"], run: function () { this._width = this.$element.width() } }, { filter: ["width", "items", "settings"], run: function (a) { a.current = this._items && this._items[this.relative(this._current)] } }, { filter: ["items", "settings"], run: function () { this.$stage.children(".cloned").remove() } }, { filter: ["width", "items", "settings"], run: function (a) { var b = this.settings.margin || "", c = !this.settings.autoWidth, d = this.settings.rtl, e = { width: "auto", "margin-left": d ? b : "", "margin-right": d ? "" : b }; !c && this.$stage.children().css(e), a.css = e } }, { filter: ["width", "items", "settings"], run: function (a) { var b = (this.width() / this.settings.items).toFixed(3) - this.settings.margin, c = null, d = this._items.length, e = !this.settings.autoWidth, f = []; for (a.items = { merge: !1, width: b }; d--;)c = this._mergers[d], c = this.settings.mergeFit && Math.min(c, this.settings.items) || c, a.items.merge = c > 1 || a.items.merge, f[d] = e ? b * c : this._items[d].width(); this._widths = f } }, { filter: ["items", "settings"], run: function () { var b = [], c = this._items, d = this.settings, e = Math.max(2 * d.items, 4), f = 2 * Math.ceil(c.length / 2), g = d.loop && c.length ? d.rewind ? e : Math.max(e, f) : 0, h = "", i = ""; for (g /= 2; g > 0;)b.push(this.normalize(b.length / 2, !0)), h += c[b[b.length - 1]][0].outerHTML, b.push(this.normalize(c.length - 1 - (b.length - 1) / 2, !0)), i = c[b[b.length - 1]][0].outerHTML + i, g -= 1; this._clones = b, a(h).addClass("cloned").appendTo(this.$stage), a(i).addClass("cloned").prependTo(this.$stage) } }, { filter: ["width", "items", "settings"], run: function () { for (var a = this.settings.rtl ? 1 : -1, b = this._clones.length + this._items.length, c = -1, d = 0, e = 0, f = []; ++c < b;)d = f[c - 1] || 0, e = this._widths[this.relative(c)] + this.settings.margin, f.push(d + e * a); this._coordinates = f } }, { filter: ["width", "items", "settings"], run: function () { var a = this.settings.stagePadding, b = this._coordinates, c = { width: Math.ceil(Math.abs(b[b.length - 1])) + 2 * a, "padding-left": a || "", "padding-right": a || "" }; this.$stage.css(c) } }, { filter: ["width", "items", "settings"], run: function (a) { var b = this._coordinates.length, c = !this.settings.autoWidth, d = this.$stage.children(); if (c && a.items.merge) for (; b--;)a.css.width = this._widths[this.relative(b)], d.eq(b).css(a.css); else c && (a.css.width = a.items.width, d.css(a.css)) } }, { filter: ["items"], run: function () { this._coordinates.length < 1 && this.$stage.removeAttr("style") } }, { filter: ["width", "items", "settings"], run: function (a) { a.current = a.current ? this.$stage.children().index(a.current) : 0, a.current = Math.max(this.minimum(), Math.min(this.maximum(), a.current)), this.reset(a.current) } }, { filter: ["position"], run: function () { this.animate(this.coordinates(this._current)) } }, { filter: ["width", "position", "items", "settings"], run: function () { var a, b, c, d, e = this.settings.rtl ? 1 : -1, f = 2 * this.settings.stagePadding, g = this.coordinates(this.current()) + f, h = g + this.width() * e, i = []; for (c = 0, d = this._coordinates.length; c < d; c++)a = this._coordinates[c - 1] || 0, b = Math.abs(this._coordinates[c]) + f * e, (this.op(a, "<=", g) && this.op(a, ">", h) || this.op(b, "<", g) && this.op(b, ">", h)) && i.push(c); this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + i.join("), :eq(") + ")").addClass("active"), this.$stage.children(".center").removeClass("center"), this.settings.center && this.$stage.children().eq(this.current()).addClass("center") } }], e.prototype.initializeStage = function () { this.$stage = this.$element.find("." + this.settings.stageClass), this.$stage.length || (this.$element.addClass(this.options.loadingClass), this.$stage = a("<" + this.settings.stageElement + "/>", { class: this.settings.stageClass }).wrap(a("<div/>", { class: this.settings.stageOuterClass })), this.$element.append(this.$stage.parent())) }, e.prototype.initializeItems = function () { var b = this.$element.find(".owl-item"); if (b.length) return this._items = b.get().map(function (b) { return a(b) }), this._mergers = this._items.map(function () { return 1 }), void this.refresh(); this.replace(this.$element.children().not(this.$stage.parent())), this.isVisible() ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass) }, e.prototype.initialize = function () { if (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) { var a, b, c; a = this.$element.find("img"), b = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : d, c = this.$element.children(b).width(), a.length && c <= 0 && this.preloadAutoWidthImages(a) } this.initializeStage(), this.initializeItems(), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized") }, e.prototype.isVisible = function () { return !this.settings.checkVisibility || this.$element.is(":visible") }, e.prototype.setup = function () { var b = this.viewport(), c = this.options.responsive, d = -1, e = null; c ? (a.each(c, function (a) { a <= b && a > d && (d = Number(a)) }), e = a.extend({}, this.options, c[d]), "function" == typeof e.stagePadding && (e.stagePadding = e.stagePadding()), delete e.responsive, e.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + d))) : e = a.extend({}, this.options), this.trigger("change", { property: { name: "settings", value: e } }), this._breakpoint = d, this.settings = e, this.invalidate("settings"), this.trigger("changed", { property: { name: "settings", value: this.settings } }) }, e.prototype.optionsLogic = function () { this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1) }, e.prototype.prepare = function (b) { var c = this.trigger("prepare", { content: b }); return c.data || (c.data = a("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(b)), this.trigger("prepared", { content: c.data }), c.data }, e.prototype.update = function () { for (var b = 0, c = this._pipe.length, d = a.proxy(function (a) { return this[a] }, this._invalidated), e = {}; b < c;)(this._invalidated.all || a.grep(this._pipe[b].filter, d).length > 0) && this._pipe[b].run(e), b++; this._invalidated = {}, !this.is("valid") && this.enter("valid") }, e.prototype.width = function (a) { switch (a = a || e.Width.Default) { case e.Width.Inner: case e.Width.Outer: return this._width; default: return this._width - 2 * this.settings.stagePadding + this.settings.margin } }, e.prototype.refresh = function () { this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed") }, e.prototype.onThrottledResize = function () { b.clearTimeout(this.resizeTimer), this.resizeTimer = b.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate) }, e.prototype.onResize = function () { return !!this._items.length && (this._width !== this.$element.width() && (!!this.isVisible() && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized"))))) }, e.prototype.registerEventHandlers = function () { a.support.transition && this.$stage.on(a.support.transition.end + ".owl.core", a.proxy(this.onTransitionEnd, this)), !1 !== this.settings.responsive && this.on(b, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function () { return !1 })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", a.proxy(this.onDragEnd, this))) }, e.prototype.onDragStart = function (b) { var d = null; 3 !== b.which && (a.support.transform ? (d = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","), d = { x: d[16 === d.length ? 12 : 4], y: d[16 === d.length ? 13 : 5] }) : (d = this.$stage.position(), d = { x: this.settings.rtl ? d.left + this.$stage.width() - this.width() + this.settings.margin : d.left, y: d.top }), this.is("animating") && (a.support.transform ? this.animate(d.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === b.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = a(b.target), this._drag.stage.start = d, this._drag.stage.current = d, this._drag.pointer = this.pointer(b), a(c).on("mouseup.owl.core touchend.owl.core", a.proxy(this.onDragEnd, this)), a(c).one("mousemove.owl.core touchmove.owl.core", a.proxy(function (b) { var d = this.difference(this._drag.pointer, this.pointer(b)); a(c).on("mousemove.owl.core touchmove.owl.core", a.proxy(this.onDragMove, this)), Math.abs(d.x) < Math.abs(d.y) && this.is("valid") || (b.preventDefault(), this.enter("dragging"), this.trigger("drag")) }, this))) }, e.prototype.onDragMove = function (a) { var b = null, c = null, d = null, e = this.difference(this._drag.pointer, this.pointer(a)), f = this.difference(this._drag.stage.start, e); this.is("dragging") && (a.preventDefault(), this.settings.loop ? (b = this.coordinates(this.minimum()), c = this.coordinates(this.maximum() + 1) - b, f.x = ((f.x - b) % c + c) % c + b) : (b = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), c = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), d = this.settings.pullDrag ? -1 * e.x / 5 : 0, f.x = Math.max(Math.min(f.x, b + d), c + d)), this._drag.stage.current = f, this.animate(f.x)) }, e.prototype.onDragEnd = function (b) { var d = this.difference(this._drag.pointer, this.pointer(b)), e = this._drag.stage.current, f = d.x > 0 ^ this.settings.rtl ? "left" : "right"; a(c).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== d.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(e.x, 0 !== d.x ? f : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = f, (Math.abs(d.x) > 3 || (new Date).getTime() - this._drag.time > 300) && this._drag.target.one("click.owl.core", function () { return !1 })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged")) }, e.prototype.closest = function (b, c) { var e = -1, f = 30, g = this.width(), h = this.coordinates(); return this.settings.freeDrag || a.each(h, a.proxy(function (a, i) { return "left" === c && b > i - f && b < i + f ? e = a : "right" === c && b > i - g - f && b < i - g + f ? e = a + 1 : this.op(b, "<", i) && this.op(b, ">", h[a + 1] !== d ? h[a + 1] : i - g) && (e = "left" === c ? a + 1 : a), -1 === e }, this)), this.settings.loop || (this.op(b, ">", h[this.minimum()]) ? e = b = this.minimum() : this.op(b, "<", h[this.maximum()]) && (e = b = this.maximum())), e }, e.prototype.animate = function (b) { var c = this.speed() > 0; this.is("animating") && this.onTransitionEnd(), c && (this.enter("animating"), this.trigger("translate")), a.support.transform3d && a.support.transition ? this.$stage.css({ transform: "translate3d(" + b + "px,0px,0px)", transition: this.speed() / 1e3 + "s" + (this.settings.slideTransition ? " " + this.settings.slideTransition : "") }) : c ? this.$stage.animate({ left: b + "px" }, this.speed(), this.settings.fallbackEasing, a.proxy(this.onTransitionEnd, this)) : this.$stage.css({ left: b + "px" }) }, e.prototype.is = function (a) { return this._states.current[a] && this._states.current[a] > 0 }, e.prototype.current = function (a) { if (a === d) return this._current; if (0 === this._items.length) return d; if (a = this.normalize(a), this._current !== a) { var b = this.trigger("change", { property: { name: "position", value: a } }); b.data !== d && (a = this.normalize(b.data)), this._current = a, this.invalidate("position"), this.trigger("changed", { property: { name: "position", value: this._current } }) } return this._current }, e.prototype.invalidate = function (b) { return "string" === a.type(b) && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) { return b }) }, e.prototype.reset = function (a) { (a = this.normalize(a)) !== d && (this._speed = 0, this._current = a, this.suppress(["translate", "translated"]), this.animate(this.coordinates(a)), this.release(["translate", "translated"])) }, e.prototype.normalize = function (a, b) { var c = this._items.length, e = b ? 0 : this._clones.length; return !this.isNumeric(a) || c < 1 ? a = d : (a < 0 || a >= c + e) && (a = ((a - e / 2) % c + c) % c + e / 2), a }, e.prototype.relative = function (a) { return a -= this._clones.length / 2, this.normalize(a, !0) }, e.prototype.maximum = function (a) { var b, c, d, e = this.settings, f = this._coordinates.length; if (e.loop) f = this._clones.length / 2 + this._items.length - 1; else if (e.autoWidth || e.merge) { if (b = this._items.length) for (c = this._items[--b].width(), d = this.$element.width(); b-- && !((c += this._items[b].width() + this.settings.margin) > d);); f = b + 1 } else f = e.center ? this._items.length - 1 : this._items.length - e.items; return a && (f -= this._clones.length / 2), Math.max(f, 0) }, e.prototype.minimum = function (a) { return a ? 0 : this._clones.length / 2 }, e.prototype.items = function (a) { return a === d ? this._items.slice() : (a = this.normalize(a, !0), this._items[a]) }, e.prototype.mergers = function (a) { return a === d ? this._mergers.slice() : (a = this.normalize(a, !0), this._mergers[a]) }, e.prototype.clones = function (b) { var c = this._clones.length / 2, e = c + this._items.length, f = function (a) { return a % 2 == 0 ? e + a / 2 : c - (a + 1) / 2 }; return b === d ? a.map(this._clones, function (a, b) { return f(b) }) : a.map(this._clones, function (a, c) { return a === b ? f(c) : null }) }, e.prototype.speed = function (a) { return a !== d && (this._speed = a), this._speed }, e.prototype.coordinates = function (b) { var c, e = 1, f = b - 1; return b === d ? a.map(this._coordinates, a.proxy(function (a, b) { return this.coordinates(b) }, this)) : (this.settings.center ? (this.settings.rtl && (e = -1, f = b + 1), c = this._coordinates[b], c += (this.width() - c + (this._coordinates[f] || 0)) / 2 * e) : c = this._coordinates[f] || 0, c = Math.ceil(c)) }, e.prototype.duration = function (a, b, c) { return 0 === c ? 0 : Math.min(Math.max(Math.abs(b - a), 1), 6) * Math.abs(c || this.settings.smartSpeed) }, e.prototype.to = function (a, b) { var c = this.current(), d = null, e = a - this.relative(c), f = (e > 0) - (e < 0), g = this._items.length, h = this.minimum(), i = this.maximum(); this.settings.loop ? (!this.settings.rewind && Math.abs(e) > g / 2 && (e += -1 * f * g), a = c + e, (d = ((a - h) % g + g) % g + h) !== a && d - e <= i && d - e > 0 && (c = d - e, a = d, this.reset(c))) : this.settings.rewind ? (i += 1, a = (a % i + i) % i) : a = Math.max(h, Math.min(i, a)), this.speed(this.duration(c, a, b)), this.current(a), this.isVisible() && this.update() }, e.prototype.next = function (a) { a = a || !1, this.to(this.relative(this.current()) + 1, a) }, e.prototype.prev = function (a) { a = a || !1, this.to(this.relative(this.current()) - 1, a) }, e.prototype.onTransitionEnd = function (a) { if (a !== d && (a.stopPropagation(), (a.target || a.srcElement || a.originalTarget) !== this.$stage.get(0))) return !1; this.leave("animating"), this.trigger("translated") }, e.prototype.viewport = function () { var d; return this.options.responsiveBaseElement !== b ? d = a(this.options.responsiveBaseElement).width() : b.innerWidth ? d = b.innerWidth : c.documentElement && c.documentElement.clientWidth ? d = c.documentElement.clientWidth : console.warn("Can not detect viewport width."), d }, e.prototype.replace = function (b) { this.$stage.empty(), this._items = [], b && (b = b instanceof jQuery ? b : a(b)), this.settings.nestedItemSelector && (b = b.find("." + this.settings.nestedItemSelector)), b.filter(function () { return 1 === this.nodeType }).each(a.proxy(function (a, b) { b = this.prepare(b), this.$stage.append(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1) }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items") }, e.prototype.add = function (b, c) { var e = this.relative(this._current); c = c === d ? this._items.length : this.normalize(c, !0), b = b instanceof jQuery ? b : a(b), this.trigger("add", { content: b, position: c }), b = this.prepare(b), 0 === this._items.length || c === this._items.length ? (0 === this._items.length && this.$stage.append(b), 0 !== this._items.length && this._items[c - 1].after(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[c].before(b), this._items.splice(c, 0, b), this._mergers.splice(c, 0, 1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[e] && this.reset(this._items[e].index()), this.invalidate("items"), this.trigger("added", { content: b, position: c }) }, e.prototype.remove = function (a) { (a = this.normalize(a, !0)) !== d && (this.trigger("remove", { content: this._items[a], position: a }), this._items[a].remove(), this._items.splice(a, 1), this._mergers.splice(a, 1), this.invalidate("items"), this.trigger("removed", { content: null, position: a })) }, e.prototype.preloadAutoWidthImages = function (b) { b.each(a.proxy(function (b, c) { this.enter("pre-loading"), c = a(c), a(new Image).one("load", a.proxy(function (a) { c.attr("src", a.target.src), c.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh() }, this)).attr("src", c.attr("src") || c.attr("data-src") || c.attr("data-src-retina")) }, this)) }, e.prototype.destroy = function () { this.$element.off(".owl.core"), this.$stage.off(".owl.core"), a(c).off(".owl.core"), !1 !== this.settings.responsive && (b.clearTimeout(this.resizeTimer), this.off(b, "resize", this._handlers.onThrottledResize)); for (var d in this._plugins) this._plugins[d].destroy(); this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$stage.remove(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel") }, e.prototype.op = function (a, b, c) { var d = this.settings.rtl; switch (b) { case "<": return d ? a > c : a < c; case ">": return d ? a < c : a > c; case ">=": return d ? a <= c : a >= c; case "<=": return d ? a >= c : a <= c } }, e.prototype.on = function (a, b, c, d) { a.addEventListener ? a.addEventListener(b, c, d) : a.attachEvent && a.attachEvent("on" + b, c) }, e.prototype.off = function (a, b, c, d) { a.removeEventListener ? a.removeEventListener(b, c, d) : a.detachEvent && a.detachEvent("on" + b, c) }, e.prototype.trigger = function (b, c, d, f, g) { var h = { item: { count: this._items.length, index: this.current() } }, i = a.camelCase(a.grep(["on", b, d], function (a) { return a }).join("-").toLowerCase()), j = a.Event([b, "owl", d || "carousel"].join(".").toLowerCase(), a.extend({ relatedTarget: this }, h, c)); return this._supress[b] || (a.each(this._plugins, function (a, b) { b.onTrigger && b.onTrigger(j) }), this.register({ type: e.Type.Event, name: b }), this.$element.trigger(j), this.settings && "function" == typeof this.settings[i] && this.settings[i].call(this, j)), j }, e.prototype.enter = function (b) { a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) { this._states.current[b] === d && (this._states.current[b] = 0), this._states.current[b]++ }, this)) }, e.prototype.leave = function (b) { a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) { this._states.current[b]-- }, this)) }, e.prototype.register = function (b) { if (b.type === e.Type.Event) { if (a.event.special[b.name] || (a.event.special[b.name] = {}), !a.event.special[b.name].owl) { var c = a.event.special[b.name]._default; a.event.special[b.name]._default = function (a) { return !c || !c.apply || a.namespace && -1 !== a.namespace.indexOf("owl") ? a.namespace && a.namespace.indexOf("owl") > -1 : c.apply(this, arguments) }, a.event.special[b.name].owl = !0 } } else b.type === e.Type.State && (this._states.tags[b.name] ? this._states.tags[b.name] = this._states.tags[b.name].concat(b.tags) : this._states.tags[b.name] = b.tags, this._states.tags[b.name] = a.grep(this._states.tags[b.name], a.proxy(function (c, d) { return a.inArray(c, this._states.tags[b.name]) === d }, this))) }, e.prototype.suppress = function (b) { a.each(b, a.proxy(function (a, b) { this._supress[b] = !0 }, this)) }, e.prototype.release = function (b) { a.each(b, a.proxy(function (a, b) { delete this._supress[b] }, this)) }, e.prototype.pointer = function (a) { var c = { x: null, y: null }; return a = a.originalEvent || a || b.event, a = a.touches && a.touches.length ? a.touches[0] : a.changedTouches && a.changedTouches.length ? a.changedTouches[0] : a, a.pageX ? (c.x = a.pageX, c.y = a.pageY) : (c.x = a.clientX, c.y = a.clientY), c }, e.prototype.isNumeric = function (a) { return !isNaN(parseFloat(a)) }, e.prototype.difference = function (a, b) { return { x: a.x - b.x, y: a.y - b.y } }, a.fn.owlCarousel = function (b) { var c = Array.prototype.slice.call(arguments, 1); return this.each(function () { var d = a(this), f = d.data("owl.carousel"); f || (f = new e(this, "object" == typeof b && b), d.data("owl.carousel", f), a.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function (b, c) { f.register({ type: e.Type.Event, name: c }), f.$element.on(c + ".owl.carousel.core", a.proxy(function (a) { a.namespace && a.relatedTarget !== this && (this.suppress([c]), f[c].apply(this, [].slice.call(arguments, 1)), this.release([c])) }, f)) })), "string" == typeof b && "_" !== b.charAt(0) && f[b].apply(f, c) }) }, a.fn.owlCarousel.Constructor = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._interval = null, this._visible = null, this._handlers = { "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoRefresh && this.watch() }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers) }; e.Defaults = { autoRefresh: !0, autoRefreshInterval: 500 }, e.prototype.watch = function () { this._interval || (this._visible = this._core.isVisible(), this._interval = b.setInterval(a.proxy(this.refresh, this), this._core.settings.autoRefreshInterval)) }, e.prototype.refresh = function () { this._core.isVisible() !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh()) }, e.prototype.destroy = function () { var a, c; b.clearInterval(this._interval); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null) }, a.fn.owlCarousel.Constructor.Plugins.AutoRefresh = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._loaded = [], this._handlers = { "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function (b) { if (b.namespace && this._core.settings && this._core.settings.lazyLoad && (b.property && "position" == b.property.name || "initialized" == b.type)) for (var c = this._core.settings, e = c.center && Math.ceil(c.items / 2) || c.items, f = c.center && -1 * e || 0, g = (b.property && b.property.value !== d ? b.property.value : this._core.current()) + f, h = this._core.clones().length, i = a.proxy(function (a, b) { this.load(b) }, this); f++ < e;)this.load(h / 2 + this._core.relative(g)), h && a.each(this._core.clones(this._core.relative(g)), i), g++ }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers) }; e.Defaults = { lazyLoad: !1 }, e.prototype.load = function (c) { var d = this._core.$stage.children().eq(c), e = d && d.find(".owl-lazy"); !e || a.inArray(d.get(0), this._loaded) > -1 || (e.each(a.proxy(function (c, d) { var e, f = a(d), g = b.devicePixelRatio > 1 && f.attr("data-src-retina") || f.attr("data-src") || f.attr("data-srcset"); this._core.trigger("load", { element: f, url: g }, "lazy"), f.is("img") ? f.one("load.owl.lazy", a.proxy(function () { f.css("opacity", 1), this._core.trigger("loaded", { element: f, url: g }, "lazy") }, this)).attr("src", g) : f.is("source") ? f.one("load.owl.lazy", a.proxy(function () { this._core.trigger("loaded", { element: f, url: g }, "lazy") }, this)).attr("srcset", g) : (e = new Image, e.onload = a.proxy(function () { f.css({ "background-image": 'url("' + g + '")', opacity: "1" }), this._core.trigger("loaded", { element: f, url: g }, "lazy") }, this), e.src = g) }, this)), this._loaded.push(d.get(0))) }, e.prototype.destroy = function () { var a, b; for (a in this.handlers) this._core.$element.off(a, this.handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Lazy = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (c) { this._core = c, this._previousHeight = null, this._handlers = { "initialized.owl.carousel refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && this.update() }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && "position" === a.property.name && this.update() }, this), "loaded.owl.lazy": a.proxy(function (a) { a.namespace && this._core.settings.autoHeight && a.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update() }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._intervalId = null, this._callback = a.proxy(function () { this.update(), this.leave("updating") }, this) }; e.Defaults = { autoHeight: !1, autoHeightClass: "owl-height" }, e.prototype.update = function () { var b = this._core._current, c = b + this._core.settings.items, d = this._core.settings.lazyLoad, e = this._core.$stage.children().toArray().slice(b, c), f = [], g = 0; a.each(e, function (b, c) { f.push(a(c).height(d ? "auto" : null)) }), g = Math.max.apply(null, f), g <= this._previousHeight || !this.enter("updating") ? this.leave("updating") : (this._previousHeight = g, this._core.$stage.parent().height(g).addClass(this._core.settings.autoHeightClass), b.setTimeout(this._callback, 100)) }, e.prototype.destroy = function () { var a, b; for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.AutoHeight = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._videos = {}, this._playing = null, this._handlers = { "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.register({ type: "state", name: "playing", tags: ["interacting"] }) }, this), "resize.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault() }, this), "refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .owl-video-frame").remove() }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && "position" === a.property.name && this._playing && this.stop() }, this), "prepared.owl.carousel": a.proxy(function (b) { if (b.namespace) { var c = a(b.content).find(".owl-video"); c.length && (c.css("display", "none"), this.fetch(c, a(b.content))) } }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".owl-video-play-icon", a.proxy(function (a) { this.play(a) }, this)) }; e.Defaults = { video: !1, videoHeight: !1, videoWidth: !1 }, e.prototype.fetch = function (a, b) { var c = function () { return a.attr("data-vimeo-id") ? "vimeo" : a.attr("data-vzaar-id") ? "vzaar" : "youtube" }(), d = a.attr("data-vimeo-id") || a.attr("data-youtube-id") || a.attr("data-vzaar-id"), e = a.attr("data-width") || this._core.settings.videoWidth, f = a.attr("data-height") || this._core.settings.videoHeight, g = a.attr("href"); if (!g) throw new Error("Missing video URL."); if (d = g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), d[3].indexOf("youtu") > -1) c = "youtube"; else if (d[3].indexOf("vimeo") > -1) c = "vimeo"; else { if (!(d[3].indexOf("vzaar") > -1)) throw new Error("Video URL not supported."); c = "vzaar" } d = d[6], this._videos[g] = { type: c, id: d, width: e, height: f }, b.attr("data-video", g), this.thumbnail(a, this._videos[g]) }, e.prototype.thumbnail = function (b, c) { var d, e, f, g = c.width && c.height ? 'style="width:' + c.width + "px;height:" + c.height + 'px;"' : "", h = b.find("img"), i = "src", j = "", k = this._core.settings, l = function (c) { e = a('<button type="button">').addClass("owl-video-play-icon").html("&#x25BA;"), d = k.lazyLoad ? a("<div/>", { class: "owl-video-tn " + j, "src-type": "image" }).attr("data-src", c) : a("<div/>", { class: "owl-video-tn" }).css("backgroundImage", "url(" + c + ")"), b.after(d), b.after(e) }; if (b.wrap(a("<div/>", { class: "owl-video-wrapper", style: g })), this._core.settings.lazyLoad && (i = "data-src", j = "owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1; "youtube" === c.type ? (f = "//img.youtube.com/vi/" + c.id + "/hqdefault.jpg", l(f)) : "vimeo" === c.type ? a.ajax({ type: "GET", url: "//vimeo.com/api/v2/video/" + c.id + ".json", jsonp: "callback", dataType: "jsonp", success: function (a) { f = a[0].thumbnail_large, l(f) } }) : "vzaar" === c.type && a.ajax({ type: "GET", url: "//vzaar.com/api/videos/" + c.id + ".json", jsonp: "callback", dataType: "jsonp", success: function (a) { f = a.framegrab_url, l(f) } }) }, e.prototype.stop = function () { this._core.trigger("stop", null, "video"), this._playing.find(".owl-video-frame").remove(), this._playing.removeClass("owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video") }, e.prototype.play = function (b) { var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass), f = this._videos[e.attr("data-video")], g = f.width || "100%", h = f.height || this._core.$stage.height(); this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), c = a('<iframe frameborder="0" allowfullscreen mozallowfullscreen webkitAllowFullScreen></iframe>'), c.attr("height", h), c.attr("width", g), "youtube" === f.type ? c.attr("src", "//www.youtube.com/embed/" + f.id + "?autoplay=1&rel=0&v=" + f.id) : "vimeo" === f.type ? c.attr("src", "//player.vimeo.com/video/" + f.id + "?autoplay=1") : "vzaar" === f.type && c.attr("src", "//view.vzaar.com/" + f.id + "/player?autoplay=true"), a(c).wrap('<div class="owl-video-frame" />').insertAfter(e.find(".owl-video")), this._playing = e.addClass("owl-video-playing")) }, e.prototype.isInFullScreen = function () { var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement; return b && a(b).parent().hasClass("owl-video-frame") }, e.prototype.destroy = function () { var a, b; this._core.$element.off("click.owl.video"); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Video = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this.core = b, this.core.options = a.extend({}, e.Defaults, this.core.options), this.swapping = !0, this.previous = d, this.next = d, this.handlers = { "change.owl.carousel": a.proxy(function (a) { a.namespace && "position" == a.property.name && (this.previous = this.core.current(), this.next = a.property.value) }, this), "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": a.proxy(function (a) { a.namespace && (this.swapping = "translated" == a.type) }, this), "translate.owl.carousel": a.proxy(function (a) { a.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap() }, this) }, this.core.$element.on(this.handlers) }; e.Defaults = { animateOut: !1, animateIn: !1 }, e.prototype.swap = function () { if (1 === this.core.settings.items && a.support.animation && a.support.transition) { this.core.speed(0); var b, c = a.proxy(this.clear, this), d = this.core.$stage.children().eq(this.previous), e = this.core.$stage.children().eq(this.next), f = this.core.settings.animateIn, g = this.core.settings.animateOut; this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({ left: b + "px" }).addClass("animated owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated owl-animated-in").addClass(f)) } }, e.prototype.clear = function (b) { a(b.target).css({ left: "" }).removeClass("animated owl-animated-out owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd() }, e.prototype.destroy = function () { var a, b; for (a in this.handlers) this.core.$element.off(a, this.handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Animate = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { var e = function (b) { this._core = b, this._call = null, this._time = 0, this._timeout = 0, this._paused = !0, this._handlers = { "changed.owl.carousel": a.proxy(function (a) { a.namespace && "settings" === a.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : a.namespace && "position" === a.property.name && this._core.settings.autoplay && this._setAutoPlayInterval() }, this), "initialized.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.autoplay && this.play() }, this), "play.owl.autoplay": a.proxy(function (a, b, c) { a.namespace && this.play(b, c) }, this), "stop.owl.autoplay": a.proxy(function (a) { a.namespace && this.stop() }, this), "mouseover.owl.autoplay": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause() }, this), "mouseleave.owl.autoplay": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play() }, this), "touchstart.owl.core": a.proxy(function () { this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause() }, this), "touchend.owl.core": a.proxy(function () { this._core.settings.autoplayHoverPause && this.play() }, this) }, this._core.$element.on(this._handlers), this._core.options = a.extend({}, e.Defaults, this._core.options) }; e.Defaults = { autoplay: !1, autoplayTimeout: 5e3, autoplayHoverPause: !1, autoplaySpeed: !1 }, e.prototype._setAutoPlayInterval = function () { this._call = b.setTimeout(a.proxy(function () { this.next(this._core.settings.autoplaySpeed) }, this), this._core.settings.autoplayTimeout) }, e.prototype.play = function (c, d) { this._paused = !1, this._core.is("rotating") || (this._core.enter("rotating"), this._setAutoPlayInterval()) }, e.prototype.next = function (b) { this._core.is("rotating") && (this._core.settings.loop || this._core.current() < this._core.maximum()) && this._core.to(this._core.relative(this._core.current() + 1), b) }, e.prototype.stop = function () { this._core.is("rotating") && (b.clearTimeout(this._call), this._core.leave("rotating")) }, e.prototype.pause = function () { this._core.is("rotating") && !this._paused && (b.clearTimeout(this._call), this._paused = !0) }, e.prototype.destroy = function () { var a, b; this.stop(); for (a in this._handlers) this._core.$element.off(a, this._handlers[a]); for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null) }, a.fn.owlCarousel.Constructor.Plugins.Autoplay = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { "use strict"; var e = function (b) { this._core = b, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = { next: this._core.next, prev: this._core.prev, to: this._core.to }, this._handlers = { "prepared.owl.carousel": a.proxy(function (b) { b.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>") }, this), "added.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 0, this._templates.pop()) }, this), "remove.owl.carousel": a.proxy(function (a) { a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 1) }, this), "changed.owl.carousel": a.proxy(function (a) { a.namespace && "position" == a.property.name && this.draw() }, this), "initialized.owl.carousel": a.proxy(function (a) { a.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation")) }, this), "refreshed.owl.carousel": a.proxy(function (a) { a.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation")) }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers) }; e.Defaults = { nav: !1, navText: ['<span aria-label="Previous">&#x2039;</span>', '<span aria-label="Next">&#x203a;</span>'], navSpeed: !1, navElement: "button", navContainer: !1, navContainerClass: "owl-nav", navClass: ["owl-prev", "owl-next"], slideBy: 1, dotClass: "owl-dot", dotsClass: "owl-dots", dots: !0, dotsEach: !1, dotsData: !1, dotsSpeed: !1, dotsContainer: !1 }, e.prototype.initialize = function () { var b, c = this._core.settings; this._controls.$relative = (c.navContainer ? a(c.navContainer) : a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = a("<" + c.navElement + ">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click", a.proxy(function (a) { this.prev(c.navSpeed) }, this)), this._controls.$next = a("<" + c.navElement + ">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click", a.proxy(function (a) { this.next(c.navSpeed) }, this)), c.dotsData || (this._templates = [a("<button>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]), this._controls.$absolute = (c.dotsContainer ? a(c.dotsContainer) : a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "button", a.proxy(function (b) { var d = a(b.target).parent().is(this._controls.$absolute) ? a(b.target).index() : a(b.target).parent().index(); b.preventDefault(), this.to(d, c.dotsSpeed) }, this)); for (b in this._overrides) this._core[b] = a.proxy(this[b], this) }, e.prototype.destroy = function () { var a, b, c, d, e; e = this._core.settings; for (a in this._handlers) this.$element.off(a, this._handlers[a]); for (b in this._controls) "$relative" === b && e.navContainer ? this._controls[b].html("") : this._controls[b].remove(); for (d in this.overrides) this._core[d] = this._overrides[d]; for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null) }, e.prototype.update = function () { var a, b, c, d = this._core.clones().length / 2, e = d + this._core.items().length, f = this._core.settings, g = f.center || f.autoWidth || f.dotsData ? 1 : f.dotsEach || f.items; if ("page" !== f.slideBy && (f.slideBy = Math.min(f.slideBy, f.items)), f.dots || "page" == f.slideBy) for (this._pages = [], a = d, b = 0, c = 0; a < e; a++) { if (b >= g || 0 === b) { if (this._pages.push({ start: Math.min(this.maximum(), a - d), end: a - d + g - 1 }), Math.min(this.maximum(), a - d) === this.maximum()) break; b = 0, ++c } b += this._core.mergers(this._core.relative(a)) } }, e.prototype.draw = function () { var b, c = this._core.settings, d = this._core.items().length <= c.items, e = this._core.relative(this._core.current()), f = c.loop || c.rewind; this._controls.$relative.toggleClass("disabled", !c.nav || d), c.nav && (this._controls.$previous.toggleClass("disabled", !f && e <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !f && e >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !c.dots || d), c.dots && (b = this._pages.length - this._controls.$absolute.children().length, c.dotsData && 0 !== b ? this._controls.$absolute.html(this._templates.join("")) : b > 0 ? this._controls.$absolute.append(new Array(b + 1).join(this._templates[0])) : b < 0 && this._controls.$absolute.children().slice(b).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(a.inArray(this.current(), this._pages)).addClass("active")) }, e.prototype.onTrigger = function (b) { var c = this._core.settings; b.page = { index: a.inArray(this.current(), this._pages), count: this._pages.length, size: c && (c.center || c.autoWidth || c.dotsData ? 1 : c.dotsEach || c.items) } }, e.prototype.current = function () { var b = this._core.relative(this._core.current()); return a.grep(this._pages, a.proxy(function (a, c) { return a.start <= b && a.end >= b }, this)).pop() }, e.prototype.getPosition = function (b) { var c, d, e = this._core.settings; return "page" == e.slideBy ? (c = a.inArray(this.current(), this._pages), d = this._pages.length, b ? ++c : --c, c = this._pages[(c % d + d) % d].start) : (c = this._core.relative(this._core.current()), d = this._core.items().length, b ? c += e.slideBy : c -= e.slideBy), c }, e.prototype.next = function (b) { a.proxy(this._overrides.to, this._core)(this.getPosition(!0), b) }, e.prototype.prev = function (b) { a.proxy(this._overrides.to, this._core)(this.getPosition(!1), b) }, e.prototype.to = function (b, c, e) { var f; !e && this._pages.length ? (f = this._pages.length, a.proxy(this._overrides.to, this._core)(this._pages[(b % f + f) % f].start, c)) : a.proxy(this._overrides.to, this._core)(b, c) }, a.fn.owlCarousel.Constructor.Plugins.Navigation = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { "use strict"; var e = function (c) { this._core = c, this._hashes = {}, this.$element = this._core.$element, this._handlers = { "initialized.owl.carousel": a.proxy(function (c) { c.namespace && "URLHash" === this._core.settings.startPosition && a(b).trigger("hashchange.owl.navigation") }, this), "prepared.owl.carousel": a.proxy(function (b) { if (b.namespace) { var c = a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash"); if (!c) return; this._hashes[c] = b.content } }, this), "changed.owl.carousel": a.proxy(function (c) { if (c.namespace && "position" === c.property.name) { var d = this._core.items(this._core.relative(this._core.current())), e = a.map(this._hashes, function (a, b) { return a === d ? b : null }).join(); if (!e || b.location.hash.slice(1) === e) return; b.location.hash = e } }, this) }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers), a(b).on("hashchange.owl.navigation", a.proxy(function (a) { var c = b.location.hash.substring(1), e = this._core.$stage.children(), f = this._hashes[c] && e.index(this._hashes[c]); f !== d && f !== this._core.current() && this._core.to(this._core.relative(f), !1, !0) }, this)) }; e.Defaults = { URLhashListener: !1 }, e.prototype.destroy = function () { var c, d; a(b).off("hashchange.owl.navigation"); for (c in this._handlers) this._core.$element.off(c, this._handlers[c]); for (d in Object.getOwnPropertyNames(this)) "function" != typeof this[d] && (this[d] = null) }, a.fn.owlCarousel.Constructor.Plugins.Hash = e }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) { function e(b, c) { var e = !1, f = b.charAt(0).toUpperCase() + b.slice(1); return a.each((b + " " + h.join(f + " ") + f).split(" "), function (a, b) { if (g[b] !== d) return e = c ? b : !0, !1 }), e } function f(a) { return e(a, !0) } var g = a("<support>").get(0).style, h = "Webkit Moz O ms".split(" "), i = { transition: { end: { WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "oTransitionEnd", transition: "transitionend" } }, animation: { end: { WebkitAnimation: "webkitAnimationEnd", MozAnimation: "animationend", OAnimation: "oAnimationEnd", animation: "animationend" } } }, j = { csstransforms: function () { return !!e("transform") }, csstransforms3d: function () { return !!e("perspective") }, csstransitions: function () { return !!e("transition") }, cssanimations: function () { return !!e("animation") } }; j.csstransitions() && (a.support.transition = new String(f("transition")), a.support.transition.end = i.transition.end[a.support.transition]), j.cssanimations() && (a.support.animation = new String(f("animation")), a.support.animation.end = i.animation.end[a.support.animation]), j.csstransforms() && (a.support.transform = new String(f("transform")), a.support.transform3d = j.csstransforms3d()) }(window.Zepto || window.jQuery, window, document);

/**
 * Swiper 11.1.4
 * Most modern mobile touch slider and framework with hardware accelerated transitions
 * https://swiperjs.com
 *
 * Copyright 2014-2024 Vladimir Kharlampidi
 *
 * Released under the MIT License
 *
 * Released on: May 24, 2024
 */

(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
		typeof define === 'function' && define.amd ? define(factory) :
			(global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.Swiper = factory());
})(this, (function () {
	'use strict';

	function isObject(o) {
		return o !== null && typeof o === 'object' && o.constructor === Object && Object.prototype.toString.call(o).slice(8, -1) === 'Object';
	}
	function extend(target, src) {
		const to = Object(target);
		const from = Object(src);
		for (const key in from) {
			if (Object.prototype.hasOwnProperty.call(from, key)) {
				if (isObject(to[key]) && isObject(from[key])) {
					extend(to[key], from[key]);
				} else {
					to[key] = from[key];
				}
			}
		}
		return to;
	}

	// Find window object in Node.js
	const getWindow = () => {
		if (typeof window !== 'undefined') {
			return window;
		}
		return undefined;
	};

	// Find document object in Node.js
	const getDocument = () => {
		const win = getWindow();
		if (win && typeof win.document !== 'undefined') {
			return win.document;
		}
		return {
			body: {},
			addEventListener() { },
			removeEventListener() { },
			activeElement: {
				blur() { },
				nodeName: '',
			},
			querySelector() {
				return null;
			},
			querySelectorAll() {
				return [];
			},
			getElementById() {
				return null;
			},
			createEvent() {
				return {
					initEvent() { },
				};
			},
			createElement() {
				return {
					children: [],
					childNodes: [],
					style: {},
					setAttribute() { },
					getElementsByTagName() {
						return [];
					},
				};
			},
			location: {
				hash: ''
			},
		};
	};
	const ssrWindow = {
		document: getDocument(),
		window: getWindow(),
	};
	function deleteProps(obj) {
		const object = obj;
		Object.keys(object).forEach(key => {
			try {
				object[key] = null;
			} catch (e) {
				// no getter for object
			}
			try {
				delete object[key];
			} catch (e) {
				// something got wrong
			}
		});
	}
	function nextTick(callback, delay) {
		if (delay === void 0) {
			delay = 0;
		}
		return setTimeout(callback, delay);
	}
	function now() {
		return Date.now();
	}
	function getComputedStyle$1(el) {
		const window = getWindow();
		let style;
		if (window.getComputedStyle) {
			style = window.getComputedStyle(el, null);
		}
		if (!style && el.currentStyle) {
			style = el.currentStyle;
		}
		return style;
	}
	function getTranslate(el, axis) {
		if (axis === void 0) {
			axis = 'x';
		}
		const window = getWindow();
		let matrix;
		let curTransform;
		let transformMatrix;
		const curStyle = getComputedStyle$1(el);
		if (window.WebKitCSSMatrix) {
			curTransform = curStyle.transform || curStyle.webkitTransform;
			if (curTransform.split(',').length > 6) {
				curTransform = curTransform.split(', ').map(a => a.replace(',', '.')).join(', ');
			}
			// Some old versions of Webkit choke when 'none' is passed; pass
			// empty string instead in this case
			transformMatrix = new window.WebKitCSSMatrix(curTransform === 'none' ? '' : curTransform);
		} else {
			transformMatrix = curStyle.MozTransform || curStyle.OTransform || curStyle.msTransform || curStyle.transform || curStyle.getPropertyValue('transform').replace('translate(', 'matrix(1, 0, 0, 1,');
			matrix = transformMatrix.toString().split(',');
		}
		if (axis === 'x') {
			// Latest Chrome and webkits Fix
			if (window.WebKitCSSMatrix) curTransform = transformMatrix.m41;
			// Crazy IE10 Matrix
			else if (matrix.length === 16) curTransform = parseFloat(matrix[12]);
			// Normal Browsers
			else curTransform = parseFloat(matrix[4]);
		}
		if (axis === 'y') {
			// Latest Chrome and webkits Fix
			if (window.WebKitCSSMatrix) curTransform = transformMatrix.m42;
			// Crazy IE10 Matrix
			else if (matrix.length === 16) curTransform = parseFloat(matrix[13]);
			// Normal Browsers
			else curTransform = parseFloat(matrix[5]);
		}
		return curTransform || 0;
	}
	function isObject$1(o) {
		return typeof o === 'object' && o !== null && o.constructor && Object.prototype.toString.call(o).slice(8, -1) === 'Object';
	}
	function isNode(node) {
		// eslint-disable-next-line
		if (typeof window !== 'undefined' && typeof window.HTMLElement !== 'undefined') {
			return node instanceof HTMLElement;
		}
		return node && (node.nodeType === 1 || node.nodeType === 11);
	}
	function extend$1() {
		const to = Object(arguments.length <= 0 ? undefined : arguments[0]);
		const noExtend = ['__proto__', 'constructor', 'prototype'];
		for (let i = 1; i < arguments.length; i += 1) {
			const nextSource = i < 0 || arguments.length <= i ? undefined : arguments[i];
			if (nextSource !== undefined && nextSource !== null && !isNode(nextSource)) {
				const keysArray = Object.keys(Object(nextSource)).filter(key => noExtend.indexOf(key) < 0);
				for (let nextIndex = 0, len = keysArray.length; nextIndex < len; nextIndex += 1) {
					const nextKey = keysArray[nextIndex];
					const desc = Object.getOwnPropertyDescriptor(nextSource, nextKey);
					if (desc !== undefined && desc.enumerable) {
						if (isObject$1(to[nextKey]) && isObject$1(nextSource[nextKey])) {
							if (nextSource[nextKey].__swiper__) {
								to[nextKey] = nextSource[nextKey];
							} else {
								extend$1(to[nextKey], nextSource[nextKey]);
							}
						} else if (!isObject$1(to[nextKey]) && isObject$1(nextSource[nextKey])) {
							to[nextKey] = {};
							if (nextSource[nextKey].__swiper__) {
								to[nextKey] = nextSource[nextKey];
							} else {
								extend$1(to[nextKey], nextSource[nextKey]);
							}
						} else {
							to[nextKey] = nextSource[nextKey];
						}
					}
				}
			}
		}
		return to;
	}
	function setCSSProperty(el, prop, value) {
		el.style.setProperty(prop, value);
	}
	function animateCSSModeScroll(_ref) {
		let {
			swiper,
			targetPosition,
			side
		} = _ref;
		const window = getWindow();
		const startPosition = -swiper.translate;
		let startTime = null;
		let time;
		const duration = swiper.params.speed;
		swiper.wrapperEl.style.scrollSnapType = 'none';
		window.cancelAnimationFrame(swiper.cssModeFrameID);
		const dir = targetPosition > startPosition ? 'next' : 'prev';
		const isOutOfBound = (current, target) => {
			return dir === 'next' && current >= target || dir === 'prev' && current <= target;
		};
		const animate = () => {
			time = new Date().getTime();
			if (startTime === null) {
				startTime = time;
			}
			const progress = Math.max(Math.min((time - startTime) / duration, 1), 0);
			const easeProgress = 0.5 - Math.cos(progress * Math.PI) / 2;
			let currentPosition = startPosition + easeProgress * (targetPosition - startPosition);
			if (isOutOfBound(currentPosition, targetPosition)) {
				currentPosition = targetPosition;
			}
			swiper.wrapperEl.scrollTo({
				[side]: currentPosition
			});
			if (isOutOfBound(currentPosition, targetPosition)) {
				swiper.wrapperEl.style.overflow = 'hidden';
				swiper.wrapperEl.style.scrollSnapType = '';
				window.cancelAnimationFrame(swiper.cssModeFrameID);
				return;
			}
			swiper.cssModeFrameID = window.requestAnimationFrame(animate);
		};
		animate();
	}
	function elementChildren(element, selector) {
		return [...element.children].filter(el => el.matches(selector));
	}
	function showEl(el) {
		const element = el;
		if (element) {
			element.style.display = '';
		}
	}
	function hideEl(el) {
		const element = el;
		if (element) {
			element.style.display = 'none';
		}
	}
	function createElement(tagName, classNames) {
		if (classNames === void 0) {
			classNames = [];
		}
		const el = document.createElement(tagName);
		el.classList.add(...(Array.isArray(classNames) ? classNames : [classNames]));
		return el;
	}
	function elementStyle(el) {
		return el.style;
	}
	function elementIndex(el) {
		let child = el;
		if (child) {
			let i = 0;
			// eslint-disable-next-line
			while ((child = child.previousSibling) !== null) {
				if (child.nodeType === 1) i += 1;
			}
			return i;
		}
		return undefined;
	}
	function elementParents(el, selector) {
		const parents = [];
		let parent = el.parentElement;
		while (parent) {
			if (selector) {
				if (parent.matches(selector)) parents.push(parent);
			} else {
				parents.push(parent);
			}
			parent = parent.parentElement;
		}
		return parents;
	}
	function elementPrevAll(el, selector) {
		const prevEls = [];
		let prev = el.previousElementSibling;
		while (prev) {
			if (selector) {
				if (prev.matches(selector)) prevEls.push(prev);
			} else prevEls.push(prev);
			prev = prev.previousElementSibling;
		}
		return prevEls;
	}
	function elementNextAll(el, selector) {
		const nextEls = [];
		let next = el.nextElementSibling;
		while (next) {
			if (selector) {
				if (next.matches(selector)) nextEls.push(next);
			} else nextEls.push(next);
			next = next.nextElementSibling;
		}
		return nextEls;
	}
	function elementOuterSize(el, size, includeMargins) {
		const window = getWindow();
		if (includeMargins) {
			return el[size === 'width' ? 'offsetWidth' : 'offsetHeight'] + parseFloat(window.getComputedStyle(el, null).getPropertyValue(size === 'width' ? 'margin-right' : 'margin-top')) + parseFloat(window.getComputedStyle(el, null).getPropertyValue(size === 'width' ? 'margin-left' : 'margin-bottom'));
		}
		return el[size === 'width' ? 'offsetWidth' : 'offsetHeight'];
	}
	let support;
	function getSupport() {
		const window = getWindow();
		const document = getDocument();
		if (!support) {
			support = {
				smoothScroll: document.documentElement && document.documentElement.style && 'scrollBehavior' in document.documentElement.style,
				touch: !!('ontouchstart' in window || window.DocumentTouch && document instanceof window.DocumentTouch)
			};
		}
		return support;
	}
	let device;
	function getDevice(overrides) {
		if (overrides === void 0) {
			overrides = {};
		}
		const window = getWindow();
		const support = getSupport();
		if (!device) {
			device = {
				ios: false,
				android: false,
				androidChrome: false,
				desktop: false,
				iphone: false,
				ipod: false,
				ipad: false,
				edge: false,
				ie: false,
				firefox: false,
				macos: false,
				windows: false,
				winPhone: false,
				chrome: false,
				safari: false,
				browser: false,
				isWebView: false,
				...overrides
			};
			const ua = window.navigator.userAgent;
			const platform = window.navigator.platform;
			const screenWidth = window.screen.width;
			const screenHeight = window.screen.height;
			let android = ua.match(/(Android);?[\s\/]+([\d.]+)?/); // eslint-disable-line
			let ipad = ua.match(/(iPad).*OS\s([\d_]+)/);
			let ipod = ua.match(/(iPod)(.*OS\s([\d_]+))?/);
			let iphone = !ipad && ua.match(/(iPhone\sOS|iOS)\s([\d_]+)/);
			device.ios = ipad || iphone || ipod;
			device.android = android;
			if (platform === 'MacIntel') {
				device.macos = true;
			}
			if (['Win32', 'Win64', 'WOW64'].indexOf(platform) >= 0) {
				device.windows = true;
			}
			// iPad
			if (ipad) {
				device.ipad = true;
			}
			// iPhone
			if (iphone && !ipod) {
				device.iphone = true;
			}
			// iPod
			if (ipod) {
				device.ipod = true;
			}
			// Android
			if (android && ua.toLowerCase().indexOf('chrome') >= 0) {
				device.androidChrome = true;
			}

			// Windows Phone
			const winPhone = ua.match(/Windows Phone/i);
			if (winPhone) {
				device.winPhone = true;
			}

			// Browser
			const edge = ua.match(/Edge\/(\d+)\./);
			if (edge) {
				device.edge = true;
			}
			const ie = ua.match(/MSIE\s(\d+)\./) || ua.match(/Trident\/.+?rv:(\d+)\./);
			if (ie) {
				device.ie = true;
			}
			const firefox = ua.match(/Firefox\/(\d+)\./);
			if (firefox) {
				device.firefox = true;
			}
			const chrome = ua.match(/Chrome\/(\d+)\./);
			if (chrome) {
				device.chrome = true;
			}
			const safari = ua.match(/Version\/(\d+)\.\d+.+Safari/);
			if (safari) {
				device.safari = true;
			}
			if (device.safari && platform === 'MacIntel') {
				// eslint-disable-next-line
				const TouchEvents = window.TouchEvent;
				if (typeof TouchEvents === 'undefined' && 'ontouchstart' in window) {
					//
				} else if (typeof TouchEvents !== 'undefined') {
					//
				}
			}

			// WebView
			const isWebView = /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(ua);
			if (isWebView) {
				device.isWebView = true;
			}
			device.browser = device.edge || device.ie || device.firefox || device.chrome || device.safari;

			// Desktop
			device.desktop = !device.ios && !device.android && !device.winPhone;
		}
		return device;
	}
	function on(el, events, handler, options) {
		const obj = el;
		if (!obj || !obj.addEventListener) return;
		const eventsArray = Array.isArray(events) ? events : events.split(' ');
		eventsArray.forEach(event => {
			obj.addEventListener(event, handler, options);
		});
	}
	function off(el, events, handler, options) {
		const obj = el;
		if (!obj || !obj.removeEventListener) return;
		const eventsArray = Array.isArray(events) ? events : events.split(' ');
		eventsArray.forEach(event => {
			obj.removeEventListener(event, handler, options);
		});
	}
	function once(el, events, handler, options) {
		const obj = el;
		if (!obj || !obj.addEventListener) return;
		const eventsArray = Array.isArray(events) ? events : events.split(' ');
		function onceHandler() {
			for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
				args[_key] = arguments[_key];
			}
			handler(...args);
			eventsArray.forEach(event => {
				obj.removeEventListener(event, onceHandler, options);
			});
		}
		eventsArray.forEach(event => {
			obj.addEventListener(event, onceHandler, options);
		});
	}
	function getBrowser() {
		const window = getWindow();
		let browser = {
			isSafari: false,
			isWebView: false
		};
		if (!window.navigator.userAgent) {
			return browser;
		}
		const ua = window.navigator.userAgent;
		const safari = ua.match(/Version\/(\d+)\.\d+.+Safari/);
		const isWebView = /(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(ua);
		if (safari) {
			browser.isSafari = true;
		}
		browser.isWebView = isWebView;
		return browser;
	}
	const SWIPER_CORE = ['events-emitter', 'update', 'translate', 'transition', 'slide', 'loop', 'grab-cursor', 'events', 'breakpoints', 'check-overflow', 'classes', 'images'];

	// Swiper Class
	class Swiper {
		constructor() {
			let el,
				params,
				// eslint-disable-next-line
				instance;
			if (arguments.length === 1 && (arguments.length <= 0 ? undefined : arguments[0]).constructor && Object.prototype.toString.call(arguments.length <= 0 ? undefined : arguments[0]).slice(8, -1) === 'Object') {
				params = arguments.length <= 0 ? undefined : arguments[0];
			} else {
				[el, params] = arguments;
			}
			if (!params) params = {};
			params = extend$1({}, params);
			if (el && !params.el) params.el = el;
			if (params.el && typeof params.el === 'string' && ssrWindow.document.querySelector) {
				const foundEl = ssrWindow.document.querySelector(params.el);
				if (!foundEl) {
					return undefined;
				}
				params.el = foundEl;
			}
			const swiper = this;
			swiper.__swiper__ = true;
			swiper.support = getSupport();
			swiper.device = getDevice({
				userAgent: params.userAgent
			});
			swiper.browser = getBrowser();
			swiper.eventsListeners = {};
			swiper.eventsAnyListeners = [];
			swiper.modules = [...swiper.__modules__];
			if (params.modules && Array.isArray(params.modules)) {
				swiper.modules.push(...params.modules);
			}
			const allModulesParams = {};
			swiper.modules.forEach(mod => {
				mod({
					params,
					swiper,
					extendParams: moduleExtendParams(allModulesParams),
					on: swiper.on.bind(swiper),
					once: swiper.once.bind(swiper),
					off: swiper.off.bind(swiper),
					emit: swiper.emit.bind(swiper)
				});
			});

			// Extend defaults with modules params
			const swiperParams = extend$1({}, Swiper.defaults, allModulesParams);

			// Extend defaults with passed params
			swiper.params = extend$1({}, swiperParams, params);
			swiper.originalParams = extend$1({}, swiper.params);
			swiper.passedParams = extend$1({}, params);

			// add event listeners
			if (swiper.params && swiper.params.on) {
				Object.keys(swiper.params.on).forEach(eventName => {
					swiper.on(eventName, swiper.params.on[eventName]);
				});
			}
			if (swiper.params && swiper.params.onAny) {
				swiper.onAny(swiper.params.onAny);
			}

			// El
			swiper.$el = el;
			if (!swiper.$el || swiper.$el.length === 0) return undefined;
			if (swiper.$el[0]) {
				swiper.$el[0].swiper = swiper;
			}

			// Classes
			swiper.classNames = [];

			// Virtual
			extend$1(swiper, {
				// slides
				slides: [],
				slidesGrid: [],
				snapGrid: [],
				slidesSizesGrid: [],
				// isDirection
				isHorizontal() {
					return swiper.params.direction === 'horizontal';
				},
				isVertical() {
					return swiper.params.direction === 'vertical';
				},
				// Indexes
				activeIndex: 0,
				realIndex: 0,
				//
				isBeginning: true,
				isEnd: false,
				// Props
				translate: 0,
				previousTranslate: 0,
				progress: 0,
				velocity: 0,
				animating: false,
				// Locks
				allowSlideNext: swiper.params.allowSlideNext,
				allowSlidePrev: swiper.params.allowSlidePrev,
				// Touch Events
				touchEvents: function touchEvents() {
					const support = swiper.support;
					const params = swiper.params;
					const touchEventsTouch = {
						start: 'touchstart',
						move: 'touchmove',
						end: 'touchend',
						cancel: 'touchcancel'
					};
					const touchEventsDesktop = {
						start: 'mousedown',
						move: 'mousemove',
						end: 'mouseup'
					};
					if (support.touch && !params.simulateTouch) {
						return touchEventsTouch;
					}
					return touchEventsDesktop;
				}(),
				touchEventsData: {
					isTouched: undefined,
					isMoved: undefined,
					allowTouchCallbacks: undefined,
					touchStartTime: undefined,
					isScrolling: undefined,
					currentTranslate: undefined,
					startTranslate: undefined,
					allowThresholdMove: undefined,
					// Form elements to match
					formElements: 'input, select, option, textarea, button, video, label',
					// Last click time
					lastClickTime: now(),
					clickTimeout: undefined,
					// Velocities
					velocities: [],
					allowMomentumBounce: undefined,
					startMoving: undefined
				},
				// Clicks
				allowClick: true,
				// Touches
				allowTouchMove: swiper.params.allowTouchMove,
				touches: {
					startX: 0,
					startY: 0,
					currentX: 0,
					currentY: 0,
					diff: 0
				},
				// Images
				imagesToLoad: [],
				imagesLoaded: 0
			});
			swiper.emit('_swiper');

			// Init
			if (swiper.params.init) {
				swiper.init();
			}

			// Return app instance
			return swiper;
		}
	}
	const prototypes = {
		eventsEmitter,
		update,
		translate,
		transition,
		slide,
		loop,
		grabCursor,
		events,
		breakpoints,
		checkOverflow,
		classes,
		images
	};
	Object.keys(prototypes).forEach(prototypeGroup => {
		Object.keys(prototypes[prototypeGroup]).forEach(protoMethod => {
			Swiper.prototype[protoMethod] = prototypes[prototypeGroup][protoMethod];
		});
	});
	Swiper.use(core);
	return Swiper;
}));


/* jQuery Parallax
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
/*
Plugin: jQuery Parallax
Version 1.1.3
Author: Ian Lunn
Twitter: @IanLunn
Author URL: http://www.ianlunn.co.uk/
Plugin URL: http://www.ianlunn.co.uk/plugins/jquery-parallax/

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html
*/

(function (jQuery) {
	var jQuerywindow = jQuery(window);
	var windowHeight = jQuerywindow.height();

	jQuerywindow.resize(function () {
		windowHeight = jQuerywindow.height();
	});

	jQuery.fn.parallax = function (xpos, speedFactor, outerHeight) {
		var jQuerythis = jQuery(this);
		var getHeight;
		var firstTop;
		var paddingTop = 0;

		//get the starting position of each element to have parallax applied to it		
		jQuerythis.each(function () {
			firstTop = jQuerythis.offset().top;
		});

		if (outerHeight) {
			getHeight = function (jqo) {
				return jqo.outerHeight(true);
			};
		} else {
			getHeight = function (jqo) {
				return jqo.height();
			};
		}

		// setup defaults if arguments aren't specified
		if (arguments.length < 1 || xpos === null) xpos = "50%";
		if (arguments.length < 2 || speedFactor === null) speedFactor = 0.1;
		if (arguments.length < 3 || outerHeight === null) outerHeight = true;

		// function to be called whenever the window is scrolled or resized
		function update() {
			var pos = jQuerywindow.scrollTop();

			jQuerythis.each(function () {
				var jQueryelement = jQuery(this);
				var top = jQueryelement.offset().top;
				var height = getHeight(jQueryelement);

				// Check if totally above or totally below viewport
				if (top + height < pos || top > pos + windowHeight) {
					return;
				}

				jQuerythis.css('backgroundPosition', xpos + " " + Math.round((firstTop - pos) * speedFactor) + "px");
			});
		}

		jQuerywindow.bind('scroll', update).resize(update);
		update();
	};
})(jQuery);


/*
	 _ _      _       _
 ___| (_) ___| | __  (_)___
/ __| | |/ __| |/ /  | / __|
\__ \ | | (__|   < _ | \__ \
|___/_|_|\___|_|\_(_)/ |___/
				   |__/

 Version: 1.8.1
  Author: Ken Wheeler
 Website: http://kenwheeler.github.io
	Docs: http://kenwheeler.github.io/slick
	Repo: http://github.com/kenwheeler/slick
  Issues: http://github.com/kenwheeler/slick/issues

 */
/* global window, document, define, jQuery, setInterval, clearInterval */
; (function (factory) {
	'use strict';
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else if (typeof exports !== 'undefined') {
		module.exports = factory(require('jquery'));
	} else {
		factory(jQuery);
	}

}(function ($) {
	'use strict';
	var Slick = window.Slick || {};

	Slick = (function () {

		var instanceUid = 0;

		function Slick(element, settings) {

			var _ = this, dataSettings;

			_.defaults = {
				accessibility: true,
				adaptiveHeight: false,
				appendArrows: $(element),
				appendDots: $(element),
				arrows: true,
				asNavFor: null,
				prevArrow: '<button class="slick-prev" aria-label="Previous" type="button">Previous</button>',
				nextArrow: '<button class="slick-next" aria-label="Next" type="button">Next</button>',
				autoplay: false,
				autoplaySpeed: 3000,
				centerMode: false,
				centerPadding: '50px',
				cssEase: 'ease',
				customPaging: function (slider, i) {
					return $('<button type="button"></button>').text(i + 1);
				},
				dots: false,
				dotsClass: 'slick-dots',
				draggable: true,
				easing: 'linear',
				edgeFriction: 0.35,
				fade: false,
				focusOnSelect: false,
				focusOnChange: false,
				infinite: true,
				initialSlide: 0,
				lazyLoad: 'ondemand',
				mobileFirst: false,
				pauseOnHover: true,
				pauseOnFocus: true,
				pauseOnDotsHover: false,
				respondTo: 'window',
				responsive: null,
				rows: 1,
				rtl: false,
				slide: '',
				slidesPerRow: 1,
				slidesToShow: 1,
				slidesToScroll: 1,
				speed: 500,
				swipe: true,
				swipeToSlide: false,
				touchMove: true,
				touchThreshold: 5,
				useCSS: true,
				useTransform: true,
				variableWidth: false,
				vertical: false,
				verticalSwiping: false,
				waitForAnimate: true,
				zIndex: 1000
			};

			_.initials = {
				animating: false,
				dragging: false,
				autoPlayTimer: null,
				currentDirection: 0,
				currentLeft: null,
				currentSlide: 0,
				direction: 1,
				$dots: null,
				listWidth: null,
				listHeight: null,
				loadIndex: 0,
				$nextArrow: null,
				$prevArrow: null,
				scrolling: false,
				slideCount: null,
				slideWidth: null,
				$slideTrack: null,
				$slides: null,
				sliding: false,
				slideOffset: 0,
				swipeLeft: null,
				swiping: false,
				$list: null,
				touchObject: {},
				transformsEnabled: false,
				unslicked: false
			};

			$.extend(_, _.initials);

			_.activeBreakpoint = null;
			_.animType = null;
			_.animProp = null;
			_.breakpoints = [];
			_.breakpointSettings = [];
			_.cssTransitions = false;
			_.focussed = false;
			_.interrupted = false;
			_.hidden = 'hidden';
			_.paused = true;
			_.positionProp = null;
			_.respondTo = null;
			_.rowCount = 1;
			_.shouldClick = true;
			_.$slider = $(element);
			_.$slidesCache = null;
			_.transformType = null;
			_.transitionType = null;
			_.visibilityChange = 'visibilitychange';
			_.windowWidth = 0;
			_.windowTimer = null;

			dataSettings = $(element).data('slick') || {};

			_.options = $.extend({}, _.defaults, settings, dataSettings);

			_.currentSlide = _.options.initialSlide;

			_.originalSettings = _.options;

			if (typeof document.mozHidden !== 'undefined') {
				_.hidden = 'mozHidden';
				_.visibilityChange = 'mozvisibilitychange';
			} else if (typeof document.webkitHidden !== 'undefined') {
				_.hidden = 'webkitHidden';
				_.visibilityChange = 'webkitvisibilitychange';
			}

			_.autoPlay = $.proxy(_.autoPlay, _);
			_.autoPlayClear = $.proxy(_.autoPlayClear, _);
			_.autoPlayIterator = $.proxy(_.autoPlayIterator, _);
			_.changeSlide = $.proxy(_.changeSlide, _);
			_.clickHandler = $.proxy(_.clickHandler, _);
			_.selectHandler = $.proxy(_.selectHandler, _);
			_.setPosition = $.proxy(_.setPosition, _);
			_.swipeHandler = $.proxy(_.swipeHandler, _);
			_.dragHandler = $.proxy(_.dragHandler, _);
			_.keyHandler = $.proxy(_.keyHandler, _);

			_.instanceUid = instanceUid++;

			// A simple way to check for HTML strings
			// Strict HTML recognition (must start with <)
			// Extracted from jQuery v1.11 source
			_.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/;


			_.registerBreakpoints();
			_.init(true);

		}

		return Slick;

	}());

	Slick.prototype.activateADA = function () {
		var _ = this;

		_.$slideTrack.find('.slick-active').attr({
			'aria-hidden': 'false',
			'tabindex': '0'
		}).find('a, input, button, select').attr({
			'tabindex': '0'
		});

	};

	Slick.prototype.addSlide = Slick.prototype.slickAdd = function (markup, index, addBefore) {

		var _ = this;

		if (typeof (index) === 'boolean') {
			addBefore = index;
			index = null;
		} else if (index < 0 || (index >= _.slideCount)) {
			return false;
		}

		_.unload();

		if (typeof (index) === 'number') {
			if (index === 0 && _.$slides.length === 0) {
				$(markup).appendTo(_.$slideTrack);
			} else if (addBefore) {
				$(markup).insertBefore(_.$slides.eq(index));
			} else {
				$(markup).insertAfter(_.$slides.eq(index));
			}
		} else {
			if (addBefore === true) {
				$(markup).prependTo(_.$slideTrack);
			} else {
				$(markup).appendTo(_.$slideTrack);
			}
		}

		_.$slides = _.$slideTrack.children(this.options.slide);

		_.$slideTrack.children(this.options.slide).detach();

		_.$slideTrack.append(_.$slides);

		_.$slides.each(function (index, element) {
			$(element).attr('data-slick-index', index);
		});

		_.$slidesCache = _.$slides;

		_.reinit();

	};

	Slick.prototype.animateHeight = function () {
		var _ = this;
		if (_.options.slidesToShow === 1 && _.options.adaptiveHeight === true && _.options.vertical === false) {
			var targetHeight = _.$slides.eq(_.currentSlide).outerHeight(true);
			_.$list.animate({
				height: targetHeight
			}, _.options.speed);
		}
	};

	Slick.prototype.animateSlide = function (targetLeft, callback) {

		var animProps = {},
			_ = this;

		_.animateHeight();

		if (_.options.rtl === true && _.options.vertical === false) {
			targetLeft = -targetLeft;
		}
		if (_.transformsEnabled === false) {
			if (_.options.vertical === false) {
				_.$slideTrack.animate({
					left: targetLeft
				}, _.options.speed, _.options.easing, callback);
			} else {
				_.$slideTrack.animate({
					top: targetLeft
				}, _.options.speed, _.options.easing, callback);
			}

		} else {

			if (_.cssTransitions === false) {
				if (_.options.rtl === true) {
					_.currentLeft = -(_.currentLeft);
				}
				$({
					animStart: _.currentLeft
				}).animate({
					animStart: targetLeft
				}, {
					duration: _.options.speed,
					easing: _.options.easing,
					step: function (now) {
						now = Math.ceil(now);
						if (_.options.vertical === false) {
							animProps[_.animType] = 'translate(' +
								now + 'px, 0px)';
							_.$slideTrack.css(animProps);
						} else {
							animProps[_.animType] = 'translate(0px,' +
								now + 'px)';
							_.$slideTrack.css(animProps);
						}
					},
					complete: function () {
						if (callback) {
							callback.call();
						}
					}
				});

			} else {

				_.applyTransition();
				targetLeft = Math.ceil(targetLeft);

				if (_.options.vertical === false) {
					animProps[_.animType] = 'translate3d(' + targetLeft + 'px, 0px, 0px)';
				} else {
					animProps[_.animType] = 'translate3d(0px,' + targetLeft + 'px, 0px)';
				}
				_.$slideTrack.css(animProps);

				if (callback) {
					setTimeout(function () {

						_.disableTransition();

						callback.call();
					}, _.options.speed);
				}

			}

		}

	};

	Slick.prototype.getNavTarget = function () {

		var _ = this,
			asNavFor = _.options.asNavFor;

		if (asNavFor && asNavFor !== null) {
			asNavFor = $(asNavFor).not(_.$slider);
		}

		return asNavFor;

	};

	Slick.prototype.asNavFor = function (index) {

		var _ = this,
			asNavFor = _.getNavTarget();

		if (asNavFor !== null && typeof asNavFor === 'object') {
			asNavFor.each(function () {
				var target = $(this).slick('getSlick');
				if (!target.unslicked) {
					target.slideHandler(index, true);
				}
			});
		}

	};

	Slick.prototype.applyTransition = function (slide) {

		var _ = this,
			transition = {};

		if (_.options.fade === false) {
			transition[_.transitionType] = _.transformType + ' ' + _.options.speed + 'ms ' + _.options.cssEase;
		} else {
			transition[_.transitionType] = 'opacity ' + _.options.speed + 'ms ' + _.options.cssEase;
		}

		if (_.options.fade === false) {
			_.$slideTrack.css(transition);
		} else {
			_.$slides.eq(slide).css(transition);
		}

	};

	Slick.prototype.autoPlay = function () {

		var _ = this;

		_.autoPlayClear();

		if (_.slideCount > _.options.slidesToShow) {
			_.autoPlayTimer = setInterval(_.autoPlayIterator, _.options.autoplaySpeed);
		}

	};

	Slick.prototype.autoPlayClear = function () {

		var _ = this;

		if (_.autoPlayTimer) {
			clearInterval(_.autoPlayTimer);
		}

	};

	Slick.prototype.autoPlayIterator = function () {

		var _ = this,
			slideTo = _.currentSlide + _.options.slidesToScroll;

		if (!_.paused && !_.interrupted && !_.focussed) {

			if (_.options.infinite === false) {

				if (_.direction === 1 && (_.currentSlide + 1) === (_.slideCount - 1)) {
					_.direction = 0;
				}

				else if (_.direction === 0) {

					slideTo = _.currentSlide - _.options.slidesToScroll;

					if (_.currentSlide - 1 === 0) {
						_.direction = 1;
					}

				}

			}

			_.slideHandler(slideTo);

		}

	};

	Slick.prototype.buildArrows = function () {

		var _ = this;

		if (_.options.arrows === true) {

			_.$prevArrow = $(_.options.prevArrow).addClass('slick-arrow');
			_.$nextArrow = $(_.options.nextArrow).addClass('slick-arrow');

			if (_.slideCount > _.options.slidesToShow) {

				_.$prevArrow.removeClass('slick-hidden').removeAttr('aria-hidden tabindex');
				_.$nextArrow.removeClass('slick-hidden').removeAttr('aria-hidden tabindex');

				if (_.htmlExpr.test(_.options.prevArrow)) {
					_.$prevArrow.prependTo(_.options.appendArrows);
				}

				if (_.htmlExpr.test(_.options.nextArrow)) {
					_.$nextArrow.appendTo(_.options.appendArrows);
				}

				if (_.options.infinite !== true) {
					_.$prevArrow
						.addClass('slick-disabled')
						.attr('aria-disabled', 'true');
				}

			} else {

				_.$prevArrow.add(_.$nextArrow)

					.addClass('slick-hidden')
					.attr({
						'aria-disabled': 'true',
						'tabindex': '-1'
					});

			}

		}

	};

	Slick.prototype.buildDots = function () {

		var _ = this,
			i, dot;

		if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

			_.$slider.addClass('slick-dotted');

			dot = $('<ul></ul>').addClass(_.options.dotsClass);

			for (i = 0; i <= _.getDotCount(); i += 1) {
				dot.append($('<li></li>').append(_.options.customPaging.call(this, _, i)));
			}

			_.$dots = dot.appendTo(_.options.appendDots);

			_.$dots.find('li').first().addClass('slick-active');

		}

	};

	Slick.prototype.buildOut = function () {

		var _ = this;

		_.$slides =
			_.$slider
				.children(_.options.slide + ':not(.slick-cloned)')
				.addClass('slick-slide');

		_.slideCount = _.$slides.length;

		_.$slides.each(function (index, element) {
			$(element)
				.attr('data-slick-index', index)
				.data('originalStyling', $(element).attr('style') || '');
		});

		_.$slider.addClass('slick-slider');

		_.$slideTrack = (_.slideCount === 0) ?
			$('<div class="slick-track"></div>').appendTo(_.$slider) :
			_.$slides.wrapAll('<div class="slick-track"></div>').parent();

		_.$list = _.$slideTrack.wrap(
			'<div class="slick-list"></div>').parent();
		_.$slideTrack.css('opacity', 0);

		if (_.options.centerMode === true || _.options.swipeToSlide === true) {
			_.options.slidesToScroll = 1;
		}

		$('img[data-lazy]', _.$slider).not('[src]').addClass('slick-loading');

		_.setupInfinite();

		_.buildArrows();

		_.buildDots();

		_.updateDots();


		_.setSlideClasses(typeof _.currentSlide === 'number' ? _.currentSlide : 0);

		if (_.options.draggable === true) {
			_.$list.addClass('draggable');
		}

	};

	Slick.prototype.buildRows = function () {

		var _ = this, a, b, c, newSlides, numOfSlides, originalSlides, slidesPerSection;

		newSlides = document.createDocumentFragment();
		originalSlides = _.$slider.children();

		if (_.options.rows > 0) {

			slidesPerSection = _.options.slidesPerRow * _.options.rows;
			numOfSlides = Math.ceil(
				originalSlides.length / slidesPerSection
			);

			for (a = 0; a < numOfSlides; a++) {
				var slide = document.createElement('div');
				for (b = 0; b < _.options.rows; b++) {
					var row = document.createElement('div');
					for (c = 0; c < _.options.slidesPerRow; c++) {
						var target = (a * slidesPerSection + ((b * _.options.slidesPerRow) + c));
						if (originalSlides.get(target)) {
							row.appendChild(originalSlides.get(target));
						}
					}
					slide.appendChild(row);
				}
				newSlides.appendChild(slide);
			}

			_.$slider.empty().append(newSlides);
			_.$slider.children().children().children()
				.css({
					'width': (100 / _.options.slidesPerRow) + '%',
					'display': 'inline-block'
				});

		}

	};

	Slick.prototype.checkResponsive = function (initial, forceUpdate) {

		var _ = this,
			breakpoint, targetBreakpoint, respondToWidth, triggerBreakpoint = false;
		var sliderWidth = _.$slider.width();
		var windowWidth = window.innerWidth || $(window).width();

		if (_.respondTo === 'window') {
			respondToWidth = windowWidth;
		} else if (_.respondTo === 'slider') {
			respondToWidth = sliderWidth;
		} else if (_.respondTo === 'min') {
			respondToWidth = Math.min(windowWidth, sliderWidth);
		}

		if (_.options.responsive &&
			_.options.responsive.length &&
			_.options.responsive !== null) {

			targetBreakpoint = null;

			for (breakpoint in _.breakpoints) {
				if (_.breakpoints.hasOwnProperty(breakpoint)) {
					if (_.originalSettings.mobileFirst === false) {
						if (respondToWidth < _.breakpoints[breakpoint]) {
							targetBreakpoint = _.breakpoints[breakpoint];
						}
					} else {
						if (respondToWidth > _.breakpoints[breakpoint]) {
							targetBreakpoint = _.breakpoints[breakpoint];
						}
					}
				}
			}

			if (targetBreakpoint !== null) {
				if (_.activeBreakpoint !== null) {
					if (targetBreakpoint !== _.activeBreakpoint || forceUpdate) {
						_.activeBreakpoint =
							targetBreakpoint;
						if (_.breakpointSettings[targetBreakpoint] === 'unslick') {
							_.unslick(targetBreakpoint);
						} else {
							_.options = $.extend({}, _.originalSettings,
								_.breakpointSettings[
								targetBreakpoint]);
							if (initial === true) {
								_.currentSlide = _.options.initialSlide;
							}
							_.refresh(initial);
						}
						triggerBreakpoint = targetBreakpoint;
					}
				} else {
					_.activeBreakpoint = targetBreakpoint;
					if (_.breakpointSettings[targetBreakpoint] === 'unslick') {
						_.unslick(targetBreakpoint);
					} else {
						_.options = $.extend({}, _.originalSettings,
							_.breakpointSettings[
							targetBreakpoint]);
						if (initial === true) {
							_.currentSlide = _.options.initialSlide;
						}
						_.refresh(initial);
					}
					triggerBreakpoint = targetBreakpoint;
				}
			} else {
				if (_.activeBreakpoint !== null) {
					_.activeBreakpoint = null;
					_.options = _.originalSettings;
					if (initial === true) {
						_.currentSlide = _.options.initialSlide;
					}
					_.refresh(initial);
					triggerBreakpoint = targetBreakpoint;
				}
			}

			// only trigger breakpoints during an actual break. not on initialize.
			if (!initial && triggerBreakpoint !== false) {
				_.$slider.trigger('breakpoint', [_, triggerBreakpoint]);
			}
		}

	};

	Slick.prototype.changeSlide = function (event, dontAnimate) {

		var _ = this,
			$target = $(event.currentTarget),
			indexOffset, slideOffset, unevenOffset;

		// If target is a link, prevent default action.
		if ($target.is('a')) {
			event.preventDefault();
		}

		// If target is not the <li> element (ie: a child), find the <li>.
		if (!$target.is('li')) {
			$target = $target.closest('li');
		}

		unevenOffset = (_.slideCount % _.options.slidesToScroll !== 0);
		indexOffset = unevenOffset ? 0 : (_.slideCount - _.currentSlide) % _.options.slidesToScroll;

		switch (event.data.message) {

			case 'previous':
				slideOffset = indexOffset === 0 ? _.options.slidesToScroll : _.options.slidesToShow - indexOffset;
				if (_.slideCount > _.options.slidesToShow) {
					_.slideHandler(_.currentSlide - slideOffset, false, dontAnimate);
				}
				break;

			case 'next':
				slideOffset = indexOffset === 0 ? _.options.slidesToScroll : indexOffset;
				if (_.slideCount > _.options.slidesToShow) {
					_.slideHandler(_.currentSlide + slideOffset, false, dontAnimate);
				}
				break;

			case 'index':
				var index = event.data.index === 0 ? 0 :
					event.data.index || $target.index() * _.options.slidesToScroll;

				_.slideHandler(_.checkNavigable(index), false, dontAnimate);
				$target.children().trigger('focus');
				break;

			default:
				return;
		}

	};

	Slick.prototype.checkNavigable = function (index) {

		var _ = this,
			navigables, prevNavigable;

		navigables = _.getNavigableIndexes();
		prevNavigable = 0;
		if (index > navigables[navigables.length - 1]) {
			index = navigables[navigables.length - 1];
		} else {
			for (var n in navigables) {
				if (index < navigables[n]) {
					index = prevNavigable;
					break;
				}
				prevNavigable = navigables[n];
			}
		}

		return index;
	};

	Slick.prototype.cleanUpEvents = function () {

		var _ = this;

		if (_.options.dots && _.$dots !== null) {

			$('li', _.$dots)
				.off('click.slick', _.changeSlide)
				.off('mouseenter.slick', $.proxy(_.interrupt, _, true))
				.off('mouseleave.slick', $.proxy(_.interrupt, _, false));

			if (_.options.accessibility === true) {
				_.$dots.off('keydown.slick', _.keyHandler);
			}
		}

		_.$slider.off('focus.slick blur.slick');

		if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {
			_.$prevArrow && _.$prevArrow.off('click.slick', _.changeSlide);
			_.$nextArrow && _.$nextArrow.off('click.slick', _.changeSlide);

			if (_.options.accessibility === true) {
				_.$prevArrow && _.$prevArrow.off('keydown.slick', _.keyHandler);
				_.$nextArrow && _.$nextArrow.off('keydown.slick', _.keyHandler);
			}
		}

		_.$list.off('touchstart.slick mousedown.slick', _.swipeHandler);
		_.$list.off('touchmove.slick mousemove.slick', _.swipeHandler);
		_.$list.off('touchend.slick mouseup.slick', _.swipeHandler);
		_.$list.off('touchcancel.slick mouseleave.slick', _.swipeHandler);

		_.$list.off('click.slick', _.clickHandler);

		$(document).off(_.visibilityChange, _.visibility);

		_.cleanUpSlideEvents();

		if (_.options.accessibility === true) {
			_.$list.off('keydown.slick', _.keyHandler);
		}

		if (_.options.focusOnSelect === true) {
			$(_.$slideTrack).children().off('click.slick', _.selectHandler);
		}

		$(window).off('orientationchange.slick.slick-' + _.instanceUid, _.orientationChange);

		$(window).off('resize.slick.slick-' + _.instanceUid, _.resize);

		$('[draggable!=true]', _.$slideTrack).off('dragstart', _.preventDefault);

		$(window).off('load.slick.slick-' + _.instanceUid, _.setPosition);

	};

	Slick.prototype.cleanUpSlideEvents = function () {

		var _ = this;

		_.$list.off('mouseenter.slick', $.proxy(_.interrupt, _, true));
		_.$list.off('mouseleave.slick', $.proxy(_.interrupt, _, false));

	};

	Slick.prototype.cleanUpRows = function () {

		var _ = this, originalSlides;

		if (_.options.rows > 0) {
			originalSlides = _.$slides.children().children();
			originalSlides.removeAttr('style');
			_.$slider.empty().append(originalSlides);
		}

	};

	Slick.prototype.clickHandler = function (event) {

		var _ = this;

		if (_.shouldClick === false) {
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();
		}

	};

	Slick.prototype.destroy = function (refresh) {

		var _ = this;

		_.autoPlayClear();

		_.touchObject = {};

		_.cleanUpEvents();

		$('.slick-cloned', _.$slider).detach();

		if (_.$dots) {
			_.$dots.remove();
		}

		if (_.$prevArrow && _.$prevArrow.length) {

			_.$prevArrow
				.removeClass('slick-disabled slick-arrow slick-hidden')
				.removeAttr('aria-hidden aria-disabled tabindex')
				.css('display', '');

			if (_.htmlExpr.test(_.options.prevArrow)) {
				_.$prevArrow.remove();
			}
		}

		if (_.$nextArrow && _.$nextArrow.length) {

			_.$nextArrow
				.removeClass('slick-disabled slick-arrow slick-hidden')
				.removeAttr('aria-hidden aria-disabled tabindex')
				.css('display', '');

			if (_.htmlExpr.test(_.options.nextArrow)) {
				_.$nextArrow.remove();
			}
		}


		if (_.$slides) {

			_.$slides
				.removeClass('slick-slide slick-active slick-center slick-visible slick-current')
				.removeAttr('aria-hidden')
				.removeAttr('data-slick-index')
				.each(function () {
					$(this).attr('style', $(this).data('originalStyling'));
				});

			_.$slideTrack.children(this.options.slide).detach();

			_.$slideTrack.detach();

			_.$list.detach();

			_.$slider.append(_.$slides);
		}

		_.cleanUpRows();

		_.$slider.removeClass('slick-slider');
		_.$slider.removeClass('slick-initialized');
		_.$slider.removeClass('slick-dotted');

		_.unslicked = true;

		if (!refresh) {
			_.$slider.trigger('destroy', [_]);
		}

	};

	Slick.prototype.disableTransition = function (slide) {

		var _ = this,
			transition = {};

		transition[_.transitionType] = '';

		if (_.options.fade === false) {
			_.$slideTrack.css(transition);
		} else {
			_.$slides.eq(slide).css(transition);
		}

	};

	Slick.prototype.fadeSlide = function (slideIndex, callback) {

		var _ = this;

		if (_.cssTransitions === false) {

			_.$slides.eq(slideIndex).css({
				zIndex: _.options.zIndex
			});

			_.$slides.eq(slideIndex).animate({
				opacity: 1
			}, _.options.speed, _.options.easing, callback);

		} else {

			_.applyTransition(slideIndex);

			_.$slides.eq(slideIndex).css({
				opacity: 1,
				zIndex: _.options.zIndex
			});

			if (callback) {
				setTimeout(function () {

					_.disableTransition(slideIndex);

					callback.call();
				}, _.options.speed);
			}

		}

	};

	Slick.prototype.fadeSlideOut = function (slideIndex) {

		var _ = this;

		if (_.cssTransitions === false) {

			_.$slides.eq(slideIndex).animate({
				opacity: 0,
				zIndex: _.options.zIndex - 2
			}, _.options.speed, _.options.easing);

		} else {

			_.applyTransition(slideIndex);

			_.$slides.eq(slideIndex).css({
				opacity: 0,
				zIndex: _.options.zIndex - 2
			});

		}

	};

	Slick.prototype.filterSlides = Slick.prototype.slickFilter = function (filter) {

		var _ = this;

		if (filter !== null) {

			_.$slidesCache = _.$slides;

			_.unload();

			_.$slideTrack.children(this.options.slide).detach();

			_.$slidesCache.filter(filter).appendTo(_.$slideTrack);

			_.reinit();

		}

	};

	Slick.prototype.focusHandler = function () {

		var _ = this;

		// If any child element receives focus within the slider we need to pause the autoplay
		_.$slider
			.off('focus.slick blur.slick')
			.on(
				'focus.slick',
				'*',
				function (event) {
					var $sf = $(this);

					setTimeout(function () {
						if (_.options.pauseOnFocus) {
							if ($sf.is(':focus')) {
								_.focussed = true;
								_.autoPlay();
							}
						}
					}, 0);
				}
			).on(
				'blur.slick',
				'*',
				function (event) {
					var $sf = $(this);

					// When a blur occurs on any elements within the slider we become unfocused
					if (_.options.pauseOnFocus) {
						_.focussed = false;
						_.autoPlay();
					}
				}
			);
	};

	Slick.prototype.getCurrent = Slick.prototype.slickCurrentSlide = function () {

		var _ = this;
		return _.currentSlide;

	};

	Slick.prototype.getDotCount = function () {

		var _ = this;

		var breakPoint = 0;
		var counter = 0;
		var pagerQty = 0;

		if (_.options.infinite === true) {
			if (_.slideCount <= _.options.slidesToShow) {
				++pagerQty;
			} else {
				while (breakPoint < _.slideCount) {
					++pagerQty;
					breakPoint = counter + _.options.slidesToScroll;
					counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
				}
			}
		} else if (_.options.centerMode === true) {
			pagerQty = _.slideCount;
		} else if (!_.options.asNavFor) {
			pagerQty = 1 + Math.ceil((_.slideCount - _.options.slidesToShow) / _.options.slidesToScroll);
		} else {
			while (breakPoint < _.slideCount) {
				++pagerQty;
				breakPoint = counter + _.options.slidesToScroll;
				counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
			}
		}

		return pagerQty - 1;

	};

	Slick.prototype.getLeft = function (slideIndex) {

		var _ = this,
			targetLeft,
			verticalHeight,
			verticalOffset = 0,
			targetSlide,
			coef;

		_.slideOffset = 0;
		verticalHeight = _.$slides.first().outerHeight(true);

		if (_.options.infinite === true) {
			if (_.slideCount > _.options.slidesToShow) {
				_.slideOffset = (_.slideWidth * _.options.slidesToShow) * -1;
				coef = -1;

				if (_.options.vertical === true && _.options.centerMode === true) {
					if (_.options.slidesToShow === 2) {
						coef = -1.5;
					} else if (_.options.slidesToShow === 1) {
						coef = -2;
					}
				}
				verticalOffset = (verticalHeight * _.options.slidesToShow) * coef;
			}
			if (_.slideCount % _.options.slidesToScroll !== 0) {
				if (slideIndex + _.options.slidesToScroll > _.slideCount && _.slideCount > _.options.slidesToShow) {
					if (slideIndex > _.slideCount) {
						_.slideOffset = ((_.options.slidesToShow - (slideIndex - _.slideCount)) * _.slideWidth) * -1;
						verticalOffset = ((_.options.slidesToShow - (slideIndex - _.slideCount)) * verticalHeight) * -1;
					} else {
						_.slideOffset = ((_.slideCount % _.options.slidesToScroll) * _.slideWidth) * -1;
						verticalOffset = ((_.slideCount % _.options.slidesToScroll) * verticalHeight) * -1;
					}
				}
			}
		} else {
			if (slideIndex + _.options.slidesToShow > _.slideCount) {
				_.slideOffset = ((slideIndex + _.options.slidesToShow) - _.slideCount) * _.slideWidth;
				verticalOffset = ((slideIndex + _.options.slidesToShow) - _.slideCount) * verticalHeight;
			}
		}

		if (_.slideCount <= _.options.slidesToShow) {
			_.slideOffset = 0;
			verticalOffset = 0;
		}

		if (_.options.centerMode === true && _.slideCount <= _.options.slidesToShow) {
			_.slideOffset = ((_.slideWidth * Math.floor(_.options.slidesToShow)) / 2) - ((_.slideWidth * _.slideCount) / 2);
		} else if (_.options.centerMode === true && _.options.infinite === true) {
			_.slideOffset += _.slideWidth * Math.floor(_.options.slidesToShow / 2) - _.slideWidth;
		} else if (_.options.centerMode === true) {
			_.slideOffset = 0;
			_.slideOffset += _.slideWidth * Math.floor(_.options.slidesToShow / 2);
		}

		if (_.options.vertical === false) {
			targetLeft = ((slideIndex * _.slideWidth) * -1) + _.slideOffset;
		} else {
			targetLeft = ((slideIndex * verticalHeight) * -1) + verticalOffset;
		}

		if (_.options.variableWidth === true) {

			if (_.slideCount <= _.options.slidesToShow || _.options.infinite === false) {
				targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex);
			} else {
				targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex + _.options.slidesToShow);
			}

			if (_.options.rtl === true) {
				if (targetSlide[0]) {
					targetLeft = (_.$slideTrack.width() - targetSlide[0].offsetLeft - targetSlide.width()) * -1;
				} else {
					targetLeft = 0;
				}
			} else {
				targetLeft = targetSlide[0] ? targetSlide[0].offsetLeft * -1 : 0;
			}

			if (_.options.centerMode === true) {
				if (_.slideCount <= _.options.slidesToShow || _.options.infinite === false) {
					targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex);
				} else {
					targetSlide = _.$slideTrack.children('.slick-slide').eq(slideIndex + _.options.slidesToShow + 1);
				}

				if (_.options.rtl === true) {
					if (targetSlide[0]) {
						targetLeft = (_.$slideTrack.width() - targetSlide[0].offsetLeft - targetSlide.width()) * -1;
					} else {
						targetLeft = 0;
					}
				} else {
					targetLeft = targetSlide[0] ? targetSlide[0].offsetLeft * -1 : 0;
				}

				targetLeft += (_.$list.width() - targetSlide.outerWidth()) / 2;
			}
		}

		return targetLeft;

	};

	Slick.prototype.getOption = Slick.prototype.slickGetOption = function (option) {

		var _ = this;

		return _.options[option];

	};

	Slick.prototype.getNavigableIndexes = function () {

		var _ = this,
			breakPoint = 0,
			counter = 0,
			indexes = [],
			max;

		if (_.options.infinite === false) {
			max = _.slideCount;
		} else {
			breakPoint = _.options.slidesToScroll * -1;
			counter = _.options.slidesToScroll * -1;
			max = _.slideCount * 2;
		}

		while (breakPoint < max) {
			indexes.push(breakPoint);
			breakPoint = counter + _.options.slidesToScroll;
			counter += _.options.slidesToScroll <= _.options.slidesToShow ? _.options.slidesToScroll : _.options.slidesToShow;
		}

		return indexes;

	};

	Slick.prototype.getSlick = function () {

		return this;

	};

	Slick.prototype.getSlideCount = function () {

		var _ = this,
			slidesTraversed, swipedSlide, swipeTarget, centerOffset;

		centerOffset = _.options.centerMode === true ? Math.floor(_.$list.width() / 2) : 0;
		swipeTarget = (_.swipeLeft * -1) + centerOffset;

		if (_.options.swipeToSlide === true) {

			_.$slideTrack.find('.slick-slide').each(function (index, slide) {

				var slideOuterWidth, slideOffset, slideRightBoundary;
				slideOuterWidth = $(slide).outerWidth();
				slideOffset = slide.offsetLeft;
				if (_.options.centerMode !== true) {
					slideOffset += (slideOuterWidth / 2);
				}

				slideRightBoundary = slideOffset + (slideOuterWidth);

				if (swipeTarget < slideRightBoundary) {
					swipedSlide = slide;
					return false;
				}
			});

			slidesTraversed = Math.abs($(swipedSlide).attr('data-slick-index') - _.currentSlide) || 1;

			return slidesTraversed;

		} else {
			return _.options.slidesToScroll;
		}

	};

	Slick.prototype.goTo = Slick.prototype.slickGoTo = function (slide, dontAnimate) {

		var _ = this;

		_.changeSlide({
			data: {
				message: 'index',
				index: parseInt(slide)
			}
		}, dontAnimate);

	};

	Slick.prototype.init = function (creation) {

		var _ = this;

		if (!$(_.$slider).hasClass('slick-initialized')) {

			$(_.$slider).addClass('slick-initialized');

			_.buildRows();
			_.buildOut();
			_.setProps();
			_.startLoad();
			_.loadSlider();
			_.initializeEvents();
			_.updateArrows();
			_.updateDots();
			_.checkResponsive(true);
			_.focusHandler();

		}

		if (creation) {
			_.$slider.trigger('init', [_]);
		}

		if (_.options.accessibility === true) {
			_.initADA();
		}

		if (_.options.autoplay) {

			_.paused = false;
			_.autoPlay();

		}

	};

	Slick.prototype.initADA = function () {
		var _ = this,
			numDotGroups = Math.ceil(_.slideCount / _.options.slidesToScroll),
			tabControlIndexes = _.getNavigableIndexes().filter(function (val) {
				return (val >= 0) && (val < _.slideCount);
			});

		_.$slides.add(_.$slideTrack.find('.slick-cloned')).attr({
			'aria-hidden': 'true',
			'tabindex': '-1'
		}).find('a, input, button, select').attr({
			'tabindex': '-1'
		});

		if (_.$dots !== null) {
			_.$slides.not(_.$slideTrack.find('.slick-cloned')).each(function (i) {
				var slideControlIndex = tabControlIndexes.indexOf(i);

				$(this).attr({
					'role': 'tabpanel',
					'id': 'slick-slide' + _.instanceUid + i,
					'tabindex': -1
				});

				if (slideControlIndex !== -1) {
					var ariaButtonControl = 'slick-slide-control' + _.instanceUid + slideControlIndex;
					if ($('#' + ariaButtonControl).length) {
						$(this).attr({
							'aria-describedby': ariaButtonControl
						});
					}
				}
			});

			_.$dots.attr('role', 'tablist').find('li').each(function (i) {
				var mappedSlideIndex = tabControlIndexes[i];

				$(this).attr({
					'role': 'presentation'
				});

				$(this).find('button').first().attr({
					'role': 'tab',
					'id': 'slick-slide-control' + _.instanceUid + i,
					'aria-controls': 'slick-slide' + _.instanceUid + mappedSlideIndex,
					'aria-label': (i + 1) + ' of ' + numDotGroups,
					'aria-selected': null,
					'tabindex': '-1'
				});

			}).eq(_.currentSlide).find('button').attr({
				'aria-selected': 'true',
				'tabindex': '0'
			}).end();
		}

		for (var i = _.currentSlide, max = i + _.options.slidesToShow; i < max; i++) {
			if (_.options.focusOnChange) {
				_.$slides.eq(i).attr({ 'tabindex': '0' });
			} else {
				_.$slides.eq(i).removeAttr('tabindex');
			}
		}

		_.activateADA();

	};

	Slick.prototype.initArrowEvents = function () {

		var _ = this;

		if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {
			_.$prevArrow
				.off('click.slick')
				.on('click.slick', {
					message: 'previous'
				}, _.changeSlide);
			_.$nextArrow
				.off('click.slick')
				.on('click.slick', {
					message: 'next'
				}, _.changeSlide);

			if (_.options.accessibility === true) {
				_.$prevArrow.on('keydown.slick', _.keyHandler);
				_.$nextArrow.on('keydown.slick', _.keyHandler);
			}
		}

	};

	Slick.prototype.initDotEvents = function () {

		var _ = this;

		if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {
			$('li', _.$dots).on('click.slick', {
				message: 'index'
			}, _.changeSlide);

			if (_.options.accessibility === true) {
				_.$dots.on('keydown.slick', _.keyHandler);
			}
		}

		if (_.options.dots === true && _.options.pauseOnDotsHover === true && _.slideCount > _.options.slidesToShow) {

			$('li', _.$dots)
				.on('mouseenter.slick', $.proxy(_.interrupt, _, true))
				.on('mouseleave.slick', $.proxy(_.interrupt, _, false));

		}

	};

	Slick.prototype.initSlideEvents = function () {

		var _ = this;

		if (_.options.pauseOnHover) {

			_.$list.on('mouseenter.slick', $.proxy(_.interrupt, _, true));
			_.$list.on('mouseleave.slick', $.proxy(_.interrupt, _, false));

		}

	};

	Slick.prototype.initializeEvents = function () {

		var _ = this;

		_.initArrowEvents();

		_.initDotEvents();
		_.initSlideEvents();

		_.$list.on('touchstart.slick mousedown.slick', {
			action: 'start'
		}, _.swipeHandler);
		_.$list.on('touchmove.slick mousemove.slick', {
			action: 'move'
		}, _.swipeHandler);
		_.$list.on('touchend.slick mouseup.slick', {
			action: 'end'
		}, _.swipeHandler);
		_.$list.on('touchcancel.slick mouseleave.slick', {
			action: 'end'
		}, _.swipeHandler);

		_.$list.on('click.slick', _.clickHandler);

		$(document).on(_.visibilityChange, $.proxy(_.visibility, _));

		if (_.options.accessibility === true) {
			_.$list.on('keydown.slick', _.keyHandler);
		}

		if (_.options.focusOnSelect === true) {
			$(_.$slideTrack).children().on('click.slick', _.selectHandler);
		}

		$(window).on('orientationchange.slick.slick-' + _.instanceUid, $.proxy(_.orientationChange, _));

		$(window).on('resize.slick.slick-' + _.instanceUid, $.proxy(_.resize, _));

		$('[draggable!=true]', _.$slideTrack).on('dragstart', _.preventDefault);

		$(window).on('load.slick.slick-' + _.instanceUid, _.setPosition);
		$(_.setPosition);

	};

	Slick.prototype.initUI = function () {

		var _ = this;

		if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {

			_.$prevArrow.show();
			_.$nextArrow.show();

		}

		if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

			_.$dots.show();

		}

	};

	Slick.prototype.keyHandler = function (event) {

		var _ = this;
		//Dont slide if the cursor is inside the form fields and arrow keys are pressed
		if (!event.target.tagName.match('TEXTAREA|INPUT|SELECT')) {
			if (event.keyCode === 37 && _.options.accessibility === true) {
				_.changeSlide({
					data: {
						message: _.options.rtl === true ? 'next' : 'previous'
					}
				});
			} else if (event.keyCode === 39 && _.options.accessibility === true) {
				_.changeSlide({
					data: {
						message: _.options.rtl === true ? 'previous' : 'next'
					}
				});
			}
		}

	};

	Slick.prototype.lazyLoad = function () {

		var _ = this,
			loadRange, cloneRange, rangeStart, rangeEnd;

		function loadImages(imagesScope) {

			$('img[data-lazy]', imagesScope).each(function () {

				var image = $(this),
					imageSource = $(this).attr('data-lazy'),
					imageSrcSet = $(this).attr('data-srcset'),
					imageSizes = $(this).attr('data-sizes') || _.$slider.attr('data-sizes'),
					imageToLoad = document.createElement('img');

				imageToLoad.onload = function () {

					image
						.animate({ opacity: 0 }, 100, function () {

							if (imageSrcSet) {
								image
									.attr('srcset', imageSrcSet);

								if (imageSizes) {
									image
										.attr('sizes', imageSizes);
								}
							}

							image
								.attr('src', imageSource)
								.animate({ opacity: 1 }, 200, function () {
									image
										.removeAttr('data-lazy data-srcset data-sizes')
										.removeClass('slick-loading');
								});
							_.$slider.trigger('lazyLoaded', [_, image, imageSource]);
						});

				};

				imageToLoad.onerror = function () {

					image
						.removeAttr('data-lazy')
						.removeClass('slick-loading')
						.addClass('slick-lazyload-error');

					_.$slider.trigger('lazyLoadError', [_, image, imageSource]);

				};

				imageToLoad.src = imageSource;

			});

		}

		if (_.options.centerMode === true) {
			if (_.options.infinite === true) {
				rangeStart = _.currentSlide + (_.options.slidesToShow / 2 + 1);
				rangeEnd = rangeStart + _.options.slidesToShow + 2;
			} else {
				rangeStart = Math.max(0, _.currentSlide - (_.options.slidesToShow / 2 + 1));
				rangeEnd = 2 + (_.options.slidesToShow / 2 + 1) + _.currentSlide;
			}
		} else {
			rangeStart = _.options.infinite ? _.options.slidesToShow + _.currentSlide : _.currentSlide;
			rangeEnd = Math.ceil(rangeStart + _.options.slidesToShow);
			if (_.options.fade === true) {
				if (rangeStart > 0) rangeStart--;
				if (rangeEnd <= _.slideCount) rangeEnd++;
			}
		}

		loadRange = _.$slider.find('.slick-slide').slice(rangeStart, rangeEnd);

		if (_.options.lazyLoad === 'anticipated') {
			var prevSlide = rangeStart - 1,
				nextSlide = rangeEnd,
				$slides = _.$slider.find('.slick-slide');

			for (var i = 0; i < _.options.slidesToScroll; i++) {
				if (prevSlide < 0) prevSlide = _.slideCount - 1;
				loadRange = loadRange.add($slides.eq(prevSlide));
				loadRange = loadRange.add($slides.eq(nextSlide));
				prevSlide--;
				nextSlide++;
			}
		}

		loadImages(loadRange);

		if (_.slideCount <= _.options.slidesToShow) {
			cloneRange = _.$slider.find('.slick-slide');
			loadImages(cloneRange);
		} else
			if (_.currentSlide >= _.slideCount - _.options.slidesToShow) {
				cloneRange = _.$slider.find('.slick-cloned').slice(0, _.options.slidesToShow);
				loadImages(cloneRange);
			} else if (_.currentSlide === 0) {
				cloneRange = _.$slider.find('.slick-cloned').slice(_.options.slidesToShow * -1);
				loadImages(cloneRange);
			}

	};

	Slick.prototype.loadSlider = function () {

		var _ = this;

		_.setPosition();

		_.$slideTrack.css({
			opacity: '1'
		});

		_.$slider.removeClass('slick-loading');

		_.initUI();

		if (_.options.lazyLoad === 'progressive') {
			_.progressiveLazyLoad();
		}

	};

	Slick.prototype.next = Slick.prototype.slickNext = function () {

		var _ = this;

		_.changeSlide({
			data: {
				message: 'next'
			}
		});

	};

	Slick.prototype.orientationChange = function () {

		var _ = this;

		_.checkResponsive();
		_.setPosition();

	};

	Slick.prototype.pause = Slick.prototype.slickPause = function () {

		var _ = this;

		_.autoPlayClear();
		_.paused = true;

	};

	Slick.prototype.play = Slick.prototype.slickPlay = function () {

		var _ = this;

		_.autoPlay();
		_.options.autoplay = true;
		_.paused = false;
		_.focussed = false;
		_.interrupted = false;

	};

	Slick.prototype.postSlide = function (index) {

		var _ = this;

		if (!_.unslicked) {

			_.$slider.trigger('afterChange', [_, index]);

			_.animating = false;

			if (_.slideCount > _.options.slidesToShow) {
				_.setPosition();
			}

			_.swipeLeft = null;

			if (_.options.autoplay) {
				_.autoPlay();
			}

			if (_.options.accessibility === true) {
				_.initADA();

				if (_.options.focusOnChange) {
					var $currentSlide = $(_.$slides.get(_.currentSlide));
					$currentSlide.attr('tabindex', 0).trigger('focus');
				}
			}

		}

	};

	Slick.prototype.prev = Slick.prototype.slickPrev = function () {

		var _ = this;

		_.changeSlide({
			data: {
				message: 'previous'
			}
		});

	};

	Slick.prototype.preventDefault = function (event) {

		event.preventDefault();

	};

	Slick.prototype.progressiveLazyLoad = function (tryCount) {

		tryCount = tryCount || 1;

		var _ = this,
			$imgsToLoad = $('img[data-lazy]', _.$slider),
			image,
			imageSource,
			imageSrcSet,
			imageSizes,
			imageToLoad;

		if ($imgsToLoad.length) {

			image = $imgsToLoad.first();
			imageSource = image.attr('data-lazy');
			imageSrcSet = image.attr('data-srcset');
			imageSizes = image.attr('data-sizes') || _.$slider.attr('data-sizes');
			imageToLoad = document.createElement('img');

			imageToLoad.onload = function () {

				if (imageSrcSet) {
					image
						.attr('srcset', imageSrcSet);

					if (imageSizes) {
						image
							.attr('sizes', imageSizes);
					}
				}

				image
					.attr('src', imageSource)
					.removeAttr('data-lazy data-srcset data-sizes')
					.removeClass('slick-loading');

				if (_.options.adaptiveHeight === true) {
					_.setPosition();
				}

				_.$slider.trigger('lazyLoaded', [_, image, imageSource]);
				_.progressiveLazyLoad();

			};

			imageToLoad.onerror = function () {

				if (tryCount < 3) {

					/**
					 * try to load the image 3 times,
					 * leave a slight delay so we don't get
					 * servers blocking the request.
					 */
					setTimeout(function () {
						_.progressiveLazyLoad(tryCount + 1);
					}, 500);

				} else {

					image
						.removeAttr('data-lazy')
						.removeClass('slick-loading')
						.addClass('slick-lazyload-error');

					_.$slider.trigger('lazyLoadError', [_, image, imageSource]);

					_.progressiveLazyLoad();

				}

			};

			imageToLoad.src = imageSource;

		} else {

			_.$slider.trigger('allImagesLoaded', [_]);

		}

	};

	Slick.prototype.refresh = function (initializing) {

		var _ = this, currentSlide, lastVisibleIndex;

		lastVisibleIndex = _.slideCount - _.options.slidesToShow;

		// in non-infinite sliders, we don't want to go past the
		// last visible index.
		if (!_.options.infinite && (_.currentSlide > lastVisibleIndex)) {
			_.currentSlide = lastVisibleIndex;
		}

		// if less slides than to show, go to start.
		if (_.slideCount <= _.options.slidesToShow) {
			_.currentSlide = 0;

		}

		currentSlide = _.currentSlide;

		_.destroy(true);

		$.extend(_, _.initials, { currentSlide: currentSlide });

		_.init();

		if (!initializing) {

			_.changeSlide({
				data: {
					message: 'index',
					index: currentSlide
				}
			}, false);

		}

	};

	Slick.prototype.registerBreakpoints = function () {

		var _ = this, breakpoint, currentBreakpoint, l,
			responsiveSettings = _.options.responsive || null;

		if (Array.isArray(responsiveSettings) && responsiveSettings.length) {

			_.respondTo = _.options.respondTo || 'window';

			for (breakpoint in responsiveSettings) {

				l = _.breakpoints.length - 1;

				if (responsiveSettings.hasOwnProperty(breakpoint)) {
					currentBreakpoint = responsiveSettings[breakpoint].breakpoint;

					// loop through the breakpoints and cut out any existing
					// ones with the same breakpoint number, we don't want dupes.
					while (l >= 0) {
						if (_.breakpoints[l] && _.breakpoints[l] === currentBreakpoint) {
							_.breakpoints.splice(l, 1);
						}
						l--;
					}

					_.breakpoints.push(currentBreakpoint);
					_.breakpointSettings[currentBreakpoint] = responsiveSettings[breakpoint].settings;

				}

			}

			_.breakpoints.sort(function (a, b) {
				return (_.options.mobileFirst) ? a - b : b - a;
			});

		}

	};

	Slick.prototype.reinit = function () {

		var _ = this;

		_.$slides =
			_.$slideTrack
				.children(_.options.slide)
				.addClass('slick-slide');

		_.slideCount = _.$slides.length;

		if (_.currentSlide >= _.slideCount && _.currentSlide !== 0) {
			_.currentSlide = _.currentSlide - _.options.slidesToScroll;
		}

		if (_.slideCount <= _.options.slidesToShow) {
			_.currentSlide = 0;
		}

		_.registerBreakpoints();

		_.setProps();
		_.setupInfinite();
		_.buildArrows();
		_.updateArrows();
		_.initArrowEvents();
		_.buildDots();
		_.updateDots();
		_.initDotEvents();
		_.cleanUpSlideEvents();
		_.initSlideEvents();

		_.checkResponsive(false, true);

		if (_.options.focusOnSelect === true) {
			$(_.$slideTrack).children().on('click.slick', _.selectHandler);
		}

		_.setSlideClasses(typeof _.currentSlide === 'number' ? _.currentSlide : 0);

		_.setPosition();
		_.focusHandler();

		_.paused = !_.options.autoplay;
		_.autoPlay();

		_.$slider.trigger('reInit', [_]);

	};

	Slick.prototype.resize = function () {

		var _ = this;

		if ($(window).width() !== _.windowWidth) {
			clearTimeout(_.windowDelay);
			_.windowDelay = window.setTimeout(function () {
				_.windowWidth = $(window).width();
				_.checkResponsive();
				if (!_.unslicked) { _.setPosition(); }
			}, 50);
		}
	};

	Slick.prototype.removeSlide = Slick.prototype.slickRemove = function (index, removeBefore, removeAll) {

		var _ = this;

		if (typeof (index) === 'boolean') {
			removeBefore = index;
			index = removeBefore === true ? 0 : _.slideCount - 1;
		} else {
			index = removeBefore === true ? --index : index;
		}

		if (_.slideCount < 1 || index < 0 || index > _.slideCount - 1) {
			return false;
		}

		_.unload();

		if (removeAll === true) {
			_.$slideTrack.children().remove();
		} else {
			_.$slideTrack.children(this.options.slide).eq(index).remove();
		}

		_.$slides = _.$slideTrack.children(this.options.slide);

		_.$slideTrack.children(this.options.slide).detach();

		_.$slideTrack.append(_.$slides);

		_.$slidesCache = _.$slides;

		_.reinit();

	};

	Slick.prototype.setCSS = function (position) {

		var _ = this,
			positionProps = {},
			x, y;

		if (_.options.rtl === true) {
			position = -position;
		}
		x = _.positionProp == 'left' ? Math.ceil(position) + 'px' : '0px';
		y = _.positionProp == 'top' ? Math.ceil(position) + 'px' : '0px';

		positionProps[_.positionProp] = position;

		if (_.transformsEnabled === false) {
			_.$slideTrack.css(positionProps);
		} else {
			positionProps = {};
			if (_.cssTransitions === false) {
				positionProps[_.animType] = 'translate(' + x + ', ' + y + ')';
				_.$slideTrack.css(positionProps);
			} else {
				positionProps[_.animType] = 'translate3d(' + x + ', ' + y + ', 0px)';
				_.$slideTrack.css(positionProps);
			}
		}

	};

	Slick.prototype.setDimensions = function () {

		var _ = this;

		if (_.options.vertical === false) {
			if (_.options.centerMode === true) {
				_.$list.css({
					padding: ('0px ' + _.options.centerPadding)
				});
			}
		} else {
			_.$list.height(_.$slides.first().outerHeight(true) * _.options.slidesToShow);
			if (_.options.centerMode === true) {
				_.$list.css({
					padding: (_.options.centerPadding + ' 0px')
				});
			}
		}

		_.listWidth = _.$list.width();
		_.listHeight = _.$list.height();


		if (_.options.vertical === false && _.options.variableWidth === false) {
			_.slideWidth = Math.ceil(_.listWidth / _.options.slidesToShow);
			_.$slideTrack.width(Math.ceil((_.slideWidth * _.$slideTrack.children('.slick-slide').length)));

		} else if (_.options.variableWidth === true) {
			_.$slideTrack.width(5000 * _.slideCount);
		} else {
			_.slideWidth = Math.ceil(_.listWidth);
			_.$slideTrack.height(Math.ceil((_.$slides.first().outerHeight(true) * _.$slideTrack.children('.slick-slide').length)));
		}

		var offset = _.$slides.first().outerWidth(true) - _.$slides.first().width();
		if (_.options.variableWidth === false) _.$slideTrack.children('.slick-slide').width(_.slideWidth - offset);

	};

	Slick.prototype.setFade = function () {

		var _ = this,
			targetLeft;

		_.$slides.each(function (index, element) {
			targetLeft = (_.slideWidth * index) * -1;
			if (_.options.rtl === true) {
				$(element).css({
					position: 'relative',
					right: targetLeft,
					top: '0',
					zIndex: _.options.zIndex - 2,
					opacity: '0'
				});
			} else {
				$(element).css({
					position: 'relative',
					left: targetLeft,
					top: '0',
					zIndex: _.options.zIndex - 2,
					opacity: '0'
				});
			}
		});

		_.$slides.eq(_.currentSlide).css({
			zIndex: _.options.zIndex - 1,
			opacity: '1'
		});

	};

	Slick.prototype.setHeight = function () {

		var _ = this;

		if (_.options.slidesToShow === 1 && _.options.adaptiveHeight === true && _.options.vertical === false) {
			var targetHeight = _.$slides.eq(_.currentSlide).outerHeight(true);
			_.$list.css('height', targetHeight + 'px');
		}

	};

	Slick.prototype.setOption =
		Slick.prototype.slickSetOption = function () {

			/**
			 * accepts arguments in format of:
			 *
			 *  - for changing a single option's value:
			 *     .slick("setOption", option, value, refresh )
			 *
			 *  - for changing a set of responsive options:
			 *     .slick("setOption", 'responsive', [{}, ...], refresh )
			 *
			 *  - for updating multiple values at once (not responsive)
			 *     .slick("setOption", { 'option': value, ... }, refresh )
			 */

			var _ = this, l, item, option, value, refresh = false, type;

			if ($.isPlainObject(arguments[0])) {

				option = arguments[0];
				refresh = arguments[1];
				type = 'multiple';

			} else if (typeof arguments[0] === 'string') {

				option = arguments[0];
				value = arguments[1];
				refresh = arguments[2];

				if (arguments[0] === 'responsive' && Array.isArray(arguments[1])) {

					type = 'responsive';

				} else if (typeof arguments[1] !== 'undefined') {

					type = 'single';

				}

			}

			if (type === 'single') {

				_.options[option] = value;


			} else if (type === 'multiple') {

				$.each(option, function (opt, val) {

					_.options[opt] = val;

				});


			} else if (type === 'responsive') {

				for (item in value) {

					if (!Array.isArray(_.options.responsive)) {

						_.options.responsive = [value[item]];

					} else {

						l = _.options.responsive.length - 1;

						// loop through the responsive object and splice out duplicates.
						while (l >= 0) {

							if (_.options.responsive[l].breakpoint === value[item].breakpoint) {

								_.options.responsive.splice(l, 1);

							}

							l--;

						}

						_.options.responsive.push(value[item]);

					}

				}

			}

			if (refresh) {

				_.unload();
				_.reinit();

			}

		};

	Slick.prototype.setPosition = function () {

		var _ = this;

		_.setDimensions();

		_.setHeight();

		if (_.options.fade === false) {
			_.setCSS(_.getLeft(_.currentSlide));
		} else {
			_.setFade();
		}

		_.$slider.trigger('setPosition', [_]);

	};

	Slick.prototype.setProps = function () {

		var _ = this,
			bodyStyle = document.body.style;

		_.positionProp = _.options.vertical === true ? 'top' : 'left';

		if (_.positionProp === 'top') {
			_.$slider.addClass('slick-vertical');
		} else {
			_.$slider.removeClass('slick-vertical');
		}

		if (bodyStyle.WebkitTransition !== undefined ||
			bodyStyle.MozTransition !== undefined ||
			bodyStyle.msTransition !== undefined) {
			if (_.options.useCSS === true) {
				_.cssTransitions = true;
			}
		}

		if (_.options.fade) {
			if (typeof _.options.zIndex === 'number') {
				if (_.options.zIndex < 3) {
					_.options.zIndex = 3;
				}
			} else {
				_.options.zIndex = _.defaults.zIndex;
			}
		}

		if (bodyStyle.OTransform !== undefined) {
			_.animType = 'OTransform';
			_.transformType = '-o-transform';
			_.transitionType = 'OTransition';
			if (bodyStyle.perspectiveProperty === undefined && bodyStyle.webkitPerspective === undefined) _.animType = false;
		}
		if (bodyStyle.MozTransform !== undefined) {
			_.animType = 'MozTransform';
			_.transformType = '-moz-transform';
			_.transitionType = 'MozTransition';
			if (bodyStyle.perspectiveProperty === undefined && bodyStyle.MozPerspective === undefined) _.animType = false;
		}
		if (bodyStyle.webkitTransform !== undefined) {
			_.animType = 'webkitTransform';
			_.transformType = '-webkit-transform';
			_.transitionType = 'webkitTransition';
			if (bodyStyle.perspectiveProperty === undefined && bodyStyle.webkitPerspective === undefined) _.animType = false;
		}
		if (bodyStyle.msTransform !== undefined) {
			_.animType = 'msTransform';
			_.transformType = '-ms-transform';
			_.transitionType = 'msTransition';
			if (bodyStyle.msTransform === undefined) _.animType = false;
		}
		if (bodyStyle.transform !== undefined && _.animType !== false) {
			_.animType = 'transform';
			_.transformType = 'transform';
			_.transitionType = 'transition';
		}
		_.transformsEnabled = _.options.useTransform && (_.animType !== null && _.animType !== false);
	};


	Slick.prototype.setSlideClasses = function (index) {

		var _ = this,
			centerOffset, allSlides, indexOffset, remainder;

		allSlides = _.$slider
			.find('.slick-slide')
			.removeClass('slick-active slick-center slick-current')
			.attr('aria-hidden', 'true');

		_.$slides
			.eq(index)
			.addClass('slick-current');

		if (_.options.centerMode === true) {

			var evenCoef = _.options.slidesToShow % 2 === 0 ? 1 : 0;

			centerOffset = Math.floor(_.options.slidesToShow / 2);

			if (_.options.infinite === true) {

				if (index >= centerOffset && index <= (_.slideCount - 1) - centerOffset) {
					_.$slides
						.slice(index - centerOffset + evenCoef, index + centerOffset + 1)
						.addClass('slick-active')
						.attr('aria-hidden', 'false');

				} else {

					indexOffset = _.options.slidesToShow + index;
					allSlides
						.slice(indexOffset - centerOffset + 1 + evenCoef, indexOffset + centerOffset + 2)
						.addClass('slick-active')
						.attr('aria-hidden', 'false');

				}

				if (index === 0) {

					allSlides
						.eq(_.options.slidesToShow + _.slideCount + 1)
						.addClass('slick-center');

				} else if (index === _.slideCount - 1) {

					allSlides
						.eq(_.options.slidesToShow)
						.addClass('slick-center');

				}

			}

			_.$slides
				.eq(index)
				.addClass('slick-center');

		} else {

			if (index >= 0 && index <= (_.slideCount - _.options.slidesToShow)) {

				_.$slides
					.slice(index, index + _.options.slidesToShow)
					.addClass('slick-active')
					.attr('aria-hidden', 'false');

			} else if (allSlides.length <= _.options.slidesToShow) {

				allSlides
					.addClass('slick-active')
					.attr('aria-hidden', 'false');

			} else {

				remainder = _.slideCount % _.options.slidesToShow;
				indexOffset = _.options.infinite === true ? _.options.slidesToShow + index : index;

				if (_.options.slidesToShow == _.options.slidesToScroll && (_.slideCount - index) < _.options.slidesToShow) {

					allSlides
						.slice(indexOffset - (_.options.slidesToShow - remainder), indexOffset + remainder)
						.addClass('slick-active')
						.attr('aria-hidden', 'false');

				} else {

					allSlides
						.slice(indexOffset, indexOffset + _.options.slidesToShow)
						.addClass('slick-active')
						.attr('aria-hidden', 'false');

				}

			}

		}

		if (_.options.lazyLoad === 'ondemand' || _.options.lazyLoad === 'anticipated') {
			_.lazyLoad();
		}
	};

	Slick.prototype.setupInfinite = function () {

		var _ = this,
			i, slideIndex, infiniteCount;

		if (_.options.fade === true) {
			_.options.centerMode = false;
		}

		if (_.options.infinite === true && _.options.fade === false) {

			slideIndex = null;

			if (_.slideCount > _.options.slidesToShow) {

				if (_.options.centerMode === true) {
					infiniteCount = _.options.slidesToShow + 1;
				} else {
					infiniteCount = _.options.slidesToShow;
				}

				for (i = _.slideCount; i > (_.slideCount -
					infiniteCount); i -= 1) {
					slideIndex = i - 1;
					$(_.$slides[slideIndex]).clone(true).removeAttr('id')
						.attr('data-slick-index', slideIndex - _.slideCount)
						.prependTo(_.$slideTrack).addClass('slick-cloned');
				}
				for (i = 0; i < infiniteCount + _.slideCount; i += 1) {
					slideIndex = i;
					$(_.$slides[slideIndex]).clone(true).removeAttr('id')
						.attr('data-slick-index', slideIndex + _.slideCount)
						.appendTo(_.$slideTrack).addClass('slick-cloned');
				}
				_.$slideTrack.find('.slick-cloned').find('[id]').each(function () {
					$(this).removeAttr('id');
				});

			}

		}

	};

	Slick.prototype.interrupt = function (toggle) {

		var _ = this;

		if (!toggle) {
			_.autoPlay();
		}
		_.interrupted = toggle;

	};

	Slick.prototype.selectHandler = function (event) {

		var _ = this;

		var targetElement =
			$(event.target).is('.slick-slide') ?
				$(event.target) :
				$(event.target).parents('.slick-slide');

		var index = parseInt(targetElement.attr('data-slick-index'));

		if (!index) index = 0;

		if (_.slideCount <= _.options.slidesToShow) {

			_.slideHandler(index, false, true);
			return;

		}

		_.slideHandler(index);

	};

	Slick.prototype.slideHandler = function (index, sync, dontAnimate) {

		var targetSlide, animSlide, oldSlide, slideLeft, targetLeft = null,
			_ = this, navTarget;

		sync = sync || false;

		if (_.animating === true && _.options.waitForAnimate === true) {
			return;
		}

		if (_.options.fade === true && _.currentSlide === index) {
			return;
		}

		if (sync === false) {
			_.asNavFor(index);
		}

		targetSlide = index;
		targetLeft = _.getLeft(targetSlide);
		slideLeft = _.getLeft(_.currentSlide);

		_.currentLeft = _.swipeLeft === null ? slideLeft : _.swipeLeft;

		if (_.options.infinite === false && _.options.centerMode === false && (index < 0 || index > _.getDotCount() * _.options.slidesToScroll)) {
			if (_.options.fade === false) {
				targetSlide = _.currentSlide;
				if (dontAnimate !== true && _.slideCount > _.options.slidesToShow) {
					_.animateSlide(slideLeft, function () {
						_.postSlide(targetSlide);
					});
				} else {
					_.postSlide(targetSlide);
				}
			}
			return;
		} else if (_.options.infinite === false && _.options.centerMode === true && (index < 0 || index > (_.slideCount - _.options.slidesToScroll))) {
			if (_.options.fade === false) {
				targetSlide = _.currentSlide;
				if (dontAnimate !== true && _.slideCount > _.options.slidesToShow) {
					_.animateSlide(slideLeft, function () {
						_.postSlide(targetSlide);
					});
				} else {
					_.postSlide(targetSlide);
				}
			}
			return;
		}

		if (_.options.autoplay) {
			clearInterval(_.autoPlayTimer);
		}

		if (targetSlide < 0) {
			if (_.slideCount % _.options.slidesToScroll !== 0) {
				animSlide = _.slideCount - (_.slideCount % _.options.slidesToScroll);
			} else {
				animSlide = _.slideCount + targetSlide;
			}
		} else if (targetSlide >= _.slideCount) {
			if (_.slideCount % _.options.slidesToScroll !== 0) {
				animSlide = 0;
			} else {
				animSlide = targetSlide - _.slideCount;
			}
		} else {
			animSlide = targetSlide;
		}

		_.animating = true;

		_.$slider.trigger('beforeChange', [_, _.currentSlide, animSlide]);

		oldSlide = _.currentSlide;
		_.currentSlide = animSlide;

		_.setSlideClasses(_.currentSlide);

		if (_.options.asNavFor) {

			navTarget = _.getNavTarget();
			navTarget = navTarget.slick('getSlick');

			if (navTarget.slideCount <= navTarget.options.slidesToShow) {
				navTarget.setSlideClasses(_.currentSlide);
			}

		}

		_.updateDots();
		_.updateArrows();

		if (_.options.fade === true) {
			if (dontAnimate !== true) {

				_.fadeSlideOut(oldSlide);

				_.fadeSlide(animSlide, function () {
					_.postSlide(animSlide);
				});

			} else {
				_.postSlide(animSlide);
			}
			_.animateHeight();
			return;
		}

		if (dontAnimate !== true && _.slideCount > _.options.slidesToShow) {
			_.animateSlide(targetLeft, function () {
				_.postSlide(animSlide);
			});
		} else {
			_.postSlide(animSlide);
		}

	};

	Slick.prototype.startLoad = function () {

		var _ = this;

		if (_.options.arrows === true && _.slideCount > _.options.slidesToShow) {

			_.$prevArrow.hide();
			_.$nextArrow.hide();

		}

		if (_.options.dots === true && _.slideCount > _.options.slidesToShow) {

			_.$dots.hide();

		}

		_.$slider.addClass('slick-loading');

	};

	Slick.prototype.swipeDirection = function () {

		var xDist, yDist, r, swipeAngle, _ = this;

		xDist = _.touchObject.startX - _.touchObject.curX;
		yDist = _.touchObject.startY - _.touchObject.curY;
		r = Math.atan2(yDist, xDist);

		swipeAngle = Math.round(r * 180 / Math.PI);
		if (swipeAngle < 0) {
			swipeAngle = 360 - Math.abs(swipeAngle);
		}

		if ((swipeAngle <= 45) && (swipeAngle >= 0)) {
			return (_.options.rtl === false ? 'left' : 'right');
		}
		if ((swipeAngle <= 360) && (swipeAngle >= 315)) {
			return (_.options.rtl === false ? 'left' : 'right');
		}
		if ((swipeAngle >= 135) && (swipeAngle <= 225)) {
			return (_.options.rtl === false ? 'right' : 'left');
		}
		if (_.options.verticalSwiping === true) {
			if ((swipeAngle >= 35) && (swipeAngle <= 135)) {
				return 'down';
			} else {
				return 'up';
			}
		}

		return 'vertical';

	};

	Slick.prototype.swipeEnd = function (event) {

		var _ = this,
			slideCount,
			direction;

		_.dragging = false;
		_.swiping = false;

		if (_.scrolling) {
			_.scrolling = false;
			return false;
		}

		_.interrupted = false;
		_.shouldClick = (_.touchObject.swipeLength > 10) ? false : true;

		if (_.touchObject.curX === undefined) {
			return false;
		}

		if (_.touchObject.edgeHit === true) {
			_.$slider.trigger('edge', [_, _.swipeDirection()]);
		}

		if (_.touchObject.swipeLength >= _.touchObject.minSwipe) {

			direction = _.swipeDirection();

			switch (direction) {

				case 'left':
				case 'down':

					slideCount =
						_.options.swipeToSlide ?
							_.checkNavigable(_.currentSlide + _.getSlideCount()) :
							_.currentSlide + _.getSlideCount();

					_.currentDirection = 0;

					break;

				case 'right':
				case 'up':

					slideCount =
						_.options.swipeToSlide ?
							_.checkNavigable(_.currentSlide - _.getSlideCount()) :
							_.currentSlide - _.getSlideCount();

					_.currentDirection = 1;

					break;

				default:


			}

			if (direction != 'vertical') {

				_.slideHandler(slideCount);
				_.touchObject = {};
				_.$slider.trigger('swipe', [_, direction]);

			}

		} else {

			if (_.touchObject.startX !== _.touchObject.curX) {

				_.slideHandler(_.currentSlide);
				_.touchObject = {};

			}

		}

	};

	Slick.prototype.swipeHandler = function (event) {

		var _ = this;

		if ((_.options.swipe === false) || ('ontouchend' in document && _.options.swipe === false)) {
			return;
		} else if (_.options.draggable === false && event.type.indexOf('mouse') !== -1) {
			return;
		}

		_.touchObject.fingerCount = event.originalEvent && event.originalEvent.touches !== undefined ?
			event.originalEvent.touches.length : 1;

		_.touchObject.minSwipe = _.listWidth / _.options
			.touchThreshold;

		if (_.options.verticalSwiping === true) {
			_.touchObject.minSwipe = _.listHeight / _.options
				.touchThreshold;
		}

		switch (event.data.action) {

			case 'start':
				_.swipeStart(event);
				break;

			case 'move':
				_.swipeMove(event);
				break;

			case 'end':
				_.swipeEnd(event);
				break;

		}

	};

	Slick.prototype.swipeMove = function (event) {

		var _ = this,
			edgeWasHit = false,
			curLeft, swipeDirection, swipeLength, positionOffset, touches, verticalSwipeLength;

		touches = event.originalEvent !== undefined ? event.originalEvent.touches : null;

		if (!_.dragging || _.scrolling || touches && touches.length !== 1) {
			return false;
		}

		curLeft = _.getLeft(_.currentSlide);

		_.touchObject.curX = touches !== undefined ? touches[0].pageX : event.clientX;
		_.touchObject.curY = touches !== undefined ? touches[0].pageY : event.clientY;

		_.touchObject.swipeLength = Math.round(Math.sqrt(
			Math.pow(_.touchObject.curX - _.touchObject.startX, 2)));

		verticalSwipeLength = Math.round(Math.sqrt(
			Math.pow(_.touchObject.curY - _.touchObject.startY, 2)));

		if (!_.options.verticalSwiping && !_.swiping && verticalSwipeLength > 4) {
			_.scrolling = true;
			return false;
		}

		if (_.options.verticalSwiping === true) {
			_.touchObject.swipeLength = verticalSwipeLength;
		}

		swipeDirection = _.swipeDirection();

		if (event.originalEvent !== undefined && _.touchObject.swipeLength > 4) {
			_.swiping = true;
			event.preventDefault();
		}

		positionOffset = (_.options.rtl === false ? 1 : -1) * (_.touchObject.curX > _.touchObject.startX ? 1 : -1);
		if (_.options.verticalSwiping === true) {
			positionOffset = _.touchObject.curY > _.touchObject.startY ? 1 : -1;
		}


		swipeLength = _.touchObject.swipeLength;

		_.touchObject.edgeHit = false;

		if (_.options.infinite === false) {
			if ((_.currentSlide === 0 && swipeDirection === 'right') || (_.currentSlide >= _.getDotCount() && swipeDirection === 'left')) {
				swipeLength = _.touchObject.swipeLength * _.options.edgeFriction;
				_.touchObject.edgeHit = true;
			}
		}

		if (_.options.vertical === false) {
			_.swipeLeft = curLeft + swipeLength * positionOffset;
		} else {
			_.swipeLeft = curLeft + (swipeLength * (_.$list.height() / _.listWidth)) * positionOffset;
		}
		if (_.options.verticalSwiping === true) {
			_.swipeLeft = curLeft + swipeLength * positionOffset;
		}

		if (_.options.fade === true || _.options.touchMove === false) {
			return false;
		}

		if (_.animating === true) {
			_.swipeLeft = null;
			return false;
		}

		_.setCSS(_.swipeLeft);

	};

	Slick.prototype.swipeStart = function (event) {

		var _ = this,
			touches;

		_.interrupted = true;

		if (_.touchObject.fingerCount !== 1 || _.slideCount <= _.options.slidesToShow) {
			_.touchObject = {};
			return false;
		}

		if (event.originalEvent !== undefined && event.originalEvent.touches !== undefined) {
			touches = event.originalEvent.touches[0];
		}

		_.touchObject.startX = _.touchObject.curX = touches !== undefined ? touches.pageX : event.clientX;
		_.touchObject.startY = _.touchObject.curY = touches !== undefined ? touches.pageY : event.clientY;

		_.dragging = true;

	};

	Slick.prototype.unfilterSlides = Slick.prototype.slickUnfilter = function () {

		var _ = this;

		if (_.$slidesCache !== null) {

			_.unload();

			_.$slideTrack.children(this.options.slide).detach();

			_.$slidesCache.appendTo(_.$slideTrack);

			_.reinit();

		}

	};

	Slick.prototype.unload = function () {

		var _ = this;

		$('.slick-cloned', _.$slider).remove();

		if (_.$dots) {
			_.$dots.remove();
		}

		if (_.$prevArrow && _.htmlExpr.test(_.options.prevArrow)) {
			_.$prevArrow.remove();
		}

		if (_.$nextArrow && _.htmlExpr.test(_.options.nextArrow)) {
			_.$nextArrow.remove();
		}

		_.$slides
			.removeClass('slick-slide slick-active slick-visible slick-current')
			.attr('aria-hidden', 'true')
			.css('width', '');

	};

	Slick.prototype.unslick = function (fromBreakpoint) {

		var _ = this;
		_.$slider.trigger('unslick', [_, fromBreakpoint]);
		_.destroy();

	};

	Slick.prototype.updateArrows = function () {

		var _ = this,
			centerOffset;

		centerOffset = Math.floor(_.options.slidesToShow / 2);

		if (_.options.arrows === true &&
			_.slideCount > _.options.slidesToShow &&
			!_.options.infinite) {

			_.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');
			_.$nextArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

			if (_.currentSlide === 0) {

				_.$prevArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
				_.$nextArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

			} else if (_.currentSlide >= _.slideCount - _.options.slidesToShow && _.options.centerMode === false) {

				_.$nextArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
				_.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

			} else if (_.currentSlide >= _.slideCount - 1 && _.options.centerMode === true) {

				_.$nextArrow.addClass('slick-disabled').attr('aria-disabled', 'true');
				_.$prevArrow.removeClass('slick-disabled').attr('aria-disabled', 'false');

			}

		}

	};

	Slick.prototype.updateDots = function () {

		var _ = this;

		if (_.$dots !== null) {

			_.$dots
				.find('li')
				.removeClass('slick-active')
				.end();

			_.$dots
				.find('li')
				.eq(Math.floor(_.currentSlide / _.options.slidesToScroll))
				.addClass('slick-active');

		}

	};

	Slick.prototype.visibility = function () {

		var _ = this;

		if (_.options.autoplay) {

			if (document[_.hidden]) {

				_.interrupted = true;

			} else {

				_.interrupted = false;

			}

		}

	};

	$.fn.slick = function () {
		var _ = this,
			opt = arguments[0],
			args = Array.prototype.slice.call(arguments, 1),
			l = _.length,
			i,
			ret;
		for (i = 0; i < l; i++) {
			if (typeof opt == 'object' || typeof opt == 'undefined')
				_[i].slick = new Slick(_[i], opt);
			else
				ret = _[i].slick[opt].apply(_[i].slick, args);
			if (typeof ret != 'undefined') return ret;
		}
		return _;
	};

}));

/* instafeed
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/*!
 * instafeed.js
 * v2.0.0-rc2
 * https://github.com/stevenschobert/instafeed.js
 *
 * Copyright 2020, Steven Schobert
 * Released under the MIT license.
 */
/*!
 * instafeed.js
 * v2.0.0-rc2
 * https://github.com/stevenschobert/instafeed.js
 *
 * Copyright 2020, Steven Schobert
 * Released under the MIT license.
 */

var Instafeed = (function () {
	'use strict';

	function _classCallCheck(instance, Constructor) {
		if (!(instance instanceof Constructor)) {
			throw new TypeError("Cannot call a class as a function");
		}
	}

	function _defineProperties(target, props) {
		for (var i = 0; i < props.length; i++) {
			var descriptor = props[i];
			descriptor.enumerable = descriptor.enumerable || false;
			descriptor.configurable = true;
			if ("value" in descriptor) descriptor.writable = true;
			Object.defineProperty(target, descriptor.key, descriptor);
		}
	}

	function _createClass(Constructor, protoProps, staticProps) {
		if (protoProps) _defineProperties(Constructor.prototype, protoProps);
		if (staticProps) _defineProperties(Constructor, staticProps);
		return Constructor;
	}

	var Instafeed = /*#__PURE__*/function () {
		function Instafeed(options) {
			_classCallCheck(this, Instafeed);

			this.options = {
				accessToken: null,
				accessTokenTimeout: 10000,
				after: null,
				apiEndpoint: 'https://graph.instagram.com',
				apiTimeout: 10000,
				before: null,
				debug: false,
				error: null,
				filter: null,
				limit: 15,
				mock: false,
				render: null,
				sort: null,
				success: null,
				target: 'instafeed',
				transform: null
			};

			if (typeof options === 'object' && options !== null) {
				for (var key in options) {
					this.options[key] = options[key];
				}
			}

			this.isInstafeed = true;
			this._state = {
				error: null,
				hasMore: false,
				isDeprecated: false,
				isFetching: false,
				isFetchingMore: false,
				isRefreshing: false,
				nextUrl: null,
				session: null
			};
		}

		_createClass(Instafeed, [{
			key: "run",
			value: function run() {
				var _this = this;

				this._debug('run', 'starting');

				this._runHook('before');

				if (typeof this.options.accessToken !== 'string') {
					var err = new Error('Missing access token');

					this._debug('run', 'missing access token');

					this._fail(err);

					return false;
				}

				if (this._state.isDeprecated) {
					var _err = new Error('The access token for this user is deprecated. Please create a new one.');

					this._debug('run', 'access token is deprecated');

					this._fail(_err);

					return false;
				}

				if (this._state.isFetching) {
					this._debug('run', 'already fetching, skipping');

					return false;
				}

				this._state.isFetching = true;

				this._debug('run', 'getting session');

				this._getSession({
					onSuccess: function onSuccess(session) {
						_this._state.session = session;

						_this._debug('run', 'got session', session);

						_this._debug('run', 'fetching media');

						_this._fetchMedia({
							onSuccess: function onSuccess(data) {
								_this._debug('run', 'fetched media', data);

								_this._success(data);
							},
							onError: function onError(err) {
								_this._debug('run', 'error fetching media', err);

								_this._fail(err);
							}
						});
					},
					onError: function onError(err) {
						_this._debug('run', 'error getting session', err);

						_this._fail(err);
					}
				});

				return true;
			}
		}, {
			key: "next",
			value: function next() {
				var _this2 = this;

				if (!this._state.hasMore) {
					this._debug('next', 'no more media to fetch');

					return false;
				}

				if (this._state.isFetchingMore) {
					this._debug('next', 'already fetching more, skipping');

					return false;
				}

				this._state.isFetchingMore = true;

				this._debug('next', 'fetching more media');

				this._fetchMedia({
					url: this._state.nextUrl,
					onSuccess: function onSuccess(data) {
						_this2._debug('next', 'fetched more media', data);

						_this2._success(data);
					},
					onError: function onError(err) {
						_this2._debug('next', 'error fetching media', err);

						_this2._fail(err);
					}
				});

				return true;
			}
		}, {
			key: "hasNext",
			value: function hasNext() {
				return this._state.hasMore;
			}
		}, {
			key: "refresh",
			value: function refresh(done) {
				var _this3 = this;

				this._debug('refresh', 'starting');

				if (this._state.isRefreshing) {
					this._debug('refresh', 'already refreshing, skipping');

					return false;
				}

				if (this._state.isDeprecated) {
					var err = new Error('The access token for this user is deprecated. Please create a new one.');

					this._debug('refresh', 'access token is deprecated');

					if (typeof done === 'function') {
						done(err, null);
					}

					return false;
				}

				this._state.isRefreshing = true;

				this._debug('refresh', 'getting session');

				this._getSession({
					onSuccess: function onSuccess(session) {
						_this3._state.session = session;

						_this3._debug('refresh', 'got session', session);

						_this3._debug('refresh', 'session is fresh', !session.isDeprecated);

						_this3._state.isDeprecated = session.isDeprecated;

						if (typeof done === 'function') {
							done(null, !session.isDeprecated);
						}
					},
					onError: function onError(err) {
						_this3._debug('refresh', 'error getting session', err);

						if (typeof done === 'function') {
							done(err, null);
						}
					},
					isRefreshing: true
				});

				return true;
			}
		}, {
			key: "_formatData",
			value: function _formatData(data) {
				var _this4 = this;

				var a = [];
				var b = [];
				var c = null;

				this._debug('_formatData', 'starting', data);

				if (typeof data === 'object' && typeof data.data === 'object' && data.data.length > 0) {
					a = data.data;
				}

				if (typeof data === 'object' && typeof data.paging === 'object' && typeof data.paging.next === 'string') {
					this._state.nextUrl = data.paging.next;
					this._state.hasMore = true;
				} else {
					this._state.nextUrl = null;
					this._state.hasMore = false;
				}

				this._debug('_formatData', 'has more', this._state.hasMore);

				this._debug('_formatData', 'next url', this._state.nextUrl);

				if (a.length > 0) {
					if (typeof this.options.transform === 'function') {
						this._debug('_formatData', 'transforming data');

						for (var i = 0; i < a.length; i++) {
							c = this.options.transform(a[i]);

							if (typeof c === 'object' && c !== null) {
								b.push(c);
							}
						}
					} else {
						b = a;
					}

					if (typeof this.options.filter === 'function') {
						this._debug('_formatData', 'filtering data');

						b = b.filter(this.options.filter);
					}

					if (typeof this.options.sort === 'function') {
						this._debug('_formatData', 'sorting data');

						b.sort(this.options.sort);
					}
				}

				this._debug('_formatData', 'finished', b);

				return b;
			}
		}, {
			key: "_renderData",
			value: function _renderData(data) {
				this._debug('_renderData', 'starting', data);

				if (typeof this.options.render === 'function') {
					this._debug('_renderData', 'using custom render function');

					this.options.render(data, this.options);
					return;
				}

				if (typeof this.options.target !== 'string') {
					this._debug('_renderData', 'target is not a string, skipping');

					return;
				}

				var targetEl = document.getElementById(this.options.target);

				if (targetEl === null) {
					this._debug('_renderData', 'no element with id', this.options.target);

					return;
				}

				var fragment = document.createDocumentFragment();

				for (var i = 0; i < data.length; i++) {
					var item = data[i];
					var el = document.createElement('img');
					var url = null;
					var hasError = false;

					if (item.type === 'IMAGE' || item.type === 'CAROUSEL_ALBUM') {
						url = item.media_url;
					}

					if (item.type === 'VIDEO') {
						url = item.thumbnail_url;
					}

					if (url === null) {
						hasError = true;
					}

					if (!hasError) {
						el.src = url;

						if (typeof item.caption === 'string' && item.caption !== null) {
							el.alt = item.caption;
						}

						fragment.appendChild(el);
					}
				}

				targetEl.appendChild(fragment);
			}
		}, {
			key: "_runHook",
			value: function _runHook(name) {
				this._debug('_runHook', 'running hook', name);

				if (typeof this.options[name] === 'function') {
					this.options[name].call(this);
				}
			}
		}, {
			key: "_makeApiRequest",
			value: function _makeApiRequest(options) {
				var _this5 = this;

				this._debug('_makeApiRequest', 'starting', options);

				var xhr = new XMLHttpRequest();
				var requestTimer = null;

				xhr.onreadystatechange = function () {
					if (xhr.readyState !== 4) {
						return;
					}

					clearTimeout(requestTimer);

					_this5._debug('_makeApiRequest', 'request finished');

					if (xhr.status >= 200 && xhr.status < 300) {
						var data = void 0;

						try {
							data = JSON.parse(xhr.responseText);

							_this5._debug('_makeApiRequest', 'request successful', data);
						} catch (err) {
							_this5._debug('_makeApiRequest', 'error parsing response', err);

							options.onError(err);
							return;
						}

						options.onSuccess(data);
					} else {
						var _err2 = new Error('Request failed, got ' + xhr.status + ' (' + xhr.statusText + ')');

						_this5._debug('_makeApiRequest', 'request failed', _err2);

						options.onError(_err2);
					}
				};

				requestTimer = setTimeout(function () {
					xhr.abort();

					var err = new Error('Request timed out');

					_this5._debug('_makeApiRequest', 'request timed out', err);

					options.onError(err);
				}, this.options.apiTimeout);

				this._debug('_makeApiRequest', 'sending request', options.url);

				xhr.open('GET', options.url, true);
				xhr.send();
			}
		}, {
			key: "_fetchMedia",
			value: function _fetchMedia(options) {
				this._debug('_fetchMedia', 'starting', options);

				var url = null;

				if (typeof options.url === 'string') {
					url = options.url;
				} else {
					url = "".concat(this.options.apiEndpoint, "/me/media?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=").concat(this.options.limit, "&access_token=").concat(this.options.accessToken);
				}

				this._makeApiRequest({
					url: url,
					onSuccess: function onSuccess(data) {
						options.onSuccess(data);
					},
					onError: function onError(err) {
						options.onError(err);
					}
				});
			}
		}, {
			key: "_getSession",
			value: function _getSession(options) {
				var _this6 = this;

				this._debug('_getSession', 'starting', options);

				var url = null;
				var session = {
					isDeprecated: false,
					token: this.options.accessToken
				};

				if (this.options.mock) {
					this._debug('_getSession', 'mock enabled, skipping');

					options.onSuccess(session);
					return;
				}

				if (options.isRefreshing) {
					this._debug('_getSession', 'refreshing token');

					url = "".concat(this.options.apiEndpoint, "/refresh_access_token?grant_type=ig_refresh_token&access_token=").concat(this.options.accessToken);
				} else {
					this._debug('_getSession', 'inspecting token');

					url = "".concat(this.options.apiEndpoint, "/access_token?grant_type=ig_exchange_token&access_token=").concat(this.options.accessToken, "&client_secret=__mock_client_secret__");
				}

				var requestTimer = setTimeout(function () {
					var err = new Error('Request timed out');

					_this6._debug('_getSession', 'request timed out', err);

					options.onError(err);
				}, this.options.accessTokenTimeout);

				this._makeApiRequest({
					url: url,
					onSuccess: function onSuccess(data) {
						clearTimeout(requestTimer);

						_this6._debug('_getSession', 'request successful', data);

						if (typeof data.access_token === 'string') {
							session.token = data.access_token;
						}

						options.onSuccess(session);
					},
					onError: function onError(err) {
						clearTimeout(requestTimer);

						_this6._debug('_getSession', 'request failed', err);

						if (err.message.indexOf('is deprecated') >= 0) {
							_this6._debug('_getSession', 'token is deprecated');

							session.isDeprecated = true;
							options.onSuccess(session);
						} else {
							options.onError(err);
						}
					}
				});
			}
		}, {
			key: "_fail",
			value: function _fail(err) {
				this._debug('_fail', 'starting', err);

				this._state.error = err;
				this._state.isFetching = false;
				this._state.isFetchingMore = false;
				this._state.isRefreshing = false;

				this._runHook('error');
			}
		}, {
			key: "_success",
			value: function _success(data) {
				this._debug('_success', 'starting', data);

				var formatted = this._formatData(data);

				this._state.isFetching = false;
				this._state.isFetchingMore = false;
				this._state.isRefreshing = false;

				this._renderData(formatted);

				this._runHook('success');

				this._runHook('after');
			}
		}, {
			key: "_debug",
			value: function _debug(prefix, message, optional) {
				if (this.options.debug) {
					var args = ['Instafeed:', "[".concat(prefix, "]")];

					if (typeof message === 'string') {
						args.push(message);
					}

					if (typeof optional !== 'undefined') {
						args.push(optional);
					}

					console.log.apply(null, args);
				}
			}
		}]);

		return Instafeed;
	}();

	return Instafeed;

}());


/**
 * @fancyapps/ui/Fancybox v5.0.36
 *
 * @copyright 2024 fancyApps
 * @license {@link https://fancyapps.com/licenses/commercial/ | Commercial}
 * @see {@link https://fancyapps.com/fancybox/ | Documentation}
 */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
		typeof define === 'function' && define.amd ? define(factory) :
			(global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.Fancybox = factory());
})(this, (function () {
	'use strict';

	const t = "5.0.36";
	const e = t => "object" == typeof t && null !== t && t.constructor === Object && "[object Object]" === Object.prototype.toString.call(t),
		i = (t, ...i) => {
			const s = i.length;
			for (let n = 0; n < s; n++) {
				const s = i[n] || {};
				Object.entries(s).forEach((([i, s]) => {
					const n = Array.isArray(s) ? s.slice() : s;
					e(n) ? t[i] = t[i] && e(t[i]) ? i(t[i], n) : n : t[i] = n
				}))
			}
			return t
		},
		s = (t, e) => (t = String(t), e ? t.replace(/&(?!#\d+;)/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#39;") : t),
		n = (t, e, i) => {
			if (!t || "string" != typeof t) return "";
			let n = document.createElement("div");
			n.innerHTML = t;
			const o = n.querySelectorAll(e || "*");
			return i && o.length ? o[o.length - 1] : o
		},
		o = {
			PANUP: "panup",
			PANDOWN: "pandown",
			PANLEFT: "panleft",
			PANRIGHT: "panright",
			ROTATECCW: "rotateccw",
			ROTATECW: "rotatecw",
			FLIPX: "flipx",
			FLIPY: "flipy",
			FITX: "fitx",
			FITY: "fity",
			RESET: "reset",
			TOGGLEFS: "togglefs"
		};
	let a = null,
		r = null;
	class l {
		constructor(t) {
			this.fancybox = t, this.progress = null, this.isClosing = !1;
			for (const t of ["onReady", "onClosing", "onDone"]) this[t] = this[t].bind(this)
		}
		onReady(t) {
			const e = t.carousel;
			e && (this.progress = this.fancybox.option("Toolbar.progress"), this.progress && e.pages.length < 2 && (this.progress = !1), this.progress && this.create(), this.update(), e.on("change", this.update))
		}
		onClosing(t) {
			const e = t.carousel;
			e && e.off("change", this.update), this.remove()
		}
		create() {
			var t;
			this.remove();
			const e = this.fancybox.carousel,
				i = e.option("Toolbar.parent") || this.fancybox.container;
			if (!i) return;
			const s = n('<div class="f-progress"></div>');
			null === (t = this.fancybox.option("Toolbar.el")) || void 0 === t || t.prepend(s), this.el = s, e.on("settle", (() => {
				this.update()
			}))
		}
		update() {
			var t;
			if (!(null === (t = this.fancybox.carousel) || void 0 === t ? void 0 : t.pages.length)) return void this.remove();
			if (this.el) {
				let t = "",
					e = this.fancybox.getSlide();
				if (e) {
					const i = e.index,
						s = this.fancybox.carousel.pages.length || 0;
					let n = e.contentEl && e.contentEl.querySelector("img");
					if (n) {
						let e = 100 * (n.naturalWidth / n.offsetWidth);
						e > 100 && (e = 100), t = `<div class="f-progress__value" style="width:${e}%"></div>`
					}
					this.el.innerHTML = `<div class="f-progress__bg"></div><div class="f-progress__slider" style="transform: translateX(${s ? 100 * i / s : 0}%)"></div>${t}`
				}
			}
		}
		remove() {
			var t;
			null === (t = this.el) || void 0 === t || t.remove(), this.el = null
		}
		onDone(t) {
			var e;
			null === (e = this.el) || void 0 === e || e.remove()
		}
	}
	class c {
		constructor(t) {
			this.fancybox = t;
			for (const t of ["onReady", "onClosing", "onKeydown"]) this[t] = this[t].bind(this)
		}
		onReady() {
			const t = this.fancybox.option("keyboard");
			t && this.fancybox.container.addEventListener("keydown", this.onKeydown, {
				passive: !1,
				capture: !0
			})
		}
		onClosing() {
			this.fancybox.container.removeEventListener("keydown", this.onKeydown, {
				passive: !1,
				capture: !0
			})
		}
		onKeydown(t) {
			if (this.fancybox.getSlide()) {
				if (this.fancybox.state === "ready") {
					const e = t.key,
						i = this.fancybox,
						s = i.option("keyboard");
					if (!s || t.ctrlKey || t.altKey || t.shiftKey) return;
					const n = t.target;
					if (n && (n.matches('input,textarea,select,button,[contenteditable="true"]') || -1 !== ["BUTTON", "TEXTAREA", "OPTION", "INPUT", "SELECT", "VIDEO"].indexOf(n.nodeName))) return;
					if (t.stopImmediatePropagation(), t.preventDefault(), Object.entries(s).forEach((([s, n]) => {
						"function" != typeof n && (n = [n]);
						for (const a of n)
							if (a === e || a === t.keyCode) switch (s) {
								case "close":
									i.close();
									break;
								case "next":
									i.next();
									break;
								case "prev":
									i.prev();
									break;
								case "toggleFS":
									i.toggleFullscreen();
									break;
								case "toggleThumbs":
									i.toggleThumbs();
									break;

								// Custom events
								default:
									i.trigger(s)
							}
					}))) return
				}
				"error" === this.fancybox.state && "Escape" === t.key && (t.preventDefault(), this.fancybox.close())
			}
		}
	}
	const h = t => {
		let e = 0;
		return t.forEach((t => {
			e += t.w
		})), e
	},
		d = (t, e) => {
			if (Math.abs(t.x - e.x) < 1 && Math.abs(t.y - e.y) < 1) return !1;
			const i = Math.atan2(e.y - t.y, e.x - t.x),
				s = 180 * i / Math.PI;
			let n;
			return s > 45 && s < 135 ? n = "up" : s > -135 && s < -45 ? n = "down" : s > 135 || s < -135 ? n = "left" : n = "right", n
		};
	class u {
		constructor(t) {
			this.fancybox = t, this.viewport = null, this.pendingUpdate = null;
			for (const t of ["onReady", "onResize", "onClosing"]) this[t] = this[t].bind(this)
		}
		onReady() {
			const t = this.fancybox.option("common.parentEl");
			t && (this.viewport = t.querySelector(".fancybox__viewport")), this.viewport || (this.viewport = document.createElement("div"), this.viewport.classList.add("fancybox__viewport"), this.fancybox.container.prepend(this.viewport)), this.fancybox.container.addEventListener("wheel", (t => {
				let e = this.fancybox.getSlide();
				e && e.panzoom && e.panzoom.wheel(t)
			}), {
				passive: !1
			}), window.addEventListener("resize", this.onResize)
		}
		onResize() {
			if (this.pendingUpdate) return;
			const t = this.fancybox.container.getBoundingClientRect();
			this.pendingUpdate = requestAnimationFrame((() => {
				this.updateViewport(t), this.pendingUpdate = null
			}))
		}
		updateViewport(t) {
			const e = this.fancybox.carousel,
				i = this.viewport,
				s = t || this.fancybox.container.getBoundingClientRect(),
				n = {
					width: s.width,
					height: s.height,
					top: s.top,
					left: s.left
				};
			if (i) {
				i.style.width = n.width ? `${n.width}px` : "", i.style.height = n.height ? `${n.height}px` : "", i.style.top = n.top ? `${n.top}px` : "", i.style.left = n.left ? `${n.left}px` : "";
				const t = getComputedStyle(i);
				n.width = parseFloat(t.width), n.height = parseFloat(t.height)
			}
			this.fancybox.option("backdrop") && this.fancybox.backdrop && (this.fancybox.backdrop.style.width = `${n.width}px`, this.fancybox.backdrop.style.height = `${n.height}px`, this.fancybox.backdrop.style.top = `${n.top}px`, this.fancybox.backdrop.style.left = `${n.left}px`), e && (e.slides.forEach((t => {
				t.update(n)
			})), e.update()), this.fancybox.trigger("refresh")
		}
		onClosing() {
			window.removeEventListener("resize", this.onResize), this.pendingUpdate && cancelAnimationFrame(this.pendingUpdate)
		}
		attach(t) {
			this.viewport && this.viewport.append(t)
		}
		detach(t) {
			this.fancybox.container.append(t)
		}
	}
	const f = {
		pageXOffset: 0,
		pageYOffset: 0
	};
	class p {
		constructor(t) {
			this.fancybox = t, this.slides = [], this.page = 0;
			for (const t of ["onReady", "onClosing", "onRefresh", "onPageChange", "onSettle"]) this[t] = this[t].bind(this)
		}
		onReady() {
			this.fancybox.on("Carousel.init", (() => {
				this.fancybox.carousel.on("refresh", this.onRefresh), this.fancybox.carousel.on("change", this.onPageChange), this.fancybox.carousel.on("settle", this.onSettle)
			}))
		}
		onClosing() {
			const t = this.fancybox.carousel;
			t && (t.off("refresh", this.onRefresh), t.off("change", this.onPageChange), t.off("settle", this.onSettle))
		}
		onRefresh(t) {
			this.slides = t.slides.map((t => t.thumbEl))
		}
		onPageChange(t) {
			this.page = t.page
		}
		onSettle(t) {
			t.preventDefault(), this.fancybox.jumpTo(this.page)
		}
	}
	class g {
		constructor(t) {
			this.fancybox = t, this.active = null, this.container = null, this.slides = [];
			for (const t of ["onReady", "onClosing", "onKeydown", "onInit", "onRefresh", "onChange", "onSettle", "onPanzoomChange"]) this[t] = this[t].bind(this)
		}
		onReady() {
			this.fancybox.option("Thumbs") && (this.fancybox.on("init", this.onInit), this.fancybox.on("Carousel.init", (() => {
				const t = this.fancybox.carousel;
				t && (t.on("refresh", this.onRefresh), t.on("change", this.onChange), t.on("settle", this.onSettle))
			})), this.fancybox.on("Panzoom.change", this.onPanzoomChange), this.fancybox.option("Thumbs.autoStart") && this.fancybox.state === "ready" && this.build())
		}
		onClosing() {
			const t = this.carousel;
			t && t.destroy(), this.fancybox.container.classList.remove("has-thumbs"), document.removeEventListener("keydown", this.onKeydown, !0), this.active = !1
		}
		onInit(t) {
			t.state === "ready" && this.fancybox.option("Thumbs.autoStart") && this.build()
		}
		onRefresh(t) {
			if (this.carousel) {
				const e = this.fancybox.slides.map((t => t.thumbEl));
				this.carousel.processSlides(e)
			}
		}
		onChange(t) {
			this.carousel && this.carousel.slideTo(t.page, {
				friction: .12
			})
		}
		onSettle(t) {
			this.carousel && this.carousel.slideTo(t.page)
		}
		onPanzoomChange(t) {
			this.carousel && this.carousel.slides.forEach((e => {
				e.el === t.container && e.el.style.setProperty("--progress", t.progress)
			}))
		}
		build() {
			if (this.active) return;
			const t = document.createElement("div");
			t.classList.add("fancybox__thumbs"), this.fancybox.container.appendChild(t), this.container = t;
			const e = this.fancybox.slides.map((t => t.thumbEl));
			this.carousel = new m(t, i({}, this.fancybox.option("Thumbs.Carousel") || {}, {
				Sync: {
					friction: .12
				},
				slides: e,
				infinite: !1,
				center: !0,
				fill: !0,
				dragFree: !0,
				slidesPerPage: 1,
				transition: !1
			})), this.carousel.on("ready", (() => {
				this.update()
			})), this.active = !0, this.fancybox.container.classList.add("has-thumbs"), this.fancybox.container.style.setProperty("--fancybox-thumbs-height", `${this.container.offsetHeight}px`), document.addEventListener("keydown", this.onKeydown, !0)
		}
		update() {
			const t = this.fancybox.getSlide();
			t && this.carousel && this.carousel.slideTo(t.index)
		}
		cleanup() {
			this.carousel && this.carousel.destroy(), this.carousel = null, this.container && this.container.remove(), this.container = null, this.active = !1, this.fancybox.container.classList.remove("has-thumbs"), this.fancybox.container.style.removeProperty("--fancybox-thumbs-height")
		}
		toggle() {
			this.active ? this.cleanup() : this.build()
		}
		onKeydown(t) {
			var e;
			this.active && "Escape" === t.key && (t.preventDefault(), t.stopPropagation(), null === (e = this.carousel) || void 0 === e || e.emit("keydown", t))
		}
	}
	const m = class {
		constructor(t, e = {}) {
			if (this.options = i(!0, {}, m.defaults, e), this.plugins = m.Plugins.map((t => new t(this)))), this.state = "init", this.emit("init"), this.container = t, this.container.classList.add("f-carousel"), this.slide = null, this.slides = [], this.page = 0, this.prevPage = null, this.userSlide = null, this.userPage = null, this.track = null, this.viewport = null, this.contentDim = 0, this.viewportDim = 0, this.progress = 0, this.friction = 0, this.isDragging = !1, this.isSliding = !1, this.isTicking = !1, this.velocity = 0, this.direction = 0, this.coord = {
				x: 0,
				y: 0
			}, this.transform = {
				x: 0,
				y: 0,
				scale: 1
			}, this.contentBounds = {
				left: 0,
				right: 0,
				top: 0,
				bottom: 0
			}, this.viewportBounds = {
				left: 0,
				right: 0,
				top: 0,
				bottom: 0
			}, this.init(), Object.seal(this), this.emit("ready"), "function" == typeof requestAnimationFrame) {
	const t = () => {
		this.isTicking || (this.isTicking = !0, requestAnimationFrame((() => this.tick())))
	};
	this.animate = t
} else this.animate = () => {
	this.isTicking || (this.isTicking = !0, setTimeout((() => this.tick()), 1e3 / 60))
}
    }
	init() {
	this.on("init", (() => {
		this.initLayout(), this.initSlides(), this.updateMetrics(), this.setInitialPosition(), this.initEvents()
	}), {
		once: !0
	})
}
    initLayout() {
	const t = this.options,
	e = t.parentEl || this.container;
	let i = t.viewport,
	s = t.track;
	if(i || (i = document.createElement("div"), i.classList.add("f-carousel__viewport"), e.prepend(i)), s || (s = document.createElement("div"), s.classList.add("f-carousel__track"), i.prepend(s)), this.track = s, this.viewport = i, this.slide = t.slide, this.options.dotNav) {
	const t = document.createElement("div");
	t.classList.add("f-carousel__dots"), this.container.appendChild(t), this.dotNav = t
}
if (this.options.nav) {
	const t = document.createElement("div");
	t.classList.add("f-carousel__nav"), this.container.appendChild(t), this.nav = t, this.prevButton = n(this.options.prevButton, t), this.nextButton = n(this.options.nextButton, t)
}
    }
initEvents() {
	this.events = new y(this)
}
initSlides() {
	this.processSlides(this.options.slides)
}
processSlides(t) {
	this.slides = [];
	const e = this.track;
	e && (e.innerHTML = ""), t && t.length && (t.forEach(((t, i) => {
		const s = document.createElement("div");
		s.classList.add("f-carousel__slide"), s.innerHTML = t.innerHTML || t.outerHTML || t, s.slide = t, s.index = i, s.pos = i, this.slides.push(s), e && e.appendChild(s)
	})), this.emit("processSlides"))
}
updateMetrics() {
	const t = this.options,
		e = this.track,
		i = this.slides;
	if (!e || !i || !i.length) return;
	const s = getComputedStyle(this.container);
	let n = "horizontal" === t.direction,
		o = n ? "width" : "height",
		a = n ? "offsetWidth" : "offsetHeight";
	this.viewportDim = this.viewport[a];
	let r = 0;
	i.forEach((e => {
		const i = parseFloat(e.dataset.dim) || (n ? e.getBoundingClientRect().width : e.getBoundingClientRect().height);
		e.dim = i, e.gap = parseFloat(getComputedStyle(e)[n ? "marginRight" : "marginBottom"]) || 0, r += e.dim + e.gap
	})), this.contentDim = r, this.pages = this.createPages(), this.updateSlidePositions(), this.updateBounds(), this.emit("updateMetrics")
}
createPages() {
	const t = this.options,
		e = this.contentDim,
		i = this.viewportDim,
		s = this.slides;
	let n = [];
	if (!s || !s.length || !i) return n;
	let o = 0,
		a = 0,
		r = 0;
	const l = t.slidesPerPage;
	if (l > 1) {
		const e = s.length;
		let i = 0,
			o = 0;
		for (; i < e;) {
			let a = 0,
				r = 0;
			const h = [];
			for (; i < e && (a++, h.push(s[i]), r += s[i].dim + s[i].gap, ++i, !(a >= l)););
			n.push({
				slides: h,
				dim: r - s[i - 1].gap,
				index: o++
			})
		}
	} else if (t.fill) {
		const e = t.center,
			l = t.dragFree;
		let c = 0;
		for (const t of s) {
			if (c + (e ? t.dim / 2 : t.dim) > i && !l) break;
			c += t.dim + t.gap
		}
		const h = e ? 1 / 2 : 1,
			d = e ? 1 / 2 : 0;
		for (const t of s) {
			const e = t.dim;
			let s = {
				index: r,
				slides: [t],
				dim: e,
				pos: a + d * e
			};
			!l && a + e > i && (s.pos = i - h * e), n.push(s), a += e + t.gap, r++
		}
	} else
		for (const t of s) {
			const e = t.dim + t.gap;
			o + e > i && n.length && (o = 0), 0 === o && n.push({
				index: r++,
				slides: [],
				dim: 0
			});
			const s = n[n.length - 1];
			s.slides.push(t), s.dim += e, o += e
		}
	return n
}
updateSlidePositions() {
	const t = this.options,
		e = this.pages,
		i = this.slides;
	if (!i || !i.length) return;
	let s = 0;
	if (e && e.length > 1) {
		let n = 0;
		e.forEach(((e, i) => {
			e.pos = n, n += e.dim;
			for (const s of e.slides) s.pos = n - e.dim, s.page = i, s.index = i, "auto" === t.slidesPerPage && (s.dim = s.getBoundingClientRect()[this.isHorizontal() ? "width" : "height"])
		}))
	} else {
		const t = i.length;
		let e = 0;
		for (let n = 0; n < t; n++) {
			const t = i[n];
			t.pos = e, t.page = n, e += t.dim + t.gap
		}
	}
	if (t.center) {
		const e = this.viewportDim / 2 - this.contentDim / 2;
		i.forEach((t => {
			t.pos += e
		}))
	}
	if (t.infinite) {
		const t = this.contentDim,
			e = this.slides[0].dim + this.slides[0].gap,
			s = this.slides[this.slides.length - 1].dim;
		i.forEach((i => {
			i.pos < -e && (i.pos += t), i.pos > t - s && (i.pos -= t)
		}))
	}
}
updateBounds() {
	const t = this.options,
		e = this.track;
	if (!e) return;
	const i = "horizontal" === t.direction,
		s = t.infinite,
		n = this.viewportDim,
		o = this.contentDim;
	let a = -n,
		r = n;
	s && (a = -1 / 0, r = 1 / 0), t.center && (a = Math.min(0, n / 2 - o / 2), r = Math.max(0, n / 2 - o / 2)), this.contentBounds = i ? {
		left: a,
		right: r
	} : {
		top: a,
		bottom: r
	}, this.emit("updateBounds")
}
setInitialPosition() {
	const t = this.options,
		e = t.initialPage;
	let i = 0;
	if (t.infinite) i = (this.pages[e] || this.slides[e]).pos;
	else {
		const s = this.pages[e];
		s && (i = s.pos + (this.viewportDim - s.dim) / 2)
	}
	this.slideTo(e, {
		friction: 0
	})
}
slideTo(t, e = {}) {
	const i = this.options,
		s = this.pages,
		n = this.slides;
	if (!n.length) return;
	const {
		friction: o = i.friction,
		transition: a = i.transition,
		onEnd: r
	} = e;
	if (this.isSliding) return;
	this.isSliding = !0, this.page = t;
	let l = 0;
	if (i.infinite) {
		const e = this.contentDim;
		for (let i = 0; i < 2; i++) {
			let i = (n[t] || s[t]).pos;
			const o = Math.abs(this.coord.x - i),
				a = Math.abs(this.coord.x - (i - e)),
				r = Math.abs(this.coord.x - (i + e));
			r < o && r < a ? (this.coord.x += e, l = e) : a < o && a < r && (this.coord.x -= e, l = -e)
		}
	}
	let c = (n[t] || s[t]).pos;
	if (s.length) {
		const e = s[t];
		if (e) {
			if (i.center) {
				const t = this.viewportDim;
				c = e.pos - (t - e.dim) / 2
			}
			if (!i.infinite) {
				const t = this.contentBounds;
				c = Math.max(c, -t.right), c = Math.min(c, -t.left)
			}
		}
	}
	this.friction = o, this.velocity = 0, this.transform.x = -c, this.direction = this.coord.x > -c ? 1 : -1, this.coord.x = -c, this.transition = a, o ? (this.state = "settle", this.animate()) : this.end(), r && (this.once("end", r), this.once("change", r))
}
prev() {
	this.slideTo(this.page - 1)
}
next() {
	this.slideTo(this.page + 1)
}
slideBy(t) {
	this.slideTo(this.page + t)
}
tick() {
	const t = this.options,
		e = t.infinite,
		i = this.contentDim,
		s = this.slides[0].dim + this.slides[0].gap,
		n = this.slides[this.slides.length - 1].dim,
		o = this.transform,
		a = this.coord,
		r = this.velocity;
	let l = this.friction,
		c = a.x,
		h = 0;
	if (e) {
		let t = -s,
			r = i - n;
		c < t && (h = r - t + 1, c = t), c > r && (h = t - r - 1, c = r)
	}
	let d = a.x - o.x,
		u = r + d * t.bounce;
	o.x += u, o.y += (a.y - o.y) * t.bounce, r *= l;
	let f = this.contentBounds,
		p = this.isHorizontal() ? "x" : "y";
	if (!e) {
		let t = f.left,
			e = f.right,
			i = o[p],
			s = i + r;
		i < -e ? (i = -e, r = 0) : i > -t && (i = -t, r = 0), s < -e ? r -= (s + e) * this.options.bounce : s > -t && (r -= (s + t) * this.options.bounce), o[p] = i
	}
	if (h && (o.x += h, a.x += h), this.track) {
		const t = this.isHorizontal() ? `translate3d(${o.x}px, 0, 0)` : `translate3d(0, ${o.x}px, 0)`;
		this.track.style.transform = t
	}
	this.setProgress(), "settle" === this.state && (Math.abs(d) < 1 && Math.abs(r) < 1 ? this.end() : this.animate()), this.isTicking = !1
}
end() {
	this.isSliding = !1;
	const t = this.page,
		e = this.prevPage;
	this.prevPage = t, this.state = "end", this.emit("end"), t !== e && this.emit("change", {
		page: t,
		prevPage: e
	})
}
setProgress() {
	const t = this.options,
		e = this.track,
		i = this.slides;
	if (!e || !i || !i.length) return;
	const s = this.viewportDim,
		n = this.isHorizontal() ? this.transform.x : this.transform.y,
		o = this.contentDim;
	let a = 0;
	t.center ? a = s / 2 - o / 2 : t.infinite || (a = Math.min(0, Math.max(s - o, 0)));
	const r = (n - a) / (o - s);
	this.progress = Math.min(1, Math.max(0, r)), this.emit("progress", this.progress);
	const l = this.direction;
	i.forEach(((e, i) => {
		const a = e.pos,
			r = e.dim;
		let c = 1,
			h = 0;
		const d = a + n,
			u = r + d;
		let f = Math.abs(d + r / 2) / s;
		const p = Math.abs(d + r / 2) < s ? 1 : 0;
		let g = 0,
			m = 0;
		if (t.infinite) {
			const e = o - s;
			u < 0 && (m += e), d > e && (m -= e), d < -s && (g += e), u > o && (g -= e)
		}
		const y = d + m,
			v = u + m;
		g && (f = Math.abs(y + r / 2) / s);
		const b = Math.max(0, 1 - 2 * f);
		t.visibleUnscaled || (c = Math.max(0, 1.2 - 2 * f)), h = d + m;
		const x = {
			el: e,
			index: i,
			dim: r,
			pos: a,
			inViewport: p,
			progress: b,
			transform: {
				scale: c,
				translate: h
			}
		};
		this.emit("beforeSlideUpdate", x, l), e.style.setProperty("--progress", p ? b : 0), e.style.setProperty("--scale", c), e.style.setProperty("--translate", h), this.emit("afterSlideUpdate", x, l)
	}))
}
destroy() {
	const t = this.options;
	this.state = "destroy", this.emit("destroy"), this.events.destroy(), this.container.classList.remove("f-carousel"), this.container.style.removeProperty("--f-progress-x"), this.nav && this.nav.remove(), this.dotNav && this.dotNav.remove(), this.track && t.slides.length && (this.track.innerHTML = "", this.slides.forEach((e => {
		this.track.appendChild(e.slide)
	}))), this.track && this.track.remove(), this.viewport && this.viewport.remove(), this.track = null, this.viewport = null, this.slides = [], this.page = 0, this.friction = 0, this.velocity = 0
}
    get isHorizontal() {
	return "horizontal" === this.options.direction
}
    get isInfinite() {
	return this.options.infinite
}
  };
m.defaults = {
	slides: [],
	slidesPerPage: "auto",
	initialPage: 0,
	friction: .12,
	transition: "fade",
	direction: "horizontal",
	center: !1,
	infinite: !0,
	fill: !0,
	dragFree: !1,
	dotNav: !1,
	nav: !1,
	prevButton: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path></svg>',
	nextButton: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path></svg>',
	parentEl: null,
	viewport: null,
	track: null,
	slide: null,
	bounce: .2,
	on: null,
	visibleUnscaled: !1
}, m.Plugins = [];
class y {
	constructor(t) {
		this.carousel = t, this.pointerStart = {
			x: 0,
			y: 0
		}, this.pointerMove = {
			x: 0,
			y: 0
		}, this.pointerEnd = {
			x: 0,
			y: 0
		}, this.startTime = 0, this.friction = 0, this.velocity = 0, this.isDragging = !1, this.isClick = !1, this.isScroll = !1, this.onPointerDown = this.onPointerDown.bind(this), this.onPointerMove = this.onPointerMove.bind(this), this.onPointerUp = this.onPointerUp.bind(this), this.onWheel = this.onWheel.bind(this), this.onScroll = this.onScroll.bind(this), this.onSelect = this.onSelect.bind(this), this.onClick = this.onClick.bind(this), this.onNavClick = this.onNavClick.bind(this), this.onDotClick = this.onDotClick.bind(this), this.attach()
	}
	attach() {
		const t = this.carousel.container;
		t.addEventListener("mousedown", this.onPointerDown, {
			passive: !1
		}), t.addEventListener("touchstart", this.onPointerDown, {
			passive: !1
		}), t.addEventListener("wheel", this.onWheel, {
			passive: !1
		}), t.addEventListener("scroll", this.onScroll), this.carousel.dotNav && t.addEventListener("click", ".f-carousel__dot", this.onDotClick), this.carousel.nav && t.addEventListener("click", ".f-carousel__nav button", this.onNavClick)
	}
	detach() {
		const t = this.carousel.container;
		t.removeEventListener("mousedown", this.onPointerDown, {
			passive: !1
		}), t.removeEventListener("touchstart", this.onPointerDown, {
			passive: !1
		}), t.removeEventListener("wheel", this.onWheel, {
			passive: !1
		}), t.removeEventListener("scroll", this.onScroll), this.carousel.dotNav && t.removeEventListener("click", ".f-carousel__dot", this.onDotClick), this.carousel.nav && t.removeEventListener("click", ".f-carousel__nav button", this.onNavClick)
	}
	onSelect(t) {
		"click" === t.type && t.detail > 1 || this.isDragging || t.preventDefault()
	}
	onClick(t) {
		this.isClick || t.preventDefault(), this.isClick = !1
	}
	onDotClick(t) {
		const e = parseInt(t.target.dataset.page, 10);
		this.carousel.slideTo(e)
	}
	onNavClick(t) {
		t.target.matches('[data-dir="prev"]') ? this.carousel.prev() : this.carousel.next()
	}
	onPointerDown(t) {
		if (!["BUTTON", "TEXTAREA", "OPTION", "INPUT", "SELECT", "VIDEO"].includes(t.target.nodeName)) {
			const e = this.carousel,
				i = e.options;
			e.isSliding && (e.end(), t.stopPropagation()), this.isScroll = !1, this.isDragging = !1, this.isClick = !0, this.startTime = (new Date).getTime(), this.pointerStart.x = t.pageX || t.touches[0].pageX, this.pointerStart.y = t.pageY || t.touches[0].pageY, document.addEventListener("selectstart", this.onSelect), document.addEventListener("click", this.onClick, {
				capture: !0
			}), document.addEventListener("touchmove", this.onPointerMove, {
				passive: !1
			}), document.addEventListener("touchend", this.onPointerUp), document.addEventListener("mousemove", this.onPointerMove, {
				passive: !1
			}), document.addEventListener("mouseup", this.onPointerUp), e.friction = i.friction, e.velocity = 0
		}
	}
	onPointerMove(t) {
		const e = this.carousel,
			i = e.options,
			s = this.pointerStart,
			n = t.touches && t.touches[0] || t,
			o = this.pointerMove;
		o.x = n.pageX, o.y = n.pageY;
		const a = o.x - s.x,
			r = o.y - s.y;
		if (!this.isDragging) {
			if (Math.abs(a) < 3 && Math.abs(r) < 3) return;
			const t = d(s, o);
			let n = e.isHorizontal() ? "left" === t || "right" === t : "up" === t || "down" === t;
			if (n) {
				if (e.isSliding) return void t.stopPropagation();
				this.isDragging = !0, e.container.classList.add("is-dragging"), e.state = "drag"
			} else this.isScroll = !0;
			this.isClick = !1
		}
		if (this.isDragging) {
			let t = e.isHorizontal() ? a : r;
			e.velocity = e.direction * (t - e.touches.diff), e.touches.diff = t, e.coord.x = e.transform.x + t, e.animate()
		} else this.isScroll && e.end()
	}
	onPointerUp(t) {
		const e = this.carousel,
			i = e.options;
		if (document.removeEventListener("selectstart", this.onSelect), document.removeEventListener("touchmove", this.onPointerMove, {
			passive: !1
		}), document.removeEventListener("touchend", this.onPointerUp), document.removeEventListener("mousemove", this.onPointerMove, {
			passive: !1
		}), document.removeEventListener("mouseup", this.onPointerUp), setTimeout((() => {
			document.removeEventListener("click", this.onClick, {
				capture: !0
			})
		}), 100), this.isDragging) {
			e.container.classList.remove("is-dragging"), e.state = "settle";
			const t = e.isHorizontal() ? this.pointerMove.x - this.pointerStart.x : this.pointerMove.y - this.pointerStart.y,
				s = (new Date).getTime() - this.startTime,
				n = e.velocity,
				o = i.dragFree;
			if (Math.abs(t) > 5 && s < 250) {
				const t = 1.5 * n / s;
				e.velocity = Math.max(-25, Math.min(25, t))
			}
			if (o) {
				const t = e.contentBounds;
				e.transform.x < -t.right && (e.coord.x = -t.right, e.velocity = 0), e.transform.x > -t.left && (e.coord.x = -t.left, e.velocity = 0)
			} else {
				let t = e.page,
					s = e.direction;
				s < 0 ? e.velocity < -i.threshold || t < e.pages.length - 1 ? t++ : s = 0 : s > 0 && (e.velocity > i.threshold || t > 0 ? t-- : s = 0), e.slideTo(t)
			}
			e.animate()
		} else this.isScroll || e.end()
	}
	onWheel(t) {
		t.preventDefault();
		const e = this.carousel,
			i = e.options;
		let s = -t.deltaY || -t.deltaX || t.wheelDelta || -t.detail;
		s = Math.max(-1, Math.min(1, s));
		let n = e.page;
		e.isSliding || (e.state = "wheel", e.velocity += s, n = e.page - Math.sign(s), e.slideTo(n), this.friction = i.friction)
	}
	onScroll() {
		const t = this.carousel;
		t.isSliding || t.end()
	}
	destroy() {
		this.detach()
	}
}
const b = {
	classes: {
		container: "f-thumbs",
		viewport: "f-thumbs__viewport",
		track: "f-thumbs__track",
		slide: "f-thumbs__slide"
	},
	dotNav: !1,
	nav: !1,
	slides: [],
	slidesPerPage: "auto",
	initialPage: null,
	center: !0,
	fill: !0,
	dragFree: !1,
	animationEffect: "fade",
	animationDuration: 250,
	parentEl: null,
	thumbTpl: '<button class="f-thumbs__slide__button" type="button"><img class="f-thumbs__slide__img" data-lazy-src="%i"/></button>',
	on: null
};
class x extends m {
	constructor(t, e = {}) {
		super(t, i(!0, {}, b, e)), this.state = "init"
	}
	initSlides() {
		const t = this.options.slides;
		this.slides = [], t && t.length && t.forEach(((t, e) => {
			const i = document.createElement("div");
			i.classList.add("f-carousel__slide", "f-thumbs__slide");
			const s = n(this.options.thumbTpl.replace(/%i/g, t.thumbSrc));
			i.appendChild(s), i.slide = t, i.index = e, i.pos = e, this.slides.push(i), this.track.appendChild(i)
		}))
	}
}
x.Plugins = m.Plugins;
const w = {
	panOnlyZoomed: !0,
	lockAxis: !1,
	friction: .12,
	bounce: .2,
	maxScale: 2,
	minScale: 1,
	on: null
};
class S {
	constructor(t, e = {}) {
		this.container = t, this.options = i({}, w, e), this.state = "init", this.id = this.options.id || (new Date).getTime(), this.container.panzoom = this, this.bind(), this.attach()
	}
	bind() {
		for (const t of ["onPointerDown", "onPointerMove", "onPointerUp", "onWheel", "onClick"]) this[t] = this[t].bind(this)
	}
	attach() {
		const t = this.container;
		t.addEventListener("mousedown", this.onPointerDown, {
			passive: !1
		}), t.addEventListener("touchstart", this.onPointerDown, {
			passive: !1
		}), t.addEventListener("wheel", this.onWheel, {
			passive: !1
		}), t.addEventListener("click", this.onClick, {
			capture: !0
		})
	}
	detach() {
		const t = this.container;
		t.removeEventListener("mousedown", this.onPointerDown, {
			passive: !1
		}), t.removeEventListener("touchstart", this.onPointerDown, {
			passive: !1
		}), t.removeEventListener("wheel", this.onWheel, {
			passive: !1
		}), t.removeEventListener("click", this.onClick, {
			capture: !0
		}), this.content && (this.content.style.removeProperty("transform"), this.content.style.removeProperty("filter")), this.content = null
	}
	onPointerDown(t) {
		if (t.button && 0 !== t.button) return;
		const e = this.content,
			i = this.container;
		if (!e) return;
		const s = e.getBoundingClientRect(),
			n = i.getBoundingClientRect(),
			o = getComputedStyle(i);
		if (this.options.panOnlyZoomed && this.transform.scale < 1.01) {
			if (s.left < n.left - 1 || s.right > n.right + 1 || s.top < n.top - 1 || s.bottom > n.bottom + 1) return
		}
		if (this.isTicking) return void t.preventDefault();
		if (this.pointerStart = {
			x: t.clientX,
			y: t.clientY
		}, this.start = {
			x: this.transform.x,
			y: this.transform.y,
			scale: this.transform.scale
		}, this.stopAnimation(), document.addEventListener("touchmove", this.onPointerMove, {
			passive: !1
		}), document.addEventListener("touchend", this.onPointerUp), document.addEventListener("mousemove", this.onPointerMove, {
			passive: !1
		}), document.addEventListener("mouseup", this.onPointerUp), this.isDragging = !0, this.trigger("start"), this.oneMove) {
			const t = this.content.getBoundingClientRect();
			this.boundX = {
				from: 0,
				to: this.container.clientWidth - t.width
			}, this.boundY = {
				from: 0,
				to: this.container.clientHeight - t.height
			}
		}
	}
	onPointerMove(t) {
		if (!this.isDragging) return;
		const e = this.pointerStart,
			i = {
				x: t.clientX,
				y: t.clientY
			},
			s = this.options.lockAxis;
		if (this.transform.scale < 1.01) {
			if ("x" === s) return;
			if ("y" === s) return
		}
		if (this.oneMove || (Math.abs(i.x - e.x) < 3 && Math.abs(i.y - e.y) < 3 ? this.oneMove = !1 : (this.oneMove = !0, this.clickStart = null)), this.oneMove) {
			const t = this.start.x + (i.x - e.x),
				s = this.start.y + (i.y - e.y),
				n = this.boundX,
				o = this.boundY;
			let a = t,
				r = s;
			n && (a = Math.max(n.from, Math.min(a, n.to))), o && (r = Math.max(o.from, Math.min(r, o.to))), this.transform.x = a, this.transform.y = r, this.animate()
		}
	}
	onPointerUp(t) {
		document.removeEventListener("touchmove", this.onPointerMove, {
			passive: !1
		}), document.removeEventListener("touchend", this.onPointerUp), document.removeEventListener("mousemove", this.onPointerMove, {
			passive: !1
		}), document.removeEventListener("mouseup", this.onPointerUp), this.isDragging = !1, this.oneMove && (this.oneMove = !1, this.trigger("end"))
	}
	onClick(t) {
		if (this.isDragging) return t.preventDefault(), void t.stopImmediatePropagation();
		const e = this.clickStart,
			i = {
				x: t.clientX,
				y: t.clientY
			};
		e && Math.abs(i.x - e.x) < 3 && Math.abs(i.y - e.y) < 3 ? this.trigger("click", t) : this.clickStart = i
	}
	onWheel(t) {
		t.preventDefault();
		let e = -t.deltaY || -t.deltaX || t.wheelDelta || -t.detail;
		e = Math.max(-1, Math.min(1, e));
		let i = this.transform.scale;
		this.zoomTo(i + i * e * .2, {
			x: t.clientX,
			y: t.clientY
		})
	}
	zoomTo(t, e = {}) {
		const i = this.content;
		if (!i) return;
		const s = this.transform.scale,
			n = this.options.minScale,
			o = this.options.maxScale;
		t = Math.max(n, Math.min(t, o));
		let a = e.x || this.container.clientWidth / 2,
			r = e.y || this.container.clientHeight / 2;
		const l = i.getBoundingClientRect(),
			c = (a - l.left) / s,
			h = (r - l.top) / s,
			d = this.transform.x - (c * t - c * s),
			u = this.transform.y - (h * t - h * s);
		this.transform.x = d, this.transform.y = u, this.transform.scale = t, this.animate()
	}
	stopAnimation() {
		this.isTicking = !1
	}
	animate() {
		this.isTicking || (this.isTicking = !0, requestAnimationFrame((() => this.tick())))
	}
	tick() {
		if (!this.content) return;
		const t = this.transform,
			e = this.current,
			i = this.options.friction;
		let s = t.x,
			n = t.y,
			o = t.scale;
		this.current.x += (s - e.x) * i, this.current.y += (n - e.y) * i, this.current.scale += (o - e.scale) * i;
		const a = this.current.x.toFixed(2),
			r = this.current.y.toFixed(2),
			l = this.current.scale.toFixed(4);
		if (this.content.style.transform = `translate3d(${a}px, ${r}px, 0) scale(${l})`, this.trigger("change"), Math.abs(s - e.x) < .01 && Math.abs(n - e.y) < .01 && Math.abs(o - e.scale) < .001) return void this.stopAnimation();
		this.animate()
	}
	setContent(t) {
		this.content && this.detach(), this.content = t, this.state = "ready", this.stopAnimation(), this.transform = {
			x: 0,
			y: 0,
			scale: 1
		}, this.current = {
			x: 0,
			y: 0,
			scale: 1
		}, this.content.style.transform = `translate3d(0px, 0px, 0) scale(1)`, this.trigger("ready")
	}
	trigger(t, ...e) {
		if (this.options.on && "function" == typeof this.options.on[t]) {
			const i = this.options.on[t];
			try {
				i.call(this, this, ...e)
			} catch (t) {
				console.error(t)
			}
		}
		const i = new CustomEvent(t, {
			bubbles: !0,
			cancelable: !0,
			detail: e
		});
		this.container.dispatchEvent(i)
	}
	destroy() {
		this.detach(), this.state = "destroy", delete this.container.panzoom
	}
}
class v {
	constructor(t) {
		this.fancybox = t;
		for (const t of ["onReady", "onClosing"]) this[t] = this[t].bind(this)
	}
	onReady() {
		this.fancybox.on("Carousel.init", (t => {
			t.slides.forEach((t => {
				t.el && this.setAspectRatio(t)
			}))
		})), this.fancybox.on("Carousel.settle", (t => {
			this.fancybox.getSlide().panzoom.setContent(this.fancybox.getSlide().contentEl)
		}))
	}
	onClosing() { }
	setAspectRatio(t) {
		const e = t.contentEl;
		if (!e) return;
		const i = this.fancybox.optionFor(t, "ratio") || e.dataset.ratio || null;
		if (!i) return;
		const s = e.getBoundingClientRect(),
			n = parseFloat(i);
		e.style.setProperty("--f-aspect-ratio", n), e.style.setProperty("--f-width", s.width), e.style.setProperty("--f-height", s.height)
	}
}
const b = {
	arrowLeft: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11.28 15.7l-1.34 1.37L5 12l4.94-5.07 1.34 1.38-2.68 2.72H19v1.94H8.6z"/></svg>',
	arrowRight: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z"/></svg>',
	close: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>',
	download: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19.35 10.04A7.49 7.49 0 0 0 12 4C9.11 4 6.6 5.64 5.35 8.04A5.994 5.994 0 0 0 0 14a6 6 0 0 0 6 6h13a5 5 0 0 0 5-5c0-2.64-2.05-4.78-4.65-4.96zM17 13v4h-10v-4H5l7-7 7 7h-2z"/></svg>',
	thumbs: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3 3v18h18V3H3zm8 16H5v-6h6v6zm0-8H5V5h6v6zm8 8h-6v-6h6v6zm0-8h-6V5h6v6z"/></svg>',
	zoom: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>'
};
class x {
	constructor(t) {
		this.fancybox = t, this.active = null, this.container = null;
		for (const t of ["onReady", "onClosing", "onDone", "onKeydown", "onPageChange", "onSettle"]) this[t] = this[t].bind(this)
	}
	onReady() {
		const t = this.fancybox.option("Toolbar");
		t && (t.autoStart || !this.fancybox.fromStart) && this.build(), this.fancybox.on("Carousel.init", (() => {
			this.fancybox.carousel.on("change", this.onPageChange), this.fancybox.carousel.on("settle", this.onSettle)
		}))
	}
	onClosing() {
		var t;
		null === (t = this.fancybox.carousel) || void 0 === t || t.off("change", this.onPageChange), null === (t = this.fancybox.carousel) || void 0 === t || t.off("settle", this.onSettle), document.removeEventListener("keydown", this.onKeydown, !0)
	}
	onDone(t) {
		var e;
		t.carousel && (null === (e = this.container) || void 0 === e || e.remove())
	}
	onKeydown(t) {
		"Escape" === t.key && this.fancybox.getSlide().state === "ready" && (t.preventDefault(), t.stopPropagation(), this.fancybox.close())
	}
	onPageChange() {
		this.update()
	}
	onSettle() {
		this.update()
	}
	build() {
		this.cleanup();
		const t = this.fancybox.option("Toolbar.items"),
			e = [{
				tpl: this.fancybox.option("Toolbar.tplLeft"),
				items: t.left || [],
				className: "left"
			}, {
				tpl: this.fancybox.option("Toolbar.tplCenter"),
				items: t.center || [],
				className: "center"
			}, {
				tpl: this.fancybox.option("Toolbar.tplRight"),
				items: t.right || [],
				className: "right"
			}],
			i = document.createElement("div");
		i.classList.add("fancybox__toolbar");
		for (const t of e)
			if (t.items.length) {
				const e = n(t.tpl);
				e.classList.add(`fancybox__toolbar__${t.className}`);
				for (const s of t.items) {
					let t;
					if ("string" == typeof s) {
						if (s.startsWith("html_")) {
							t = document.createElement("div"), t.innerHTML = this.fancybox.option(s.substring(5));
							continue
						}
						if (s === o[s.toUpperCase()]) {
							const e = this.fancybox.option(`Toolbar.buttons.${s}`);
							if (!e) continue;
							t = n(e.tpl || this.fancybox.option(`Toolbar.buttonTpl`)), t.addEventListener("click", (() => {
								this.fancybox.trigger(s)
							})), e.class && t.classList.add(e.class)
						} else b[s] && (t = n(this.fancybox.option("Toolbar.buttonTpl")), t.addEventListener("click", (() => {
							this.fancybox.trigger(s)
						})), t.appendChild(n(b[s])))
					}
					t && e.appendChild(t)
				}
				i.appendChild(e)
			}
		const s = this.fancybox.option("Toolbar.parent") || this.fancybox.container;
		s && s.prepend(i), this.container = i, this.update(), document.addEventListener("keydown", this.onKeydown, !0)
	}
	update() {
		const t = this.fancybox.getSlide(),
			e = t.index,
			i = this.fancybox.carousel.pages.length || 0;
		if (this.container) {
			this.container.style.visibility = t.state === "ready" ? "" : "hidden";
			for (const s of this.container.querySelectorAll("[data-fancybox-index]")) s.innerHTML = e + 1;
			for (const s of this.container.querySelectorAll("[data-fancybox-count]")) s.innerHTML = i;
			for (const s of this.container.querySelectorAll('[data-fancybox-prev]')) s.disabled = !this.fancybox.hasPrev();
			for (const s of this.container.querySelectorAll('[data-fancybox-next]')) s.disabled = !this.fancybox.hasNext();
			for (const e of this.container.querySelectorAll("[data-fancybox-download]")) {
				const i = t.downloadSrc || t.src || "";
				i ? (e.href = i, e.download = i, e.style.display = "") : e.style.display = "none"
			}
		}
	}
	cleanup() {
		this.container && this.container.remove(), this.container = null
	}
}
class w extends l {
	onReady(t) {
		var e;
		this.progress = this.fancybox.option("Toolbar.progress"), this.progress && (this.container = (null === (e = this.fancybox.option("Toolbar.el")) || void 0 === e ? void 0 : e.querySelector(".f-progress")) || null, this.create())
	}
	create() {
		this.remove(), super.create()
	}
}
const S = {
	animated: !0,
	autoStart: !0,
	backdrop: "rgba(24, 25, 26, .9)",
	click: "close",
	closeButton: "inside",
	common: {
		parentEl: null
	},
	compact: "auto",
	contentClick: "toggleZoom",
	contentDblClick: !1,
	defaultDisplay: "flex",
	defaultType: "image",
	delegate: "[data-fancybox]",
	drageable: "auto",
	hideScrollbar: !0,
	idle: 3500,
	keyboard: {
		Escape: "close",
		Delete: "close",
		Backspace: "close",
		PageUp: "prev",
		PageDown: "next",
		ArrowUp: "prev",
		ArrowDown: "next",
		ArrowRight: "next",
		ArrowLeft: "prev"
	},
	l10n: {
		CLOSE: "Close",
		NEXT: "Next",
		PREV: "Previous",
		MODAL: "You can close this modal content with the ESC key",
		ERROR: "Something Went Wrong, Please Try Again Later",
		IMAGE_ERROR: "Image Not Found",
		ELEMENT_NOT_FOUND: "HTML Element Not Found",
		AJAX_NOT_FOUND: "Error Loading AJAX : Not Found",
		AJAX_FORBIDDEN: "Error Loading AJAX : Forbidden",
		IFRAME_ERROR: "Error Loading Page"
	},
	mainClass: "",
	placeFocusBack: !0,
	slug: "",
	startIndex: 0,
	Toolbar: {
		autoStart: !0,
		buttons: {
			close: {
				tpl: b.close
			}
		},
		display: {
			left: [],
			middle: [],
			right: ["close"]
		},
		progress: !0
	},
	Thumbs: {
		type: "modern"
	},
	trapFocus: !0,
	wheel: "zoom"
};
let C = 0;
class P extends (function (t) {
	return Object.getOwnPropertyNames(t).reduce(((e, i) => (e[i] = t[i], e)), {})
}({
	on(t, e, i) {
		const s = this;
		if (!s.events) return s;
		let n = t.split(" ");
		return i ? n.forEach((t => {
			s.events[t] || (s.events[t] = []);
			const n = s.events[t];
			n && (e.once = !0, n.includes(e) || n.unshift(e))
		})) : n.forEach((t => {
			s.events[t] || (s.events[t] = []), s.events[t].push(e)
		})), s
	},
	once(t, e) {
		return this.on(t, e, !0)
	},
	off(t, e) {
		const i = this;
		if (!i.events) return i;
		t.split(" ").forEach((t => {
			const s = i.events[t];
			if (!s || !s.length) return;
			let n = -1;
			for (let t = 0; t < s.length; t++) s[t] === e && (n = t); - 1 !== n && s.splice(n, 1)
		}))
	},
	emit(t, ...e) {
		const i = this;
		if (!i.events) return;
		const s = i.events[t];
		if (s && s.length) {
			let t = [...s];
			for (const s of t) try {
				s.apply(i, e)
			} catch (t) {
				console.error(t)
			}
		}
		const n = i.events["*"];
		if (n && n.length) {
			let s = [...n];
			for (const n of s) try {
				n.apply(i, [t, ...e])
			} catch (t) {
				console.error(t)
			}
		}
	}
})) {
	constructor(t = {}, e = []) {
		super(), this.options = i(!0, {}, S, t), this.plugins = [new u(this), new c(this), ...e], this.events = {}, this.userSlides = [], this.state = "init", this.id = this.options.id || ++C, this.init()
	}
	init() {
		if (this.state !== "init") return;
		this.timers = new Set, this.on("init", (() => {
			this.attachPlugins(), this.emit("attachPlugins")
		}), {
			once: !0
		}), this.emit("init"), this.state !== "destroy" && (this.state = "ready", this.emit("ready"))
	}
	attachPlugins() {
		for (const t of this.plugins) t.onReady(this)
	}
	option(t, ...e) {
		const s = this.getSlide();
		let n = s ? this.optionFor(s, t) : void 0;
		let o = this.options;
		if (t && "function" == typeof t.charAt) {
			if (void 0 !== n) return n;
			let e = t.split(".");
			for (const t of e) {
				if ("object" != typeof o || null === o || !(t in o)) {
					o = void 0;
					break
				}
				o = o[t]
			}
			return o
		}
		if (e.length) {
			let i = e[0];
			const n = s && s.type && this.options[s.type] || {};
			if (e.length > 1) {
				const s = e[1];
				let n = t.split(".");
				const a = n.pop();
				let r = n.reduce(((t, e) => t[e]), o);
				r && (r[a] = s)
			} else "object" == typeof i ? o = i : o[t] = i
		}
	}
	optionFor(t, e) {
		let s = t.options || {};
		if (e && "function" == typeof e.charAt) {
			let n = e.split(".");
			for (const t of n) {
				if ("object" != typeof s || null === s || !(t in s)) {
					s = void 0;
					break
				}
				s = s[t]
			}
		}
		return s
	}
	getSlide() {
		const t = this.carousel;
		if (!t) return;
		const e = t.page;
		return t.slides[e]
	}
	getSlides() {
		var t;
		return (null === (t = this.carousel) || void 0 === t ? void 0 : t.slides) || []
	}
	close(t) {
		if (this.isClosing()) return;
		const e = new Event("shouldClose", {
			bubbles: !0,
			cancelable: !0
		});
		if (this.emit("shouldClose", e, t), e.defaultPrevented) return;
		this.isClosing = !0, this.state = "closing", this.emit("closing", t);
		const i = this.container;
		this.carousel && this.carousel.state !== "destroy" && this.carousel.destroy();
		for (const t of this.timers) clearTimeout(t);
		this.timers.clear();
		const s = this.options.placeFocusBack ? this.options.triggerEl || null : null;
		if (s) {
			const t = this.scrollPosition;
			t && window.scrollTo(t.left, t.top);
			const e = () => {
				try {
					s.focus({
						preventScroll: !0
					})
				} catch (t) { }
			};
			/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream ? setTimeout((() => e()), 200) : e()
		}
		const n = () => {
			this.emit("done"), this.state = "destroy", this.emit("destroy"), P.getInstance(this.id) && P.destroy(this.id)
		};
		if (i.classList.remove("is-closing"), this.options.animated && i.offsetParent) {
			i.addEventListener("animationend", (() => {
				i.remove(), n()
			}), {
				once: !0
			});
			const t = getComputedStyle(i);
			t.animationName && "none" !== t.animationName && t.animationDuration && parseFloat(t.animationDuration)
		} else i.remove(), n();
		this.options.hideScrollbar && document.documentElement.classList.remove("with-fancybox")
	}
	destroy() {
		this.state !== "destroy" && (this.state = "destroy", this.emit("destroy"))
	}
	isClosing() {
		return ["closing", "destroy"].includes(this.state)
	}
	static bind(t, e) {
		let i = null;
		const s = (s, n) => {
			s.preventDefault(), s.stopPropagation();
			const o = s.currentTarget,
				a = new P(e);
			a.options.triggerEl = o;
			let r = [],
				l = a.options.delegate;
			if (l) {
				r = o.querySelectorAll(l);
				const t = o.closest(l);
				t && r.length < 2 && r.push(t)
			} else r = [o];
			const c = r.length;
			if (c) {
				const t = o.dataset.fancybox || "";
				if (t && c > 1) {
					const e = document.querySelectorAll(`[data-fancybox="${t}"]`);
					r = Array.from(e)
				}
				let e = [];
				for (const t of r) e.push({
					src: t.href || t.dataset.src || t.src,
					triggerEl: t
				});
				a.load(e, r.indexOf(o)), i = a
			}
		};
		document.querySelectorAll(t).forEach((t => {
			t.addEventListener("click", s)
		}))
	}
	static getInstance(t) {
		if (t) return P.openers.get(t);
		const e = Array.from(P.openers.values()).reverse();
		return e.length ? e[0] : null
	}
	static getSlide() {
		var t;
		return (null === (t = P.getInstance()) || void 0 === t ? void 0 : t.getSlide()) || null
	}
	static getSlides() {
		var t;
		return (null === (t = P.getInstance()) || void 0 === t ? void 0 : t.getSlides()) || []
	}
	static show(t, e) {
		return new P(e).load(t)
	}
	static close() {
		const t = P.getInstance();
		t && t.close()
	}
	static destroy(t) {
		const e = P.getInstance(t);
		e && e.destroy(), P.openers.delete(t)
	}
}
return P.version = t, P.defaults = S, P.Plugins = {
	Toolbar: x,
	Thumbs: g,
	Html: v,
	Images: class {
		constructor(t) {
			this.fancybox = t;
			for (const t of ["onReady", "onClosing", "onInitSlide", "onRemoveSlide", "onChange", "onSettle", "onRefresh"]) this[t] = this[t].bind(this)
		}
		onReady() {
			this.fancybox.on("Carousel.initSlide", this.onInitSlide), this.fancybox.on("Carousel.removeSlide", this.onRemoveSlide), this.fancybox.on("Carousel.change", this.onChange), this.fancybox.on("Carousel.settle", this.onSettle), this.fancybox.on("Carousel.Panzoom.ready", ((t, e) => {
				this.onPanzoomReady(e)
			}))
		}
		onClosing() {
			this.fancybox.off("Carousel.initSlide", this.onInitSlide), this.fancybox.off("Carousel.removeSlide", this.onRemoveSlide), this.fancybox.off("Carousel.change", this.onChange), this.fancybox.off("Carousel.settle", this.onSettle)
		}
		onInitSlide(t, e) {
			this.processType(e)
		}
		onRemoveSlide(t, e) {
			e.panzoom && e.panzoom.destroy(), e.imageEl = null
		}
		onChange(t, e, i) {
			for (const s of t.slides) {
				const t = s.panzoom;
				t && s.index !== i && t.reset(.35)
			}
		}
		onSettle(t) {
			const e = t.getSlide();
			e && e.panzoom && e.panzoom.reset(.35)
		}
		onRefresh(t, e) {
			e.slides.forEach((t => {
				t.panzoom && t.panzoom.update()
			}))
		}
		processType(t) {
			if (t.el && "image" === t.type && !t.imageEl) {
				const e = document.createElement("img");
				e.classList.add("fancybox-image"), e.src = t.src || "", e.alt = t.alt || "", e.draggable = !1, t.sizes && (e.sizes = t.sizes), t.srcset && (e.srcset = t.srcset), t.imageEl = e;
				const i = this.fancybox.optionFor(t, "content");
				if (i) {
					const t = document.createElement("div");
					t.classList.add("fancybox-content"), t.appendChild(e), t.insertAdjacentHTML("beforeend", i), t.contentEl = t
				}
				this.lazyLoad(t)
			}
		}
		lazyLoad(t) {
			const e = this.fancybox,
				i = t.imageEl;
			if (!i) return;
			e.emit("beforeLoad", t);
			const s = e.optionFor(t, "thumb"),
				n = e.optionFor(t, "placeholder");
			let o = "idle";
			const a = () => {
				t.state = "ready", e.revealContent(t), e.emit("done", t)
			};
			if (i.src) {
				const e = i.complete && i.naturalWidth > 0;
				if (e) return void a();
				if (n) {
					i.alt = "", i.src = n;
					const e = i.complete && i.naturalWidth > 0;
					e && (i.classList.add("f-placeholder"), i.alt = t.alt || "", i.src = t.src || "")
				}
			} else i.src = s || t.src || "";
			t.state = "loading", i.addEventListener("load", (() => {
				i.classList.remove("f-placeholder"), t.state = "loaded", e.emit("load", t), "idle" === o ? a() : this.revealImage(t)
			})), i.addEventListener("error", (() => {
				t.state = "error", e.revealContent(t), e.emit("error", t)
			})), setTimeout((() => {
				o = "ready", i.complete && i.naturalWidth > 0 ? a() : i.src && this.revealImage(t)
			}), 500)
		}
		revealImage(t) {
			const e = t.imageEl;
			e && (e.style.visibility = "", e.classList.add("fancybox-fadeIn"), setTimeout((() => {
				e.classList.remove("fancybox-fadeIn")
			}), 350))
		}
		onPanzoomReady(t) {
			const e = this.fancybox.option("Images.contentClick"),
				i = this.fancybox.option("Images.contentDblClick");
			e && t.on("click", e), i && t.on("dblClick", i)
		}
	},
	IFrames: class {
		constructor(t) {
			this.fancybox = t;
			for (const t of ["onReady", "onClosing", "onInitSlide", "onRemoveSlide", "onRefresh"]) this[t] = this[t].bind(this)
		}
		onReady() {
			this.fancybox.on("Carousel.initSlide", this.onInitSlide)
		}
		onClosing() { }
		onInitSlide(t, e) {
			this.processType(e)
		}
		onRemoveSlide(t, e) { }
		onRefresh(t, e) { }
		processType(t) {
			if (!t.el || "iframe" !== t.type && "video" !== t.type || t.iframeEl) return;
			const e = document.createElement("iframe");
			e.className = "fancybox-iframe", e.name = `fancybox-iframe-${(new Date).getTime()}`, e.frameborder = "0", e.scrolling = "auto", e.setAttribute("allowfullscreen", ""), e.setAttribute("allow", "autoplay; fullscreen");
			let i = this.fancybox.optionFor(t, "src");
			if ("video" === t.type) {
				const e = this.fancybox.optionFor(t, "video");
				i = this.fancybox.option("videoTpl").replace(/%s/, i).replace(/%w/, e.width).replace(/%h/, e.height).replace(/%a/, e.autoplay ? "1" : "0")
			}
			e.src = i || t.src || "", t.iframeEl = e;
			const s = this.fancybox.optionFor(t, "content");
			if (s) {
				const t = document.createElement("div");
				t.classList.add("fancybox-content"), t.appendChild(e), t.insertAdjacentHTML("beforeend", s), t.contentEl = t
			}
			this.lazyLoad(t)
		}
		lazyLoad(t) {
			const e = this.fancybox,
				i = t.iframeEl;
			if (!i) return;
			e.emit("beforeLoad", t), t.state = "loading", i.addEventListener("load", (() => {
				t.state = "ready", e.revealContent(t), e.emit("done", t)
			})), i.addEventListener("error", (() => {
				t.state = "error", e.revealContent(t), e.emit("error", t)
			}))
		}
	},
	Carousel: class {
		constructor(t) {
			this.fancybox = t;
			for (const t of ["onReady", "onClosing", "onRefresh", "onPageChange", "onSettle"]) this[t] = this[t].bind(this)
		}
		onReady() {
			this.fancybox.carousel = new m(this.fancybox.container.querySelector(".fancybox__carousel"), i({
				Dots: {
					dotTpl: '<button type="button" class="f-carousel__dot" data-page="%i" aria-label="Go to page %i"></button>'
				}
			}, this.fancybox.option("Carousel") || {}, {
				on: {
					init: t => this.fancybox.emit("Carousel.init", t),
					initSlide: (t, e) => this.fancybox.emit("Carousel.initSlide", t, e),
					removeSlide: (t, e) => this.fancybox.emit("Carousel.removeSlide", t, e),
					settle: t => this.fancybox.emit("Carousel.settle", t),
					refresh: t => this.fancybox.emit("Carousel.refresh", t),
					change: (t, e, i) => this.fancybox.emit("Carousel.change", t, e, i)
				}
			}))
		}
		onClosing() {
			this.fancybox.carousel && this.fancybox.carousel.destroy()
		}
		onRefresh(t) {
			this.fancybox.carousel.processSlides(this.fancybox.slides)
		}
		onPageChange(t) {
			this.fancybox.page = t.page
		}
		onSettle(t) {
			this.fancybox.page = t.page
		}
	},
	Spinner: l,
	Toolbar: class {
		constructor(t) {
			this.fancybox = t;
			for (const t of ["onReady", "onClosing", "onKeydown", "onDone"]) this[t] = this[t].bind(this)
		}
		onReady() {
			var t;
			this.fancybox.option("Toolbar") && (this.instance = new x(this.fancybox)), this.progress = new w(this.fancybox), null === (t = this.instance) || void 0 === t || t.build()
		}
		onClosing() {
			var t;
			null === (t = this.instance) || void 0 === t || t.cleanup(), document.removeEventListener("keydown", this.onKeydown, !0)
		}
		onDone(t) {
			var e;
			t.carousel && (null === (e = this.instance) || void 0 === e || e.cleanup())
		}
		onKeydown(t) {
			"Escape" === t.key && this.fancybox.getSlide().state === "ready" && (t.preventDefault(), t.stopPropagation(), this.fancybox.close())
		}
	},
	Thumbs: g
}


	/* Jquery.sticky.min.js
	-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */
	//
	//  jQuery Sticky Kit
	//  version 1.0.4 | Copyright 2015, Anthony Garand
	//
	(function (factory) {
		if (typeof define === 'function' && define.amd) {
			// AMD
			define(['jquery'], factory);
		} else if (typeof exports === 'object') {
			// CommonJS
			module.exports = factory(require('jquery'));
		} else {
			// Browser globals
			factory(jQuery);
		}
	}(function ($) {
		var win = $(window);

		$.fn.stick = function (opts) {
			var doc, elm, enable_bottoming, inner_scrolling, manual_spacer, offset_top, outer_width, parent_selector, recalc_every, sticky_class, win_height;
			if (opts == null) {
				opts = {};
			}
			sticky_class = opts.sticky_class, parent_selector = opts.parent, offset_top = opts.offset_top, recalc_every = opts.recalc_every, inner_scrolling = opts.inner_scrolling, manual_spacer = opts.spacer, enable_bottoming = opts.bottoming;
			if (offset_top == null) {
				offset_top = 0;
			}
			if (parent_selector == null) {
				parent_selector = void 0;
			}
			if (inner_scrolling == null) {
				inner_scrolling = true;
			}
			if (sticky_class == null) {
				sticky_class = "is_stuck";
			}
			doc = $(document);
			if (enable_bottoming == null) {
				enable_bottoming = true;
			}
			win_height = function () {
				return win.height();
			};
			outer_width = function () {
				var $this, w;
				$this = $(this);
				w = $this.width();
				$this.parents().each(function () {
					var parent;
					parent = $(this);
					if (parent.css('float') === 'right' || parent.css('float') === 'left') {
						return w = parent.width();
					}
				});
				return w;
			};

			// ... (full sticky code) ...
			!function (t) { "function" == typeof define && define.amd ? define(["jquery"], t) : "object" == typeof module && module.exports ? module.exports = t(require("jquery")) : t(jQuery) }(function (t) { var e = Array.prototype.slice, i = Array.prototype.splice, n = { topSpacing: 0, bottomSpacing: 0, className: "is-sticky", wrapperClassName: "sticky-wrapper", center: !1, getWidthFrom: "", widthFromWrapper: !0, responsiveWidth: !1, zIndex: "auto" }, r = t(window), s = t(document), o = [], c = r.height(), a = function () { for (var e = r.scrollTop(), i = s.height(), n = i - c, a = e > n ? n - e : 0, p = 0, d = o.length; p < d; p++) { var l = o[p], u = l.stickyWrapper.offset().top, h = u - l.topSpacing - a; if (l.stickyWrapper.css("height", l.stickyElement.outerHeight()), e <= h) null !== l.currentTop && (l.stickyElement.css({ width: "", position: "", top: "", "z-index": "" }), l.stickyElement.parent().removeClass(l.className), l.stickyElement.trigger("sticky-end", [l]), l.currentTop = null); else { var g = i - l.stickyElement.outerHeight() - l.topSpacing - l.bottomSpacing - e - a; if (g < 0 ? g += l.topSpacing : g = l.topSpacing, l.currentTop !== g) { var m; l.getWidthFrom ? m = t(l.getWidthFrom).width() || null : l.widthFromWrapper && (m = l.stickyWrapper.width()), null == m && (m = l.stickyElement.width()), l.stickyElement.css("width", m).css("position", "fixed").css("top", g).css("z-index", l.zIndex), l.stickyElement.parent().addClass(l.className), null === l.currentTop ? l.stickyElement.trigger("sticky-start", [l]) : l.stickyElement.trigger("sticky-update", [l]), l.currentTop === l.topSpacing && l.currentTop > g || null === l.currentTop && g < l.topSpacing ? l.stickyElement.trigger("sticky-bottom-reached", [l]) : null !== l.currentTop && g === l.topSpacing && l.currentTop < g && l.stickyElement.trigger("sticky-bottom-unreached", [l]), l.currentTop = g } var y = l.stickyWrapper.parent(), f = l.stickyElement.offset().top + l.stickyElement.outerHeight() >= y.offset().top + y.outerHeight() && l.stickyElement.offset().top <= l.topSpacing; f ? l.stickyElement.css("position", "absolute").css("top", "").css("bottom", 0).css("z-index", "") : l.stickyElement.css("position", "fixed").css("top", g).css("bottom", "").css("z-index", l.zIndex) } } }, p = function () { c = r.height(); for (var e = 0, i = o.length; e < i; e++) { var n = o[e], s = null; n.getWidthFrom ? n.responsiveWidth && (s = t(n.getWidthFrom).width()) : n.widthFromWrapper && (s = n.stickyWrapper.width()), null != s && n.stickyElement.css("width", s) } }, d = { init: function (e) { return this.each(function () { var i = t.extend({}, n, e), r = t(this), s = r.attr("id"), c = s ? s + "-" + n.wrapperClassName : n.wrapperClassName, a = t("<div></div>").attr("id", c).addClass(i.wrapperClassName); r.wrapAll(function () { if (0 == t(this).parent("#" + c).length) return a }); var p = r.parent(); i.center && p.css({ width: r.outerWidth(), marginLeft: "auto", marginRight: "auto" }), "right" === r.css("float") && r.css({ float: "none" }).parent().css({ float: "right" }), i.stickyElement = r, i.stickyWrapper = p, i.currentTop = null, o.push(i), d.setWrapperHeight(this), d.setupChangeListeners(this) }) }, setWrapperHeight: function (e) { var i = t(e), n = i.parent(); n && n.css("height", i.outerHeight()) }, setupChangeListeners: function (t) { if (window.MutationObserver) { var e = new window.MutationObserver(function (e) { (e[0].addedNodes.length || e[0].removedNodes.length) && d.setWrapperHeight(t) }); e.observe(t, { subtree: !0, childList: !0 }) } else window.addEventListener ? (t.addEventListener("DOMNodeInserted", function () { d.setWrapperHeight(t) }, !1), t.addEventListener("DOMNodeRemoved", function () { d.setWrapperHeight(t) }, !1)) : window.attachEvent && (t.attachEvent("onDOMNodeInserted", function () { d.setWrapperHeight(t) }), t.attachEvent("onDOMNodeRemoved", function () { d.setWrapperHeight(t) })) }, update: a, unstick: function (e) { return this.each(function () { for (var e = this, n = t(e), r = -1, s = o.length; s-- > 0;)o[s].stickyElement.get(0) === e && (i.call(o, s, 1), r = s); r !== -1 && (n.unwrap(), n.css({ width: "", position: "", top: "", float: "", "z-index": "" })) }) } }; window.addEventListener ? (window.addEventListener("scroll", a, !1), window.addEventListener("resize", p, !1)) : window.attachEvent && (window.attachEvent("onscroll", a), window.attachEvent("onresize", p)), t.fn.sticky = function (i) { return d[i] ? d[i].apply(this, e.call(arguments, 1)) : "object" != typeof i && i ? void t.error("Method " + i + " does not exist on jQuery.sticky") : d.init.apply(this, arguments) }, t.fn.unstick = function (i) { return d[i] ? d[i].apply(this, e.call(arguments, 1)) : "object" != typeof i && i ? void t.error("Method " + i + " does not exist on jQuery.sticky") : d.unstick.apply(this, arguments) }, t(function () { setTimeout(a, 0) }) });

			return this;
		};
	}));

//# sourceMappingURL=jquery.sticky.min.js.map


/* bootstrap-spin - v1.0
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/* ========================================================================
 * bootstrap-spin - v1.0
 * https://github.com/wpic/bootstrap-spin
 * ========================================================================
 * Copyright 2014 WPIC, Hamed Abdollahpour
 *
 * ========================================================================
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */

(function ($) {

	$.fn.bootstrapNumber = function (options) {

		var settings = $.extend({
			upClass: 'default',
			downClass: 'default',
			center: true
		}, options);

		return this.each(function (e) {
			var self = $(this);
			var clone = self.clone();

			var min = self.attr('min');
			var max = self.attr('max');

			function setText(n) {
				if ((min && n < min) || (max && n > max)) {
					return false;
				}

				clone.val(n);
				return true;
			}

			var group = $("<div class='input-group'></div>");
			var down = $("<button type='button'><span class='icon icon-minus'></span></button>").attr('class', 'btn btn-' + settings.downClass).click(function () {
				setText(parseInt(clone.val()) - 1);
			});
			var up = $("<button type='button'><span class='icon icon-plus'></span></button>").attr('class', 'btn btn-' + settings.upClass).click(function () {
				setText(parseInt(clone.val()) + 1);
			});
			$("<span class='input-group-btn'></span>").append(down).appendTo(group);
			clone.appendTo(group);
			if (clone) {
				clone.css('text-align', 'center');
			}
			$("<span class='input-group-btn'></span>").append(up).appendTo(group);

			// remove spins from original
			clone.attr('type', 'text').keydown(function (e) {
				if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -0 ||
					(e.keyCode == 65 && e.ctrlKey === true) ||
					(e.keyCode >= 35 && e.keyCode <= 39)) {
					return;
				}
				if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
					e.preventDefault();
				}

				var c = String.fromCharCode(e.which);
				var n = parseInt(clone.val() + c);

				if ((min && n < min) || (max && n > max)) {
					e.preventDefault();
				}
			});

			self.replaceWith(group);
		});
	};
}(jQuery));


/* Jquery Countdown
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/*!
 * The Final Countdown for jQuery v2.2.0 (http://hilios.github.io/jQuery.countdown/)
 * Copyright (c) 2016 Edson Hilios
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
!function (a) { "use strict"; "function" == typeof define && define.amd ? define(["jquery"], a) : a(jQuery) }(function (a) { "use strict"; function b(a) { if (a instanceof Date) return a; if (String(a).match(g)) return String(a).match(/^[0-9]*$/) && (a = Number(a)), String(a).match(/\-/) && (a = String(a).replace(/\-/g, "/")), new Date(a); throw new Error("Couldn't cast `" + a + "` to a date object.") } function c(a) { var b = a.toString().replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1"); return new RegExp(b) } function d(a) { return function (b) { var d = b.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi); if (d) for (var f = 0, g = d.length; f < g; ++f) { var h = d[f].match(/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/), j = c(h[0]), k = h[1] || "", l = h[3] || "", m = null; h = h[2], i.hasOwnProperty(h) && (m = i[h], m = Number(a[m])), null !== m && ("!" === k && (m = e(l, m)), "" === k && m < 10 && (m = "0" + m.toString()), b = b.replace(j, m.toString())) } return b = b.replace(/%%/, "%") } } function e(a, b) { var c = "s", d = ""; return a && (a = a.replace(/(:|;|\s)/gi, "").split(/\,/), 1 === a.length ? c = a[0] : (d = a[0], c = a[1])), Math.abs(b) > 1 ? c : d } var f = [], g = [], h = { precision: 100, elapse: !1, defer: !1 }; g.push(/^[0-9]*$/.source), g.push(/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source), g.push(/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source), g = new RegExp(g.join("|")); var i = { Y: "years", m: "months", n: "daysToMonth", d: "daysToWeek", w: "weeks", W: "weeksToMonth", H: "hours", M: "minutes", S: "seconds", D: "totalDays", I: "totalHours", N: "totalMinutes", T: "totalSeconds" }, j = function (b, c, d) { this.el = b, this.$el = a(b), this.interval = null, this.offset = {}, this.options = a.extend({}, h), this.instanceNumber = f.length, f.push(this), this.$el.data("countdown-instance", this.instanceNumber), d && ("function" == typeof d ? (this.$el.on("update.countdown", d), this.$el.on("stoped.countdown", d), this.$el.on("finish.countdown", d)) : this.options = a.extend({}, h, d)), this.setFinalDate(c), this.options.defer === !1 && this.start() }; a.extend(j.prototype, { start: function () { null !== this.interval && clearInterval(this.interval); var a = this; this.update(), this.interval = setInterval(function () { a.update.call(a) }, this.options.precision) }, stop: function () { clearInterval(this.interval), this.interval = null, this.dispatchEvent("stoped") }, toggle: function () { this.interval ? this.stop() : this.start() }, pause: function () { this.stop() }, resume: function () { this.start() }, remove: function () { this.stop.call(this), f[this.instanceNumber] = null, delete this.$el.data().countdownInstance }, setFinalDate: function (a) { this.finalDate = b(a) }, update: function () { if (0 === this.$el.closest("html").length) return void this.remove(); var b, c = void 0 !== a._data(this.el, "events"), d = new Date; b = this.finalDate.getTime() - d.getTime(), b = Math.ceil(b / 1e3), b = !this.options.elapse && b < 0 ? 0 : Math.abs(b), this.totalSecsLeft !== b && c && (this.totalSecsLeft = b, this.elapsed = d >= this.finalDate, this.offset = { seconds: this.totalSecsLeft % 60, minutes: Math.floor(this.totalSecsLeft / 60) % 60, hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24, days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7, daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7, daysToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 % 30.4368), weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7), weeksToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7) % 4, months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368), years: Math.abs(this.finalDate.getFullYear() - d.getFullYear()), totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24), totalHours: Math.floor(this.totalSecsLeft / 60 / 60), totalMinutes: Math.floor(this.totalSecsLeft / 60), totalSeconds: this.totalSecsLeft }, this.options.elapse || 0 !== this.totalSecsLeft ? this.dispatchEvent("update") : (this.stop(), this.dispatchEvent("finish"))) }, dispatchEvent: function (b) { var c = a.Event(b + ".countdown"); c.finalDate = this.finalDate, c.elapsed = this.elapsed, c.offset = a.extend({}, this.offset), c.strftime = d(this.offset), this.$el.trigger(c) } }), a.fn.countdown = function () { var b = Array.prototype.slice.call(arguments, 0); return this.each(function () { var c = a(this).data("countdown-instance"); if (void 0 !== c) { var d = f[c], e = b[0]; j.prototype.hasOwnProperty(e) ? d[e].apply(d, b.slice(1)) : null === String(e).match(/^[$A-Z_][0-9A-Z_$]*$/i) ? (d.setFinalDate.call(d, e), d.start()) : a.error("Method %s does not exist on jQuery.countdown".replace(/\%s/gi, e)) } else new j(this, b[0], b[1]) }) } });


/* JQuery.nicescroll 3.6.8
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/* jquery.nicescroll 3.7.6 InuYaksa*2017 MIT http://nicescroll.areaaperta.com */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		// Node/CommonJS
		module.exports = factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function (jQuery) {

	// ... (full nicescroll code) ...
	(function (f) { "function" === typeof define && define.amd ? define(["jquery"], f) : "object" === typeof exports ? module.exports = f(require("jquery")) : f(jQuery) })(function (f) {
		var B = !1, F = !1, O = 0, P = 2E3, A = 0, J = ["webkit", "ms", "moz", "o"], v = window.requestAnimationFrame || !1, w = window.cancelAnimationFrame || !1; if (!v) for (var Q in J) { var G = J[Q]; if (v = window[G + "RequestAnimationFrame"]) { w = window[G + "CancelAnimationFrame"] || window[G + "CancelRequestAnimationFrame"]; break } } var x = window.MutationObserver || window.WebKitMutationObserver ||
			!1, K = {
				zindex: "auto", cursoropacitymin: 0, cursoropacitymax: 1, cursorcolor: "#424242", cursorwidth: "6px", cursorborder: "1px solid #fff", cursorborderradius: "5px", scrollspeed: 60, mousescrollstep: 24, touchbehavior: !1, hwacceleration: !0, usetransition: !0, boxzoom: !1, dblclickzoom: !0, gesturezoom: !0, grabcursorenabled: !0, autohidemode: !0, background: "", iframeautoresize: !0, cursorminheight: 32, preservenativescrolling: !0, railoffset: !1, railhoffset: !1, bouncescroll: !0, spacebarenabled: !0, railpadding: { top: 0, right: 0, left: 0, bottom: 0 },
				disableoutline: !0, horizrailenabled: !0, railalign: "right", railvalign: "bottom", enabletranslate3d: !0, enablemousewheel: !0, enablekeyboard: !0, smoothscroll: !0, sensitiverail: !0, enablemouselockapi: !0, cursorfixedheight: !1, directionlockdeadzone: 6, hidecursordelay: 400, nativeparentscrolling: !0, enablescrollonselection: !0, overflowx: !0, overflowy: !0, cursordragspeed: .3, rtlmode: "auto", cursordragontouch: !1, oneaxismousemode: "auto", scriptpath: function () {
					var f = document.getElementsByTagName("script"), f = f.length ? f[f.length -
						1].src.split("?")[0] : ""; return 0 < f.split("/").length ? f.split("/").slice(0, -1).join("/") + "/" : ""
				}(), preventmultitouchscrolling: !0, disablemutationobserver: !1
			}, H = !1, R = function () {
				if (H) return H; var f = document.createElement("DIV"), c = f.style, k = navigator.userAgent, l = navigator.platform, d = { haspointerlock: "pointerLockElement" in document || "webkitPointerLockElement" in document || "mozPointerLockElement" in document }; d.isopera = "opera" in window; d.isopera12 = d.isopera && "getUserMedia" in navigator; d.isoperamini = "[object OperaMini]" ===
					Object.prototype.toString.call(window.operamini); d.isie = "all" in document && "attachEvent" in f && !d.isopera; d.isieold = d.isie && !("msInterpolationMode" in c); d.isie7 = d.isie && !d.isieold && (!("documentMode" in document) || 7 == document.documentMode); d.isie8 = d.isie && "documentMode" in document && 8 == document.documentMode; d.isie9 = d.isie && "performance" in window && 9 == document.documentMode; d.isie10 = d.isie && "performance" in window && 10 == document.documentMode; d.isie11 = "msRequestFullscreen" in f && 11 <= document.documentMode; d.isieedge12 =
						navigator.userAgent.match(/Edge\/12\./); d.isieedge = "msOverflowStyle" in f; d.ismodernie = d.isie11 || d.isieedge; d.isie9mobile = /iemobile.9/i.test(k); d.isie9mobile && (d.isie9 = !1); d.isie7mobile = !d.isie9mobile && d.isie7 && /iemobile/i.test(k); d.ismozilla = "MozAppearance" in c; d.iswebkit = "WebkitAppearance" in c; d.ischrome = "chrome" in window; d.ischrome38 = d.ischrome && "touchAction" in c; d.ischrome22 = !d.ischrome38 && d.ischrome && d.haspointerlock; d.ischrome26 = !d.ischrome38 && d.ischrome && "transition" in c; d.cantouch = "ontouchstart" in
							document.documentElement || "ontouchstart" in window; d.hasw3ctouch = (window.PointerEvent || !1) && (0 < navigator.MaxTouchPoints || 0 < navigator.msMaxTouchPoints); d.hasmstouch = !d.hasw3ctouch && (window.MSPointerEvent || !1); d.ismac = /^mac$/i.test(l); d.isios = d.cantouch && /iphone|ipad|ipod/i.test(l); d.isios4 = d.isios && !("seal" in Object); d.isios7 = d.isios && "webkitHidden" in document; d.isios8 = d.isios && "hidden" in document; d.isandroid = /android/i.test(k); d.haseventlistener = "addEventListener" in f; d.trstyle = !1; d.hastransform = !1;
				d.hastranslate3d = !1; d.transitionstyle = !1; d.hastransition = !1; d.transitionend = !1; l = ["transform", "msTransform", "webkitTransform", "MozTransform", "OTransform"]; for (k = 0; k < l.length; k++)if (void 0 !== c[l[k]]) { d.trstyle = l[k]; break } d.hastransform = !!d.trstyle; d.hastransform && (c[d.trstyle] = "translate3d(1px,2px,3px)", d.hastranslate3d = /translate3d/.test(c[d.trstyle])); d.transitionstyle = !1; d.prefixstyle = ""; d.transitionend = !1; for (var l = "transition webkitTransition msTransition MozTransition OTransition OTransition KhtmlTransition".split(" "),
					q = " -webkit- -ms- -moz- -o- -o -khtml-".split(" "), t = "transitionend webkitTransitionEnd msTransitionEnd transitionend otransitionend oTransitionEnd KhtmlTransitionEnd".split(" "), k = 0; k < l.length; k++)if (l[k] in c) { d.transitionstyle = l[k]; d.prefixstyle = q[k]; d.transitionend = t[k]; break } d.ischrome26 && (d.prefixstyle = q[1]); d.hastransition = d.transitionstyle; a: {
						k = ["grab", "-webkit-grab", "-moz-grab"]; if (d.ischrome && !d.ischrome38 || d.isie) k = []; for (l = 0; l < k.length; l++)if (q = k[l], c.cursor = q, c.cursor == q) { c = q; break a } c =
							"url(//patriciaportfolio.googlecode.com/files/openhand.cur),n-resize"
					} d.cursorgrabvalue = c; d.hasmousecapture = "setCapture" in f; d.hasMutationObserver = !1 !== x; return H = d
			}, S = function (h, c) {
				function k() { var b = a.doc.css(e.trstyle); return b && "matrix" == b.substr(0, 6) ? b.replace(/^.*\((.*)\)$/g, "$1").replace(/px/g, "").split(/, +/) : !1 } function l() { var b = a.win; if ("zIndex" in b) return b.zIndex(); for (; 0 < b.length && 9 != b[0].nodeType;) { var g = b.css("zIndex"); if (!isNaN(g) && 0 != g) return parseInt(g); b = b.parent() } return !1 } function d(b,
					g, u) { g = b.css(g); b = parseFloat(g); return isNaN(b) ? (b = z[g] || 0, u = 3 == b ? u ? a.win.outerHeight() - a.win.innerHeight() : a.win.outerWidth() - a.win.innerWidth() : 1, a.isie8 && b && (b += 1), u ? b : 0) : b } function q(b, g, u, c) {
						a._bind(b, g, function (a) {
							a = a ? a : window.event; var c = {
								original: a, target: a.target || a.srcElement, type: "wheel", deltaMode: "MozMousePixelScroll" == a.type ? 0 : 1, deltaX: 0, deltaZ: 0, preventDefault: function () { a.preventDefault ? a.preventDefault() : a.returnValue = !1; return !1 }, stopImmediatePropagation: function () {
									a.stopImmediatePropagation ?
										a.stopImmediatePropagation() : a.cancelBubble = !0
								}
							}; "mousewheel" == g ? (a.wheelDeltaX && (c.deltaX = -.025 * a.wheelDeltaX), a.wheelDeltaY && (c.deltaY = -.025 * a.wheelDeltaY), c.deltaY || c.deltaX || (c.deltaY = -.025 * a.wheelDelta)) : c.deltaY = a.detail; return u.call(b, c)
						}, c)
					} function t(b, g, c) {
						var d, e; 0 == b.deltaMode ? (d = -Math.floor(a.opt.mousescrollstep / 54 * b.deltaX), e = -Math.floor(a.opt.mousescrollstep / 54 * b.deltaY)) : 1 == b.deltaMode && (d = -Math.floor(b.deltaX * a.opt.mousescrollstep), e = -Math.floor(b.deltaY * a.opt.mousescrollstep));
						g && a.opt.oneaxismousemode && 0 == d && e && (d = e, e = 0, c && (0 > d ? a.getScrollLeft() >= a.page.maxw : 0 >= a.getScrollLeft()) && (e = d, d = 0)); a.isrtlmode && (d = -d); d && (a.scrollmom && a.scrollmom.stop(), a.lastdeltax += d, a.debounced("mousewheelx", function () { var b = a.lastdeltax; a.lastdeltax = 0; a.rail.drag || a.doScrollLeftBy(b) }, 15)); if (e) {
							if (a.opt.nativeparentscrolling && c && !a.ispage && !a.zoomactive) if (0 > e) { if (a.getScrollTop() >= a.page.maxh) return !0 } else if (0 >= a.getScrollTop()) return !0; a.scrollmom && a.scrollmom.stop(); a.lastdeltay += e;
							a.synched("mousewheely", function () { var b = a.lastdeltay; a.lastdeltay = 0; a.rail.drag || a.doScrollBy(b) }, 15)
						} b.stopImmediatePropagation(); return b.preventDefault()
					} var a = this; this.version = "3.6.8"; this.name = "nicescroll"; this.me = c; this.opt = { doc: f("body"), win: !1 }; f.extend(this.opt, K); this.opt.snapbackspeed = 80; if (h) for (var r in a.opt) void 0 !== h[r] && (a.opt[r] = h[r]); a.opt.disablemutationobserver && (x = !1); this.iddoc = (this.doc = a.opt.doc) && this.doc[0] ? this.doc[0].id || "" : ""; this.ispage = /^BODY|HTML/.test(a.opt.win ?
						a.opt.win[0].nodeName : this.doc[0].nodeName); this.haswrapper = !1 !== a.opt.win; this.win = a.opt.win || (this.ispage ? f(window) : this.doc); this.docscroll = this.ispage && !this.haswrapper ? f(window) : this.win; this.body = f("body"); this.iframe = this.isfixed = this.viewport = !1; this.isiframe = "IFRAME" == this.doc[0].nodeName && "IFRAME" == this.win[0].nodeName; this.istextarea = "TEXTAREA" == this.win[0].nodeName; this.forcescreen = !1; this.canshowonmouseevent = "scroll" != a.opt.autohidemode; this.page = this.view = this.onzoomout = this.onzoomin =
							this.onscrollcancel = this.onscrollend = this.onscrollstart = this.onclick = this.ongesturezoom = this.onkeypress = this.onmousewheel = this.onmousemove = this.onmouseup = this.onmousedown = !1; this.scroll = { x: 0, y: 0 }; this.scrollratio = { x: 0, y: 0 }; this.cursorheight = 20; this.scrollvaluemax = 0; if ("auto" == this.opt.rtlmode) {
								r = this.win[0] == window ? this.body : this.win; var p = r.css("writing-mode") || r.css("-webkit-writing-mode") || r.css("-ms-writing-mode") || r.css("-moz-writing-mode"); "horizontal-tb" == p || "lr-tb" == p || "" == p ? (this.isrtlmode =
									"rtl" == r.css("direction"), this.isvertical = !1) : (this.isrtlmode = "vertical-rl" == p || "tb" == p || "tb-rl" == p || "rl-tb" == p, this.isvertical = "vertical-rl" == p || "tb" == p || "tb-rl" == p)
							} else this.isrtlmode = !0 === this.opt.rtlmode, this.isvertical = !1; this.observerbody = this.observerremover = this.observer = this.scrollmom = this.scrollrunning = !1; do this.id = "ascrail" + P++; while (document.getElementById(this.id)); this.hasmousefocus = this.hasfocus = this.zoomactive = this.zoom = this.selectiondrag = this.cursorfreezed = this.cursor = this.rail =
								!1; this.visibility = !0; this.hidden = this.locked = this.railslocked = !1; this.cursoractive = !0; this.wheelprevented = !1; this.overflowx = a.opt.overflowx; this.overflowy = a.opt.overflowy; this.nativescrollingarea = !1; this.checkarea = 0; this.events = []; this.saved = {}; this.delaylist = {}; this.synclist = {}; this.lastdeltay = this.lastdeltax = 0; this.detected = R(); var e = f.extend({}, this.detected); this.ishwscroll = (this.canhwscroll = e.hastransform && a.opt.hwacceleration) && a.haswrapper; this.hasreversehr = this.isrtlmode ? this.isvertical ?
									!(e.iswebkit || e.isie || e.isie11) : !(e.iswebkit || e.isie && !e.isie10 && !e.isie11) : !1; this.istouchcapable = !1; e.cantouch || !e.hasw3ctouch && !e.hasmstouch ? !e.cantouch || e.isios || e.isandroid || !e.iswebkit && !e.ismozilla || (this.istouchcapable = !0) : this.istouchcapable = !0; a.opt.enablemouselockapi || (e.hasmousecapture = !1, e.haspointerlock = !1); this.debounced = function (b, g, c) { a && (a.delaylist[b] || (g.call(a), a.delaylist[b] = { h: v(function () { a.delaylist[b].fn.call(a); a.delaylist[b] = !1 }, c) }), a.delaylist[b].fn = g) }; var I = !1; this.synched =
										function (b, g) { a.synclist[b] = g; (function () { I || (v(function () { if (a) { I = !1; for (var b in a.synclist) { var g = a.synclist[b]; g && g.call(a); a.synclist[b] = !1 } } }), I = !0) })(); return b }; this.unsynched = function (b) { a.synclist[b] && (a.synclist[b] = !1) }; this.css = function (b, g) { for (var c in g) a.saved.css.push([b, c, b.css(c)]), b.css(c, g[c]) }; this.scrollTop = function (b) { return void 0 === b ? a.getScrollTop() : a.setScrollTop(b) }; this.scrollLeft = function (b) { return void 0 === b ? a.getScrollLeft() : a.setScrollLeft(b) }; var D = function (a, g,
											c, d, e, f, k) { this.st = a; this.ed = g; this.spd = c; this.p1 = d || 0; this.p2 = e || 1; this.p3 = f || 0; this.p4 = k || 1; this.ts = (new Date).getTime(); this.df = this.ed - this.st }; D.prototype = {
												B2: function (a) { return 3 * a * a * (1 - a) }, B3: function (a) { return 3 * a * (1 - a) * (1 - a) }, B4: function (a) { return (1 - a) * (1 - a) * (1 - a) }, getNow: function () { var a = 1 - ((new Date).getTime() - this.ts) / this.spd, g = this.B2(a) + this.B3(a) + this.B4(a); return 0 > a ? this.ed : this.st + Math.round(this.df * g) }, update: function (a, g) {
													this.st = this.getNow(); this.ed = a; this.spd = g; this.ts = (new Date).getTime();
													this.df = this.ed - this.st; return this
												}
											}; if (this.ishwscroll) {
												this.doc.translate = { x: 0, y: 0, tx: "0px", ty: "0px" }; e.hastranslate3d && e.isios && this.doc.css("-webkit-backface-visibility", "hidden"); this.getScrollTop = function (b) { if (!b) { if (b = k()) return 16 == b.length ? -b[13] : -b[5]; if (a.timerscroll && a.timerscroll.bz) return a.timerscroll.bz.getNow() } return a.doc.translate.y }; this.getScrollLeft = function (b) { if (!b) { if (b = k()) return 16 == b.length ? -b[12] : -b[4]; if (a.timerscroll && a.timerscroll.bh) return a.timerscroll.bh.getNow() } return a.doc.translate.x };
												this.notifyScrollEvent = function (a) { var g = document.createEvent("UIEvents"); g.initUIEvent("scroll", !1, !0, window, 1); g.niceevent = !0; a.dispatchEvent(g) }; var y = this.isrtlmode ? 1 : -1; e.hastranslate3d && a.opt.enabletranslate3d ? (this.setScrollTop = function (b, g) { a.doc.translate.y = b; a.doc.translate.ty = -1 * b + "px"; a.doc.css(e.trstyle, "translate3d(" + a.doc.translate.tx + "," + a.doc.translate.ty + ",0px)"); g || a.notifyScrollEvent(a.win[0]) }, this.setScrollLeft = function (b, g) {
													a.doc.translate.x = b; a.doc.translate.tx = b * y + "px"; a.doc.css(e.trstyle,
														"translate3d(" + a.doc.translate.tx + "," + a.doc.translate.ty + ",0px)"); g || a.notifyScrollEvent(a.win[0])
												}) : (this.setScrollTop = function (b, g) { a.doc.translate.y = b; a.doc.translate.ty = -1 * b + "px"; a.doc.css(e.trstyle, "translate(" + a.doc.translate.tx + "," + a.doc.translate.ty + ")"); g || a.notifyScrollEvent(a.win[0]) }, this.setScrollLeft = function (b, g) { a.doc.translate.x = b; a.doc.translate.tx = b * y + "px"; a.doc.css(e.trstyle, "translate(" + a.doc.translate.tx + "," + a.doc.translate.ty + ")"); g || a.notifyScrollEvent(a.win[0]) })
											} else this.getScrollTop =
												function () { return a.docscroll.scrollTop() }, this.setScrollTop = function (b) { return setTimeout(function () { a && a.docscroll.scrollTop(b) }, 1) }, this.getScrollLeft = function () { return a.hasreversehr ? a.detected.ismozilla ? a.page.maxw - Math.abs(a.docscroll.scrollLeft()) : a.page.maxw - a.docscroll.scrollLeft() : a.docscroll.scrollLeft() }, this.setScrollLeft = function (b) { return setTimeout(function () { if (a) return a.hasreversehr && (b = a.detected.ismozilla ? -(a.page.maxw - b) : a.page.maxw - b), a.docscroll.scrollLeft(b) }, 1) }; this.getTarget =
													function (a) { return a ? a.target ? a.target : a.srcElement ? a.srcElement : !1 : !1 }; this.hasParent = function (a, g) { if (!a) return !1; for (var c = a.target || a.srcElement || a || !1; c && c.id != g;)c = c.parentNode || !1; return !1 !== c }; var z = { thin: 1, medium: 3, thick: 5 }; this.getDocumentScrollOffset = function () { return { top: window.pageYOffset || document.documentElement.scrollTop, left: window.pageXOffset || document.documentElement.scrollLeft } }; this.getOffset = function () {
														if (a.isfixed) {
															var b = a.win.offset(), g = a.getDocumentScrollOffset(); b.top -= g.top;
															b.left -= g.left; return b
														} b = a.win.offset(); if (!a.viewport) return b; g = a.viewport.offset(); return { top: b.top - g.top, left: b.left - g.left }
													}; this.updateScrollBar = function (b) {
														var g, c, e; if (a.ishwscroll) a.rail.css({ height: a.win.innerHeight() - (a.opt.railpadding.top + a.opt.railpadding.bottom) }), a.railh && a.railh.css({ width: a.win.innerWidth() - (a.opt.railpadding.left + a.opt.railpadding.right) }); else {
															var f = a.getOffset(); g = f.top; c = f.left - (a.opt.railpadding.left + a.opt.railpadding.right); g += d(a.win, "border-top-width", !0);
															c += a.rail.align ? a.win.outerWidth() - d(a.win, "border-right-width") - a.rail.width : d(a.win, "border-left-width"); if (e = a.opt.railoffset) e.top && (g += e.top), e.left && (c += e.left); a.railslocked || a.rail.css({ top: g, left: c, height: (b ? b.h : a.win.innerHeight()) - (a.opt.railpadding.top + a.opt.railpadding.bottom) }); a.zoom && a.zoom.css({ top: g + 1, left: 1 == a.rail.align ? c - 20 : c + a.rail.width + 4 }); if (a.railh && !a.railslocked) {
																g = f.top; c = f.left; if (e = a.opt.railhoffset) e.top && (g += e.top), e.left && (c += e.left); b = a.railh.align ? g + d(a.win, "border-top-width",
																	!0) + a.win.innerHeight() - a.railh.height : g + d(a.win, "border-top-width", !0); c += d(a.win, "border-left-width"); a.railh.css({ top: b - (a.opt.railpadding.top + a.opt.railpadding.bottom), left: c, width: a.railh.width })
															}
														}
													}; this.doRailClick = function (b, g, c) {
														var d; a.railslocked || (a.cancelEvent(b), g ? (g = c ? a.doScrollLeft : a.doScrollTop, d = c ? (b.pageX - a.railh.offset().left - a.cursorwidth / 2) * a.scrollratio.x : (b.pageY - a.rail.offset().top - a.cursorheight / 2) * a.scrollratio.y, g(d)) : (g = c ? a.doScrollLeftBy : a.doScrollBy, d = c ? a.scroll.x : a.scroll.y,
															b = c ? b.pageX - a.railh.offset().left : b.pageY - a.rail.offset().top, c = c ? a.view.w : a.view.h, g(d >= b ? c : -c)))
													}; a.hasanimationframe = v; a.hascancelanimationframe = w; a.hasanimationframe ? a.hascancelanimationframe || (w = function () { a.cancelAnimationFrame = !0 }) : (v = function (a) { return setTimeout(a, 15 - Math.floor(+new Date / 1E3) % 16) }, w = clearTimeout); this.init = function () {
														a.saved.css = []; if (e.isie7mobile || e.isoperamini) return !0; e.hasmstouch && a.css(a.ispage ? f("html") : a.win, { _touchaction: "none" }); var b = e.ismodernie || e.isie10 ? { "-ms-overflow-style": "none" } :
															{ "overflow-y": "hidden" }; a.zindex = "auto"; a.zindex = a.ispage || "auto" != a.opt.zindex ? a.opt.zindex : l() || "auto"; !a.ispage && "auto" != a.zindex && a.zindex > A && (A = a.zindex); a.isie && 0 == a.zindex && "auto" == a.opt.zindex && (a.zindex = "auto"); if (!a.ispage || !e.cantouch && !e.isieold && !e.isie9mobile) {
																var c = a.docscroll; a.ispage && (c = a.haswrapper ? a.win : a.doc); e.isie9mobile || a.css(c, b); a.ispage && e.isie7 && ("BODY" == a.doc[0].nodeName ? a.css(f("html"), { "overflow-y": "hidden" }) : "HTML" == a.doc[0].nodeName && a.css(f("body"), b)); !e.isios ||
																	a.ispage || a.haswrapper || a.css(f("body"), { "-webkit-overflow-scrolling": "touch" }); var d = f(document.createElement("div")); d.css({ position: "relative", top: 0, "float": "right", width: a.opt.cursorwidth, height: 0, "background-color": a.opt.cursorcolor, border: a.opt.cursorborder, "background-clip": "padding-box", "-webkit-border-radius": a.opt.cursorborderradius, "-moz-border-radius": a.opt.cursorborderradius, "border-radius": a.opt.cursorborderradius }); d.hborder = parseFloat(d.outerHeight() - d.innerHeight()); d.addClass("nicescroll-cursors");
																a.cursor = d; var m = f(document.createElement("div")); m.attr("id", a.id); m.addClass("nicescroll-rails nicescroll-rails-vr"); var k, h, p = ["left", "right", "top", "bottom"], L; for (L in p) h = p[L], (k = a.opt.railpadding[h]) ? m.css("padding-" + h, k + "px") : a.opt.railpadding[h] = 0; m.append(d); m.width = Math.max(parseFloat(a.opt.cursorwidth), d.outerWidth()); m.css({ width: m.width + "px", zIndex: a.zindex, background: a.opt.background, cursor: "default" }); m.visibility = !0; m.scrollable = !0; m.align = "left" == a.opt.railalign ? 0 : 1; a.rail = m; d = a.rail.drag =
																	!1; !a.opt.boxzoom || a.ispage || e.isieold || (d = document.createElement("div"), a.bind(d, "click", a.doZoom), a.bind(d, "mouseenter", function () { a.zoom.css("opacity", a.opt.cursoropacitymax) }), a.bind(d, "mouseleave", function () { a.zoom.css("opacity", a.opt.cursoropacitymin) }), a.zoom = f(d), a.zoom.css({ cursor: "pointer", zIndex: a.zindex, backgroundImage: "url(" + a.opt.scriptpath + "zoomico.png)", height: 18, width: 18, backgroundPosition: "0px 0px" }), a.opt.dblclickzoom && a.bind(a.win, "dblclick", a.doZoom), e.cantouch && a.opt.gesturezoom &&
																		(a.ongesturezoom = function (b) { 1.5 < b.scale && a.doZoomIn(b); .8 > b.scale && a.doZoomOut(b); return a.cancelEvent(b) }, a.bind(a.win, "gestureend", a.ongesturezoom))); a.railh = !1; var n; a.opt.horizrailenabled && (a.css(c, { overflowX: "hidden" }), d = f(document.createElement("div")), d.css({
																			position: "absolute", top: 0, height: a.opt.cursorwidth, width: 0, backgroundColor: a.opt.cursorcolor, border: a.opt.cursorborder, backgroundClip: "padding-box", "-webkit-border-radius": a.opt.cursorborderradius, "-moz-border-radius": a.opt.cursorborderradius,
																			"border-radius": a.opt.cursorborderradius
																		}), e.isieold && d.css("overflow", "hidden"), d.wborder = parseFloat(d.outerWidth() - d.innerWidth()), d.addClass("nicescroll-cursors"), a.cursorh = d, n = f(document.createElement("div")), n.attr("id", a.id + "-hr"), n.addClass("nicescroll-rails nicescroll-rails-hr"), n.height = Math.max(parseFloat(a.opt.cursorwidth), d.outerHeight()), n.css({ height: n.height + "px", zIndex: a.zindex, background: a.opt.background }), n.append(d), n.visibility = !0, n.scrollable = !0, n.align = "top" == a.opt.railvalign ?
																			0 : 1, a.railh = n, a.railh.drag = !1); a.ispage ? (m.css({ position: "fixed", top: 0, height: "100%" }), m.align ? m.css({ right: 0 }) : m.css({ left: 0 }), a.body.append(m), a.railh && (n.css({ position: "fixed", left: 0, width: "100%" }), n.align ? n.css({ bottom: 0 }) : n.css({ top: 0 }), a.body.append(n))) : (a.ishwscroll ? ("static" == a.win.css("position") && a.css(a.win, { position: "relative" }), c = "HTML" == a.win[0].nodeName ? a.body : a.win, f(c).scrollTop(0).scrollLeft(0), a.zoom && (a.zoom.css({ position: "absolute", top: 1, right: 0, "margin-right": m.width + 4 }), c.append(a.zoom)),
																				m.css({ position: "absolute", top: 0 }), m.align ? m.css({ right: 0 }) : m.css({ left: 0 }), c.append(m), n && (n.css({ position: "absolute", left: 0, bottom: 0 }), n.align ? n.css({ bottom: 0 }) : n.css({ top: 0 }), c.append(n))) : (a.isfixed = "fixed" == a.win.css("position"), c = a.isfixed ? "fixed" : "absolute", a.isfixed || (a.viewport = a.getViewport(a.win[0])), a.viewport && (a.body = a.viewport, 0 == /fixed|absolute/.test(a.viewport.css("position")) && a.css(a.viewport, { position: "relative" })), m.css({ position: c }), a.zoom && a.zoom.css({ position: c }), a.updateScrollBar(),
																					a.body.append(m), a.zoom && a.body.append(a.zoom), a.railh && (n.css({ position: c }), a.body.append(n))), e.isios && a.css(a.win, { "-webkit-tap-highlight-color": "rgba(0,0,0,0)", "-webkit-touch-callout": "none" }), e.isie && a.opt.disableoutline && a.win.attr("hideFocus", "true"), e.iswebkit && a.opt.disableoutline && a.win.css("outline", "none")); !1 === a.opt.autohidemode ? (a.autohidedom = !1, a.rail.css({ opacity: a.opt.cursoropacitymax }), a.railh && a.railh.css({ opacity: a.opt.cursoropacitymax })) : !0 === a.opt.autohidemode || "leave" === a.opt.autohidemode ?
																						(a.autohidedom = f().add(a.rail), e.isie8 && (a.autohidedom = a.autohidedom.add(a.cursor)), a.railh && (a.autohidedom = a.autohidedom.add(a.railh)), a.railh && e.isie8 && (a.autohidedom = a.autohidedom.add(a.cursorh))) : "scroll" == a.opt.autohidemode ? (a.autohidedom = f().add(a.rail), a.railh && (a.autohidedom = a.autohidedom.add(a.railh))) : "cursor" == a.opt.autohidemode ? (a.autohidedom = f().add(a.cursor), a.railh && (a.autohidedom = a.autohidedom.add(a.cursorh))) : "hidden" == a.opt.autohidemode && (a.autohidedom = !1, a.hide(), a.railslocked =
																							!1); if (e.isie9mobile) a.scrollmom = new M(a), a.onmangotouch = function () {
																								var b = a.getScrollTop(), c = a.getScrollLeft(); if (b == a.scrollmom.lastscrolly && c == a.scrollmom.lastscrollx) return !0; var g = b - a.mangotouch.sy, d = c - a.mangotouch.sx; if (0 != Math.round(Math.sqrt(Math.pow(d, 2) + Math.pow(g, 2)))) {
																									var e = 0 > g ? -1 : 1, f = 0 > d ? -1 : 1, u = +new Date; a.mangotouch.lazy && clearTimeout(a.mangotouch.lazy); 80 < u - a.mangotouch.tm || a.mangotouch.dry != e || a.mangotouch.drx != f ? (a.scrollmom.stop(), a.scrollmom.reset(c, b), a.mangotouch.sy = b, a.mangotouch.ly =
																										b, a.mangotouch.sx = c, a.mangotouch.lx = c, a.mangotouch.dry = e, a.mangotouch.drx = f, a.mangotouch.tm = u) : (a.scrollmom.stop(), a.scrollmom.update(a.mangotouch.sx - d, a.mangotouch.sy - g), a.mangotouch.tm = u, g = Math.max(Math.abs(a.mangotouch.ly - b), Math.abs(a.mangotouch.lx - c)), a.mangotouch.ly = b, a.mangotouch.lx = c, 2 < g && (a.mangotouch.lazy = setTimeout(function () { a.mangotouch.lazy = !1; a.mangotouch.dry = 0; a.mangotouch.drx = 0; a.mangotouch.tm = 0; a.scrollmom.doMomentum(30) }, 100)))
																								}
																							}, m = a.getScrollTop(), n = a.getScrollLeft(), a.mangotouch =
																								{ sy: m, ly: m, dry: 0, sx: n, lx: n, drx: 0, lazy: !1, tm: 0 }, a.bind(a.docscroll, "scroll", a.onmangotouch); else {
																	if (e.cantouch || a.istouchcapable || a.opt.touchbehavior || e.hasmstouch) {
																		a.scrollmom = new M(a); a.ontouchstart = function (b) {
																			if (b.pointerType && 2 != b.pointerType && "touch" != b.pointerType) return !1; a.hasmoving = !1; if (!a.railslocked) {
																				var c; if (e.hasmstouch) for (c = b.target ? b.target : !1; c;) {
																					var g = f(c).getNiceScroll(); if (0 < g.length && g[0].me == a.me) break; if (0 < g.length) return !1; if ("DIV" == c.nodeName && c.id == a.id) break; c = c.parentNode ?
																						c.parentNode : !1
																				} a.cancelScroll(); if ((c = a.getTarget(b)) && /INPUT/i.test(c.nodeName) && /range/i.test(c.type)) return a.stopPropagation(b); !("clientX" in b) && "changedTouches" in b && (b.clientX = b.changedTouches[0].clientX, b.clientY = b.changedTouches[0].clientY); a.forcescreen && (g = b, b = { original: b.original ? b.original : b }, b.clientX = g.screenX, b.clientY = g.screenY); a.rail.drag = { x: b.clientX, y: b.clientY, sx: a.scroll.x, sy: a.scroll.y, st: a.getScrollTop(), sl: a.getScrollLeft(), pt: 2, dl: !1 }; if (a.ispage || !a.opt.directionlockdeadzone) a.rail.drag.dl =
																					"f"; else { var g = f(window).width(), d = f(window).height(), d = Math.max(0, Math.max(document.body.scrollHeight, document.documentElement.scrollHeight) - d), g = Math.max(0, Math.max(document.body.scrollWidth, document.documentElement.scrollWidth) - g); a.rail.drag.ck = !a.rail.scrollable && a.railh.scrollable ? 0 < d ? "v" : !1 : a.rail.scrollable && !a.railh.scrollable ? 0 < g ? "h" : !1 : !1; a.rail.drag.ck || (a.rail.drag.dl = "f") } a.opt.touchbehavior && a.isiframe && e.isie && (g = a.win.position(), a.rail.drag.x += g.left, a.rail.drag.y += g.top); a.hasmoving =
																						!1; a.lastmouseup = !1; a.scrollmom.reset(b.clientX, b.clientY); if (!e.cantouch && !this.istouchcapable && !b.pointerType) { if (!c || !/INPUT|SELECT|TEXTAREA/i.test(c.nodeName)) return !a.ispage && e.hasmousecapture && c.setCapture(), a.opt.touchbehavior ? (c.onclick && !c._onclick && (c._onclick = c.onclick, c.onclick = function (b) { if (a.hasmoving) return !1; c._onclick.call(this, b) }), a.cancelEvent(b)) : a.stopPropagation(b); /SUBMIT|CANCEL|BUTTON/i.test(f(c).attr("type")) && (pc = { tg: c, click: !1 }, a.preventclick = pc) }
																			}
																		}; a.ontouchend = function (b) {
																			if (!a.rail.drag) return !0;
																			if (2 == a.rail.drag.pt) { if (b.pointerType && 2 != b.pointerType && "touch" != b.pointerType) return !1; a.scrollmom.doMomentum(); a.rail.drag = !1; if (a.hasmoving && (a.lastmouseup = !0, a.hideCursor(), e.hasmousecapture && document.releaseCapture(), !e.cantouch)) return a.cancelEvent(b) } else if (1 == a.rail.drag.pt) return a.onmouseup(b)
																		}; var q = a.opt.touchbehavior && a.isiframe && !e.hasmousecapture; a.ontouchmove = function (b, c) {
																			if (!a.rail.drag || b.targetTouches && a.opt.preventmultitouchscrolling && 1 < b.targetTouches.length || b.pointerType &&
																				2 != b.pointerType && "touch" != b.pointerType) return !1; if (2 == a.rail.drag.pt) {
																					if (e.cantouch && e.isios && void 0 === b.original) return !0; a.hasmoving = !0; a.preventclick && !a.preventclick.click && (a.preventclick.click = a.preventclick.tg.onclick || !1, a.preventclick.tg.onclick = a.onpreventclick); b = f.extend({ original: b }, b); "changedTouches" in b && (b.clientX = b.changedTouches[0].clientX, b.clientY = b.changedTouches[0].clientY); if (a.forcescreen) { var g = b; b = { original: b.original ? b.original : b }; b.clientX = g.screenX; b.clientY = g.screenY } var d,
																						g = d = 0; q && !c && (d = a.win.position(), g = -d.left, d = -d.top); var u = b.clientY + d; d = u - a.rail.drag.y; var m = b.clientX + g, k = m - a.rail.drag.x, h = a.rail.drag.st - d; a.ishwscroll && a.opt.bouncescroll ? 0 > h ? h = Math.round(h / 2) : h > a.page.maxh && (h = a.page.maxh + Math.round((h - a.page.maxh) / 2)) : (0 > h && (u = h = 0), h > a.page.maxh && (h = a.page.maxh, u = 0)); var l; a.railh && a.railh.scrollable && (l = a.isrtlmode ? k - a.rail.drag.sl : a.rail.drag.sl - k, a.ishwscroll && a.opt.bouncescroll ? 0 > l ? l = Math.round(l / 2) : l > a.page.maxw && (l = a.page.maxw + Math.round((l - a.page.maxw) /
																							2)) : (0 > l && (m = l = 0), l > a.page.maxw && (l = a.page.maxw, m = 0))); g = !1; if (a.rail.drag.dl) g = !0, "v" == a.rail.drag.dl ? l = a.rail.drag.sl : "h" == a.rail.drag.dl && (h = a.rail.drag.st); else { d = Math.abs(d); var k = Math.abs(k), C = a.opt.directionlockdeadzone; if ("v" == a.rail.drag.ck) { if (d > C && k <= .3 * d) return a.rail.drag = !1, !0; k > C && (a.rail.drag.dl = "f", f("body").scrollTop(f("body").scrollTop())) } else if ("h" == a.rail.drag.ck) { if (k > C && d <= .3 * k) return a.rail.drag = !1, !0; d > C && (a.rail.drag.dl = "f", f("body").scrollLeft(f("body").scrollLeft())) } } a.synched("touchmove",
																								function () { a.rail.drag && 2 == a.rail.drag.pt && (a.prepareTransition && a.prepareTransition(0), a.rail.scrollable && a.setScrollTop(h), a.scrollmom.update(m, u), a.railh && a.railh.scrollable ? (a.setScrollLeft(l), a.showCursor(h, l)) : a.showCursor(h), e.isie10 && document.selection.clear()) }); e.ischrome && a.istouchcapable && (g = !1); if (g) return a.cancelEvent(b)
																				} else if (1 == a.rail.drag.pt) return a.onmousemove(b)
																		}
																	} a.onmousedown = function (b, c) {
																		if (!a.rail.drag || 1 == a.rail.drag.pt) {
																			if (a.railslocked) return a.cancelEvent(b); a.cancelScroll();
																			a.rail.drag = { x: b.clientX, y: b.clientY, sx: a.scroll.x, sy: a.scroll.y, pt: 1, hr: !!c }; var g = a.getTarget(b); !a.ispage && e.hasmousecapture && g.setCapture(); a.isiframe && !e.hasmousecapture && (a.saved.csspointerevents = a.doc.css("pointer-events"), a.css(a.doc, { "pointer-events": "none" })); a.hasmoving = !1; return a.cancelEvent(b)
																		}
																	}; a.onmouseup = function (b) {
																		if (a.rail.drag) {
																			if (1 != a.rail.drag.pt) return !0; e.hasmousecapture && document.releaseCapture(); a.isiframe && !e.hasmousecapture && a.doc.css("pointer-events", a.saved.csspointerevents);
																			a.rail.drag = !1; a.hasmoving && a.triggerScrollEnd(); return a.cancelEvent(b)
																		}
																	}; a.onmousemove = function (b) {
																		if (a.rail.drag) {
																			if (1 == a.rail.drag.pt) {
																				if (e.ischrome && 0 == b.which) return a.onmouseup(b); a.cursorfreezed = !0; a.hasmoving = !0; if (a.rail.drag.hr) { a.scroll.x = a.rail.drag.sx + (b.clientX - a.rail.drag.x); 0 > a.scroll.x && (a.scroll.x = 0); var c = a.scrollvaluemaxw; a.scroll.x > c && (a.scroll.x = c) } else a.scroll.y = a.rail.drag.sy + (b.clientY - a.rail.drag.y), 0 > a.scroll.y && (a.scroll.y = 0), c = a.scrollvaluemax, a.scroll.y > c && (a.scroll.y =
																					c); a.synched("mousemove", function () { a.rail.drag && 1 == a.rail.drag.pt && (a.showCursor(), a.rail.drag.hr ? a.hasreversehr ? a.doScrollLeft(a.scrollvaluemaxw - Math.round(a.scroll.x * a.scrollratio.x), a.opt.cursordragspeed) : a.doScrollLeft(Math.round(a.scroll.x * a.scrollratio.x), a.opt.cursordragspeed) : a.doScrollTop(Math.round(a.scroll.y * a.scrollratio.y), a.opt.cursordragspeed)) }); return a.cancelEvent(b)
																			}
																		} else a.checkarea = 0
																	}; if (e.cantouch || a.opt.touchbehavior) a.onpreventclick = function (b) {
																		if (a.preventclick) return a.preventclick.tg.onclick =
																			a.preventclick.click, a.preventclick = !1, a.cancelEvent(b)
																	}, a.bind(a.win, "mousedown", a.ontouchstart), a.onclick = e.isios ? !1 : function (b) { return a.lastmouseup ? (a.lastmouseup = !1, a.cancelEvent(b)) : !0 }, a.opt.grabcursorenabled && e.cursorgrabvalue && (a.css(a.ispage ? a.doc : a.win, { cursor: e.cursorgrabvalue }), a.css(a.rail, { cursor: e.cursorgrabvalue })); else {
																		var r = function (b) {
																			if (a.selectiondrag) {
																				if (b) { var c = a.win.outerHeight(); b = b.pageY - a.selectiondrag.top; 0 < b && b < c && (b = 0); b >= c && (b -= c); a.selectiondrag.df = b } 0 != a.selectiondrag.df &&
																					(a.doScrollBy(2 * -Math.floor(a.selectiondrag.df / 6)), a.debounced("doselectionscroll", function () { r() }, 50))
																			}
																		}; a.hasTextSelected = "getSelection" in document ? function () { return 0 < document.getSelection().rangeCount } : "selection" in document ? function () { return "None" != document.selection.type } : function () { return !1 }; a.onselectionstart = function (b) { a.ispage || (a.selectiondrag = a.win.offset()) }; a.onselectionend = function (b) { a.selectiondrag = !1 }; a.onselectiondrag = function (b) {
																			a.selectiondrag && a.hasTextSelected() && a.debounced("selectionscroll",
																				function () { r(b) }, 250)
																		}
																	} e.hasw3ctouch ? (a.css(a.rail, { "touch-action": "none" }), a.css(a.cursor, { "touch-action": "none" }), a.bind(a.win, "pointerdown", a.ontouchstart), a.bind(document, "pointerup", a.ontouchend), a.bind(document, "pointermove", a.ontouchmove)) : e.hasmstouch ? (a.css(a.rail, { "-ms-touch-action": "none" }), a.css(a.cursor, { "-ms-touch-action": "none" }), a.bind(a.win, "MSPointerDown", a.ontouchstart), a.bind(document, "MSPointerUp", a.ontouchend), a.bind(document, "MSPointerMove", a.ontouchmove), a.bind(a.cursor, "MSGestureHold",
																		function (a) { a.preventDefault() }), a.bind(a.cursor, "contextmenu", function (a) { a.preventDefault() })) : this.istouchcapable && (a.bind(a.win, "touchstart", a.ontouchstart), a.bind(document, "touchend", a.ontouchend), a.bind(document, "touchcancel", a.ontouchend), a.bind(document, "touchmove", a.ontouchmove)); if (a.opt.cursordragontouch || !e.cantouch && !a.opt.touchbehavior) a.rail.css({ cursor: "default" }), a.railh && a.railh.css({ cursor: "default" }), a.jqbind(a.rail, "mouseenter", function () {
																			if (!a.ispage && !a.win.is(":visible")) return !1;
																			a.canshowonmouseevent && a.showCursor(); a.rail.active = !0
																		}), a.jqbind(a.rail, "mouseleave", function () { a.rail.active = !1; a.rail.drag || a.hideCursor() }), a.opt.sensitiverail && (a.bind(a.rail, "click", function (b) { a.doRailClick(b, !1, !1) }), a.bind(a.rail, "dblclick", function (b) { a.doRailClick(b, !0, !1) }), a.bind(a.cursor, "click", function (b) { a.cancelEvent(b) }), a.bind(a.cursor, "dblclick", function (b) { a.cancelEvent(b) })), a.railh && (a.jqbind(a.railh, "mouseenter", function () {
																			if (!a.ispage && !a.win.is(":visible")) return !1; a.canshowonmouseevent &&
																				a.showCursor(); a.rail.active = !0
																		}), a.jqbind(a.railh, "mouseleave", function () { a.rail.active = !1; a.rail.drag || a.hideCursor() }), a.opt.sensitiverail && (a.bind(a.railh, "click", function (b) { a.doRailClick(b, !1, !0) }), a.bind(a.railh, "dblclick", function (b) { a.doRailClick(b, !0, !0) }), a.bind(a.cursorh, "click", function (b) { a.cancelEvent(b) }), a.bind(a.cursorh, "dblclick", function (b) { a.cancelEvent(b) }))); e.cantouch || a.opt.touchbehavior ? (a.bind(e.hasmousecapture ? a.win : document, "mouseup", a.ontouchend), a.bind(document, "mousemove",
																			a.ontouchmove), a.onclick && a.bind(document, "click", a.onclick), a.opt.cursordragontouch ? (a.bind(a.cursor, "mousedown", a.onmousedown), a.bind(a.cursor, "mouseup", a.onmouseup), a.cursorh && a.bind(a.cursorh, "mousedown", function (b) { a.onmousedown(b, !0) }), a.cursorh && a.bind(a.cursorh, "mouseup", a.onmouseup)) : (a.bind(a.rail, "mousedown", function (a) { a.preventDefault() }), a.railh && a.bind(a.railh, "mousedown", function (a) { a.preventDefault() }))) : (a.bind(e.hasmousecapture ? a.win : document, "mouseup", a.onmouseup), a.bind(document,
																				"mousemove", a.onmousemove), a.onclick && a.bind(document, "click", a.onclick), a.bind(a.cursor, "mousedown", a.onmousedown), a.bind(a.cursor, "mouseup", a.onmouseup), a.railh && (a.bind(a.cursorh, "mousedown", function (b) { a.onmousedown(b, !0) }), a.bind(a.cursorh, "mouseup", a.onmouseup)), !a.ispage && a.opt.enablescrollonselection && (a.bind(a.win[0], "mousedown", a.onselectionstart), a.bind(document, "mouseup", a.onselectionend), a.bind(a.cursor, "mouseup", a.onselectionend), a.cursorh && a.bind(a.cursorh, "mouseup", a.onselectionend),
																					a.bind(document, "mousemove", a.onselectiondrag)), a.zoom && (a.jqbind(a.zoom, "mouseenter", function () { a.canshowonmouseevent && a.showCursor(); a.rail.active = !0 }), a.jqbind(a.zoom, "mouseleave", function () { a.rail.active = !1; a.rail.drag || a.hideCursor() }))); a.opt.enablemousewheel && (a.isiframe || a.mousewheel(e.isie && a.ispage ? document : a.win, a.onmousewheel), a.mousewheel(a.rail, a.onmousewheel), a.railh && a.mousewheel(a.railh, a.onmousewheelhr)); a.ispage || e.cantouch || /HTML|^BODY/.test(a.win[0].nodeName) || (a.win.attr("tabindex") ||
																						a.win.attr({ tabindex: O++ }), a.jqbind(a.win, "focus", function (b) { B = a.getTarget(b).id || !0; a.hasfocus = !0; a.canshowonmouseevent && a.noticeCursor() }), a.jqbind(a.win, "blur", function (b) { B = !1; a.hasfocus = !1 }), a.jqbind(a.win, "mouseenter", function (b) { F = a.getTarget(b).id || !0; a.hasmousefocus = !0; a.canshowonmouseevent && a.noticeCursor() }), a.jqbind(a.win, "mouseleave", function () { F = !1; a.hasmousefocus = !1; a.rail.drag || a.hideCursor() }))
																} a.onkeypress = function (b) {
																	if (a.railslocked && 0 == a.page.maxh) return !0; b = b ? b : window.e; var c =
																		a.getTarget(b); if (c && /INPUT|TEXTAREA|SELECT|OPTION/.test(c.nodeName) && (!c.getAttribute("type") && !c.type || !/submit|button|cancel/i.tp) || f(c).attr("contenteditable")) return !0; if (a.hasfocus || a.hasmousefocus && !B || a.ispage && !B && !F) {
																			c = b.keyCode; if (a.railslocked && 27 != c) return a.cancelEvent(b); var g = b.ctrlKey || !1, d = b.shiftKey || !1, e = !1; switch (c) {
																				case 38: case 63233: a.doScrollBy(72); e = !0; break; case 40: case 63235: a.doScrollBy(-72); e = !0; break; case 37: case 63232: a.railh && (g ? a.doScrollLeft(0) : a.doScrollLeftBy(72),
																					e = !0); break; case 39: case 63234: a.railh && (g ? a.doScrollLeft(a.page.maxw) : a.doScrollLeftBy(-72), e = !0); break; case 33: case 63276: a.doScrollBy(a.view.h); e = !0; break; case 34: case 63277: a.doScrollBy(-a.view.h); e = !0; break; case 36: case 63273: a.railh && g ? a.doScrollPos(0, 0) : a.doScrollTo(0); e = !0; break; case 35: case 63275: a.railh && g ? a.doScrollPos(a.page.maxw, a.page.maxh) : a.doScrollTo(a.page.maxh); e = !0; break; case 32: a.opt.spacebarenabled && (d ? a.doScrollBy(a.view.h) : a.doScrollBy(-a.view.h), e = !0); break; case 27: a.zoomactive &&
																						(a.doZoom(), e = !0)
																			}if (e) return a.cancelEvent(b)
																		}
																}; a.opt.enablekeyboard && a.bind(document, e.isopera && !e.isopera12 ? "keypress" : "keydown", a.onkeypress); a.bind(document, "keydown", function (b) { b.ctrlKey && (a.wheelprevented = !0) }); a.bind(document, "keyup", function (b) { b.ctrlKey || (a.wheelprevented = !1) }); a.bind(window, "blur", function (b) { a.wheelprevented = !1 }); a.bind(window, "resize", a.lazyResize); a.bind(window, "orientationchange", a.lazyResize); a.bind(window, "load", a.lazyResize); if (e.ischrome && !a.ispage && !a.haswrapper) {
																	var t =
																		a.win.attr("style"), m = parseFloat(a.win.css("width")) + 1; a.win.css("width", m); a.synched("chromefix", function () { a.win.attr("style", t) })
																} a.onAttributeChange = function (b) { a.lazyResize(a.isieold ? 250 : 30) }; a.isie11 || !1 === x || (a.observerbody = new x(function (b) { b.forEach(function (b) { if ("attributes" == b.type) return f("body").hasClass("modal-open") && f("body").hasClass("modal-dialog") && !f.contains(f(".modal-dialog")[0], a.doc[0]) ? a.hide() : a.show() }); if (document.body.scrollHeight != a.page.maxh) return a.lazyResize(30) }),
																	a.observerbody.observe(document.body, { childList: !0, subtree: !0, characterData: !1, attributes: !0, attributeFilter: ["class"] })); a.ispage || a.haswrapper || (!1 !== x ? (a.observer = new x(function (b) { b.forEach(a.onAttributeChange) }), a.observer.observe(a.win[0], { childList: !0, characterData: !1, attributes: !0, subtree: !1 }), a.observerremover = new x(function (b) { b.forEach(function (b) { if (0 < b.removedNodes.length) for (var c in b.removedNodes) if (a && b.removedNodes[c] == a.win[0]) return a.remove() }) }), a.observerremover.observe(a.win[0].parentNode,
																		{ childList: !0, characterData: !1, attributes: !1, subtree: !1 })) : (a.bind(a.win, e.isie && !e.isie9 ? "propertychange" : "DOMAttrModified", a.onAttributeChange), e.isie9 && a.win[0].attachEvent("onpropertychange", a.onAttributeChange), a.bind(a.win, "DOMNodeRemoved", function (b) { b.target == a.win[0] && a.remove() }))); !a.ispage && a.opt.boxzoom && a.bind(window, "resize", a.resizeZoom); a.istextarea && (a.bind(a.win, "keydown", a.lazyResize), a.bind(a.win, "mouseup", a.lazyResize)); a.lazyResize(30)
															} if ("IFRAME" == this.doc[0].nodeName) {
																var N =
																	function () {
																		a.iframexd = !1; var c; try { c = "contentDocument" in this ? this.contentDocument : this.contentWindow.document } catch (g) { a.iframexd = !0, c = !1 } if (a.iframexd) return "console" in window && console.log("NiceScroll error: policy restriced iframe"), !0; a.forcescreen = !0; a.isiframe && (a.iframe = { doc: f(c), html: a.doc.contents().find("html")[0], body: a.doc.contents().find("body")[0] }, a.getContentSize = function () {
																			return {
																				w: Math.max(a.iframe.html.scrollWidth, a.iframe.body.scrollWidth), h: Math.max(a.iframe.html.scrollHeight,
																					a.iframe.body.scrollHeight)
																			}
																		}, a.docscroll = f(a.iframe.body)); if (!e.isios && a.opt.iframeautoresize && !a.isiframe) { a.win.scrollTop(0); a.doc.height(""); var d = Math.max(c.getElementsByTagName("html")[0].scrollHeight, c.body.scrollHeight); a.doc.height(d) } a.lazyResize(30); e.isie7 && a.css(f(a.iframe.html), b); a.css(f(a.iframe.body), b); e.isios && a.haswrapper && a.css(f(c.body), { "-webkit-transform": "translate3d(0,0,0)" }); "contentWindow" in this ? a.bind(this.contentWindow, "scroll", a.onscroll) : a.bind(c, "scroll", a.onscroll);
																		a.opt.enablemousewheel && a.mousewheel(c, a.onmousewheel); a.opt.enablekeyboard && a.bind(c, e.isopera ? "keypress" : "keydown", a.onkeypress); if (e.cantouch || a.opt.touchbehavior) a.bind(c, "mousedown", a.ontouchstart), a.bind(c, "mousemove", function (b) { return a.ontouchmove(b, !0) }), a.opt.grabcursorenabled && e.cursorgrabvalue && a.css(f(c.body), { cursor: e.cursorgrabvalue }); a.bind(c, "mouseup", a.ontouchend); a.zoom && (a.opt.dblclickzoom && a.bind(c, "dblclick", a.doZoom), a.ongesturezoom && a.bind(c, "gestureend", a.ongesturezoom))
																	};
																this.doc[0].readyState && "complete" == this.doc[0].readyState && setTimeout(function () { N.call(a.doc[0], !1) }, 500); a.bind(this.doc, "load", N)
															}
													}; this.showCursor = function (b, c) {
														a.cursortimeout && (clearTimeout(a.cursortimeout), a.cursortimeout = 0); if (a.rail) {
															a.autohidedom && (a.autohidedom.stop().css({ opacity: a.opt.cursoropacitymax }), a.cursoractive = !0); a.rail.drag && 1 == a.rail.drag.pt || (void 0 !== b && !1 !== b && (a.scroll.y = Math.round(1 * b / a.scrollratio.y)), void 0 !== c && (a.scroll.x = Math.round(1 * c / a.scrollratio.x))); a.cursor.css({
																height: a.cursorheight,
																top: a.scroll.y
															}); if (a.cursorh) { var d = a.hasreversehr ? a.scrollvaluemaxw - a.scroll.x : a.scroll.x; !a.rail.align && a.rail.visibility ? a.cursorh.css({ width: a.cursorwidth, left: d + a.rail.width }) : a.cursorh.css({ width: a.cursorwidth, left: d }); a.cursoractive = !0 } a.zoom && a.zoom.stop().css({ opacity: a.opt.cursoropacitymax })
														}
													}; this.hideCursor = function (b) {
														a.cursortimeout || !a.rail || !a.autohidedom || a.hasmousefocus && "leave" == a.opt.autohidemode || (a.cursortimeout = setTimeout(function () {
															a.rail.active && a.showonmouseevent || (a.autohidedom.stop().animate({ opacity: a.opt.cursoropacitymin }),
																a.zoom && a.zoom.stop().animate({ opacity: a.opt.cursoropacitymin }), a.cursoractive = !1); a.cursortimeout = 0
														}, b || a.opt.hidecursordelay))
													}; this.noticeCursor = function (b, c, d) { a.showCursor(c, d); a.rail.active || a.hideCursor(b) }; this.getContentSize = a.ispage ? function () { return { w: Math.max(document.body.scrollWidth, document.documentElement.scrollWidth), h: Math.max(document.body.scrollHeight, document.documentElement.scrollHeight) } } : a.haswrapper ? function () {
														return {
															w: a.doc.outerWidth() + parseInt(a.win.css("paddingLeft")) +
																parseInt(a.win.css("paddingRight")), h: a.doc.outerHeight() + parseInt(a.win.css("paddingTop")) + parseInt(a.win.css("paddingBottom"))
														}
													} : function () { return { w: a.docscroll[0].scrollWidth, h: a.docscroll[0].scrollHeight } }; this.onResize = function (b, c) {
														if (!a || !a.win) return !1; if (!a.haswrapper && !a.ispage) { if ("none" == a.win.css("display")) return a.visibility && a.hideRail().hideRailHr(), !1; a.hidden || a.visibility || a.showRail().showRailHr() } var d = a.page.maxh, e = a.page.maxw, f = a.view.h, k = a.view.w; a.view = {
															w: a.ispage ? a.win.width() :
																parseInt(a.win[0].clientWidth), h: a.ispage ? a.win.height() : parseInt(a.win[0].clientHeight)
														}; a.page = c ? c : a.getContentSize(); a.page.maxh = Math.max(0, a.page.h - a.view.h); a.page.maxw = Math.max(0, a.page.w - a.view.w); if (a.page.maxh == d && a.page.maxw == e && a.view.w == k && a.view.h == f) { if (a.ispage) return a; d = a.win.offset(); if (a.lastposition && (e = a.lastposition, e.top == d.top && e.left == d.left)) return a; a.lastposition = d } 0 == a.page.maxh ? (a.hideRail(), a.scrollvaluemax = 0, a.scroll.y = 0, a.scrollratio.y = 0, a.cursorheight = 0, a.setScrollTop(0),
															a.rail && (a.rail.scrollable = !1)) : (a.page.maxh -= a.opt.railpadding.top + a.opt.railpadding.bottom, a.rail.scrollable = !0); 0 == a.page.maxw ? (a.hideRailHr(), a.scrollvaluemaxw = 0, a.scroll.x = 0, a.scrollratio.x = 0, a.cursorwidth = 0, a.setScrollLeft(0), a.railh && (a.railh.scrollable = !1)) : (a.page.maxw -= a.opt.railpadding.left + a.opt.railpadding.right, a.railh && (a.railh.scrollable = a.opt.horizrailenabled)); a.railslocked = a.locked || 0 == a.page.maxh && 0 == a.page.maxw; if (a.railslocked) return a.ispage || a.updateScrollBar(a.view), !1; a.hidden ||
																a.visibility ? !a.railh || a.hidden || a.railh.visibility || a.showRailHr() : a.showRail().showRailHr(); a.istextarea && a.win.css("resize") && "none" != a.win.css("resize") && (a.view.h -= 20); a.cursorheight = Math.min(a.view.h, Math.round(a.view.h / a.page.h * a.view.h)); a.cursorheight = a.opt.cursorfixedheight ? a.opt.cursorfixedheight : Math.max(a.opt.cursorminheight, a.cursorheight); a.cursorwidth = Math.min(a.view.w, Math.round(a.view.w / a.page.w * a.view.w)); a.cursorwidth = a.opt.cursorfixedheight ? a.opt.cursorfixedheight : Math.max(a.opt.cursorminheight,
																	a.cursorwidth); a.scrollvaluemax = a.view.h - a.cursorheight - a.cursor.hborder - (a.opt.railpadding.top + a.opt.railpadding.bottom); a.railh && (a.railh.width = 0 < a.page.maxh ? a.view.w - a.rail.width : a.view.w, a.scrollvaluemaxw = a.railh.width - a.cursorwidth - a.cursorh.wborder - (a.opt.railpadding.left + a.opt.railpadding.right)); a.ispage || a.updateScrollBar(a.view); a.scrollratio = { x: a.page.maxw / a.scrollvaluemaxw, y: a.page.maxh / a.scrollvaluemax }; a.getScrollTop() > a.page.maxh ? a.doScrollTop(a.page.maxh) : (a.scroll.y = Math.round(a.getScrollTop() *
																		(1 / a.scrollratio.y)), a.scroll.x = Math.round(a.getScrollLeft() * (1 / a.scrollratio.x)), a.cursoractive && a.noticeCursor()); a.scroll.y && 0 == a.getScrollTop() && a.doScrollTo(Math.floor(a.scroll.y * a.scrollratio.y)); return a
													}; this.resize = a.onResize; this.hlazyresize = 0; this.lazyResize = function (b) { a.haswrapper || a.hide(); a.hlazyresize && clearTimeout(a.hlazyresize); a.hlazyresize = setTimeout(function () { a && a.show().resize() }, 240); return a }; this.jqbind = function (b, c, d) { a.events.push({ e: b, n: c, f: d, q: !0 }); f(b).bind(c, d) }; this.mousewheel =
														function (b, c, d) { b = "jquery" in b ? b[0] : b; if ("onwheel" in document.createElement("div")) a._bind(b, "wheel", c, d || !1); else { var e = void 0 !== document.onmousewheel ? "mousewheel" : "DOMMouseScroll"; q(b, e, c, d || !1); "DOMMouseScroll" == e && q(b, "MozMousePixelScroll", c, d || !1) } }; e.haseventlistener ? (this.bind = function (b, c, d, e) { a._bind("jquery" in b ? b[0] : b, c, d, e || !1) }, this._bind = function (b, c, d, e) { a.events.push({ e: b, n: c, f: d, b: e, q: !1 }); b.addEventListener(c, d, e || !1) }, this.cancelEvent = function (a) {
															if (!a) return !1; a = a.original ? a.original :
																a; a.cancelable && a.preventDefault(); a.stopPropagation(); a.preventManipulation && a.preventManipulation(); return !1
														}, this.stopPropagation = function (a) { if (!a) return !1; a = a.original ? a.original : a; a.stopPropagation(); return !1 }, this._unbind = function (a, c, d, e) { a.removeEventListener(c, d, e) }) : (this.bind = function (b, c, d, e) {
															var f = "jquery" in b ? b[0] : b; a._bind(f, c, function (b) {
																(b = b || window.event || !1) && b.srcElement && (b.target = b.srcElement); "pageY" in b || (b.pageX = b.clientX + document.documentElement.scrollLeft, b.pageY = b.clientY +
																	document.documentElement.scrollTop); return !1 === d.call(f, b) || !1 === e ? a.cancelEvent(b) : !0
															})
														}, this._bind = function (b, c, d, e) { a.events.push({ e: b, n: c, f: d, b: e, q: !1 }); b.attachEvent ? b.attachEvent("on" + c, d) : b["on" + c] = d }, this.cancelEvent = function (a) { a = window.event || !1; if (!a) return !1; a.cancelBubble = !0; a.cancel = !0; return a.returnValue = !1 }, this.stopPropagation = function (a) { a = window.event || !1; if (!a) return !1; a.cancelBubble = !0; return !1 }, this._unbind = function (a, c, d, e) { a.detachEvent ? a.detachEvent("on" + c, d) : a["on" + c] = !1 });
				this.unbindAll = function () { for (var b = 0; b < a.events.length; b++) { var c = a.events[b]; c.q ? c.e.unbind(c.n, c.f) : a._unbind(c.e, c.n, c.f, c.b) } }; this.showRail = function () { 0 == a.page.maxh || !a.ispage && "none" == a.win.css("display") || (a.visibility = !0, a.rail.visibility = !0, a.rail.css("display", "block")); return a }; this.showRailHr = function () { if (!a.railh) return a; 0 == a.page.maxw || !a.ispage && "none" == a.win.css("display") || (a.railh.visibility = !0, a.railh.css("display", "block")); return a }; this.hideRail = function () {
					a.visibility =
						!1; a.rail.visibility = !1; a.rail.css("display", "none"); return a
				}; this.hideRailHr = function () { if (!a.railh) return a; a.railh.visibility = !1; a.railh.css("display", "none"); return a }; this.show = function () { a.hidden = !1; a.railslocked = !1; return a.showRail().showRailHr() }; this.hide = function () { a.hidden = !0; a.railslocked = !0; return a.hideRail().hideRailHr() }; this.toggle = function () { return a.hidden ? a.show() : a.hide() }; this.remove = function () {
					a.stop(); a.cursortimeout && clearTimeout(a.cursortimeout); for (var b in a.delaylist) a.delaylist[b] &&
						w(a.delaylist[b].h); a.doZoomOut(); a.unbindAll(); e.isie9 && a.win[0].detachEvent("onpropertychange", a.onAttributeChange); !1 !== a.observer && a.observer.disconnect(); !1 !== a.observerremover && a.observerremover.disconnect(); !1 !== a.observerbody && a.observerbody.disconnect(); a.events = null; a.cursor && a.cursor.remove(); a.cursorh && a.cursorh.remove(); a.rail && a.rail.remove(); a.railh && a.railh.remove(); a.zoom && a.zoom.remove(); for (b = 0; b < a.saved.css.length; b++) { var c = a.saved.css[b]; c[0].css(c[1], void 0 === c[2] ? "" : c[2]) } a.saved =
							!1; a.me.data("__nicescroll", ""); var d = f.nicescroll; d.each(function (b) { if (this && this.id === a.id) { delete d[b]; for (var c = ++b; c < d.length; c++, b++)d[b] = d[c]; d.length--; d.length && delete d[d.length] } }); for (var k in a) a[k] = null, delete a[k]; a = null
				}; this.scrollstart = function (b) { this.onscrollstart = b; return a }; this.scrollend = function (b) { this.onscrollend = b; return a }; this.scrollcancel = function (b) { this.onscrollcancel = b; return a }; this.zoomin = function (b) { this.onzoomin = b; return a }; this.zoomout = function (b) {
					this.onzoomout =
						b; return a
				}; this.isScrollable = function (a) { a = a.target ? a.target : a; if ("OPTION" == a.nodeName) return !0; for (; a && 1 == a.nodeType && !/^BODY|HTML/.test(a.nodeName);) { var c = f(a), c = c.css("overflowY") || c.css("overflowX") || c.css("overflow") || ""; if (/scroll|auto/.test(c)) return a.clientHeight != a.scrollHeight; a = a.parentNode ? a.parentNode : !1 } return !1 }; this.getViewport = function (a) {
					for (a = a && a.parentNode ? a.parentNode : !1; a && 1 == a.nodeType && !/^BODY|HTML/.test(a.nodeName);) {
						var c = f(a); if (/fixed|absolute/.test(c.css("position"))) return c;
						var d = c.css("overflowY") || c.css("overflowX") || c.css("overflow") || ""; if (/scroll|auto/.test(d) && a.clientHeight != a.scrollHeight || 0 < c.getNiceScroll().length) return c; a = a.parentNode ? a.parentNode : !1
					} return !1
				}; this.triggerScrollEnd = function () { if (a.onscrollend) { var b = a.getScrollLeft(), c = a.getScrollTop(); a.onscrollend.call(a, { type: "scrollend", current: { x: b, y: c }, end: { x: b, y: c } }) } }; this.onmousewheel = function (b) {
					if (!a.wheelprevented) {
						if (a.railslocked) return a.debounced("checkunlock", a.resize, 250), !0; if (a.rail.drag) return a.cancelEvent(b);
						"auto" == a.opt.oneaxismousemode && 0 != b.deltaX && (a.opt.oneaxismousemode = !1); if (a.opt.oneaxismousemode && 0 == b.deltaX && !a.rail.scrollable) return a.railh && a.railh.scrollable ? a.onmousewheelhr(b) : !0; var c = +new Date, d = !1; a.opt.preservenativescrolling && a.checkarea + 600 < c && (a.nativescrollingarea = a.isScrollable(b), d = !0); a.checkarea = c; if (a.nativescrollingarea) return !0; if (b = t(b, !1, d)) a.checkarea = 0; return b
					}
				}; this.onmousewheelhr = function (b) {
					if (!a.wheelprevented) {
						if (a.railslocked || !a.railh.scrollable) return !0; if (a.rail.drag) return a.cancelEvent(b);
						var c = +new Date, d = !1; a.opt.preservenativescrolling && a.checkarea + 600 < c && (a.nativescrollingarea = a.isScrollable(b), d = !0); a.checkarea = c; return a.nativescrollingarea ? !0 : a.railslocked ? a.cancelEvent(b) : t(b, !0, d)
					}
				}; this.stop = function () { a.cancelScroll(); a.scrollmon && a.scrollmon.stop(); a.cursorfreezed = !1; a.scroll.y = Math.round(a.getScrollTop() * (1 / a.scrollratio.y)); a.noticeCursor(); return a }; this.getTransitionSpeed = function (b) {
					b = Math.min(Math.round(10 * a.opt.scrollspeed), Math.round(b / 20 * a.opt.scrollspeed)); return 20 <
						b ? b : 0
				}; a.opt.smoothscroll ? a.ishwscroll && e.hastransition && a.opt.usetransition && a.opt.smoothscroll ? (this.prepareTransition = function (b, c) { var d = c ? 20 < b ? b : 0 : a.getTransitionSpeed(b), f = d ? e.prefixstyle + "transform " + d + "ms ease-out" : ""; a.lasttransitionstyle && a.lasttransitionstyle == f || (a.lasttransitionstyle = f, a.doc.css(e.transitionstyle, f)); return d }, this.doScrollLeft = function (b, c) { var d = a.scrollrunning ? a.newscrolly : a.getScrollTop(); a.doScrollPos(b, d, c) }, this.doScrollTop = function (b, c) {
					var d = a.scrollrunning ?
						a.newscrollx : a.getScrollLeft(); a.doScrollPos(d, b, c)
				}, this.doScrollPos = function (b, c, d) {
					var f = a.getScrollTop(), k = a.getScrollLeft(); (0 > (a.newscrolly - f) * (c - f) || 0 > (a.newscrollx - k) * (b - k)) && a.cancelScroll(); 0 == a.opt.bouncescroll && (0 > c ? c = 0 : c > a.page.maxh && (c = a.page.maxh), 0 > b ? b = 0 : b > a.page.maxw && (b = a.page.maxw)); if (a.scrollrunning && b == a.newscrollx && c == a.newscrolly) return !1; a.newscrolly = c; a.newscrollx = b; a.newscrollspeed = d || !1; if (a.timer) return !1; a.timer = setTimeout(function () {
						var d = a.getScrollTop(), f = a.getScrollLeft(),
							k = Math.round(Math.sqrt(Math.pow(b - f, 2) + Math.pow(c - d, 2))), k = a.newscrollspeed && 1 < a.newscrollspeed ? a.newscrollspeed : a.getTransitionSpeed(k); a.newscrollspeed && 1 >= a.newscrollspeed && (k *= a.newscrollspeed); a.prepareTransition(k, !0); a.timerscroll && a.timerscroll.tm && clearInterval(a.timerscroll.tm); 0 < k && (!a.scrollrunning && a.onscrollstart && a.onscrollstart.call(a, { type: "scrollstart", current: { x: f, y: d }, request: { x: b, y: c }, end: { x: a.newscrollx, y: a.newscrolly }, speed: k }), e.transitionend ? a.scrollendtrapped || (a.scrollendtrapped =
								!0, a.bind(a.doc, e.transitionend, a.onScrollTransitionEnd, !1)) : (a.scrollendtrapped && clearTimeout(a.scrollendtrapped), a.scrollendtrapped = setTimeout(a.onScrollTransitionEnd, k)), a.timerscroll = { bz: new D(d, a.newscrolly, k, 0, 0, .58, 1), bh: new D(f, a.newscrollx, k, 0, 0, .58, 1) }, a.cursorfreezed || (a.timerscroll.tm = setInterval(function () { a.showCursor(a.getScrollTop(), a.getScrollLeft()) }, 60))); a.synched("doScroll-set", function () {
									a.timer = 0; a.scrollendtrapped && (a.scrollrunning = !0); a.setScrollTop(a.newscrolly); a.setScrollLeft(a.newscrollx);
									if (!a.scrollendtrapped) a.onScrollTransitionEnd()
								})
					}, 50)
				}, this.cancelScroll = function () { if (!a.scrollendtrapped) return !0; var b = a.getScrollTop(), c = a.getScrollLeft(); a.scrollrunning = !1; e.transitionend || clearTimeout(e.transitionend); a.scrollendtrapped = !1; a._unbind(a.doc[0], e.transitionend, a.onScrollTransitionEnd); a.prepareTransition(0); a.setScrollTop(b); a.railh && a.setScrollLeft(c); a.timerscroll && a.timerscroll.tm && clearInterval(a.timerscroll.tm); a.timerscroll = !1; a.cursorfreezed = !1; a.showCursor(b, c); return a },
					this.onScrollTransitionEnd = function () {
						a.scrollendtrapped && a._unbind(a.doc[0], e.transitionend, a.onScrollTransitionEnd); a.scrollendtrapped = !1; a.prepareTransition(0); a.timerscroll && a.timerscroll.tm && clearInterval(a.timerscroll.tm); a.timerscroll = !1; var b = a.getScrollTop(), c = a.getScrollLeft(); a.setScrollTop(b); a.railh && a.setScrollLeft(c); a.noticeCursor(!1, b, c); a.cursorfreezed = !1; 0 > b ? b = 0 : b > a.page.maxh && (b = a.page.maxh); 0 > c ? c = 0 : c > a.page.maxw && (c = a.page.maxw); if (b != a.newscrolly || c != a.newscrollx) return a.doScrollPos(c,
							b, a.opt.snapbackspeed); a.onscrollend && a.scrollrunning && a.triggerScrollEnd(); a.scrollrunning = !1
					}) : (this.doScrollLeft = function (b, c) { var d = a.scrollrunning ? a.newscrolly : a.getScrollTop(); a.doScrollPos(b, d, c) }, this.doScrollTop = function (b, c) { var d = a.scrollrunning ? a.newscrollx : a.getScrollLeft(); a.doScrollPos(d, b, c) }, this.doScrollPos = function (b, c, d) {
						function e() {
							if (a.cancelAnimationFrame) return !0; a.scrollrunning = !0; if (p = 1 - p) return a.timer = v(e) || 1; var b = 0, c, d, f = d = a.getScrollTop(); if (a.dst.ay) {
								f = a.bzscroll ?
									a.dst.py + a.bzscroll.getNow() * a.dst.ay : a.newscrolly; c = f - d; if (0 > c && f < a.newscrolly || 0 < c && f > a.newscrolly) f = a.newscrolly; a.setScrollTop(f); f == a.newscrolly && (b = 1)
							} else b = 1; d = c = a.getScrollLeft(); if (a.dst.ax) { d = a.bzscroll ? a.dst.px + a.bzscroll.getNow() * a.dst.ax : a.newscrollx; c = d - c; if (0 > c && d < a.newscrollx || 0 < c && d > a.newscrollx) d = a.newscrollx; a.setScrollLeft(d); d == a.newscrollx && (b += 1) } else b += 1; 2 == b ? (a.timer = 0, a.cursorfreezed = !1, a.bzscroll = !1, a.scrollrunning = !1, 0 > f ? f = 0 : f > a.page.maxh && (f = Math.max(0, a.page.maxh)),
								0 > d ? d = 0 : d > a.page.maxw && (d = a.page.maxw), d != a.newscrollx || f != a.newscrolly ? a.doScrollPos(d, f) : a.onscrollend && a.triggerScrollEnd()) : a.timer = v(e) || 1
						} c = void 0 === c || !1 === c ? a.getScrollTop(!0) : c; if (a.timer && a.newscrolly == c && a.newscrollx == b) return !0; a.timer && w(a.timer); a.timer = 0; var f = a.getScrollTop(), k = a.getScrollLeft(); (0 > (a.newscrolly - f) * (c - f) || 0 > (a.newscrollx - k) * (b - k)) && a.cancelScroll(); a.newscrolly = c; a.newscrollx = b; a.bouncescroll && a.rail.visibility || (0 > a.newscrolly ? a.newscrolly = 0 : a.newscrolly > a.page.maxh &&
							(a.newscrolly = a.page.maxh)); a.bouncescroll && a.railh.visibility || (0 > a.newscrollx ? a.newscrollx = 0 : a.newscrollx > a.page.maxw && (a.newscrollx = a.page.maxw)); a.dst = {}; a.dst.x = b - k; a.dst.y = c - f; a.dst.px = k; a.dst.py = f; var h = Math.round(Math.sqrt(Math.pow(a.dst.x, 2) + Math.pow(a.dst.y, 2))); a.dst.ax = a.dst.x / h; a.dst.ay = a.dst.y / h; var l = 0, n = h; 0 == a.dst.x ? (l = f, n = c, a.dst.ay = 1, a.dst.py = 0) : 0 == a.dst.y && (l = k, n = b, a.dst.ax = 1, a.dst.px = 0); h = a.getTransitionSpeed(h); d && 1 >= d && (h *= d); a.bzscroll = 0 < h ? a.bzscroll ? a.bzscroll.update(n, h) :
								new D(l, n, h, 0, 1, 0, 1) : !1; if (!a.timer) { (f == a.page.maxh && c >= a.page.maxh || k == a.page.maxw && b >= a.page.maxw) && a.checkContentSize(); var p = 1; a.cancelAnimationFrame = !1; a.timer = 1; a.onscrollstart && !a.scrollrunning && a.onscrollstart.call(a, { type: "scrollstart", current: { x: k, y: f }, request: { x: b, y: c }, end: { x: a.newscrollx, y: a.newscrolly }, speed: h }); e(); (f == a.page.maxh && c >= f || k == a.page.maxw && b >= k) && a.checkContentSize(); a.noticeCursor() }
					}, this.cancelScroll = function () {
						a.timer && w(a.timer); a.timer = 0; a.bzscroll = !1; a.scrollrunning =
							!1; return a
					}) : (this.doScrollLeft = function (b, c) { var d = a.getScrollTop(); a.doScrollPos(b, d, c) }, this.doScrollTop = function (b, c) { var d = a.getScrollLeft(); a.doScrollPos(d, b, c) }, this.doScrollPos = function (b, c, d) { var e = b > a.page.maxw ? a.page.maxw : b; 0 > e && (e = 0); var f = c > a.page.maxh ? a.page.maxh : c; 0 > f && (f = 0); a.synched("scroll", function () { a.setScrollTop(f); a.setScrollLeft(e) }) }, this.cancelScroll = function () { }); this.doScrollBy = function (b, c) {
						var d = 0, d = c ? Math.floor((a.scroll.y - b) * a.scrollratio.y) : (a.timer ? a.newscrolly :
							a.getScrollTop(!0)) - b; if (a.bouncescroll) { var e = Math.round(a.view.h / 2); d < -e ? d = -e : d > a.page.maxh + e && (d = a.page.maxh + e) } a.cursorfreezed = !1; e = a.getScrollTop(!0); if (0 > d && 0 >= e) return a.noticeCursor(); if (d > a.page.maxh && e >= a.page.maxh) return a.checkContentSize(), a.noticeCursor(); a.doScrollTop(d)
					}; this.doScrollLeftBy = function (b, c) {
						var d = 0, d = c ? Math.floor((a.scroll.x - b) * a.scrollratio.x) : (a.timer ? a.newscrollx : a.getScrollLeft(!0)) - b; if (a.bouncescroll) {
							var e = Math.round(a.view.w / 2); d < -e ? d = -e : d > a.page.maxw + e && (d = a.page.maxw +
								e)
						} a.cursorfreezed = !1; e = a.getScrollLeft(!0); if (0 > d && 0 >= e || d > a.page.maxw && e >= a.page.maxw) return a.noticeCursor(); a.doScrollLeft(d)
					}; this.doScrollTo = function (b, c) { a.cursorfreezed = !1; a.doScrollTop(b) }; this.checkContentSize = function () { var b = a.getContentSize(); b.h == a.page.h && b.w == a.page.w || a.resize(!1, b) }; a.onscroll = function (b) {
						a.rail.drag || a.cursorfreezed || a.synched("scroll", function () {
							a.scroll.y = Math.round(a.getScrollTop() * (1 / a.scrollratio.y)); a.railh && (a.scroll.x = Math.round(a.getScrollLeft() * (1 / a.scrollratio.x)));
							a.noticeCursor()
						})
					}; a.bind(a.docscroll, "scroll", a.onscroll); this.doZoomIn = function (b) {
						if (!a.zoomactive) {
							a.zoomactive = !0; a.zoomrestore = { style: {} }; var c = "position top left zIndex backgroundColor marginTop marginBottom marginLeft marginRight".split(" "), d = a.win[0].style, k; for (k in c) { var h = c[k]; a.zoomrestore.style[h] = void 0 !== d[h] ? d[h] : "" } a.zoomrestore.style.width = a.win.css("width"); a.zoomrestore.style.height = a.win.css("height"); a.zoomrestore.padding = {
								w: a.win.outerWidth() - a.win.width(), h: a.win.outerHeight() -
									a.win.height()
							}; e.isios4 && (a.zoomrestore.scrollTop = f(window).scrollTop(), f(window).scrollTop(0)); a.win.css({ position: e.isios4 ? "absolute" : "fixed", top: 0, left: 0, zIndex: A + 100, margin: 0 }); c = a.win.css("backgroundColor"); ("" == c || /transparent|rgba\(0, 0, 0, 0\)|rgba\(0,0,0,0\)/.test(c)) && a.win.css("backgroundColor", "#fff"); a.rail.css({ zIndex: A + 101 }); a.zoom.css({ zIndex: A + 102 }); a.zoom.css("backgroundPosition", "0px -18px"); a.resizeZoom(); a.onzoomin && a.onzoomin.call(a); return a.cancelEvent(b)
						}
					}; this.doZoomOut =
						function (b) { if (a.zoomactive) return a.zoomactive = !1, a.win.css("margin", ""), a.win.css(a.zoomrestore.style), e.isios4 && f(window).scrollTop(a.zoomrestore.scrollTop), a.rail.css({ "z-index": a.zindex }), a.zoom.css({ "z-index": a.zindex }), a.zoomrestore = !1, a.zoom.css("backgroundPosition", "0px 0px"), a.onResize(), a.onzoomout && a.onzoomout.call(a), a.cancelEvent(b) }; this.doZoom = function (b) { return a.zoomactive ? a.doZoomOut(b) : a.doZoomIn(b) }; this.resizeZoom = function () {
							if (a.zoomactive) {
								var b = a.getScrollTop(); a.win.css({
									width: f(window).width() -
										a.zoomrestore.padding.w + "px", height: f(window).height() - a.zoomrestore.padding.h + "px"
								}); a.onResize(); a.setScrollTop(Math.min(a.page.maxh, b))
							}
						}; this.init(); f.nicescroll.push(this)
			}, M = function (f) {
				var c = this; this.nc = f; this.steptime = this.lasttime = this.speedy = this.speedx = this.lasty = this.lastx = 0; this.snapy = this.snapx = !1; this.demuly = this.demulx = 0; this.lastscrolly = this.lastscrollx = -1; this.timer = this.chky = this.chkx = 0; this.time = function () { return +new Date }; this.reset = function (f, h) {
					c.stop(); var d = c.time(); c.steptime =
						0; c.lasttime = d; c.speedx = 0; c.speedy = 0; c.lastx = f; c.lasty = h; c.lastscrollx = -1; c.lastscrolly = -1
				}; this.update = function (f, h) { var d = c.time(); c.steptime = d - c.lasttime; c.lasttime = d; var d = h - c.lasty, q = f - c.lastx, t = c.nc.getScrollTop(), a = c.nc.getScrollLeft(), t = t + d, a = a + q; c.snapx = 0 > a || a > c.nc.page.maxw; c.snapy = 0 > t || t > c.nc.page.maxh; c.speedx = q; c.speedy = d; c.lastx = f; c.lasty = h }; this.stop = function () { c.nc.unsynched("domomentum2d"); c.timer && clearTimeout(c.timer); c.timer = 0; c.lastscrollx = -1; c.lastscrolly = -1 }; this.doSnapy = function (f,
					h) { var d = !1; 0 > h ? (h = 0, d = !0) : h > c.nc.page.maxh && (h = c.nc.page.maxh, d = !0); 0 > f ? (f = 0, d = !0) : f > c.nc.page.maxw && (f = c.nc.page.maxw, d = !0); d ? c.nc.doScrollPos(f, h, c.nc.opt.snapbackspeed) : c.nc.triggerScrollEnd() }; this.doMomentum = function (f) {
						var h = c.time(), d = f ? h + f : c.lasttime; f = c.nc.getScrollLeft(); var q = c.nc.getScrollTop(), t = c.nc.page.maxh, a = c.nc.page.maxw; c.speedx = 0 < a ? Math.min(60, c.speedx) : 0; c.speedy = 0 < t ? Math.min(60, c.speedy) : 0; d = d && 60 >= h - d; if (0 > q || q > t || 0 > f || f > a) d = !1; f = c.speedx && d ? c.speedx : !1; if (c.speedy && d && c.speedy ||
							f) {
							var r = Math.max(16, c.steptime); 50 < r && (f = r / 50, c.speedx *= f, c.speedy *= f, r = 50); c.demulxy = 0; c.lastscrollx = c.nc.getScrollLeft(); c.chkx = c.lastscrollx; c.lastscrolly = c.nc.getScrollTop(); c.chky = c.lastscrolly; var p = c.lastscrollx, e = c.lastscrolly, v = function () {
								var d = 600 < c.time() - h ? .04 : .02; c.speedx && (p = Math.floor(c.lastscrollx - c.speedx * (1 - c.demulxy)), c.lastscrollx = p, 0 > p || p > a) && (d = .1); c.speedy && (e = Math.floor(c.lastscrolly - c.speedy * (1 - c.demulxy)), c.lastscrolly = e, 0 > e || e > t) && (d = .1); c.demulxy = Math.min(1, c.demulxy +
									d); c.nc.synched("domomentum2d", function () { c.speedx && (c.nc.getScrollLeft(), c.chkx = p, c.nc.setScrollLeft(p)); c.speedy && (c.nc.getScrollTop(), c.chky = e, c.nc.setScrollTop(e)); c.timer || (c.nc.hideCursor(), c.doSnapy(p, e)) }); 1 > c.demulxy ? c.timer = setTimeout(v, r) : (c.stop(), c.nc.hideCursor(), c.doSnapy(p, e))
							}; v()
						} else c.doSnapy(c.nc.getScrollLeft(), c.nc.getScrollTop())
					}
			}, y = f.fn.scrollTop; f.cssHooks.pageYOffset = {
				get: function (h, c, k) { return (c = f.data(h, "__nicescroll") || !1) && c.ishwscroll ? c.getScrollTop() : y.call(h) }, set: function (h,
					c) { var k = f.data(h, "__nicescroll") || !1; k && k.ishwscroll ? k.setScrollTop(parseInt(c)) : y.call(h, c); return this }
			}; f.fn.scrollTop = function (h) { if (void 0 === h) { var c = this[0] ? f.data(this[0], "__nicescroll") || !1 : !1; return c && c.ishwscroll ? c.getScrollTop() : y.call(this) } return this.each(function () { var c = f.data(this, "__nicescroll") || !1; c && c.ishwscroll ? c.setScrollTop(parseInt(h)) : y.call(f(this), h) }) }; var z = f.fn.scrollLeft; f.cssHooks.pageXOffset = {
				get: function (h, c, k) {
					return (c = f.data(h, "__nicescroll") || !1) && c.ishwscroll ?
						c.getScrollLeft() : z.call(h)
				}, set: function (h, c) { var k = f.data(h, "__nicescroll") || !1; k && k.ishwscroll ? k.setScrollLeft(parseInt(c)) : z.call(h, c); return this }
			}; f.fn.scrollLeft = function (h) { if (void 0 === h) { var c = this[0] ? f.data(this[0], "__nicescroll") || !1 : !1; return c && c.ishwscroll ? c.getScrollLeft() : z.call(this) } return this.each(function () { var c = f.data(this, "__nicescroll") || !1; c && c.ishwscroll ? c.setScrollLeft(parseInt(h)) : z.call(f(this), h) }) }; var E = function (h) {
				var c = this; this.length = 0; this.name = "nicescrollarray";
				this.each = function (d) { f.each(c, d); return c }; this.push = function (d) { c[c.length] = d; c.length++ }; this.eq = function (d) { return c[d] }; if (h) for (var k = 0; k < h.length; k++) { var l = f.data(h[k], "__nicescroll") || !1; l && (this[this.length] = l, this.length++) } return this
			}; (function (f, c, k) { for (var l = 0; l < c.length; l++)k(f, c[l]) })(E.prototype, "show hide toggle onResize resize remove stop doScrollPos".split(" "), function (f, c) { f[c] = function () { var f = arguments; return this.each(function () { this[c].apply(this, f) }) } }); f.fn.getNiceScroll =
				function (h) { return void 0 === h ? new E(this) : this[h] && f.data(this[h], "__nicescroll") || !1 }; f.expr[":"].nicescroll = function (h) { return void 0 !== f.data(h, "__nicescroll") }; f.fn.niceScroll = function (h, c) {
					void 0 !== c || "object" != typeof h || "jquery" in h || (c = h, h = !1); c = f.extend({}, c); var k = new E; void 0 === c && (c = {}); h && (c.doc = f(h), c.win = f(this)); var l = !("doc" in c); l || "win" in c || (c.win = f(this)); this.each(function () {
						var d = f(this).data("__nicescroll") || !1; d || (c.doc = l ? f(this) : c.doc, d = new S(c, f(this)), f(this).data("__nicescroll",
							d)); k.push(d)
					}); return 1 == k.length ? k[0] : k
				}; window.NiceScroll = { getjQuery: function () { return f } }; f.nicescroll || (f.nicescroll = new E, f.nicescroll.options = K)
	});

	$.fn.niceScroll = function (wrapper, _opt) {
		if (typeof _opt == 'undefined') {
			if ((typeof wrapper == 'object') && !('jquery' in wrapper)) {
				_opt = wrapper;
				wrapper = false;
			}
		}
		var ret = new NiceScrollArray();
		if (typeof _opt == 'undefined') _opt = {};

		if (wrapper || false) {
			_opt.doc = $(wrapper);
			_opt.win = $(this);
		}
		var doc = !('doc' in _opt) ? $(this) : _opt.doc;

		doc.each(function () {
			var nice = $(this).data('__nicescroll') || false;
			if (!nice) {
				_opt.doc = (wrapper) ? _opt.doc : $(this);
				_opt.win = (wrapper) ? _opt.win : $(this);
				nice = new NiceScroll(_opt, $(this));
				$(this).data('__nicescroll', nice);
			}
			ret.push(nice);
		});

		return (ret.length == 1) ? ret[0] : ret;

	};

	window.NiceScroll = {
		getjQuery: function () {
			return jQuery
		}
	};

	if (!$.nicescroll) {
		$.nicescroll = new NiceScrollArray();
		$.nicescroll.options = _globaloptions;
	}

}));




/* JQuery Nice Select - v1.0
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/* jQuery Nice Select - v1.1.0
	https://github.com/hernansartorio/jquery-nice-select
	Made by Hernán Sartorio  */
(function ($) {

	$.fn.niceSelect = function (method) {

		// Methods
		if (typeof method == 'string') {
			if (method == 'update') {
				this.each(function () {
					var $select = $(this);
					var $dropdown = $(this).next('.nice-select');
					var open = $dropdown.hasClass('open');

					if ($dropdown.length) {
						$dropdown.remove();
						create_nice_select($select);

						if (open) {
							$select.next().trigger('click');
						}
					}
				});
			} else if (method == 'destroy') {
				this.each(function () {
					var $select = $(this);
					var $dropdown = $(this).next('.nice-select');

					if ($dropdown.length) {
						$dropdown.remove();
						$select.css('display', '');
					}
				});
				if ($('.nice-select').length == 0) {
					$(document).off('.nice_select');
				}
			} else {
				console.log('Method "' + method + '" does not exist.')
			}
			return this;
		}

		// Hide native select
		this.hide();

		// Create custom markup
		this.each(function () {
			var $select = $(this);

			if (!$select.next().hasClass('nice-select')) {
				create_nice_select($select);
			}
		});

		function create_nice_select($select) {
			$select.after($('<div></div>')
				.addClass('nice-select')
				.addClass($select.attr('class') || '')
				.addClass($select.attr('disabled') ? 'disabled' : '')
				.addClass($select.attr('multiple') ? 'multiple' : '')
				.attr('tabindex', $select.attr('disabled') ? null : '0')
				.html($select.attr('multiple') ? '<span class="multiple-options"></span><ul class="list"></ul>' : '<span class="current"></span><ul class="list"></ul>')
			);

			var $dropdown = $select.next();
			var $options = $select.find('option');
			if ($select.attr('multiple')) {
				var $selected = $select.find('option:selected');
				var $selected_html = '';
				$selected.each(function () {
					$selected_option = $(this);
					$selected_html += '<span class="current">' + $selected_option.data('display') || $selected_option.text() + '</span>';
				});
				$dropdown.find('.multiple-options').html($selected_html);
			} else {
				var $selected = $select.find('option:selected');
				$dropdown.find('.current').html($selected.data('display') || $selected.text());
			}


			$options.each(function (i) {
				var $option = $(this);
				var display = $option.data('display');

				$dropdown.find('ul').append($('<li></li>')
					.attr('data-value', $option.val())
					.attr('data-display', (display || null))
					.addClass('option' +
						($option.is(':selected') ? ' selected' : '') +
						($option.is(':disabled') ? ' disabled' : ''))
					.html($option.text())
				);
			});
		}

		/* Event listeners */

		// Unbind existing events in case that the plugin has been initialized before
		$(document).off('.nice_select');

		// Open/close
		$(document).on('click.nice_select', '.nice-select', function (event) {
			var $dropdown = $(this);

			$('.nice-select').not($dropdown).removeClass('open');
			$dropdown.toggleClass('open');

			if ($dropdown.hasClass('open')) {
				$dropdown.find('.option');
				$dropdown.find('.focus').removeClass('focus');
				$dropdown.find('.selected').addClass('focus');
			} else {
				$dropdown.focus();
			}
		});

		// Close when clicking outside
		$(document).on('click.nice_select', function (event) {
			if ($(event.target).closest('.nice-select').length === 0) {
				$('.nice-select').removeClass('open').find('.option');
			}
		});

		// Option click
		$(document).on('click.nice_select', '.nice-select .option:not(.disabled)', function (event) {
			var $option = $(this);
			var $dropdown = $option.closest('.nice-select');

			if ($dropdown.hasClass('multiple')) {
				if ($option.hasClass('selected')) {
					$option.removeClass('selected');
				} else {
					$option.addClass('selected');
				}
				var $selected = $dropdown.find('.option.selected');
				var $selected_html = '';
				$selected.each(function () {
					$selected_option = $(this);
					$selected_html += '<span class="current">' + $selected_option.data('display') || $selected_option.text() + '</span>';
				});
				$dropdown.find('.multiple-options').html($selected_html);
				$dropdown.prev('select').val($selected.map(function () { return $(this).data('value'); }).get()).trigger('change');
			} else {
				$dropdown.find('.selected').removeClass('selected');
				$option.addClass('selected');
				var text = $option.data('display') || $option.text();
				$dropdown.find('.current').text(text);
				$dropdown.prev('select').val($option.data('value')).trigger('change');
			}
		});

		// Keyboard events
		$(document).on('keydown.nice_select', '.nice-select', function (event) {
			var $dropdown = $(this);
			var $focused_option = $($dropdown.find('.focus') || $dropdown.find('.list .option.selected'));

			// Space or Enter
			if (event.keyCode == 32 || event.keyCode == 13) {
				if ($dropdown.hasClass('open')) {
					$focused_option.trigger('click');
				} else {
					$dropdown.trigger('click');
				}
				return false;
				// Down
			} else if (event.keyCode == 40) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				} else {
					var $next = $focused_option.nextAll('.option:not(.disabled)').first();
					if ($next.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$next.addClass('focus');
					}
				}
				return false;
				// Up
			} else if (event.keyCode == 38) {
				if (!$dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				} else {
					var $prev = $focused_option.prevAll('.option:not(.disabled)').first();
					if ($prev.length > 0) {
						$dropdown.find('.focus').removeClass('focus');
						$prev.addClass('focus');
					}
				}
				return false;
				// Esc
			} else if (event.keyCode == 27) {
				if ($dropdown.hasClass('open')) {
					$dropdown.trigger('click');
				}
				// Tab
			} else if (event.keyCode == 9) {
				if ($dropdown.hasClass('open')) {
					return false;
				}
			}
		});

		// HTML shim
		var style = document.createElement('a').style;
		style.cssText = 'pointer-events:auto';
		if (style.pointerEvents !== 'auto') {
			$('html').addClass('no-csspointerevents');
		}

		return this;

	};

}(jQuery));


/* jQuery UI - v1.12.1
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/*!
 * jQuery UI 1.13.3
 * http://jqueryui.com
 *
 * Copyright jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 */
!function (t) { "use strict"; "function" == typeof define && define.amd ? define(["jquery"], t) : t(jQuery) }(function (t) {
	"use strict"; t.ui = t.ui || {}; var e = t.ui.version = "1.13.3";
	// ... (full jQuery UI code) ...
	(function (factory) {
		if (typeof define === "function" && define.amd) {

			// AMD. Register as an anonymous module.
			define(["jquery"], factory);
		} else {

			// Browser globals
			factory(jQuery);
		}
	}(function ($) {

		$.ui = $.ui || {};

		var version = $.ui.version = "1.12.1";


		/*!
		 * jQuery UI Widget 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Widget
		//>>group: Core
		//>>description: Provides a factory for creating stateful widgets with a common API.
		//>>docs: http://api.jqueryui.com/jQuery.widget/
		//>>demos: http://jqueryui.com/widget/



		var widgetUuid = 0;
		var widgetSlice = Array.prototype.slice;

		$.cleanData = (function (orig) {
			return function (elems) {
				var events, elem, i;
				for (i = 0; (elem = elems[i]) != null; i++) {
					try {

						// Only trigger remove when necessary to save time
						events = $._data(elem, "events");
						if (events && events.remove) {
							$(elem).triggerHandler("remove");
						}

						// Http://bugs.jquery.com/ticket/8235
					} catch (e) { }
				}
				orig(elems);
			};
		})($.cleanData);

		$.widget = function (name, base, prototype) {
			var existingConstructor, constructor, basePrototype;

			// ProxiedPrototype allows the provided prototype to remain unmodified
			// so that it can be used as a mixin for multiple widgets (#8876)
			var proxiedPrototype = {};

			var namespace = name.split(".")[0];
			name = name.split(".")[1];
			var fullName = namespace + "-" + name;

			if (!prototype) {
				prototype = base;
				base = $.Widget;
			}

			if ($.isArray(prototype)) {
				prototype = $.extend.apply(null, [{}].concat(prototype));
			}

			// Create selector for plugin
			$.expr[":"][fullName.toLowerCase()] = function (elem) {
				return !!$.data(elem, fullName);
			};

			$[namespace] = $[namespace] || {};
			existingConstructor = $[namespace][name];
			constructor = $[namespace][name] = function (options, element) {

				// Allow instantiation without "new" keyword
				if (!this._createWidget) {
					return new constructor(options, element);
				}

				// Allow instantiation without initializing for simple inheritance
				// must use "new" keyword (the code above always passes args)
				if (arguments.length) {
					this._createWidget(options, element);
				}
			};

			// Extend with the existing constructor to carry over any static properties
			$.extend(constructor, existingConstructor, {
				version: prototype.version,

				// Copy the object used to create the prototype in case we need to
				// redefine the widget later
				_proto: $.extend({}, prototype),

				// Track widgets that inherit from this widget in case this widget is
				// redefined after a widget inherits from it
				_childConstructors: []
			});

			basePrototype = new base();

			// We need to make the options hash a property directly on the new instance
			// otherwise we'll modify the options hash on the prototype that we're
			// inheriting from
			basePrototype.options = $.widget.extend({}, basePrototype.options);
			$.each(prototype, function (prop, value) {
				if (!$.isFunction(value)) {
					proxiedPrototype[prop] = value;
					return;
				}
				proxiedPrototype[prop] = (function () {
					function _super() {
						return base.prototype[prop].apply(this, arguments);
					}

					function _superApply(args) {
						return base.prototype[prop].apply(this, args);
					}

					return function () {
						var __super = this._super;
						var __superApply = this._superApply;
						var returnValue;

						this._super = _super;
						this._superApply = _superApply;

						returnValue = value.apply(this, arguments);

						this._super = __super;
						this._superApply = __superApply;

						return returnValue;
					};
				})();
			});
			constructor.prototype = $.widget.extend(basePrototype, {

				// TODO: remove support for widgetEventPrefix
				// always use the name + a colon as the prefix, e.g., draggable:start
				// don't prefix for widgets that aren't DOM-based
				widgetEventPrefix: existingConstructor ? (basePrototype.widgetEventPrefix || name) : name
			}, proxiedPrototype, {
				constructor: constructor,
				namespace: namespace,
				widgetName: name,
				widgetFullName: fullName
			});

			// If this widget is being redefined then we need to find all widgets that
			// are inheriting from it and redefine all of them so that they inherit from
			// the new version of this widget. We're essentially trying to replace one
			// level in the prototype chain.
			if (existingConstructor) {
				$.each(existingConstructor._childConstructors, function (i, child) {
					var childPrototype = child.prototype;

					// Redefine the child widget using the same prototype that was
					// originally used, but inherit from the new version of the base
					$.widget(childPrototype.namespace + "." + childPrototype.widgetName, constructor,
						child._proto);
				});

				// Remove the list of existing child constructors from the old constructor
				// so the old child constructors can be garbage collected
				delete existingConstructor._childConstructors;
			} else {
				base._childConstructors.push(constructor);
			}

			$.widget.bridge(name, constructor);

			return constructor;
		};

		$.widget.extend = function (target) {
			var input = widgetSlice.call(arguments, 1);
			var inputIndex = 0;
			var inputLength = input.length;
			var key;
			var value;

			for (; inputIndex < inputLength; inputIndex++) {
				for (key in input[inputIndex]) {
					value = input[inputIndex][key];
					if (input[inputIndex].hasOwnProperty(key) && value !== undefined) {

						// Clone objects
						if ($.isPlainObject(value)) {
							target[key] = $.isPlainObject(target[key]) ?
								$.widget.extend({}, target[key], value) :

								// Don't extend strings, arrays, etc. with objects
								$.widget.extend({}, value);

							// Copy everything else by reference
						} else {
							target[key] = value;
						}
					}
				}
			}
			return target;
		};

		$.widget.bridge = function (name, object) {
			var fullName = object.prototype.widgetFullName || name;
			$.fn[name] = function (options) {
				var isMethodCall = typeof options === "string";
				var args = widgetSlice.call(arguments, 1);
				var returnValue = this;

				if (isMethodCall) {

					// If this is an empty collection, we need to have the instance method
					// return undefined instead of the jQuery instance
					if (!this.length && options === "instance") {
						returnValue = undefined;
					} else {
						this.each(function () {
							var methodValue;
							var instance = $.data(this, fullName);

							if (options === "instance") {
								returnValue = instance;
								return false;
							}

							if (!instance) {
								return $.error("cannot call methods on " + name +
									" prior to initialization; " +
									"attempted to call method '" + options + "'");
							}

							if (!$.isFunction(instance[options]) || options.charAt(0) === "_") {
								return $.error("no such method '" + options + "' for " + name +
									" widget instance");
							}

							methodValue = instance[options].apply(instance, args);

							if (methodValue !== instance && methodValue !== undefined) {
								returnValue = methodValue && methodValue.jquery ?
									returnValue.pushStack(methodValue.get()) :
									methodValue;
								return false;
							}
						});
					}
				} else {

					// Allow multiple hashes to be passed on init
					if (args.length) {
						options = $.widget.extend.apply(null, [options].concat(args));
					}

					this.each(function () {
						var instance = $.data(this, fullName);
						if (instance) {
							instance.option(options || {});
							if (instance._init) {
								instance._init();
							}
						} else {
							$.data(this, fullName, new object(options, this));
						}
					});
				}

				return returnValue;
			};
		};

		$.Widget = function ( /* options, element */) { };
		$.Widget._childConstructors = [];

		$.Widget.prototype = {
			widgetName: "widget",
			widgetEventPrefix: "",
			defaultElement: "<div>",

			options: {
				classes: {},
				disabled: false,

				// Callbacks
				create: null
			},

			_createWidget: function (options, element) {
				element = $(element || this.defaultElement || this)[0];
				this.element = $(element);
				this.uuid = widgetUuid++;
				this.eventNamespace = "." + this.widgetName + this.uuid;

				this.bindings = $();
				this.hoverable = $();
				this.focusable = $();
				this.classesElementLookup = {};

				if (element !== this) {
					$.data(element, this.widgetFullName, this);
					this._on(true, this.element, {
						remove: function (event) {
							if (event.target === element) {
								this.destroy();
							}
						}
					});
					this.document = $(element.style ?

						// Element within the document
						element.ownerDocument :

						// Element is window or document
						element.document || element);
					this.window = $(this.document[0].defaultView || this.document[0].parentWindow);
				}

				this.options = $.widget.extend({},
					this.options,
					this._getCreateOptions(),
					options);

				this._create();

				if (this.options.disabled) {
					this._setOptionDisabled(this.options.disabled);
				}

				this._trigger("create", null, this._getCreateEventData());
				this._init();
			},

			_getCreateOptions: function () {
				return {};
			},

			_getCreateEventData: $.noop,

			_create: $.noop,

			_init: $.noop,

			destroy: function () {
				var that = this;

				this._destroy();
				$.each(this.classesElementLookup, function (key, value) {
					that._removeClass(value, key);
				});

				// We can probably remove the unbind calls in 2.0
				// all event bindings should go through this._on()
				this.element
					.off(this.eventNamespace)
					.removeData(this.widgetFullName);
				this.widget()
					.off(this.eventNamespace)
					.removeAttr("aria-disabled");

				// Clean up events and states
				this.bindings.off(this.eventNamespace);
			},

			_destroy: $.noop,

			widget: function () {
				return this.element;
			},

			option: function (key, value) {
				var options = key;
				var parts;
				var curOption;
				var i;

				if (arguments.length === 0) {

					// Don't return a reference to the internal hash
					return $.widget.extend({}, this.options);
				}

				if (typeof key === "string") {

					// Handle nested keys, e.g., "foo.bar" => { foo: { bar: ___ } }
					options = {};
					parts = key.split(".");
					key = parts.shift();
					if (parts.length) {
						curOption = options[key] = $.widget.extend({}, this.options[key]);
						for (i = 0; i < parts.length - 1; i++) {
							curOption[parts[i]] = curOption[parts[i]] || {};
							curOption = curOption[parts[i]];
						}
						key = parts.pop();
						if (arguments.length === 1) {
							return curOption[key] === undefined ? null : curOption[key];
						}
						curOption[key] = value;
					} else {
						if (arguments.length === 1) {
							return this.options[key] === undefined ? null : this.options[key];
						}
						options[key] = value;
					}
				}

				this._setOptions(options);

				return this;
			},

			_setOptions: function (options) {
				var key;

				for (key in options) {
					this._setOption(key, options[key]);
				}

				return this;
			},

			_setOption: function (key, value) {
				if (key === "classes") {
					this._setOptionClasses(value);
				}

				this.options[key] = value;

				if (key === "disabled") {
					this._setOptionDisabled(value);
				}

				return this;
			},

			_setOptionClasses: function (value) {
				var classKey, elements, currentElements;

				for (classKey in value) {
					currentElements = this.classesElementLookup[classKey];
					if (value[classKey] === this.options.classes[classKey] ||
						!currentElements ||
						!currentElements.length) {
						continue;
					}

					// We are doing this to create a new jQuery object because the _removeClass() call
					// on the next line is going to destroy the reference to the current elements being
					// tracked. We need to save a copy of this collection so that we can add the new classes
					// below.
					elements = $(currentElements.get());
					this._removeClass(currentElements, classKey);

					// We don't use _addClass() here, because that uses this.options.classes
					// for generating the string of classes. We want to use the value passed in from
					// _setOption(), this is the new value of the classes option which was passed to
					// _setOption(). We pass this value directly to _classes().
					elements.addClass(this._classes({
						element: elements,
						keys: classKey,
						classes: value,
						add: true
					}));
				}
			},

			_setOptionDisabled: function (value) {
				this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null, !!value);

				// If the widget is becoming disabled, then nothing is interactive
				if (value) {
					this._removeClass(this.hoverable, null, "ui-state-hover");
					this._removeClass(this.focusable, null, "ui-state-focus");
				}
			},

			enable: function () {
				return this._setOptions({ disabled: false });
			},

			disable: function () {
				return this._setOptions({ disabled: true });
			},

			_classes: function (options) {
				var full = [];
				var that = this;

				options = $.extend({
					element: this.element,
					classes: this.options.classes || {}
				}, options);

				function processClassString(classes, checkOption) {
					var current, i;
					for (i = 0; i < classes.length; i++) {
						current = that.classesElementLookup[classes[i]] || $();
						if (options.add) {
							current = $($.unique(current.get().concat(options.element.get())));
						} else {
							current = $(current.not(options.element).get());
						}
						that.classesElementLookup[classes[i]] = current;
						full.push(classes[i]);
						if (checkOption && options.classes[classes[i]]) {
							full.push(options.classes[classes[i]]);
						}
					}
				}

				this._on(options.element, {
					"remove": "_untrackClassesElement"
				});

				if (options.keys) {
					processClassString(options.keys.match(/\S+/g) || [], true);
				}
				if (options.extra) {
					processClassString(options.extra.match(/\S+/g) || []);
				}

				return full.join(" ");
			},

			_untrackClassesElement: function (event) {
				var that = this;
				$.each(that.classesElementLookup, function (key, value) {
					if ($.inArray(event.target, value) !== -1) {
						that.classesElementLookup[key] = $(value.not(event.target).get());
					}
				});
			},

			_removeClass: function (element, keys, extra) {
				return this._toggleClass(element, keys, extra, false);
			},

			_addClass: function (element, keys, extra) {
				return this._toggleClass(element, keys, extra, true);
			},

			_toggleClass: function (element, keys, extra, add) {
				add = (typeof add === "boolean") ? add : extra;
				var shift = (typeof element === "string" || element === null),
					options = {
						extra: shift ? keys : extra,
						keys: shift ? element : keys,
						element: shift ? this.element : element,
						add: add
					};
				options.element.toggleClass(this._classes(options), add);
				return this;
			},

			_on: function (suppressDisabledCheck, element, handlers) {
				var delegateElement;
				var instance = this;

				// No suppressDisabledCheck flag, shuffle arguments
				if (typeof suppressDisabledCheck !== "boolean") {
					handlers = element;
					element = suppressDisabledCheck;
					suppressDisabledCheck = false;
				}

				// No element argument, shuffle and use this.element
				if (!handlers) {
					handlers = element;
					element = this.element;
					delegateElement = this.widget();
				} else {
					element = delegateElement = $(element);
					this.bindings = this.bindings.add(element);
				}

				$.each(handlers, function (event, handler) {
					function handlerProxy() {

						// Allow widgets to customize the disabled handling
						// - disabled as an array instead of boolean
						// - disabled class as method for disabling individual parts
						if (!suppressDisabledCheck &&
							(instance.options.disabled === true ||
								$(this).hasClass("ui-state-disabled"))) {
							return;
						}
						return (typeof handler === "string" ? instance[handler] : handler)
							.apply(instance, arguments);
					}

					// Copy the guid so direct unbinding works
					if (typeof handler !== "string") {
						handlerProxy.guid = handler.guid =
							handler.guid || handlerProxy.guid || $.guid++;
					}

					var match = event.match(/^([\w:-]*)\s*(.*)$/);
					var eventName = match[1] + instance.eventNamespace;
					var selector = match[2];

					if (selector) {
						delegateElement.on(eventName, selector, handlerProxy);
					} else {
						element.on(eventName, handlerProxy);
					}
				});
			},

			_off: function (element, eventName) {
				eventName = (eventName || "").split(" ").join(this.eventNamespace + " ") +
					this.eventNamespace;
				element.off(eventName).off(eventName);

				// Clear the stack to avoid memory leaks (#10056)
				this.bindings = $(this.bindings.not(element).get());
				this.focusable = $(this.focusable.not(element).get());
				this.hoverable = $(this.hoverable.not(element).get());
			},

			_delay: function (handler, delay) {
				function handlerProxy() {
					return (typeof handler === "string" ? instance[handler] : handler)
						.apply(instance, arguments);
				}
				var instance = this;
				return setTimeout(handlerProxy, delay || 0);
			},

			_hoverable: function (element) {
				this.hoverable = this.hoverable.add(element);
				this._on(element, {
					mouseenter: function (event) {
						this._addClass($(event.currentTarget), null, "ui-state-hover");
					},
					mouseleave: function (event) {
						this._removeClass($(event.currentTarget), null, "ui-state-hover");
					}
				});
			},

			_focusable: function (element) {
				this.focusable = this.focusable.add(element);
				this._on(element, {
					focusin: function (event) {
						this._addClass($(event.currentTarget), null, "ui-state-focus");
					},
					focusout: function (event) {
						this._removeClass($(event.currentTarget), null, "ui-state-focus");
					}
				});
			},

			_trigger: function (type, event, data) {
				var prop, orig;
				var callback = this.options[type];

				data = data || {};
				event = $.Event(event);
				event.type = (type === this.widgetEventPrefix ?
					type :
					this.widgetEventPrefix + type).toLowerCase();

				// The original event may come from any element
				// so we need to reset the target on the new event
				event.target = this.element[0];

				// Copy original event properties over to the new event
				orig = event.originalEvent;
				if (orig) {
					for (prop in orig) {
						if (!(prop in event)) {
							event[prop] = orig[prop];
						}
					}
				}

				this.element.trigger(event, data);
				return !($.isFunction(callback) &&
					callback.apply(this.element[0], [event].concat(data)) === false ||
					event.isDefaultPrevented());
			}
		};

		$.each({ show: "fadeIn", hide: "fadeOut" }, function (method, defaultEffect) {
			$.Widget.prototype["_" + method] = function (element, options, callback) {
				if (typeof options === "string") {
					options = { effect: options };
				}

				var hasOptions;
				var effectName = !options ?
					method :
					options === true || typeof options === "number" ?
						defaultEffect :
						options.effect || defaultEffect;

				options = options || {};
				if (typeof options === "number") {
					options = { duration: options };
				}

				hasOptions = !$.isEmptyObject(options);
				options.complete = callback;

				if (options.delay) {
					element.delay(options.delay);
				}

				if (hasOptions && $.effects && $.effects.effect[effectName]) {
					element[method](options);
				} else if (effectName !== method && element[effectName]) {
					element[effectName](options.duration, options.easing, callback);
				} else {
					element.queue(function (next) {
						$(this)[method]();
						if (callback) {
							callback.call(element[0]);
						}
						next();
					});
				}
			};
		});

		var widget = $.widget;


		/*!
		 * jQuery UI Position 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 *
		 * http://api.jqueryui.com/position/
		 */

		//>>label: Position
		//>>group: Core
		//>>description: Positions elements relative to other elements.
		//>>docs: http://api.jqueryui.com/position/
		//>>demos: http://jqueryui.com/position/


		(function () {
			var cachedScrollbarWidth,
				max = Math.max,
				abs = Math.abs,
				rhorizontal = /left|center|right/,
				rvertical = /top|center|bottom/,
				roffset = /[\+\-]\d+(\.[\d]+)?%?/,
				rposition = /^\w+/,
				rpercent = /%$/,
				_position = $.fn.position;

			function getOffsets(offsets, width, height) {
				return [
					parseFloat(offsets[0]) * (rpercent.test(offsets[0]) ? width / 100 : 1),
					parseFloat(offsets[1]) * (rpercent.test(offsets[1]) ? height / 100 : 1)
				];
			}

			function parseCss(element, property) {
				return parseInt($.css(element, property), 10) || 0;
			}

			function getDimensions(elem) {
				var raw = elem[0];
				if (raw.nodeType === 9) {
					return {
						width: elem.width(),
						height: elem.height(),
						offset: { top: 0, left: 0 }
					};
				}
				if ($.isWindow(raw)) {
					return {
						width: elem.width(),
						height: elem.height(),
						offset: { top: elem.scrollTop(), left: elem.scrollLeft() }
					};
				}
				if (raw.preventDefault) {
					return {
						width: 0,
						height: 0,
						offset: { top: raw.pageY, left: raw.pageX }
					};
				}
				return {
					width: elem.outerWidth(),
					height: elem.outerHeight(),
					offset: elem.offset()
				};
			}

			$.position = {
				scrollbarWidth: function () {
					if (cachedScrollbarWidth !== undefined) {
						return cachedScrollbarWidth;
					}
					var w1, w2,
						div = $("<div " +
							"style='display:block;position:absolute;width:50px;height:50px;overflow:hidden;'>" +
							"<div style='height:100px;width:auto;'></div></div>"),
						innerDiv = div.children()[0];

					$("body").append(div);
					w1 = innerDiv.offsetWidth;
					div.css("overflow", "scroll");

					w2 = innerDiv.offsetWidth;

					if (w1 === w2) {
						w2 = div[0].clientWidth;
					}

					div.remove();

					return (cachedScrollbarWidth = w1 - w2);
				},
				getScrollInfo: function (within) {
					var overflowX = within.isWindow || within.isDocument ? "" :
						within.element.css("overflow-x"),
						overflowY = within.isWindow || within.isDocument ? "" :
							within.element.css("overflow-y"),
						hasOverflowX = overflowX === "scroll" ||
							(overflowX === "auto" && within.width < within.element[0].scrollWidth),
						hasOverflowY = overflowY === "scroll" ||
							(overflowY === "auto" && within.height < within.element[0].scrollHeight);
					return {
						width: hasOverflowY ? $.position.scrollbarWidth() : 0,
						height: hasOverflowX ? $.position.scrollbarWidth() : 0
					};
				},
				getWithinInfo: function (element) {
					var withinElement = $(element || window),
						isWindow = $.isWindow(withinElement[0]),
						isDocument = !!withinElement[0] && withinElement[0].nodeType === 9,
						hasOffset = !isWindow && !isDocument;
					return {
						element: withinElement,
						isWindow: isWindow,
						isDocument: isDocument,
						offset: hasOffset ? $(element).offset() : { left: 0, top: 0 },
						scrollLeft: withinElement.scrollLeft(),
						scrollTop: withinElement.scrollTop(),
						width: withinElement.outerWidth(),
						height: withinElement.outerHeight()
					};
				}
			};

			$.fn.position = function (options) {
				if (!options || !options.of) {
					return _position.apply(this, arguments);
				}

				// Make a copy, we don't want to modify arguments
				options = $.extend({}, options);

				var atOffset, targetWidth, targetHeight, targetOffset, basePosition, dimensions,
					target = $(options.of),
					within = $.position.getWithinInfo(options.within),
					scrollInfo = $.position.getScrollInfo(within),
					collision = (options.collision || "flip").split(" "),
					offsets = {};

				dimensions = getDimensions(target);
				if (target[0].preventDefault) {

					// Force left top to allow flipping
					options.at = "left top";
				}
				targetWidth = dimensions.width;
				targetHeight = dimensions.height;
				targetOffset = dimensions.offset;

				// Clone to reuse original targetOffset later
				basePosition = $.extend({}, targetOffset);

				// Force my and at to have valid horizontal and vertical positions
				// if a value is missing or invalid, it will be converted to center
				$.each(["my", "at"], function () {
					var pos = (options[this] || "").split(" "),
						horizontalOffset,
						verticalOffset;

					if (pos.length === 1) {
						pos = rhorizontal.test(pos[0]) ?
							pos.concat(["center"]) :
							rvertical.test(pos[0]) ?
								["center"].concat(pos) :
								["center", "center"];
					}
					pos[0] = rhorizontal.test(pos[0]) ? pos[0] : "center";
					pos[1] = rvertical.test(pos[1]) ? pos[1] : "center";

					// Calculate offsets
					horizontalOffset = roffset.exec(pos[0]);
					verticalOffset = roffset.exec(pos[1]);
					offsets[this] = [
						horizontalOffset ? horizontalOffset[0] : 0,
						verticalOffset ? verticalOffset[0] : 0
					];

					// Reduce to just the positions without the offsets
					options[this] = [
						rposition.exec(pos[0])[0],
						rposition.exec(pos[1])[0]
					];
				});

				// Normalize collision option
				if (collision.length === 1) {
					collision[1] = collision[0];
				}

				if (options.at[0] === "right") {
					basePosition.left += targetWidth;
				} else if (options.at[0] === "center") {
					basePosition.left += targetWidth / 2;
				}

				if (options.at[1] === "bottom") {
					basePosition.top += targetHeight;
				} else if (options.at[1] === "center") {
					basePosition.top += targetHeight / 2;
				}

				atOffset = getOffsets(offsets.at, targetWidth, targetHeight);
				basePosition.left += atOffset[0];
				basePosition.top += atOffset[1];

				return this.each(function () {
					var collisionPosition, using,
						elem = $(this),
						elemWidth = elem.outerWidth(),
						elemHeight = elem.outerHeight(),
						marginLeft = parseCss(this, "marginLeft"),
						marginTop = parseCss(this, "marginTop"),
						collisionWidth = elemWidth + marginLeft + parseCss(this, "marginRight") +
							scrollInfo.width,
						collisionHeight = elemHeight + marginTop + parseCss(this, "marginBottom") +
							scrollInfo.height,
						position = $.extend({}, basePosition),
						myOffset = getOffsets(offsets.my, elem.outerWidth(), elem.outerHeight());

					if (options.my[0] === "right") {
						position.left -= elemWidth;
					} else if (options.my[0] === "center") {
						position.left -= elemWidth / 2;
					}

					if (options.my[1] === "bottom") {
						position.top -= elemHeight;
					} else if (options.my[1] === "center") {
						position.top -= elemHeight / 2;
					}

					position.left += myOffset[0];
					position.top += myOffset[1];

					collisionPosition = {
						marginLeft: marginLeft,
						marginTop: marginTop
					};

					$.each(["left", "top"], function (i, dir) {
						if ($.ui.position[collision[i]]) {
							$.ui.position[collision[i]][dir](position, {
								targetWidth: targetWidth,
								targetHeight: targetHeight,
								elemWidth: elemWidth,
								elemHeight: elemHeight,
								collisionPosition: collisionPosition,
								collisionWidth: collisionWidth,
								collisionHeight: collisionHeight,
								offset: [atOffset[0] + myOffset[0], atOffset[1] + myOffset[1]],
								my: options.my,
								at: options.at,
								within: within,
								elem: elem
							});
						}
					});

					if (options.using) {

						// Adds feedback as second argument to using callback, if present
						using = function (props) {
							var left = targetOffset.left - position.left,
								right = left + targetWidth - elemWidth,
								top = targetOffset.top - position.top,
								bottom = top + targetHeight - elemHeight,
								feedback = {
									target: {
										element: target,
										left: targetOffset.left,
										top: targetOffset.top,
										width: targetWidth,
										height: targetHeight
									},
									element: {
										element: elem,
										left: position.left,
										top: position.top,
										width: elemWidth,
										height: elemHeight
									},
									horizontal: right < 0 ? "left" : left > 0 ? "right" : "center",
									vertical: bottom < 0 ? "top" : top > 0 ? "bottom" : "middle"
								};
							if (targetWidth < elemWidth && abs(left + right) < targetWidth) {
								feedback.horizontal = "center";
							}
							if (targetHeight < elemHeight && abs(top + bottom) < targetHeight) {
								feedback.vertical = "middle";
							}
							if (max(abs(left), abs(right)) > max(abs(top), abs(bottom))) {
								feedback.important = "horizontal";
							} else {
								feedback.important = "vertical";
							}
							options.using.call(this, props, feedback);
						};
					}

					elem.offset($.extend(position, { using: using }));
				});
			};

			$.ui.position = {
				fit: {
					left: function (position, data) {
						var within = data.within,
							withinOffset = within.isWindow ? within.scrollLeft : within.offset.left,
							outerWidth = within.width,
							collisionPosLeft = position.left - data.collisionPosition.marginLeft,
							overLeft = withinOffset - collisionPosLeft,
							overRight = collisionPosLeft + data.collisionWidth - outerWidth - withinOffset,
							newOverRight;

						// Element is wider than within
						if (data.collisionWidth > outerWidth) {

							// Element is initially over the left side of within
							if (overLeft > 0 && overRight <= 0) {
								newOverRight = position.left + overLeft + data.collisionWidth - outerWidth -
									withinOffset;
								position.left += overLeft - newOverRight;

								// Element is initially over right side of within
							} else if (overRight > 0 && overLeft <= 0) {
								position.left = withinOffset;

								// Element is initially over both left and right sides of within
							} else {
								if (overLeft > overRight) {
									position.left = withinOffset + outerWidth - data.collisionWidth;
								} else {
									position.left = withinOffset;
								}
							}

							// Too far left -> align with left edge
						} else if (overLeft > 0) {
							position.left += overLeft;

							// Too far right -> align with right edge
						} else if (overRight > 0) {
							position.left -= overRight;

							// Adjust based on position and margin
						} else {
							position.left = max(position.left - collisionPosLeft, position.left);
						}
					},
					top: function (position, data) {
						var within = data.within,
							withinOffset = within.isWindow ? within.scrollTop : within.offset.top,
							outerHeight = data.within.height,
							collisionPosTop = position.top - data.collisionPosition.marginTop,
							overTop = withinOffset - collisionPosTop,
							overBottom = collisionPosTop + data.collisionHeight - outerHeight - withinOffset,
							newOverBottom;

						// Element is taller than within
						if (data.collisionHeight > outerHeight) {

							// Element is initially over the top of within
							if (overTop > 0 && overBottom <= 0) {
								newOverBottom = position.top + overTop + data.collisionHeight - outerHeight -
									withinOffset;
								position.top += overTop - newOverBottom;

								// Element is initially over bottom of within
							} else if (overBottom > 0 && overTop <= 0) {
								position.top = withinOffset;

								// Element is initially over both top and bottom of within
							} else {
								if (overTop > overBottom) {
									position.top = withinOffset + outerHeight - data.collisionHeight;
								} else {
									position.top = withinOffset;
								}
							}

							// Too far up -> align with top
						} else if (overTop > 0) {
							position.top += overTop;

							// Too far down -> align with bottom edge
						} else if (overBottom > 0) {
							position.top -= overBottom;

							// Adjust based on position and margin
						} else {
							position.top = max(position.top - collisionPosTop, position.top);
						}
					}
				},
				flip: {
					left: function (position, data) {
						var within = data.within,
							withinOffset = within.offset.left + within.scrollLeft,
							outerWidth = within.width,
							offsetLeft = within.isWindow ? within.scrollLeft : within.offset.left,
							collisionPosLeft = position.left - data.collisionPosition.marginLeft,
							overLeft = collisionPosLeft - offsetLeft,
							overRight = collisionPosLeft + data.collisionWidth - outerWidth - offsetLeft,
							myOffset = data.my[0] === "left" ?
								-data.elemWidth :
								data.my[0] === "right" ?
									data.elemWidth :
									0,
							atOffset = data.at[0] === "left" ?
								data.targetWidth :
								data.at[0] === "right" ?
									-data.targetWidth :
									0,
							offset = -2 * data.offset[0],
							newOverRight,
							newOverLeft;

						if (overLeft < 0) {
							newOverRight = position.left + myOffset + atOffset + offset + data.collisionWidth -
								outerWidth - withinOffset;
							if (newOverRight < 0 || newOverRight < abs(overLeft)) {
								position.left += myOffset + atOffset + offset;
							}
						} else if (overRight > 0) {
							newOverLeft = position.left - data.collisionPosition.marginLeft + myOffset +
								atOffset + offset - offsetLeft;
							if (newOverLeft > 0 || abs(newOverLeft) < overRight) {
								position.left += myOffset + atOffset + offset;
							}
						}
					},
					top: function (position, data) {
						var within = data.within,
							withinOffset = within.offset.top + within.scrollTop,
							outerHeight = within.height,
							offsetTop = within.isWindow ? within.scrollTop : within.offset.top,
							collisionPosTop = position.top - data.collisionPosition.marginTop,
							overTop = collisionPosTop - offsetTop,
							overBottom = collisionPosTop + data.collisionHeight - outerHeight - offsetTop,
							top = data.my[1] === "top",
							myOffset = top ?
								-data.elemHeight :
								data.my[1] === "bottom" ?
									data.elemHeight :
									0,
							atOffset = data.at[1] === "top" ?
								data.targetHeight :
								data.at[1] === "bottom" ?
									-data.targetHeight :
									0,
							offset = -2 * data.offset[1],
							newOverTop,
							newOverBottom;
						if (overTop < 0) {
							newOverBottom = position.top + myOffset + atOffset + offset + data.collisionHeight -
								outerHeight - withinOffset;
							if (newOverBottom < 0 || newOverBottom < abs(overTop)) {
								position.top += myOffset + atOffset + offset;
							}
						} else if (overBottom > 0) {
							newOverTop = position.top - data.collisionPosition.marginTop + myOffset + atOffset +
								offset - offsetTop;
							if (newOverTop > 0 || abs(newOverTop) < overBottom) {
								position.top += myOffset + atOffset + offset;
							}
						}
					}
				},
				flipfit: {
					left: function () {
						$.ui.position.flip.left.apply(this, arguments);
						$.ui.position.fit.left.apply(this, arguments);
					},
					top: function () {
						$.ui.position.flip.top.apply(this, arguments);
						$.ui.position.fit.top.apply(this, arguments);
					}
				}
			};

		})();

		var position = $.ui.position;


		/*!
		 * jQuery UI :data 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: :data Selector
		//>>group: Core
		//>>description: Selects elements which have data stored under the specified key.
		//>>docs: http://api.jqueryui.com/data-selector/


		var data = $.extend($.expr[":"], {
			data: $.expr.createPseudo ?
				$.expr.createPseudo(function (dataName) {
					return function (elem) {
						return !!$.data(elem, dataName);
					};
				}) :

				// Support: jQuery <1.8
				function (elem, i, match) {
					return !!$.data(elem, match[3]);
				}
		});

		/*!
		 * jQuery UI Disable Selection 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: disableSelection
		//>>group: Core
		//>>description: Disable selection of text content within the set of matched elements.
		//>>docs: http://api.jqueryui.com/disableSelection/

		// This file is deprecated


		var disableSelection = $.fn.extend({
			disableSelection: (function () {
				var eventType = "onselectstart" in document.createElement("div") ?
					"selectstart" :
					"mousedown";

				return function () {
					return this.on(eventType + ".ui-disableSelection", function (event) {
						event.preventDefault();
					});
				};
			})(),

			enableSelection: function () {
				return this.off(".ui-disableSelection");
			}
		});


		/*!
		 * jQuery UI Effects 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Effects Core
		//>>group: Effects
		// jscs:disable maximumLineLength
		//>>description: Extends the internal jQuery effects. Includes morphing and easing. Required by all other effects.
		// jscs:enable maximumLineLength
		//>>docs: http://api.jqueryui.com/category/effects-core/
		//>>demos: http://jqueryui.com/effect/



		var dataSpace = "ui-effects-",
			dataSpaceStyle = "ui-effects-style",
			dataSpaceAnimated = "ui-effects-animated",

			// Create a local jQuery because jQuery Color relies on it and the
			// global may not exist with AMD and a custom build (#10199)
			jQuery = $;

		$.effects = {
			effect: {}
		};

		/*!
		 * jQuery Color Animations v2.1.2
		 * https://github.com/jquery/jquery-color
		 *
		 * Copyright 2014 jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 *
		 * Date: Wed Jan 16 08:47:09 2013 -0600
		 */
		(function (jQuery, undefined) {

			var stepHooks = "backgroundColor borderBottomColor borderLeftColor borderRightColor " +
				"borderTopColor color columnRuleColor outlineColor textDecorationColor textEmphasisColor",

				// Plusequals test for += 100 -= 100
				rplusequals = /^([\-+])=\s*(\d+\.?\d*)/,

				// A set of RE's that can match strings and generate color tuples.
				stringParsers = [{
					re: /rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
					parse: function (execResult) {
						return [
							execResult[1],
							execResult[2],
							execResult[3],
							execResult[4]
						];
					}
				}, {
					re: /rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
					parse: function (execResult) {
						return [
							execResult[1] * 2.55,
							execResult[2] * 2.55,
							execResult[3] * 2.55,
							execResult[4]
						];
					}
				}, {

					// This regex ignores A-F because it's compared against an already lowercased string
					re: /#([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})/,
					parse: function (execResult) {
						return [
							parseInt(execResult[1], 16),
							parseInt(execResult[2], 16),
							parseInt(execResult[3], 16)
						];
					}
				}, {

					// This regex ignores A-F because it's compared against an already lowercased string
					re: /#([a-f0-9])([a-f0-9])([a-f0-9])/,
					parse: function (execResult) {
						return [
							parseInt(execResult[1] + execResult[1], 16),
							parseInt(execResult[2] + execResult[2], 16),
							parseInt(execResult[3] + execResult[3], 16)
						];
					}
				}, {
					re: /hsla?\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
					space: "hsla",
					parse: function (execResult) {
						return [
							execResult[1],
							execResult[2] / 100,
							execResult[3] / 100,
							execResult[4]
						];
					}
				}],

				// JQuery.Color( )
				color = jQuery.Color = function (color, green, blue, alpha) {
					return new jQuery.Color.fn.parse(color, green, blue, alpha);
				},
				spaces = {
					rgba: {
						props: {
							red: {
								idx: 0,
								type: "byte"
							},
							green: {
								idx: 1,
								type: "byte"
							},
							blue: {
								idx: 2,
								type: "byte"
							}
						}
					},

					hsla: {
						props: {
							hue: {
								idx: 0,
								type: "degrees"
							},
							saturation: {
								idx: 1,
								type: "percent"
							},
							lightness: {
								idx: 2,
								type: "percent"
							}
						}
					}
				},
				propTypes = {
					"byte": {
						floor: true,
						max: 255
					},
					"percent": {
						max: 1
					},
					"degrees": {
						mod: 360,
						floor: true
					}
				},
				support = color.support = {},

				// Element for support tests
				supportElem = jQuery("<p>")[0],

				// Colors = jQuery.Color.names
				colors,

				// Local aliases of functions called often
				each = jQuery.each;

			// Determine rgba support immediately
			supportElem.style.cssText = "background-color:rgba(1,1,1,.5)";
			support.rgba = supportElem.style.backgroundColor.indexOf("rgba") > -1;

			// Define cache name and alpha properties
			// for rgba and hsla spaces
			each(spaces, function (spaceName, space) {
				space.cache = "_" + spaceName;
				space.props.alpha = {
					idx: 3,
					type: "percent",
					def: 1
				};
			});

			function clamp(value, prop, allowEmpty) {
				var type = propTypes[prop.type] || {};

				if (value == null) {
					return (allowEmpty || !prop.def) ? null : prop.def;
				}

				// ~~ is an short way of doing floor for positive numbers
				value = type.floor ? ~~value : parseFloat(value);

				// IE will pass in empty strings as value for alpha,
				// which will hit this case
				if (isNaN(value)) {
					return prop.def;
				}

				if (type.mod) {

					// We add mod before modding to make sure that negatives values
					// get converted properly: -10 -> 350
					return (value + type.mod) % type.mod;
				}

				// For now all property types without mod have min and max
				return 0 > value ? 0 : type.max < value ? type.max : value;
			}

			function stringParse(string) {
				var inst = color(),
					rgba = inst._rgba = [];

				string = string.toLowerCase();

				each(stringParsers, function (i, parser) {
					var parsed,
						match = parser.re.exec(string),
						values = match && parser.parse(match),
						spaceName = parser.space || "rgba";

					if (values) {
						parsed = inst[spaceName](values);

						// If this was an rgba parse the assignment might happen twice
						// oh well....
						inst[spaces[spaceName].cache] = parsed[spaces[spaceName].cache];
						rgba = inst._rgba = parsed._rgba;

						// Exit each( stringParsers ) here because we matched
						return false;
					}
				});

				// Found a stringParser that handled it
				if (rgba.length) {

					// If this came from a parsed string, force "transparent" when alpha is 0
					// chrome, (and maybe others) return "transparent" as rgba(0,0,0,0)
					if (rgba.join() === "0,0,0,0") {
						jQuery.extend(rgba, colors.transparent);
					}
					return inst;
				}

				// Named colors
				return colors[string];
			}

			color.fn = jQuery.extend(color.prototype, {
				parse: function (red, green, blue, alpha) {
					if (red === undefined) {
						this._rgba = [null, null, null, null];
						return this;
					}
					if (red.jquery || red.nodeType) {
						red = jQuery(red).css(green);
						green = undefined;
					}

					var inst = this,
						type = jQuery.type(red),
						rgba = this._rgba = [];

					// More than 1 argument specified - assume ( red, green, blue, alpha )
					if (green !== undefined) {
						red = [red, green, blue, alpha];
						type = "array";
					}

					if (type === "string") {
						return this.parse(stringParse(red) || colors._default);
					}

					if (type === "array") {
						each(spaces.rgba.props, function (key, prop) {
							rgba[prop.idx] = clamp(red[prop.idx], prop);
						});
						return this;
					}

					if (type === "object") {
						if (red instanceof color) {
							each(spaces, function (spaceName, space) {
								if (red[space.cache]) {
									inst[space.cache] = red[space.cache].slice();
								}
							});
						} else {
							each(spaces, function (spaceName, space) {
								var cache = space.cache;
								each(space.props, function (key, prop) {

									// If the cache doesn't exist, and we know how to convert
									if (!inst[cache] && space.to) {

										// If the value was null, we don't need to copy it
										// if the key was alpha, we don't need to copy it either
										if (key === "alpha" || red[key] == null) {
											return;
										}
										inst[cache] = space.to(inst._rgba);
									}

									// This is the only case where we allow nulls for ALL properties.
									// call clamp with alwaysAllowEmpty
									inst[cache][prop.idx] = clamp(red[key], prop, true);
								});

								// Everything defined but alpha?
								if (inst[cache] &&
									jQuery.inArray(null, inst[cache].slice(0, 3)) < 0) {

									// Use the default of 1
									inst[cache][3] = 1;
									if (space.from) {
										inst._rgba = space.from(inst[cache]);
									}
								}
							});
						}
						return this;
					}
				},
				is: function (compare) {
					var is = color(compare),
						same = true,
						inst = this;

					each(spaces, function (_, space) {
						var localCache,
							isCache = is[space.cache];
						if (isCache) {
							localCache = inst[space.cache] || space.to && space.to(inst._rgba) || [];
							each(space.props, function (_, prop) {
								if (isCache[prop.idx] != null) {
									same = (isCache[prop.idx] === localCache[prop.idx]);
									return same;
								}
							});
						}
						return same;
					});
					return same;
				},
				_space: function () {
					var used = [],
						inst = this;
					each(spaces, function (spaceName, space) {
						if (inst[space.cache]) {
							used.push(spaceName);
						}
					});
					return used.pop();
				},
				transition: function (other, distance) {
					var end = color(other),
						spaceName = end._space(),
						space = spaces[spaceName],
						startColor = this.alpha() === 0 ? color("transparent") : this,
						start = startColor[space.cache] || space.to(startColor._rgba),
						result = start.slice();

					end = end[space.cache];
					each(space.props, function (key, prop) {
						var index = prop.idx,
							startValue = start[index],
							endValue = end[index],
							type = propTypes[prop.type] || {};

						// If null, don't override start value
						if (endValue === null) {
							return;
						}

						// If null - use end
						if (startValue === null) {
							result[index] = endValue;
						} else {
							if (type.mod) {
								if (endValue - startValue > type.mod / 2) {
									startValue += type.mod;
								} else if (startValue - endValue > type.mod / 2) {
									startValue -= type.mod;
								}
							}
							result[index] = clamp((endValue - startValue) * distance + startValue, prop);
						}
					});
					return this[spaceName](result);
				},
				blend: function (opaque) {

					// If we are already opaque - return ourself
					if (this._rgba[3] === 1) {
						return this;
					}

					var rgb = this._rgba.slice(),
						a = rgb.pop(),
						blend = color(opaque)._rgba;

					return color(jQuery.map(rgb, function (v, i) {
						return (1 - a) * blend[i] + a * v;
					}));
				},
				toRgbaString: function () {
					var prefix = "rgba(",
						rgba = jQuery.map(this._rgba, function (v, i) {
							return v == null ? (i > 2 ? 1 : 0) : v;
						});

					if (rgba[3] === 1) {
						rgba.pop();
						prefix = "rgb(";
					}

					return prefix + rgba.join() + ")";
				},
				toHslaString: function () {
					var prefix = "hsla(",
						hsla = jQuery.map(this.hsla(), function (v, i) {
							if (v == null) {
								v = i > 2 ? 1 : 0;
							}

							// Catch 1 and 2
							if (i && i < 3) {
								v = Math.round(v * 100) + "%";
							}
							return v;
						});

					if (hsla[3] === 1) {
						hsla.pop();
						prefix = "hsl(";
					}
					return prefix + hsla.join() + ")";
				},
				toHexString: function (includeAlpha) {
					var rgba = this._rgba.slice(),
						alpha = rgba.pop();

					if (includeAlpha) {
						rgba.push(~~(alpha * 255));
					}

					return "#" + jQuery.map(rgba, function (v) {

						// Default to 0 when nulls exist
						v = (v || 0).toString(16);
						return v.length === 1 ? "0" + v : v;
					}).join("");
				},
				toString: function () {
					return this._rgba[3] === 0 ? "transparent" : this.toRgbaString();
				}
			});
			color.fn.parse.prototype = color.fn;

			// Hsla conversions adapted from:
			// https://code.google.com/p/maashaack/source/browse/packages/graphics/trunk/src/graphics/colors/HUE2RGB.as?r=5021

			function hue2rgb(p, q, h) {
				h = (h + 1) % 1;
				if (h * 6 < 1) {
					return p + (q - p) * h * 6;
				}
				if (h * 2 < 1) {
					return q;
				}
				if (h * 3 < 2) {
					return p + (q - p) * ((2 / 3) - h) * 6;
				}
				return p;
			}

			spaces.hsla.to = function (rgba) {
				if (rgba[0] == null || rgba[1] == null || rgba[2] == null) {
					return [null, null, null, rgba[3]];
				}
				var r = rgba[0] / 255,
					g = rgba[1] / 255,
					b = rgba[2] / 255,
					a = rgba[3],
					max = Math.max(r, g, b),
					min = Math.min(r, g, b),
					diff = max - min,
					add = max + min,
					l = add * 0.5,
					h, s;

				if (min === max) {
					h = 0;
				} else if (r === max) {
					h = (60 * (g - b) / diff) + 360;
				} else if (g === max) {
					h = (60 * (b - r) / diff) + 120;
				} else {
					h = (60 * (r - g) / diff) + 240;
				}

				// Chroma (diff) == 0 means greyscale which, by definition, saturation = 0%
				// otherwise, saturation is based on the ratio of chroma (diff) to lightness (add)
				if (diff === 0) {
					s = 0;
				} else if (l <= 0.5) {
					s = diff / add;
				} else {
					s = diff / (2 - add);
				}
				return [Math.round(h) % 360, s, l, a == null ? 1 : a];
			};

			spaces.hsla.from = function (hsla) {
				if (hsla[0] == null || hsla[1] == null || hsla[2] == null) {
					return [null, null, null, hsla[3]];
				}
				var h = hsla[0] / 360,
					s = hsla[1],
					l = hsla[2],
					a = hsla[3],
					q = l <= 0.5 ? l * (1 + s) : l + s - l * s,
					p = 2 * l - q;

				return [
					Math.round(hue2rgb(p, q, h + (1 / 3)) * 255),
					Math.round(hue2rgb(p, q, h) * 255),
					Math.round(hue2rgb(p, q, h - (1 / 3)) * 255),
					a
				];
			};

			each(spaces, function (spaceName, space) {
				var props = space.props,
					cache = space.cache,
					to = space.to,
					from = space.from;

				// Makes rgba() and hsla()
				color.fn[spaceName] = function (value) {

					// Generate a cache for this space if it doesn't exist
					if (to && !this[cache]) {
						this[cache] = to(this._rgba);
					}
					if (value === undefined) {
						return this[cache].slice();
					}

					var ret,
						type = jQuery.type(value),
						arr = (type === "array" || type === "object") ? value : arguments,
						local = this[cache].slice();

					each(props, function (key, prop) {
						var val = arr[type === "object" ? key : prop.idx];
						if (val == null) {
							val = local[prop.idx];
						}
						local[prop.idx] = clamp(val, prop);
					});

					if (from) {
						ret = color(from(local));
						ret[cache] = local;
						return ret;
					} else {
						return color(local);
					}
				};

				// Makes red() green() blue() alpha() hue() saturation() lightness()
				each(props, function (key, prop) {

					// Alpha is included in more than one space
					if (color.fn[key]) {
						return;
					}
					color.fn[key] = function (value) {
						var vtype = jQuery.type(value),
							fn = (key === "alpha" ? (this._hsla ? "hsla" : "rgba") : spaceName),
							local = this[fn](),
							cur = local[prop.idx],
							match;

						if (vtype === "undefined") {
							return cur;
						}

						if (vtype === "function") {
							value = value.call(this, cur);
							vtype = jQuery.type(value);
						}
						if (value == null && prop.empty) {
							return this;
						}
						if (vtype === "string") {
							match = rplusequals.exec(value);
							if (match) {
								value = cur + parseFloat(match[2]) * (match[1] === "+" ? 1 : -1);
							}
						}
						local[prop.idx] = value;
						return this[fn](local);
					};
				});
			});

			// Add cssHook and .fx.step function for each named hook.
			// accept a space separated string of properties
			color.hook = function (hook) {
				var hooks = hook.split(" ");
				each(hooks, function (i, hook) {
					jQuery.cssHooks[hook] = {
						set: function (elem, value) {
							var parsed, curElem,
								backgroundColor = "";

							if (value !== "transparent" && (jQuery.type(value) !== "string" ||
								(parsed = stringParse(value)))) {
								value = color(parsed || value);
								if (!support.rgba && value._rgba[3] !== 1) {
									curElem = hook === "backgroundColor" ? elem.parentNode : elem;
									while (
										(backgroundColor === "" || backgroundColor === "transparent") &&
										curElem && curElem.style
									) {
										try {
											backgroundColor = jQuery.css(curElem, "backgroundColor");
											curElem = curElem.parentNode;
										} catch (e) {
										}
									}

									value = value.blend(backgroundColor && backgroundColor !== "transparent" ?
										backgroundColor :
										"_default");
								}

								value = value.toRgbaString();
							}
							try {
								elem.style[hook] = value;
							} catch (e) {

								// Wrapped to prevent IE from throwing errors on "invalid" values like
								// 'auto' or 'inherit'
							}
						}
					};
					jQuery.fx.step[hook] = function (fx) {
						if (!fx.colorInit) {
							fx.start = color(fx.elem, hook);
							fx.end = color(fx.end);
							fx.colorInit = true;
						}
						jQuery.cssHooks[hook].set(fx.elem, fx.start.transition(fx.end, fx.pos));
					};
				});

			};

			color.hook(stepHooks);

			jQuery.cssHooks.borderColor = {
				expand: function (value) {
					var expanded = {};

					each(["Top", "Right", "Bottom", "Left"], function (i, part) {
						expanded["border" + part + "Color"] = value;
					});
					return expanded;
				}
			};

			// Basic color names only.
			// Usage of any of the other color names requires adding yourself or including
			// jquery.color.svg-names.js.
			colors = jQuery.Color.names = {

				// 4.1. Basic color keywords
				aqua: "#00ffff",
				black: "#000000",
				blue: "#0000ff",
				fuchsia: "#ff00ff",
				gray: "#808080",
				green: "#008000",
				lime: "#00ff00",
				maroon: "#800000",
				navy: "#000080",
				olive: "#808000",
				purple: "#800080",
				red: "#ff0000",
				silver: "#c0c0c0",
				teal: "#008080",
				white: "#ffffff",
				yellow: "#ffff00",

				// 4.2.3. "transparent" color keyword
				transparent: [null, null, null, 0],

				_default: "#ffffff"
			};

		})(jQuery);

		/******************************************************************************/
		/****************************** CLASS ANIMATIONS ******************************/
		/******************************************************************************/
		(function () {

			var classAnimationActions = ["add", "remove", "toggle"],
				shorthandStyles = {
					border: 1,
					borderBottom: 1,
					borderColor: 1,
					borderLeft: 1,
					borderRight: 1,
					borderTop: 1,
					borderWidth: 1,
					margin: 1,
					padding: 1
				};

			$.each(
				["borderLeftStyle", "borderRightStyle", "borderBottomStyle", "borderTopStyle"],
				function (_, prop) {
					$.fx.step[prop] = function (fx) {
						if (fx.end !== "none" && !fx.setAttr || fx.pos === 1 && !fx.setAttr) {
							jQuery.style(fx.elem, prop, fx.end);
							fx.setAttr = true;
						}
					};
				}
			);

			function getElementStyles(elem) {
				var key, len,
					style = elem.ownerDocument.defaultView ?
						elem.ownerDocument.defaultView.getComputedStyle(elem, null) :
						elem.currentStyle,
					styles = {};

				if (style && style.length && style[0] && style[style[0]]) {
					len = style.length;
					while (len--) {
						key = style[len];
						if (typeof style[key] === "string") {
							styles[$.camelCase(key)] = style[key];
						}
					}

					// Support: Opera, IE <9
				} else {
					for (key in style) {
						if (typeof style[key] === "string") {
							styles[key] = style[key];
						}
					}
				}

				return styles;
			}

			function styleDifference(oldStyle, newStyle) {
				var diff = {},
					name, value;

				for (name in newStyle) {
					value = newStyle[name];
					if (oldStyle[name] !== value) {
						if (!shorthandStyles[name]) {
							if ($.fx.step[name] || !isNaN(parseFloat(value))) {
								diff[name] = value;
							}
						}
					}
				}

				return diff;
			}

			// Support: jQuery <1.8
			if (!$.fn.addBack) {
				$.fn.addBack = function (selector) {
					return this.add(selector == null ?
						this.prevObject : this.prevObject.filter(selector)
					);
				};
			}

			$.effects.animateClass = function (value, duration, easing, callback) {
				var o = $.speed(duration, easing, callback);

				return this.queue(function () {
					var animated = $(this),
						baseClass = animated.attr("class") || "",
						applyClassChange,
						allAnimations = o.children ? animated.find("*").addBack() : animated;

					// Map the animated objects to store the original styles.
					allAnimations = allAnimations.map(function () {
						var el = $(this);
						return {
							el: el,
							start: getElementStyles(this)
						};
					});

					// Apply class change
					applyClassChange = function () {
						$.each(classAnimationActions, function (i, action) {
							if (value[action]) {
								animated[action + "Class"](value[action]);
							}
						});
					};
					applyClassChange();

					// Map all animated objects again - calculate new styles and diff
					allAnimations = allAnimations.map(function () {
						this.end = getElementStyles(this.el[0]);
						this.diff = styleDifference(this.start, this.end);
						return this;
					});

					// Apply original class
					animated.attr("class", baseClass);

					// Map all animated objects again - this time collecting a promise
					allAnimations = allAnimations.map(function () {
						var styleInfo = this,
							dfd = $.Deferred(),
							opts = $.extend({}, o, {
								queue: false,
								complete: function () {
									dfd.resolve(styleInfo);
								}
							});

						this.el.animate(this.diff, opts);
						return dfd.promise();
					});

					// Once all animations have completed:
					$.when.apply($, allAnimations.get()).done(function () {

						// Set the final class
						applyClassChange();

						// For each animated element,
						// clear all css properties that were animated
						$.each(arguments, function () {
							var el = this.el;
							$.each(this.diff, function (key) {
								el.css(key, "");
							});
						});

						// This is guarnteed to be there if you use jQuery.speed()
						// it also handles dequeuing the next anim...
						o.complete.call(animated[0]);
					});
				});
			};

			$.fn.extend({
				addClass: (function (orig) {
					return function (classNames, speed, easing, callback) {
						return speed ?
							$.effects.animateClass.call(this,
								{ add: classNames }, speed, easing, callback) :
							orig.apply(this, arguments);
					};
				})($.fn.addClass),

				removeClass: (function (orig) {
					return function (classNames, speed, easing, callback) {
						return arguments.length > 1 ?
							$.effects.animateClass.call(this,
								{ remove: classNames }, speed, easing, callback) :
							orig.apply(this, arguments);
					};
				})($.fn.removeClass),

				toggleClass: (function (orig) {
					return function (classNames, force, speed, easing, callback) {
						if (typeof force === "boolean" || force === undefined) {
							if (!speed) {

								// Without speed parameter
								return orig.apply(this, arguments);
							} else {
								return $.effects.animateClass.call(this,
									(force ? { add: classNames } : { remove: classNames }),
									speed, easing, callback);
							}
						} else {

							// Without force parameter
							return $.effects.animateClass.call(this,
								{ toggle: classNames }, force, speed, easing);
						}
					};
				})($.fn.toggleClass),

				switchClass: function (remove, add, speed, easing, callback) {
					return $.effects.animateClass.call(this, {
						add: add,
						remove: remove
					}, speed, easing, callback);
				}
			});

		})();

		/******************************************************************************/
		/*********************************** EFFECTS **********************************/
		/******************************************************************************/

		(function () {

			if ($.expr && $.expr.filters && $.expr.filters.animated) {
				$.expr.filters.animated = (function (orig) {
					return function (elem) {
						return !!$(elem).data(dataSpaceAnimated) || orig(elem);
					};
				})($.expr.filters.animated);
			}

			if ($.uiBackCompat !== false) {
				$.extend($.effects, {

					// Saves a set of properties in a data storage
					save: function (element, set) {
						var i = 0, length = set.length;
						for (; i < length; i++) {
							if (set[i] !== null) {
								element.data(dataSpace + set[i], element[0].style[set[i]]);
							}
						}
					},

					// Restores a set of previously saved properties from a data storage
					restore: function (element, set) {
						var val, i = 0, length = set.length;
						for (; i < length; i++) {
							if (set[i] !== null) {
								val = element.data(dataSpace + set[i]);
								element.css(set[i], val);
							}
						}
					},

					setMode: function (el, mode) {
						if (mode === "toggle") {
							mode = el.is(":hidden") ? "show" : "hide";
						}
						return mode;
					},

					// Wraps the element around a wrapper that copies position properties
					createWrapper: function (element) {

						// If the element is already wrapped, return it
						if (element.parent().is(".ui-effects-wrapper")) {
							return element.parent();
						}

						// Wrap the element
						var props = {
							width: element.outerWidth(true),
							height: element.outerHeight(true),
							"float": element.css("float")
						},
							wrapper = $("<div></div>")
								.addClass("ui-effects-wrapper")
								.css({
									fontSize: "100%",
									background: "transparent",
									border: "none",
									margin: 0,
									padding: 0
								}),

							// Store the size in case width/height are defined in % - Fixes #5245
							size = {
								width: element.width(),
								height: element.height()
							},
							active = document.activeElement;

						// Support: Firefox
						// Firefox incorrectly exposes anonymous content
						// https://bugzilla.mozilla.org/show_bug.cgi?id=561664
						try {
							active.id;
						} catch (e) {
							active = document.body;
						}

						element.wrap(wrapper);

						// Fixes #7595 - Elements lose focus when wrapped.
						if (element[0] === active || $.contains(element[0], active)) {
							$(active).trigger("focus");
						}

						// Hotfix for jQuery 1.4 since some change in wrap() seems to actually
						// lose the reference to the wrapped element
						wrapper = element.parent();

						// Transfer positioning properties to the wrapper
						if (element.css("position") === "static") {
							wrapper.css({ position: "relative" });
							element.css({ position: "relative" });
						} else {
							$.extend(props, {
								position: element.css("position"),
								zIndex: element.css("z-index")
							});
							$.each(["top", "left", "bottom", "right"], function (i, pos) {
								props[pos] = element.css(pos);
								if (isNaN(parseInt(props[pos], 10))) {
									props[pos] = "auto";
								}
							});
							element.css({
								position: "relative",
								top: 0,
								left: 0,
								right: "auto",
								bottom: "auto"
							});
						}
						element.css(size);

						return wrapper.css(props).show();
					},

					removeWrapper: function (element) {
						var active = document.activeElement;

						if (element.parent().is(".ui-effects-wrapper")) {
							element.parent().replaceWith(element);

							// Fixes #7595 - Elements lose focus when wrapped.
							if (element[0] === active || $.contains(element[0], active)) {
								$(active).trigger("focus");
							}
						}

						return element;
					}
				});
			}

			$.extend($.effects, {
				version: "1.12.1",

				define: function (name, mode, effect) {
					if (!effect) {
						effect = mode;
						mode = "effect";
					}

					$.effects.effect[name] = effect;
					$.effects.effect[name].mode = mode;

					return effect;
				},

				scaledDimensions: function (element, percent, direction) {
					if (percent === 0) {
						return {
							height: 0,
							width: 0,
							outerHeight: 0,
							outerWidth: 0
						};
					}

					var x = direction !== "horizontal" ? ((percent || 100) / 100) : 1,
						y = direction !== "vertical" ? ((percent || 100) / 100) : 1;

					return {
						height: element.height() * y,
						width: element.width() * x,
						outerHeight: element.outerHeight() * y,
						outerWidth: element.outerWidth() * x
					};

				},

				clipToBox: function (animation) {
					return {
						width: animation.clip.right - animation.clip.left,
						height: animation.clip.bottom - animation.clip.top,
						left: animation.clip.left,
						top: animation.clip.top
					};
				},

				// Injects recently queued functions to be first in line (after "inprogress")
				unshift: function (element, queueLength, count) {
					var queue = element.queue();

					if (queueLength > 1) {
						queue.splice.apply(queue,
							[1, 0].concat(queue.splice(queueLength, count)));
					}
					element.dequeue();
				},

				saveStyle: function (element) {
					element.data(dataSpaceStyle, element[0].style.cssText);
				},

				restoreStyle: function (element) {
					element[0].style.cssText = element.data(dataSpaceStyle) || "";
					element.removeData(dataSpaceStyle);
				},

				mode: function (element, mode) {
					var hidden = element.is(":hidden");

					if (mode === "toggle") {
						mode = hidden ? "show" : "hide";
					}
					if (hidden ? mode === "hide" : mode === "show") {
						mode = "none";
					}
					return mode;
				},

				// Translates a [top,left] array into a baseline value
				getBaseline: function (origin, original) {
					var y, x;

					switch (origin[0]) {
						case "top":
							y = 0;
							break;
						case "middle":
							y = 0.5;
							break;
						case "bottom":
							y = 1;
							break;
						default:
							y = origin[0] / original.height;
					}

					switch (origin[1]) {
						case "left":
							x = 0;
							break;
						case "center":
							x = 0.5;
							break;
						case "right":
							x = 1;
							break;
						default:
							x = origin[1] / original.width;
					}

					return {
						x: x,
						y: y
					};
				},

				// Creates a placeholder element so that the original element can be made absolute
				createPlaceholder: function (element) {
					var placeholder,
						cssPosition = element.css("position"),
						position = element.position();

					// Lock in margins first to account for form elements, which
					// will change margin if you explicitly set height
					// see: http://jsfiddle.net/JZSMt/3/ https://bugs.webkit.org/show_bug.cgi?id=107380
					// Support: Safari
					element.css({
						marginTop: element.css("marginTop"),
						marginBottom: element.css("marginBottom"),
						marginLeft: element.css("marginLeft"),
						marginRight: element.css("marginRight")
					})
						.outerWidth(element.outerWidth())
						.outerHeight(element.outerHeight());

					if (/^(static|relative)/.test(cssPosition)) {
						cssPosition = "absolute";

						placeholder = $("<" + element[0].nodeName + ">").insertAfter(element).css({

							// Convert inline to inline block to account for inline elements
							// that turn to inline block based on content (like img)
							display: /^(inline|ruby)/.test(element.css("display")) ?
								"inline-block" :
								"block",
							visibility: "hidden",

							// Margins need to be set to account for margin collapse
							marginTop: element.css("marginTop"),
							marginBottom: element.css("marginBottom"),
							marginLeft: element.css("marginLeft"),
							marginRight: element.css("marginRight"),
							"float": element.css("float")
						})
							.outerWidth(element.outerWidth())
							.outerHeight(element.outerHeight())
							.addClass("ui-effects-placeholder");

						element.data(dataSpace + "placeholder", placeholder);
					}

					element.css({
						position: cssPosition,
						left: position.left,
						top: position.top
					});

					return placeholder;
				},

				removePlaceholder: function (element) {
					var dataKey = dataSpace + "placeholder",
						placeholder = element.data(dataKey);

					if (placeholder) {
						placeholder.remove();
						element.removeData(dataKey);
					}
				},

				// Removes a placeholder if it exists and restores
				// properties that were modified during placeholder creation
				cleanUp: function (element) {
					$.effects.restoreStyle(element);
					$.effects.removePlaceholder(element);
				},

				setTransition: function (element, list, factor, value) {
					value = value || {};
					$.each(list, function (i, x) {
						var unit = element.cssUnit(x);
						if (unit[0] > 0) {
							value[x] = unit[0] * factor + unit[1];
						}
					});
					return value;
				}
			});

			// Return an effect options object for the given parameters:
			function _normalizeArguments(effect, options, speed, callback) {

				// Allow passing all options as the first parameter
				if ($.isPlainObject(effect)) {
					options = effect;
					effect = effect.effect;
				}

				// Convert to an object
				effect = { effect: effect };

				// Catch (effect, null, ...)
				if (options == null) {
					options = {};
				}

				// Catch (effect, callback)
				if ($.isFunction(options)) {
					callback = options;
					speed = null;
					options = {};
				}

				// Catch (effect, speed, ?)
				if (typeof options === "number" || $.fx.speeds[options]) {
					callback = speed;
					speed = options;
					options = {};
				}

				// Catch (effect, options, callback)
				if ($.isFunction(speed)) {
					callback = speed;
					speed = null;
				}

				// Add options to effect
				if (options) {
					$.extend(effect, options);
				}

				speed = speed || options.duration;
				effect.duration = $.fx.off ? 0 :
					typeof speed === "number" ? speed :
						speed in $.fx.speeds ? $.fx.speeds[speed] :
							$.fx.speeds._default;

				effect.complete = callback || options.complete;

				return effect;
			}

			function standardAnimationOption(option) {

				// Valid standard speeds (nothing, number, named speed)
				if (!option || typeof option === "number" || $.fx.speeds[option]) {
					return true;
				}

				// Invalid strings - treat as "normal" speed
				if (typeof option === "string" && !$.effects.effect[option]) {
					return true;
				}

				// Complete callback
				if ($.isFunction(option)) {
					return true;
				}

				// Options hash (but not naming an effect)
				if (typeof option === "object" && !option.effect) {
					return true;
				}

				// Didn't match any standard API
				return false;
			}

			$.fn.extend({
				effect: function ( /* effect, options, speed, callback */) {
					var args = _normalizeArguments.apply(this, arguments),
						effectMethod = $.effects.effect[args.effect],
						defaultMode = effectMethod.mode,
						queue = args.queue,
						queueName = queue || "fx",
						complete = args.complete,
						mode = args.mode,
						modes = [],
						prefilter = function (next) {
							var el = $(this),
								normalizedMode = $.effects.mode(el, mode) || defaultMode;

							// Sentinel for duck-punching the :animated psuedo-selector
							el.data(dataSpaceAnimated, true);

							// Save effect mode for later use,
							// we can't just call $.effects.mode again later,
							// as the .show() below destroys the initial state
							modes.push(normalizedMode);

							// See $.uiBackCompat inside of run() for removal of defaultMode in 1.13
							if (defaultMode && (normalizedMode === "show" ||
								(normalizedMode === defaultMode && normalizedMode === "hide"))) {
								el.show();
							}

							if (!defaultMode || normalizedMode !== "none") {
								$.effects.saveStyle(el);
							}

							if ($.isFunction(next)) {
								next();
							}
						};

					if ($.fx.off || !effectMethod) {

						// Delegate to the original method (e.g., .show()) if possible
						if (mode) {
							return this[mode](args.duration, complete);
						} else {
							return this.each(function () {
								if (complete) {
									complete.call(this);
								}
							});
						}
					}

					function run(next) {
						var elem = $(this);

						function cleanup() {
							elem.removeData(dataSpaceAnimated);

							$.effects.cleanUp(elem);

							if (args.mode === "hide") {
								elem.hide();
							}

							done();
						}

						function done() {
							if ($.isFunction(complete)) {
								complete.call(elem[0]);
							}

							if ($.isFunction(next)) {
								next();
							}
						}

						// Override mode option on a per element basis,
						// as toggle can be either show or hide depending on element state
						args.mode = modes.shift();

						if ($.uiBackCompat !== false && !defaultMode) {
							if (elem.is(":hidden") ? mode === "hide" : mode === "show") {

								// Call the core method to track "olddisplay" properly
								elem[mode]();
								done();
							} else {
								effectMethod.call(elem[0], args, done);
							}
						} else {
							if (args.mode === "none") {

								// Call the core method to track "olddisplay" properly
								elem[mode]();
								done();
							} else {
								effectMethod.call(elem[0], args, cleanup);
							}
						}
					}

					// Run prefilter on all elements first to ensure that
					// any showing or hiding happens before placeholder creation,
					// which ensures that any layout changes are correctly captured.
					return queue === false ?
						this.each(prefilter).each(run) :
						this.queue(queueName, prefilter).queue(queueName, run);
				},

				show: (function (orig) {
					return function (option) {
						if (standardAnimationOption(option)) {
							return orig.apply(this, arguments);
						} else {
							var args = _normalizeArguments.apply(this, arguments);
							args.mode = "show";
							return this.effect.call(this, args);
						}
					};
				})($.fn.show),

				hide: (function (orig) {
					return function (option) {
						if (standardAnimationOption(option)) {
							return orig.apply(this, arguments);
						} else {
							var args = _normalizeArguments.apply(this, arguments);
							args.mode = "hide";
							return this.effect.call(this, args);
						}
					};
				})($.fn.hide),

				toggle: (function (orig) {
					return function (option) {
						if (standardAnimationOption(option) || typeof option === "boolean") {
							return orig.apply(this, arguments);
						} else {
							var args = _normalizeArguments.apply(this, arguments);
							args.mode = "toggle";
							return this.effect.call(this, args);
						}
					};
				})($.fn.toggle),

				cssUnit: function (key) {
					var style = this.css(key),
						val = [];

					$.each(["em", "px", "%", "pt"], function (i, unit) {
						if (style.indexOf(unit) > 0) {
							val = [parseFloat(style), unit];
						}
					});
					return val;
				},

				cssClip: function (clipObj) {
					if (clipObj) {
						return this.css("clip", "rect(" + clipObj.top + "px " + clipObj.right + "px " +
							clipObj.bottom + "px " + clipObj.left + "px)");
					}
					return parseClip(this.css("clip"), this);
				},

				transfer: function (options, done) {
					var element = $(this),
						target = $(options.to),
						targetFixed = target.css("position") === "fixed",
						body = $("body"),
						fixTop = targetFixed ? body.scrollTop() : 0,
						fixLeft = targetFixed ? body.scrollLeft() : 0,
						endPosition = target.offset(),
						animation = {
							top: endPosition.top - fixTop,
							left: endPosition.left - fixLeft,
							height: target.innerHeight(),
							width: target.innerWidth()
						},
						startPosition = element.offset(),
						transfer = $("<div class='ui-effects-transfer'></div>")
							.appendTo("body")
							.addClass(options.className)
							.css({
								top: startPosition.top - fixTop,
								left: startPosition.left - fixLeft,
								height: element.innerHeight(),
								width: element.innerWidth(),
								position: targetFixed ? "fixed" : "absolute"
							})
							.animate(animation, options.duration, options.easing, function () {
								transfer.remove();
								if ($.isFunction(done)) {
									done();
								}
							});
				}
			});

			function parseClip(str, element) {
				var outerWidth = element.outerWidth(),
					outerHeight = element.outerHeight(),
					clipRegex = /^rect\((-?\d*\.?\d*px|-?\d+%|auto),?\s*(-?\d*\.?\d*px|-?\d+%|auto),?\s*(-?\d*\.?\d*px|-?\d+%|auto),?\s*(-?\d*\.?\d*px|-?\d+%|auto)\)$/,
					values = clipRegex.exec(str) || ["", 0, outerWidth, outerHeight, 0];

				return {
					top: parseFloat(values[1]) || 0,
					right: values[2] === "auto" ? outerWidth : parseFloat(values[2]),
					bottom: values[3] === "auto" ? outerHeight : parseFloat(values[3]),
					left: parseFloat(values[4]) || 0
				};
			}

			$.fx.step.clip = function (fx) {
				if (!fx.clipInit) {
					fx.start = $(fx.elem).cssClip();
					if (typeof fx.end === "string") {
						fx.end = parseClip(fx.end, fx.elem);
					}
					fx.clipInit = true;
				}

				$(fx.elem).cssClip({
					top: fx.pos * (fx.end.top - fx.start.top) + fx.start.top,
					right: fx.pos * (fx.end.right - fx.start.right) + fx.start.right,
					bottom: fx.pos * (fx.end.bottom - fx.start.bottom) + fx.start.bottom,
					left: fx.pos * (fx.end.left - fx.start.left) + fx.start.left
				});
			};

		})();

		/******************************************************************************/
		/*********************************** EASING ***********************************/
		/******************************************************************************/

		(function () {

			// Based on easing equations from Robert Penner (http://www.robertpenner.com/easing)

			var baseEasings = {};

			$.each(["Quad", "Cubic", "Quart", "Quint", "Expo"], function (i, name) {
				baseEasings[name] = function (p) {
					return Math.pow(p, i + 2);
				};
			});

			$.extend(baseEasings, {
				Sine: function (p) {
					return 1 - Math.cos(p * Math.PI / 2);
				},
				Circ: function (p) {
					return 1 - Math.sqrt(1 - p * p);
				},
				Elastic: function (p) {
					return p === 0 || p === 1 ? p :
						-Math.pow(2, 8 * (p - 1)) * Math.sin(((p - 1) * 80 - 7.5) * Math.PI / 15);
				},
				Back: function (p) {
					return p * p * (3 * p - 2);
				},
				Bounce: function (p) {
					var pow2,
						bounce = 4;

					while (p < ((pow2 = Math.pow(2, --bounce)) - 1) / 11) { }
					return 1 / Math.pow(4, 3 - bounce) - 7.5625 * Math.pow((pow2 * 3 - 2) / 22 - p, 2);
				}
			});

			$.each(baseEasings, function (name, easeIn) {
				$.easing["easeIn" + name] = easeIn;
				$.easing["easeOut" + name] = function (p) {
					return 1 - easeIn(1 - p);
				};
				$.easing["easeInOut" + name] = function (p) {
					return p < 0.5 ?
						easeIn(p * 2) / 2 :
						1 - easeIn(p * -2 + 2) / 2;
				};
			});

		})();

		var effect = $.effects;


		/*!
		 * jQuery UI Effects Blind 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Blind Effect
		//>>group: Effects
		//>>description: Blinds the element.
		//>>docs: http://api.jqueryui.com/blind-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectBlind = $.effects.define("blind", "hide", function (options, done) {
			var map = {
				up: ["bottom", "top"],
				vertical: ["bottom", "top"],
				down: ["top", "bottom"],
				left: ["right", "left"],
				horizontal: ["right", "left"],
				right: ["left", "right"]
			},
				element = $(this),
				direction = options.direction || "up",
				start = element.cssClip(),
				animate = { clip: $.extend({}, start) },
				placeholder = $.effects.createPlaceholder(element);

			animate.clip[map[direction][0]] = animate.clip[map[direction][1]];

			if (options.mode === "show") {
				element.cssClip(animate.clip);
				if (placeholder) {
					placeholder.css($.effects.clipToBox(animate));
				}

				animate.clip = start;
			}

			if (placeholder) {
				placeholder.animate($.effects.clipToBox(animate), options.duration, options.easing);
			}

			element.animate(animate, {
				queue: false,
				duration: options.duration,
				easing: options.easing,
				complete: done
			});
		});


		/*!
		 * jQuery UI Effects Bounce 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Bounce Effect
		//>>group: Effects
		//>>description: Bounces an element horizontally or vertically n times.
		//>>docs: http://api.jqueryui.com/bounce-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectBounce = $.effects.define("bounce", function (options, done) {
			var upAnim, downAnim, refValue,
				element = $(this),

				// Defaults:
				mode = options.mode,
				hide = mode === "hide",
				show = mode === "show",
				direction = options.direction || "up",
				distance = options.distance,
				times = options.times || 5,

				// Number of internal animations
				anims = times * 2 + (show || hide ? 1 : 0),
				speed = options.duration / anims,
				easing = options.easing,

				// Utility:
				ref = (direction === "up" || direction === "down") ? "top" : "left",
				motion = (direction === "up" || direction === "left"),
				i = 0,

				queuelen = element.queue().length;

			$.effects.createPlaceholder(element);

			refValue = element.css(ref);

			// Default distance for the BIGGEST bounce is the outer Distance / 3
			if (!distance) {
				distance = element[ref === "top" ? "outerHeight" : "outerWidth"]() / 3;
			}

			if (show) {
				downAnim = { opacity: 1 };
				downAnim[ref] = refValue;

				// If we are showing, force opacity 0 and set the initial position
				// then do the "first" animation
				element
					.css("opacity", 0)
					.css(ref, motion ? -distance * 2 : distance * 2)
					.animate(downAnim, speed, easing);
			}

			// Start at the smallest distance if we are hiding
			if (hide) {
				distance = distance / Math.pow(2, times - 1);
			}

			downAnim = {};
			downAnim[ref] = refValue;

			// Bounces up/down/left/right then back to 0 -- times * 2 animations happen here
			for (; i < times; i++) {
				upAnim = {};
				upAnim[ref] = (motion ? "-=" : "+=") + distance;

				element
					.animate(upAnim, speed, easing)
					.animate(downAnim, speed, easing);

				distance = hide ? distance * 2 : distance / 2;
			}

			// Last Bounce when Hiding
			if (hide) {
				upAnim = { opacity: 0 };
				upAnim[ref] = (motion ? "-=" : "+=") + distance;

				element.animate(upAnim, speed, easing);
			}

			element.queue(done);

			$.effects.unshift(element, queuelen, anims + 1);
		});


		/*!
		 * jQuery UI Effects Clip 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Clip Effect
		//>>group: Effects
		//>>description: Clips the element on and off like an old TV.
		//>>docs: http://api.jqueryui.com/clip-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectClip = $.effects.define("clip", "hide", function (options, done) {
			var start,
				animate = {},
				element = $(this),
				direction = options.direction || "vertical",
				both = direction === "both",
				horizontal = both || direction === "horizontal",
				vertical = both || direction === "vertical";

			start = element.cssClip();
			animate.clip = {
				top: vertical ? (start.bottom - start.top) / 2 : start.top,
				right: horizontal ? (start.right - start.left) / 2 : start.right,
				bottom: vertical ? (start.bottom - start.top) / 2 : start.bottom,
				left: horizontal ? (start.right - start.left) / 2 : start.left
			};

			$.effects.createPlaceholder(element);

			if (options.mode === "show") {
				element.cssClip(animate.clip);
				animate.clip = start;
			}

			element.animate(animate, {
				queue: false,
				duration: options.duration,
				easing: options.easing,
				complete: done
			});

		});


		/*!
		 * jQuery UI Effects Drop 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Drop Effect
		//>>group: Effects
		//>>description: Moves an element in one direction and hides it at the same time.
		//>>docs: http://api.jqueryui.com/drop-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectDrop = $.effects.define("drop", "hide", function (options, done) {

			var distance,
				element = $(this),
				mode = options.mode,
				show = mode === "show",
				direction = options.direction || "left",
				ref = (direction === "up" || direction === "down") ? "top" : "left",
				motion = (direction === "up" || direction === "left") ? "-=" : "+=",
				oppositeMotion = (motion === "+=") ? "-=" : "+=",
				animation = {
					opacity: 0
				};

			$.effects.createPlaceholder(element);

			distance = options.distance ||
				element[ref === "top" ? "outerHeight" : "outerWidth"](true) / 2;

			animation[ref] = motion + distance;

			if (show) {
				element.css(animation);

				animation[ref] = oppositeMotion + distance;
				animation.opacity = 1;
			}

			// Animate
			element.animate(animation, {
				queue: false,
				duration: options.duration,
				easing: options.easing,
				complete: done
			});
		});


		/*!
		 * jQuery UI Effects Explode 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Explode Effect
		//>>group: Effects
		// jscs:disable maximumLineLength
		//>>description: Explodes an element in all directions into n pieces. Implodes an element to its original wholeness.
		// jscs:enable maximumLineLength
		//>>docs: http://api.jqueryui.com/explode-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectExplode = $.effects.define("explode", "hide", function (options, done) {

			var i, j, left, top, mx, my,
				rows = options.pieces ? Math.round(Math.sqrt(options.pieces)) : 3,
				cells = rows,
				element = $(this),
				mode = options.mode,
				show = mode === "show",

				// Show and then visibility:hidden the element before calculating offset
				offset = element.show().css("visibility", "hidden").offset(),

				// Width and height of a piece
				width = Math.ceil(element.outerWidth() / cells),
				height = Math.ceil(element.outerHeight() / rows),
				pieces = [];

			// Children animate complete:
			function childComplete() {
				pieces.push(this);
				if (pieces.length === rows * cells) {
					animComplete();
				}
			}

			// Clone the element for each row and cell.
			for (i = 0; i < rows; i++) { // ===>
				top = offset.top + i * height;
				my = i - (rows - 1) / 2;

				for (j = 0; j < cells; j++) { // |||
					left = offset.left + j * width;
					mx = j - (cells - 1) / 2;

					// Create a clone of the now hidden main element that will be absolute positioned
					// within a wrapper div off the -left and -top equal to size of our pieces
					element
						.clone()
						.appendTo("body")
						.wrap("<div></div>")
						.css({
							position: "absolute",
							visibility: "visible",
							left: -j * width,
							top: -i * height
						})

						// Select the wrapper - make it overflow: hidden and absolute positioned based on
						// where the original was located +left and +top equal to the size of pieces
						.parent()
						.addClass("ui-effects-explode")
						.css({
							position: "absolute",
							overflow: "hidden",
							width: width,
							height: height,
							left: left + (show ? mx * width : 0),
							top: top + (show ? my * height : 0),
							opacity: show ? 0 : 1
						})
						.animate({
							left: left + (show ? 0 : mx * width),
							top: top + (show ? 0 : my * height),
							opacity: show ? 1 : 0
						}, options.duration || 500, options.easing, childComplete);
				}
			}

			function animComplete() {
				element.css({
					visibility: "visible"
				});
				$(pieces).remove();
				done();
			}
		});


		/*!
		 * jQuery UI Effects Fade 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Fade Effect
		//>>group: Effects
		//>>description: Fades the element.
		//>>docs: http://api.jqueryui.com/fade-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectFade = $.effects.define("fade", "toggle", function (options, done) {
			var show = options.mode === "show";

			$(this)
				.css("opacity", show ? 0 : 1)
				.animate({
					opacity: show ? 1 : 0
				}, {
					queue: false,
					duration: options.duration,
					easing: options.easing,
					complete: done
				});
		});


		/*!
		 * jQuery UI Effects Fold 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Fold Effect
		//>>group: Effects
		//>>description: Folds an element first horizontally and then vertically.
		//>>docs: http://api.jqueryui.com/fold-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectFold = $.effects.define("fold", "hide", function (options, done) {

			// Create element
			var element = $(this),
				mode = options.mode,
				show = mode === "show",
				hide = mode === "hide",
				size = options.size || 15,
				percent = /([0-9]+)%/.exec(size),
				horizFirst = !!options.horizFirst,
				ref = horizFirst ? ["right", "bottom"] : ["bottom", "right"],
				duration = options.duration / 2,

				placeholder = $.effects.createPlaceholder(element),

				start = element.cssClip(),
				animation1 = { clip: $.extend({}, start) },
				animation2 = { clip: $.extend({}, start) },

				distance = [start[ref[0]], start[ref[1]]],

				queuelen = element.queue().length;

			if (percent) {
				size = parseInt(percent[1], 10) / 100 * distance[hide ? 0 : 1];
			}
			animation1.clip[ref[0]] = size;
			animation2.clip[ref[0]] = size;
			animation2.clip[ref[1]] = 0;

			if (show) {
				element.cssClip(animation2.clip);
				if (placeholder) {
					placeholder.css($.effects.clipToBox(animation2));
				}

				animation2.clip = start;
			}

			// Animate
			element
				.queue(function (next) {
					if (placeholder) {
						placeholder
							.animate($.effects.clipToBox(animation1), duration, options.easing)
							.animate($.effects.clipToBox(animation2), duration, options.easing);
					}

					next();
				})
				.animate(animation1, duration, options.easing)
				.animate(animation2, duration, options.easing)
				.queue(done);

			$.effects.unshift(element, queuelen, 4);
		});


		/*!
		 * jQuery UI Effects Highlight 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Highlight Effect
		//>>group: Effects
		//>>description: Highlights the background of an element in a defined color for a custom duration.
		//>>docs: http://api.jqueryui.com/highlight-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectHighlight = $.effects.define("highlight", "show", function (options, done) {
			var element = $(this),
				animation = {
					backgroundColor: element.css("backgroundColor")
				};

			if (options.mode === "hide") {
				animation.opacity = 0;
			}

			$.effects.saveStyle(element);

			element
				.css({
					backgroundImage: "none",
					backgroundColor: options.color || "#ffff99"
				})
				.animate(animation, {
					queue: false,
					duration: options.duration,
					easing: options.easing,
					complete: done
				});
		});


		/*!
		 * jQuery UI Effects Size 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Size Effect
		//>>group: Effects
		//>>description: Resize an element to a specified width and height.
		//>>docs: http://api.jqueryui.com/size-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectSize = $.effects.define("size", function (options, done) {

			// Create element
			var baseline, factor, temp,
				element = $(this),

				// Copy for children
				cProps = ["fontSize"],
				vProps = ["borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom"],
				hProps = ["borderLeftWidth", "borderRightWidth", "paddingLeft", "paddingRight"],

				// Set options
				mode = options.mode,
				restore = mode !== "effect",
				scale = options.scale || "both",
				origin = options.origin || ["middle", "center"],
				position = element.css("position"),
				pos = element.position(),
				original = $.effects.scaledDimensions(element),
				from = options.from || original,
				to = options.to || $.effects.scaledDimensions(element, 0);

			$.effects.createPlaceholder(element);

			if (mode === "show") {
				temp = from;
				from = to;
				to = temp;
			}

			// Set scaling factor
			factor = {
				from: {
					y: from.height / original.height,
					x: from.width / original.width
				},
				to: {
					y: to.height / original.height,
					x: to.width / original.width
				}
			};

			// Scale the css box
			if (scale === "box" || scale === "both") {

				// Vertical props scaling
				if (factor.from.y !== factor.to.y) {
					from = $.effects.setTransition(element, vProps, factor.from.y, from);
					to = $.effects.setTransition(element, vProps, factor.to.y, to);
				}

				// Horizontal props scaling
				if (factor.from.x !== factor.to.x) {
					from = $.effects.setTransition(element, hProps, factor.from.x, from);
					to = $.effects.setTransition(element, hProps, factor.to.x, to);
				}
			}

			// Scale the content
			if (scale === "content" || scale === "both") {

				// Vertical props scaling
				if (factor.from.y !== factor.to.y) {
					from = $.effects.setTransition(element, cProps, factor.from.y, from);
					to = $.effects.setTransition(element, cProps, factor.to.y, to);
				}
			}

			// Adjust the position properties based on the provided origin points
			if (origin) {
				baseline = $.effects.getBaseline(origin, original);
				from.top = (original.outerHeight - from.outerHeight) * baseline.y + pos.top;
				from.left = (original.outerWidth - from.outerWidth) * baseline.x + pos.left;
				to.top = (original.outerHeight - to.outerHeight) * baseline.y + pos.top;
				to.left = (original.outerWidth - to.outerWidth) * baseline.x + pos.left;
			}
			element.css(from);

			// Animate the children if desired
			if (scale === "content" || scale === "both") {

				vProps = vProps.concat(["marginTop", "marginBottom"]).concat(cProps);
				hProps = hProps.concat(["marginLeft", "marginRight"]);

				// Only animate children with width attributes specified
				// TODO: is this right? should we include anything with css width specified as well
				element.find("*[width]").each(function () {
					var child = $(this),
						childOriginal = $.effects.scaledDimensions(child),
						childFrom = {
							height: childOriginal.height * factor.from.y,
							width: childOriginal.width * factor.from.x,
							outerHeight: childOriginal.outerHeight * factor.from.y,
							outerWidth: childOriginal.outerWidth * factor.from.x
						},
						childTo = {
							height: childOriginal.height * factor.to.y,
							width: childOriginal.width * factor.to.x,
							outerHeight: childOriginal.height * factor.to.y,
							outerWidth: childOriginal.width * factor.to.x
						};

					// Vertical props scaling
					if (factor.from.y !== factor.to.y) {
						childFrom = $.effects.setTransition(child, vProps, factor.from.y, childFrom);
						childTo = $.effects.setTransition(child, vProps, factor.to.y, childTo);
					}

					// Horizontal props scaling
					if (factor.from.x !== factor.to.x) {
						childFrom = $.effects.setTransition(child, hProps, factor.from.x, childFrom);
						childTo = $.effects.setTransition(child, hProps, factor.to.x, childTo);
					}

					if (restore) {
						$.effects.saveStyle(child);
					}

					// Animate children
					child.css(childFrom);
					child.animate(childTo, options.duration, options.easing, function () {

						// Restore children
						if (restore) {
							$.effects.restoreStyle(child);
						}
					});
				});
			}

			// Animate
			element.animate(to, {
				queue: false,
				duration: options.duration,
				easing: options.easing,
				complete: function () {

					var offset = element.offset();

					if (to.opacity === 0) {
						element.css("opacity", from.opacity);
					}

					if (!restore) {
						element
							.css("position", position === "static" ? "relative" : position)
							.offset(offset);

						// Need to save style here so that automatic style restoration
						// doesn't restore to the original styles from before the animation.
						$.effects.saveStyle(element);
					}

					done();
				}
			});

		});


		/*!
		 * jQuery UI Effects Scale 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Scale Effect
		//>>group: Effects
		//>>description: Grows or shrinks an element and its content.
		//>>docs: http://api.jqueryui.com/scale-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectScale = $.effects.define("scale", function (options, done) {

			// Create element
			var el = $(this),
				mode = options.mode,
				percent = parseInt(options.percent, 10) ||
					(parseInt(options.percent, 10) === 0 ? 0 : (mode !== "effect" ? 0 : 100)),

				newOptions = $.extend(true, {
					from: $.effects.scaledDimensions(el),
					to: $.effects.scaledDimensions(el, percent, options.direction || "both"),
					origin: options.origin || ["middle", "center"]
				}, options);

			// Fade option to support puff
			if (options.fade) {
				newOptions.from.opacity = 1;
				newOptions.to.opacity = 0;
			}

			$.effects.effect.size.call(this, newOptions, done);
		});


		/*!
		 * jQuery UI Effects Puff 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Puff Effect
		//>>group: Effects
		//>>description: Creates a puff effect by scaling the element up and hiding it at the same time.
		//>>docs: http://api.jqueryui.com/puff-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectPuff = $.effects.define("puff", "hide", function (options, done) {
			var newOptions = $.extend(true, {}, options, {
				fade: true,
				percent: parseInt(options.percent, 10) || 150
			});

			$.effects.effect.scale.call(this, newOptions, done);
		});


		/*!
		 * jQuery UI Effects Pulsate 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Pulsate Effect
		//>>group: Effects
		//>>description: Pulsates an element n times by changing the opacity to zero and back.
		//>>docs: http://api.jqueryui.com/pulsate-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectPulsate = $.effects.define("pulsate", "show", function (options, done) {
			var element = $(this),
				mode = options.mode,
				show = mode === "show",
				hide = mode === "hide",
				showhide = show || hide,

				// Showing or hiding leaves off the "last" animation
				anims = ((options.times || 5) * 2) + (showhide ? 1 : 0),
				duration = options.duration / anims,
				animateTo = 0,
				i = 1,
				queuelen = element.queue().length;

			if (show || !element.is(":visible")) {
				element.css("opacity", 0).show();
				animateTo = 1;
			}

			// Anims - 1 opacity "toggles"
			for (; i < anims; i++) {
				element.animate({ opacity: animateTo }, duration, options.easing);
				animateTo = 1 - animateTo;
			}

			element.animate({ opacity: animateTo }, duration, options.easing);

			element.queue(done);

			$.effects.unshift(element, queuelen, anims + 1);
		});


		/*!
		 * jQuery UI Effects Shake 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Shake Effect
		//>>group: Effects
		//>>description: Shakes an element horizontally or vertically n times.
		//>>docs: http://api.jqueryui.com/shake-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectShake = $.effects.define("shake", function (options, done) {

			var i = 1,
				element = $(this),
				direction = options.direction || "left",
				distance = options.distance || 20,
				times = options.times || 3,
				anims = times * 2 + 1,
				speed = Math.round(options.duration / anims),
				ref = (direction === "up" || direction === "down") ? "top" : "left",
				positiveMotion = (direction === "up" || direction === "left"),
				animation = {},
				animation1 = {},
				animation2 = {},

				queuelen = element.queue().length;

			$.effects.createPlaceholder(element);

			// Animation
			animation[ref] = (positiveMotion ? "-=" : "+=") + distance;
			animation1[ref] = (positiveMotion ? "+=" : "-=") + distance * 2;
			animation2[ref] = (positiveMotion ? "-=" : "+=") + distance * 2;

			// Animate
			element.animate(animation, speed, options.easing);

			// Shakes
			for (; i < times; i++) {
				element
					.animate(animation1, speed, options.easing)
					.animate(animation2, speed, options.easing);
			}

			element
				.animate(animation1, speed, options.easing)
				.animate(animation, speed / 2, options.easing)
				.queue(done);

			$.effects.unshift(element, queuelen, anims + 1);
		});


		/*!
		 * jQuery UI Effects Slide 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Slide Effect
		//>>group: Effects
		//>>description: Slides an element in and out of the viewport.
		//>>docs: http://api.jqueryui.com/slide-effect/
		//>>demos: http://jqueryui.com/effect/



		var effectsEffectSlide = $.effects.define("slide", "show", function (options, done) {
			var startClip, startRef,
				element = $(this),
				map = {
					up: ["bottom", "top"],
					down: ["top", "bottom"],
					left: ["right", "left"],
					right: ["left", "right"]
				},
				mode = options.mode,
				direction = options.direction || "left",
				ref = (direction === "up" || direction === "down") ? "top" : "left",
				positiveMotion = (direction === "up" || direction === "left"),
				distance = options.distance ||
					element[ref === "top" ? "outerHeight" : "outerWidth"](true),
				animation = {};

			$.effects.createPlaceholder(element);

			startClip = element.cssClip();
			startRef = element.position()[ref];

			// Define hide animation
			animation[ref] = (positiveMotion ? -1 : 1) * distance + startRef;
			animation.clip = element.cssClip();
			animation.clip[map[direction][1]] = animation.clip[map[direction][0]];

			// Reverse the animation if we're showing
			if (mode === "show") {
				element.cssClip(animation.clip);
				element.css(ref, animation[ref]);
				animation.clip = startClip;
				animation[ref] = startRef;
			}

			// Actually animate
			element.animate(animation, {
				queue: false,
				duration: options.duration,
				easing: options.easing,
				complete: done
			});
		});


		/*!
		 * jQuery UI Effects Transfer 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Transfer Effect
		//>>group: Effects
		//>>description: Displays a transfer effect from one element to another.
		//>>docs: http://api.jqueryui.com/transfer-effect/
		//>>demos: http://jqueryui.com/effect/



		var effect;
		if ($.uiBackCompat !== false) {
			effect = $.effects.define("transfer", function (options, done) {
				$(this).transfer(options, done);
			});
		}
		var effectsEffectTransfer = effect;


		/*!
		 * jQuery UI Focusable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: :focusable Selector
		//>>group: Core
		//>>description: Selects elements which can be focused.
		//>>docs: http://api.jqueryui.com/focusable-selector/



		// Selectors
		$.ui.focusable = function (element, hasTabindex) {
			var map, mapName, img, focusableIfVisible, fieldset,
				nodeName = element.nodeName.toLowerCase();

			if ("area" === nodeName) {
				map = element.parentNode;
				mapName = map.name;
				if (!element.href || !mapName || map.nodeName.toLowerCase() !== "map") {
					return false;
				}
				img = $("img[usemap='#" + mapName + "']");
				return img.length > 0 && img.is(":visible");
			}

			if (/^(input|select|textarea|button|object)$/.test(nodeName)) {
				focusableIfVisible = !element.disabled;

				if (focusableIfVisible) {

					// Form controls within a disabled fieldset are disabled.
					// However, controls within the fieldset's legend do not get disabled.
					// Since controls generally aren't placed inside legends, we skip
					// this portion of the check.
					fieldset = $(element).closest("fieldset")[0];
					if (fieldset) {
						focusableIfVisible = !fieldset.disabled;
					}
				}
			} else if ("a" === nodeName) {
				focusableIfVisible = element.href || hasTabindex;
			} else {
				focusableIfVisible = hasTabindex;
			}

			return focusableIfVisible && $(element).is(":visible") && visible($(element));
		};

		// Support: IE 8 only
		// IE 8 doesn't resolve inherit to visible/hidden for computed values
		function visible(element) {
			var visibility = element.css("visibility");
			while (visibility === "inherit") {
				element = element.parent();
				visibility = element.css("visibility");
			}
			return visibility !== "hidden";
		}

		$.extend($.expr[":"], {
			focusable: function (element) {
				return $.ui.focusable(element, $.attr(element, "tabindex") != null);
			}
		});

		var focusable = $.ui.focusable;




		// Support: IE8 Only
		// IE8 does not support the form attribute and when it is supplied. It overwrites the form prop
		// with a string, so we need to find the proper form.
		var form = $.fn.form = function () {
			return typeof this[0].form === "string" ? this.closest("form") : $(this[0].form);
		};


		/*!
		 * jQuery UI Form Reset Mixin 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Form Reset Mixin
		//>>group: Core
		//>>description: Refresh input widgets when their form is reset
		//>>docs: http://api.jqueryui.com/form-reset-mixin/



		var formResetMixin = $.ui.formResetMixin = {
			_formResetHandler: function () {
				var form = $(this);

				// Wait for the form reset to actually happen before refreshing
				setTimeout(function () {
					var instances = form.data("ui-form-reset-instances");
					$.each(instances, function () {
						this.refresh();
					});
				});
			},

			_bindFormResetHandler: function () {
				this.form = this.element.form();
				if (!this.form.length) {
					return;
				}

				var instances = this.form.data("ui-form-reset-instances") || [];
				if (!instances.length) {

					// We don't use _on() here because we use a single event handler per form
					this.form.on("reset.ui-form-reset", this._formResetHandler);
				}
				instances.push(this);
				this.form.data("ui-form-reset-instances", instances);
			},

			_unbindFormResetHandler: function () {
				if (!this.form.length) {
					return;
				}

				var instances = this.form.data("ui-form-reset-instances");
				instances.splice($.inArray(this, instances), 1);
				if (instances.length) {
					this.form.data("ui-form-reset-instances", instances);
				} else {
					this.form
						.removeData("ui-form-reset-instances")
						.off("reset.ui-form-reset");
				}
			}
		};


		/*!
		 * jQuery UI Support for jQuery core 1.7.x 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 *
		 */

		//>>label: jQuery 1.7 Support
		//>>group: Core
		//>>description: Support version 1.7.x of jQuery core



		// Support: jQuery 1.7 only
		// Not a great way to check versions, but since we only support 1.7+ and only
		// need to detect <1.8, this is a simple check that should suffice. Checking
		// for "1.7." would be a bit safer, but the version string is 1.7, not 1.7.0
		// and we'll never reach 1.70.0 (if we do, we certainly won't be supporting
		// 1.7 anymore). See #11197 for why we're not using feature detection.
		if ($.fn.jquery.substring(0, 3) === "1.7") {

			// Setters for .innerWidth(), .innerHeight(), .outerWidth(), .outerHeight()
			// Unlike jQuery Core 1.8+, these only support numeric values to set the
			// dimensions in pixels
			$.each(["Width", "Height"], function (i, name) {
				var side = name === "Width" ? ["Left", "Right"] : ["Top", "Bottom"],
					type = name.toLowerCase(),
					orig = {
						innerWidth: $.fn.innerWidth,
						innerHeight: $.fn.innerHeight,
						outerWidth: $.fn.outerWidth,
						outerHeight: $.fn.outerHeight
					};

				function reduce(elem, size, border, margin) {
					$.each(side, function () {
						size -= parseFloat($.css(elem, "padding" + this)) || 0;
						if (border) {
							size -= parseFloat($.css(elem, "border" + this + "Width")) || 0;
						}
						if (margin) {
							size -= parseFloat($.css(elem, "margin" + this)) || 0;
						}
					});
					return size;
				}

				$.fn["inner" + name] = function (size) {
					if (size === undefined) {
						return orig["inner" + name].call(this);
					}

					return this.each(function () {
						$(this).css(type, reduce(this, size) + "px");
					});
				};

				$.fn["outer" + name] = function (size, margin) {
					if (typeof size !== "number") {
						return orig["outer" + name].call(this, size);
					}

					return this.each(function () {
						$(this).css(type, reduce(this, size, true, margin) + "px");
					});
				};
			});

			$.fn.addBack = function (selector) {
				return this.add(selector == null ?
					this.prevObject : this.prevObject.filter(selector)
				);
			};
		}

		;
		/*!
		 * jQuery UI Keycode 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Keycode
		//>>group: Core
		//>>description: Provide keycodes as keynames
		//>>docs: http://api.jqueryui.com/jQuery.ui.keyCode/


		var keycode = $.ui.keyCode = {
			BACKSPACE: 8,
			COMMA: 188,
			DELETE: 46,
			DOWN: 40,
			END: 35,
			ENTER: 13,
			ESCAPE: 27,
			HOME: 36,
			LEFT: 37,
			PAGE_DOWN: 34,
			PAGE_UP: 33,
			PERIOD: 190,
			RIGHT: 39,
			SPACE: 32,
			TAB: 9,
			UP: 38
		};




		// Internal use only
		var escapeSelector = $.ui.escapeSelector = (function () {
			var selectorEscape = /([!"#$%&'()*+,./:;<=>?@[\]^`{|}~])/g;
			return function (selector) {
				return selector.replace(selectorEscape, "\\$1");
			};
		})();


		/*!
		 * jQuery UI Labels 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: labels
		//>>group: Core
		//>>description: Find all the labels associated with a given input
		//>>docs: http://api.jqueryui.com/labels/



		var labels = $.fn.labels = function () {
			var ancestor, selector, id, labels, ancestors;

			// Check control.labels first
			if (this[0].labels && this[0].labels.length) {
				return this.pushStack(this[0].labels);
			}

			// Support: IE <= 11, FF <= 37, Android <= 2.3 only
			// Above browsers do not support control.labels. Everything below is to support them
			// as well as document fragments. control.labels does not work on document fragments
			labels = this.eq(0).parents("label");

			// Look for the label based on the id
			id = this.attr("id");
			if (id) {

				// We don't search against the document in case the element
				// is disconnected from the DOM
				ancestor = this.eq(0).parents().last();

				// Get a full set of top level ancestors
				ancestors = ancestor.add(ancestor.length ? ancestor.siblings() : this.siblings());

				// Create a selector for the label based on the id
				selector = "label[for='" + $.ui.escapeSelector(id) + "']";

				labels = labels.add(ancestors.find(selector).addBack(selector));

			}

			// Return whatever we have found for labels
			return this.pushStack(labels);
		};


		/*!
		 * jQuery UI Scroll Parent 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: scrollParent
		//>>group: Core
		//>>description: Get the closest ancestor element that is scrollable.
		//>>docs: http://api.jqueryui.com/scrollParent/



		var scrollParent = $.fn.scrollParent = function (includeHidden) {
			var position = this.css("position"),
				excludeStaticParent = position === "absolute",
				overflowRegex = includeHidden ? /(auto|scroll|hidden)/ : /(auto|scroll)/,
				scrollParent = this.parents().filter(function () {
					var parent = $(this);
					if (excludeStaticParent && parent.css("position") === "static") {
						return false;
					}
					return overflowRegex.test(parent.css("overflow") + parent.css("overflow-y") +
						parent.css("overflow-x"));
				}).eq(0);

			return position === "fixed" || !scrollParent.length ?
				$(this[0].ownerDocument || document) :
				scrollParent;
		};


		/*!
		 * jQuery UI Tabbable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: :tabbable Selector
		//>>group: Core
		//>>description: Selects elements which can be tabbed to.
		//>>docs: http://api.jqueryui.com/tabbable-selector/



		var tabbable = $.extend($.expr[":"], {
			tabbable: function (element) {
				var tabIndex = $.attr(element, "tabindex"),
					hasTabindex = tabIndex != null;
				return (!hasTabindex || tabIndex >= 0) && $.ui.focusable(element, hasTabindex);
			}
		});


		/*!
		 * jQuery UI Unique ID 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: uniqueId
		//>>group: Core
		//>>description: Functions to generate and remove uniqueId's
		//>>docs: http://api.jqueryui.com/uniqueId/



		var uniqueId = $.fn.extend({
			uniqueId: (function () {
				var uuid = 0;

				return function () {
					return this.each(function () {
						if (!this.id) {
							this.id = "ui-id-" + (++uuid);
						}
					});
				};
			})(),

			removeUniqueId: function () {
				return this.each(function () {
					if (/^ui-id-\d+$/.test(this.id)) {
						$(this).removeAttr("id");
					}
				});
			}
		});


		/*!
		 * jQuery UI Accordion 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Accordion
		//>>group: Widgets
		// jscs:disable maximumLineLength
		//>>description: Displays collapsible content panels for presenting information in a limited amount of space.
		// jscs:enable maximumLineLength
		//>>docs: http://api.jqueryui.com/accordion/
		//>>demos: http://jqueryui.com/accordion/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/accordion.css
		//>>css.theme: ../../themes/base/theme.css



		var widgetsAccordion = $.widget("ui.accordion", {
			version: "1.12.1",
			options: {
				active: 0,
				animate: {},
				classes: {
					"ui-accordion-header": "ui-corner-top",
					"ui-accordion-header-collapsed": "ui-corner-all",
					"ui-accordion-content": "ui-corner-bottom"
				},
				collapsible: false,
				event: "click",
				header: "> li > :first-child, > :not(li):even",
				heightStyle: "auto",
				icons: {
					activeHeader: "ui-icon-triangle-1-s",
					header: "ui-icon-triangle-1-e"
				},

				// Callbacks
				activate: null,
				beforeActivate: null
			},

			hideProps: {
				borderTopWidth: "hide",
				borderBottomWidth: "hide",
				paddingTop: "hide",
				paddingBottom: "hide",
				height: "hide"
			},

			showProps: {
				borderTopWidth: "show",
				borderBottomWidth: "show",
				paddingTop: "show",
				paddingBottom: "show",
				height: "show"
			},

			_create: function () {
				var options = this.options;

				this.prevShow = this.prevHide = $();
				this._addClass("ui-accordion", "ui-widget ui-helper-reset");
				this.element.attr("role", "tablist");

				// Don't allow collapsible: false and active: false / null
				if (!options.collapsible && (options.active === false || options.active == null)) {
					options.active = 0;
				}

				this._processPanels();

				// handle negative values
				if (options.active < 0) {
					options.active += this.headers.length;
				}
				this._refresh();
			},

			_getCreateEventData: function () {
				return {
					header: this.active,
					panel: !this.active.length ? $() : this.active.next()
				};
			},

			_createIcons: function () {
				var icon, children,
					icons = this.options.icons;

				if (icons) {
					icon = $("<span>");
					this._addClass(icon, "ui-accordion-header-icon", "ui-icon " + icons.header);
					icon.prependTo(this.headers);
					children = this.active.children(".ui-accordion-header-icon");
					this._removeClass(children, icons.header)
						._addClass(children, null, icons.activeHeader)
						._addClass(this.headers, "ui-accordion-icons");
				}
			},

			_destroyIcons: function () {
				this._removeClass(this.headers, "ui-accordion-icons");
				this.headers.children(".ui-accordion-header-icon").remove();
			},

			_destroy: function () {
				var contents;

				// Clean up main element
				this.element.removeAttr("role");

				// Clean up headers
				this.headers
					.removeAttr("role aria-expanded aria-selected aria-controls tabIndex")
					.removeUniqueId();

				this._destroyIcons();

				// Clean up content panels
				contents = this.headers.next()
					.css("display", "")
					.removeAttr("role aria-hidden aria-labelledby")
					.removeUniqueId();

				if (this.options.heightStyle !== "content") {
					contents.css("height", "");
				}
			},

			_setOption: function (key, value) {
				if (key === "active") {

					// _activate() will handle invalid values and update this.options
					this._activate(value);
					return;
				}

				if (key === "event") {
					if (this.options.event) {
						this._off(this.headers, this.options.event);
					}
					this._setupEvents(value);
				}

				this._super(key, value);

				// Setting collapsible: false while collapsed; open first panel
				if (key === "collapsible" && !value && this.options.active === false) {
					this._activate(0);
				}

				if (key === "icons") {
					this._destroyIcons();
					if (value) {
						this._createIcons();
					}
				}
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this.element.attr("aria-disabled", value);

				// Support: IE8 Only
				// #5332 / #6059 - opacity doesn't cascade to positioned elements in IE
				// so we need to add the disabled class to the headers and panels
				this._toggleClass(null, "ui-state-disabled", !!value);
				this._toggleClass(this.headers.add(this.headers.next()), null, "ui-state-disabled",
					!!value);
			},

			_keydown: function (event) {
				if (event.altKey || event.ctrlKey) {
					return;
				}

				var keyCode = $.ui.keyCode,
					length = this.headers.length,
					currentIndex = this.headers.index(event.target),
					toFocus = false;

				switch (event.keyCode) {
					case keyCode.RIGHT:
					case keyCode.DOWN:
						toFocus = this.headers[(currentIndex + 1) % length];
						break;
					case keyCode.LEFT:
					case keyCode.UP:
						toFocus = this.headers[(currentIndex - 1 + length) % length];
						break;
					case keyCode.SPACE:
					case keyCode.ENTER:
						this._eventHandler(event);
						break;
					case keyCode.HOME:
						toFocus = this.headers[0];
						break;
					case keyCode.END:
						toFocus = this.headers[length - 1];
						break;
				}

				if (toFocus) {
					$(event.target).attr("tabIndex", -1);
					$(toFocus).attr("tabIndex", 0);
					$(toFocus).trigger("focus");
					event.preventDefault();
				}
			},

			_panelKeyDown: function (event) {
				if (event.keyCode === $.ui.keyCode.UP && event.ctrlKey) {
					$(event.currentTarget).prev().trigger("focus");
				}
			},

			refresh: function () {
				var options = this.options;
				this._processPanels();

				// Was collapsed or no panel
				if ((options.active === false && options.collapsible === true) ||
					!this.headers.length) {
					options.active = false;
					this.active = $();

					// active false only when collapsible is true
				} else if (options.active === false) {
					this._activate(0);

					// was active, but active panel is gone
				} else if (this.active.length && !$.contains(this.element[0], this.active[0])) {

					// all remaining panel are disabled
					if (this.headers.length === this.headers.find(".ui-state-disabled").length) {
						options.active = false;
						this.active = $();

						// activate previous panel
					} else {
						this._activate(Math.max(0, options.active - 1));
					}

					// was active, active panel still exists
				} else {

					// make sure active index is correct
					options.active = this.headers.index(this.active);
				}

				this._destroyIcons();

				this._refresh();
			},

			_processPanels: function () {
				var prevHeaders = this.headers,
					prevPanels = this.panels;

				this.headers = this.element.find(this.options.header);
				this._addClass(this.headers, "ui-accordion-header ui-accordion-header-collapsed",
					"ui-state-default");

				this.panels = this.headers.next().filter(":not(.ui-accordion-content-active)").hide();
				this._addClass(this.panels, "ui-accordion-content", "ui-helper-reset ui-widget-content");

				// Avoid memory leaks (#10056)
				if (prevPanels) {
					this._off(prevHeaders.not(this.headers));
					this._off(prevPanels.not(this.panels));
				}
			},

			_refresh: function () {
				var maxHeight,
					options = this.options,
					heightStyle = options.heightStyle,
					parent = this.element.parent();

				this.active = this._findActive(options.active);
				this._addClass(this.active, "ui-accordion-header-active", "ui-state-active")
					._removeClass(this.active, "ui-accordion-header-collapsed");
				this._addClass(this.active.next(), "ui-accordion-content-active");
				this.active.next().show();

				this.headers
					.attr("role", "tab")
					.each(function () {
						var header = $(this),
							headerId = header.uniqueId().attr("id"),
							panel = header.next(),
							panelId = panel.uniqueId().attr("id");
						header.attr("aria-controls", panelId);
						panel.attr("aria-labelledby", headerId);
					})
					.next()
					.attr("role", "tabpanel");

				this.headers
					.not(this.active)
					.attr({
						"aria-selected": "false",
						"aria-expanded": "false",
						tabIndex: -1
					})
					.next()
					.attr({
						"aria-hidden": "true"
					})
					.hide();

				// Make sure at least one header is in the tab order
				if (!this.active.length) {
					this.headers.eq(0).attr("tabIndex", 0);
				} else {
					this.active.attr({
						"aria-selected": "true",
						"aria-expanded": "true",
						tabIndex: 0
					})
						.next()
						.attr({
							"aria-hidden": "false"
						});
				}

				this._createIcons();

				this._setupEvents(options.event);

				if (heightStyle === "fill") {
					maxHeight = parent.height();
					this.element.siblings(":visible").each(function () {
						var elem = $(this),
							position = elem.css("position");

						if (position === "absolute" || position === "fixed") {
							return;
						}
						maxHeight -= elem.outerHeight(true);
					});

					this.headers.each(function () {
						maxHeight -= $(this).outerHeight(true);
					});

					this.headers.next()
						.each(function () {
							$(this).height(Math.max(0, maxHeight -
								$(this).innerHeight() + $(this).height()));
						})
						.css("overflow", "auto");
				} else if (heightStyle === "auto") {
					maxHeight = 0;
					this.headers.next()
						.each(function () {
							var isVisible = $(this).is(":visible");
							if (!isVisible) {
								$(this).show();
							}
							maxHeight = Math.max(maxHeight, $(this).css("height", "").height());
							if (!isVisible) {
								$(this).hide();
							}
						})
						.height(maxHeight);
				}
			},

			_activate: function (index) {
				var active = this._findActive(index)[0];

				// Trying to activate the already active panel
				if (active === this.active[0]) {
					return;
				}

				// Trying to collapse, simulate a click on the currently active header
				active = active || this.active[0];

				this._eventHandler({
					target: active,
					currentTarget: active,
					preventDefault: $.noop
				});
			},

			_findActive: function (selector) {
				return typeof selector === "number" ? this.headers.eq(selector) : $();
			},

			_setupEvents: function (event) {
				var events = {
					keydown: "_keydown"
				};
				if (event) {
					$.each(event.split(" "), function (index, eventName) {
						events[eventName] = "_eventHandler";
					});
				}

				this._off(this.headers.add(this.headers.next()));
				this._on(this.headers, events);
				this._on(this.headers.next(), { keydown: "_panelKeyDown" });
				this._hoverable(this.headers);
				this._focusable(this.headers);
			},

			_eventHandler: function (event) {
				var activeChildren, clickedChildren,
					options = this.options,
					active = this.active,
					clicked = $(event.currentTarget),
					clickedIsActive = clicked[0] === active[0],
					collapsing = clickedIsActive && options.collapsible,
					toShow = collapsing ? $() : clicked.next(),
					toHide = active.next(),
					eventData = {
						oldHeader: active,
						oldPanel: toHide,
						newHeader: collapsing ? $() : clicked,
						newPanel: toShow
					};

				event.preventDefault();

				if (

					// click on active header, but not collapsible
					(clickedIsActive && !options.collapsible) ||

					// allow canceling activation
					(this._trigger("beforeActivate", event, eventData) === false)) {
					return;
				}

				options.active = collapsing ? false : this.headers.index(clicked);

				// When the call to ._toggle() comes after the class changes
				// it causes a very odd bug in IE 8 (see #6720)
				this.active = clickedIsActive ? $() : clicked;
				this._toggle(eventData);

				// Switch classes
				// corner classes on the previously active header stay after the animation
				this._removeClass(active, "ui-accordion-header-active", "ui-state-active");
				if (options.icons) {
					activeChildren = active.children(".ui-accordion-header-icon");
					this._removeClass(activeChildren, null, options.icons.activeHeader)
						._addClass(activeChildren, null, options.icons.header);
				}

				if (!clickedIsActive) {
					this._removeClass(clicked, "ui-accordion-header-collapsed")
						._addClass(clicked, "ui-accordion-header-active", "ui-state-active");
					if (options.icons) {
						clickedChildren = clicked.children(".ui-accordion-header-icon");
						this._removeClass(clickedChildren, null, options.icons.header)
							._addClass(clickedChildren, null, options.icons.activeHeader);
					}

					this._addClass(clicked.next(), "ui-accordion-content-active");
				}
			},

			_toggle: function (data) {
				var toShow = data.newPanel,
					toHide = this.prevShow.length ? this.prevShow : data.oldPanel;

				// Handle activating a panel during the animation for another activation
				this.prevShow.add(this.prevHide).stop(true, true);
				this.prevShow = toShow;
				this.prevHide = toHide;

				if (this.options.animate) {
					this._animate(toShow, toHide, data);
				} else {
					toHide.hide();
					toShow.show();
					this._toggleComplete(data);
				}

				toHide.attr({
					"aria-hidden": "true"
				});
				toHide.prev().attr({
					"aria-selected": "false",
					"aria-expanded": "false"
				});

				// if we're switching panels, remove the old header from the tab order
				// if we're opening from collapsed state, remove the previous header from the tab order
				// if we're collapsing, then keep the collapsing header in the tab order
				if (toShow.length && toHide.length) {
					toHide.prev().attr({
						"tabIndex": -1,
						"aria-expanded": "false"
					});
				} else if (toShow.length) {
					this.headers.filter(function () {
						return parseInt($(this).attr("tabIndex"), 10) === 0;
					})
						.attr("tabIndex", -1);
				}

				toShow
					.attr("aria-hidden", "false")
					.prev()
					.attr({
						"aria-selected": "true",
						"aria-expanded": "true",
						tabIndex: 0
					});
			},

			_animate: function (toShow, toHide, data) {
				var total, easing, duration,
					that = this,
					adjust = 0,
					boxSizing = toShow.css("box-sizing"),
					down = toShow.length &&
						(!toHide.length || (toShow.index() < toHide.index())),
					animate = this.options.animate || {},
					options = down && animate.down || animate,
					complete = function () {
						that._toggleComplete(data);
					};

				if (typeof options === "number") {
					duration = options;
				}
				if (typeof options === "string") {
					easing = options;
				}

				// fall back from options to animation in case of partial down settings
				easing = easing || options.easing || animate.easing;
				duration = duration || options.duration || animate.duration;

				if (!toHide.length) {
					return toShow.animate(this.showProps, duration, easing, complete);
				}
				if (!toShow.length) {
					return toHide.animate(this.hideProps, duration, easing, complete);
				}

				total = toShow.show().outerHeight();
				toHide.animate(this.hideProps, {
					duration: duration,
					easing: easing,
					step: function (now, fx) {
						fx.now = Math.round(now);
					}
				});
				toShow
					.hide()
					.animate(this.showProps, {
						duration: duration,
						easing: easing,
						complete: complete,
						step: function (now, fx) {
							fx.now = Math.round(now);
							if (fx.prop !== "height") {
								if (boxSizing === "content-box") {
									adjust += fx.now;
								}
							} else if (that.options.heightStyle !== "content") {
								fx.now = Math.round(total - toHide.outerHeight() - adjust);
								adjust = 0;
							}
						}
					});
			},

			_toggleComplete: function (data) {
				var toHide = data.oldPanel,
					prev = toHide.prev();

				this._removeClass(toHide, "ui-accordion-content-active");
				this._removeClass(prev, "ui-accordion-header-active")
					._addClass(prev, "ui-accordion-header-collapsed");

				// Work around for rendering bug in IE (#5421)
				if (toHide.length) {
					toHide.parent()[0].className = toHide.parent()[0].className;
				}
				this._trigger("activate", null, data);
			}
		});



		var safeActiveElement = $.ui.safeActiveElement = function (document) {
			var activeElement;

			// Support: IE 9 only
			// IE9 throws an "Unspecified error" accessing document.activeElement from an <iframe>
			try {
				activeElement = document.activeElement;
			} catch (error) {
				activeElement = document.body;
			}

			// Support: IE 9 - 11 only
			// IE may return null instead of an element
			// Interestingly, this only seems to occur when NOT in an iframe
			if (!activeElement) {
				activeElement = document.body;
			}

			// Support: IE 11 only
			// IE11 returns a seemingly empty object in some cases when accessing
			// document.activeElement from an <iframe>
			if (!activeElement.nodeName) {
				activeElement = document.body;
			}

			return activeElement;
		};


		/*!
		 * jQuery UI Menu 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Menu
		//>>group: Widgets
		//>>description: Creates nestable menus.
		//>>docs: http://api.jqueryui.com/menu/
		//>>demos: http://jqueryui.com/menu/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/menu.css
		//>>css.theme: ../../themes/base/theme.css



		var widgetsMenu = $.widget("ui.menu", {
			version: "1.12.1",
			defaultElement: "<ul>",
			delay: 300,
			options: {
				icons: {
					submenu: "ui-icon-caret-1-e"
				},
				items: "> *",
				menus: "ul",
				position: {
					my: "left top",
					at: "right top"
				},
				role: "menu",

				// Callbacks
				blur: null,
				focus: null,
				select: null
			},

			_create: function () {
				this.activeMenu = this.element;

				// Flag used to prevent firing of the click handler
				// as the event bubbles up through nested menus
				this.mouseHandled = false;
				this.element
					.uniqueId()
					.attr({
						role: this.options.role,
						tabIndex: 0
					});

				this._addClass("ui-menu", "ui-widget ui-widget-content");
				this._on({

					// Prevent focus from sticking to links inside menu after clicking
					// them (focus should always stay on UL during navigation).
					"mousedown .ui-menu-item": function (event) {
						event.preventDefault();
					},
					"click .ui-menu-item": function (event) {
						var target = $(event.target);
						var active = $($.ui.safeActiveElement(this.document[0]));
						if (!this.mouseHandled && target.not(".ui-state-disabled").length) {
							this.select(event);

							// Only set the mouseHandled flag if the event will bubble, see #9469.
							if (!event.isPropagationStopped()) {
								this.mouseHandled = true;
							}

							// Open submenu on click
							if (target.has(".ui-menu").length) {
								this.expand(event);
							} else if (!this.element.is(":focus") &&
								active.closest(".ui-menu").length) {

								// Redirect focus to the menu
								this.element.trigger("focus", [true]);

								// If the active item is on the top level, let it stay active.
								// Otherwise, blur the active item since it is no longer visible.
								if (this.active && this.active.parents(".ui-menu").length === 1) {
									clearTimeout(this.timer);
								}
							}
						}
					},
					"mouseenter .ui-menu-item": function (event) {

						// Ignore mouse events while typeahead is active, see #10458.
						// Prevents focusing the wrong item when typeahead causes a scroll while the mouse
						// is over an item in the menu
						if (this.previousFilter) {
							return;
						}

						var actualTarget = $(event.target).closest(".ui-menu-item"),
							target = $(event.currentTarget);

						// Ignore bubbled events on parent items, see #11641
						if (actualTarget[0] !== target[0]) {
							return;
						}

						// Remove ui-state-active class from siblings of the newly focused menu item
						// to avoid a jump caused by adjacent elements both having a class with a border
						this._removeClass(target.siblings().children(".ui-state-active"),
							null, "ui-state-active");
						this.focus(event, target);
					},
					mouseleave: "collapseAll",
					"mouseleave .ui-menu": "collapseAll",
					focus: function (event, keepActiveItem) {

						// If there's already an active item, keep it active
						// If not, activate the first item
						var item = this.active || this.element.find(this.options.items).eq(0);

						if (!keepActiveItem) {
							this.focus(event, item);
						}
					},
					blur: function (event) {
						this._delay(function () {
							var notContained = !$.contains(
								this.element[0],
								$.ui.safeActiveElement(this.document[0])
							);
							if (notContained) {
								this.collapseAll(event);
							}
						});
					},
					keydown: "_keydown"
				});

				this.refresh();

				// Clicks outside of a menu collapse any open menus
				this._on(this.document, {
					click: function (event) {
						if (this._closeOnDocumentClick(event)) {
							this.collapseAll(event);
						}

						// Reset the mouseHandled flag
						this.mouseHandled = false;
					}
				});
			},

			_destroy: function () {
				var items = this.element.find(".ui-menu-item")
					.removeAttr("role aria-disabled"),
					submenus = items.children(".ui-menu-item-wrapper")
						.removeUniqueId()
						.removeAttr("tabIndex role aria-haspopup");

				// Destroy (sub)menus
				this.element
					.removeAttr("aria-activedescendant")
					.find(".ui-menu").addBack()
					.removeAttr("role aria-labelledby aria-expanded aria-hidden aria-disabled " +
						"tabIndex")
					.removeUniqueId()
					.show();

				submenus.children().each(function () {
					var elem = $(this);
					if (elem.data("ui-menu-submenu-caret")) {
						elem.remove();
					}
				});
			},

			_keydown: function (event) {
				var match, prev, character, skip,
					preventDefault = true;

				switch (event.keyCode) {
					case $.ui.keyCode.PAGE_UP:
						this.previousPage(event);
						break;
					case $.ui.keyCode.PAGE_DOWN:
						this.nextPage(event);
						break;
					case $.ui.keyCode.HOME:
						this._move("first", "first", event);
						break;
					case $.ui.keyCode.END:
						this._move("last", "last", event);
						break;
					case $.ui.keyCode.UP:
						this.previous(event);
						break;
					case $.ui.keyCode.DOWN:
						this.next(event);
						break;
					case $.ui.keyCode.LEFT:
						this.collapse(event);
						break;
					case $.ui.keyCode.RIGHT:
						if (this.active && !this.active.is(".ui-state-disabled")) {
							this.expand(event);
						}
						break;
					case $.ui.keyCode.ENTER:
					case $.ui.keyCode.SPACE:
						this._activate(event);
						break;
					case $.ui.keyCode.ESCAPE:
						this.collapse(event);
						break;
					default:
						preventDefault = false;
						prev = this.previousFilter || "";
						skip = false;

						// Support number pad values
						character = event.keyCode >= 96 && event.keyCode <= 105 ?
							(event.keyCode - 96).toString() : String.fromCharCode(event.keyCode);

						clearTimeout(this.filterTimer);

						if (character === prev) {
							skip = true;
						} else {
							character = prev + character;
						}

						match = this._filterMenuItems(character);
						match = skip && match.index(this.active.next()) !== -1 ?
							this.active.nextAll(".ui-menu-item") :
							match;

						// If no matches on the current filter, reset to the last character pressed
						// to move down the menu to the first item that starts with that character
						if (!match.length) {
							character = String.fromCharCode(event.keyCode);
							match = this._filterMenuItems(character);
						}

						if (match.length) {
							this.focus(event, match);
							this.previousFilter = character;
							this.filterTimer = this._delay(function () {
								delete this.previousFilter;
							}, 1000);
						} else {
							delete this.previousFilter;
						}
				}

				if (preventDefault) {
					event.preventDefault();
				}
			},

			_activate: function (event) {
				if (this.active && !this.active.is(".ui-state-disabled")) {
					if (this.active.children("[aria-haspopup='true']").length) {
						this.expand(event);
					} else {
						this.select(event);
					}
				}
			},

			refresh: function () {
				var menus, items, newSubmenus, newItems, newWrappers,
					that = this,
					icon = this.options.icons.submenu,
					submenus = this.element.find(this.options.menus);

				this._toggleClass("ui-menu-icons", null, !!this.element.find(".ui-icon").length);

				// Initialize nested menus
				newSubmenus = submenus.filter(":not(.ui-menu)")
					.hide()
					.attr({
						role: this.options.role,
						"aria-hidden": "true",
						"aria-expanded": "false"
					})
					.each(function () {
						var menu = $(this),
							item = menu.prev(),
							submenuCaret = $("<span>").data("ui-menu-submenu-caret", true);

						that._addClass(submenuCaret, "ui-menu-icon", "ui-icon " + icon);
						item
							.attr("aria-haspopup", "true")
							.prepend(submenuCaret);
						menu.attr("aria-labelledby", item.attr("id"));
					});

				this._addClass(newSubmenus, "ui-menu", "ui-widget ui-widget-content ui-front");

				menus = submenus.add(this.element);
				items = menus.find(this.options.items);

				// Initialize menu-items containing spaces and/or dashes only as dividers
				items.not(".ui-menu-item").each(function () {
					var item = $(this);
					if (that._isDivider(item)) {
						that._addClass(item, "ui-menu-divider", "ui-widget-content");
					}
				});

				// Don't refresh list items that are already adapted
				newItems = items.not(".ui-menu-item, .ui-menu-divider");
				newWrappers = newItems.children()
					.not(".ui-menu")
					.uniqueId()
					.attr({
						tabIndex: -1,
						role: this._itemRole()
					});
				this._addClass(newItems, "ui-menu-item")
					._addClass(newWrappers, "ui-menu-item-wrapper");

				// Add aria-disabled attribute to any disabled menu item
				items.filter(".ui-state-disabled").attr("aria-disabled", "true");

				// If the active item has been removed, blur the menu
				if (this.active && !$.contains(this.element[0], this.active[0])) {
					this.blur();
				}
			},

			_itemRole: function () {
				return {
					menu: "menuitem",
					listbox: "option"
				}[this.options.role];
			},

			_setOption: function (key, value) {
				if (key === "icons") {
					var icons = this.element.find(".ui-menu-icon");
					this._removeClass(icons, null, this.options.icons.submenu)
						._addClass(icons, null, value.submenu);
				}
				this._super(key, value);
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this.element.attr("aria-disabled", String(value));
				this._toggleClass(null, "ui-state-disabled", !!value);
			},

			focus: function (event, item) {
				var nested, focused, activeParent;
				this.blur(event, event && event.type === "focus");

				this._scrollIntoView(item);

				this.active = item.first();

				focused = this.active.children(".ui-menu-item-wrapper");
				this._addClass(focused, null, "ui-state-active");

				// Only update aria-activedescendant if there's a role
				// otherwise we assume focus is managed elsewhere
				if (this.options.role) {
					this.element.attr("aria-activedescendant", focused.attr("id"));
				}

				// Highlight active parent menu item, if any
				activeParent = this.active
					.parent()
					.closest(".ui-menu-item")
					.children(".ui-menu-item-wrapper");
				this._addClass(activeParent, null, "ui-state-active");

				if (event && event.type === "keydown") {
					this._close();
				} else {
					this.timer = this._delay(function () {
						this._close();
					}, this.delay);
				}

				nested = item.children(".ui-menu");
				if (nested.length && event && (/^mouse/.test(event.type))) {
					this._startOpening(nested);
				}
				this.activeMenu = item.parent();

				this._trigger("focus", event, { item: item });
			},

			_scrollIntoView: function (item) {
				var borderTop, paddingTop, offset, scroll, elementHeight, itemHeight;
				if (this._hasScroll()) {
					borderTop = parseFloat($.css(this.activeMenu[0], "borderTopWidth")) || 0;
					paddingTop = parseFloat($.css(this.activeMenu[0], "paddingTop")) || 0;
					offset = item.offset().top - this.activeMenu.offset().top - borderTop - paddingTop;
					scroll = this.activeMenu.scrollTop();
					elementHeight = this.activeMenu.height();
					itemHeight = item.outerHeight();

					if (offset < 0) {
						this.activeMenu.scrollTop(scroll + offset);
					} else if (offset + itemHeight > elementHeight) {
						this.activeMenu.scrollTop(scroll + offset - elementHeight + itemHeight);
					}
				}
			},

			blur: function (event, fromFocus) {
				if (!fromFocus) {
					clearTimeout(this.timer);
				}

				if (!this.active) {
					return;
				}

				this._removeClass(this.active.children(".ui-menu-item-wrapper"),
					null, "ui-state-active");

				this._trigger("blur", event, { item: this.active });
				this.active = null;
			},

			_startOpening: function (submenu) {
				clearTimeout(this.timer);

				// Don't open if already open fixes a Firefox bug that caused a .5 pixel
				// shift in the submenu position when mousing over the caret icon
				if (submenu.attr("aria-hidden") !== "true") {
					return;
				}

				this.timer = this._delay(function () {
					this._close();
					this._open(submenu);
				}, this.delay);
			},

			_open: function (submenu) {
				var position = $.extend({
					of: this.active
				}, this.options.position);

				clearTimeout(this.timer);
				this.element.find(".ui-menu").not(submenu.parents(".ui-menu"))
					.hide()
					.attr("aria-hidden", "true");

				submenu
					.show()
					.removeAttr("aria-hidden")
					.attr("aria-expanded", "true")
					.position(position);
			},

			collapseAll: function (event, all) {
				clearTimeout(this.timer);
				this.timer = this._delay(function () {

					// If we were passed an event, look for the submenu that contains the event
					var currentMenu = all ? this.element :
						$(event && event.target).closest(this.element.find(".ui-menu"));

					// If we found no valid submenu ancestor, use the main menu to close all
					// sub menus anyway
					if (!currentMenu.length) {
						currentMenu = this.element;
					}

					this._close(currentMenu);

					this.blur(event);

					// Work around active item staying active after menu is blurred
					this._removeClass(currentMenu.find(".ui-state-active"), null, "ui-state-active");

					this.activeMenu = currentMenu;
				}, this.delay);
			},

			// With no arguments, closes the currently active menu - if nothing is active
			// it closes all menus.  If passed an argument, it will search for menus BELOW
			_close: function (startMenu) {
				if (!startMenu) {
					startMenu = this.active ? this.active.parent() : this.element;
				}

				startMenu.find(".ui-menu")
					.hide()
					.attr("aria-hidden", "true")
					.attr("aria-expanded", "false");
			},

			_closeOnDocumentClick: function (event) {
				return !$(event.target).closest(".ui-menu").length;
			},

			_isDivider: function (item) {

				// Match hyphen, em dash, en dash
				return !/[^\-\u2014\u2013\s]/.test(item.text());
			},

			collapse: function (event) {
				var newItem = this.active &&
					this.active.parent().closest(".ui-menu-item", this.element);
				if (newItem && newItem.length) {
					this._close();
					this.focus(event, newItem);
				}
			},

			expand: function (event) {
				var newItem = this.active &&
					this.active
						.children(".ui-menu ")
						.find(this.options.items)
						.first();

				if (newItem && newItem.length) {
					this._open(newItem.parent());

					// Delay so Firefox will not hide activedescendant change in expanding submenu from AT
					this._delay(function () {
						this.focus(event, newItem);
					});
				}
			},

			next: function (event) {
				this._move("next", "first", event);
			},

			previous: function (event) {
				this._move("prev", "last", event);
			},

			isFirstItem: function () {
				return this.active && !this.active.prevAll(".ui-menu-item").length;
			},

			isLastItem: function () {
				return this.active && !this.active.nextAll(".ui-menu-item").length;
			},

			_move: function (direction, filter, event) {
				var next;
				if (this.active) {
					if (direction === "first" || direction === "last") {
						next = this.active
						[direction === "first" ? "prevAll" : "nextAll"](".ui-menu-item")
							.eq(-1);
					} else {
						next = this.active
						[direction + "All"](".ui-menu-item")
							.eq(0);
					}
				}
				if (!next || !next.length || !this.active) {
					next = this.activeMenu.find(this.options.items)[filter]();
				}

				this.focus(event, next);
			},

			nextPage: function (event) {
				var item, base, height;

				if (!this.active) {
					this.next(event);
					return;
				}
				if (this.isLastItem()) {
					return;
				}
				if (this._hasScroll()) {
					base = this.active.offset().top;
					height = this.element.height();
					this.active.nextAll(".ui-menu-item").each(function () {
						item = $(this);
						return item.offset().top - base - height < 0;
					});

					this.focus(event, item);
				} else {
					this.focus(event, this.activeMenu.find(this.options.items)
					[!this.active ? "first" : "last"]());
				}
			},

			previousPage: function (event) {
				var item, base, height;
				if (!this.active) {
					this.next(event);
					return;
				}
				if (this.isFirstItem()) {
					return;
				}
				if (this._hasScroll()) {
					base = this.active.offset().top;
					height = this.element.height();
					this.active.prevAll(".ui-menu-item").each(function () {
						item = $(this);
						return item.offset().top - base + height > 0;
					});

					this.focus(event, item);
				} else {
					this.focus(event, this.activeMenu.find(this.options.items).first());
				}
			},

			_hasScroll: function () {
				return this.element.outerHeight() < this.element.prop("scrollHeight");
			},

			select: function (event) {

				// TODO: It should never be possible to not have an active item at this
				// point, but the tests don't trigger mouseenter before click.
				this.active = this.active || $(event.target).closest(".ui-menu-item");
				var ui = { item: this.active };
				if (!this.active.has(".ui-menu").length) {
					this.collapseAll(event, true);
				}
				this._trigger("select", event, ui);
			},

			_filterMenuItems: function (character) {
				var escapedCharacter = character.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"),
					regex = new RegExp("^" + escapedCharacter, "i");

				return this.activeMenu
					.find(this.options.items)

					// Only match on items, not dividers or other content (#10571)
					.filter(".ui-menu-item")
					.filter(function () {
						return regex.test(
							$.trim($(this).children(".ui-menu-item-wrapper").text()));
					});
			}
		});


		/*!
		 * jQuery UI Autocomplete 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Autocomplete
		//>>group: Widgets
		//>>description: Lists suggested words as the user is typing.
		//>>docs: http://api.jqueryui.com/autocomplete/
		//>>demos: http://jqueryui.com/autocomplete/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/autocomplete.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.autocomplete", {
			version: "1.12.1",
			defaultElement: "<input>",
			options: {
				appendTo: null,
				autoFocus: false,
				delay: 300,
				minLength: 1,
				position: {
					my: "left top",
					at: "left bottom",
					collision: "none"
				},
				source: null,

				// Callbacks
				change: null,
				close: null,
				focus: null,
				open: null,
				response: null,
				search: null,
				select: null
			},

			requestIndex: 0,
			pending: 0,

			_create: function () {

				// Some browsers only repeat keydown events, not keypress events,
				// so we use the suppressKeyPress flag to determine if we've already
				// handled the keydown event. #7269
				// Unfortunately the code for & in keypress is the same as the up arrow,
				// so we use the suppressKeyPressRepeat flag to avoid handling keypress
				// events when we know the keydown event was used to modify the
				// search term. #7799
				var suppressKeyPress, suppressKeyPressRepeat, suppressInput,
					nodeName = this.element[0].nodeName.toLowerCase(),
					isTextarea = nodeName === "textarea",
					isInput = nodeName === "input";

				// Textareas are always multi-line
				// Inputs are always single-line, even if inside a contentEditable element
				// IE also treats inputs as contentEditable
				// All other element types are determined by whether or not they're contentEditable
				this.isMultiLine = isTextarea || !isInput && this._isContentEditable(this.element);

				this.valueMethod = this.element[isTextarea || isInput ? "val" : "text"];
				this.isNewMenu = true;

				this._addClass("ui-autocomplete-input");
				this.element.attr("autocomplete", "off");

				this._on(this.element, {
					keydown: function (event) {
						if (this.element.prop("readOnly")) {
							suppressKeyPress = true;
							suppressInput = true;
							suppressKeyPressRepeat = true;
							return;
						}

						suppressKeyPress = false;
						suppressInput = false;
						suppressKeyPressRepeat = false;
						var keyCode = $.ui.keyCode;
						switch (event.keyCode) {
							case keyCode.PAGE_UP:
								suppressKeyPress = true;
								this._move("previousPage", event);
								break;
							case keyCode.PAGE_DOWN:
								suppressKeyPress = true;
								this._move("nextPage", event);
								break;
							case keyCode.UP:
								suppressKeyPress = true;
								this._keyEvent("previous", event);
								break;
							case keyCode.DOWN:
								suppressKeyPress = true;
								this._keyEvent("next", event);
								break;
							case keyCode.ENTER:

								// when menu is open and has focus
								if (this.menu.active) {

									// #6055 - Opera still allows the keypress to occur
									// which causes forms to submit
									suppressKeyPress = true;
									event.preventDefault();
									this.menu.select(event);
								}
								break;
							case keyCode.TAB:
								if (this.menu.active) {
									this.menu.select(event);
								}
								break;
							case keyCode.ESCAPE:
								if (this.menu.element.is(":visible")) {
									if (!this.isMultiLine) {
										this._value(this.term);
									}
									this.close(event);

									// Different browsers have different default behavior for escape
									// Single press can mean undo or clear
									// Double press in IE means clear the whole form
									event.preventDefault();
								}
								break;
							default:
								suppressKeyPressRepeat = true;

								// search timeout should be triggered before the input value is changed
								this._searchTimeout(event);
								break;
						}
					},
					keypress: function (event) {
						if (suppressKeyPress) {
							suppressKeyPress = false;
							if (!this.isMultiLine || this.menu.element.is(":visible")) {
								event.preventDefault();
							}
							return;
						}
						if (suppressKeyPressRepeat) {
							return;
						}

						// Replicate some key handlers to allow them to repeat in Firefox and Opera
						var keyCode = $.ui.keyCode;
						switch (event.keyCode) {
							case keyCode.PAGE_UP:
								this._move("previousPage", event);
								break;
							case keyCode.PAGE_DOWN:
								this._move("nextPage", event);
								break;
							case keyCode.UP:
								this._keyEvent("previous", event);
								break;
							case keyCode.DOWN:
								this._keyEvent("next", event);
								break;
						}
					},
					input: function (event) {
						if (suppressInput) {
							suppressInput = false;
							event.preventDefault();
							return;
						}
						this._searchTimeout(event);
					},
					focus: function () {
						this.selectedItem = null;
						this.previous = this._value();
					},
					blur: function (event) {
						if (this.cancelBlur) {
							delete this.cancelBlur;
							return;
						}

						clearTimeout(this.searching);
						this.close(event);
						this._change(event);
					}
				});

				this._initSource();
				this.menu = $("<ul>")
					.appendTo(this._appendTo())
					.menu({

						// disable ARIA support, the live region takes care of that
						role: null
					})
					.hide()
					.menu("instance");

				this._addClass(this.menu.element, "ui-autocomplete", "ui-front");
				this._on(this.menu.element, {
					mousedown: function (event) {

						// prevent moving focus out of the text field
						event.preventDefault();

						// IE doesn't prevent moving focus even with event.preventDefault()
						// so we set a flag to know when we should ignore the blur event
						this.cancelBlur = true;
						this._delay(function () {
							delete this.cancelBlur;

							// Support: IE 8 only
							// Right clicking a menu item or selecting text from the menu items will
							// result in focus moving out of the input. However, we've already received
							// and ignored the blur event because of the cancelBlur flag set above. So
							// we restore focus to ensure that the menu closes properly based on the user's
							// next actions.
							if (this.element[0] !== $.ui.safeActiveElement(this.document[0])) {
								this.element.trigger("focus");
							}
						});
					},
					menufocus: function (event, ui) {
						var label, item;

						// support: Firefox
						// Prevent accidental activation of menu items in Firefox (#7024 #9118)
						if (this.isNewMenu) {
							this.isNewMenu = false;
							if (event.originalEvent && /^mouse/.test(event.originalEvent.type)) {
								this.menu.blur();

								this.document.one("mousemove", function () {
									$(event.target).trigger(event.originalEvent);
								});

								return;
							}
						}

						item = ui.item.data("ui-autocomplete-item");
						if (false !== this._trigger("focus", event, { item: item })) {

							// use value to match what will end up in the input, if it was a key event
							if (event.originalEvent && /^key/.test(event.originalEvent.type)) {
								this._value(item.value);
							}
						}

						// Announce the value in the liveRegion
						label = ui.item.attr("aria-label") || item.value;
						if (label && $.trim(label).length) {
							this.liveRegion.children().hide();
							$("<div>").text(label).appendTo(this.liveRegion);
						}
					},
					menuselect: function (event, ui) {
						var item = ui.item.data("ui-autocomplete-item"),
							previous = this.previous;

						// Only trigger when focus was lost (click on menu)
						if (this.element[0] !== $.ui.safeActiveElement(this.document[0])) {
							this.element.trigger("focus");
							this.previous = previous;

							// #6109 - IE triggers two focus events and the second
							// is asynchronous, so we need to reset the previous
							// term synchronously and asynchronously :-(
							this._delay(function () {
								this.previous = previous;
								this.selectedItem = item;
							});
						}

						if (false !== this._trigger("select", event, { item: item })) {
							this._value(item.value);
						}

						// reset the term after the select event
						// this allows custom select handling to work properly
						this.term = this._value();

						this.close(event);
						this.selectedItem = item;
					}
				});

				this.liveRegion = $("<div>", {
					role: "status",
					"aria-live": "assertive",
					"aria-relevant": "additions"
				})
					.appendTo(this.document[0].body);

				this._addClass(this.liveRegion, null, "ui-helper-hidden-accessible");

				// Turning off autocomplete prevents the browser from remembering the
				// value when navigating through history, so we re-enable autocomplete
				// if the page is unloaded before the widget is destroyed. #7790
				this._on(this.window, {
					beforeunload: function () {
						this.element.removeAttr("autocomplete");
					}
				});
			},

			_destroy: function () {
				clearTimeout(this.searching);
				this.element.removeAttr("autocomplete");
				this.menu.element.remove();
				this.liveRegion.remove();
			},

			_setOption: function (key, value) {
				this._super(key, value);
				if (key === "source") {
					this._initSource();
				}
				if (key === "appendTo") {
					this.menu.element.appendTo(this._appendTo());
				}
				if (key === "disabled" && value && this.xhr) {
					this.xhr.abort();
				}
			},

			_isEventTargetInWidget: function (event) {
				var menuElement = this.menu.element[0];

				return event.target === this.element[0] ||
					event.target === menuElement ||
					$.contains(menuElement, event.target);
			},

			_closeOnClickOutside: function (event) {
				if (!this._isEventTargetInWidget(event)) {
					this.close();
				}
			},

			_appendTo: function () {
				var element = this.options.appendTo;

				if (element) {
					element = element.jquery || element.nodeType ?
						$(element) :
						this.document.find(element).eq(0);
				}

				if (!element || !element[0]) {
					element = this.element.closest(".ui-front, dialog");
				}

				if (!element.length) {
					element = this.document[0].body;
				}

				return element;
			},

			_initSource: function () {
				var array, url,
					that = this;
				if ($.isArray(this.options.source)) {
					array = this.options.source;
					this.source = function (request, response) {
						response($.ui.autocomplete.filter(array, request.term));
					};
				} else if (typeof this.options.source === "string") {
					url = this.options.source;
					this.source = function (request, response) {
						if (that.xhr) {
							that.xhr.abort();
						}
						that.xhr = $.ajax({
							url: url,
							data: request,
							dataType: "json",
							success: function (data) {
								response(data);
							},
							error: function () {
								response([]);
							}
						});
					};
				} else {
					this.source = this.options.source;
				}
			},

			_searchTimeout: function (event) {
				clearTimeout(this.searching);
				this.searching = this._delay(function () {

					// Search if the value has changed, or if the user retypes the same value (see #7434)
					var equalValues = this.term === this._value(),
						menuVisible = this.menu.element.is(":visible"),
						modifierKey = event.altKey || event.ctrlKey || event.metaKey || event.shiftKey;

					if (!equalValues || (equalValues && !menuVisible && !modifierKey)) {
						this.selectedItem = null;
						this.search(null, event);
					}
				}, this.options.delay);
			},

			search: function (value, event) {
				value = value != null ? value : this._value();

				// Always save the actual value, not the one passed as an argument
				this.term = this._value();

				if (value.length < this.options.minLength) {
					return this.close(event);
				}

				if (this._trigger("search", event) === false) {
					return;
				}

				return this._search(value);
			},

			_search: function (value) {
				this.pending++;
				this._addClass("ui-autocomplete-loading");
				this.cancelSearch = false;

				this.source({ term: value }, this._response());
			},

			_response: function () {
				var index = ++this.requestIndex;

				return $.proxy(function (content) {
					if (index === this.requestIndex) {
						this.__response(content);
					}

					this.pending--;
					if (!this.pending) {
						this._removeClass("ui-autocomplete-loading");
					}
				}, this);
			},

			__response: function (content) {
				if (content) {
					content = this._normalize(content);
				}
				this._trigger("response", null, { content: content });
				if (!this.options.disabled && content && content.length && !this.cancelSearch) {
					this._suggest(content);
					this._trigger("open");
				} else {

					// use ._close() instead of .close() so we don't cancel future searches
					this._close();
				}
			},

			close: function (event) {
				this.cancelSearch = true;
				this._close(event);
			},

			_close: function (event) {

				// Remove the handler that closes the menu on outside clicks
				this._off(this.document, "mousedown");

				if (this.menu.element.is(":visible")) {
					this.menu.element.hide();
					this.menu.blur();
					this.isNewMenu = true;
					this._trigger("close", event);
				}
			},

			_change: function (event) {
				if (this.previous !== this._value()) {
					this._trigger("change", event, { item: this.selectedItem });
				}
			},

			_normalize: function (items) {

				// assume all items have the right format when the first item is complete
				if (items.length && items[0].label && items[0].value) {
					return items;
				}
				return $.map(items, function (item) {
					if (typeof item === "string") {
						return {
							label: item,
							value: item
						};
					}
					return $.extend({}, item, {
						label: item.label || item.value,
						value: item.value || item.label
					});
				});
			},

			_suggest: function (items) {
				var ul = this.menu.element.empty();
				this._renderMenu(ul, items);
				this.isNewMenu = true;
				this.menu.refresh();

				// Size and position menu
				ul.show();
				this._resizeMenu();
				ul.position($.extend({
					of: this.element
				}, this.options.position));

				if (this.options.autoFocus) {
					this.menu.next();
				}

				// Listen for interactions outside of the widget (#6642)
				this._on(this.document, {
					mousedown: "_closeOnClickOutside"
				});
			},

			_resizeMenu: function () {
				var ul = this.menu.element;
				ul.outerWidth(Math.max(

					// Firefox wraps long text (possibly a rounding bug)
					// so we add 1px to avoid the wrapping (#7513)
					ul.width("").outerWidth() + 1,
					this.element.outerWidth()
				));
			},

			_renderMenu: function (ul, items) {
				var that = this;
				$.each(items, function (index, item) {
					that._renderItemData(ul, item);
				});
			},

			_renderItemData: function (ul, item) {
				return this._renderItem(ul, item).data("ui-autocomplete-item", item);
			},

			_renderItem: function (ul, item) {
				return $("<li>")
					.append($("<div>").text(item.label))
					.appendTo(ul);
			},

			_move: function (direction, event) {
				if (!this.menu.element.is(":visible")) {
					this.search(null, event);
					return;
				}
				if (this.menu.isFirstItem() && /^previous/.test(direction) ||
					this.menu.isLastItem() && /^next/.test(direction)) {

					if (!this.isMultiLine) {
						this._value(this.term);
					}

					this.menu.blur();
					return;
				}
				this.menu[direction](event);
			},

			widget: function () {
				return this.menu.element;
			},

			_value: function () {
				return this.valueMethod.apply(this.element, arguments);
			},

			_keyEvent: function (keyEvent, event) {
				if (!this.isMultiLine || this.menu.element.is(":visible")) {
					this._move(keyEvent, event);

					// Prevents moving cursor to beginning/end of the text field in some browsers
					event.preventDefault();
				}
			},

			// Support: Chrome <=50
			// We should be able to just use this.element.prop( "isContentEditable" )
			// but hidden elements always report false in Chrome.
			// https://code.google.com/p/chromium/issues/detail?id=313082
			_isContentEditable: function (element) {
				if (!element.length) {
					return false;
				}

				var editable = element.prop("contentEditable");

				if (editable === "inherit") {
					return this._isContentEditable(element.parent());
				}

				return editable === "true";
			}
		});

		$.extend($.ui.autocomplete, {
			escapeRegex: function (value) {
				return value.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&");
			},
			filter: function (array, term) {
				var matcher = new RegExp($.ui.autocomplete.escapeRegex(term), "i");
				return $.grep(array, function (value) {
					return matcher.test(value.label || value.value || value);
				});
			}
		});

		// Live region extension, adding a `messages` option
		// NOTE: This is an experimental API. We are still investigating
		// a full solution for string manipulation and internationalization.
		$.widget("ui.autocomplete", $.ui.autocomplete, {
			options: {
				messages: {
					noResults: "No search results.",
					results: function (amount) {
						return amount + (amount > 1 ? " results are" : " result is") +
							" available, use up and down arrow keys to navigate.";
					}
				}
			},

			__response: function (content) {
				var message;
				this._superApply(arguments);
				if (this.options.disabled || this.cancelSearch) {
					return;
				}
				if (content && content.length) {
					message = this.options.messages.results(content.length);
				} else {
					message = this.options.messages.noResults;
				}
				this.liveRegion.children().hide();
				$("<div>").text(message).appendTo(this.liveRegion);
			}
		});

		var widgetsAutocomplete = $.ui.autocomplete;


		/*!
		 * jQuery UI Controlgroup 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Controlgroup
		//>>group: Widgets
		//>>description: Visually groups form control widgets
		//>>docs: http://api.jqueryui.com/controlgroup/
		//>>demos: http://jqueryui.com/controlgroup/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/controlgroup.css
		//>>css.theme: ../../themes/base/theme.css


		var controlgroupCornerRegex = /ui-corner-([a-z]){2,6}/g;

		var widgetsControlgroup = $.widget("ui.controlgroup", {
			version: "1.12.1",
			defaultElement: "<div>",
			options: {
				direction: "horizontal",
				disabled: null,
				onlyVisible: true,
				items: {
					"button": "input[type=button], input[type=submit], input[type=reset], button, a",
					"controlgroupLabel": ".ui-controlgroup-label",
					"checkboxradio": "input[type='checkbox'], input[type='radio']",
					"selectmenu": "select",
					"spinner": ".ui-spinner-input"
				}
			},

			_create: function () {
				this._enhance();
			},

			// To support the enhanced option in jQuery Mobile, we isolate DOM manipulation
			_enhance: function () {
				this.element.attr("role", "toolbar");
				this.refresh();
			},

			_destroy: function () {
				this._callChildMethod("destroy");
				this.childWidgets.removeData("ui-controlgroup-data");
				this.element.removeAttr("role");
				if (this.options.items.controlgroupLabel) {
					this.element
						.find(this.options.items.controlgroupLabel)
						.find(".ui-controlgroup-label-contents")
						.contents().unwrap();
				}
			},

			_initWidgets: function () {
				var that = this,
					childWidgets = [];

				// First we iterate over each of the items options
				$.each(this.options.items, function (widget, selector) {
					var labels;
					var options = {};

					// Make sure the widget has a selector set
					if (!selector) {
						return;
					}

					if (widget === "controlgroupLabel") {
						labels = that.element.find(selector);
						labels.each(function () {
							var element = $(this);

							if (element.children(".ui-controlgroup-label-contents").length) {
								return;
							}
							element.contents()
								.wrapAll("<span class='ui-controlgroup-label-contents'></span>");
						});
						that._addClass(labels, null, "ui-widget ui-widget-content ui-state-default");
						childWidgets = childWidgets.concat(labels.get());
						return;
					}

					// Make sure the widget actually exists
					if (!$.fn[widget]) {
						return;
					}

					// We assume everything is in the middle to start because we can't determine
					// first / last elements until all enhancments are done.
					if (that["_" + widget + "Options"]) {
						options = that["_" + widget + "Options"]("middle");
					} else {
						options = { classes: {} };
					}

					// Find instances of this widget inside controlgroup and init them
					that.element
						.find(selector)
						.each(function () {
							var element = $(this);
							var instance = element[widget]("instance");

							// We need to clone the default options for this type of widget to avoid
							// polluting the variable options which has a wider scope than a single widget.
							var instanceOptions = $.widget.extend({}, options);

							// If the button is the child of a spinner ignore it
							// TODO: Find a more generic solution
							if (widget === "button" && element.parent(".ui-spinner").length) {
								return;
							}

							// Create the widget if it doesn't exist
							if (!instance) {
								instance = element[widget]()[widget]("instance");
							}
							if (instance) {
								instanceOptions.classes =
									that._resolveClassesValues(instanceOptions.classes, instance);
							}
							element[widget](instanceOptions);

							// Store an instance of the controlgroup to be able to reference
							// from the outermost element for changing options and refresh
							var widgetElement = element[widget]("widget");
							$.data(widgetElement[0], "ui-controlgroup-data",
								instance ? instance : element[widget]("instance"));

							childWidgets.push(widgetElement[0]);
						});
				});

				this.childWidgets = $($.unique(childWidgets));
				this._addClass(this.childWidgets, "ui-controlgroup-item");
			},

			_callChildMethod: function (method) {
				this.childWidgets.each(function () {
					var element = $(this),
						data = element.data("ui-controlgroup-data");
					if (data && data[method]) {
						data[method]();
					}
				});
			},

			_updateCornerClass: function (element, position) {
				var remove = "ui-corner-top ui-corner-bottom ui-corner-left ui-corner-right ui-corner-all";
				var add = this._buildSimpleOptions(position, "label").classes.label;

				this._removeClass(element, null, remove);
				this._addClass(element, null, add);
			},

			_buildSimpleOptions: function (position, key) {
				var direction = this.options.direction === "vertical";
				var result = {
					classes: {}
				};
				result.classes[key] = {
					"middle": "",
					"first": "ui-corner-" + (direction ? "top" : "left"),
					"last": "ui-corner-" + (direction ? "bottom" : "right"),
					"only": "ui-corner-all"
				}[position];

				return result;
			},

			_spinnerOptions: function (position) {
				var options = this._buildSimpleOptions(position, "ui-spinner");

				options.classes["ui-spinner-up"] = "";
				options.classes["ui-spinner-down"] = "";

				return options;
			},

			_buttonOptions: function (position) {
				return this._buildSimpleOptions(position, "ui-button");
			},

			_checkboxradioOptions: function (position) {
				return this._buildSimpleOptions(position, "ui-checkboxradio-label");
			},

			_selectmenuOptions: function (position) {
				var direction = this.options.direction === "vertical";
				return {
					width: direction ? "auto" : false,
					classes: {
						middle: {
							"ui-selectmenu-button-open": "",
							"ui-selectmenu-button-closed": ""
						},
						first: {
							"ui-selectmenu-button-open": "ui-corner-" + (direction ? "top" : "tl"),
							"ui-selectmenu-button-closed": "ui-corner-" + (direction ? "top" : "left")
						},
						last: {
							"ui-selectmenu-button-open": direction ? "" : "ui-corner-tr",
							"ui-selectmenu-button-closed": "ui-corner-" + (direction ? "bottom" : "right")
						},
						only: {
							"ui-selectmenu-button-open": "ui-corner-top",
							"ui-selectmenu-button-closed": "ui-corner-all"
						}

					}[position]
				};
			},

			_resolveClassesValues: function (classes, instance) {
				var result = {};
				$.each(classes, function (key) {
					var current = instance.options.classes[key] || "";
					current = $.trim(current.replace(controlgroupCornerRegex, ""));
					result[key] = (current + " " + classes[key]).replace(/\s+/g, " ");
				});
				return result;
			},

			_setOption: function (key, value) {
				if (key === "direction") {
					this._removeClass("ui-controlgroup-" + this.options.direction);
				}

				this._super(key, value);
				if (key === "disabled") {
					this._callChildMethod(value ? "disable" : "enable");
					return;
				}

				this.refresh();
			},

			refresh: function () {
				var children,
					that = this;

				this._addClass("ui-controlgroup ui-controlgroup-" + this.options.direction);

				if (this.options.direction === "horizontal") {
					this._addClass(null, "ui-helper-clearfix");
				}
				this._initWidgets();

				children = this.childWidgets;

				// We filter here because we need to track all childWidgets not just the visible ones
				if (this.options.onlyVisible) {
					children = children.filter(":visible");
				}

				if (children.length) {

					// We do this last because we need to make sure all enhancment is done
					// before determining first and last
					$.each(["first", "last"], function (index, value) {
						var instance = children[value]().data("ui-controlgroup-data");

						if (instance && that["_" + instance.widgetName + "Options"]) {
							var options = that["_" + instance.widgetName + "Options"](
								children.length === 1 ? "only" : value
							);
							options.classes = that._resolveClassesValues(options.classes, instance);
							instance.element[instance.widgetName](options);
						} else {
							that._updateCornerClass(children[value](), value);
						}
					});

					// Finally call the refresh method on each of the child widgets.
					this._callChildMethod("refresh");
				}
			}
		});

		/*!
		 * jQuery UI Checkboxradio 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Checkboxradio
		//>>group: Widgets
		//>>description: Enhances a form with multiple themeable checkboxes or radio buttons.
		//>>docs: http://api.jqueryui.com/checkboxradio/
		//>>demos: http://jqueryui.com/checkboxradio/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/button.css
		//>>css.structure: ../../themes/base/checkboxradio.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.checkboxradio", [$.ui.formResetMixin, {
			version: "1.12.1",
			options: {
				disabled: null,
				label: null,
				icon: true,
				classes: {
					"ui-checkboxradio-label": "ui-corner-all",
					"ui-checkboxradio-icon": "ui-corner-all"
				}
			},

			_getCreateOptions: function () {
				var disabled, labels;
				var that = this;
				var options = this._super() || {};

				// We read the type here, because it makes more sense to throw a element type error first,
				// rather then the error for lack of a label. Often if its the wrong type, it
				// won't have a label (e.g. calling on a div, btn, etc)
				this._readType();

				labels = this.element.labels();

				// If there are multiple labels, use the last one
				this.label = $(labels[labels.length - 1]);
				if (!this.label.length) {
					$.error("No label found for checkboxradio widget");
				}

				this.originalLabel = "";

				// We need to get the label text but this may also need to make sure it does not contain the
				// input itself.
				this.label.contents().not(this.element[0]).each(function () {

					// The label contents could be text, html, or a mix. We concat each element to get a
					// string representation of the label, without the input as part of it.
					that.originalLabel += this.nodeType === 3 ? $(this).text() : this.outerHTML;
				});

				// Set the label option if we found label text
				if (this.originalLabel) {
					options.label = this.originalLabel;
				}

				disabled = this.element[0].disabled;
				if (disabled != null) {
					options.disabled = disabled;
				}
				return options;
			},

			_create: function () {
				var checked = this.element[0].checked;

				this._bindFormResetHandler();

				if (this.options.disabled == null) {
					this.options.disabled = this.element[0].disabled;
				}

				this._setOption("disabled", this.options.disabled);
				this._addClass("ui-checkboxradio", "ui-helper-hidden-accessible");
				this._addClass(this.label, "ui-checkboxradio-label", "ui-button ui-widget");

				if (this.type === "radio") {
					this._addClass(this.label, "ui-checkboxradio-radio-label");
				}

				if (this.options.label && this.options.label !== this.originalLabel) {
					this._updateLabel();
				} else if (this.originalLabel) {
					this.options.label = this.originalLabel;
				}

				this._enhance();

				if (checked) {
					this._addClass(this.label, "ui-checkboxradio-checked", "ui-state-active");
					if (this.icon) {
						this._addClass(this.icon, null, "ui-state-hover");
					}
				}

				this._on({
					change: "_toggleClasses",
					focus: function () {
						this._addClass(this.label, null, "ui-state-focus ui-visual-focus");
					},
					blur: function () {
						this._removeClass(this.label, null, "ui-state-focus ui-visual-focus");
					}
				});
			},

			_readType: function () {
				var nodeName = this.element[0].nodeName.toLowerCase();
				this.type = this.element[0].type;
				if (nodeName !== "input" || !/radio|checkbox/.test(this.type)) {
					$.error("Can't create checkboxradio on element.nodeName=" + nodeName +
						" and element.type=" + this.type);
				}
			},

			// Support jQuery Mobile enhanced option
			_enhance: function () {
				this._updateIcon(this.element[0].checked);
			},

			widget: function () {
				return this.label;
			},

			_getRadioGroup: function () {
				var group;
				var name = this.element[0].name;
				var nameSelector = "input[name='" + $.ui.escapeSelector(name) + "']";

				if (!name) {
					return $([]);
				}

				if (this.form.length) {
					group = $(this.form[0].elements).filter(nameSelector);
				} else {

					// Not inside a form, check all inputs that also are not inside a form
					group = $(nameSelector).filter(function () {
						return $(this).form().length === 0;
					});
				}

				return group.not(this.element);
			},

			_toggleClasses: function () {
				var checked = this.element[0].checked;
				this._toggleClass(this.label, "ui-checkboxradio-checked", "ui-state-active", checked);

				if (this.options.icon && this.type === "checkbox") {
					this._toggleClass(this.icon, null, "ui-icon-check ui-state-checked", checked)
						._toggleClass(this.icon, null, "ui-icon-blank", !checked);
				}

				if (this.type === "radio") {
					this._getRadioGroup()
						.each(function () {
							var instance = $(this).checkboxradio("instance");

							if (instance) {
								instance._removeClass(instance.label,
									"ui-checkboxradio-checked", "ui-state-active");
							}
						});
				}
			},

			_destroy: function () {
				this._unbindFormResetHandler();

				if (this.icon) {
					this.icon.remove();
					this.iconSpace.remove();
				}
			},

			_setOption: function (key, value) {

				// We don't allow the value to be set to nothing
				if (key === "label" && !value) {
					return;
				}

				this._super(key, value);

				if (key === "disabled") {
					this._toggleClass(this.label, null, "ui-state-disabled", value);
					this.element[0].disabled = value;

					// Don't refresh when setting disabled
					return;
				}
				this.refresh();
			},

			_updateIcon: function (checked) {
				var toAdd = "ui-icon ui-icon-background ";

				if (this.options.icon) {
					if (!this.icon) {
						this.icon = $("<span>");
						this.iconSpace = $("<span> </span>");
						this._addClass(this.iconSpace, "ui-checkboxradio-icon-space");
					}

					if (this.type === "checkbox") {
						toAdd += checked ? "ui-icon-check ui-state-checked" : "ui-icon-blank";
						this._removeClass(this.icon, null, checked ? "ui-icon-blank" : "ui-icon-check");
					} else {
						toAdd += "ui-icon-blank";
					}
					this._addClass(this.icon, "ui-checkboxradio-icon", toAdd);
					if (!checked) {
						this._removeClass(this.icon, null, "ui-icon-check ui-state-checked");
					}
					this.icon.prependTo(this.label).after(this.iconSpace);
				} else if (this.icon !== undefined) {
					this.icon.remove();
					this.iconSpace.remove();
					delete this.icon;
				}
			},

			_updateLabel: function () {

				// Remove the contents of the label ( minus the icon, icon space, and input )
				var contents = this.label.contents().not(this.element[0]);
				if (this.icon) {
					contents = contents.not(this.icon[0]);
				}
				if (this.iconSpace) {
					contents = contents.not(this.iconSpace[0]);
				}
				contents.remove();

				this.label.append(this.options.label);
			},

			refresh: function () {
				var checked = this.element[0].checked,
					isDisabled = this.element[0].disabled;

				this._updateIcon(checked);
				this._toggleClass(this.label, "ui-checkboxradio-checked", "ui-state-active", checked);
				if (this.options.label !== null) {
					this._updateLabel();
				}

				if (isDisabled !== this.options.disabled) {
					this._setOptions({ "disabled": isDisabled });
				}
			}

		}]);

		var widgetsCheckboxradio = $.ui.checkboxradio;


		/*!
		 * jQuery UI Button 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Button
		//>>group: Widgets
		//>>description: Enhances a form with themeable buttons.
		//>>docs: http://api.jqueryui.com/button/
		//>>demos: http://jqueryui.com/button/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/button.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.button", {
			version: "1.12.1",
			defaultElement: "<button>",
			options: {
				classes: {
					"ui-button": "ui-corner-all"
				},
				disabled: null,
				icon: null,
				iconPosition: "beginning",
				label: null,
				showLabel: true
			},

			_getCreateOptions: function () {
				var disabled,

					// This is to support cases like in jQuery Mobile where the base widget does have
					// an implementation of _getCreateOptions
					options = this._super() || {};

				this.isInput = this.element.is("input");

				disabled = this.element[0].disabled;
				if (disabled != null) {
					options.disabled = disabled;
				}

				this.originalLabel = this.isInput ? this.element.val() : this.element.html();
				if (this.originalLabel) {
					options.label = this.originalLabel;
				}

				return options;
			},

			_create: function () {
				if (!this.option.showLabel & !this.options.icon) {
					this.options.showLabel = true;
				}

				// We have to check the option again here even though we did in _getCreateOptions,
				// because null may have been passed on init which would override what was set in
				// _getCreateOptions
				if (this.options.disabled == null) {
					this.options.disabled = this.element[0].disabled || false;
				}

				this.hasTitle = !!this.element.attr("title");

				// Check to see if the label needs to be set or if its already correct
				if (this.options.label && this.options.label !== this.originalLabel) {
					if (this.isInput) {
						this.element.val(this.options.label);
					} else {
						this.element.html(this.options.label);
					}
				}
				this._addClass("ui-button", "ui-widget");
				this._setOption("disabled", this.options.disabled);
				this._enhance();

				if (this.element.is("a")) {
					this._on({
						"keyup": function (event) {
							if (event.keyCode === $.ui.keyCode.SPACE) {
								event.preventDefault();

								// Support: PhantomJS <= 1.9, IE 8 Only
								// If a native click is available use it so we actually cause navigation
								// otherwise just trigger a click event
								if (this.element[0].click) {
									this.element[0].click();
								} else {
									this.element.trigger("click");
								}
							}
						}
					});
				}
			},

			_enhance: function () {
				if (!this.element.is("button")) {
					this.element.attr("role", "button");
				}

				if (this.options.icon) {
					this._updateIcon("icon", this.options.icon);
					this._updateTooltip();
				}
			},

			_updateTooltip: function () {
				this.title = this.element.attr("title");

				if (!this.options.showLabel && !this.title) {
					this.element.attr("title", this.options.label);
				}
			},

			_updateIcon: function (option, value) {
				var icon = option !== "iconPosition",
					position = icon ? this.options.iconPosition : value,
					displayBlock = position === "top" || position === "bottom";

				// Create icon
				if (!this.icon) {
					this.icon = $("<span>");

					this._addClass(this.icon, "ui-button-icon", "ui-icon");

					if (!this.options.showLabel) {
						this._addClass("ui-button-icon-only");
					}
				} else if (icon) {

					// If we are updating the icon remove the old icon class
					this._removeClass(this.icon, null, this.options.icon);
				}

				// If we are updating the icon add the new icon class
				if (icon) {
					this._addClass(this.icon, null, value);
				}

				this._attachIcon(position);

				// If the icon is on top or bottom we need to add the ui-widget-icon-block class and remove
				// the iconSpace if there is one.
				if (displayBlock) {
					this._addClass(this.icon, null, "ui-widget-icon-block");
					if (this.iconSpace) {
						this.iconSpace.remove();
					}
				} else {

					// Position is beginning or end so remove the ui-widget-icon-block class and add the
					// space if it does not exist
					if (!this.iconSpace) {
						this.iconSpace = $("<span> </span>");
						this._addClass(this.iconSpace, "ui-button-icon-space");
					}
					this._removeClass(this.icon, null, "ui-wiget-icon-block");
					this._attachIconSpace(position);
				}
			},

			_destroy: function () {
				this.element.removeAttr("role");

				if (this.icon) {
					this.icon.remove();
				}
				if (this.iconSpace) {
					this.iconSpace.remove();
				}
				if (!this.hasTitle) {
					this.element.removeAttr("title");
				}
			},

			_attachIconSpace: function (iconPosition) {
				this.icon[/^(?:end|bottom)/.test(iconPosition) ? "before" : "after"](this.iconSpace);
			},

			_attachIcon: function (iconPosition) {
				this.element[/^(?:end|bottom)/.test(iconPosition) ? "append" : "prepend"](this.icon);
			},

			_setOptions: function (options) {
				var newShowLabel = options.showLabel === undefined ?
					this.options.showLabel :
					options.showLabel,
					newIcon = options.icon === undefined ? this.options.icon : options.icon;

				if (!newShowLabel && !newIcon) {
					options.showLabel = true;
				}
				this._super(options);
			},

			_setOption: function (key, value) {
				if (key === "icon") {
					if (value) {
						this._updateIcon(key, value);
					} else if (this.icon) {
						this.icon.remove();
						if (this.iconSpace) {
							this.iconSpace.remove();
						}
					}
				}

				if (key === "iconPosition") {
					this._updateIcon(key, value);
				}

				// Make sure we can't end up with a button that has neither text nor icon
				if (key === "showLabel") {
					this._toggleClass("ui-button-icon-only", null, !value);
					this._updateTooltip();
				}

				if (key === "label") {
					if (this.isInput) {
						this.element.val(value);
					} else {

						// If there is an icon, append it, else nothing then append the value
						// this avoids removal of the icon when setting label text
						this.element.html(value);
						if (this.icon) {
							this._attachIcon(this.options.iconPosition);
							this._attachIconSpace(this.options.iconPosition);
						}
					}
				}

				this._super(key, value);

				if (key === "disabled") {
					this._toggleClass(null, "ui-state-disabled", value);
					this.element[0].disabled = value;
					if (value) {
						this.element.blur();
					}
				}
			},

			refresh: function () {

				// Make sure to only check disabled if its an element that supports this otherwise
				// check for the disabled class to determine state
				var isDisabled = this.element.is("input, button") ?
					this.element[0].disabled : this.element.hasClass("ui-button-disabled");

				if (isDisabled !== this.options.disabled) {
					this._setOptions({ disabled: isDisabled });
				}

				this._updateTooltip();
			}
		});

		// DEPRECATED
		if ($.uiBackCompat !== false) {

			// Text and Icons options
			$.widget("ui.button", $.ui.button, {
				options: {
					text: true,
					icons: {
						primary: null,
						secondary: null
					}
				},

				_create: function () {
					if (this.options.showLabel && !this.options.text) {
						this.options.showLabel = this.options.text;
					}
					if (!this.options.showLabel && this.options.text) {
						this.options.text = this.options.showLabel;
					}
					if (!this.options.icon && (this.options.icons.primary ||
						this.options.icons.secondary)) {
						if (this.options.icons.primary) {
							this.options.icon = this.options.icons.primary;
						} else {
							this.options.icon = this.options.icons.secondary;
							this.options.iconPosition = "end";
						}
					} else if (this.options.icon) {
						this.options.icons.primary = this.options.icon;
					}
					this._super();
				},

				_setOption: function (key, value) {
					if (key === "text") {
						this._super("showLabel", value);
						return;
					}
					if (key === "showLabel") {
						this.options.text = value;
					}
					if (key === "icon") {
						this.options.icons.primary = value;
					}
					if (key === "icons") {
						if (value.primary) {
							this._super("icon", value.primary);
							this._super("iconPosition", "beginning");
						} else if (value.secondary) {
							this._super("icon", value.secondary);
							this._super("iconPosition", "end");
						}
					}
					this._superApply(arguments);
				}
			});

			$.fn.button = (function (orig) {
				return function () {
					if (!this.length || (this.length && this[0].tagName !== "INPUT") ||
						(this.length && this[0].tagName === "INPUT" && (
							this.attr("type") !== "checkbox" && this.attr("type") !== "radio"
						))) {
						return orig.apply(this, arguments);
					}
					if (!$.ui.checkboxradio) {
						$.error("Checkboxradio widget missing");
					}
					if (arguments.length === 0) {
						return this.checkboxradio({
							"icon": false
						});
					}
					return this.checkboxradio.apply(this, arguments);
				};
			})($.fn.button);

			$.fn.buttonset = function () {
				if (!$.ui.controlgroup) {
					$.error("Controlgroup widget missing");
				}
				if (arguments[0] === "option" && arguments[1] === "items" && arguments[2]) {
					return this.controlgroup.apply(this,
						[arguments[0], "items.button", arguments[2]]);
				}
				if (arguments[0] === "option" && arguments[1] === "items") {
					return this.controlgroup.apply(this, [arguments[0], "items.button"]);
				}
				if (typeof arguments[0] === "object" && arguments[0].items) {
					arguments[0].items = {
						button: arguments[0].items
					};
				}
				return this.controlgroup.apply(this, arguments);
			};
		}

		var widgetsButton = $.ui.button;


		// jscs:disable maximumLineLength
		/* jscs:disable requireCamelCaseOrUpperCaseIdentifiers */
		/*!
		 * jQuery UI Datepicker 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Datepicker
		//>>group: Widgets
		//>>description: Displays a calendar from an input or inline for selecting dates.
		//>>docs: http://api.jqueryui.com/datepicker/
		//>>demos: http://jqueryui.com/datepicker/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/datepicker.css
		//>>css.theme: ../../themes/base/theme.css



		$.extend($.ui, { datepicker: { version: "1.12.1" } });

		var datepicker_instActive;

		function datepicker_getZindex(elem) {
			var position, value;
			while (elem.length && elem[0] !== document) {

				// Ignore z-index if position is set to a value where z-index is ignored by the browser
				// This makes behavior of this function consistent across browsers
				// WebKit always returns auto if the element is positioned
				position = elem.css("position");
				if (position === "absolute" || position === "relative" || position === "fixed") {

					// IE returns 0 when zIndex is not specified
					// other browsers return a string
					// we ignore the case of nested elements with an explicit value of 0
					// <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
					value = parseInt(elem.css("zIndex"), 10);
					if (!isNaN(value) && value !== 0) {
						return value;
					}
				}
				elem = elem.parent();
			}

			return 0;
		}
		/* Date picker manager.
		   Use the singleton instance of this class, $.datepicker, to interact with the date picker.
		   Settings for (groups of) date pickers are maintained in an instance object,
		   allowing multiple different settings on the same page. */

		function Datepicker() {
			this._curInst = null; // The current instance in use
			this._keyEvent = false; // If the last event was a key event
			this._disabledInputs = []; // List of date picker inputs that have been disabled
			this._datepickerShowing = false; // True if the popup picker is showing , false if not
			this._inDialog = false; // True if showing within a "dialog", false if not
			this._mainDivId = "ui-datepicker-div"; // The ID of the main datepicker division
			this._inlineClass = "ui-datepicker-inline"; // The name of the inline marker class
			this._appendClass = "ui-datepicker-append"; // The name of the append marker class
			this._triggerClass = "ui-datepicker-trigger"; // The name of the trigger marker class
			this._dialogClass = "ui-datepicker-dialog"; // The name of the dialog marker class
			this._disableClass = "ui-datepicker-disabled"; // The name of the disabled covering marker class
			this._unselectableClass = "ui-datepicker-unselectable"; // The name of the unselectable cell marker class
			this._currentClass = "ui-datepicker-current-day"; // The name of the current day marker class
			this._dayOverClass = "ui-datepicker-days-cell-over"; // The name of the day hover marker class
			this.regional = []; // Available regional settings, indexed by language code
			this.regional[""] = { // Default regional settings
				closeText: "Done", // Display text for close link
				prevText: "Prev", // Display text for previous month link
				nextText: "Next", // Display text for next month link
				currentText: "Today", // Display text for current month link
				monthNames: ["January", "February", "March", "April", "May", "June",
					"July", "August", "September", "October", "November", "December"], // Names of months for drop-down and formatting
				monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], // For formatting
				dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], // For formatting
				dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"], // For formatting
				dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"], // Column headings for days starting at Sunday
				weekHeader: "Wk", // Column header for week of the year
				dateFormat: "mm/dd/yy", // See format options on parseDate
				firstDay: 0, // The first day of the week, Sun = 0, Mon = 1, ...
				isRTL: false, // True if right-to-left language, false if left-to-right
				showMonthAfterYear: false, // True if the year select precedes month, false for month then year
				yearSuffix: "" // Additional text to append to the year in the month headers
			};
			this._defaults = { // Global defaults for all the date picker instances
				showOn: "focus", // "focus" for popup on focus,
				// "button" for trigger button, or "both" for either
				showAnim: "fadeIn", // Name of jQuery animation for popup
				showOptions: {}, // Options for enhanced animations
				defaultDate: null, // Used when field is blank: actual date,
				// +/-number for offset from today, null for today
				appendText: "", // Display text following the input box, e.g. showing the format
				buttonText: "...", // Text for trigger button
				buttonImage: "", // URL for trigger button image
				buttonImageOnly: false, // True if the image appears alone, false if it appears on a button
				hideIfNoPrevNext: false, // True to hide next/previous month links
				// if not applicable, false to just disable them
				navigationAsDateFormat: false, // True if date formatting applied to prev/today/next links
				gotoCurrent: false, // True if today link goes back to current selection instead
				changeMonth: false, // True if month can be selected directly, false if only prev/next
				changeYear: false, // True if year can be selected directly, false if only prev/next
				yearRange: "c-10:c+10", // Range of years to display in drop-down,
				// either relative to today's year (-nn:+nn), relative to currently displayed year
				// (c-nn:c+nn), absolute (nnnn:nnnn), or a combination of the above (nnnn:-n)
				showOtherMonths: false, // True to show dates in other months, false to leave blank
				selectOtherMonths: false, // True to allow selection of dates in other months, false for unselectable
				showWeek: false, // True to show week of the year, false to not show it
				calculateWeek: this.iso8601Week, // How to calculate the week of the year,
				// takes a Date and returns the number of the week for it
				shortYearCutoff: "+10", // Short year values < this are in the current century,
				// > this are in the previous century,
				// string value starting with "+" for current year + value
				minDate: null, // The earliest selectable date, or null for no limit
				maxDate: null, // The latest selectable date, or null for no limit
				duration: "fast", // Duration of display/closure
				beforeShowDay: null, // Function that takes a date and returns an array with
				// [0] = true if selectable, false if not, [1] = custom CSS class name(s) or "",
				// [2] = cell title (optional), e.g. $.datepicker.noWeekends
				beforeShow: null, // Function that takes an input field and
				// returns a set of custom settings for the date picker
				onSelect: null, // Define a callback function when a date is selected
				onChangeMonthYear: null, // Define a callback function when the month or year is changed
				onClose: null, // Define a callback function when the datepicker is closed
				numberOfMonths: 1, // Number of months to show at a time
				showCurrentAtPos: 0, // The position in multipe months at which to show the current month (starting at 0)
				stepMonths: 1, // Number of months to step back/forward
				stepBigMonths: 12, // Number of months to step back/forward for the big links
				altField: "", // Selector for an alternate field to store selected dates into
				altFormat: "", // The date format to use for the alternate field
				constrainInput: true, // The input is constrained by the current date format
				showButtonPanel: false, // True to show button panel, false to not show it
				autoSize: false, // True to size the input for the date format, false to leave as is
				disabled: false // The initial disabled state
			};
			$.extend(this._defaults, this.regional[""]);
			this.regional.en = $.extend(true, {}, this.regional[""]);
			this.regional["en-US"] = $.extend(true, {}, this.regional.en);
			this.dpDiv = datepicker_bindHover($("<div id='" + this._mainDivId + "' class='ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>"));
		}

		$.extend(Datepicker.prototype, {
			/* Class name added to elements to indicate already configured with a date picker. */
			markerClassName: "hasDatepicker",

			//Keep track of the maximum number of rows displayed (see #7043)
			maxRows: 4,

			// TODO rename to "widget" when switching to widget factory
			_widgetDatepicker: function () {
				return this.dpDiv;
			},

			/* Override the default settings for all instances of the date picker.
			 * @param  settings  object - the new settings to use as defaults (anonymous object)
			 * @return the manager object
			 */
			setDefaults: function (settings) {
				datepicker_extendRemove(this._defaults, settings || {});
				return this;
			},

			/* Attach the date picker to a jQuery selection.
			 * @param  target	element - the target input field or division or span
			 * @param  settings  object - the new settings to use for this date picker instance (anonymous)
			 */
			_attachDatepicker: function (target, settings) {
				var nodeName, inline, inst;
				nodeName = target.nodeName.toLowerCase();
				inline = (nodeName === "div" || nodeName === "span");
				if (!target.id) {
					this.uuid += 1;
					target.id = "dp" + this.uuid;
				}
				inst = this._newInst($(target), inline);
				inst.settings = $.extend({}, settings || {});
				if (nodeName === "input") {
					this._connectDatepicker(target, inst);
				} else if (inline) {
					this._inlineDatepicker(target, inst);
				}
			},

			/* Create a new instance object. */
			_newInst: function (target, inline) {
				var id = target[0].id.replace(/([^A-Za-z0-9_\-])/g, "\\\\$1"); // escape jQuery meta chars
				return {
					id: id, input: target, // associated target
					selectedDay: 0, selectedMonth: 0, selectedYear: 0, // current selection
					drawMonth: 0, drawYear: 0, // month being drawn
					inline: inline, // is datepicker inline or not
					dpDiv: (!inline ? this.dpDiv : // presentation div
						datepicker_bindHover($("<div class='" + this._inlineClass + " ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>")))
				};
			},

			/* Attach the date picker to an input field. */
			_connectDatepicker: function (target, inst) {
				var input = $(target);
				inst.append = $([]);
				inst.trigger = $([]);
				if (input.hasClass(this.markerClassName)) {
					return;
				}
				this._attachments(input, inst);
				input.addClass(this.markerClassName).on("keydown", this._doKeyDown).
					on("keypress", this._doKeyPress).on("keyup", this._doKeyUp);
				this._autoSize(inst);
				$.data(target, "datepicker", inst);

				//If disabled option is true, disable the datepicker once it has been attached to the input (see ticket #5665)
				if (inst.settings.disabled) {
					this._disableDatepicker(target);
				}
			},

			/* Make attachments based on settings. */
			_attachments: function (input, inst) {
				var showOn, buttonText, buttonImage,
					appendText = this._get(inst, "appendText"),
					isRTL = this._get(inst, "isRTL");

				if (inst.append) {
					inst.append.remove();
				}
				if (appendText) {
					inst.append = $("<span class='" + this._appendClass + "'>" + appendText + "</span>");
					input[isRTL ? "before" : "after"](inst.append);
				}

				input.off("focus", this._showDatepicker);

				if (inst.trigger) {
					inst.trigger.remove();
				}

				showOn = this._get(inst, "showOn");
				if (showOn === "focus" || showOn === "both") { // pop-up date picker when in the marked field
					input.on("focus", this._showDatepicker);
				}
				if (showOn === "button" || showOn === "both") { // pop-up date picker when button clicked
					buttonText = this._get(inst, "buttonText");
					buttonImage = this._get(inst, "buttonImage");
					inst.trigger = $(this._get(inst, "buttonImageOnly") ?
						$("<img/>").addClass(this._triggerClass).
							attr({ src: buttonImage, alt: buttonText, title: buttonText }) :
						$("<button type='button'></button>").addClass(this._triggerClass).
							html(!buttonImage ? buttonText : $("<img/>").attr(
								{ src: buttonImage, alt: buttonText, title: buttonText })));
					input[isRTL ? "before" : "after"](inst.trigger);
					inst.trigger.on("click", function () {
						if ($.datepicker._datepickerShowing && $.datepicker._lastInput === input[0]) {
							$.datepicker._hideDatepicker();
						} else if ($.datepicker._datepickerShowing && $.datepicker._lastInput !== input[0]) {
							$.datepicker._hideDatepicker();
							$.datepicker._showDatepicker(input[0]);
						} else {
							$.datepicker._showDatepicker(input[0]);
						}
						return false;
					});
				}
			},

			/* Apply the maximum length for the date format. */
			_autoSize: function (inst) {
				if (this._get(inst, "autoSize") && !inst.inline) {
					var findMax, max, maxI, i,
						date = new Date(2009, 12 - 1, 20), // Ensure double digits
						dateFormat = this._get(inst, "dateFormat");

					if (dateFormat.match(/[DM]/)) {
						findMax = function (names) {
							max = 0;
							maxI = 0;
							for (i = 0; i < names.length; i++) {
								if (names[i].length > max) {
									max = names[i].length;
									maxI = i;
								}
							}
							return maxI;
						};
						date.setMonth(findMax(this._get(inst, (dateFormat.match(/MM/) ?
							"monthNames" : "monthNamesShort"))));
						date.setDate(findMax(this._get(inst, (dateFormat.match(/DD/) ?
							"dayNames" : "dayNamesShort"))) + 20 - date.getDay());
					}
					inst.input.attr("size", this._formatDate(inst, date).length);
				}
			},

			/* Attach an inline date picker to a div. */
			_inlineDatepicker: function (target, inst) {
				var divSpan = $(target);
				if (divSpan.hasClass(this.markerClassName)) {
					return;
				}
				divSpan.addClass(this.markerClassName).append(inst.dpDiv);
				$.data(target, "datepicker", inst);
				this._setDate(inst, this._getDefaultDate(inst), true);
				this._updateDatepicker(inst);
				this._updateAlternate(inst);

				//If disabled option is true, disable the datepicker before showing it (see ticket #5665)
				if (inst.settings.disabled) {
					this._disableDatepicker(target);
				}

				// Set display:block in place of inst.dpDiv.show() which won't work on disconnected elements
				// http://bugs.jqueryui.com/ticket/7552 - A Datepicker created on a detached div has zero height
				inst.dpDiv.css("display", "block");
			},

			/* Pop-up the date picker in a "dialog" box.
			 * @param  input element - ignored
			 * @param  date	string or Date - the initial date to display
			 * @param  onSelect  function - the function to call when a date is selected
			 * @param  settings  object - update the dialog date picker instance's settings (anonymous object)
			 * @param  pos int[2] - coordinates for the dialog's position within the screen or
			 *					event - with x/y coordinates or
			 *					leave empty for default (screen centre)
			 * @return the manager object
			 */
			_dialogDatepicker: function (input, date, onSelect, settings, pos) {
				var id, browserWidth, browserHeight, scrollX, scrollY,
					inst = this._dialogInst; // internal instance

				if (!inst) {
					this.uuid += 1;
					id = "dp" + this.uuid;
					this._dialogInput = $("<input type='text' id='" + id +
						"' style='position: absolute; top: -100px; width: 0px;'/>");
					this._dialogInput.on("keydown", this._doKeyDown);
					$("body").append(this._dialogInput);
					inst = this._dialogInst = this._newInst(this._dialogInput, false);
					inst.settings = {};
					$.data(this._dialogInput[0], "datepicker", inst);
				}
				datepicker_extendRemove(inst.settings, settings || {});
				date = (date && date.constructor === Date ? this._formatDate(inst, date) : date);
				this._dialogInput.val(date);

				this._pos = (pos ? (pos.length ? pos : [pos.pageX, pos.pageY]) : null);
				if (!this._pos) {
					browserWidth = document.documentElement.clientWidth;
					browserHeight = document.documentElement.clientHeight;
					scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
					scrollY = document.documentElement.scrollTop || document.body.scrollTop;
					this._pos = // should use actual width/height below
						[(browserWidth / 2) - 100 + scrollX, (browserHeight / 2) - 150 + scrollY];
				}

				// Move input on screen for focus, but hidden behind dialog
				this._dialogInput.css("left", (this._pos[0] + 20) + "px").css("top", this._pos[1] + "px");
				inst.settings.onSelect = onSelect;
				this._inDialog = true;
				this.dpDiv.addClass(this._dialogClass);
				this._showDatepicker(this._dialogInput[0]);
				if ($.blockUI) {
					$.blockUI(this.dpDiv);
				}
				$.data(this._dialogInput[0], "datepicker", inst);
				return this;
			},

			/* Detach a datepicker from its control.
			 * @param  target	element - the target input field or division or span
			 */
			_destroyDatepicker: function (target) {
				var nodeName,
					$target = $(target),
					inst = $.data(target, "datepicker");

				if (!$target.hasClass(this.markerClassName)) {
					return;
				}

				nodeName = target.nodeName.toLowerCase();
				$.removeData(target, "datepicker");
				if (nodeName === "input") {
					inst.append.remove();
					inst.trigger.remove();
					$target.removeClass(this.markerClassName).
						off("focus", this._showDatepicker).
						off("keydown", this._doKeyDown).
						off("keypress", this._doKeyPress).
						off("keyup", this._doKeyUp);
				} else if (nodeName === "div" || nodeName === "span") {
					$target.removeClass(this.markerClassName).empty();
				}

				if (datepicker_instActive === inst) {
					datepicker_instActive = null;
				}
			},

			/* Enable the date picker to a jQuery selection.
			 * @param  target	element - the target input field or division or span
			 */
			_enableDatepicker: function (target) {
				var nodeName, inline,
					$target = $(target),
					inst = $.data(target, "datepicker");

				if (!$target.hasClass(this.markerClassName)) {
					return;
				}

				nodeName = target.nodeName.toLowerCase();
				if (nodeName === "input") {
					target.disabled = false;
					inst.trigger.filter("button").
						each(function () { this.disabled = false; }).end().
						filter("img").css({ opacity: "1.0", cursor: "" });
				} else if (nodeName === "div" || nodeName === "span") {
					inline = $target.children("." + this._inlineClass);
					inline.children().removeClass("ui-state-disabled");
					inline.find("select.ui-datepicker-month, select.ui-datepicker-year").
						prop("disabled", false);
				}
				this._disabledInputs = $.map(this._disabledInputs,
					function (value) { return (value === target ? null : value); }); // delete entry
			},

			/* Disable the date picker to a jQuery selection.
			 * @param  target	element - the target input field or division or span
			 */
			_disableDatepicker: function (target) {
				var nodeName, inline,
					$target = $(target),
					inst = $.data(target, "datepicker");

				if (!$target.hasClass(this.markerClassName)) {
					return;
				}

				nodeName = target.nodeName.toLowerCase();
				if (nodeName === "input") {
					target.disabled = true;
					inst.trigger.filter("button").
						each(function () { this.disabled = true; }).end().
						filter("img").css({ opacity: "0.5", cursor: "default" });
				} else if (nodeName === "div" || nodeName === "span") {
					inline = $target.children("." + this._inlineClass);
					inline.children().addClass("ui-state-disabled");
					inline.find("select.ui-datepicker-month, select.ui-datepicker-year").
						prop("disabled", true);
				}
				this._disabledInputs = $.map(this._disabledInputs,
					function (value) { return (value === target ? null : value); }); // delete entry
				this._disabledInputs[this._disabledInputs.length] = target;
			},

			/* Is the first field in a jQuery collection disabled as a datepicker?
			 * @param  target	element - the target input field or division or span
			 * @return boolean - true if disabled, false if enabled
			 */
			_isDisabledDatepicker: function (target) {
				if (!target) {
					return false;
				}
				for (var i = 0; i < this._disabledInputs.length; i++) {
					if (this._disabledInputs[i] === target) {
						return true;
					}
				}
				return false;
			},

			/* Retrieve the instance data for the target control.
			 * @param  target  element - the target input field or division or span
			 * @return  object - the associated instance data
			 * @throws  error if a jQuery problem getting data
			 */
			_getInst: function (target) {
				try {
					return $.data(target, "datepicker");
				}
				catch (err) {
					throw "Missing instance data for this datepicker";
				}
			},

			/* Update or retrieve the settings for a date picker attached to an input field or division.
			 * @param  target  element - the target input field or division or span
			 * @param  name	object - the new settings to update or
			 *				string - the name of the setting to change or retrieve,
			 *				when retrieving also "all" for all instance settings or
			 *				"defaults" for all global defaults
			 * @param  value   any - the new value for the setting
			 *				(omit if above is an object or to retrieve a value)
			 */
			_optionDatepicker: function (target, name, value) {
				var settings, date, minDate, maxDate,
					inst = this._getInst(target);

				if (arguments.length === 2 && typeof name === "string") {
					return (name === "defaults" ? $.extend({}, $.datepicker._defaults) :
						(inst ? (name === "all" ? $.extend({}, inst.settings) :
							this._get(inst, name)) : null));
				}

				settings = name || {};
				if (typeof name === "string") {
					settings = {};
					settings[name] = value;
				}

				if (inst) {
					if (this._curInst === inst) {
						this._hideDatepicker();
					}

					date = this._getDateDatepicker(target, true);
					minDate = this._getMinMaxDate(inst, "min");
					maxDate = this._getMinMaxDate(inst, "max");
					datepicker_extendRemove(inst.settings, settings);

					// reformat the old minDate/maxDate values if dateFormat changes and a new minDate/maxDate isn't provided
					if (minDate !== null && settings.dateFormat !== undefined && settings.minDate === undefined) {
						inst.settings.minDate = this._formatDate(inst, minDate);
					}
					if (maxDate !== null && settings.dateFormat !== undefined && settings.maxDate === undefined) {
						inst.settings.maxDate = this._formatDate(inst, maxDate);
					}
					if ("disabled" in settings) {
						if (settings.disabled) {
							this._disableDatepicker(target);
						} else {
							this._enableDatepicker(target);
						}
					}
					this._attachments($(target), inst);
					this._autoSize(inst);
					this._setDate(inst, date);
					this._updateAlternate(inst);
					this._updateDatepicker(inst);
				}
			},

			// Change method deprecated
			_changeDatepicker: function (target, name, value) {
				this._optionDatepicker(target, name, value);
			},

			/* Redraw the date picker attached to an input field or division.
			 * @param  target  element - the target input field or division or span
			 */
			_refreshDatepicker: function (target) {
				var inst = this._getInst(target);
				if (inst) {
					this._updateDatepicker(inst);
				}
			},

			/* Set the dates for a jQuery selection.
			 * @param  target element - the target input field or division or span
			 * @param  date	Date - the new date
			 */
			_setDateDatepicker: function (target, date) {
				var inst = this._getInst(target);
				if (inst) {
					this._setDate(inst, date);
					this._updateDatepicker(inst);
					this._updateAlternate(inst);
				}
			},

			/* Get the date(s) for the first entry in a jQuery selection.
			 * @param  target element - the target input field or division or span
			 * @param  noDefault boolean - true if no default date is to be used
			 * @return Date - the current date
			 */
			_getDateDatepicker: function (target, noDefault) {
				var inst = this._getInst(target);
				if (inst && !inst.inline) {
					this._setDateFromField(inst, noDefault);
				}
				return (inst ? this._getDate(inst) : null);
			},

			/* Handle keystrokes. */
			_doKeyDown: function (event) {
				var onSelect, dateStr, sel,
					inst = $.datepicker._getInst(event.target),
					handled = true,
					isRTL = inst.dpDiv.is(".ui-datepicker-rtl");

				inst._keyEvent = true;
				if ($.datepicker._datepickerShowing) {
					switch (event.keyCode) {
						case 9: $.datepicker._hideDatepicker();
							handled = false;
							break; // hide on tab out
						case 13: sel = $("td." + $.datepicker._dayOverClass + ":not(." +
							$.datepicker._currentClass + ")", inst.dpDiv);
							if (sel[0]) {
								$.datepicker._selectDay(event.target, inst.selectedMonth, inst.selectedYear, sel[0]);
							}

							onSelect = $.datepicker._get(inst, "onSelect");
							if (onSelect) {
								dateStr = $.datepicker._formatDate(inst);

								// Trigger custom callback
								onSelect.apply((inst.input ? inst.input[0] : null), [dateStr, inst]);
							} else {
								$.datepicker._hideDatepicker();
							}

							return false; // don't submit the form
						case 27: $.datepicker._hideDatepicker();
							break; // hide on escape
						case 33: $.datepicker._adjustDate(event.target, (event.ctrlKey ?
							-$.datepicker._get(inst, "stepBigMonths") :
							-$.datepicker._get(inst, "stepMonths")), "M");
							break; // previous month/year on page up/+ ctrl
						case 34: $.datepicker._adjustDate(event.target, (event.ctrlKey ?
							+$.datepicker._get(inst, "stepBigMonths") :
							+$.datepicker._get(inst, "stepMonths")), "M");
							break; // next month/year on page down/+ ctrl
						case 35: if (event.ctrlKey || event.metaKey) {
							$.datepicker._clearDate(event.target);
						}
							handled = event.ctrlKey || event.metaKey;
							break; // clear on ctrl or command +end
						case 36: if (event.ctrlKey || event.metaKey) {
							$.datepicker._gotoToday(event.target);
						}
							handled = event.ctrlKey || event.metaKey;
							break; // current on ctrl or command +home
						case 37: if (event.ctrlKey || event.metaKey) {
							$.datepicker._adjustDate(event.target, (isRTL ? +1 : -1), "D");
						}
							handled = event.ctrlKey || event.metaKey;

							// -1 day on ctrl or command +left
							if (event.originalEvent.altKey) {
								$.datepicker._adjustDate(event.target, (event.ctrlKey ?
									-$.datepicker._get(inst, "stepBigMonths") :
									-$.datepicker._get(inst, "stepMonths")), "M");
							}

							// next month/year on alt +left on Mac
							break;
						case 38: if (event.ctrlKey || event.metaKey) {
							$.datepicker._adjustDate(event.target, -7, "D");
						}
							handled = event.ctrlKey || event.metaKey;
							break; // -1 week on ctrl or command +up
						case 39: if (event.ctrlKey || event.metaKey) {
							$.datepicker._adjustDate(event.target, (isRTL ? -1 : +1), "D");
						}
							handled = event.ctrlKey || event.metaKey;

							// +1 day on ctrl or command +right
							if (event.originalEvent.altKey) {
								$.datepicker._adjustDate(event.target, (event.ctrlKey ?
									+$.datepicker._get(inst, "stepBigMonths") :
									+$.datepicker._get(inst, "stepMonths")), "M");
							}

							// next month/year on alt +right
							break;
						case 40: if (event.ctrlKey || event.metaKey) {
							$.datepicker._adjustDate(event.target, +7, "D");
						}
							handled = event.ctrlKey || event.metaKey;
							break; // +1 week on ctrl or command +down
						default: handled = false;
					}
				} else if (event.keyCode === 36 && event.ctrlKey) { // display the date picker on ctrl+home
					$.datepicker._showDatepicker(this);
				} else {
					handled = false;
				}

				if (handled) {
					event.preventDefault();
					event.stopPropagation();
				}
			},

			/* Filter entered characters - based on date format. */
			_doKeyPress: function (event) {
				var chars, chr,
					inst = $.datepicker._getInst(event.target);

				if ($.datepicker._get(inst, "constrainInput")) {
					chars = $.datepicker._possibleChars($.datepicker._get(inst, "dateFormat"));
					chr = String.fromCharCode(event.charCode == null ? event.keyCode : event.charCode);
					return event.ctrlKey || event.metaKey || (chr < " " || !chars || chars.indexOf(chr) > -1);
				}
			},

			/* Synchronise manual entry and field/alternate field. */
			_doKeyUp: function (event) {
				var date,
					inst = $.datepicker._getInst(event.target);

				if (inst.input.val() !== inst.lastVal) {
					try {
						date = $.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
							(inst.input ? inst.input.val() : null),
							$.datepicker._getFormatConfig(inst));

						if (date) { // only if valid
							$.datepicker._setDateFromField(inst);
							$.datepicker._updateAlternate(inst);
							$.datepicker._updateDatepicker(inst);
						}
					}
					catch (err) {
					}
				}
				return true;
			},

			/* Pop-up the date picker for a given input field.
			 * If false returned from beforeShow event handler do not show.
			 * @param  input  element - the input field attached to the date picker or
			 *					event - if triggered by focus
			 */
			_showDatepicker: function (input) {
				input = input.target || input;
				if (input.nodeName.toLowerCase() !== "input") { // find from button/image trigger
					input = $("input", input.parentNode)[0];
				}

				if ($.datepicker._isDisabledDatepicker(input) || $.datepicker._lastInput === input) { // already here
					return;
				}

				var inst, beforeShow, beforeShowSettings, isFixed,
					offset, showAnim, duration;

				inst = $.datepicker._getInst(input);
				if ($.datepicker._curInst && $.datepicker._curInst !== inst) {
					$.datepicker._curInst.dpDiv.stop(true, true);
					if (inst && $.datepicker._datepickerShowing) {
						$.datepicker._hideDatepicker($.datepicker._curInst.input[0]);
					}
				}

				beforeShow = $.datepicker._get(inst, "beforeShow");
				beforeShowSettings = beforeShow ? beforeShow.apply(input, [input, inst]) : {};
				if (beforeShowSettings === false) {
					return;
				}
				datepicker_extendRemove(inst.settings, beforeShowSettings);

				inst.lastVal = null;
				$.datepicker._lastInput = input;
				$.datepicker._setDateFromField(inst);

				if ($.datepicker._inDialog) { // hide cursor
					input.value = "";
				}
				if (!$.datepicker._pos) { // position below input
					$.datepicker._pos = $.datepicker._findPos(input);
					$.datepicker._pos[1] += input.offsetHeight; // add the height
				}

				isFixed = false;
				$(input).parents().each(function () {
					isFixed |= $(this).css("position") === "fixed";
					return !isFixed;
				});

				offset = { left: $.datepicker._pos[0], top: $.datepicker._pos[1] };
				$.datepicker._pos = null;

				//to avoid flashes on Firefox
				inst.dpDiv.empty();

				// determine sizing offscreen
				inst.dpDiv.css({ position: "absolute", display: "block", top: "-1000px" });
				$.datepicker._updateDatepicker(inst);

				// fix width for dynamic number of date pickers
				// and adjust position before showing
				offset = $.datepicker._checkOffset(inst, offset, isFixed);
				inst.dpDiv.css({
					position: ($.datepicker._inDialog && $.blockUI ?
						"static" : (isFixed ? "fixed" : "absolute")), display: "none",
					left: offset.left + "px", top: offset.top + "px"
				});

				if (!inst.inline) {
					showAnim = $.datepicker._get(inst, "showAnim");
					duration = $.datepicker._get(inst, "duration");
					inst.dpDiv.css("z-index", datepicker_getZindex($(input)) + 1);
					$.datepicker._datepickerShowing = true;

					if ($.effects && $.effects.effect[showAnim]) {
						inst.dpDiv.show(showAnim, $.datepicker._get(inst, "showOptions"), duration);
					} else {
						inst.dpDiv[showAnim || "show"](showAnim ? duration : null);
					}

					if ($.datepicker._shouldFocusInput(inst)) {
						inst.input.trigger("focus");
					}

					$.datepicker._curInst = inst;
				}
			},

			/* Generate the date picker content. */
			_updateDatepicker: function (inst) {
				this.maxRows = 4; //Reset the max number of rows being displayed (see #7043)
				datepicker_instActive = inst; // for delegate hover events
				inst.dpDiv.empty().append(this._generateHTML(inst));
				this._attachHandlers(inst);

				var origyearshtml,
					numMonths = this._getNumberOfMonths(inst),
					cols = numMonths[1],
					width = 17,
					activeCell = inst.dpDiv.find("." + this._dayOverClass + " a");

				if (activeCell.length > 0) {
					datepicker_handleMouseover.apply(activeCell.get(0));
				}

				inst.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");
				if (cols > 1) {
					inst.dpDiv.addClass("ui-datepicker-multi-" + cols).css("width", (width * cols) + "em");
				}
				inst.dpDiv[(numMonths[0] !== 1 || numMonths[1] !== 1 ? "add" : "remove") +
					"Class"]("ui-datepicker-multi");
				inst.dpDiv[(this._get(inst, "isRTL") ? "add" : "remove") +
					"Class"]("ui-datepicker-rtl");

				if (inst === $.datepicker._curInst && $.datepicker._datepickerShowing && $.datepicker._shouldFocusInput(inst)) {
					inst.input.trigger("focus");
				}

				// Deffered render of the years select (to avoid flashes on Firefox)
				if (inst.yearshtml) {
					origyearshtml = inst.yearshtml;
					setTimeout(function () {

						//assure that inst.yearshtml didn't change.
						if (origyearshtml === inst.yearshtml && inst.yearshtml) {
							inst.dpDiv.find("select.ui-datepicker-year:first").replaceWith(inst.yearshtml);
						}
						origyearshtml = inst.yearshtml = null;
					}, 0);
				}
			},

			// #6694 - don't focus the input if it's already focused
			// this breaks the change event in IE
			// Support: IE and jQuery <1.9
			_shouldFocusInput: function (inst) {
				return inst.input && inst.input.is(":visible") && !inst.input.is(":disabled") && !inst.input.is(":focus");
			},

			/* Check positioning to remain on screen. */
			_checkOffset: function (inst, offset, isFixed) {
				var dpWidth = inst.dpDiv.outerWidth(),
					dpHeight = inst.dpDiv.outerHeight(),
					inputWidth = inst.input ? inst.input.outerWidth() : 0,
					inputHeight = inst.input ? inst.input.outerHeight() : 0,
					viewWidth = document.documentElement.clientWidth + (isFixed ? 0 : $(document).scrollLeft()),
					viewHeight = document.documentElement.clientHeight + (isFixed ? 0 : $(document).scrollTop());

				offset.left -= (this._get(inst, "isRTL") ? (dpWidth - inputWidth) : 0);
				offset.left -= (isFixed && offset.left === inst.input.offset().left) ? $(document).scrollLeft() : 0;
				offset.top -= (isFixed && offset.top === (inst.input.offset().top + inputHeight)) ? $(document).scrollTop() : 0;

				// Now check if datepicker is showing outside window viewport - move to a better place if so.
				offset.left -= Math.min(offset.left, (offset.left + dpWidth > viewWidth && viewWidth > dpWidth) ?
					Math.abs(offset.left + dpWidth - viewWidth) : 0);
				offset.top -= Math.min(offset.top, (offset.top + dpHeight > viewHeight && viewHeight > dpHeight) ?
					Math.abs(dpHeight + inputHeight) : 0);

				return offset;
			},

			/* Find an object's position on the screen. */
			_findPos: function (obj) {
				var position,
					inst = this._getInst(obj),
					isRTL = this._get(inst, "isRTL");

				while (obj && (obj.type === "hidden" || obj.nodeType !== 1 || $.expr.filters.hidden(obj))) {
					obj = obj[isRTL ? "previousSibling" : "nextSibling"];
				}

				position = $(obj).offset();
				return [position.left, position.top];
			},

			/* Hide the date picker from view.
			 * @param  input  element - the input field attached to the date picker
			 */
			_hideDatepicker: function (input) {
				var showAnim, duration, postProcess, onClose,
					inst = this._curInst;

				if (!inst || (input && inst !== $.data(input, "datepicker"))) {
					return;
				}

				if (this._datepickerShowing) {
					showAnim = this._get(inst, "showAnim");
					duration = this._get(inst, "duration");
					postProcess = function () {
						$.datepicker._tidyDialog(inst);
					};

					// DEPRECATED: after BC for 1.8.x $.effects[ showAnim ] is not needed
					if ($.effects && ($.effects.effect[showAnim] || $.effects[showAnim])) {
						inst.dpDiv.hide(showAnim, $.datepicker._get(inst, "showOptions"), duration, postProcess);
					} else {
						inst.dpDiv[(showAnim === "slideDown" ? "slideUp" :
							(showAnim === "fadeIn" ? "fadeOut" : "hide"))]((showAnim ? duration : null), postProcess);
					}

					if (!showAnim) {
						postProcess();
					}
					this._datepickerShowing = false;

					onClose = this._get(inst, "onClose");
					if (onClose) {
						onClose.apply((inst.input ? inst.input[0] : null), [(inst.input ? inst.input.val() : ""), inst]);
					}

					this._lastInput = null;
					if (this._inDialog) {
						this._dialogInput.css({ position: "absolute", left: "0", top: "-100px" });
						if ($.blockUI) {
							$.unblockUI();
							$("body").append(this.dpDiv);
						}
					}
					this._inDialog = false;
				}
			},

			/* Tidy up after a dialog display. */
			_tidyDialog: function (inst) {
				inst.dpDiv.removeClass(this._dialogClass).off(".ui-datepicker-calendar");
			},

			/* Close date picker if clicked elsewhere. */
			_checkExternalClick: function (event) {
				if (!$.datepicker._curInst) {
					return;
				}

				var $target = $(event.target),
					inst = $.datepicker._getInst($target[0]);

				if ((($target[0].id !== $.datepicker._mainDivId &&
					$target.parents("#" + $.datepicker._mainDivId).length === 0 &&
					!$target.hasClass($.datepicker.markerClassName) &&
					!$target.closest("." + $.datepicker._triggerClass).length &&
					$.datepicker._datepickerShowing && !($.datepicker._inDialog && $.blockUI))) ||
					($target.hasClass($.datepicker.markerClassName) && $.datepicker._curInst !== inst)) {
					$.datepicker._hideDatepicker();
				}
			},

			/* Adjust one of the date sub-fields. */
			_adjustDate: function (id, offset, period) {
				var target = $(id),
					inst = this._getInst(target[0]);

				if (this._isDisabledDatepicker(target[0])) {
					return;
				}
				this._adjustInstDate(inst, offset +
					(period === "M" ? this._get(inst, "showCurrentAtPos") : 0), // undo positioning
					period);
				this._updateDatepicker(inst);
			},

			/* Action for current link. */
			_gotoToday: function (id) {
				var date,
					target = $(id),
					inst = this._getInst(target[0]);

				if (this._get(inst, "gotoCurrent") && inst.currentDay) {
					inst.selectedDay = inst.currentDay;
					inst.drawMonth = inst.selectedMonth = inst.currentMonth;
					inst.drawYear = inst.selectedYear = inst.currentYear;
				} else {
					date = new Date();
					inst.selectedDay = date.getDate();
					inst.drawMonth = inst.selectedMonth = date.getMonth();
					inst.drawYear = inst.selectedYear = date.getFullYear();
				}
				this._notifyChange(inst);
				this._adjustDate(target);
			},

			/* Action for selecting a new month/year. */
			_selectMonthYear: function (id, select, period) {
				var target = $(id),
					inst = this._getInst(target[0]);

				inst["selected" + (period === "M" ? "Month" : "Year")] =
					inst["draw" + (period === "M" ? "Month" : "Year")] =
					parseInt(select.options[select.selectedIndex].value, 10);

				this._notifyChange(inst);
				this._adjustDate(target);
			},

			/* Action for selecting a day. */
			_selectDay: function (id, month, year, td) {
				var inst,
					target = $(id);

				if ($(td).hasClass(this._unselectableClass) || this._isDisabledDatepicker(target[0])) {
					return;
				}

				inst = this._getInst(target[0]);
				inst.selectedDay = inst.currentDay = $("a", td).html();
				inst.selectedMonth = inst.currentMonth = month;
				inst.selectedYear = inst.currentYear = year;
				this._selectDate(id, this._formatDate(inst,
					inst.currentDay, inst.currentMonth, inst.currentYear));
			},

			/* Erase the input field and hide the date picker. */
			_clearDate: function (id) {
				var target = $(id);
				this._selectDate(target, "");
			},

			/* Update the input field with the selected date. */
			_selectDate: function (id, dateStr) {
				var onSelect,
					target = $(id),
					inst = this._getInst(target[0]);

				dateStr = (dateStr != null ? dateStr : this._formatDate(inst));
				if (inst.input) {
					inst.input.val(dateStr);
				}
				this._updateAlternate(inst);

				onSelect = this._get(inst, "onSelect");
				if (onSelect) {
					onSelect.apply((inst.input ? inst.input[0] : null), [dateStr, inst]);  // trigger custom callback
				} else if (inst.input) {
					inst.input.trigger("change"); // fire the change event
				}

				if (inst.inline) {
					this._updateDatepicker(inst);
				} else {
					this._hideDatepicker();
					this._lastInput = inst.input[0];
					if (typeof (inst.input[0]) !== "object") {
						inst.input.trigger("focus"); // restore focus
					}
					this._lastInput = null;
				}
			},

			/* Update any alternate field to synchronise with the main field. */
			_updateAlternate: function (inst) {
				var altFormat, date, dateStr,
					altField = this._get(inst, "altField");

				if (altField) { // update alternate field too
					altFormat = this._get(inst, "altFormat") || this._get(inst, "dateFormat");
					date = this._getDate(inst);
					dateStr = this.formatDate(altFormat, date, this._getFormatConfig(inst));
					$(altField).val(dateStr);
				}
			},

			/* Set as beforeShowDay function to prevent selection of weekends.
			 * @param  date  Date - the date to customise
			 * @return [boolean, string] - is this date selectable?, what is its CSS class?
			 */
			noWeekends: function (date) {
				var day = date.getDay();
				return [(day > 0 && day < 6), ""];
			},

			/* Set as calculateWeek to determine the week of the year based on the ISO 8601 definition.
			 * @param  date  Date - the date to get the week for
			 * @return  number - the number of the week within the year that contains this date
			 */
			iso8601Week: function (date) {
				var time,
					checkDate = new Date(date.getTime());

				// Find Thursday of this week starting on Monday
				checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay() || 7));

				time = checkDate.getTime();
				checkDate.setMonth(0); // Compare with Jan 1
				checkDate.setDate(1);
				return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
			},

			/* Parse a string value into a date object.
			 * See formatDate below for the possible formats.
			 *
			 * @param  format string - the expected format of the date
			 * @param  value string - the date in the above format
			 * @param  settings Object - attributes include:
			 *					shortYearCutoff  number - the cutoff year for determining the century (optional)
			 *					dayNamesShort	string[7] - abbreviated names of the days from Sunday (optional)
			 *					dayNames		string[7] - names of the days from Sunday (optional)
			 *					monthNamesShort string[12] - abbreviated names of the months (optional)
			 *					monthNames		string[12] - names of the months (optional)
			 * @return  Date - the extracted date value or null if value is blank
			 */
			parseDate: function (format, value, settings) {
				if (format == null || value == null) {
					throw "Invalid arguments";
				}

				value = (typeof value === "object" ? value.toString() : value + "");
				if (value === "") {
					return null;
				}

				var iFormat, dim, extra,
					iValue = 0,
					shortYearCutoffTemp = (settings ? settings.shortYearCutoff : null) || this._defaults.shortYearCutoff,
					shortYearCutoff = (typeof shortYearCutoffTemp !== "string" ? shortYearCutoffTemp :
						new Date().getFullYear() % 100 + parseInt(shortYearCutoffTemp, 10)),
					dayNamesShort = (settings ? settings.dayNamesShort : null) || this._defaults.dayNamesShort,
					dayNames = (settings ? settings.dayNames : null) || this._defaults.dayNames,
					monthNamesShort = (settings ? settings.monthNamesShort : null) || this._defaults.monthNamesShort,
					monthNames = (settings ? settings.monthNames : null) || this._defaults.monthNames,
					year = -1,
					month = -1,
					day = -1,
					doy = -1,
					literal = false,
					date,

					// Check whether a format character is doubled
					lookAhead = function (match) {
						var matches = (iFormat + 1 < format.length && format.charAt(iFormat + 1) === match);
						if (matches) {
							iFormat++;
						}
						return matches;
					},

					// Extract a number from the string value
					getNumber = function (match) {
						var isDoubled = lookAhead(match),
							size = (match === "@" ? 14 : (match === "!" ? 20 :
								(match === "y" && isDoubled ? 4 : (match === "o" ? 3 : 2)))),
							minSize = (match === "y" ? size : 1),
							digits = new RegExp("^\\d{" + minSize + "," + size + "}"),
							num = value.substring(iValue).match(digits);
						if (!num) {
							throw "Missing number at position " + iValue;
						}
						iValue += num[0].length;
						return parseInt(num[0], 10);
					},

					// Extract a name from the string value and convert to an index
					getName = function (match, shortNames, longNames) {
						var index = -1,
							names = $.map(lookAhead(match) ? longNames : shortNames, function (v, k) {
								return [[k, v]];
							}).sort(function (a, b) {
								return -(a[1].length - b[1].length);
							});

						$.each(names, function (i, pair) {
							var name = pair[1];
							if (value.substr(iValue, name.length).toLowerCase() === name.toLowerCase()) {
								index = pair[0];
								iValue += name.length;
								return false;
							}
						});
						if (index !== -1) {
							return index + 1;
						} else {
							throw "Unknown name at position " + iValue;
						}
					},

					// Confirm that a literal character matches the string value
					checkLiteral = function () {
						if (value.charAt(iValue) !== format.charAt(iFormat)) {
							throw "Unexpected literal at position " + iValue;
						}
						iValue++;
					};

				for (iFormat = 0; iFormat < format.length; iFormat++) {
					if (literal) {
						if (format.charAt(iFormat) === "'" && !lookAhead("'")) {
							literal = false;
						} else {
							checkLiteral();
						}
					} else {
						switch (format.charAt(iFormat)) {
							case "d":
								day = getNumber("d");
								break;
							case "D":
								getName("D", dayNamesShort, dayNames);
								break;
							case "o":
								doy = getNumber("o");
								break;
							case "m":
								month = getNumber("m");
								break;
							case "M":
								month = getName("M", monthNamesShort, monthNames);
								break;
							case "y":
								year = getNumber("y");
								break;
							case "@":
								date = new Date(getNumber("@"));
								year = date.getFullYear();
								month = date.getMonth() + 1;
								day = date.getDate();
								break;
							case "!":
								date = new Date((getNumber("!") - this._ticksTo1970) / 10000);
								year = date.getFullYear();
								month = date.getMonth() + 1;
								day = date.getDate();
								break;
							case "'":
								if (lookAhead("'")) {
									checkLiteral();
								} else {
									literal = true;
								}
								break;
							default:
								checkLiteral();
						}
					}
				}

				if (iValue < value.length) {
					extra = value.substr(iValue);
					if (!/^\s+/.test(extra)) {
						throw "Extra/unparsed characters found in date: " + extra;
					}
				}

				if (year === -1) {
					year = new Date().getFullYear();
				} else if (year < 100) {
					year += new Date().getFullYear() - new Date().getFullYear() % 100 +
						(year <= shortYearCutoff ? 0 : -100);
				}

				if (doy > -1) {
					month = 1;
					day = doy;
					do {
						dim = this._getDaysInMonth(year, month - 1);
						if (day <= dim) {
							break;
						}
						month++;
						day -= dim;
					} while (true);
				}

				date = this._daylightSavingAdjust(new Date(year, month - 1, day));
				if (date.getFullYear() !== year || date.getMonth() + 1 !== month || date.getDate() !== day) {
					throw "Invalid date"; // E.g. 31/02/00
				}
				return date;
			},

			/* Standard date formats. */
			ATOM: "yy-mm-dd", // RFC 3339 (ISO 8601)
			COOKIE: "D, dd M yy",
			ISO_8601: "yy-mm-dd",
			RFC_822: "D, d M y",
			RFC_850: "DD, dd-M-y",
			RFC_1036: "D, d M y",
			RFC_1123: "D, d M yy",
			RFC_2822: "D, d M yy",
			RSS: "D, d M y", // RFC 822
			TICKS: "!",
			TIMESTAMP: "@",
			W3C: "yy-mm-dd", // ISO 8601

			_ticksTo1970: (((1970 - 1) * 365 + Math.floor(1970 / 4) - Math.floor(1970 / 100) +
				Math.floor(1970 / 400)) * 24 * 60 * 60 * 10000000),

			/* Format a date object into a string value.
			 * The format can be combinations of the following:
			 * d  - day of month (no leading zero)
			 * dd - day of month (two digit)
			 * o  - day of year (no leading zeros)
			 * oo - day of year (three digit)
			 * D  - day name short
			 * DD - day name long
			 * m  - month of year (no leading zero)
			 * mm - month of year (two digit)
			 * M  - month name short
			 * MM - month name long
			 * y  - year (two digit)
			 * yy - year (four digit)
			 * @ - Unix timestamp (ms since 01/01/1970)
			 * ! - Windows ticks (100ns since 01/01/0001)
			 * "..." - literal text
			 * '' - single quote
			 *
			 * @param  format string - the desired format of the date
			 * @param  date Date - the date value to format
			 * @param  settings Object - attributes include:
			 *					dayNamesShort	string[7] - abbreviated names of the days from Sunday (optional)
			 *					dayNames		string[7] - names of the days from Sunday (optional)
			 *					monthNamesShort string[12] - abbreviated names of the months (optional)
			 *					monthNames		string[12] - names of the months (optional)
			 * @return  string - the date in the above format
			 */
			formatDate: function (format, date, settings) {
				if (!date) {
					return "";
				}

				var iFormat,
					dayNamesShort = (settings ? settings.dayNamesShort : null) || this._defaults.dayNamesShort,
					dayNames = (settings ? settings.dayNames : null) || this._defaults.dayNames,
					monthNamesShort = (settings ? settings.monthNamesShort : null) || this._defaults.monthNamesShort,
					monthNames = (settings ? settings.monthNames : null) || this._defaults.monthNames,

					// Check whether a format character is doubled
					lookAhead = function (match) {
						var matches = (iFormat + 1 < format.length && format.charAt(iFormat + 1) === match);
						if (matches) {
							iFormat++;
						}
						return matches;
					},

					// Format a number, with leading zero if necessary
					formatNumber = function (match, value, len) {
						var num = "" + value;
						if (lookAhead(match)) {
							while (num.length < len) {
								num = "0" + num;
							}
						}
						return num;
					},

					// Format a name, short or long as requested
					formatName = function (match, value, shortNames, longNames) {
						return (lookAhead(match) ? longNames[value] : shortNames[value]);
					},
					output = "",
					literal = false;

				if (date) {
					for (iFormat = 0; iFormat < format.length; iFormat++) {
						if (literal) {
							if (format.charAt(iFormat) === "'" && !lookAhead("'")) {
								literal = false;
							} else {
								output += format.charAt(iFormat);
							}
						} else {
							switch (format.charAt(iFormat)) {
								case "d":
									output += formatNumber("d", date.getDate(), 2);
									break;
								case "D":
									output += formatName("D", date.getDay(), dayNamesShort, dayNames);
									break;
								case "o":
									output += formatNumber("o",
										Math.round((new Date(date.getFullYear(), date.getMonth(), date.getDate()).getTime() - new Date(date.getFullYear(), 0, 0).getTime()) / 86400000), 3);
									break;
								case "m":
									output += formatNumber("m", date.getMonth() + 1, 2);
									break;
								case "M":
									output += formatName("M", date.getMonth(), monthNamesShort, monthNames);
									break;
								case "y":
									output += (lookAhead("y") ? date.getFullYear() :
										(date.getFullYear() % 100 < 10 ? "0" : "") + date.getFullYear() % 100);
									break;
								case "@":
									output += date.getTime();
									break;
								case "!":
									output += date.getTime() * 10000 + this._ticksTo1970;
									break;
								case "'":
									if (lookAhead("'")) {
										output += "'";
									} else {
										literal = true;
									}
									break;
								default:
									output += format.charAt(iFormat);
							}
						}
					}
				}
				return output;
			},

			/* Extract all possible characters from the date format. */
			_possibleChars: function (format) {
				var iFormat,
					chars = "",
					literal = false,

					// Check whether a format character is doubled
					lookAhead = function (match) {
						var matches = (iFormat + 1 < format.length && format.charAt(iFormat + 1) === match);
						if (matches) {
							iFormat++;
						}
						return matches;
					};

				for (iFormat = 0; iFormat < format.length; iFormat++) {
					if (literal) {
						if (format.charAt(iFormat) === "'" && !lookAhead("'")) {
							literal = false;
						} else {
							chars += format.charAt(iFormat);
						}
					} else {
						switch (format.charAt(iFormat)) {
							case "d": case "m": case "y": case "@":
								chars += "0123456789";
								break;
							case "D": case "M":
								return null; // Accept anything
							case "'":
								if (lookAhead("'")) {
									chars += "'";
								} else {
									literal = true;
								}
								break;
							default:
								chars += format.charAt(iFormat);
						}
					}
				}
				return chars;
			},

			/* Get a setting value, defaulting if necessary. */
			_get: function (inst, name) {
				return inst.settings[name] !== undefined ?
					inst.settings[name] : this._defaults[name];
			},

			/* Parse existing date and initialise date picker. */
			_setDateFromField: function (inst, noDefault) {
				if (inst.input.val() === inst.lastVal) {
					return;
				}

				var dateFormat = this._get(inst, "dateFormat"),
					dates = inst.lastVal = inst.input ? inst.input.val() : null,
					defaultDate = this._getDefaultDate(inst),
					date = defaultDate,
					settings = this._getFormatConfig(inst);

				try {
					date = this.parseDate(dateFormat, dates, settings) || defaultDate;
				} catch (event) {
					dates = (noDefault ? "" : dates);
				}
				inst.selectedDay = date.getDate();
				inst.drawMonth = inst.selectedMonth = date.getMonth();
				inst.drawYear = inst.selectedYear = date.getFullYear();
				inst.currentDay = (dates ? date.getDate() : 0);
				inst.currentMonth = (dates ? date.getMonth() : 0);
				inst.currentYear = (dates ? date.getFullYear() : 0);
				this._adjustInstDate(inst);
			},

			/* Retrieve the default date shown on opening. */
			_getDefaultDate: function (inst) {
				return this._restrictMinMax(inst,
					this._determineDate(inst, this._get(inst, "defaultDate"), new Date()));
			},

			/* A date may be specified as an exact value or a relative one. */
			_determineDate: function (inst, date, defaultDate) {
				var offsetNumeric = function (offset) {
					var date = new Date();
					date.setDate(date.getDate() + offset);
					return date;
				},
					offsetString = function (offset) {
						try {
							return $.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
								offset, $.datepicker._getFormatConfig(inst));
						}
						catch (e) {

							// Ignore
						}

						var date = (offset.toLowerCase().match(/^c/) ?
							$.datepicker._getDate(inst) : null) || new Date(),
							year = date.getFullYear(),
							month = date.getMonth(),
							day = date.getDate(),
							pattern = /([+\-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,
							matches = pattern.exec(offset);

						while (matches) {
							switch (matches[2] || "d") {
								case "d": case "D":
									day += parseInt(matches[1], 10); break;
								case "w": case "W":
									day += parseInt(matches[1], 10) * 7; break;
								case "m": case "M":
									month += parseInt(matches[1], 10);
									day = Math.min(day, $.datepicker._getDaysInMonth(year, month));
									break;
								case "y": case "Y":
									year += parseInt(matches[1], 10);
									day = Math.min(day, $.datepicker._getDaysInMonth(year, month));
									break;
							}
							matches = pattern.exec(offset);
						}
						return new Date(year, month, day);
					},
					newDate = (date == null || date === "" ? defaultDate : (typeof date === "string" ? offsetString(date) :
						(typeof date === "number" ? (isNaN(date) ? defaultDate : offsetNumeric(date)) : new Date(date.getTime()))));

				newDate = (newDate && newDate.toString() === "Invalid Date" ? defaultDate : newDate);
				if (newDate) {
					newDate.setHours(0);
					newDate.setMinutes(0);
					newDate.setSeconds(0);
					newDate.setMilliseconds(0);
				}
				return this._daylightSavingAdjust(newDate);
			},

			/* Handle switch to/from daylight saving.
			 * Hours may be non-zero on daylight saving cut-over:
			 * > 12 when midnight changeover, but then cannot generate
			 * midnight datetime, so jump to 1AM, otherwise reset.
			 * @param  date  (Date) the date to check
			 * @return  (Date) the corrected date
			 */
			_daylightSavingAdjust: function (date) {
				if (!date) {
					return null;
				}
				date.setHours(date.getHours() > 12 ? date.getHours() + 2 : 0);
				return date;
			},

			/* Set the date(s) directly. */
			_setDate: function (inst, date, noChange) {
				var clear = !date,
					origMonth = inst.selectedMonth,
					origYear = inst.selectedYear,
					newDate = this._restrictMinMax(inst, this._determineDate(inst, date, new Date()));

				inst.selectedDay = inst.currentDay = newDate.getDate();
				inst.drawMonth = inst.selectedMonth = inst.currentMonth = newDate.getMonth();
				inst.drawYear = inst.selectedYear = inst.currentYear = newDate.getFullYear();
				if ((origMonth !== inst.selectedMonth || origYear !== inst.selectedYear) && !noChange) {
					this._notifyChange(inst);
				}
				this._adjustInstDate(inst);
				if (inst.input) {
					inst.input.val(clear ? "" : this._formatDate(inst));
				}
			},

			/* Retrieve the date(s) directly. */
			_getDate: function (inst) {
				var startDate = (!inst.currentYear || (inst.input && inst.input.val() === "") ? null :
					this._daylightSavingAdjust(new Date(
						inst.currentYear, inst.currentMonth, inst.currentDay)));
				return startDate;
			},

			/* Attach the onxxx handlers.  These are declared statically so
			 * they work with static code transformers like Caja.
			 */
			_attachHandlers: function (inst) {
				var stepMonths = this._get(inst, "stepMonths"),
					id = "#" + inst.id.replace(/\\\\/g, "\\");
				inst.dpDiv.find("[data-handler]").map(function () {
					var handler = {
						prev: function () {
							$.datepicker._adjustDate(id, -stepMonths, "M");
						},
						next: function () {
							$.datepicker._adjustDate(id, +stepMonths, "M");
						},
						hide: function () {
							$.datepicker._hideDatepicker();
						},
						today: function () {
							$.datepicker._gotoToday(id);
						},
						selectDay: function () {
							$.datepicker._selectDay(id, +this.getAttribute("data-month"), +this.getAttribute("data-year"), this);
							return false;
						},
						selectMonth: function () {
							$.datepicker._selectMonthYear(id, this, "M");
							return false;
						},
						selectYear: function () {
							$.datepicker._selectMonthYear(id, this, "Y");
							return false;
						}
					};
					$(this).on(this.getAttribute("data-event"), handler[this.getAttribute("data-handler")]);
				});
			},

			/* Generate the HTML for the current state of the date picker. */
			_generateHTML: function (inst) {
				var maxDraw, prevText, prev, nextText, next, currentText, gotoDate,
					controls, buttonPanel, firstDay, showWeek, dayNames, dayNamesMin,
					monthNames, monthNamesShort, beforeShowDay, showOtherMonths,
					selectOtherMonths, defaultDate, html, dow, row, group, col, selectedDate,
					cornerClass, calender, thead, day, daysInMonth, leadDays, curRows, numRows,
					printDate, dRow, tbody, daySettings, otherMonth, unselectable,
					tempDate = new Date(),
					today = this._daylightSavingAdjust(
						new Date(tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate())), // clear time
					isRTL = this._get(inst, "isRTL"),
					showButtonPanel = this._get(inst, "showButtonPanel"),
					hideIfNoPrevNext = this._get(inst, "hideIfNoPrevNext"),
					navigationAsDateFormat = this._get(inst, "navigationAsDateFormat"),
					numMonths = this._getNumberOfMonths(inst),
					showCurrentAtPos = this._get(inst, "showCurrentAtPos"),
					stepMonths = this._get(inst, "stepMonths"),
					isMultiMonth = (numMonths[0] !== 1 || numMonths[1] !== 1),
					currentDate = this._daylightSavingAdjust((!inst.currentDay ? new Date(9999, 9, 9) :
						new Date(inst.currentYear, inst.currentMonth, inst.currentDay))),
					minDate = this._getMinMaxDate(inst, "min"),
					maxDate = this._getMinMaxDate(inst, "max"),
					drawMonth = inst.drawMonth - showCurrentAtPos,
					drawYear = inst.drawYear;

				if (drawMonth < 0) {
					drawMonth += 12;
					drawYear--;
				}
				if (maxDate) {
					maxDraw = this._daylightSavingAdjust(new Date(maxDate.getFullYear(),
						maxDate.getMonth() - (numMonths[0] * numMonths[1]) + 1, maxDate.getDate()));
					maxDraw = (minDate && maxDraw < minDate ? minDate : maxDraw);
					while (this._daylightSavingAdjust(new Date(drawYear, drawMonth, 1)) > maxDraw) {
						drawMonth--;
						if (drawMonth < 0) {
							drawMonth = 11;
							drawYear--;
						}
					}
				}
				inst.drawMonth = drawMonth;
				inst.drawYear = drawYear;

				prevText = this._get(inst, "prevText");
				prevText = (!navigationAsDateFormat ? prevText : this.formatDate(prevText,
					this._daylightSavingAdjust(new Date(drawYear, drawMonth - stepMonths, 1)),
					this._getFormatConfig(inst)));

				prev = (this._canAdjustMonth(inst, -1, drawYear, drawMonth) ?
					"<a class='ui-datepicker-prev ui-corner-all' data-handler='prev' data-event='click'" +
					" title='" + prevText + "'><span class='ui-icon ui-icon-circle-triangle-" + (isRTL ? "e" : "w") + "'>" + prevText + "</span></a>" :
					(hideIfNoPrevNext ? "" : "<a class='ui-datepicker-prev ui-corner-all ui-state-disabled' title='" + prevText + "'><span class='ui-icon ui-icon-circle-triangle-" + (isRTL ? "e" : "w") + "'>" + prevText + "</span></a>"));

				nextText = this._get(inst, "nextText");
				nextText = (!navigationAsDateFormat ? nextText : this.formatDate(nextText,
					this._daylightSavingAdjust(new Date(drawYear, drawMonth + stepMonths, 1)),
					this._getFormatConfig(inst)));

				next = (this._canAdjustMonth(inst, +1, drawYear, drawMonth) ?
					"<a class='ui-datepicker-next ui-corner-all' data-handler='next' data-event='click'" +
					" title='" + nextText + "'><span class='ui-icon ui-icon-circle-triangle-" + (isRTL ? "w" : "e") + "'>" + nextText + "</span></a>" :
					(hideIfNoPrevNext ? "" : "<a class='ui-datepicker-next ui-corner-all ui-state-disabled' title='" + nextText + "'><span class='ui-icon ui-icon-circle-triangle-" + (isRTL ? "w" : "e") + "'>" + nextText + "</span></a>"));

				currentText = this._get(inst, "currentText");
				gotoDate = (this._get(inst, "gotoCurrent") && inst.currentDay ? currentDate : today);
				currentText = (!navigationAsDateFormat ? currentText :
					this.formatDate(currentText, gotoDate, this._getFormatConfig(inst)));

				controls = (!inst.inline ? "<button type='button' class='ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all' data-handler='hide' data-event='click'>" +
					this._get(inst, "closeText") + "</button>" : "");

				buttonPanel = (showButtonPanel) ? "<div class='ui-datepicker-buttonpane ui-widget-content'>" + (isRTL ? controls : "") +
					(this._isInRange(inst, gotoDate) ? "<button type='button' class='ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all' data-handler='today' data-event='click'" +
						">" + currentText + "</button>" : "") + (isRTL ? "" : controls) + "</div>" : "";

				firstDay = parseInt(this._get(inst, "firstDay"), 10);
				firstDay = (isNaN(firstDay) ? 0 : firstDay);

				showWeek = this._get(inst, "showWeek");
				dayNames = this._get(inst, "dayNames");
				dayNamesMin = this._get(inst, "dayNamesMin");
				monthNames = this._get(inst, "monthNames");
				monthNamesShort = this._get(inst, "monthNamesShort");
				beforeShowDay = this._get(inst, "beforeShowDay");
				showOtherMonths = this._get(inst, "showOtherMonths");
				selectOtherMonths = this._get(inst, "selectOtherMonths");
				defaultDate = this._getDefaultDate(inst);
				html = "";

				for (row = 0; row < numMonths[0]; row++) {
					group = "";
					this.maxRows = 4;
					for (col = 0; col < numMonths[1]; col++) {
						selectedDate = this._daylightSavingAdjust(new Date(drawYear, drawMonth, inst.selectedDay));
						cornerClass = " ui-corner-all";
						calender = "";
						if (isMultiMonth) {
							calender += "<div class='ui-datepicker-group";
							if (numMonths[1] > 1) {
								switch (col) {
									case 0: calender += " ui-datepicker-group-first";
										cornerClass = " ui-corner-" + (isRTL ? "right" : "left"); break;
									case numMonths[1] - 1: calender += " ui-datepicker-group-last";
										cornerClass = " ui-corner-" + (isRTL ? "left" : "right"); break;
									default: calender += " ui-datepicker-group-middle"; cornerClass = ""; break;
								}
							}
							calender += "'>";
						}
						calender += "<div class='ui-datepicker-header ui-widget-header ui-helper-clearfix" + cornerClass + "'>" +
							(/all|left/.test(cornerClass) && row === 0 ? (isRTL ? next : prev) : "") +
							(/all|right/.test(cornerClass) && row === 0 ? (isRTL ? prev : next) : "") +
							this._generateMonthYearHeader(inst, drawMonth, drawYear, minDate, maxDate,
								row > 0 || col > 0, monthNames, monthNamesShort) + // draw month headers
							"</div><table class='ui-datepicker-calendar'><thead>" +
							"<tr>";
						thead = (showWeek ? "<th class='ui-datepicker-week-col'>" + this._get(inst, "weekHeader") + "</th>" : "");
						for (dow = 0; dow < 7; dow++) { // days of the week
							day = (dow + firstDay) % 7;
							thead += "<th scope='col'" + ((dow + firstDay + 6) % 7 >= 5 ? " class='ui-datepicker-week-end'" : "") + ">" +
								"<span title='" + dayNames[day] + "'>" + dayNamesMin[day] + "</span></th>";
						}
						calender += thead + "</tr></thead><tbody>";
						daysInMonth = this._getDaysInMonth(drawYear, drawMonth);
						if (drawYear === inst.selectedYear && drawMonth === inst.selectedMonth) {
							inst.selectedDay = Math.min(inst.selectedDay, daysInMonth);
						}
						leadDays = (this._getFirstDayOfMonth(drawYear, drawMonth) - firstDay + 7) % 7;
						curRows = Math.ceil((leadDays + daysInMonth) / 7); // calculate the number of rows to generate
						numRows = (isMultiMonth ? this.maxRows > curRows ? this.maxRows : curRows : curRows); //If multiple months, use the higher number of rows (see #7043)
						this.maxRows = numRows;
						printDate = this._daylightSavingAdjust(new Date(drawYear, drawMonth, 1 - leadDays));
						for (dRow = 0; dRow < numRows; dRow++) { // create date picker rows
							calender += "<tr>";
							tbody = (!showWeek ? "" : "<td class='ui-datepicker-week-col'>" +
								this._get(inst, "calculateWeek")(printDate) + "</td>");
							for (dow = 0; dow < 7; dow++) { // create date picker days
								daySettings = (beforeShowDay ?
									beforeShowDay.apply((inst.input ? inst.input[0] : null), [printDate]) : [true, ""]);
								otherMonth = (printDate.getMonth() !== drawMonth);
								unselectable = (otherMonth && !selectOtherMonths) || !daySettings[0] ||
									(minDate && printDate < minDate) || (maxDate && printDate > maxDate);
								tbody += "<td class='" +
									((dow + firstDay + 6) % 7 >= 5 ? " ui-datepicker-week-end" : "") + // highlight weekends
									(otherMonth ? " ui-datepicker-other-month" : "") + // highlight days from other months
									((printDate.getTime() === selectedDate.getTime() && drawMonth === inst.selectedMonth && inst._keyEvent) || // user pressed key
										(defaultDate.getTime() === printDate.getTime() && defaultDate.getTime() === selectedDate.getTime()) ?

										// or defaultDate is current printedDate and defaultDate is selectedDate
										" " + this._dayOverClass : "") + // highlight selected day
									(unselectable ? " " + this._unselectableClass + " ui-state-disabled" : "") +  // highlight unselectable days
									(otherMonth && !showOtherMonths ? "" : " " + daySettings[1] + // highlight custom dates
										(printDate.getTime() === currentDate.getTime() ? " " + this._currentClass : "") + // highlight selected day
										(printDate.getTime() === today.getTime() ? " ui-datepicker-today" : "")) + "'" + // highlight today (if different)
									((!otherMonth || showOtherMonths) && daySettings[2] ? " title='" + daySettings[2].replace(/'/g, "&#39;") + "'" : "") + // cell title
									(unselectable ? "" : " data-handler='selectDay' data-event='click' data-month='" + printDate.getMonth() + "' data-year='" + printDate.getFullYear() + "'") + ">" + // actions
									(otherMonth && !showOtherMonths ? "&#xa0;" : // display for other months
										(unselectable ? "<span class='ui-state-default'>" + printDate.getDate() + "</span>" : "<a class='ui-state-default" +
											(printDate.getTime() === today.getTime() ? " ui-state-highlight" : "") +
											(printDate.getTime() === currentDate.getTime() ? " ui-state-active" : "") + // highlight selected day
											(otherMonth ? " ui-priority-secondary" : "") + // distinguish dates from other months
											"' href='#'>" + printDate.getDate() + "</a>")) + "</td>"; // display selectable date
								printDate.setDate(printDate.getDate() + 1);
								printDate = this._daylightSavingAdjust(printDate);
							}
							calender += tbody + "</tr>";
						}
						drawMonth++;
						if (drawMonth > 11) {
							drawMonth = 0;
							drawYear++;
						}
						calender += "</tbody></table>" + (isMultiMonth ? "</div>" +
							((numMonths[0] > 0 && col === numMonths[1] - 1) ? "<div class='ui-datepicker-row-break'></div>" : "") : "");
						group += calender;
					}
					html += group;
				}
				html += buttonPanel;
				inst._keyEvent = false;
				return html;
			},

			/* Generate the month and year header. */
			_generateMonthYearHeader: function (inst, drawMonth, drawYear, minDate, maxDate,
				secondary, monthNames, monthNamesShort) {

				var inMinYear, inMaxYear, month, years, thisYear, determineYear, year, endYear,
					changeMonth = this._get(inst, "changeMonth"),
					changeYear = this._get(inst, "changeYear"),
					showMonthAfterYear = this._get(inst, "showMonthAfterYear"),
					html = "<div class='ui-datepicker-title'>",
					monthHtml = "";

				// Month selection
				if (secondary || !changeMonth) {
					monthHtml += "<span class='ui-datepicker-month'>" + monthNames[drawMonth] + "</span>";
				} else {
					inMinYear = (minDate && minDate.getFullYear() === drawYear);
					inMaxYear = (maxDate && maxDate.getFullYear() === drawYear);
					monthHtml += "<select class='ui-datepicker-month' data-handler='selectMonth' data-event='change'>";
					for (month = 0; month < 12; month++) {
						if ((!inMinYear || month >= minDate.getMonth()) && (!inMaxYear || month <= maxDate.getMonth())) {
							monthHtml += "<option value='" + month + "'" +
								(month === drawMonth ? " selected='selected'" : "") +
								">" + monthNamesShort[month] + "</option>";
						}
					}
					monthHtml += "</select>";
				}

				if (!showMonthAfterYear) {
					html += monthHtml + (secondary || !(changeMonth && changeYear) ? "&#xa0;" : "");
				}

				// Year selection
				if (!inst.yearshtml) {
					inst.yearshtml = "";
					if (secondary || !changeYear) {
						html += "<span class='ui-datepicker-year'>" + drawYear + "</span>";
					} else {

						// determine range of years to display
						years = this._get(inst, "yearRange").split(":");
						thisYear = new Date().getFullYear();
						determineYear = function (value) {
							var year = (value.match(/c[+\-].*/) ? drawYear + parseInt(value.substring(1), 10) :
								(value.match(/[+\-].*/) ? thisYear + parseInt(value, 10) :
									parseInt(value, 10)));
							return (isNaN(year) ? thisYear : year);
						};
						year = determineYear(years[0]);
						endYear = Math.max(year, determineYear(years[1] || ""));
						year = (minDate ? Math.max(year, minDate.getFullYear()) : year);
						endYear = (maxDate ? Math.min(endYear, maxDate.getFullYear()) : endYear);
						inst.yearshtml += "<select class='ui-datepicker-year' data-handler='selectYear' data-event='change'>";
						for (; year <= endYear; year++) {
							inst.yearshtml += "<option value='" + year + "'" +
								(year === drawYear ? " selected='selected'" : "") +
								">" + year + "</option>";
						}
						inst.yearshtml += "</select>";

						html += inst.yearshtml;
						inst.yearshtml = null;
					}
				}

				html += this._get(inst, "yearSuffix");
				if (showMonthAfterYear) {
					html += (secondary || !(changeMonth && changeYear) ? "&#xa0;" : "") + monthHtml;
				}
				html += "</div>"; // Close datepicker_header
				return html;
			},

			/* Adjust one of the date sub-fields. */
			_adjustInstDate: function (inst, offset, period) {
				var year = inst.selectedYear + (period === "Y" ? offset : 0),
					month = inst.selectedMonth + (period === "M" ? offset : 0),
					day = Math.min(inst.selectedDay, this._getDaysInMonth(year, month)) + (period === "D" ? offset : 0),
					date = this._restrictMinMax(inst, this._daylightSavingAdjust(new Date(year, month, day)));

				inst.selectedDay = date.getDate();
				inst.drawMonth = inst.selectedMonth = date.getMonth();
				inst.drawYear = inst.selectedYear = date.getFullYear();
				if (period === "M" || period === "Y") {
					this._notifyChange(inst);
				}
			},

			/* Ensure a date is within any min/max bounds. */
			_restrictMinMax: function (inst, date) {
				var minDate = this._getMinMaxDate(inst, "min"),
					maxDate = this._getMinMaxDate(inst, "max"),
					newDate = (minDate && date < minDate ? minDate : date);
				return (maxDate && newDate > maxDate ? maxDate : newDate);
			},

			/* Notify change of month/year. */
			_notifyChange: function (inst) {
				var onChange = this._get(inst, "onChangeMonthYear");
				if (onChange) {
					onChange.apply((inst.input ? inst.input[0] : null),
						[inst.selectedYear, inst.selectedMonth + 1, inst]);
				}
			},

			/* Determine the number of months to show. */
			_getNumberOfMonths: function (inst) {
				var numMonths = this._get(inst, "numberOfMonths");
				return (numMonths == null ? [1, 1] : (typeof numMonths === "number" ? [1, numMonths] : numMonths));
			},

			/* Determine the current maximum date - ensure no time components are set. */
			_getMinMaxDate: function (inst, minMax) {
				return this._determineDate(inst, this._get(inst, minMax + "Date"), null);
			},

			/* Find the number of days in a given month. */
			_getDaysInMonth: function (year, month) {
				return 32 - this._daylightSavingAdjust(new Date(year, month, 32)).getDate();
			},

			/* Find the day of the week of the first of a month. */
			_getFirstDayOfMonth: function (year, month) {
				return new Date(year, month, 1).getDay();
			},

			/* Determines if we should allow a "next/prev" month display change. */
			_canAdjustMonth: function (inst, offset, curYear, curMonth) {
				var numMonths = this._getNumberOfMonths(inst),
					date = this._daylightSavingAdjust(new Date(curYear,
						curMonth + (offset < 0 ? offset : numMonths[0] * numMonths[1]), 1));

				if (offset < 0) {
					date.setDate(this._getDaysInMonth(date.getFullYear(), date.getMonth()));
				}
				return this._isInRange(inst, date);
			},

			/* Is the given date in the accepted range? */
			_isInRange: function (inst, date) {
				var yearSplit, currentYear,
					minDate = this._getMinMaxDate(inst, "min"),
					maxDate = this._getMinMaxDate(inst, "max"),
					minYear = null,
					maxYear = null,
					years = this._get(inst, "yearRange");
				if (years) {
					yearSplit = years.split(":");
					currentYear = new Date().getFullYear();
					minYear = parseInt(yearSplit[0], 10);
					maxYear = parseInt(yearSplit[1], 10);
					if (yearSplit[0].match(/[+\-].*/)) {
						minYear += currentYear;
					}
					if (yearSplit[1].match(/[+\-].*/)) {
						maxYear += currentYear;
					}
				}

				return ((!minDate || date.getTime() >= minDate.getTime()) &&
					(!maxDate || date.getTime() <= maxDate.getTime()) &&
					(!minYear || date.getFullYear() >= minYear) &&
					(!maxYear || date.getFullYear() <= maxYear));
			},

			/* Provide the configuration settings for formatting/parsing. */
			_getFormatConfig: function (inst) {
				var shortYearCutoff = this._get(inst, "shortYearCutoff");
				shortYearCutoff = (typeof shortYearCutoff !== "string" ? shortYearCutoff :
					new Date().getFullYear() % 100 + parseInt(shortYearCutoff, 10));
				return {
					shortYearCutoff: shortYearCutoff,
					dayNamesShort: this._get(inst, "dayNamesShort"), dayNames: this._get(inst, "dayNames"),
					monthNamesShort: this._get(inst, "monthNamesShort"), monthNames: this._get(inst, "monthNames")
				};
			},

			/* Format the given date for display. */
			_formatDate: function (inst, day, month, year) {
				if (!day) {
					inst.currentDay = inst.selectedDay;
					inst.currentMonth = inst.selectedMonth;
					inst.currentYear = inst.selectedYear;
				}
				var date = (day ? (typeof day === "object" ? day :
					this._daylightSavingAdjust(new Date(year, month, day))) :
					this._daylightSavingAdjust(new Date(inst.currentYear, inst.currentMonth, inst.currentDay)));
				return this.formatDate(this._get(inst, "dateFormat"), date, this._getFormatConfig(inst));
			}
		});

		/*
		 * Bind hover events for datepicker elements.
		 * Done via delegate so the binding only occurs once in the lifetime of the parent div.
		 * Global datepicker_instActive, set by _updateDatepicker allows the handlers to find their way back to the active picker.
		 */
		function datepicker_bindHover(dpDiv) {
			var selector = "button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";
			return dpDiv.on("mouseout", selector, function () {
				$(this).removeClass("ui-state-hover");
				if (this.className.indexOf("ui-datepicker-prev") !== -1) {
					$(this).removeClass("ui-datepicker-prev-hover");
				}
				if (this.className.indexOf("ui-datepicker-next") !== -1) {
					$(this).removeClass("ui-datepicker-next-hover");
				}
			})
				.on("mouseover", selector, datepicker_handleMouseover);
		}

		function datepicker_handleMouseover() {
			if (!$.datepicker._isDisabledDatepicker(datepicker_instActive.inline ? datepicker_instActive.dpDiv.parent()[0] : datepicker_instActive.input[0])) {
				$(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");
				$(this).addClass("ui-state-hover");
				if (this.className.indexOf("ui-datepicker-prev") !== -1) {
					$(this).addClass("ui-datepicker-prev-hover");
				}
				if (this.className.indexOf("ui-datepicker-next") !== -1) {
					$(this).addClass("ui-datepicker-next-hover");
				}
			}
		}

		/* jQuery extend now ignores nulls! */
		function datepicker_extendRemove(target, props) {
			$.extend(target, props);
			for (var name in props) {
				if (props[name] == null) {
					target[name] = props[name];
				}
			}
			return target;
		}

		/* Invoke the datepicker functionality.
		   @param  options  string - a command, optionally followed by additional parameters or
							Object - settings for attaching new datepicker functionality
		   @return  jQuery object */
		$.fn.datepicker = function (options) {

			/* Verify an empty collection wasn't passed - Fixes #6976 */
			if (!this.length) {
				return this;
			}

			/* Initialise the date picker. */
			if (!$.datepicker.initialized) {
				$(document).on("mousedown", $.datepicker._checkExternalClick);
				$.datepicker.initialized = true;
			}

			/* Append datepicker main container to body if not exist. */
			if ($("#" + $.datepicker._mainDivId).length === 0) {
				$("body").append($.datepicker.dpDiv);
			}

			var otherArgs = Array.prototype.slice.call(arguments, 1);
			if (typeof options === "string" && (options === "isDisabled" || options === "getDate" || options === "widget")) {
				return $.datepicker["_" + options + "Datepicker"].
					apply($.datepicker, [this[0]].concat(otherArgs));
			}
			if (options === "option" && arguments.length === 2 && typeof arguments[1] === "string") {
				return $.datepicker["_" + options + "Datepicker"].
					apply($.datepicker, [this[0]].concat(otherArgs));
			}
			return this.each(function () {
				typeof options === "string" ?
					$.datepicker["_" + options + "Datepicker"].
						apply($.datepicker, [this].concat(otherArgs)) :
					$.datepicker._attachDatepicker(this, options);
			});
		};

		$.datepicker = new Datepicker(); // singleton instance
		$.datepicker.initialized = false;
		$.datepicker.uuid = new Date().getTime();
		$.datepicker.version = "1.12.1";

		var widgetsDatepicker = $.datepicker;




		// This file is deprecated
		var ie = $.ui.ie = !!/msie [\w.]+/.exec(navigator.userAgent.toLowerCase());

		/*!
		 * jQuery UI Mouse 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Mouse
		//>>group: Widgets
		//>>description: Abstracts mouse-based interactions to assist in creating certain widgets.
		//>>docs: http://api.jqueryui.com/mouse/



		var mouseHandled = false;
		$(document).on("mouseup", function () {
			mouseHandled = false;
		});

		var widgetsMouse = $.widget("ui.mouse", {
			version: "1.12.1",
			options: {
				cancel: "input, textarea, button, select, option",
				distance: 1,
				delay: 0
			},
			_mouseInit: function () {
				var that = this;

				this.element
					.on("mousedown." + this.widgetName, function (event) {
						return that._mouseDown(event);
					})
					.on("click." + this.widgetName, function (event) {
						if (true === $.data(event.target, that.widgetName + ".preventClickEvent")) {
							$.removeData(event.target, that.widgetName + ".preventClickEvent");
							event.stopImmediatePropagation();
							return false;
						}
					});

				this.started = false;
			},

			// TODO: make sure destroying one instance of mouse doesn't mess with
			// other instances of mouse
			_mouseDestroy: function () {
				this.element.off("." + this.widgetName);
				if (this._mouseMoveDelegate) {
					this.document
						.off("mousemove." + this.widgetName, this._mouseMoveDelegate)
						.off("mouseup." + this.widgetName, this._mouseUpDelegate);
				}
			},

			_mouseDown: function (event) {

				// don't let more than one widget handle mouseStart
				if (mouseHandled) {
					return;
				}

				this._mouseMoved = false;

				// We may have missed mouseup (out of window)
				(this._mouseStarted && this._mouseUp(event));

				this._mouseDownEvent = event;

				var that = this,
					btnIsLeft = (event.which === 1),

					// event.target.nodeName works around a bug in IE 8 with
					// disabled inputs (#7620)
					elIsCancel = (typeof this.options.cancel === "string" && event.target.nodeName ?
						$(event.target).closest(this.options.cancel).length : false);
				if (!btnIsLeft || elIsCancel || !this._mouseCapture(event)) {
					return true;
				}

				this.mouseDelayMet = !this.options.delay;
				if (!this.mouseDelayMet) {
					this._mouseDelayTimer = setTimeout(function () {
						that.mouseDelayMet = true;
					}, this.options.delay);
				}

				if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
					this._mouseStarted = (this._mouseStart(event) !== false);
					if (!this._mouseStarted) {
						event.preventDefault();
						return true;
					}
				}

				// Click event may never have fired (Gecko & Opera)
				if (true === $.data(event.target, this.widgetName + ".preventClickEvent")) {
					$.removeData(event.target, this.widgetName + ".preventClickEvent");
				}

				// These delegates are required to keep context
				this._mouseMoveDelegate = function (event) {
					return that._mouseMove(event);
				};
				this._mouseUpDelegate = function (event) {
					return that._mouseUp(event);
				};

				this.document
					.on("mousemove." + this.widgetName, this._mouseMoveDelegate)
					.on("mouseup." + this.widgetName, this._mouseUpDelegate);

				event.preventDefault();

				mouseHandled = true;
				return true;
			},

			_mouseMove: function (event) {

				// Only check for mouseups outside the document if you've moved inside the document
				// at least once. This prevents the firing of mouseup in the case of IE<9, which will
				// fire a mousemove event if content is placed under the cursor. See #7778
				// Support: IE <9
				if (this._mouseMoved) {

					// IE mouseup check - mouseup happened when mouse was out of window
					if ($.ui.ie && (!document.documentMode || document.documentMode < 9) &&
						!event.button) {
						return this._mouseUp(event);

						// Iframe mouseup check - mouseup occurred in another document
					} else if (!event.which) {

						// Support: Safari <=8 - 9
						// Safari sets which to 0 if you press any of the following keys
						// during a drag (#14461)
						if (event.originalEvent.altKey || event.originalEvent.ctrlKey ||
							event.originalEvent.metaKey || event.originalEvent.shiftKey) {
							this.ignoreMissingWhich = true;
						} else if (!this.ignoreMissingWhich) {
							return this._mouseUp(event);
						}
					}
				}

				if (event.which || event.button) {
					this._mouseMoved = true;
				}

				if (this._mouseStarted) {
					this._mouseDrag(event);
					return event.preventDefault();
				}

				if (this._mouseDistanceMet(event) && this._mouseDelayMet(event)) {
					this._mouseStarted =
						(this._mouseStart(this._mouseDownEvent, event) !== false);
					(this._mouseStarted ? this._mouseDrag(event) : this._mouseUp(event));
				}

				return !this._mouseStarted;
			},

			_mouseUp: function (event) {
				this.document
					.off("mousemove." + this.widgetName, this._mouseMoveDelegate)
					.off("mouseup." + this.widgetName, this._mouseUpDelegate);

				if (this._mouseStarted) {
					this._mouseStarted = false;

					if (event.target === this._mouseDownEvent.target) {
						$.data(event.target, this.widgetName + ".preventClickEvent", true);
					}

					this._mouseStop(event);
				}

				if (this._mouseDelayTimer) {
					clearTimeout(this._mouseDelayTimer);
					delete this._mouseDelayTimer;
				}

				this.ignoreMissingWhich = false;
				mouseHandled = false;
				event.preventDefault();
			},

			_mouseDistanceMet: function (event) {
				return (Math.max(
					Math.abs(this._mouseDownEvent.pageX - event.pageX),
					Math.abs(this._mouseDownEvent.pageY - event.pageY)
				) >= this.options.distance
				);
			},

			_mouseDelayMet: function ( /* event */) {
				return this.mouseDelayMet;
			},

			// These are placeholder methods, to be overriden by extending plugin
			_mouseStart: function ( /* event */) { },
			_mouseDrag: function ( /* event */) { },
			_mouseStop: function ( /* event */) { },
			_mouseCapture: function ( /* event */) { return true; }
		});




		// $.ui.plugin is deprecated. Use $.widget() extensions instead.
		var plugin = $.ui.plugin = {
			add: function (module, option, set) {
				var i,
					proto = $.ui[module].prototype;
				for (i in set) {
					proto.plugins[i] = proto.plugins[i] || [];
					proto.plugins[i].push([option, set[i]]);
				}
			},
			call: function (instance, name, args, allowDisconnected) {
				var i,
					set = instance.plugins[name];

				if (!set) {
					return;
				}

				if (!allowDisconnected && (!instance.element[0].parentNode ||
					instance.element[0].parentNode.nodeType === 11)) {
					return;
				}

				for (i = 0; i < set.length; i++) {
					if (instance.options[set[i][0]]) {
						set[i][1].apply(instance.element, args);
					}
				}
			}
		};



		var safeBlur = $.ui.safeBlur = function (element) {

			// Support: IE9 - 10 only
			// If the <body> is blurred, IE will switch windows, see #9420
			if (element && element.nodeName.toLowerCase() !== "body") {
				$(element).trigger("blur");
			}
		};


		/*!
		 * jQuery UI Draggable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Draggable
		//>>group: Interactions
		//>>description: Enables dragging functionality for any element.
		//>>docs: http://api.jqueryui.com/draggable/
		//>>demos: http://jqueryui.com/draggable/
		//>>css.structure: ../../themes/base/draggable.css



		$.widget("ui.draggable", $.ui.mouse, {
			version: "1.12.1",
			widgetEventPrefix: "drag",
			options: {
				addClasses: true,
				appendTo: "parent",
				axis: false,
				connectToSortable: false,
				containment: false,
				cursor: "auto",
				cursorAt: false,
				grid: false,
				handle: false,
				helper: "original",
				iframeFix: false,
				opacity: false,
				refreshPositions: false,
				revert: false,
				revertDuration: 500,
				scope: "default",
				scroll: true,
				scrollSensitivity: 20,
				scrollSpeed: 20,
				snap: false,
				snapMode: "both",
				snapTolerance: 20,
				stack: false,
				zIndex: false,

				// Callbacks
				drag: null,
				start: null,
				stop: null
			},
			_create: function () {

				if (this.options.helper === "original") {
					this._setPositionRelative();
				}
				if (this.options.addClasses) {
					this._addClass("ui-draggable");
				}
				this._setHandleClassName();

				this._mouseInit();
			},

			_setOption: function (key, value) {
				this._super(key, value);
				if (key === "handle") {
					this._removeHandleClassName();
					this._setHandleClassName();
				}
			},

			_destroy: function () {
				if ((this.helper || this.element).is(".ui-draggable-dragging")) {
					this.destroyOnClear = true;
					return;
				}
				this._removeHandleClassName();
				this._mouseDestroy();
			},

			_mouseCapture: function (event) {
				var o = this.options;

				// Among others, prevent a drag on a resizable-handle
				if (this.helper || o.disabled ||
					$(event.target).closest(".ui-resizable-handle").length > 0) {
					return false;
				}

				//Quit if we're not on a valid handle
				this.handle = this._getHandle(event);
				if (!this.handle) {
					return false;
				}

				this._blurActiveElement(event);

				this._blockFrames(o.iframeFix === true ? "iframe" : o.iframeFix);

				return true;

			},

			_blockFrames: function (selector) {
				this.iframeBlocks = this.document.find(selector).map(function () {
					var iframe = $(this);

					return $("<div>")
						.css("position", "absolute")
						.appendTo(iframe.parent())
						.outerWidth(iframe.outerWidth())
						.outerHeight(iframe.outerHeight())
						.offset(iframe.offset())[0];
				});
			},

			_unblockFrames: function () {
				if (this.iframeBlocks) {
					this.iframeBlocks.remove();
					delete this.iframeBlocks;
				}
			},

			_blurActiveElement: function (event) {
				var activeElement = $.ui.safeActiveElement(this.document[0]),
					target = $(event.target);

				// Don't blur if the event occurred on an element that is within
				// the currently focused element
				// See #10527, #12472
				if (target.closest(activeElement).length) {
					return;
				}

				// Blur any element that currently has focus, see #4261
				$.ui.safeBlur(activeElement);
			},

			_mouseStart: function (event) {

				var o = this.options;

				//Create and append the visible helper
				this.helper = this._createHelper(event);

				this._addClass(this.helper, "ui-draggable-dragging");

				//Cache the helper size
				this._cacheHelperProportions();

				//If ddmanager is used for droppables, set the global draggable
				if ($.ui.ddmanager) {
					$.ui.ddmanager.current = this;
				}

				/*
				 * - Position generation -
				 * This block generates everything position related - it's the core of draggables.
				 */

				//Cache the margins of the original element
				this._cacheMargins();

				//Store the helper's css position
				this.cssPosition = this.helper.css("position");
				this.scrollParent = this.helper.scrollParent(true);
				this.offsetParent = this.helper.offsetParent();
				this.hasFixedAncestor = this.helper.parents().filter(function () {
					return $(this).css("position") === "fixed";
				}).length > 0;

				//The element's absolute position on the page minus margins
				this.positionAbs = this.element.offset();
				this._refreshOffsets(event);

				//Generate the original position
				this.originalPosition = this.position = this._generatePosition(event, false);
				this.originalPageX = event.pageX;
				this.originalPageY = event.pageY;

				//Adjust the mouse offset relative to the helper if "cursorAt" is supplied
				(o.cursorAt && this._adjustOffsetFromHelper(o.cursorAt));

				//Set a containment if given in the options
				this._setContainment();

				//Trigger event + callbacks
				if (this._trigger("start", event) === false) {
					this._clear();
					return false;
				}

				//Recache the helper size
				this._cacheHelperProportions();

				//Prepare the droppable offsets
				if ($.ui.ddmanager && !o.dropBehaviour) {
					$.ui.ddmanager.prepareOffsets(this, event);
				}

				// Execute the drag once - this causes the helper not to be visible before getting its
				// correct position
				this._mouseDrag(event, true);

				// If the ddmanager is used for droppables, inform the manager that dragging has started
				// (see #5003)
				if ($.ui.ddmanager) {
					$.ui.ddmanager.dragStart(this, event);
				}

				return true;
			},

			_refreshOffsets: function (event) {
				this.offset = {
					top: this.positionAbs.top - this.margins.top,
					left: this.positionAbs.left - this.margins.left,
					scroll: false,
					parent: this._getParentOffset(),
					relative: this._getRelativeOffset()
				};

				this.offset.click = {
					left: event.pageX - this.offset.left,
					top: event.pageY - this.offset.top
				};
			},

			_mouseDrag: function (event, noPropagation) {

				// reset any necessary cached properties (see #5009)
				if (this.hasFixedAncestor) {
					this.offset.parent = this._getParentOffset();
				}

				//Compute the helpers position
				this.position = this._generatePosition(event, true);
				this.positionAbs = this._convertPositionTo("absolute");

				//Call plugins and callbacks and use the resulting position if something is returned
				if (!noPropagation) {
					var ui = this._uiHash();
					if (this._trigger("drag", event, ui) === false) {
						this._mouseUp(new $.Event("mouseup", event));
						return false;
					}
					this.position = ui.position;
				}

				this.helper[0].style.left = this.position.left + "px";
				this.helper[0].style.top = this.position.top + "px";

				if ($.ui.ddmanager) {
					$.ui.ddmanager.drag(this, event);
				}

				return false;
			},

			_mouseStop: function (event) {

				//If we are using droppables, inform the manager about the drop
				var that = this,
					dropped = false;
				if ($.ui.ddmanager && !this.options.dropBehaviour) {
					dropped = $.ui.ddmanager.drop(this, event);
				}

				//if a drop comes from outside (a sortable)
				if (this.dropped) {
					dropped = this.dropped;
					this.dropped = false;
				}

				if ((this.options.revert === "invalid" && !dropped) ||
					(this.options.revert === "valid" && dropped) ||
					this.options.revert === true || ($.isFunction(this.options.revert) &&
						this.options.revert.call(this.element, dropped))
				) {
					$(this.helper).animate(
						this.originalPosition,
						parseInt(this.options.revertDuration, 10),
						function () {
							if (that._trigger("stop", event) !== false) {
								that._clear();
							}
						}
					);
				} else {
					if (this._trigger("stop", event) !== false) {
						this._clear();
					}
				}

				return false;
			},

			_mouseUp: function (event) {
				this._unblockFrames();

				// If the ddmanager is used for droppables, inform the manager that dragging has stopped
				// (see #5003)
				if ($.ui.ddmanager) {
					$.ui.ddmanager.dragStop(this, event);
				}

				// Only need to focus if the event occurred on the draggable itself, see #10527
				if (this.handleElement.is(event.target)) {

					// The interaction is over; whether or not the click resulted in a drag,
					// focus the element
					this.element.trigger("focus");
				}

				return $.ui.mouse.prototype._mouseUp.call(this, event);
			},

			cancel: function () {

				if (this.helper.is(".ui-draggable-dragging")) {
					this._mouseUp(new $.Event("mouseup", { target: this.element[0] }));
				} else {
					this._clear();
				}

				return this;

			},

			_getHandle: function (event) {
				return this.options.handle ?
					!!$(event.target).closest(this.element.find(this.options.handle)).length :
					true;
			},

			_setHandleClassName: function () {
				this.handleElement = this.options.handle ?
					this.element.find(this.options.handle) : this.element;
				this._addClass(this.handleElement, "ui-draggable-handle");
			},

			_removeHandleClassName: function () {
				this._removeClass(this.handleElement, "ui-draggable-handle");
			},

			_createHelper: function (event) {

				var o = this.options,
					helperIsFunction = $.isFunction(o.helper),
					helper = helperIsFunction ?
						$(o.helper.apply(this.element[0], [event])) :
						(o.helper === "clone" ?
							this.element.clone().removeAttr("id") :
							this.element);

				if (!helper.parents("body").length) {
					helper.appendTo((o.appendTo === "parent" ?
						this.element[0].parentNode :
						o.appendTo));
				}

				// Http://bugs.jqueryui.com/ticket/9446
				// a helper function can return the original element
				// which wouldn't have been set to relative in _create
				if (helperIsFunction && helper[0] === this.element[0]) {
					this._setPositionRelative();
				}

				if (helper[0] !== this.element[0] &&
					!(/(fixed|absolute)/).test(helper.css("position"))) {
					helper.css("position", "absolute");
				}

				return helper;

			},

			_setPositionRelative: function () {
				if (!(/^(?:r|a|f)/).test(this.element.css("position"))) {
					this.element[0].style.position = "relative";
				}
			},

			_adjustOffsetFromHelper: function (obj) {
				if (typeof obj === "string") {
					obj = obj.split(" ");
				}
				if ($.isArray(obj)) {
					obj = { left: +obj[0], top: +obj[1] || 0 };
				}
				if ("left" in obj) {
					this.offset.click.left = obj.left + this.margins.left;
				}
				if ("right" in obj) {
					this.offset.click.left = this.helperProportions.width - obj.right + this.margins.left;
				}
				if ("top" in obj) {
					this.offset.click.top = obj.top + this.margins.top;
				}
				if ("bottom" in obj) {
					this.offset.click.top = this.helperProportions.height - obj.bottom + this.margins.top;
				}
			},

			_isRootNode: function (element) {
				return (/(html|body)/i).test(element.tagName) || element === this.document[0];
			},

			_getParentOffset: function () {

				//Get the offsetParent and cache its position
				var po = this.offsetParent.offset(),
					document = this.document[0];

				// This is a special case where we need to modify a offset calculated on start, since the
				// following happened:
				// 1. The position of the helper is absolute, so it's position is calculated based on the
				// next positioned parent
				// 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't
				// the document, which means that the scroll is included in the initial calculation of the
				// offset of the parent, and never recalculated upon drag
				if (this.cssPosition === "absolute" && this.scrollParent[0] !== document &&
					$.contains(this.scrollParent[0], this.offsetParent[0])) {
					po.left += this.scrollParent.scrollLeft();
					po.top += this.scrollParent.scrollTop();
				}

				if (this._isRootNode(this.offsetParent[0])) {
					po = { top: 0, left: 0 };
				}

				return {
					top: po.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
					left: po.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
				};

			},

			_getRelativeOffset: function () {
				if (this.cssPosition !== "relative") {
					return { top: 0, left: 0 };
				}

				var p = this.element.position(),
					scrollIsRootNode = this._isRootNode(this.scrollParent[0]);

				return {
					top: p.top - (parseInt(this.helper.css("top"), 10) || 0) +
						(!scrollIsRootNode ? this.scrollParent.scrollTop() : 0),
					left: p.left - (parseInt(this.helper.css("left"), 10) || 0) +
						(!scrollIsRootNode ? this.scrollParent.scrollLeft() : 0)
				};

			},

			_cacheMargins: function () {
				this.margins = {
					left: (parseInt(this.element.css("marginLeft"), 10) || 0),
					top: (parseInt(this.element.css("marginTop"), 10) || 0),
					right: (parseInt(this.element.css("marginRight"), 10) || 0),
					bottom: (parseInt(this.element.css("marginBottom"), 10) || 0)
				};
			},

			_cacheHelperProportions: function () {
				this.helperProportions = {
					width: this.helper.outerWidth(),
					height: this.helper.outerHeight()
				};
			},

			_setContainment: function () {

				var isUserScrollable, c, ce,
					o = this.options,
					document = this.document[0];

				this.relativeContainer = null;

				if (!o.containment) {
					this.containment = null;
					return;
				}

				if (o.containment === "window") {
					this.containment = [
						$(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left,
						$(window).scrollTop() - this.offset.relative.top - this.offset.parent.top,
						$(window).scrollLeft() + $(window).width() -
						this.helperProportions.width - this.margins.left,
						$(window).scrollTop() +
						($(window).height() || document.body.parentNode.scrollHeight) -
						this.helperProportions.height - this.margins.top
					];
					return;
				}

				if (o.containment === "document") {
					this.containment = [
						0,
						0,
						$(document).width() - this.helperProportions.width - this.margins.left,
						($(document).height() || document.body.parentNode.scrollHeight) -
						this.helperProportions.height - this.margins.top
					];
					return;
				}

				if (o.containment.constructor === Array) {
					this.containment = o.containment;
					return;
				}

				if (o.containment === "parent") {
					o.containment = this.helper[0].parentNode;
				}

				c = $(o.containment);
				ce = c[0];

				if (!ce) {
					return;
				}

				isUserScrollable = /(scroll|auto)/.test(c.css("overflow"));

				this.containment = [
					(parseInt(c.css("borderLeftWidth"), 10) || 0) +
					(parseInt(c.css("paddingLeft"), 10) || 0),
					(parseInt(c.css("borderTopWidth"), 10) || 0) +
					(parseInt(c.css("paddingTop"), 10) || 0),
					(isUserScrollable ? Math.max(ce.scrollWidth, ce.offsetWidth) : ce.offsetWidth) -
					(parseInt(c.css("borderRightWidth"), 10) || 0) -
					(parseInt(c.css("paddingRight"), 10) || 0) -
					this.helperProportions.width -
					this.margins.left -
					this.margins.right,
					(isUserScrollable ? Math.max(ce.scrollHeight, ce.offsetHeight) : ce.offsetHeight) -
					(parseInt(c.css("borderBottomWidth"), 10) || 0) -
					(parseInt(c.css("paddingBottom"), 10) || 0) -
					this.helperProportions.height -
					this.margins.top -
					this.margins.bottom
				];
				this.relativeContainer = c;
			},

			_convertPositionTo: function (d, pos) {

				if (!pos) {
					pos = this.position;
				}

				var mod = d === "absolute" ? 1 : -1,
					scrollIsRootNode = this._isRootNode(this.scrollParent[0]);

				return {
					top: (

						// The absolute mouse position
						pos.top +

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.top * mod +

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.top * mod -
						((this.cssPosition === "fixed" ?
							-this.offset.scroll.top :
							(scrollIsRootNode ? 0 : this.offset.scroll.top)) * mod)
					),
					left: (

						// The absolute mouse position
						pos.left +

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.left * mod +

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.left * mod -
						((this.cssPosition === "fixed" ?
							-this.offset.scroll.left :
							(scrollIsRootNode ? 0 : this.offset.scroll.left)) * mod)
					)
				};

			},

			_generatePosition: function (event, constrainPosition) {

				var containment, co, top, left,
					o = this.options,
					scrollIsRootNode = this._isRootNode(this.scrollParent[0]),
					pageX = event.pageX,
					pageY = event.pageY;

				// Cache the scroll
				if (!scrollIsRootNode || !this.offset.scroll) {
					this.offset.scroll = {
						top: this.scrollParent.scrollTop(),
						left: this.scrollParent.scrollLeft()
					};
				}

				/*
				 * - Position constraining -
				 * Constrain the position to a mix of grid, containment.
				 */

				// If we are not dragging yet, we won't check for options
				if (constrainPosition) {
					if (this.containment) {
						if (this.relativeContainer) {
							co = this.relativeContainer.offset();
							containment = [
								this.containment[0] + co.left,
								this.containment[1] + co.top,
								this.containment[2] + co.left,
								this.containment[3] + co.top
							];
						} else {
							containment = this.containment;
						}

						if (event.pageX - this.offset.click.left < containment[0]) {
							pageX = containment[0] + this.offset.click.left;
						}
						if (event.pageY - this.offset.click.top < containment[1]) {
							pageY = containment[1] + this.offset.click.top;
						}
						if (event.pageX - this.offset.click.left > containment[2]) {
							pageX = containment[2] + this.offset.click.left;
						}
						if (event.pageY - this.offset.click.top > containment[3]) {
							pageY = containment[3] + this.offset.click.top;
						}
					}

					if (o.grid) {

						//Check for grid elements set to 0 to prevent divide by 0 error causing invalid
						// argument errors in IE (see ticket #6950)
						top = o.grid[1] ? this.originalPageY + Math.round((pageY -
							this.originalPageY) / o.grid[1]) * o.grid[1] : this.originalPageY;
						pageY = containment ? ((top - this.offset.click.top >= containment[1] ||
							top - this.offset.click.top > containment[3]) ?
							top :
							((top - this.offset.click.top >= containment[1]) ?
								top - o.grid[1] : top + o.grid[1])) : top;

						left = o.grid[0] ? this.originalPageX +
							Math.round((pageX - this.originalPageX) / o.grid[0]) * o.grid[0] :
							this.originalPageX;
						pageX = containment ? ((left - this.offset.click.left >= containment[0] ||
							left - this.offset.click.left > containment[2]) ?
							left :
							((left - this.offset.click.left >= containment[0]) ?
								left - o.grid[0] : left + o.grid[0])) : left;
					}

					if (o.axis === "y") {
						pageX = this.originalPageX;
					}

					if (o.axis === "x") {
						pageY = this.originalPageY;
					}
				}

				return {
					top: (

						// The absolute mouse position
						pageY -

						// Click offset (relative to the element)
						this.offset.click.top -

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.top -

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.top +
						(this.cssPosition === "fixed" ?
							-this.offset.scroll.top :
							(scrollIsRootNode ? 0 : this.offset.scroll.top))
					),
					left: (

						// The absolute mouse position
						pageX -

						// Click offset (relative to the element)
						this.offset.click.left -

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.left -

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.left +
						(this.cssPosition === "fixed" ?
							-this.offset.scroll.left :
							(scrollIsRootNode ? 0 : this.offset.scroll.left))
					)
				};

			},

			_clear: function () {
				this._removeClass(this.helper, "ui-draggable-dragging");
				if (this.helper[0] !== this.element[0] && !this.cancelHelperRemoval) {
					this.helper.remove();
				}
				this.helper = null;
				this.cancelHelperRemoval = false;
				if (this.destroyOnClear) {
					this.destroy();
				}
			},

			// From now on bulk stuff - mainly helpers

			_trigger: function (type, event, ui) {
				ui = ui || this._uiHash();
				$.ui.plugin.call(this, type, [event, ui, this], true);

				// Absolute position and offset (see #6884 ) have to be recalculated after plugins
				if (/^(drag|start|stop)/.test(type)) {
					this.positionAbs = this._convertPositionTo("absolute");
					ui.offset = this.positionAbs;
				}
				return $.Widget.prototype._trigger.call(this, type, event, ui);
			},

			plugins: {},

			_uiHash: function () {
				return {
					helper: this.helper,
					position: this.position,
					originalPosition: this.originalPosition,
					offset: this.positionAbs
				};
			}

		});

		$.ui.plugin.add("draggable", "connectToSortable", {
			start: function (event, ui, draggable) {
				var uiSortable = $.extend({}, ui, {
					item: draggable.element
				});

				draggable.sortables = [];
				$(draggable.options.connectToSortable).each(function () {
					var sortable = $(this).sortable("instance");

					if (sortable && !sortable.options.disabled) {
						draggable.sortables.push(sortable);

						// RefreshPositions is called at drag start to refresh the containerCache
						// which is used in drag. This ensures it's initialized and synchronized
						// with any changes that might have happened on the page since initialization.
						sortable.refreshPositions();
						sortable._trigger("activate", event, uiSortable);
					}
				});
			},
			stop: function (event, ui, draggable) {
				var uiSortable = $.extend({}, ui, {
					item: draggable.element
				});

				draggable.cancelHelperRemoval = false;

				$.each(draggable.sortables, function () {
					var sortable = this;

					if (sortable.isOver) {
						sortable.isOver = 0;

						// Allow this sortable to handle removing the helper
						draggable.cancelHelperRemoval = true;
						sortable.cancelHelperRemoval = false;

						// Use _storedCSS To restore properties in the sortable,
						// as this also handles revert (#9675) since the draggable
						// may have modified them in unexpected ways (#8809)
						sortable._storedCSS = {
							position: sortable.placeholder.css("position"),
							top: sortable.placeholder.css("top"),
							left: sortable.placeholder.css("left")
						};

						sortable._mouseStop(event);

						// Once drag has ended, the sortable should return to using
						// its original helper, not the shared helper from draggable
						sortable.options.helper = sortable.options._helper;
					} else {

						// Prevent this Sortable from removing the helper.
						// However, don't set the draggable to remove the helper
						// either as another connected Sortable may yet handle the removal.
						sortable.cancelHelperRemoval = true;

						sortable._trigger("deactivate", event, uiSortable);
					}
				});
			},
			drag: function (event, ui, draggable) {
				$.each(draggable.sortables, function () {
					var innermostIntersecting = false,
						sortable = this;

					// Copy over variables that sortable's _intersectsWith uses
					sortable.positionAbs = draggable.positionAbs;
					sortable.helperProportions = draggable.helperProportions;
					sortable.offset.click = draggable.offset.click;

					if (sortable._intersectsWith(sortable.containerCache)) {
						innermostIntersecting = true;

						$.each(draggable.sortables, function () {

							// Copy over variables that sortable's _intersectsWith uses
							this.positionAbs = draggable.positionAbs;
							this.helperProportions = draggable.helperProportions;
							this.offset.click = draggable.offset.click;

							if (this !== sortable &&
								this._intersectsWith(this.containerCache) &&
								$.contains(sortable.element[0], this.element[0])) {
								innermostIntersecting = false;
							}

							return innermostIntersecting;
						});
					}

					if (innermostIntersecting) {

						// If it intersects, we use a little isOver variable and set it once,
						// so that the move-in stuff gets fired only once.
						if (!sortable.isOver) {
							sortable.isOver = 1;

							// Store draggable's parent in case we need to reappend to it later.
							draggable._parent = ui.helper.parent();

							sortable.currentItem = ui.helper
								.appendTo(sortable.element)
								.data("ui-sortable-item", true);

							// Store helper option to later restore it
							sortable.options._helper = sortable.options.helper;

							sortable.options.helper = function () {
								return ui.helper[0];
							};

							// Fire the start events of the sortable with our passed browser event,
							// and our own helper (so it doesn't create a new one)
							event.target = sortable.currentItem[0];
							sortable._mouseCapture(event, true);
							sortable._mouseStart(event, true, true);

							// Because the browser event is way off the new appended portlet,
							// modify necessary variables to reflect the changes
							sortable.offset.click.top = draggable.offset.click.top;
							sortable.offset.click.left = draggable.offset.click.left;
							sortable.offset.parent.left -= draggable.offset.parent.left -
								sortable.offset.parent.left;
							sortable.offset.parent.top -= draggable.offset.parent.top -
								sortable.offset.parent.top;

							draggable._trigger("toSortable", event);

							// Inform draggable that the helper is in a valid drop zone,
							// used solely in the revert option to handle "valid/invalid".
							draggable.dropped = sortable.element;

							// Need to refreshPositions of all sortables in the case that
							// adding to one sortable changes the location of the other sortables (#9675)
							$.each(draggable.sortables, function () {
								this.refreshPositions();
							});

							// Hack so receive/update callbacks work (mostly)
							draggable.currentItem = draggable.element;
							sortable.fromOutside = draggable;
						}

						if (sortable.currentItem) {
							sortable._mouseDrag(event);

							// Copy the sortable's position because the draggable's can potentially reflect
							// a relative position, while sortable is always absolute, which the dragged
							// element has now become. (#8809)
							ui.position = sortable.position;
						}
					} else {

						// If it doesn't intersect with the sortable, and it intersected before,
						// we fake the drag stop of the sortable, but make sure it doesn't remove
						// the helper by using cancelHelperRemoval.
						if (sortable.isOver) {

							sortable.isOver = 0;
							sortable.cancelHelperRemoval = true;

							// Calling sortable's mouseStop would trigger a revert,
							// so revert must be temporarily false until after mouseStop is called.
							sortable.options._revert = sortable.options.revert;
							sortable.options.revert = false;

							sortable._trigger("out", event, sortable._uiHash(sortable));
							sortable._mouseStop(event, true);

							// Restore sortable behaviors that were modfied
							// when the draggable entered the sortable area (#9481)
							sortable.options.revert = sortable.options._revert;
							sortable.options.helper = sortable.options._helper;

							if (sortable.placeholder) {
								sortable.placeholder.remove();
							}

							// Restore and recalculate the draggable's offset considering the sortable
							// may have modified them in unexpected ways. (#8809, #10669)
							ui.helper.appendTo(draggable._parent);
							draggable._refreshOffsets(event);
							ui.position = draggable._generatePosition(event, true);

							draggable._trigger("fromSortable", event);

							// Inform draggable that the helper is no longer in a valid drop zone
							draggable.dropped = false;

							// Need to refreshPositions of all sortables just in case removing
							// from one sortable changes the location of other sortables (#9675)
							$.each(draggable.sortables, function () {
								this.refreshPositions();
							});
						}
					}
				});
			}
		});

		$.ui.plugin.add("draggable", "cursor", {
			start: function (event, ui, instance) {
				var t = $("body"),
					o = instance.options;

				if (t.css("cursor")) {
					o._cursor = t.css("cursor");
				}
				t.css("cursor", o.cursor);
			},
			stop: function (event, ui, instance) {
				var o = instance.options;
				if (o._cursor) {
					$("body").css("cursor", o._cursor);
				}
			}
		});

		$.ui.plugin.add("draggable", "opacity", {
			start: function (event, ui, instance) {
				var t = $(ui.helper),
					o = instance.options;
				if (t.css("opacity")) {
					o._opacity = t.css("opacity");
				}
				t.css("opacity", o.opacity);
			},
			stop: function (event, ui, instance) {
				var o = instance.options;
				if (o._opacity) {
					$(ui.helper).css("opacity", o._opacity);
				}
			}
		});

		$.ui.plugin.add("draggable", "scroll", {
			start: function (event, ui, i) {
				if (!i.scrollParentNotHidden) {
					i.scrollParentNotHidden = i.helper.scrollParent(false);
				}

				if (i.scrollParentNotHidden[0] !== i.document[0] &&
					i.scrollParentNotHidden[0].tagName !== "HTML") {
					i.overflowOffset = i.scrollParentNotHidden.offset();
				}
			},
			drag: function (event, ui, i) {

				var o = i.options,
					scrolled = false,
					scrollParent = i.scrollParentNotHidden[0],
					document = i.document[0];

				if (scrollParent !== document && scrollParent.tagName !== "HTML") {
					if (!o.axis || o.axis !== "x") {
						if ((i.overflowOffset.top + scrollParent.offsetHeight) - event.pageY <
							o.scrollSensitivity) {
							scrollParent.scrollTop = scrolled = scrollParent.scrollTop + o.scrollSpeed;
						} else if (event.pageY - i.overflowOffset.top < o.scrollSensitivity) {
							scrollParent.scrollTop = scrolled = scrollParent.scrollTop - o.scrollSpeed;
						}
					}

					if (!o.axis || o.axis !== "y") {
						if ((i.overflowOffset.left + scrollParent.offsetWidth) - event.pageX <
							o.scrollSensitivity) {
							scrollParent.scrollLeft = scrolled = scrollParent.scrollLeft + o.scrollSpeed;
						} else if (event.pageX - i.overflowOffset.left < o.scrollSensitivity) {
							scrollParent.scrollLeft = scrolled = scrollParent.scrollLeft - o.scrollSpeed;
						}
					}

				} else {

					if (!o.axis || o.axis !== "x") {
						if (event.pageY - $(document).scrollTop() < o.scrollSensitivity) {
							scrolled = $(document).scrollTop($(document).scrollTop() - o.scrollSpeed);
						} else if ($(window).height() - (event.pageY - $(document).scrollTop()) <
							o.scrollSensitivity) {
							scrolled = $(document).scrollTop($(document).scrollTop() + o.scrollSpeed);
						}
					}

					if (!o.axis || o.axis !== "y") {
						if (event.pageX - $(document).scrollLeft() < o.scrollSensitivity) {
							scrolled = $(document).scrollLeft(
								$(document).scrollLeft() - o.scrollSpeed
							);
						} else if ($(window).width() - (event.pageX - $(document).scrollLeft()) <
							o.scrollSensitivity) {
							scrolled = $(document).scrollLeft(
								$(document).scrollLeft() + o.scrollSpeed
							);
						}
					}

				}

				if (scrolled !== false && $.ui.ddmanager && !o.dropBehaviour) {
					$.ui.ddmanager.prepareOffsets(i, event);
				}

			}
		});

		$.ui.plugin.add("draggable", "snap", {
			start: function (event, ui, i) {

				var o = i.options;

				i.snapElements = [];

				$(o.snap.constructor !== String ? (o.snap.items || ":data(ui-draggable)") : o.snap)
					.each(function () {
						var $t = $(this),
							$o = $t.offset();
						if (this !== i.element[0]) {
							i.snapElements.push({
								item: this,
								width: $t.outerWidth(), height: $t.outerHeight(),
								top: $o.top, left: $o.left
							});
						}
					});

			},
			drag: function (event, ui, inst) {

				var ts, bs, ls, rs, l, r, t, b, i, first,
					o = inst.options,
					d = o.snapTolerance,
					x1 = ui.offset.left, x2 = x1 + inst.helperProportions.width,
					y1 = ui.offset.top, y2 = y1 + inst.helperProportions.height;

				for (i = inst.snapElements.length - 1; i >= 0; i--) {

					l = inst.snapElements[i].left - inst.margins.left;
					r = l + inst.snapElements[i].width;
					t = inst.snapElements[i].top - inst.margins.top;
					b = t + inst.snapElements[i].height;

					if (x2 < l - d || x1 > r + d || y2 < t - d || y1 > b + d ||
						!$.contains(inst.snapElements[i].item.ownerDocument,
							inst.snapElements[i].item)) {
						if (inst.snapElements[i].snapping) {
							(inst.options.snap.release &&
								inst.options.snap.release.call(
									inst.element,
									event,
									$.extend(inst._uiHash(), { snapItem: inst.snapElements[i].item })
								));
						}
						inst.snapElements[i].snapping = false;
						continue;
					}

					if (o.snapMode !== "inner") {
						ts = Math.abs(t - y2) <= d;
						bs = Math.abs(b - y1) <= d;
						ls = Math.abs(l - x2) <= d;
						rs = Math.abs(r - x1) <= d;
						if (ts) {
							ui.position.top = inst._convertPositionTo("relative", {
								top: t - inst.helperProportions.height,
								left: 0
							}).top;
						}
						if (bs) {
							ui.position.top = inst._convertPositionTo("relative", {
								top: b,
								left: 0
							}).top;
						}
						if (ls) {
							ui.position.left = inst._convertPositionTo("relative", {
								top: 0,
								left: l - inst.helperProportions.width
							}).left;
						}
						if (rs) {
							ui.position.left = inst._convertPositionTo("relative", {
								top: 0,
								left: r
							}).left;
						}
					}

					first = (ts || bs || ls || rs);

					if (o.snapMode !== "outer") {
						ts = Math.abs(t - y1) <= d;
						bs = Math.abs(b - y2) <= d;
						ls = Math.abs(l - x1) <= d;
						rs = Math.abs(r - x2) <= d;
						if (ts) {
							ui.position.top = inst._convertPositionTo("relative", {
								top: t,
								left: 0
							}).top;
						}
						if (bs) {
							ui.position.top = inst._convertPositionTo("relative", {
								top: b - inst.helperProportions.height,
								left: 0
							}).top;
						}
						if (ls) {
							ui.position.left = inst._convertPositionTo("relative", {
								top: 0,
								left: l
							}).left;
						}
						if (rs) {
							ui.position.left = inst._convertPositionTo("relative", {
								top: 0,
								left: r - inst.helperProportions.width
							}).left;
						}
					}

					if (!inst.snapElements[i].snapping && (ts || bs || ls || rs || first)) {
						(inst.options.snap.snap &&
							inst.options.snap.snap.call(
								inst.element,
								event,
								$.extend(inst._uiHash(), {
									snapItem: inst.snapElements[i].item
								})));
					}
					inst.snapElements[i].snapping = (ts || bs || ls || rs || first);

				}

			}
		});

		$.ui.plugin.add("draggable", "stack", {
			start: function (event, ui, instance) {
				var min,
					o = instance.options,
					group = $.makeArray($(o.stack)).sort(function (a, b) {
						return (parseInt($(a).css("zIndex"), 10) || 0) -
							(parseInt($(b).css("zIndex"), 10) || 0);
					});

				if (!group.length) { return; }

				min = parseInt($(group[0]).css("zIndex"), 10) || 0;
				$(group).each(function (i) {
					$(this).css("zIndex", min + i);
				});
				this.css("zIndex", (min + group.length));
			}
		});

		$.ui.plugin.add("draggable", "zIndex", {
			start: function (event, ui, instance) {
				var t = $(ui.helper),
					o = instance.options;

				if (t.css("zIndex")) {
					o._zIndex = t.css("zIndex");
				}
				t.css("zIndex", o.zIndex);
			},
			stop: function (event, ui, instance) {
				var o = instance.options;

				if (o._zIndex) {
					$(ui.helper).css("zIndex", o._zIndex);
				}
			}
		});

		var widgetsDraggable = $.ui.draggable;


		/*!
		 * jQuery UI Resizable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Resizable
		//>>group: Interactions
		//>>description: Enables resize functionality for any element.
		//>>docs: http://api.jqueryui.com/resizable/
		//>>demos: http://jqueryui.com/resizable/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/resizable.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.resizable", $.ui.mouse, {
			version: "1.12.1",
			widgetEventPrefix: "resize",
			options: {
				alsoResize: false,
				animate: false,
				animateDuration: "slow",
				animateEasing: "swing",
				aspectRatio: false,
				autoHide: false,
				classes: {
					"ui-resizable-se": "ui-icon ui-icon-gripsmall-diagonal-se"
				},
				containment: false,
				ghost: false,
				grid: false,
				handles: "e,s,se",
				helper: false,
				maxHeight: null,
				maxWidth: null,
				minHeight: 10,
				minWidth: 10,

				// See #7960
				zIndex: 90,

				// Callbacks
				resize: null,
				start: null,
				stop: null
			},

			_num: function (value) {
				return parseFloat(value) || 0;
			},

			_isNumber: function (value) {
				return !isNaN(parseFloat(value));
			},

			_hasScroll: function (el, a) {

				if ($(el).css("overflow") === "hidden") {
					return false;
				}

				var scroll = (a && a === "left") ? "scrollLeft" : "scrollTop",
					has = false;

				if (el[scroll] > 0) {
					return true;
				}

				// TODO: determine which cases actually cause this to happen
				// if the element doesn't have the scroll set, see if it's possible to
				// set the scroll
				el[scroll] = 1;
				has = (el[scroll] > 0);
				el[scroll] = 0;
				return has;
			},

			_create: function () {

				var margins,
					o = this.options,
					that = this;
				this._addClass("ui-resizable");

				$.extend(this, {
					_aspectRatio: !!(o.aspectRatio),
					aspectRatio: o.aspectRatio,
					originalElement: this.element,
					_proportionallyResizeElements: [],
					_helper: o.helper || o.ghost || o.animate ? o.helper || "ui-resizable-helper" : null
				});

				// Wrap the element if it cannot hold child nodes
				if (this.element[0].nodeName.match(/^(canvas|textarea|input|select|button|img)$/i)) {

					this.element.wrap(
						$("<div class='ui-wrapper' style='overflow: hidden;'></div>").css({
							position: this.element.css("position"),
							width: this.element.outerWidth(),
							height: this.element.outerHeight(),
							top: this.element.css("top"),
							left: this.element.css("left")
						})
					);

					this.element = this.element.parent().data(
						"ui-resizable", this.element.resizable("instance")
					);

					this.elementIsWrapper = true;

					margins = {
						marginTop: this.originalElement.css("marginTop"),
						marginRight: this.originalElement.css("marginRight"),
						marginBottom: this.originalElement.css("marginBottom"),
						marginLeft: this.originalElement.css("marginLeft")
					};

					this.element.css(margins);
					this.originalElement.css("margin", 0);

					// support: Safari
					// Prevent Safari textarea resize
					this.originalResizeStyle = this.originalElement.css("resize");
					this.originalElement.css("resize", "none");

					this._proportionallyResizeElements.push(this.originalElement.css({
						position: "static",
						zoom: 1,
						display: "block"
					}));

					// Support: IE9
					// avoid IE jump (hard set the margin)
					this.originalElement.css(margins);

					this._proportionallyResize();
				}

				this._setupHandles();

				if (o.autoHide) {
					$(this.element)
						.on("mouseenter", function () {
							if (o.disabled) {
								return;
							}
							that._removeClass("ui-resizable-autohide");
							that._handles.show();
						})
						.on("mouseleave", function () {
							if (o.disabled) {
								return;
							}
							if (!that.resizing) {
								that._addClass("ui-resizable-autohide");
								that._handles.hide();
							}
						});
				}

				this._mouseInit();
			},

			_destroy: function () {

				this._mouseDestroy();

				var wrapper,
					_destroy = function (exp) {
						$(exp)
							.removeData("resizable")
							.removeData("ui-resizable")
							.off(".resizable")
							.find(".ui-resizable-handle")
							.remove();
					};

				// TODO: Unwrap at same DOM position
				if (this.elementIsWrapper) {
					_destroy(this.element);
					wrapper = this.element;
					this.originalElement.css({
						position: wrapper.css("position"),
						width: wrapper.outerWidth(),
						height: wrapper.outerHeight(),
						top: wrapper.css("top"),
						left: wrapper.css("left")
					}).insertAfter(wrapper);
					wrapper.remove();
				}

				this.originalElement.css("resize", this.originalResizeStyle);
				_destroy(this.originalElement);

				return this;
			},

			_setOption: function (key, value) {
				this._super(key, value);

				switch (key) {
					case "handles":
						this._removeHandles();
						this._setupHandles();
						break;
					default:
						break;
				}
			},

			_setupHandles: function () {
				var o = this.options, handle, i, n, hname, axis, that = this;
				this.handles = o.handles ||
					(!$(".ui-resizable-handle", this.element).length ?
						"e,s,se" : {
							n: ".ui-resizable-n",
							e: ".ui-resizable-e",
							s: ".ui-resizable-s",
							w: ".ui-resizable-w",
							se: ".ui-resizable-se",
							sw: ".ui-resizable-sw",
							ne: ".ui-resizable-ne",
							nw: ".ui-resizable-nw"
						});

				this._handles = $();
				if (this.handles.constructor === String) {

					if (this.handles === "all") {
						this.handles = "n,e,s,w,se,sw,ne,nw";
					}

					n = this.handles.split(",");
					this.handles = {};

					for (i = 0; i < n.length; i++) {

						handle = $.trim(n[i]);
						hname = "ui-resizable-" + handle;
						axis = $("<div>");
						this._addClass(axis, "ui-resizable-handle " + hname);

						axis.css({ zIndex: o.zIndex });

						this.handles[handle] = ".ui-resizable-" + handle;
						this.element.append(axis);
					}

				}

				this._renderAxis = function (target) {

					var i, axis, padPos, padWrapper;

					target = target || this.element;

					for (i in this.handles) {

						if (this.handles[i].constructor === String) {
							this.handles[i] = this.element.children(this.handles[i]).first().show();
						} else if (this.handles[i].jquery || this.handles[i].nodeType) {
							this.handles[i] = $(this.handles[i]);
							this._on(this.handles[i], { "mousedown": that._mouseDown });
						}

						if (this.elementIsWrapper &&
							this.originalElement[0]
								.nodeName
								.match(/^(textarea|input|select|button)$/i)) {
							axis = $(this.handles[i], this.element);

							padWrapper = /sw|ne|nw|se|n|s/.test(i) ?
								axis.outerHeight() :
								axis.outerWidth();

							padPos = ["padding",
								/ne|nw|n/.test(i) ? "Top" :
									/se|sw|s/.test(i) ? "Bottom" :
										/^e$/.test(i) ? "Right" : "Left"].join("");

							target.css(padPos, padWrapper);

							this._proportionallyResize();
						}

						this._handles = this._handles.add(this.handles[i]);
					}
				};

				// TODO: make renderAxis a prototype function
				this._renderAxis(this.element);

				this._handles = this._handles.add(this.element.find(".ui-resizable-handle"));
				this._handles.disableSelection();

				this._handles.on("mouseover", function () {
					if (!that.resizing) {
						if (this.className) {
							axis = this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
						}
						that.axis = axis && axis[1] ? axis[1] : "se";
					}
				});

				if (o.autoHide) {
					this._handles.hide();
					this._addClass("ui-resizable-autohide");
				}
			},

			_removeHandles: function () {
				this._handles.remove();
			},

			_mouseCapture: function (event) {
				var i, handle,
					capture = false;

				for (i in this.handles) {
					handle = $(this.handles[i])[0];
					if (handle === event.target || $.contains(handle, event.target)) {
						capture = true;
					}
				}

				return !this.options.disabled && capture;
			},

			_mouseStart: function (event) {

				var curleft, curtop, cursor,
					o = this.options,
					el = this.element;

				this.resizing = true;

				this._renderProxy();

				curleft = this._num(this.helper.css("left"));
				curtop = this._num(this.helper.css("top"));

				if (o.containment) {
					curleft += $(o.containment).scrollLeft() || 0;
					curtop += $(o.containment).scrollTop() || 0;
				}

				this.offset = this.helper.offset();
				this.position = { left: curleft, top: curtop };

				this.size = this._helper ? {
					width: this.helper.width(),
					height: this.helper.height()
				} : {
					width: el.width(),
					height: el.height()
				};

				this.originalSize = this._helper ? {
					width: el.outerWidth(),
					height: el.outerHeight()
				} : {
					width: el.width(),
					height: el.height()
				};

				this.sizeDiff = {
					width: el.outerWidth() - el.width(),
					height: el.outerHeight() - el.height()
				};

				this.originalPosition = { left: curleft, top: curtop };
				this.originalMousePosition = { left: event.pageX, top: event.pageY };

				this.aspectRatio = (typeof o.aspectRatio === "number") ?
					o.aspectRatio :
					((this.originalSize.width / this.originalSize.height) || 1);

				cursor = $(".ui-resizable-" + this.axis).css("cursor");
				$("body").css("cursor", cursor === "auto" ? this.axis + "-resize" : cursor);

				this._addClass("ui-resizable-resizing");
				this._propagate("start", event);
				return true;
			},

			_mouseDrag: function (event) {

				var data, props,
					smp = this.originalMousePosition,
					a = this.axis,
					dx = (event.pageX - smp.left) || 0,
					dy = (event.pageY - smp.top) || 0,
					trigger = this._change[a];

				this._updatePrevProperties();

				if (!trigger) {
					return false;
				}

				data = trigger.apply(this, [event, dx, dy]);

				this._updateVirtualBoundaries(event.shiftKey);
				if (this._aspectRatio || event.shiftKey) {
					data = this._updateRatio(data, event);
				}

				data = this._respectSize(data, event);

				this._updateCache(data);

				this._propagate("resize", event);

				props = this._applyChanges();

				if (!this._helper && this._proportionallyResizeElements.length) {
					this._proportionallyResize();
				}

				if (!$.isEmptyObject(props)) {
					this._updatePrevProperties();
					this._trigger("resize", event, this.ui());
					this._applyChanges();
				}

				return false;
			},

			_mouseStop: function (event) {

				this.resizing = false;
				var pr, ista, soffseth, soffsetw, s, left, top,
					o = this.options, that = this;

				if (this._helper) {

					pr = this._proportionallyResizeElements;
					ista = pr.length && (/textarea/i).test(pr[0].nodeName);
					soffseth = ista && this._hasScroll(pr[0], "left") ? 0 : that.sizeDiff.height;
					soffsetw = ista ? 0 : that.sizeDiff.width;

					s = {
						width: (that.helper.width() - soffsetw),
						height: (that.helper.height() - soffseth)
					};
					left = (parseFloat(that.element.css("left")) +
						(that.position.left - that.originalPosition.left)) || null;
					top = (parseFloat(that.element.css("top")) +
						(that.position.top - that.originalPosition.top)) || null;

					if (!o.animate) {
						this.element.css($.extend(s, { top: top, left: left }));
					}

					that.helper.height(that.size.height);
					that.helper.width(that.size.width);

					if (this._helper && !o.animate) {
						this._proportionallyResize();
					}
				}

				$("body").css("cursor", "auto");

				this._removeClass("ui-resizable-resizing");

				this._propagate("stop", event);

				if (this._helper) {
					this.helper.remove();
				}

				return false;

			},

			_updatePrevProperties: function () {
				this.prevPosition = {
					top: this.position.top,
					left: this.position.left
				};
				this.prevSize = {
					width: this.size.width,
					height: this.size.height
				};
			},

			_applyChanges: function () {
				var props = {};

				if (this.position.top !== this.prevPosition.top) {
					props.top = this.position.top + "px";
				}
				if (this.position.left !== this.prevPosition.left) {
					props.left = this.position.left + "px";
				}
				if (this.size.width !== this.prevSize.width) {
					props.width = this.size.width + "px";
				}
				if (this.size.height !== this.prevSize.height) {
					props.height = this.size.height + "px";
				}

				this.helper.css(props);

				return props;
			},

			_updateVirtualBoundaries: function (forceAspectRatio) {
				var pMinWidth, pMaxWidth, pMinHeight, pMaxHeight, b,
					o = this.options;

				b = {
					minWidth: this._isNumber(o.minWidth) ? o.minWidth : 0,
					maxWidth: this._isNumber(o.maxWidth) ? o.maxWidth : Infinity,
					minHeight: this._isNumber(o.minHeight) ? o.minHeight : 0,
					maxHeight: this._isNumber(o.maxHeight) ? o.maxHeight : Infinity
				};

				if (this._aspectRatio || forceAspectRatio) {
					pMinWidth = b.minHeight * this.aspectRatio;
					pMinHeight = b.minWidth / this.aspectRatio;
					pMaxWidth = b.maxHeight * this.aspectRatio;
					pMaxHeight = b.maxWidth / this.aspectRatio;

					if (pMinWidth > b.minWidth) {
						b.minWidth = pMinWidth;
					}
					if (pMinHeight > b.minHeight) {
						b.minHeight = pMinHeight;
					}
					if (pMaxWidth < b.maxWidth) {
						b.maxWidth = pMaxWidth;
					}
					if (pMaxHeight < b.maxHeight) {
						b.maxHeight = pMaxHeight;
					}
				}
				this._vBoundaries = b;
			},

			_updateCache: function (data) {
				this.offset = this.helper.offset();
				if (this._isNumber(data.left)) {
					this.position.left = data.left;
				}
				if (this._isNumber(data.top)) {
					this.position.top = data.top;
				}
				if (this._isNumber(data.height)) {
					this.size.height = data.height;
				}
				if (this._isNumber(data.width)) {
					this.size.width = data.width;
				}
			},

			_updateRatio: function (data) {

				var cpos = this.position,
					csize = this.size,
					a = this.axis;

				if (this._isNumber(data.height)) {
					data.width = (data.height * this.aspectRatio);
				} else if (this._isNumber(data.width)) {
					data.height = (data.width / this.aspectRatio);
				}

				if (a === "sw") {
					data.left = cpos.left + (csize.width - data.width);
					data.top = null;
				}
				if (a === "nw") {
					data.top = cpos.top + (csize.height - data.height);
					data.left = cpos.left + (csize.width - data.width);
				}

				return data;
			},

			_respectSize: function (data) {

				var o = this._vBoundaries,
					a = this.axis,
					ismaxw = this._isNumber(data.width) && o.maxWidth && (o.maxWidth < data.width),
					ismaxh = this._isNumber(data.height) && o.maxHeight && (o.maxHeight < data.height),
					isminw = this._isNumber(data.width) && o.minWidth && (o.minWidth > data.width),
					isminh = this._isNumber(data.height) && o.minHeight && (o.minHeight > data.height),
					dw = this.originalPosition.left + this.originalSize.width,
					dh = this.originalPosition.top + this.originalSize.height,
					cw = /sw|nw|w/.test(a), ch = /nw|ne|n/.test(a);
				if (isminw) {
					data.width = o.minWidth;
				}
				if (isminh) {
					data.height = o.minHeight;
				}
				if (ismaxw) {
					data.width = o.maxWidth;
				}
				if (ismaxh) {
					data.height = o.maxHeight;
				}

				if (isminw && cw) {
					data.left = dw - o.minWidth;
				}
				if (ismaxw && cw) {
					data.left = dw - o.maxWidth;
				}
				if (isminh && ch) {
					data.top = dh - o.minHeight;
				}
				if (ismaxh && ch) {
					data.top = dh - o.maxHeight;
				}

				// Fixing jump error on top/left - bug #2330
				if (!data.width && !data.height && !data.left && data.top) {
					data.top = null;
				} else if (!data.width && !data.height && !data.top && data.left) {
					data.left = null;
				}

				return data;
			},

			_getPaddingPlusBorderDimensions: function (element) {
				var i = 0,
					widths = [],
					borders = [
						element.css("borderTopWidth"),
						element.css("borderRightWidth"),
						element.css("borderBottomWidth"),
						element.css("borderLeftWidth")
					],
					paddings = [
						element.css("paddingTop"),
						element.css("paddingRight"),
						element.css("paddingBottom"),
						element.css("paddingLeft")
					];

				for (; i < 4; i++) {
					widths[i] = (parseFloat(borders[i]) || 0);
					widths[i] += (parseFloat(paddings[i]) || 0);
				}

				return {
					height: widths[0] + widths[2],
					width: widths[1] + widths[3]
				};
			},

			_proportionallyResize: function () {

				if (!this._proportionallyResizeElements.length) {
					return;
				}

				var prel,
					i = 0,
					element = this.helper || this.element;

				for (; i < this._proportionallyResizeElements.length; i++) {

					prel = this._proportionallyResizeElements[i];

					// TODO: Seems like a bug to cache this.outerDimensions
					// considering that we are in a loop.
					if (!this.outerDimensions) {
						this.outerDimensions = this._getPaddingPlusBorderDimensions(prel);
					}

					prel.css({
						height: (element.height() - this.outerDimensions.height) || 0,
						width: (element.width() - this.outerDimensions.width) || 0
					});

				}

			},

			_renderProxy: function () {

				var el = this.element, o = this.options;
				this.elementOffset = el.offset();

				if (this._helper) {

					this.helper = this.helper || $("<div style='overflow:hidden;'></div>");

					this._addClass(this.helper, this._helper);
					this.helper.css({
						width: this.element.outerWidth(),
						height: this.element.outerHeight(),
						position: "absolute",
						left: this.elementOffset.left + "px",
						top: this.elementOffset.top + "px",
						zIndex: ++o.zIndex //TODO: Don't modify option
					});

					this.helper
						.appendTo("body")
						.disableSelection();

				} else {
					this.helper = this.element;
				}

			},

			_change: {
				e: function (event, dx) {
					return { width: this.originalSize.width + dx };
				},
				w: function (event, dx) {
					var cs = this.originalSize, sp = this.originalPosition;
					return { left: sp.left + dx, width: cs.width - dx };
				},
				n: function (event, dx, dy) {
					var cs = this.originalSize, sp = this.originalPosition;
					return { top: sp.top + dy, height: cs.height - dy };
				},
				s: function (event, dx, dy) {
					return { height: this.originalSize.height + dy };
				},
				se: function (event, dx, dy) {
					return $.extend(this._change.s.apply(this, arguments),
						this._change.e.apply(this, [event, dx, dy]));
				},
				sw: function (event, dx, dy) {
					return $.extend(this._change.s.apply(this, arguments),
						this._change.w.apply(this, [event, dx, dy]));
				},
				ne: function (event, dx, dy) {
					return $.extend(this._change.n.apply(this, arguments),
						this._change.e.apply(this, [event, dx, dy]));
				},
				nw: function (event, dx, dy) {
					return $.extend(this._change.n.apply(this, arguments),
						this._change.w.apply(this, [event, dx, dy]));
				}
			},

			_propagate: function (n, event) {
				$.ui.plugin.call(this, n, [event, this.ui()]);
				(n !== "resize" && this._trigger(n, event, this.ui()));
			},

			plugins: {},

			ui: function () {
				return {
					originalElement: this.originalElement,
					element: this.element,
					helper: this.helper,
					position: this.position,
					size: this.size,
					originalSize: this.originalSize,
					originalPosition: this.originalPosition
				};
			}

		});

		/*
		 * Resizable Extensions
		 */

		$.ui.plugin.add("resizable", "animate", {

			stop: function (event) {
				var that = $(this).resizable("instance"),
					o = that.options,
					pr = that._proportionallyResizeElements,
					ista = pr.length && (/textarea/i).test(pr[0].nodeName),
					soffseth = ista && that._hasScroll(pr[0], "left") ? 0 : that.sizeDiff.height,
					soffsetw = ista ? 0 : that.sizeDiff.width,
					style = {
						width: (that.size.width - soffsetw),
						height: (that.size.height - soffseth)
					},
					left = (parseFloat(that.element.css("left")) +
						(that.position.left - that.originalPosition.left)) || null,
					top = (parseFloat(that.element.css("top")) +
						(that.position.top - that.originalPosition.top)) || null;

				that.element.animate(
					$.extend(style, top && left ? { top: top, left: left } : {}), {
					duration: o.animateDuration,
					easing: o.animateEasing,
					step: function () {

						var data = {
							width: parseFloat(that.element.css("width")),
							height: parseFloat(that.element.css("height")),
							top: parseFloat(that.element.css("top")),
							left: parseFloat(that.element.css("left"))
						};

						if (pr && pr.length) {
							$(pr[0]).css({ width: data.width, height: data.height });
						}

						// Propagating resize, and updating values for each animation step
						that._updateCache(data);
						that._propagate("resize", event);

					}
				}
				);
			}

		});

		$.ui.plugin.add("resizable", "containment", {

			start: function () {
				var element, p, co, ch, cw, width, height,
					that = $(this).resizable("instance"),
					o = that.options,
					el = that.element,
					oc = o.containment,
					ce = (oc instanceof $) ?
						oc.get(0) :
						(/parent/.test(oc)) ? el.parent().get(0) : oc;

				if (!ce) {
					return;
				}

				that.containerElement = $(ce);

				if (/document/.test(oc) || oc === document) {
					that.containerOffset = {
						left: 0,
						top: 0
					};
					that.containerPosition = {
						left: 0,
						top: 0
					};

					that.parentData = {
						element: $(document),
						left: 0,
						top: 0,
						width: $(document).width(),
						height: $(document).height() || document.body.parentNode.scrollHeight
					};
				} else {
					element = $(ce);
					p = [];
					$(["Top", "Right", "Left", "Bottom"]).each(function (i, name) {
						p[i] = that._num(element.css("padding" + name));
					});

					that.containerOffset = element.offset();
					that.containerPosition = element.position();
					that.containerSize = {
						height: (element.innerHeight() - p[3]),
						width: (element.innerWidth() - p[1])
					};

					co = that.containerOffset;
					ch = that.containerSize.height;
					cw = that.containerSize.width;
					width = (that._hasScroll(ce, "left") ? ce.scrollWidth : cw);
					height = (that._hasScroll(ce) ? ce.scrollHeight : ch);

					that.parentData = {
						element: ce,
						left: co.left,
						top: co.top,
						width: width,
						height: height
					};
				}
			},

			resize: function (event) {
				var woset, hoset, isParent, isOffsetRelative,
					that = $(this).resizable("instance"),
					o = that.options,
					co = that.containerOffset,
					cp = that.position,
					pRatio = that._aspectRatio || event.shiftKey,
					cop = {
						top: 0,
						left: 0
					},
					ce = that.containerElement,
					continueResize = true;

				if (ce[0] !== document && (/static/).test(ce.css("position"))) {
					cop = co;
				}

				if (cp.left < (that._helper ? co.left : 0)) {
					that.size.width = that.size.width +
						(that._helper ?
							(that.position.left - co.left) :
							(that.position.left - cop.left));

					if (pRatio) {
						that.size.height = that.size.width / that.aspectRatio;
						continueResize = false;
					}
					that.position.left = o.helper ? co.left : 0;
				}

				if (cp.top < (that._helper ? co.top : 0)) {
					that.size.height = that.size.height +
						(that._helper ?
							(that.position.top - co.top) :
							that.position.top);

					if (pRatio) {
						that.size.width = that.size.height * that.aspectRatio;
						continueResize = false;
					}
					that.position.top = that._helper ? co.top : 0;
				}

				isParent = that.containerElement.get(0) === that.element.parent().get(0);
				isOffsetRelative = /relative|absolute/.test(that.containerElement.css("position"));

				if (isParent && isOffsetRelative) {
					that.offset.left = that.parentData.left + that.position.left;
					that.offset.top = that.parentData.top + that.position.top;
				} else {
					that.offset.left = that.element.offset().left;
					that.offset.top = that.element.offset().top;
				}

				woset = Math.abs(that.sizeDiff.width +
					(that._helper ?
						that.offset.left - cop.left :
						(that.offset.left - co.left)));

				hoset = Math.abs(that.sizeDiff.height +
					(that._helper ?
						that.offset.top - cop.top :
						(that.offset.top - co.top)));

				if (woset + that.size.width >= that.parentData.width) {
					that.size.width = that.parentData.width - woset;
					if (pRatio) {
						that.size.height = that.size.width / that.aspectRatio;
						continueResize = false;
					}
				}

				if (hoset + that.size.height >= that.parentData.height) {
					that.size.height = that.parentData.height - hoset;
					if (pRatio) {
						that.size.width = that.size.height * that.aspectRatio;
						continueResize = false;
					}
				}

				if (!continueResize) {
					that.position.left = that.prevPosition.left;
					that.position.top = that.prevPosition.top;
					that.size.width = that.prevSize.width;
					that.size.height = that.prevSize.height;
				}
			},

			stop: function () {
				var that = $(this).resizable("instance"),
					o = that.options,
					co = that.containerOffset,
					cop = that.containerPosition,
					ce = that.containerElement,
					helper = $(that.helper),
					ho = helper.offset(),
					w = helper.outerWidth() - that.sizeDiff.width,
					h = helper.outerHeight() - that.sizeDiff.height;

				if (that._helper && !o.animate && (/relative/).test(ce.css("position"))) {
					$(this).css({
						left: ho.left - cop.left - co.left,
						width: w,
						height: h
					});
				}

				if (that._helper && !o.animate && (/static/).test(ce.css("position"))) {
					$(this).css({
						left: ho.left - cop.left - co.left,
						width: w,
						height: h
					});
				}
			}
		});

		$.ui.plugin.add("resizable", "alsoResize", {

			start: function () {
				var that = $(this).resizable("instance"),
					o = that.options;

				$(o.alsoResize).each(function () {
					var el = $(this);
					el.data("ui-resizable-alsoresize", {
						width: parseFloat(el.width()), height: parseFloat(el.height()),
						left: parseFloat(el.css("left")), top: parseFloat(el.css("top"))
					});
				});
			},

			resize: function (event, ui) {
				var that = $(this).resizable("instance"),
					o = that.options,
					os = that.originalSize,
					op = that.originalPosition,
					delta = {
						height: (that.size.height - os.height) || 0,
						width: (that.size.width - os.width) || 0,
						top: (that.position.top - op.top) || 0,
						left: (that.position.left - op.left) || 0
					};

				$(o.alsoResize).each(function () {
					var el = $(this), start = $(this).data("ui-resizable-alsoresize"), style = {},
						css = el.parents(ui.originalElement[0]).length ?
							["width", "height"] :
							["width", "height", "top", "left"];

					$.each(css, function (i, prop) {
						var sum = (start[prop] || 0) + (delta[prop] || 0);
						if (sum && sum >= 0) {
							style[prop] = sum || null;
						}
					});

					el.css(style);
				});
			},

			stop: function () {
				$(this).removeData("ui-resizable-alsoresize");
			}
		});

		$.ui.plugin.add("resizable", "ghost", {

			start: function () {

				var that = $(this).resizable("instance"), cs = that.size;

				that.ghost = that.originalElement.clone();
				that.ghost.css({
					opacity: 0.25,
					display: "block",
					position: "relative",
					height: cs.height,
					width: cs.width,
					margin: 0,
					left: 0,
					top: 0
				});

				that._addClass(that.ghost, "ui-resizable-ghost");

				// DEPRECATED
				// TODO: remove after 1.12
				if ($.uiBackCompat !== false && typeof that.options.ghost === "string") {

					// Ghost option
					that.ghost.addClass(this.options.ghost);
				}

				that.ghost.appendTo(that.helper);

			},

			resize: function () {
				var that = $(this).resizable("instance");
				if (that.ghost) {
					that.ghost.css({
						position: "relative",
						height: that.size.height,
						width: that.size.width
					});
				}
			},

			stop: function () {
				var that = $(this).resizable("instance");
				if (that.ghost && that.helper) {
					that.helper.get(0).removeChild(that.ghost.get(0));
				}
			}

		});

		$.ui.plugin.add("resizable", "grid", {

			resize: function () {
				var outerDimensions,
					that = $(this).resizable("instance"),
					o = that.options,
					cs = that.size,
					os = that.originalSize,
					op = that.originalPosition,
					a = that.axis,
					grid = typeof o.grid === "number" ? [o.grid, o.grid] : o.grid,
					gridX = (grid[0] || 1),
					gridY = (grid[1] || 1),
					ox = Math.round((cs.width - os.width) / gridX) * gridX,
					oy = Math.round((cs.height - os.height) / gridY) * gridY,
					newWidth = os.width + ox,
					newHeight = os.height + oy,
					isMaxWidth = o.maxWidth && (o.maxWidth < newWidth),
					isMaxHeight = o.maxHeight && (o.maxHeight < newHeight),
					isMinWidth = o.minWidth && (o.minWidth > newWidth),
					isMinHeight = o.minHeight && (o.minHeight > newHeight);

				o.grid = grid;

				if (isMinWidth) {
					newWidth += gridX;
				}
				if (isMinHeight) {
					newHeight += gridY;
				}
				if (isMaxWidth) {
					newWidth -= gridX;
				}
				if (isMaxHeight) {
					newHeight -= gridY;
				}

				if (/^(se|s|e)$/.test(a)) {
					that.size.width = newWidth;
					that.size.height = newHeight;
				} else if (/^(ne)$/.test(a)) {
					that.size.width = newWidth;
					that.size.height = newHeight;
					that.position.top = op.top - oy;
				} else if (/^(sw)$/.test(a)) {
					that.size.width = newWidth;
					that.size.height = newHeight;
					that.position.left = op.left - ox;
				} else {
					if (newHeight - gridY <= 0 || newWidth - gridX <= 0) {
						outerDimensions = that._getPaddingPlusBorderDimensions(this);
					}

					if (newHeight - gridY > 0) {
						that.size.height = newHeight;
						that.position.top = op.top - oy;
					} else {
						newHeight = gridY - outerDimensions.height;
						that.size.height = newHeight;
						that.position.top = op.top + os.height - newHeight;
					}
					if (newWidth - gridX > 0) {
						that.size.width = newWidth;
						that.position.left = op.left - ox;
					} else {
						newWidth = gridX - outerDimensions.width;
						that.size.width = newWidth;
						that.position.left = op.left + os.width - newWidth;
					}
				}
			}

		});

		var widgetsResizable = $.ui.resizable;


		/*!
		 * jQuery UI Dialog 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Dialog
		//>>group: Widgets
		//>>description: Displays customizable dialog windows.
		//>>docs: http://api.jqueryui.com/dialog/
		//>>demos: http://jqueryui.com/dialog/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/dialog.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.dialog", {
			version: "1.12.1",
			options: {
				appendTo: "body",
				autoOpen: true,
				buttons: [],
				classes: {
					"ui-dialog": "ui-corner-all",
					"ui-dialog-titlebar": "ui-corner-all"
				},
				closeOnEscape: true,
				closeText: "Close",
				draggable: true,
				hide: null,
				height: "auto",
				maxHeight: null,
				maxWidth: null,
				minHeight: 150,
				minWidth: 150,
				modal: false,
				position: {
					my: "center",
					at: "center",
					of: window,
					collision: "fit",

					// Ensure the titlebar is always visible
					using: function (pos) {
						var topOffset = $(this).css(pos).offset().top;
						if (topOffset < 0) {
							$(this).css("top", pos.top - topOffset);
						}
					}
				},
				resizable: true,
				show: null,
				title: null,
				width: 300,

				// Callbacks
				beforeClose: null,
				close: null,
				drag: null,
				dragStart: null,
				dragStop: null,
				focus: null,
				open: null,
				resize: null,
				resizeStart: null,
				resizeStop: null
			},

			sizeRelatedOptions: {
				buttons: true,
				height: true,
				maxHeight: true,
				maxWidth: true,
				minHeight: true,
				minWidth: true,
				width: true
			},

			resizableRelatedOptions: {
				maxHeight: true,
				maxWidth: true,
				minHeight: true,
				minWidth: true
			},

			_create: function () {
				this.originalCss = {
					display: this.element[0].style.display,
					width: this.element[0].style.width,
					minHeight: this.element[0].style.minHeight,
					maxHeight: this.element[0].style.maxHeight,
					height: this.element[0].style.height
				};
				this.originalPosition = {
					parent: this.element.parent(),
					index: this.element.parent().children().index(this.element)
				};
				this.originalTitle = this.element.attr("title");
				if (this.options.title == null && this.originalTitle != null) {
					this.options.title = this.originalTitle;
				}

				// Dialogs can't be disabled
				if (this.options.disabled) {
					this.options.disabled = false;
				}

				this._createWrapper();

				this.element
					.show()
					.removeAttr("title")
					.appendTo(this.uiDialog);

				this._addClass("ui-dialog-content", "ui-widget-content");

				this._createTitlebar();
				this._createButtonPane();

				if (this.options.draggable && $.fn.draggable) {
					this._makeDraggable();
				}
				if (this.options.resizable && $.fn.resizable) {
					this._makeResizable();
				}

				this._isOpen = false;

				this._trackFocus();
			},

			_init: function () {
				if (this.options.autoOpen) {
					this.open();
				}
			},

			_appendTo: function () {
				var element = this.options.appendTo;
				if (element && (element.jquery || element.nodeType)) {
					return $(element);
				}
				return this.document.find(element || "body").eq(0);
			},

			_destroy: function () {
				var next,
					originalPosition = this.originalPosition;

				this._untrackInstance();
				this._destroyOverlay();

				this.element
					.removeUniqueId()
					.css(this.originalCss)

					// Without detaching first, the following becomes really slow
					.detach();

				this.uiDialog.remove();

				if (this.originalTitle) {
					this.element.attr("title", this.originalTitle);
				}

				next = originalPosition.parent.children().eq(originalPosition.index);

				// Don't try to place the dialog next to itself (#8613)
				if (next.length && next[0] !== this.element[0]) {
					next.before(this.element);
				} else {
					originalPosition.parent.append(this.element);
				}
			},

			widget: function () {
				return this.uiDialog;
			},

			disable: $.noop,
			enable: $.noop,

			close: function (event) {
				var that = this;

				if (!this._isOpen || this._trigger("beforeClose", event) === false) {
					return;
				}

				this._isOpen = false;
				this._focusedElement = null;
				this._destroyOverlay();
				this._untrackInstance();

				if (!this.opener.filter(":focusable").trigger("focus").length) {

					// Hiding a focused element doesn't trigger blur in WebKit
					// so in case we have nothing to focus on, explicitly blur the active element
					// https://bugs.webkit.org/show_bug.cgi?id=47182
					$.ui.safeBlur($.ui.safeActiveElement(this.document[0]));
				}

				this._hide(this.uiDialog, this.options.hide, function () {
					that._trigger("close", event);
				});
			},

			isOpen: function () {
				return this._isOpen;
			},

			moveToTop: function () {
				this._moveToTop();
			},

			_moveToTop: function (event, silent) {
				var moved = false,
					zIndices = this.uiDialog.siblings(".ui-front:visible").map(function () {
						return +$(this).css("z-index");
					}).get(),
					zIndexMax = Math.max.apply(null, zIndices);

				if (zIndexMax >= +this.uiDialog.css("z-index")) {
					this.uiDialog.css("z-index", zIndexMax + 1);
					moved = true;
				}

				if (moved && !silent) {
					this._trigger("focus", event);
				}
				return moved;
			},

			open: function () {
				var that = this;
				if (this._isOpen) {
					if (this._moveToTop()) {
						this._focusTabbable();
					}
					return;
				}

				this._isOpen = true;
				this.opener = $($.ui.safeActiveElement(this.document[0]));

				this._size();
				this._position();
				this._createOverlay();
				this._moveToTop(null, true);

				// Ensure the overlay is moved to the top with the dialog, but only when
				// opening. The overlay shouldn't move after the dialog is open so that
				// modeless dialogs opened after the modal dialog stack properly.
				if (this.overlay) {
					this.overlay.css("z-index", this.uiDialog.css("z-index") - 1);
				}

				this._show(this.uiDialog, this.options.show, function () {
					that._focusTabbable();
					that._trigger("focus");
				});

				// Track the dialog immediately upon openening in case a focus event
				// somehow occurs outside of the dialog before an element inside the
				// dialog is focused (#10152)
				this._makeFocusTarget();

				this._trigger("open");
			},

			_focusTabbable: function () {

				// Set focus to the first match:
				// 1. An element that was focused previously
				// 2. First element inside the dialog matching [autofocus]
				// 3. Tabbable element inside the content element
				// 4. Tabbable element inside the buttonpane
				// 5. The close button
				// 6. The dialog itself
				var hasFocus = this._focusedElement;
				if (!hasFocus) {
					hasFocus = this.element.find("[autofocus]");
				}
				if (!hasFocus.length) {
					hasFocus = this.element.find(":tabbable");
				}
				if (!hasFocus.length) {
					hasFocus = this.uiDialogButtonPane.find(":tabbable");
				}
				if (!hasFocus.length) {
					hasFocus = this.uiDialogTitlebarClose.filter(":tabbable");
				}
				if (!hasFocus.length) {
					hasFocus = this.uiDialog;
				}
				hasFocus.eq(0).trigger("focus");
			},

			_keepFocus: function (event) {
				function checkFocus() {
					var activeElement = $.ui.safeActiveElement(this.document[0]),
						isActive = this.uiDialog[0] === activeElement ||
							$.contains(this.uiDialog[0], activeElement);
					if (!isActive) {
						this._focusTabbable();
					}
				}
				event.preventDefault();
				checkFocus.call(this);

				// support: IE
				// IE <= 8 doesn't prevent moving focus even with event.preventDefault()
				// so we check again later
				this._delay(checkFocus);
			},

			_createWrapper: function () {
				this.uiDialog = $("<div>")
					.hide()
					.attr({

						// Setting tabIndex makes the div focusable
						tabIndex: -1,
						role: "dialog"
					})
					.appendTo(this._appendTo());

				this._addClass(this.uiDialog, "ui-dialog", "ui-widget ui-widget-content ui-front");
				this._on(this.uiDialog, {
					keydown: function (event) {
						if (this.options.closeOnEscape && !event.isDefaultPrevented() && event.keyCode &&
							event.keyCode === $.ui.keyCode.ESCAPE) {
							event.preventDefault();
							this.close(event);
							return;
						}

						// Prevent tabbing out of dialogs
						if (event.keyCode !== $.ui.keyCode.TAB || event.isDefaultPrevented()) {
							return;
						}
						var tabbables = this.uiDialog.find(":tabbable"),
							first = tabbables.filter(":first"),
							last = tabbables.filter(":last");

						if ((event.target === last[0] || event.target === this.uiDialog[0]) &&
							!event.shiftKey) {
							this._delay(function () {
								first.trigger("focus");
							});
							event.preventDefault();
						} else if ((event.target === first[0] ||
							event.target === this.uiDialog[0]) && event.shiftKey) {
							this._delay(function () {
								last.trigger("focus");
							});
							event.preventDefault();
						}
					},
					mousedown: function (event) {
						if (this._moveToTop(event)) {
							this._focusTabbable();
						}
					}
				});

				// We assume that any existing aria-describedby attribute means
				// that the dialog content is marked up properly
				// otherwise we brute force the content as the description
				if (!this.element.find("[aria-describedby]").length) {
					this.uiDialog.attr({
						"aria-describedby": this.element.uniqueId().attr("id")
					});
				}
			},

			_createTitlebar: function () {
				var uiDialogTitle;

				this.uiDialogTitlebar = $("<div>");
				this._addClass(this.uiDialogTitlebar,
					"ui-dialog-titlebar", "ui-widget-header ui-helper-clearfix");
				this._on(this.uiDialogTitlebar, {
					mousedown: function (event) {

						// Don't prevent click on close button (#8838)
						// Focusing a dialog that is partially scrolled out of view
						// causes the browser to scroll it into view, preventing the click event
						if (!$(event.target).closest(".ui-dialog-titlebar-close")) {

							// Dialog isn't getting focus when dragging (#8063)
							this.uiDialog.trigger("focus");
						}
					}
				});

				// Support: IE
				// Use type="button" to prevent enter keypresses in textboxes from closing the
				// dialog in IE (#9312)
				this.uiDialogTitlebarClose = $("<button type='button'></button>")
					.button({
						label: $("<a>").text(this.options.closeText).html(),
						icon: "ui-icon-closethick",
						showLabel: false
					})
					.appendTo(this.uiDialogTitlebar);

				this._addClass(this.uiDialogTitlebarClose, "ui-dialog-titlebar-close");
				this._on(this.uiDialogTitlebarClose, {
					click: function (event) {
						event.preventDefault();
						this.close(event);
					}
				});

				uiDialogTitle = $("<span>").uniqueId().prependTo(this.uiDialogTitlebar);
				this._addClass(uiDialogTitle, "ui-dialog-title");
				this._title(uiDialogTitle);

				this.uiDialogTitlebar.prependTo(this.uiDialog);

				this.uiDialog.attr({
					"aria-labelledby": uiDialogTitle.attr("id")
				});
			},

			_title: function (title) {
				if (this.options.title) {
					title.text(this.options.title);
				} else {
					title.html("&#160;");
				}
			},

			_createButtonPane: function () {
				this.uiDialogButtonPane = $("<div>");
				this._addClass(this.uiDialogButtonPane, "ui-dialog-buttonpane",
					"ui-widget-content ui-helper-clearfix");

				this.uiButtonSet = $("<div>")
					.appendTo(this.uiDialogButtonPane);
				this._addClass(this.uiButtonSet, "ui-dialog-buttonset");

				this._createButtons();
			},

			_createButtons: function () {
				var that = this,
					buttons = this.options.buttons;

				// If we already have a button pane, remove it
				this.uiDialogButtonPane.remove();
				this.uiButtonSet.empty();

				if ($.isEmptyObject(buttons) || ($.isArray(buttons) && !buttons.length)) {
					this._removeClass(this.uiDialog, "ui-dialog-buttons");
					return;
				}

				$.each(buttons, function (name, props) {
					var click, buttonOptions;
					props = $.isFunction(props) ?
						{ click: props, text: name } :
						props;

					// Default to a non-submitting button
					props = $.extend({ type: "button" }, props);

					// Change the context for the click callback to be the main element
					click = props.click;
					buttonOptions = {
						icon: props.icon,
						iconPosition: props.iconPosition,
						showLabel: props.showLabel,

						// Deprecated options
						icons: props.icons,
						text: props.text
					};

					delete props.click;
					delete props.icon;
					delete props.iconPosition;
					delete props.showLabel;

					// Deprecated options
					delete props.icons;
					if (typeof props.text === "boolean") {
						delete props.text;
					}

					$("<button></button>", props)
						.button(buttonOptions)
						.appendTo(that.uiButtonSet)
						.on("click", function () {
							click.apply(that.element[0], arguments);
						});
				});
				this._addClass(this.uiDialog, "ui-dialog-buttons");
				this.uiDialogButtonPane.appendTo(this.uiDialog);
			},

			_makeDraggable: function () {
				var that = this,
					options = this.options;

				function filteredUi(ui) {
					return {
						position: ui.position,
						offset: ui.offset
					};
				}

				this.uiDialog.draggable({
					cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
					handle: ".ui-dialog-titlebar",
					containment: "document",
					start: function (event, ui) {
						that._addClass($(this), "ui-dialog-dragging");
						that._blockFrames();
						that._trigger("dragStart", event, filteredUi(ui));
					},
					drag: function (event, ui) {
						that._trigger("drag", event, filteredUi(ui));
					},
					stop: function (event, ui) {
						var left = ui.offset.left - that.document.scrollLeft(),
							top = ui.offset.top - that.document.scrollTop();

						options.position = {
							my: "left top",
							at: "left" + (left >= 0 ? "+" : "") + left + " " +
								"top" + (top >= 0 ? "+" : "") + top,
							of: that.window
						};
						that._removeClass($(this), "ui-dialog-dragging");
						that._unblockFrames();
						that._trigger("dragStop", event, filteredUi(ui));
					}
				});
			},

			_makeResizable: function () {
				var that = this,
					options = this.options,
					handles = options.resizable,

					// .ui-resizable has position: relative defined in the stylesheet
					// but dialogs have to use absolute or fixed positioning
					position = this.uiDialog.css("position"),
					resizeHandles = typeof handles === "string" ?
						handles :
						"n,e,s,w,se,sw,ne,nw";

				function filteredUi(ui) {
					return {
						originalPosition: ui.originalPosition,
						originalSize: ui.originalSize,
						position: ui.position,
						size: ui.size
					};
				}

				this.uiDialog.resizable({
					cancel: ".ui-dialog-content",
					containment: "document",
					alsoResize: this.element,
					maxWidth: options.maxWidth,
					maxHeight: options.maxHeight,
					minWidth: options.minWidth,
					minHeight: this._minHeight(),
					handles: resizeHandles,
					start: function (event, ui) {
						that._addClass($(this), "ui-dialog-resizing");
						that._blockFrames();
						that._trigger("resizeStart", event, filteredUi(ui));
					},
					resize: function (event, ui) {
						that._trigger("resize", event, filteredUi(ui));
					},
					stop: function (event, ui) {
						var offset = that.uiDialog.offset(),
							left = offset.left - that.document.scrollLeft(),
							top = offset.top - that.document.scrollTop();

						options.height = that.uiDialog.height();
						options.width = that.uiDialog.width();
						options.position = {
							my: "left top",
							at: "left" + (left >= 0 ? "+" : "") + left + " " +
								"top" + (top >= 0 ? "+" : "") + top,
							of: that.window
						};
						that._removeClass($(this), "ui-dialog-resizing");
						that._unblockFrames();
						that._trigger("resizeStop", event, filteredUi(ui));
					}
				})
					.css("position", position);
			},

			_trackFocus: function () {
				this._on(this.widget(), {
					focusin: function (event) {
						this._makeFocusTarget();
						this._focusedElement = $(event.target);
					}
				});
			},

			_makeFocusTarget: function () {
				this._untrackInstance();
				this._trackingInstances().unshift(this);
			},

			_untrackInstance: function () {
				var instances = this._trackingInstances(),
					exists = $.inArray(this, instances);
				if (exists !== -1) {
					instances.splice(exists, 1);
				}
			},

			_trackingInstances: function () {
				var instances = this.document.data("ui-dialog-instances");
				if (!instances) {
					instances = [];
					this.document.data("ui-dialog-instances", instances);
				}
				return instances;
			},

			_minHeight: function () {
				var options = this.options;

				return options.height === "auto" ?
					options.minHeight :
					Math.min(options.minHeight, options.height);
			},

			_position: function () {

				// Need to show the dialog to get the actual offset in the position plugin
				var isVisible = this.uiDialog.is(":visible");
				if (!isVisible) {
					this.uiDialog.show();
				}
				this.uiDialog.position(this.options.position);
				if (!isVisible) {
					this.uiDialog.hide();
				}
			},

			_setOptions: function (options) {
				var that = this,
					resize = false,
					resizableOptions = {};

				$.each(options, function (key, value) {
					that._setOption(key, value);

					if (key in that.sizeRelatedOptions) {
						resize = true;
					}
					if (key in that.resizableRelatedOptions) {
						resizableOptions[key] = value;
					}
				});

				if (resize) {
					this._size();
					this._position();
				}
				if (this.uiDialog.is(":data(ui-resizable)")) {
					this.uiDialog.resizable("option", resizableOptions);
				}
			},

			_setOption: function (key, value) {
				var isDraggable, isResizable,
					uiDialog = this.uiDialog;

				if (key === "disabled") {
					return;
				}

				this._super(key, value);

				if (key === "appendTo") {
					this.uiDialog.appendTo(this._appendTo());
				}

				if (key === "buttons") {
					this._createButtons();
				}

				if (key === "closeText") {
					this.uiDialogTitlebarClose.button({

						// Ensure that we always pass a string
						label: $("<a>").text("" + this.options.closeText).html()
					});
				}

				if (key === "draggable") {
					isDraggable = uiDialog.is(":data(ui-draggable)");
					if (isDraggable && !value) {
						uiDialog.draggable("destroy");
					}

					if (!isDraggable && value) {
						this._makeDraggable();
					}
				}

				if (key === "position") {
					this._position();
				}

				if (key === "resizable") {

					// currently resizable, becoming non-resizable
					isResizable = uiDialog.is(":data(ui-resizable)");
					if (isResizable && !value) {
						uiDialog.resizable("destroy");
					}

					// Currently resizable, changing handles
					if (isResizable && typeof value === "string") {
						uiDialog.resizable("option", "handles", value);
					}

					// Currently non-resizable, becoming resizable
					if (!isResizable && value !== false) {
						this._makeResizable();
					}
				}

				if (key === "title") {
					this._title(this.uiDialogTitlebar.find(".ui-dialog-title"));
				}
			},

			_size: function () {

				// If the user has resized the dialog, the .ui-dialog and .ui-dialog-content
				// divs will both have width and height set, so we need to reset them
				var nonContentHeight, minContentHeight, maxContentHeight,
					options = this.options;

				// Reset content sizing
				this.element.show().css({
					width: "auto",
					minHeight: 0,
					maxHeight: "none",
					height: 0
				});

				if (options.minWidth > options.width) {
					options.width = options.minWidth;
				}

				// Reset wrapper sizing
				// determine the height of all the non-content elements
				nonContentHeight = this.uiDialog.css({
					height: "auto",
					width: options.width
				})
					.outerHeight();
				minContentHeight = Math.max(0, options.minHeight - nonContentHeight);
				maxContentHeight = typeof options.maxHeight === "number" ?
					Math.max(0, options.maxHeight - nonContentHeight) :
					"none";

				if (options.height === "auto") {
					this.element.css({
						minHeight: minContentHeight,
						maxHeight: maxContentHeight,
						height: "auto"
					});
				} else {
					this.element.height(Math.max(0, options.height - nonContentHeight));
				}

				if (this.uiDialog.is(":data(ui-resizable)")) {
					this.uiDialog.resizable("option", "minHeight", this._minHeight());
				}
			},

			_blockFrames: function () {
				this.iframeBlocks = this.document.find("iframe").map(function () {
					var iframe = $(this);

					return $("<div>")
						.css({
							position: "absolute",
							width: iframe.outerWidth(),
							height: iframe.outerHeight()
						})
						.appendTo(iframe.parent())
						.offset(iframe.offset())[0];
				});
			},

			_unblockFrames: function () {
				if (this.iframeBlocks) {
					this.iframeBlocks.remove();
					delete this.iframeBlocks;
				}
			},

			_allowInteraction: function (event) {
				if ($(event.target).closest(".ui-dialog").length) {
					return true;
				}

				// TODO: Remove hack when datepicker implements
				// the .ui-front logic (#8989)
				return !!$(event.target).closest(".ui-datepicker").length;
			},

			_createOverlay: function () {
				if (!this.options.modal) {
					return;
				}

				// We use a delay in case the overlay is created from an
				// event that we're going to be cancelling (#2804)
				var isOpening = true;
				this._delay(function () {
					isOpening = false;
				});

				if (!this.document.data("ui-dialog-overlays")) {

					// Prevent use of anchors and inputs
					// Using _on() for an event handler shared across many instances is
					// safe because the dialogs stack and must be closed in reverse order
					this._on(this.document, {
						focusin: function (event) {
							if (isOpening) {
								return;
							}

							if (!this._allowInteraction(event)) {
								event.preventDefault();
								this._trackingInstances()[0]._focusTabbable();
							}
						}
					});
				}

				this.overlay = $("<div>")
					.appendTo(this._appendTo());

				this._addClass(this.overlay, null, "ui-widget-overlay ui-front");
				this._on(this.overlay, {
					mousedown: "_keepFocus"
				});
				this.document.data("ui-dialog-overlays",
					(this.document.data("ui-dialog-overlays") || 0) + 1);
			},

			_destroyOverlay: function () {
				if (!this.options.modal) {
					return;
				}

				if (this.overlay) {
					var overlays = this.document.data("ui-dialog-overlays") - 1;

					if (!overlays) {
						this._off(this.document, "focusin");
						this.document.removeData("ui-dialog-overlays");
					} else {
						this.document.data("ui-dialog-overlays", overlays);
					}

					this.overlay.remove();
					this.overlay = null;
				}
			}
		});

		// DEPRECATED
		// TODO: switch return back to widget declaration at top of file when this is removed
		if ($.uiBackCompat !== false) {

			// Backcompat for dialogClass option
			$.widget("ui.dialog", $.ui.dialog, {
				options: {
					dialogClass: ""
				},
				_createWrapper: function () {
					this._super();
					this.uiDialog.addClass(this.options.dialogClass);
				},
				_setOption: function (key, value) {
					if (key === "dialogClass") {
						this.uiDialog
							.removeClass(this.options.dialogClass)
							.addClass(value);
					}
					this._superApply(arguments);
				}
			});
		}

		var widgetsDialog = $.ui.dialog;


		/*!
		 * jQuery UI Droppable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Droppable
		//>>group: Interactions
		//>>description: Enables drop targets for draggable elements.
		//>>docs: http://api.jqueryui.com/droppable/
		//>>demos: http://jqueryui.com/droppable/



		$.widget("ui.droppable", {
			version: "1.12.1",
			widgetEventPrefix: "drop",
			options: {
				accept: "*",
				addClasses: true,
				greedy: false,
				scope: "default",
				tolerance: "intersect",

				// Callbacks
				activate: null,
				deactivate: null,
				drop: null,
				out: null,
				over: null
			},
			_create: function () {

				var proportions,
					o = this.options,
					accept = o.accept;

				this.isover = false;
				this.isout = true;

				this.accept = $.isFunction(accept) ? accept : function (d) {
					return d.is(accept);
				};

				this.proportions = function ( /* valueToWrite */) {
					if (arguments.length) {

						// Store the droppable's proportions
						proportions = arguments[0];
					} else {

						// Retrieve or derive the droppable's proportions
						return proportions ?
							proportions :
							proportions = {
								width: this.element[0].offsetWidth,
								height: this.element[0].offsetHeight
							};
					}
				};

				this._addToManager(o.scope);

				o.addClasses && this._addClass("ui-droppable");

			},

			_addToManager: function (scope) {

				// Add the reference and positions to the manager
				$.ui.ddmanager.droppables[scope] = $.ui.ddmanager.droppables[scope] || [];
				$.ui.ddmanager.droppables[scope].push(this);
			},

			_splice: function (drop) {
				var i = 0;
				for (; i < drop.length; i++) {
					if (drop[i] === this) {
						drop.splice(i, 1);
					}
				}
			},

			_destroy: function () {
				var drop = $.ui.ddmanager.droppables[this.options.scope];

				this._splice(drop);
			},

			_setOption: function (key, value) {

				if (key === "accept") {
					this.accept = $.isFunction(value) ? value : function (d) {
						return d.is(value);
					};
				} else if (key === "scope") {
					var drop = $.ui.ddmanager.droppables[this.options.scope];

					this._splice(drop);
					this._addToManager(value);
				}

				this._super(key, value);
			},

			_activate: function (event) {
				var draggable = $.ui.ddmanager.current;

				this._addActiveClass();
				if (draggable) {
					this._trigger("activate", event, this.ui(draggable));
				}
			},

			_deactivate: function (event) {
				var draggable = $.ui.ddmanager.current;

				this._removeActiveClass();
				if (draggable) {
					this._trigger("deactivate", event, this.ui(draggable));
				}
			},

			_over: function (event) {

				var draggable = $.ui.ddmanager.current;

				// Bail if draggable and droppable are same element
				if (!draggable || (draggable.currentItem ||
					draggable.element)[0] === this.element[0]) {
					return;
				}

				if (this.accept.call(this.element[0], (draggable.currentItem ||
					draggable.element))) {
					this._addHoverClass();
					this._trigger("over", event, this.ui(draggable));
				}

			},

			_out: function (event) {

				var draggable = $.ui.ddmanager.current;

				// Bail if draggable and droppable are same element
				if (!draggable || (draggable.currentItem ||
					draggable.element)[0] === this.element[0]) {
					return;
				}

				if (this.accept.call(this.element[0], (draggable.currentItem ||
					draggable.element))) {
					this._removeHoverClass();
					this._trigger("out", event, this.ui(draggable));
				}

			},

			_drop: function (event, custom) {

				var draggable = custom || $.ui.ddmanager.current,
					childrenIntersection = false;

				// Bail if draggable and droppable are same element
				if (!draggable || (draggable.currentItem ||
					draggable.element)[0] === this.element[0]) {
					return false;
				}

				this.element
					.find(":data(ui-droppable)")
					.not(".ui-draggable-dragging")
					.each(function () {
						var inst = $(this).droppable("instance");
						if (
							inst.options.greedy &&
							!inst.options.disabled &&
							inst.options.scope === draggable.options.scope &&
							inst.accept.call(
								inst.element[0], (draggable.currentItem || draggable.element)
							) &&
							intersect(
								draggable,
								$.extend(inst, { offset: inst.element.offset() }),
								inst.options.tolerance, event
							)
						) {
							childrenIntersection = true;
							return false;
						}
					});
				if (childrenIntersection) {
					return false;
				}

				if (this.accept.call(this.element[0],
					(draggable.currentItem || draggable.element))) {
					this._removeActiveClass();
					this._removeHoverClass();

					this._trigger("drop", event, this.ui(draggable));
					return this.element;
				}

				return false;

			},

			ui: function (c) {
				return {
					draggable: (c.currentItem || c.element),
					helper: c.helper,
					position: c.position,
					offset: c.positionAbs
				};
			},

			// Extension points just to make backcompat sane and avoid duplicating logic
			// TODO: Remove in 1.13 along with call to it below
			_addHoverClass: function () {
				this._addClass("ui-droppable-hover");
			},

			_removeHoverClass: function () {
				this._removeClass("ui-droppable-hover");
			},

			_addActiveClass: function () {
				this._addClass("ui-droppable-active");
			},

			_removeActiveClass: function () {
				this._removeClass("ui-droppable-active");
			}
		});

		var intersect = $.ui.intersect = (function () {
			function isOverAxis(x, reference, size) {
				return (x >= reference) && (x < (reference + size));
			}

			return function (draggable, droppable, toleranceMode, event) {

				if (!droppable.offset) {
					return false;
				}

				var x1 = (draggable.positionAbs ||
					draggable.position.absolute).left + draggable.margins.left,
					y1 = (draggable.positionAbs ||
						draggable.position.absolute).top + draggable.margins.top,
					x2 = x1 + draggable.helperProportions.width,
					y2 = y1 + draggable.helperProportions.height,
					l = droppable.offset.left,
					t = droppable.offset.top,
					r = l + droppable.proportions().width,
					b = t + droppable.proportions().height;

				switch (toleranceMode) {
					case "fit":
						return (l <= x1 && x2 <= r && t <= y1 && y2 <= b);
					case "intersect":
						return (l < x1 + (draggable.helperProportions.width / 2) && // Right Half
							x2 - (draggable.helperProportions.width / 2) < r && // Left Half
							t < y1 + (draggable.helperProportions.height / 2) && // Bottom Half
							y2 - (draggable.helperProportions.height / 2) < b); // Top Half
					case "pointer":
						return isOverAxis(event.pageY, t, droppable.proportions().height) &&
							isOverAxis(event.pageX, l, droppable.proportions().width);
					case "touch":
						return (
							(y1 >= t && y1 <= b) || // Top edge touching
							(y2 >= t && y2 <= b) || // Bottom edge touching
							(y1 < t && y2 > b) // Surrounded vertically
						) && (
								(x1 >= l && x1 <= r) || // Left edge touching
								(x2 >= l && x2 <= r) || // Right edge touching
								(x1 < l && x2 > r) // Surrounded horizontally
							);
					default:
						return false;
				}
			};
		})();

		/*
			This manager tracks offsets of draggables and droppables
		*/
		$.ui.ddmanager = {
			current: null,
			droppables: { "default": [] },
			prepareOffsets: function (t, event) {

				var i, j,
					m = $.ui.ddmanager.droppables[t.options.scope] || [],
					type = event ? event.type : null, // workaround for #2317
					list = (t.currentItem || t.element).find(":data(ui-droppable)").addBack();

				droppablesLoop: for (i = 0; i < m.length; i++) {

					// No disabled and non-accepted
					if (m[i].options.disabled || (t && !m[i].accept.call(m[i].element[0],
						(t.currentItem || t.element)))) {
						continue;
					}

					// Filter out elements in the current dragged item
					for (j = 0; j < list.length; j++) {
						if (list[j] === m[i].element[0]) {
							m[i].proportions().height = 0;
							continue droppablesLoop;
						}
					}

					m[i].visible = m[i].element.css("display") !== "none";
					if (!m[i].visible) {
						continue;
					}

					// Activate the droppable if used directly from draggables
					if (type === "mousedown") {
						m[i]._activate.call(m[i], event);
					}

					m[i].offset = m[i].element.offset();
					m[i].proportions({
						width: m[i].element[0].offsetWidth,
						height: m[i].element[0].offsetHeight
					});

				}

			},
			drop: function (draggable, event) {

				var dropped = false;

				// Create a copy of the droppables in case the list changes during the drop (#9116)
				$.each(($.ui.ddmanager.droppables[draggable.options.scope] || []).slice(), function () {

					if (!this.options) {
						return;
					}
					if (!this.options.disabled && this.visible &&
						intersect(draggable, this, this.options.tolerance, event)) {
						dropped = this._drop.call(this, event) || dropped;
					}

					if (!this.options.disabled && this.visible && this.accept.call(this.element[0],
						(draggable.currentItem || draggable.element))) {
						this.isout = true;
						this.isover = false;
						this._deactivate.call(this, event);
					}

				});
				return dropped;

			},
			dragStart: function (draggable, event) {

				// Listen for scrolling so that if the dragging causes scrolling the position of the
				// droppables can be recalculated (see #5003)
				draggable.element.parentsUntil("body").on("scroll.droppable", function () {
					if (!draggable.options.refreshPositions) {
						$.ui.ddmanager.prepareOffsets(draggable, event);
					}
				});
			},
			drag: function (draggable, event) {

				// If you have a highly dynamic page, you might try this option. It renders positions
				// every time you move the mouse.
				if (draggable.options.refreshPositions) {
					$.ui.ddmanager.prepareOffsets(draggable, event);
				}

				// Run through all droppables and check their positions based on specific tolerance options
				$.each($.ui.ddmanager.droppables[draggable.options.scope] || [], function () {

					if (this.options.disabled || this.greedyChild || !this.visible) {
						return;
					}

					var parentInstance, scope, parent,
						intersects = intersect(draggable, this, this.options.tolerance, event),
						c = !intersects && this.isover ?
							"isout" :
							(intersects && !this.isover ? "isover" : null);
					if (!c) {
						return;
					}

					if (this.options.greedy) {

						// find droppable parents with same scope
						scope = this.options.scope;
						parent = this.element.parents(":data(ui-droppable)").filter(function () {
							return $(this).droppable("instance").options.scope === scope;
						});

						if (parent.length) {
							parentInstance = $(parent[0]).droppable("instance");
							parentInstance.greedyChild = (c === "isover");
						}
					}

					// We just moved into a greedy child
					if (parentInstance && c === "isover") {
						parentInstance.isover = false;
						parentInstance.isout = true;
						parentInstance._out.call(parentInstance, event);
					}

					this[c] = true;
					this[c === "isout" ? "isover" : "isout"] = false;
					this[c === "isover" ? "_over" : "_out"].call(this, event);

					// We just moved out of a greedy child
					if (parentInstance && c === "isout") {
						parentInstance.isout = false;
						parentInstance.isover = true;
						parentInstance._over.call(parentInstance, event);
					}
				});

			},
			dragStop: function (draggable, event) {
				draggable.element.parentsUntil("body").off("scroll.droppable");

				// Call prepareOffsets one final time since IE does not fire return scroll events when
				// overflow was caused by drag (see #5003)
				if (!draggable.options.refreshPositions) {
					$.ui.ddmanager.prepareOffsets(draggable, event);
				}
			}
		};

		// DEPRECATED
		// TODO: switch return back to widget declaration at top of file when this is removed
		if ($.uiBackCompat !== false) {

			// Backcompat for activeClass and hoverClass options
			$.widget("ui.droppable", $.ui.droppable, {
				options: {
					hoverClass: false,
					activeClass: false
				},
				_addActiveClass: function () {
					this._super();
					if (this.options.activeClass) {
						this.element.addClass(this.options.activeClass);
					}
				},
				_removeActiveClass: function () {
					this._super();
					if (this.options.activeClass) {
						this.element.removeClass(this.options.activeClass);
					}
				},
				_addHoverClass: function () {
					this._super();
					if (this.options.hoverClass) {
						this.element.addClass(this.options.hoverClass);
					}
				},
				_removeHoverClass: function () {
					this._super();
					if (this.options.hoverClass) {
						this.element.removeClass(this.options.hoverClass);
					}
				}
			});
		}

		var widgetsDroppable = $.ui.droppable;


		/*!
		 * jQuery UI Progressbar 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Progressbar
		//>>group: Widgets
		// jscs:disable maximumLineLength
		//>>description: Displays a status indicator for loading state, standard percentage, and other progress indicators.
		// jscs:enable maximumLineLength
		//>>docs: http://api.jqueryui.com/progressbar/
		//>>demos: http://jqueryui.com/progressbar/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/progressbar.css
		//>>css.theme: ../../themes/base/theme.css



		var widgetsProgressbar = $.widget("ui.progressbar", {
			version: "1.12.1",
			options: {
				classes: {
					"ui-progressbar": "ui-corner-all",
					"ui-progressbar-value": "ui-corner-left",
					"ui-progressbar-complete": "ui-corner-right"
				},
				max: 100,
				value: 0,

				change: null,
				complete: null
			},

			min: 0,

			_create: function () {

				// Constrain initial value
				this.oldValue = this.options.value = this._constrainedValue();

				this.element.attr({

					// Only set static values; aria-valuenow and aria-valuemax are
					// set inside _refreshValue()
					role: "progressbar",
					"aria-valuemin": this.min
				});
				this._addClass("ui-progressbar", "ui-widget ui-widget-content");

				this.valueDiv = $("<div>").appendTo(this.element);
				this._addClass(this.valueDiv, "ui-progressbar-value", "ui-widget-header");
				this._refreshValue();
			},

			_destroy: function () {
				this.element.removeAttr("role aria-valuemin aria-valuemax aria-valuenow");

				this.valueDiv.remove();
			},

			value: function (newValue) {
				if (newValue === undefined) {
					return this.options.value;
				}

				this.options.value = this._constrainedValue(newValue);
				this._refreshValue();
			},

			_constrainedValue: function (newValue) {
				if (newValue === undefined) {
					newValue = this.options.value;
				}

				this.indeterminate = newValue === false;

				// Sanitize value
				if (typeof newValue !== "number") {
					newValue = 0;
				}

				return this.indeterminate ? false :
					Math.min(this.options.max, Math.max(this.min, newValue));
			},

			_setOptions: function (options) {

				// Ensure "value" option is set after other values (like max)
				var value = options.value;
				delete options.value;

				this._super(options);

				this.options.value = this._constrainedValue(value);
				this._refreshValue();
			},

			_setOption: function (key, value) {
				if (key === "max") {

					// Don't allow a max less than min
					value = Math.max(this.min, value);
				}
				this._super(key, value);
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this.element.attr("aria-disabled", value);
				this._toggleClass(null, "ui-state-disabled", !!value);
			},

			_percentage: function () {
				return this.indeterminate ?
					100 :
					100 * (this.options.value - this.min) / (this.options.max - this.min);
			},

			_refreshValue: function () {
				var value = this.options.value,
					percentage = this._percentage();

				this.valueDiv
					.toggle(this.indeterminate || value > this.min)
					.width(percentage.toFixed(0) + "%");

				this
					._toggleClass(this.valueDiv, "ui-progressbar-complete", null,
						value === this.options.max)
					._toggleClass("ui-progressbar-indeterminate", null, this.indeterminate);

				if (this.indeterminate) {
					this.element.removeAttr("aria-valuenow");
					if (!this.overlayDiv) {
						this.overlayDiv = $("<div>").appendTo(this.valueDiv);
						this._addClass(this.overlayDiv, "ui-progressbar-overlay");
					}
				} else {
					this.element.attr({
						"aria-valuemax": this.options.max,
						"aria-valuenow": value
					});
					if (this.overlayDiv) {
						this.overlayDiv.remove();
						this.overlayDiv = null;
					}
				}

				if (this.oldValue !== value) {
					this.oldValue = value;
					this._trigger("change");
				}
				if (value === this.options.max) {
					this._trigger("complete");
				}
			}
		});


		/*!
		 * jQuery UI Selectable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Selectable
		//>>group: Interactions
		//>>description: Allows groups of elements to be selected with the mouse.
		//>>docs: http://api.jqueryui.com/selectable/
		//>>demos: http://jqueryui.com/selectable/
		//>>css.structure: ../../themes/base/selectable.css



		var widgetsSelectable = $.widget("ui.selectable", $.ui.mouse, {
			version: "1.12.1",
			options: {
				appendTo: "body",
				autoRefresh: true,
				distance: 0,
				filter: "*",
				tolerance: "touch",

				// Callbacks
				selected: null,
				selecting: null,
				start: null,
				stop: null,
				unselected: null,
				unselecting: null
			},
			_create: function () {
				var that = this;

				this._addClass("ui-selectable");

				this.dragged = false;

				// Cache selectee children based on filter
				this.refresh = function () {
					that.elementPos = $(that.element[0]).offset();
					that.selectees = $(that.options.filter, that.element[0]);
					that._addClass(that.selectees, "ui-selectee");
					that.selectees.each(function () {
						var $this = $(this),
							selecteeOffset = $this.offset(),
							pos = {
								left: selecteeOffset.left - that.elementPos.left,
								top: selecteeOffset.top - that.elementPos.top
							};
						$.data(this, "selectable-item", {
							element: this,
							$element: $this,
							left: pos.left,
							top: pos.top,
							right: pos.left + $this.outerWidth(),
							bottom: pos.top + $this.outerHeight(),
							startselected: false,
							selected: $this.hasClass("ui-selected"),
							selecting: $this.hasClass("ui-selecting"),
							unselecting: $this.hasClass("ui-unselecting")
						});
					});
				};
				this.refresh();

				this._mouseInit();

				this.helper = $("<div>");
				this._addClass(this.helper, "ui-selectable-helper");
			},

			_destroy: function () {
				this.selectees.removeData("selectable-item");
				this._mouseDestroy();
			},

			_mouseStart: function (event) {
				var that = this,
					options = this.options;

				this.opos = [event.pageX, event.pageY];
				this.elementPos = $(this.element[0]).offset();

				if (this.options.disabled) {
					return;
				}

				this.selectees = $(options.filter, this.element[0]);

				this._trigger("start", event);

				$(options.appendTo).append(this.helper);

				// position helper (lasso)
				this.helper.css({
					"left": event.pageX,
					"top": event.pageY,
					"width": 0,
					"height": 0
				});

				if (options.autoRefresh) {
					this.refresh();
				}

				this.selectees.filter(".ui-selected").each(function () {
					var selectee = $.data(this, "selectable-item");
					selectee.startselected = true;
					if (!event.metaKey && !event.ctrlKey) {
						that._removeClass(selectee.$element, "ui-selected");
						selectee.selected = false;
						that._addClass(selectee.$element, "ui-unselecting");
						selectee.unselecting = true;

						// selectable UNSELECTING callback
						that._trigger("unselecting", event, {
							unselecting: selectee.element
						});
					}
				});

				$(event.target).parents().addBack().each(function () {
					var doSelect,
						selectee = $.data(this, "selectable-item");
					if (selectee) {
						doSelect = (!event.metaKey && !event.ctrlKey) ||
							!selectee.$element.hasClass("ui-selected");
						that._removeClass(selectee.$element, doSelect ? "ui-unselecting" : "ui-selected")
							._addClass(selectee.$element, doSelect ? "ui-selecting" : "ui-unselecting");
						selectee.unselecting = !doSelect;
						selectee.selecting = doSelect;
						selectee.selected = doSelect;

						// selectable (UN)SELECTING callback
						if (doSelect) {
							that._trigger("selecting", event, {
								selecting: selectee.element
							});
						} else {
							that._trigger("unselecting", event, {
								unselecting: selectee.element
							});
						}
						return false;
					}
				});

			},

			_mouseDrag: function (event) {

				this.dragged = true;

				if (this.options.disabled) {
					return;
				}

				var tmp,
					that = this,
					options = this.options,
					x1 = this.opos[0],
					y1 = this.opos[1],
					x2 = event.pageX,
					y2 = event.pageY;

				if (x1 > x2) { tmp = x2; x2 = x1; x1 = tmp; }
				if (y1 > y2) { tmp = y2; y2 = y1; y1 = tmp; }
				this.helper.css({ left: x1, top: y1, width: x2 - x1, height: y2 - y1 });

				this.selectees.each(function () {
					var selectee = $.data(this, "selectable-item"),
						hit = false,
						offset = {};

					//prevent helper from being selected if appendTo: selectable
					if (!selectee || selectee.element === that.element[0]) {
						return;
					}

					offset.left = selectee.left + that.elementPos.left;
					offset.right = selectee.right + that.elementPos.left;
					offset.top = selectee.top + that.elementPos.top;
					offset.bottom = selectee.bottom + that.elementPos.top;

					if (options.tolerance === "touch") {
						hit = (!(offset.left > x2 || offset.right < x1 || offset.top > y2 ||
							offset.bottom < y1));
					} else if (options.tolerance === "fit") {
						hit = (offset.left > x1 && offset.right < x2 && offset.top > y1 &&
							offset.bottom < y2);
					}

					if (hit) {

						// SELECT
						if (selectee.selected) {
							that._removeClass(selectee.$element, "ui-selected");
							selectee.selected = false;
						}
						if (selectee.unselecting) {
							that._removeClass(selectee.$element, "ui-unselecting");
							selectee.unselecting = false;
						}
						if (!selectee.selecting) {
							that._addClass(selectee.$element, "ui-selecting");
							selectee.selecting = true;

							// selectable SELECTING callback
							that._trigger("selecting", event, {
								selecting: selectee.element
							});
						}
					} else {

						// UNSELECT
						if (selectee.selecting) {
							if ((event.metaKey || event.ctrlKey) && selectee.startselected) {
								that._removeClass(selectee.$element, "ui-selecting");
								selectee.selecting = false;
								that._addClass(selectee.$element, "ui-selected");
								selectee.selected = true;
							} else {
								that._removeClass(selectee.$element, "ui-selecting");
								selectee.selecting = false;
								if (selectee.startselected) {
									that._addClass(selectee.$element, "ui-unselecting");
									selectee.unselecting = true;
								}

								// selectable UNSELECTING callback
								that._trigger("unselecting", event, {
									unselecting: selectee.element
								});
							}
						}
						if (selectee.selected) {
							if (!event.metaKey && !event.ctrlKey && !selectee.startselected) {
								that._removeClass(selectee.$element, "ui-selected");
								selectee.selected = false;

								that._addClass(selectee.$element, "ui-unselecting");
								selectee.unselecting = true;

								// selectable UNSELECTING callback
								that._trigger("unselecting", event, {
									unselecting: selectee.element
								});
							}
						}
					}
				});

				return false;
			},

			_mouseStop: function (event) {
				var that = this;

				this.dragged = false;

				$(".ui-unselecting", this.element[0]).each(function () {
					var selectee = $.data(this, "selectable-item");
					that._removeClass(selectee.$element, "ui-unselecting");
					selectee.unselecting = false;
					selectee.startselected = false;
					that._trigger("unselected", event, {
						unselected: selectee.element
					});
				});
				$(".ui-selecting", this.element[0]).each(function () {
					var selectee = $.data(this, "selectable-item");
					that._removeClass(selectee.$element, "ui-selecting")
						._addClass(selectee.$element, "ui-selected");
					selectee.selecting = false;
					selectee.selected = true;
					selectee.startselected = true;
					that._trigger("selected", event, {
						selected: selectee.element
					});
				});
				this._trigger("stop", event);

				this.helper.remove();

				return false;
			}

		});


		/*!
		 * jQuery UI Selectmenu 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Selectmenu
		//>>group: Widgets
		// jscs:disable maximumLineLength
		//>>description: Duplicates and extends the functionality of a native HTML select element, allowing it to be customizable in behavior and appearance far beyond the limitations of a native select.
		// jscs:enable maximumLineLength
		//>>docs: http://api.jqueryui.com/selectmenu/
		//>>demos: http://jqueryui.com/selectmenu/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/selectmenu.css, ../../themes/base/button.css
		//>>css.theme: ../../themes/base/theme.css



		var widgetsSelectmenu = $.widget("ui.selectmenu", [$.ui.formResetMixin, {
			version: "1.12.1",
			defaultElement: "<select>",
			options: {
				appendTo: null,
				classes: {
					"ui-selectmenu-button-open": "ui-corner-top",
					"ui-selectmenu-button-closed": "ui-corner-all"
				},
				disabled: null,
				icons: {
					button: "ui-icon-triangle-1-s"
				},
				position: {
					my: "left top",
					at: "left bottom",
					collision: "none"
				},
				width: false,

				// Callbacks
				change: null,
				close: null,
				focus: null,
				open: null,
				select: null
			},

			_create: function () {
				var selectmenuId = this.element.uniqueId().attr("id");
				this.ids = {
					element: selectmenuId,
					button: selectmenuId + "-button",
					menu: selectmenuId + "-menu"
				};

				this._drawButton();
				this._drawMenu();
				this._bindFormResetHandler();

				this._rendered = false;
				this.menuItems = $();
			},

			_drawButton: function () {
				var icon,
					that = this,
					item = this._parseOption(
						this.element.find("option:selected"),
						this.element[0].selectedIndex
					);

				// Associate existing label with the new button
				this.labels = this.element.labels().attr("for", this.ids.button);
				this._on(this.labels, {
					click: function (event) {
						this.button.focus();
						event.preventDefault();
					}
				});

				// Hide original select element
				this.element.hide();

				// Create button
				this.button = $("<span>", {
					tabindex: this.options.disabled ? -1 : 0,
					id: this.ids.button,
					role: "combobox",
					"aria-expanded": "false",
					"aria-autocomplete": "list",
					"aria-owns": this.ids.menu,
					"aria-haspopup": "true",
					title: this.element.attr("title")
				})
					.insertAfter(this.element);

				this._addClass(this.button, "ui-selectmenu-button ui-selectmenu-button-closed",
					"ui-button ui-widget");

				icon = $("<span>").appendTo(this.button);
				this._addClass(icon, "ui-selectmenu-icon", "ui-icon " + this.options.icons.button);
				this.buttonItem = this._renderButtonItem(item)
					.appendTo(this.button);

				if (this.options.width !== false) {
					this._resizeButton();
				}

				this._on(this.button, this._buttonEvents);
				this.button.one("focusin", function () {

					// Delay rendering the menu items until the button receives focus.
					// The menu may have already been rendered via a programmatic open.
					if (!that._rendered) {
						that._refreshMenu();
					}
				});
			},

			_drawMenu: function () {
				var that = this;

				// Create menu
				this.menu = $("<ul>", {
					"aria-hidden": "true",
					"aria-labelledby": this.ids.button,
					id: this.ids.menu
				});

				// Wrap menu
				this.menuWrap = $("<div>").append(this.menu);
				this._addClass(this.menuWrap, "ui-selectmenu-menu", "ui-front");
				this.menuWrap.appendTo(this._appendTo());

				// Initialize menu widget
				this.menuInstance = this.menu
					.menu({
						classes: {
							"ui-menu": "ui-corner-bottom"
						},
						role: "listbox",
						select: function (event, ui) {
							event.preventDefault();

							// Support: IE8
							// If the item was selected via a click, the text selection
							// will be destroyed in IE
							that._setSelection();

							that._select(ui.item.data("ui-selectmenu-item"), event);
						},
						focus: function (event, ui) {
							var item = ui.item.data("ui-selectmenu-item");

							// Prevent inital focus from firing and check if its a newly focused item
							if (that.focusIndex != null && item.index !== that.focusIndex) {
								that._trigger("focus", event, { item: item });
								if (!that.isOpen) {
									that._select(item, event);
								}
							}
							that.focusIndex = item.index;

							that.button.attr("aria-activedescendant",
								that.menuItems.eq(item.index).attr("id"));
						}
					})
					.menu("instance");

				// Don't close the menu on mouseleave
				this.menuInstance._off(this.menu, "mouseleave");

				// Cancel the menu's collapseAll on document click
				this.menuInstance._closeOnDocumentClick = function () {
					return false;
				};

				// Selects often contain empty items, but never contain dividers
				this.menuInstance._isDivider = function () {
					return false;
				};
			},

			refresh: function () {
				this._refreshMenu();
				this.buttonItem.replaceWith(
					this.buttonItem = this._renderButtonItem(

						// Fall back to an empty object in case there are no options
						this._getSelectedItem().data("ui-selectmenu-item") || {}
					)
				);
				if (this.options.width === null) {
					this._resizeButton();
				}
			},

			_refreshMenu: function () {
				var item,
					options = this.element.find("option");

				this.menu.empty();

				this._parseOptions(options);
				this._renderMenu(this.menu, this.items);

				this.menuInstance.refresh();
				this.menuItems = this.menu.find("li")
					.not(".ui-selectmenu-optgroup")
					.find(".ui-menu-item-wrapper");

				this._rendered = true;

				if (!options.length) {
					return;
				}

				item = this._getSelectedItem();

				// Update the menu to have the correct item focused
				this.menuInstance.focus(null, item);
				this._setAria(item.data("ui-selectmenu-item"));

				// Set disabled state
				this._setOption("disabled", this.element.prop("disabled"));
			},

			open: function (event) {
				if (this.options.disabled) {
					return;
				}

				// If this is the first time the menu is being opened, render the items
				if (!this._rendered) {
					this._refreshMenu();
				} else {

					// Menu clears focus on close, reset focus to selected item
					this._removeClass(this.menu.find(".ui-state-active"), null, "ui-state-active");
					this.menuInstance.focus(null, this._getSelectedItem());
				}

				// If there are no options, don't open the menu
				if (!this.menuItems.length) {
					return;
				}

				this.isOpen = true;
				this._toggleAttr();
				this._resizeMenu();
				this._position();

				this._on(this.document, this._documentClick);

				this._trigger("open", event);
			},

			_position: function () {
				this.menuWrap.position($.extend({ of: this.button }, this.options.position));
			},

			close: function (event) {
				if (!this.isOpen) {
					return;
				}

				this.isOpen = false;
				this._toggleAttr();

				this.range = null;
				this._off(this.document);

				this._trigger("close", event);
			},

			widget: function () {
				return this.button;
			},

			menuWidget: function () {
				return this.menu;
			},

			_renderButtonItem: function (item) {
				var buttonItem = $("<span>");

				this._setText(buttonItem, item.label);
				this._addClass(buttonItem, "ui-selectmenu-text");

				return buttonItem;
			},

			_renderMenu: function (ul, items) {
				var that = this,
					currentOptgroup = "";

				$.each(items, function (index, item) {
					var li;

					if (item.optgroup !== currentOptgroup) {
						li = $("<li>", {
							text: item.optgroup
						});
						that._addClass(li, "ui-selectmenu-optgroup", "ui-menu-divider" +
							(item.element.parent("optgroup").prop("disabled") ?
								" ui-state-disabled" :
								""));

						li.appendTo(ul);

						currentOptgroup = item.optgroup;
					}

					that._renderItemData(ul, item);
				});
			},

			_renderItemData: function (ul, item) {
				return this._renderItem(ul, item).data("ui-selectmenu-item", item);
			},

			_renderItem: function (ul, item) {
				var li = $("<li>"),
					wrapper = $("<div>", {
						title: item.element.attr("title")
					});

				if (item.disabled) {
					this._addClass(li, null, "ui-state-disabled");
				}
				this._setText(wrapper, item.label);

				return li.append(wrapper).appendTo(ul);
			},

			_setText: function (element, value) {
				if (value) {
					element.text(value);
				} else {
					element.html("&#160;");
				}
			},

			_move: function (direction, event) {
				var item, next,
					filter = ".ui-menu-item";

				if (this.isOpen) {
					item = this.menuItems.eq(this.focusIndex).parent("li");
				} else {
					item = this.menuItems.eq(this.element[0].selectedIndex).parent("li");
					filter += ":not(.ui-state-disabled)";
				}

				if (direction === "first" || direction === "last") {
					next = item[direction === "first" ? "prevAll" : "nextAll"](filter).eq(-1);
				} else {
					next = item[direction + "All"](filter).eq(0);
				}

				if (next.length) {
					this.menuInstance.focus(event, next);
				}
			},

			_getSelectedItem: function () {
				return this.menuItems.eq(this.element[0].selectedIndex).parent("li");
			},

			_toggle: function (event) {
				this[this.isOpen ? "close" : "open"](event);
			},

			_setSelection: function () {
				var selection;

				if (!this.range) {
					return;
				}

				if (window.getSelection) {
					selection = window.getSelection();
					selection.removeAllRanges();
					selection.addRange(this.range);

					// Support: IE8
				} else {
					this.range.select();
				}

				// Support: IE
				// Setting the text selection kills the button focus in IE, but
				// restoring the focus doesn't kill the selection.
				this.button.focus();
			},

			_documentClick: {
				mousedown: function (event) {
					if (!this.isOpen) {
						return;
					}

					if (!$(event.target).closest(".ui-selectmenu-menu, #" +
						$.ui.escapeSelector(this.ids.button)).length) {
						this.close(event);
					}
				}
			},

			_buttonEvents: {

				// Prevent text selection from being reset when interacting with the selectmenu (#10144)
				mousedown: function () {
					var selection;

					if (window.getSelection) {
						selection = window.getSelection();
						if (selection.rangeCount) {
							this.range = selection.getRangeAt(0);
						}

						// Support: IE8
					} else {
						this.range = document.selection.createRange();
					}
				},

				click: function (event) {
					this._setSelection();
					this._toggle(event);
				},

				keydown: function (event) {
					var preventDefault = true;
					switch (event.keyCode) {
						case $.ui.keyCode.TAB:
						case $.ui.keyCode.ESCAPE:
							this.close(event);
							preventDefault = false;
							break;
						case $.ui.keyCode.ENTER:
							if (this.isOpen) {
								this._selectFocusedItem(event);
							}
							break;
						case $.ui.keyCode.UP:
							if (event.altKey) {
								this._toggle(event);
							} else {
								this._move("prev", event);
							}
							break;
						case $.ui.keyCode.DOWN:
							if (event.altKey) {
								this._toggle(event);
							} else {
								this._move("next", event);
							}
							break;
						case $.ui.keyCode.SPACE:
							if (this.isOpen) {
								this._selectFocusedItem(event);
							} else {
								this._toggle(event);
							}
							break;
						case $.ui.keyCode.LEFT:
							this._move("prev", event);
							break;
						case $.ui.keyCode.RIGHT:
							this._move("next", event);
							break;
						case $.ui.keyCode.HOME:
						case $.ui.keyCode.PAGE_UP:
							this._move("first", event);
							break;
						case $.ui.keyCode.END:
						case $.ui.keyCode.PAGE_DOWN:
							this._move("last", event);
							break;
						default:
							this.menu.trigger(event);
							preventDefault = false;
					}

					if (preventDefault) {
						event.preventDefault();
					}
				}
			},

			_selectFocusedItem: function (event) {
				var item = this.menuItems.eq(this.focusIndex).parent("li");
				if (!item.hasClass("ui-state-disabled")) {
					this._select(item.data("ui-selectmenu-item"), event);
				}
			},

			_select: function (item, event) {
				var oldIndex = this.element[0].selectedIndex;

				// Change native select element
				this.element[0].selectedIndex = item.index;
				this.buttonItem.replaceWith(this.buttonItem = this._renderButtonItem(item));
				this._setAria(item);
				this._trigger("select", event, { item: item });

				if (item.index !== oldIndex) {
					this._trigger("change", event, { item: item });
				}

				this.close(event);
			},

			_setAria: function (item) {
				var id = this.menuItems.eq(item.index).attr("id");

				this.button.attr({
					"aria-labelledby": id,
					"aria-activedescendant": id
				});
				this.menu.attr("aria-activedescendant", id);
			},

			_setOption: function (key, value) {
				if (key === "icons") {
					var icon = this.button.find("span.ui-icon");
					this._removeClass(icon, null, this.options.icons.button)
						._addClass(icon, null, value.button);
				}

				this._super(key, value);

				if (key === "appendTo") {
					this.menuWrap.appendTo(this._appendTo());
				}

				if (key === "width") {
					this._resizeButton();
				}
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this.menuInstance.option("disabled", value);
				this.button.attr("aria-disabled", value);
				this._toggleClass(this.button, null, "ui-state-disabled", value);

				this.element.prop("disabled", value);
				if (value) {
					this.button.attr("tabindex", -1);
					this.close();
				} else {
					this.button.attr("tabindex", 0);
				}
			},

			_appendTo: function () {
				var element = this.options.appendTo;

				if (element) {
					element = element.jquery || element.nodeType ?
						$(element) :
						this.document.find(element).eq(0);
				}

				if (!element || !element[0]) {
					element = this.element.closest(".ui-front, dialog");
				}

				if (!element.length) {
					element = this.document[0].body;
				}

				return element;
			},

			_toggleAttr: function () {
				this.button.attr("aria-expanded", this.isOpen);

				// We can't use two _toggleClass() calls here, because we need to make sure
				// we always remove classes first and add them second, otherwise if both classes have the
				// same theme class, it will be removed after we add it.
				this._removeClass(this.button, "ui-selectmenu-button-" +
					(this.isOpen ? "closed" : "open"))
					._addClass(this.button, "ui-selectmenu-button-" +
						(this.isOpen ? "open" : "closed"))
					._toggleClass(this.menuWrap, "ui-selectmenu-open", null, this.isOpen);

				this.menu.attr("aria-hidden", !this.isOpen);
			},

			_resizeButton: function () {
				var width = this.options.width;

				// For `width: false`, just remove inline style and stop
				if (width === false) {
					this.button.css("width", "");
					return;
				}

				// For `width: null`, match the width of the original element
				if (width === null) {
					width = this.element.show().outerWidth();
					this.element.hide();
				}

				this.button.outerWidth(width);
			},

			_resizeMenu: function () {
				this.menu.outerWidth(Math.max(
					this.button.outerWidth(),

					// Support: IE10
					// IE10 wraps long text (possibly a rounding bug)
					// so we add 1px to avoid the wrapping
					this.menu.width("").outerWidth() + 1
				));
			},

			_getCreateOptions: function () {
				var options = this._super();

				options.disabled = this.element.prop("disabled");

				return options;
			},

			_parseOptions: function (options) {
				var that = this,
					data = [];
				options.each(function (index, item) {
					data.push(that._parseOption($(item), index));
				});
				this.items = data;
			},

			_parseOption: function (option, index) {
				var optgroup = option.parent("optgroup");

				return {
					element: option,
					index: index,
					value: option.val(),
					label: option.text(),
					optgroup: optgroup.attr("label") || "",
					disabled: optgroup.prop("disabled") || option.prop("disabled")
				};
			},

			_destroy: function () {
				this._unbindFormResetHandler();
				this.menuWrap.remove();
				this.button.remove();
				this.element.show();
				this.element.removeUniqueId();
				this.labels.attr("for", this.ids.element);
			}
		}]);


		/*!
		 * jQuery UI Slider 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Slider
		//>>group: Widgets
		//>>description: Displays a flexible slider with ranges and accessibility via keyboard.
		//>>docs: http://api.jqueryui.com/slider/
		//>>demos: http://jqueryui.com/slider/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/slider.css
		//>>css.theme: ../../themes/base/theme.css



		var widgetsSlider = $.widget("ui.slider", $.ui.mouse, {
			version: "1.12.1",
			widgetEventPrefix: "slide",

			options: {
				animate: false,
				classes: {
					"ui-slider": "ui-corner-all",
					"ui-slider-handle": "ui-corner-all",

					// Note: ui-widget-header isn't the most fittingly semantic framework class for this
					// element, but worked best visually with a variety of themes
					"ui-slider-range": "ui-corner-all ui-widget-header"
				},
				distance: 0,
				max: 100,
				min: 0,
				orientation: "horizontal",
				range: false,
				step: 1,
				value: 0,
				values: null,

				// Callbacks
				change: null,
				slide: null,
				start: null,
				stop: null
			},

			// Number of pages in a slider
			// (how many times can you page up/down to go through the whole range)
			numPages: 5,

			_create: function () {
				this._keySliding = false;
				this._mouseSliding = false;
				this._animateOff = true;
				this._handleIndex = null;
				this._detectOrientation();
				this._mouseInit();
				this._calculateNewMax();

				this._addClass("ui-slider ui-slider-" + this.orientation,
					"ui-widget ui-widget-content");

				this._refresh();

				this._animateOff = false;
			},

			_refresh: function () {
				this._createRange();
				this._createHandles();
				this._setupEvents();
				this._refreshValue();
			},

			_createHandles: function () {
				var i, handleCount,
					options = this.options,
					existingHandles = this.element.find(".ui-slider-handle"),
					handle = "<span tabindex='0'></span>",
					handles = [];

				handleCount = (options.values && options.values.length) || 1;

				if (existingHandles.length > handleCount) {
					existingHandles.slice(handleCount).remove();
					existingHandles = existingHandles.slice(0, handleCount);
				}

				for (i = existingHandles.length; i < handleCount; i++) {
					handles.push(handle);
				}

				this.handles = existingHandles.add($(handles.join("")).appendTo(this.element));

				this._addClass(this.handles, "ui-slider-handle", "ui-state-default");

				this.handle = this.handles.eq(0);

				this.handles.each(function (i) {
					$(this)
						.data("ui-slider-handle-index", i)
						.attr("tabIndex", 0);
				});
			},

			_createRange: function () {
				var options = this.options;

				if (options.range) {
					if (options.range === true) {
						if (!options.values) {
							options.values = [this._valueMin(), this._valueMin()];
						} else if (options.values.length && options.values.length !== 2) {
							options.values = [options.values[0], options.values[0]];
						} else if ($.isArray(options.values)) {
							options.values = options.values.slice(0);
						}
					}

					if (!this.range || !this.range.length) {
						this.range = $("<div>")
							.appendTo(this.element);

						this._addClass(this.range, "ui-slider-range");
					} else {
						this._removeClass(this.range, "ui-slider-range-min ui-slider-range-max");

						// Handle range switching from true to min/max
						this.range.css({
							"left": "",
							"bottom": ""
						});
					}
					if (options.range === "min" || options.range === "max") {
						this._addClass(this.range, "ui-slider-range-" + options.range);
					}
				} else {
					if (this.range) {
						this.range.remove();
					}
					this.range = null;
				}
			},

			_setupEvents: function () {
				this._off(this.handles);
				this._on(this.handles, this._handleEvents);
				this._hoverable(this.handles);
				this._focusable(this.handles);
			},

			_destroy: function () {
				this.handles.remove();
				if (this.range) {
					this.range.remove();
				}

				this._mouseDestroy();
			},

			_mouseCapture: function (event) {
				var position, normValue, distance, closestHandle, index, allowed, offset, mouseOverHandle,
					that = this,
					o = this.options;

				if (o.disabled) {
					return false;
				}

				this.elementSize = {
					width: this.element.outerWidth(),
					height: this.element.outerHeight()
				};
				this.elementOffset = this.element.offset();

				position = { x: event.pageX, y: event.pageY };
				normValue = this._normValueFromMouse(position);
				distance = this._valueMax() - this._valueMin() + 1;
				this.handles.each(function (i) {
					var thisDistance = Math.abs(normValue - that.values(i));
					if ((distance > thisDistance) ||
						(distance === thisDistance &&
							(i === that._lastChangedValue || that.values(i) === o.min))) {
						distance = thisDistance;
						closestHandle = $(this);
						index = i;
					}
				});

				allowed = this._start(event, index);
				if (allowed === false) {
					return false;
				}
				this._mouseSliding = true;

				this._handleIndex = index;

				this._addClass(closestHandle, null, "ui-state-active");
				closestHandle.trigger("focus");

				offset = closestHandle.offset();
				mouseOverHandle = !$(event.target).parents().addBack().is(".ui-slider-handle");
				this._clickOffset = mouseOverHandle ? { left: 0, top: 0 } : {
					left: event.pageX - offset.left - (closestHandle.width() / 2),
					top: event.pageY - offset.top -
						(closestHandle.height() / 2) -
						(parseInt(closestHandle.css("borderTopWidth"), 10) || 0) -
						(parseInt(closestHandle.css("borderBottomWidth"), 10) || 0) +
						(parseInt(closestHandle.css("marginTop"), 10) || 0)
				};

				if (!this.handles.hasClass("ui-state-hover")) {
					this._slide(event, index, normValue);
				}
				this._animateOff = true;
				return true;
			},

			_mouseStart: function () {
				return true;
			},

			_mouseDrag: function (event) {
				var position = { x: event.pageX, y: event.pageY },
					normValue = this._normValueFromMouse(position);

				this._slide(event, this._handleIndex, normValue);

				return false;
			},

			_mouseStop: function (event) {
				this._removeClass(this.handles, null, "ui-state-active");
				this._mouseSliding = false;

				this._stop(event, this._handleIndex);
				this._change(event, this._handleIndex);

				this._handleIndex = null;
				this._clickOffset = null;
				this._animateOff = false;

				return false;
			},

			_detectOrientation: function () {
				this.orientation = (this.options.orientation === "vertical") ? "vertical" : "horizontal";
			},

			_normValueFromMouse: function (position) {
				var pixelTotal,
					pixelMouse,
					percentMouse,
					valueTotal,
					valueMouse;

				if (this.orientation === "horizontal") {
					pixelTotal = this.elementSize.width;
					pixelMouse = position.x - this.elementOffset.left -
						(this._clickOffset ? this._clickOffset.left : 0);
				} else {
					pixelTotal = this.elementSize.height;
					pixelMouse = position.y - this.elementOffset.top -
						(this._clickOffset ? this._clickOffset.top : 0);
				}

				percentMouse = (pixelMouse / pixelTotal);
				if (percentMouse > 1) {
					percentMouse = 1;
				}
				if (percentMouse < 0) {
					percentMouse = 0;
				}
				if (this.orientation === "vertical") {
					percentMouse = 1 - percentMouse;
				}

				valueTotal = this._valueMax() - this._valueMin();
				valueMouse = this._valueMin() + percentMouse * valueTotal;

				return this._trimAlignValue(valueMouse);
			},

			_uiHash: function (index, value, values) {
				var uiHash = {
					handle: this.handles[index],
					handleIndex: index,
					value: value !== undefined ? value : this.value()
				};

				if (this._hasMultipleValues()) {
					uiHash.value = value !== undefined ? value : this.values(index);
					uiHash.values = values || this.values();
				}

				return uiHash;
			},

			_hasMultipleValues: function () {
				return this.options.values && this.options.values.length;
			},

			_start: function (event, index) {
				return this._trigger("start", event, this._uiHash(index));
			},

			_slide: function (event, index, newVal) {
				var allowed, otherVal,
					currentValue = this.value(),
					newValues = this.values();

				if (this._hasMultipleValues()) {
					otherVal = this.values(index ? 0 : 1);
					currentValue = this.values(index);

					if (this.options.values.length === 2 && this.options.range === true) {
						newVal = index === 0 ? Math.min(otherVal, newVal) : Math.max(otherVal, newVal);
					}

					newValues[index] = newVal;
				}

				if (newVal === currentValue) {
					return;
				}

				allowed = this._trigger("slide", event, this._uiHash(index, newVal, newValues));

				// A slide can be canceled by returning false from the slide callback
				if (allowed === false) {
					return;
				}

				if (this._hasMultipleValues()) {
					this.values(index, newVal);
				} else {
					this.value(newVal);
				}
			},

			_stop: function (event, index) {
				this._trigger("stop", event, this._uiHash(index));
			},

			_change: function (event, index) {
				if (!this._keySliding && !this._mouseSliding) {

					//store the last changed value index for reference when handles overlap
					this._lastChangedValue = index;
					this._trigger("change", event, this._uiHash(index));
				}
			},

			value: function (newValue) {
				if (arguments.length) {
					this.options.value = this._trimAlignValue(newValue);
					this._refreshValue();
					this._change(null, 0);
					return;
				}

				return this._value();
			},

			values: function (index, newValue) {
				var vals,
					newValues,
					i;

				if (arguments.length > 1) {
					this.options.values[index] = this._trimAlignValue(newValue);
					this._refreshValue();
					this._change(null, index);
					return;
				}

				if (arguments.length) {
					if ($.isArray(arguments[0])) {
						vals = this.options.values;
						newValues = arguments[0];
						for (i = 0; i < vals.length; i += 1) {
							vals[i] = this._trimAlignValue(newValues[i]);
							this._change(null, i);
						}
						this._refreshValue();
					} else {
						if (this._hasMultipleValues()) {
							return this._values(index);
						} else {
							return this.value();
						}
					}
				} else {
					return this._values();
				}
			},

			_setOption: function (key, value) {
				var i,
					valsLength = 0;

				if (key === "range" && this.options.range === true) {
					if (value === "min") {
						this.options.value = this._values(0);
						this.options.values = null;
					} else if (value === "max") {
						this.options.value = this._values(this.options.values.length - 1);
						this.options.values = null;
					}
				}

				if ($.isArray(this.options.values)) {
					valsLength = this.options.values.length;
				}

				this._super(key, value);

				switch (key) {
					case "orientation":
						this._detectOrientation();
						this._removeClass("ui-slider-horizontal ui-slider-vertical")
							._addClass("ui-slider-" + this.orientation);
						this._refreshValue();
						if (this.options.range) {
							this._refreshRange(value);
						}

						// Reset positioning from previous orientation
						this.handles.css(value === "horizontal" ? "bottom" : "left", "");
						break;
					case "value":
						this._animateOff = true;
						this._refreshValue();
						this._change(null, 0);
						this._animateOff = false;
						break;
					case "values":
						this._animateOff = true;
						this._refreshValue();

						// Start from the last handle to prevent unreachable handles (#9046)
						for (i = valsLength - 1; i >= 0; i--) {
							this._change(null, i);
						}
						this._animateOff = false;
						break;
					case "step":
					case "min":
					case "max":
						this._animateOff = true;
						this._calculateNewMax();
						this._refreshValue();
						this._animateOff = false;
						break;
					case "range":
						this._animateOff = true;
						this._refresh();
						this._animateOff = false;
						break;
				}
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this._toggleClass(null, "ui-state-disabled", !!value);
			},

			//internal value getter
			// _value() returns value trimmed by min and max, aligned by step
			_value: function () {
				var val = this.options.value;
				val = this._trimAlignValue(val);

				return val;
			},

			//internal values getter
			// _values() returns array of values trimmed by min and max, aligned by step
			// _values( index ) returns single value trimmed by min and max, aligned by step
			_values: function (index) {
				var val,
					vals,
					i;

				if (arguments.length) {
					val = this.options.values[index];
					val = this._trimAlignValue(val);

					return val;
				} else if (this._hasMultipleValues()) {

					// .slice() creates a copy of the array
					// this copy gets trimmed by min and max and then returned
					vals = this.options.values.slice();
					for (i = 0; i < vals.length; i += 1) {
						vals[i] = this._trimAlignValue(vals[i]);
					}

					return vals;
				} else {
					return [];
				}
			},

			// Returns the step-aligned value that val is closest to, between (inclusive) min and max
			_trimAlignValue: function (val) {
				if (val <= this._valueMin()) {
					return this._valueMin();
				}
				if (val >= this._valueMax()) {
					return this._valueMax();
				}
				var step = (this.options.step > 0) ? this.options.step : 1,
					valModStep = (val - this._valueMin()) % step,
					alignValue = val - valModStep;

				if (Math.abs(valModStep) * 2 >= step) {
					alignValue += (valModStep > 0) ? step : (-step);
				}

				// Since JavaScript has problems with large floats, round
				// the final value to 5 digits after the decimal point (see #4124)
				return parseFloat(alignValue.toFixed(5));
			},

			_calculateNewMax: function () {
				var max = this.options.max,
					min = this._valueMin(),
					step = this.options.step,
					aboveMin = Math.round((max - min) / step) * step;
				max = aboveMin + min;
				if (max > this.options.max) {

					//If max is not divisible by step, rounding off may increase its value
					max -= step;
				}
				this.max = parseFloat(max.toFixed(this._precision()));
			},

			_precision: function () {
				var precision = this._precisionOf(this.options.step);
				if (this.options.min !== null) {
					precision = Math.max(precision, this._precisionOf(this.options.min));
				}
				return precision;
			},

			_precisionOf: function (num) {
				var str = num.toString(),
					decimal = str.indexOf(".");
				return decimal === -1 ? 0 : str.length - decimal - 1;
			},

			_valueMin: function () {
				return this.options.min;
			},

			_valueMax: function () {
				return this.max;
			},

			_refreshRange: function (orientation) {
				if (orientation === "vertical") {
					this.range.css({ "width": "", "left": "" });
				}
				if (orientation === "horizontal") {
					this.range.css({ "height": "", "bottom": "" });
				}
			},

			_refreshValue: function () {
				var lastValPercent, valPercent, value, valueMin, valueMax,
					oRange = this.options.range,
					o = this.options,
					that = this,
					animate = (!this._animateOff) ? o.animate : false,
					_set = {};

				if (this._hasMultipleValues()) {
					this.handles.each(function (i) {
						valPercent = (that.values(i) - that._valueMin()) / (that._valueMax() -
							that._valueMin()) * 100;
						_set[that.orientation === "horizontal" ? "left" : "bottom"] = valPercent + "%";
						$(this).stop(1, 1)[animate ? "animate" : "css"](_set, o.animate);
						if (that.options.range === true) {
							if (that.orientation === "horizontal") {
								if (i === 0) {
									that.range.stop(1, 1)[animate ? "animate" : "css"]({
										left: valPercent + "%"
									}, o.animate);
								}
								if (i === 1) {
									that.range[animate ? "animate" : "css"]({
										width: (valPercent - lastValPercent) + "%"
									}, {
										queue: false,
										duration: o.animate
									});
								}
							} else {
								if (i === 0) {
									that.range.stop(1, 1)[animate ? "animate" : "css"]({
										bottom: (valPercent) + "%"
									}, o.animate);
								}
								if (i === 1) {
									that.range[animate ? "animate" : "css"]({
										height: (valPercent - lastValPercent) + "%"
									}, {
										queue: false,
										duration: o.animate
									});
								}
							}
						}
						lastValPercent = valPercent;
					});
				} else {
					value = this.value();
					valueMin = this._valueMin();
					valueMax = this._valueMax();
					valPercent = (valueMax !== valueMin) ?
						(value - valueMin) / (valueMax - valueMin) * 100 :
						0;
					_set[this.orientation === "horizontal" ? "left" : "bottom"] = valPercent + "%";
					this.handle.stop(1, 1)[animate ? "animate" : "css"](_set, o.animate);

					if (oRange === "min" && this.orientation === "horizontal") {
						this.range.stop(1, 1)[animate ? "animate" : "css"]({
							width: valPercent + "%"
						}, o.animate);
					}
					if (oRange === "max" && this.orientation === "horizontal") {
						this.range.stop(1, 1)[animate ? "animate" : "css"]({
							width: (100 - valPercent) + "%"
						}, o.animate);
					}
					if (oRange === "min" && this.orientation === "vertical") {
						this.range.stop(1, 1)[animate ? "animate" : "css"]({
							height: valPercent + "%"
						}, o.animate);
					}
					if (oRange === "max" && this.orientation === "vertical") {
						this.range.stop(1, 1)[animate ? "animate" : "css"]({
							height: (100 - valPercent) + "%"
						}, o.animate);
					}
				}
			},

			_handleEvents: {
				keydown: function (event) {
					var allowed, curVal, newVal, step,
						index = $(event.target).data("ui-slider-handle-index");

					switch (event.keyCode) {
						case $.ui.keyCode.HOME:
						case $.ui.keyCode.END:
						case $.ui.keyCode.PAGE_UP:
						case $.ui.keyCode.PAGE_DOWN:
						case $.ui.keyCode.UP:
						case $.ui.keyCode.RIGHT:
						case $.ui.keyCode.DOWN:
						case $.ui.keyCode.LEFT:
							event.preventDefault();
							if (!this._keySliding) {
								this._keySliding = true;
								this._addClass($(event.target), null, "ui-state-active");
								allowed = this._start(event, index);
								if (allowed === false) {
									return;
								}
							}
							break;
					}

					step = this.options.step;
					if (this._hasMultipleValues()) {
						curVal = newVal = this.values(index);
					} else {
						curVal = newVal = this.value();
					}

					switch (event.keyCode) {
						case $.ui.keyCode.HOME:
							newVal = this._valueMin();
							break;
						case $.ui.keyCode.END:
							newVal = this._valueMax();
							break;
						case $.ui.keyCode.PAGE_UP:
							newVal = this._trimAlignValue(
								curVal + ((this._valueMax() - this._valueMin()) / this.numPages)
							);
							break;
						case $.ui.keyCode.PAGE_DOWN:
							newVal = this._trimAlignValue(
								curVal - ((this._valueMax() - this._valueMin()) / this.numPages));
							break;
						case $.ui.keyCode.UP:
						case $.ui.keyCode.RIGHT:
							if (curVal === this._valueMax()) {
								return;
							}
							newVal = this._trimAlignValue(curVal + step);
							break;
						case $.ui.keyCode.DOWN:
						case $.ui.keyCode.LEFT:
							if (curVal === this._valueMin()) {
								return;
							}
							newVal = this._trimAlignValue(curVal - step);
							break;
					}

					this._slide(event, index, newVal);
				},
				keyup: function (event) {
					var index = $(event.target).data("ui-slider-handle-index");

					if (this._keySliding) {
						this._keySliding = false;
						this._stop(event, index);
						this._change(event, index);
						this._removeClass($(event.target), null, "ui-state-active");
					}
				}
			}
		});


		/*!
		 * jQuery UI Sortable 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Sortable
		//>>group: Interactions
		//>>description: Enables items in a list to be sorted using the mouse.
		//>>docs: http://api.jqueryui.com/sortable/
		//>>demos: http://jqueryui.com/sortable/
		//>>css.structure: ../../themes/base/sortable.css



		var widgetsSortable = $.widget("ui.sortable", $.ui.mouse, {
			version: "1.12.1",
			widgetEventPrefix: "sort",
			ready: false,
			options: {
				appendTo: "parent",
				axis: false,
				connectWith: false,
				containment: false,
				cursor: "auto",
				cursorAt: false,
				dropOnEmpty: true,
				forcePlaceholderSize: false,
				forceHelperSize: false,
				grid: false,
				handle: false,
				helper: "original",
				items: "> *",
				opacity: false,
				placeholder: false,
				revert: false,
				scroll: true,
				scrollSensitivity: 20,
				scrollSpeed: 20,
				scope: "default",
				tolerance: "intersect",
				zIndex: 1000,

				// Callbacks
				activate: null,
				beforeStop: null,
				change: null,
				deactivate: null,
				out: null,
				over: null,
				receive: null,
				remove: null,
				sort: null,
				start: null,
				stop: null,
				update: null
			},

			_isOverAxis: function (x, reference, size) {
				return (x >= reference) && (x < (reference + size));
			},

			_isFloating: function (item) {
				return (/left|right/).test(item.css("float")) ||
					(/inline|table-cell/).test(item.css("display"));
			},

			_create: function () {
				this.containerCache = {};
				this._addClass("ui-sortable");

				//Get the items
				this.refresh();

				//Let's determine the parent's offset
				this.offset = this.element.offset();

				//Initialize mouse events for interaction
				this._mouseInit();

				this._setHandleClassName();

				//We're ready to go
				this.ready = true;

			},

			_setOption: function (key, value) {
				this._super(key, value);

				if (key === "handle") {
					this._setHandleClassName();
				}
			},

			_setHandleClassName: function () {
				var that = this;
				this._removeClass(this.element.find(".ui-sortable-handle"), "ui-sortable-handle");
				$.each(this.items, function () {
					that._addClass(
						this.instance.options.handle ?
							this.item.find(this.instance.options.handle) :
							this.item,
						"ui-sortable-handle"
					);
				});
			},

			_destroy: function () {
				this._mouseDestroy();

				for (var i = this.items.length - 1; i >= 0; i--) {
					this.items[i].item.removeData(this.widgetName + "-item");
				}

				return this;
			},

			_mouseCapture: function (event, overrideHandle) {
				var currentItem = null,
					validHandle = false,
					that = this;

				if (this.reverting) {
					return false;
				}

				if (this.options.disabled || this.options.type === "static") {
					return false;
				}

				//We have to refresh the items data once first
				this._refreshItems(event);

				//Find out if the clicked node (or one of its parents) is a actual item in this.items
				$(event.target).parents().each(function () {
					if ($.data(this, that.widgetName + "-item") === that) {
						currentItem = $(this);
						return false;
					}
				});
				if ($.data(event.target, that.widgetName + "-item") === that) {
					currentItem = $(event.target);
				}

				if (!currentItem) {
					return false;
				}
				if (this.options.handle && !overrideHandle) {
					$(this.options.handle, currentItem).find("*").addBack().each(function () {
						if (this === event.target) {
							validHandle = true;
						}
					});
					if (!validHandle) {
						return false;
					}
				}

				this.currentItem = currentItem;
				this._removeCurrentsFromItems();
				return true;

			},

			_mouseStart: function (event, overrideHandle, noActivation) {

				var i, body,
					o = this.options;

				this.currentContainer = this;

				//We only need to call refreshPositions, because the refreshItems call has been moved to
				// mouseCapture
				this.refreshPositions();

				//Create and append the visible helper
				this.helper = this._createHelper(event);

				//Cache the helper size
				this._cacheHelperProportions();

				/*
				 * - Position generation -
				 * This block generates everything position related - it's the core of draggables.
				 */

				//Cache the margins of the original element
				this._cacheMargins();

				//Get the next scrolling parent
				this.scrollParent = this.helper.scrollParent();

				//The element's absolute position on the page minus margins
				this.offset = this.currentItem.offset();
				this.offset = {
					top: this.offset.top - this.margins.top,
					left: this.offset.left - this.margins.left
				};

				$.extend(this.offset, {
					click: { //Where the click happened, relative to the element
						left: event.pageX - this.offset.left,
						top: event.pageY - this.offset.top
					},
					parent: this._getParentOffset(),

					// This is a relative to absolute position minus the actual position calculation -
					// only used for relative positioned helper
					relative: this._getRelativeOffset()
				});

				// Only after we got the offset, we can change the helper's position to absolute
				// TODO: Still need to figure out a way to make relative sorting possible
				this.helper.css("position", "absolute");
				this.cssPosition = this.helper.css("position");

				//Generate the original position
				this.originalPosition = this._generatePosition(event);
				this.originalPageX = event.pageX;
				this.originalPageY = event.pageY;

				//Adjust the mouse offset relative to the helper if "cursorAt" is supplied
				(o.cursorAt && this._adjustOffsetFromHelper(o.cursorAt));

				//Cache the former DOM position
				this.domPosition = {
					prev: this.currentItem.prev()[0],
					parent: this.currentItem.parent()[0]
				};

				// If the helper is not the original, hide the original so it's not playing any role during
				// the drag, won't cause anything bad this way
				if (this.helper[0] !== this.currentItem[0]) {
					this.currentItem.hide();
				}

				//Create the placeholder
				this._createPlaceholder();

				//Set a containment if given in the options
				if (o.containment) {
					this._setContainment();
				}

				if (o.cursor && o.cursor !== "auto") { // cursor option
					body = this.document.find("body");

					// Support: IE
					this.storedCursor = body.css("cursor");
					body.css("cursor", o.cursor);

					this.storedStylesheet =
						$("<style>*{ cursor: " + o.cursor + " !important; }</style>").appendTo(body);
				}

				if (o.opacity) { // opacity option
					if (this.helper.css("opacity")) {
						this._storedOpacity = this.helper.css("opacity");
					}
					this.helper.css("opacity", o.opacity);
				}

				if (o.zIndex) { // zIndex option
					if (this.helper.css("zIndex")) {
						this._storedZIndex = this.helper.css("zIndex");
					}
					this.helper.css("zIndex", o.zIndex);
				}

				//Prepare scrolling
				if (this.scrollParent[0] !== this.document[0] &&
					this.scrollParent[0].tagName !== "HTML") {
					this.overflowOffset = this.scrollParent.offset();
				}

				//Call callbacks
				this._trigger("start", event, this._uiHash());

				//Recache the helper size
				if (!this._preserveHelperProportions) {
					this._cacheHelperProportions();
				}

				//Post "activate" events to possible containers
				if (!noActivation) {
					for (i = this.containers.length - 1; i >= 0; i--) {
						this.containers[i]._trigger("activate", event, this._uiHash(this));
					}
				}

				//Prepare possible droppables
				if ($.ui.ddmanager) {
					$.ui.ddmanager.current = this;
				}

				if ($.ui.ddmanager && !o.dropBehaviour) {
					$.ui.ddmanager.prepareOffsets(this, event);
				}

				this.dragging = true;

				this._addClass(this.helper, "ui-sortable-helper");

				// Execute the drag once - this causes the helper not to be visiblebefore getting its
				// correct position
				this._mouseDrag(event);
				return true;

			},

			_mouseDrag: function (event) {
				var i, item, itemElement, intersection,
					o = this.options,
					scrolled = false;

				//Compute the helpers position
				this.position = this._generatePosition(event);
				this.positionAbs = this._convertPositionTo("absolute");

				if (!this.lastPositionAbs) {
					this.lastPositionAbs = this.positionAbs;
				}

				//Do scrolling
				if (this.options.scroll) {
					if (this.scrollParent[0] !== this.document[0] &&
						this.scrollParent[0].tagName !== "HTML") {

						if ((this.overflowOffset.top + this.scrollParent[0].offsetHeight) -
							event.pageY < o.scrollSensitivity) {
							this.scrollParent[0].scrollTop =
								scrolled = this.scrollParent[0].scrollTop + o.scrollSpeed;
						} else if (event.pageY - this.overflowOffset.top < o.scrollSensitivity) {
							this.scrollParent[0].scrollTop =
								scrolled = this.scrollParent[0].scrollTop - o.scrollSpeed;
						}

						if ((this.overflowOffset.left + this.scrollParent[0].offsetWidth) -
							event.pageX < o.scrollSensitivity) {
							this.scrollParent[0].scrollLeft = scrolled =
								this.scrollParent[0].scrollLeft + o.scrollSpeed;
						} else if (event.pageX - this.overflowOffset.left < o.scrollSensitivity) {
							this.scrollParent[0].scrollLeft = scrolled =
								this.scrollParent[0].scrollLeft - o.scrollSpeed;
						}

					} else {

						if (event.pageY - this.document.scrollTop() < o.scrollSensitivity) {
							scrolled = this.document.scrollTop(this.document.scrollTop() - o.scrollSpeed);
						} else if (this.window.height() - (event.pageY - this.document.scrollTop()) <
							o.scrollSensitivity) {
							scrolled = this.document.scrollTop(this.document.scrollTop() + o.scrollSpeed);
						}

						if (event.pageX - this.document.scrollLeft() < o.scrollSensitivity) {
							scrolled = this.document.scrollLeft(
								this.document.scrollLeft() - o.scrollSpeed
							);
						} else if (this.window.width() - (event.pageX - this.document.scrollLeft()) <
							o.scrollSensitivity) {
							scrolled = this.document.scrollLeft(
								this.document.scrollLeft() + o.scrollSpeed
							);
						}

					}

					if (scrolled !== false && $.ui.ddmanager && !o.dropBehaviour) {
						$.ui.ddmanager.prepareOffsets(this, event);
					}
				}

				//Regenerate the absolute position used for position checks
				this.positionAbs = this._convertPositionTo("absolute");

				//Set the helper position
				if (!this.options.axis || this.options.axis !== "y") {
					this.helper[0].style.left = this.position.left + "px";
				}
				if (!this.options.axis || this.options.axis !== "x") {
					this.helper[0].style.top = this.position.top + "px";
				}

				//Rearrange
				for (i = this.items.length - 1; i >= 0; i--) {

					//Cache variables and intersection, continue if no intersection
					item = this.items[i];
					itemElement = item.item[0];
					intersection = this._intersectsWithPointer(item);
					if (!intersection) {
						continue;
					}

					// Only put the placeholder inside the current Container, skip all
					// items from other containers. This works because when moving
					// an item from one container to another the
					// currentContainer is switched before the placeholder is moved.
					//
					// Without this, moving items in "sub-sortables" can cause
					// the placeholder to jitter between the outer and inner container.
					if (item.instance !== this.currentContainer) {
						continue;
					}

					// Cannot intersect with itself
					// no useless actions that have been done before
					// no action if the item moved is the parent of the item checked
					if (itemElement !== this.currentItem[0] &&
						this.placeholder[intersection === 1 ? "next" : "prev"]()[0] !== itemElement &&
						!$.contains(this.placeholder[0], itemElement) &&
						(this.options.type === "semi-dynamic" ?
							!$.contains(this.element[0], itemElement) :
							true
						)
					) {

						this.direction = intersection === 1 ? "down" : "up";

						if (this.options.tolerance === "pointer" || this._intersectsWithSides(item)) {
							this._rearrange(event, item);
						} else {
							break;
						}

						this._trigger("change", event, this._uiHash());
						break;
					}
				}

				//Post events to containers
				this._contactContainers(event);

				//Interconnect with droppables
				if ($.ui.ddmanager) {
					$.ui.ddmanager.drag(this, event);
				}

				//Call callbacks
				this._trigger("sort", event, this._uiHash());

				this.lastPositionAbs = this.positionAbs;
				return false;

			},

			_mouseStop: function (event, noPropagation) {

				if (!event) {
					return;
				}

				//If we are using droppables, inform the manager about the drop
				if ($.ui.ddmanager && !this.options.dropBehaviour) {
					$.ui.ddmanager.drop(this, event);
				}

				if (this.options.revert) {
					var that = this,
						cur = this.placeholder.offset(),
						axis = this.options.axis,
						animation = {};

					if (!axis || axis === "x") {
						animation.left = cur.left - this.offset.parent.left - this.margins.left +
							(this.offsetParent[0] === this.document[0].body ?
								0 :
								this.offsetParent[0].scrollLeft
							);
					}
					if (!axis || axis === "y") {
						animation.top = cur.top - this.offset.parent.top - this.margins.top +
							(this.offsetParent[0] === this.document[0].body ?
								0 :
								this.offsetParent[0].scrollTop
							);
					}
					this.reverting = true;
					$(this.helper).animate(
						animation,
						parseInt(this.options.revert, 10) || 500,
						function () {
							that._clear(event);
						}
					);
				} else {
					this._clear(event, noPropagation);
				}

				return false;

			},

			cancel: function () {

				if (this.dragging) {

					this._mouseUp(new $.Event("mouseup", { target: null }));

					if (this.options.helper === "original") {
						this.currentItem.css(this._storedCSS);
						this._removeClass(this.currentItem, "ui-sortable-helper");
					} else {
						this.currentItem.show();
					}

					//Post deactivating events to containers
					for (var i = this.containers.length - 1; i >= 0; i--) {
						this.containers[i]._trigger("deactivate", null, this._uiHash(this));
						if (this.containers[i].containerCache.over) {
							this.containers[i]._trigger("out", null, this._uiHash(this));
							this.containers[i].containerCache.over = 0;
						}
					}

				}

				if (this.placeholder) {

					//$(this.placeholder[0]).remove(); would have been the jQuery way - unfortunately,
					// it unbinds ALL events from the original node!
					if (this.placeholder[0].parentNode) {
						this.placeholder[0].parentNode.removeChild(this.placeholder[0]);
					}
					if (this.options.helper !== "original" && this.helper &&
						this.helper[0].parentNode) {
						this.helper.remove();
					}

					$.extend(this, {
						helper: null,
						dragging: false,
						reverting: false,
						_noFinalSort: null
					});

					if (this.domPosition.prev) {
						$(this.domPosition.prev).after(this.currentItem);
					} else {
						$(this.domPosition.parent).prepend(this.currentItem);
					}
				}

				return this;

			},

			serialize: function (o) {

				var items = this._getItemsAsjQuery(o && o.connected),
					str = [];
				o = o || {};

				$(items).each(function () {
					var res = ($(o.item || this).attr(o.attribute || "id") || "")
						.match(o.expression || (/(.+)[\-=_](.+)/));
					if (res) {
						str.push(
							(o.key || res[1] + "[]") +
							"=" + (o.key && o.expression ? res[1] : res[2]));
					}
				});

				if (!str.length && o.key) {
					str.push(o.key + "=");
				}

				return str.join("&");

			},

			toArray: function (o) {

				var items = this._getItemsAsjQuery(o && o.connected),
					ret = [];

				o = o || {};

				items.each(function () {
					ret.push($(o.item || this).attr(o.attribute || "id") || "");
				});
				return ret;

			},

			/* Be careful with the following core functions */
			_intersectsWith: function (item) {

				var x1 = this.positionAbs.left,
					x2 = x1 + this.helperProportions.width,
					y1 = this.positionAbs.top,
					y2 = y1 + this.helperProportions.height,
					l = item.left,
					r = l + item.width,
					t = item.top,
					b = t + item.height,
					dyClick = this.offset.click.top,
					dxClick = this.offset.click.left,
					isOverElementHeight = (this.options.axis === "x") || ((y1 + dyClick) > t &&
						(y1 + dyClick) < b),
					isOverElementWidth = (this.options.axis === "y") || ((x1 + dxClick) > l &&
						(x1 + dxClick) < r),
					isOverElement = isOverElementHeight && isOverElementWidth;

				if (this.options.tolerance === "pointer" ||
					this.options.forcePointerForContainers ||
					(this.options.tolerance !== "pointer" &&
						this.helperProportions[this.floating ? "width" : "height"] >
						item[this.floating ? "width" : "height"])
				) {
					return isOverElement;
				} else {

					return (l < x1 + (this.helperProportions.width / 2) && // Right Half
						x2 - (this.helperProportions.width / 2) < r && // Left Half
						t < y1 + (this.helperProportions.height / 2) && // Bottom Half
						y2 - (this.helperProportions.height / 2) < b); // Top Half

				}
			},

			_intersectsWithPointer: function (item) {
				var verticalDirection, horizontalDirection,
					isOverElementHeight = (this.options.axis === "x") ||
						this._isOverAxis(
							this.positionAbs.top + this.offset.click.top, item.top, item.height),
					isOverElementWidth = (this.options.axis === "y") ||
						this._isOverAxis(
							this.positionAbs.left + this.offset.click.left, item.left, item.width),
					isOverElement = isOverElementHeight && isOverElementWidth;

				if (!isOverElement) {
					return false;
				}

				verticalDirection = this._getDragVerticalDirection();
				horizontalDirection = this._getDragHorizontalDirection();

				return this.floating ?
					((horizontalDirection === "right" || verticalDirection === "down") ? 2 : 1)
					: (verticalDirection && (verticalDirection === "down" ? 2 : 1));

			},

			_intersectsWithSides: function (item) {

				var isOverBottomHalf = this._isOverAxis(this.positionAbs.top +
					this.offset.click.top, item.top + (item.height / 2), item.height),
					isOverRightHalf = this._isOverAxis(this.positionAbs.left +
						this.offset.click.left, item.left + (item.width / 2), item.width),
					verticalDirection = this._getDragVerticalDirection(),
					horizontalDirection = this._getDragHorizontalDirection();

				if (this.floating && horizontalDirection) {
					return ((horizontalDirection === "right" && isOverRightHalf) ||
						(horizontalDirection === "left" && !isOverRightHalf));
				} else {
					return verticalDirection && ((verticalDirection === "down" && isOverBottomHalf) ||
						(verticalDirection === "up" && !isOverBottomHalf));
				}

			},

			_getDragVerticalDirection: function () {
				var delta = this.positionAbs.top - this.lastPositionAbs.top;
				return delta !== 0 && (delta > 0 ? "down" : "up");
			},

			_getDragHorizontalDirection: function () {
				var delta = this.positionAbs.left - this.lastPositionAbs.left;
				return delta !== 0 && (delta > 0 ? "right" : "left");
			},

			refresh: function (event) {
				this._refreshItems(event);
				this._setHandleClassName();
				this.refreshPositions();
				return this;
			},

			_connectWith: function () {
				var options = this.options;
				return options.connectWith.constructor === String ?
					[options.connectWith] :
					options.connectWith;
			},

			_getItemsAsjQuery: function (connected) {

				var i, j, cur, inst,
					items = [],
					queries = [],
					connectWith = this._connectWith();

				if (connectWith && connected) {
					for (i = connectWith.length - 1; i >= 0; i--) {
						cur = $(connectWith[i], this.document[0]);
						for (j = cur.length - 1; j >= 0; j--) {
							inst = $.data(cur[j], this.widgetFullName);
							if (inst && inst !== this && !inst.options.disabled) {
								queries.push([$.isFunction(inst.options.items) ?
									inst.options.items.call(inst.element) :
									$(inst.options.items, inst.element)
										.not(".ui-sortable-helper")
										.not(".ui-sortable-placeholder"), inst]);
							}
						}
					}
				}

				queries.push([$.isFunction(this.options.items) ?
					this.options.items
						.call(this.element, null, { options: this.options, item: this.currentItem }) :
					$(this.options.items, this.element)
						.not(".ui-sortable-helper")
						.not(".ui-sortable-placeholder"), this]);

				function addItems() {
					items.push(this);
				}
				for (i = queries.length - 1; i >= 0; i--) {
					queries[i][0].each(addItems);
				}

				return $(items);

			},

			_removeCurrentsFromItems: function () {

				var list = this.currentItem.find(":data(" + this.widgetName + "-item)");

				this.items = $.grep(this.items, function (item) {
					for (var j = 0; j < list.length; j++) {
						if (list[j] === item.item[0]) {
							return false;
						}
					}
					return true;
				});

			},

			_refreshItems: function (event) {

				this.items = [];
				this.containers = [this];

				var i, j, cur, inst, targetData, _queries, item, queriesLength,
					items = this.items,
					queries = [[$.isFunction(this.options.items) ?
						this.options.items.call(this.element[0], event, { item: this.currentItem }) :
						$(this.options.items, this.element), this]],
					connectWith = this._connectWith();

				//Shouldn't be run the first time through due to massive slow-down
				if (connectWith && this.ready) {
					for (i = connectWith.length - 1; i >= 0; i--) {
						cur = $(connectWith[i], this.document[0]);
						for (j = cur.length - 1; j >= 0; j--) {
							inst = $.data(cur[j], this.widgetFullName);
							if (inst && inst !== this && !inst.options.disabled) {
								queries.push([$.isFunction(inst.options.items) ?
									inst.options.items
										.call(inst.element[0], event, { item: this.currentItem }) :
									$(inst.options.items, inst.element), inst]);
								this.containers.push(inst);
							}
						}
					}
				}

				for (i = queries.length - 1; i >= 0; i--) {
					targetData = queries[i][1];
					_queries = queries[i][0];

					for (j = 0, queriesLength = _queries.length; j < queriesLength; j++) {
						item = $(_queries[j]);

						// Data for target checking (mouse manager)
						item.data(this.widgetName + "-item", targetData);

						items.push({
							item: item,
							instance: targetData,
							width: 0, height: 0,
							left: 0, top: 0
						});
					}
				}

			},

			refreshPositions: function (fast) {

				// Determine whether items are being displayed horizontally
				this.floating = this.items.length ?
					this.options.axis === "x" || this._isFloating(this.items[0].item) :
					false;

				//This has to be redone because due to the item being moved out/into the offsetParent,
				// the offsetParent's position will change
				if (this.offsetParent && this.helper) {
					this.offset.parent = this._getParentOffset();
				}

				var i, item, t, p;

				for (i = this.items.length - 1; i >= 0; i--) {
					item = this.items[i];

					//We ignore calculating positions of all connected containers when we're not over them
					if (item.instance !== this.currentContainer && this.currentContainer &&
						item.item[0] !== this.currentItem[0]) {
						continue;
					}

					t = this.options.toleranceElement ?
						$(this.options.toleranceElement, item.item) :
						item.item;

					if (!fast) {
						item.width = t.outerWidth();
						item.height = t.outerHeight();
					}

					p = t.offset();
					item.left = p.left;
					item.top = p.top;
				}

				if (this.options.custom && this.options.custom.refreshContainers) {
					this.options.custom.refreshContainers.call(this);
				} else {
					for (i = this.containers.length - 1; i >= 0; i--) {
						p = this.containers[i].element.offset();
						this.containers[i].containerCache.left = p.left;
						this.containers[i].containerCache.top = p.top;
						this.containers[i].containerCache.width =
							this.containers[i].element.outerWidth();
						this.containers[i].containerCache.height =
							this.containers[i].element.outerHeight();
					}
				}

				return this;
			},

			_createPlaceholder: function (that) {
				that = that || this;
				var className,
					o = that.options;

				if (!o.placeholder || o.placeholder.constructor === String) {
					className = o.placeholder;
					o.placeholder = {
						element: function () {

							var nodeName = that.currentItem[0].nodeName.toLowerCase(),
								element = $("<" + nodeName + ">", that.document[0]);

							that._addClass(element, "ui-sortable-placeholder",
								className || that.currentItem[0].className)
								._removeClass(element, "ui-sortable-helper");

							if (nodeName === "tbody") {
								that._createTrPlaceholder(
									that.currentItem.find("tr").eq(0),
									$("<tr>", that.document[0]).appendTo(element)
								);
							} else if (nodeName === "tr") {
								that._createTrPlaceholder(that.currentItem, element);
							} else if (nodeName === "img") {
								element.attr("src", that.currentItem.attr("src"));
							}

							if (!className) {
								element.css("visibility", "hidden");
							}

							return element;
						},
						update: function (container, p) {

							// 1. If a className is set as 'placeholder option, we don't force sizes -
							// the class is responsible for that
							// 2. The option 'forcePlaceholderSize can be enabled to force it even if a
							// class name is specified
							if (className && !o.forcePlaceholderSize) {
								return;
							}

							//If the element doesn't have a actual height by itself (without styles coming
							// from a stylesheet), it receives the inline height from the dragged item
							if (!p.height()) {
								p.height(
									that.currentItem.innerHeight() -
									parseInt(that.currentItem.css("paddingTop") || 0, 10) -
									parseInt(that.currentItem.css("paddingBottom") || 0, 10));
							}
							if (!p.width()) {
								p.width(
									that.currentItem.innerWidth() -
									parseInt(that.currentItem.css("paddingLeft") || 0, 10) -
									parseInt(that.currentItem.css("paddingRight") || 0, 10));
							}
						}
					};
				}

				//Create the placeholder
				that.placeholder = $(o.placeholder.element.call(that.element, that.currentItem));

				//Append it after the actual current item
				that.currentItem.after(that.placeholder);

				//Update the size of the placeholder (TODO: Logic to fuzzy, see line 316/317)
				o.placeholder.update(that, that.placeholder);

			},

			_createTrPlaceholder: function (sourceTr, targetTr) {
				var that = this;

				sourceTr.children().each(function () {
					$("<td>&#160;</td>", that.document[0])
						.attr("colspan", $(this).attr("colspan") || 1)
						.appendTo(targetTr);
				});
			},

			_contactContainers: function (event) {
				var i, j, dist, itemWithLeastDistance, posProperty, sizeProperty, cur, nearBottom,
					floating, axis,
					innermostContainer = null,
					innermostIndex = null;

				// Get innermost container that intersects with item
				for (i = this.containers.length - 1; i >= 0; i--) {

					// Never consider a container that's located within the item itself
					if ($.contains(this.currentItem[0], this.containers[i].element[0])) {
						continue;
					}

					if (this._intersectsWith(this.containers[i].containerCache)) {

						// If we've already found a container and it's more "inner" than this, then continue
						if (innermostContainer &&
							$.contains(
								this.containers[i].element[0],
								innermostContainer.element[0])) {
							continue;
						}

						innermostContainer = this.containers[i];
						innermostIndex = i;

					} else {

						// container doesn't intersect. trigger "out" event if necessary
						if (this.containers[i].containerCache.over) {
							this.containers[i]._trigger("out", event, this._uiHash(this));
							this.containers[i].containerCache.over = 0;
						}
					}

				}

				// If no intersecting containers found, return
				if (!innermostContainer) {
					return;
				}

				// Move the item into the container if it's not there already
				if (this.containers.length === 1) {
					if (!this.containers[innermostIndex].containerCache.over) {
						this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
						this.containers[innermostIndex].containerCache.over = 1;
					}
				} else {

					// When entering a new container, we will find the item with the least distance and
					// append our item near it
					dist = 10000;
					itemWithLeastDistance = null;
					floating = innermostContainer.floating || this._isFloating(this.currentItem);
					posProperty = floating ? "left" : "top";
					sizeProperty = floating ? "width" : "height";
					axis = floating ? "pageX" : "pageY";

					for (j = this.items.length - 1; j >= 0; j--) {
						if (!$.contains(
							this.containers[innermostIndex].element[0], this.items[j].item[0])
						) {
							continue;
						}
						if (this.items[j].item[0] === this.currentItem[0]) {
							continue;
						}

						cur = this.items[j].item.offset()[posProperty];
						nearBottom = false;
						if (event[axis] - cur > this.items[j][sizeProperty] / 2) {
							nearBottom = true;
						}

						if (Math.abs(event[axis] - cur) < dist) {
							dist = Math.abs(event[axis] - cur);
							itemWithLeastDistance = this.items[j];
							this.direction = nearBottom ? "up" : "down";
						}
					}

					//Check if dropOnEmpty is enabled
					if (!itemWithLeastDistance && !this.options.dropOnEmpty) {
						return;
					}

					if (this.currentContainer === this.containers[innermostIndex]) {
						if (!this.currentContainer.containerCache.over) {
							this.containers[innermostIndex]._trigger("over", event, this._uiHash());
							this.currentContainer.containerCache.over = 1;
						}
						return;
					}

					itemWithLeastDistance ?
						this._rearrange(event, itemWithLeastDistance, null, true) :
						this._rearrange(event, null, this.containers[innermostIndex].element, true);
					this._trigger("change", event, this._uiHash());
					this.containers[innermostIndex]._trigger("change", event, this._uiHash(this));
					this.currentContainer = this.containers[innermostIndex];

					//Update the placeholder
					this.options.placeholder.update(this.currentContainer, this.placeholder);

					this.containers[innermostIndex]._trigger("over", event, this._uiHash(this));
					this.containers[innermostIndex].containerCache.over = 1;
				}

			},

			_createHelper: function (event) {

				var o = this.options,
					helper = $.isFunction(o.helper) ?
						$(o.helper.apply(this.element[0], [event, this.currentItem])) :
						(o.helper === "clone" ? this.currentItem.clone() : this.currentItem);

				//Add the helper to the DOM if that didn't happen already
				if (!helper.parents("body").length) {
					$(o.appendTo !== "parent" ?
						o.appendTo :
						this.currentItem[0].parentNode)[0].appendChild(helper[0]);
				}

				if (helper[0] === this.currentItem[0]) {
					this._storedCSS = {
						width: this.currentItem[0].style.width,
						height: this.currentItem[0].style.height,
						position: this.currentItem.css("position"),
						top: this.currentItem.css("top"),
						left: this.currentItem.css("left")
					};
				}

				if (!helper[0].style.width || o.forceHelperSize) {
					helper.width(this.currentItem.width());
				}
				if (!helper[0].style.height || o.forceHelperSize) {
					helper.height(this.currentItem.height());
				}

				return helper;

			},

			_adjustOffsetFromHelper: function (obj) {
				if (typeof obj === "string") {
					obj = obj.split(" ");
				}
				if ($.isArray(obj)) {
					obj = { left: +obj[0], top: +obj[1] || 0 };
				}
				if ("left" in obj) {
					this.offset.click.left = obj.left + this.margins.left;
				}
				if ("right" in obj) {
					this.offset.click.left = this.helperProportions.width - obj.right + this.margins.left;
				}
				if ("top" in obj) {
					this.offset.click.top = obj.top + this.margins.top;
				}
				if ("bottom" in obj) {
					this.offset.click.top = this.helperProportions.height - obj.bottom + this.margins.top;
				}
			},

			_getParentOffset: function () {

				//Get the offsetParent and cache its position
				this.offsetParent = this.helper.offsetParent();
				var po = this.offsetParent.offset();

				// This is a special case where we need to modify a offset calculated on start, since the
				// following happened:
				// 1. The position of the helper is absolute, so it's position is calculated based on the
				// next positioned parent
				// 2. The actual offset parent is a child of the scroll parent, and the scroll parent isn't
				// the document, which means that the scroll is included in the initial calculation of the
				// offset of the parent, and never recalculated upon drag
				if (this.cssPosition === "absolute" && this.scrollParent[0] !== this.document[0] &&
					$.contains(this.scrollParent[0], this.offsetParent[0])) {
					po.left += this.scrollParent.scrollLeft();
					po.top += this.scrollParent.scrollTop();
				}

				// This needs to be actually done for all browsers, since pageX/pageY includes this
				// information with an ugly IE fix
				if (this.offsetParent[0] === this.document[0].body ||
					(this.offsetParent[0].tagName &&
						this.offsetParent[0].tagName.toLowerCase() === "html" && $.ui.ie)) {
					po = { top: 0, left: 0 };
				}

				return {
					top: po.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
					left: po.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
				};

			},

			_getRelativeOffset: function () {

				if (this.cssPosition === "relative") {
					var p = this.currentItem.position();
					return {
						top: p.top - (parseInt(this.helper.css("top"), 10) || 0) +
							this.scrollParent.scrollTop(),
						left: p.left - (parseInt(this.helper.css("left"), 10) || 0) +
							this.scrollParent.scrollLeft()
					};
				} else {
					return { top: 0, left: 0 };
				}

			},

			_cacheMargins: function () {
				this.margins = {
					left: (parseInt(this.currentItem.css("marginLeft"), 10) || 0),
					top: (parseInt(this.currentItem.css("marginTop"), 10) || 0)
				};
			},

			_cacheHelperProportions: function () {
				this.helperProportions = {
					width: this.helper.outerWidth(),
					height: this.helper.outerHeight()
				};
			},

			_setContainment: function () {

				var ce, co, over,
					o = this.options;
				if (o.containment === "parent") {
					o.containment = this.helper[0].parentNode;
				}
				if (o.containment === "document" || o.containment === "window") {
					this.containment = [
						0 - this.offset.relative.left - this.offset.parent.left,
						0 - this.offset.relative.top - this.offset.parent.top,
						o.containment === "document" ?
							this.document.width() :
							this.window.width() - this.helperProportions.width - this.margins.left,
						(o.containment === "document" ?
							(this.document.height() || document.body.parentNode.scrollHeight) :
							this.window.height() || this.document[0].body.parentNode.scrollHeight
						) - this.helperProportions.height - this.margins.top
					];
				}

				if (!(/^(document|window|parent)$/).test(o.containment)) {
					ce = $(o.containment)[0];
					co = $(o.containment).offset();
					over = ($(ce).css("overflow") !== "hidden");

					this.containment = [
						co.left + (parseInt($(ce).css("borderLeftWidth"), 10) || 0) +
						(parseInt($(ce).css("paddingLeft"), 10) || 0) - this.margins.left,
						co.top + (parseInt($(ce).css("borderTopWidth"), 10) || 0) +
						(parseInt($(ce).css("paddingTop"), 10) || 0) - this.margins.top,
						co.left + (over ? Math.max(ce.scrollWidth, ce.offsetWidth) : ce.offsetWidth) -
						(parseInt($(ce).css("borderLeftWidth"), 10) || 0) -
						(parseInt($(ce).css("paddingRight"), 10) || 0) -
						this.helperProportions.width - this.margins.left,
						co.top + (over ? Math.max(ce.scrollHeight, ce.offsetHeight) : ce.offsetHeight) -
						(parseInt($(ce).css("borderTopWidth"), 10) || 0) -
						(parseInt($(ce).css("paddingBottom"), 10) || 0) -
						this.helperProportions.height - this.margins.top
					];
				}

			},

			_convertPositionTo: function (d, pos) {

				if (!pos) {
					pos = this.position;
				}
				var mod = d === "absolute" ? 1 : -1,
					scroll = this.cssPosition === "absolute" &&
						!(this.scrollParent[0] !== this.document[0] &&
							$.contains(this.scrollParent[0], this.offsetParent[0])) ?
						this.offsetParent :
						this.scrollParent,
					scrollIsRootNode = (/(html|body)/i).test(scroll[0].tagName);

				return {
					top: (

						// The absolute mouse position
						pos.top +

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.top * mod +

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.top * mod -
						((this.cssPosition === "fixed" ?
							-this.scrollParent.scrollTop() :
							(scrollIsRootNode ? 0 : scroll.scrollTop())) * mod)
					),
					left: (

						// The absolute mouse position
						pos.left +

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.left * mod +

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.left * mod -
						((this.cssPosition === "fixed" ?
							-this.scrollParent.scrollLeft() : scrollIsRootNode ? 0 :
								scroll.scrollLeft()) * mod)
					)
				};

			},

			_generatePosition: function (event) {

				var top, left,
					o = this.options,
					pageX = event.pageX,
					pageY = event.pageY,
					scroll = this.cssPosition === "absolute" &&
						!(this.scrollParent[0] !== this.document[0] &&
							$.contains(this.scrollParent[0], this.offsetParent[0])) ?
						this.offsetParent :
						this.scrollParent,
					scrollIsRootNode = (/(html|body)/i).test(scroll[0].tagName);

				// This is another very weird special case that only happens for relative elements:
				// 1. If the css position is relative
				// 2. and the scroll parent is the document or similar to the offset parent
				// we have to refresh the relative offset during the scroll so there are no jumps
				if (this.cssPosition === "relative" && !(this.scrollParent[0] !== this.document[0] &&
					this.scrollParent[0] !== this.offsetParent[0])) {
					this.offset.relative = this._getRelativeOffset();
				}

				/*
				 * - Position constraining -
				 * Constrain the position to a mix of grid, containment.
				 */

				if (this.originalPosition) { //If we are not dragging yet, we won't check for options

					if (this.containment) {
						if (event.pageX - this.offset.click.left < this.containment[0]) {
							pageX = this.containment[0] + this.offset.click.left;
						}
						if (event.pageY - this.offset.click.top < this.containment[1]) {
							pageY = this.containment[1] + this.offset.click.top;
						}
						if (event.pageX - this.offset.click.left > this.containment[2]) {
							pageX = this.containment[2] + this.offset.click.left;
						}
						if (event.pageY - this.offset.click.top > this.containment[3]) {
							pageY = this.containment[3] + this.offset.click.top;
						}
					}

					if (o.grid) {
						top = this.originalPageY + Math.round((pageY - this.originalPageY) /
							o.grid[1]) * o.grid[1];
						pageY = this.containment ?
							((top - this.offset.click.top >= this.containment[1] &&
								top - this.offset.click.top <= this.containment[3]) ?
								top :
								((top - this.offset.click.top >= this.containment[1]) ?
									top - o.grid[1] : top + o.grid[1])) :
							top;

						left = this.originalPageX + Math.round((pageX - this.originalPageX) /
							o.grid[0]) * o.grid[0];
						pageX = this.containment ?
							((left - this.offset.click.left >= this.containment[0] &&
								left - this.offset.click.left <= this.containment[2]) ?
								left :
								((left - this.offset.click.left >= this.containment[0]) ?
									left - o.grid[0] : left + o.grid[0])) :
							left;
					}

				}

				return {
					top: (

						// The absolute mouse position
						pageY -

						// Click offset (relative to the element)
						this.offset.click.top -

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.top -

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.top +
						((this.cssPosition === "fixed" ?
							-this.scrollParent.scrollTop() :
							(scrollIsRootNode ? 0 : scroll.scrollTop())))
					),
					left: (

						// The absolute mouse position
						pageX -

						// Click offset (relative to the element)
						this.offset.click.left -

						// Only for relative positioned nodes: Relative offset from element to offset parent
						this.offset.relative.left -

						// The offsetParent's offset without borders (offset + border)
						this.offset.parent.left +
						((this.cssPosition === "fixed" ?
							-this.scrollParent.scrollLeft() :
							scrollIsRootNode ? 0 : scroll.scrollLeft()))
					)
				};

			},

			_rearrange: function (event, i, a, hardRefresh) {

				a ? a[0].appendChild(this.placeholder[0]) :
					i.item[0].parentNode.insertBefore(this.placeholder[0],
						(this.direction === "down" ? i.item[0] : i.item[0].nextSibling));

				//Various things done here to improve the performance:
				// 1. we create a setTimeout, that calls refreshPositions
				// 2. on the instance, we have a counter variable, that get's higher after every append
				// 3. on the local scope, we copy the counter variable, and check in the timeout,
				// if it's still the same
				// 4. this lets only the last addition to the timeout stack through
				this.counter = this.counter ? ++this.counter : 1;
				var counter = this.counter;

				this._delay(function () {
					if (counter === this.counter) {

						//Precompute after each DOM insertion, NOT on mousemove
						this.refreshPositions(!hardRefresh);
					}
				});

			},

			_clear: function (event, noPropagation) {

				this.reverting = false;

				// We delay all events that have to be triggered to after the point where the placeholder
				// has been removed and everything else normalized again
				var i,
					delayedTriggers = [];

				// We first have to update the dom position of the actual currentItem
				// Note: don't do it if the current item is already removed (by a user), or it gets
				// reappended (see #4088)
				if (!this._noFinalSort && this.currentItem.parent().length) {
					this.placeholder.before(this.currentItem);
				}
				this._noFinalSort = null;

				if (this.helper[0] === this.currentItem[0]) {
					for (i in this._storedCSS) {
						if (this._storedCSS[i] === "auto" || this._storedCSS[i] === "static") {
							this._storedCSS[i] = "";
						}
					}
					this.currentItem.css(this._storedCSS);
					this._removeClass(this.currentItem, "ui-sortable-helper");
				} else {
					this.currentItem.show();
				}

				if (this.fromOutside && !noPropagation) {
					delayedTriggers.push(function (event) {
						this._trigger("receive", event, this._uiHash(this.fromOutside));
					});
				}
				if ((this.fromOutside ||
					this.domPosition.prev !==
					this.currentItem.prev().not(".ui-sortable-helper")[0] ||
					this.domPosition.parent !== this.currentItem.parent()[0]) && !noPropagation) {

					// Trigger update callback if the DOM position has changed
					delayedTriggers.push(function (event) {
						this._trigger("update", event, this._uiHash());
					});
				}

				// Check if the items Container has Changed and trigger appropriate
				// events.
				if (this !== this.currentContainer) {
					if (!noPropagation) {
						delayedTriggers.push(function (event) {
							this._trigger("remove", event, this._uiHash());
						});
						delayedTriggers.push((function (c) {
							return function (event) {
								c._trigger("receive", event, this._uiHash(this));
							};
						}).call(this, this.currentContainer));
						delayedTriggers.push((function (c) {
							return function (event) {
								c._trigger("update", event, this._uiHash(this));
							};
						}).call(this, this.currentContainer));
					}
				}

				//Post events to containers
				function delayEvent(type, instance, container) {
					return function (event) {
						container._trigger(type, event, instance._uiHash(instance));
					};
				}
				for (i = this.containers.length - 1; i >= 0; i--) {
					if (!noPropagation) {
						delayedTriggers.push(delayEvent("deactivate", this, this.containers[i]));
					}
					if (this.containers[i].containerCache.over) {
						delayedTriggers.push(delayEvent("out", this, this.containers[i]));
						this.containers[i].containerCache.over = 0;
					}
				}

				//Do what was originally in plugins
				if (this.storedCursor) {
					this.document.find("body").css("cursor", this.storedCursor);
					this.storedStylesheet.remove();
				}
				if (this._storedOpacity) {
					this.helper.css("opacity", this._storedOpacity);
				}
				if (this._storedZIndex) {
					this.helper.css("zIndex", this._storedZIndex === "auto" ? "" : this._storedZIndex);
				}

				this.dragging = false;

				if (!noPropagation) {
					this._trigger("beforeStop", event, this._uiHash());
				}

				//$(this.placeholder[0]).remove(); would have been the jQuery way - unfortunately,
				// it unbinds ALL events from the original node!
				this.placeholder[0].parentNode.removeChild(this.placeholder[0]);

				if (!this.cancelHelperRemoval) {
					if (this.helper[0] !== this.currentItem[0]) {
						this.helper.remove();
					}
					this.helper = null;
				}

				if (!noPropagation) {
					for (i = 0; i < delayedTriggers.length; i++) {

						// Trigger all delayed events
						delayedTriggers[i].call(this, event);
					}
					this._trigger("stop", event, this._uiHash());
				}

				this.fromOutside = false;
				return !this.cancelHelperRemoval;

			},

			_trigger: function () {
				if ($.Widget.prototype._trigger.apply(this, arguments) === false) {
					this.cancel();
				}
			},

			_uiHash: function (_inst) {
				var inst = _inst || this;
				return {
					helper: inst.helper,
					placeholder: inst.placeholder || $([]),
					position: inst.position,
					originalPosition: inst.originalPosition,
					offset: inst.positionAbs,
					item: inst.currentItem,
					sender: _inst ? _inst.element : null
				};
			}

		});


		/*!
		 * jQuery UI Spinner 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Spinner
		//>>group: Widgets
		//>>description: Displays buttons to easily input numbers via the keyboard or mouse.
		//>>docs: http://api.jqueryui.com/spinner/
		//>>demos: http://jqueryui.com/spinner/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/spinner.css
		//>>css.theme: ../../themes/base/theme.css



		function spinnerModifer(fn) {
			return function () {
				var previous = this.element.val();
				fn.apply(this, arguments);
				this._refresh();
				if (previous !== this.element.val()) {
					this._trigger("change");
				}
			};
		}

		$.widget("ui.spinner", {
			version: "1.12.1",
			defaultElement: "<input>",
			widgetEventPrefix: "spin",
			options: {
				classes: {
					"ui-spinner": "ui-corner-all",
					"ui-spinner-down": "ui-corner-br",
					"ui-spinner-up": "ui-corner-tr"
				},
				culture: null,
				icons: {
					down: "ui-icon-triangle-1-s",
					up: "ui-icon-triangle-1-n"
				},
				incremental: true,
				max: null,
				min: null,
				numberFormat: null,
				page: 10,
				step: 1,

				change: null,
				spin: null,
				start: null,
				stop: null
			},

			_create: function () {

				// handle string values that need to be parsed
				this._setOption("max", this.options.max);
				this._setOption("min", this.options.min);
				this._setOption("step", this.options.step);

				// Only format if there is a value, prevents the field from being marked
				// as invalid in Firefox, see #9573.
				if (this.value() !== "") {

					// Format the value, but don't constrain.
					this._value(this.element.val(), true);
				}

				this._draw();
				this._on(this._events);
				this._refresh();

				// Turning off autocomplete prevents the browser from remembering the
				// value when navigating through history, so we re-enable autocomplete
				// if the page is unloaded before the widget is destroyed. #7790
				this._on(this.window, {
					beforeunload: function () {
						this.element.removeAttr("autocomplete");
					}
				});
			},

			_getCreateOptions: function () {
				var options = this._super();
				var element = this.element;

				$.each(["min", "max", "step"], function (i, option) {
					var value = element.attr(option);
					if (value != null && value.length) {
						options[option] = value;
					}
				});

				return options;
			},

			_events: {
				keydown: function (event) {
					if (this._start(event) && this._keydown(event)) {
						event.preventDefault();
					}
				},
				keyup: "_stop",
				focus: function () {
					this.previous = this.element.val();
				},
				blur: function (event) {
					if (this.cancelBlur) {
						delete this.cancelBlur;
						return;
					}

					this._stop();
					this._refresh();
					if (this.previous !== this.element.val()) {
						this._trigger("change", event);
					}
				},
				mousewheel: function (event, delta) {
					if (!delta) {
						return;
					}
					if (!this.spinning && !this._start(event)) {
						return false;
					}

					this._spin((delta > 0 ? 1 : -1) * this.options.step, event);
					clearTimeout(this.mousewheelTimer);
					this.mousewheelTimer = this._delay(function () {
						if (this.spinning) {
							this._stop(event);
						}
					}, 100);
					event.preventDefault();
				},
				"mousedown .ui-spinner-button": function (event) {
					var previous;

					// We never want the buttons to have focus; whenever the user is
					// interacting with the spinner, the focus should be on the input.
					// If the input is focused then this.previous is properly set from
					// when the input first received focus. If the input is not focused
					// then we need to set this.previous based on the value before spinning.
					previous = this.element[0] === $.ui.safeActiveElement(this.document[0]) ?
						this.previous : this.element.val();
					function checkFocus() {
						var isActive = this.element[0] === $.ui.safeActiveElement(this.document[0]);
						if (!isActive) {
							this.element.trigger("focus");
							this.previous = previous;

							// support: IE
							// IE sets focus asynchronously, so we need to check if focus
							// moved off of the input because the user clicked on the button.
							this._delay(function () {
								this.previous = previous;
							});
						}
					}

					// Ensure focus is on (or stays on) the text field
					event.preventDefault();
					checkFocus.call(this);

					// Support: IE
					// IE doesn't prevent moving focus even with event.preventDefault()
					// so we set a flag to know when we should ignore the blur event
					// and check (again) if focus moved off of the input.
					this.cancelBlur = true;
					this._delay(function () {
						delete this.cancelBlur;
						checkFocus.call(this);
					});

					if (this._start(event) === false) {
						return;
					}

					this._repeat(null, $(event.currentTarget)
						.hasClass("ui-spinner-up") ? 1 : -1, event);
				},
				"mouseup .ui-spinner-button": "_stop",
				"mouseenter .ui-spinner-button": function (event) {

					// button will add ui-state-active if mouse was down while mouseleave and kept down
					if (!$(event.currentTarget).hasClass("ui-state-active")) {
						return;
					}

					if (this._start(event) === false) {
						return false;
					}
					this._repeat(null, $(event.currentTarget)
						.hasClass("ui-spinner-up") ? 1 : -1, event);
				},

				// TODO: do we really want to consider this a stop?
				// shouldn't we just stop the repeater and wait until mouseup before
				// we trigger the stop event?
				"mouseleave .ui-spinner-button": "_stop"
			},

			// Support mobile enhanced option and make backcompat more sane
			_enhance: function () {
				this.uiSpinner = this.element
					.attr("autocomplete", "off")
					.wrap("<span>")
					.parent()

					// Add buttons
					.append(
						"<a></a><a></a>"
					);
			},

			_draw: function () {
				this._enhance();

				this._addClass(this.uiSpinner, "ui-spinner", "ui-widget ui-widget-content");
				this._addClass("ui-spinner-input");

				this.element.attr("role", "spinbutton");

				// Button bindings
				this.buttons = this.uiSpinner.children("a")
					.attr("tabIndex", -1)
					.attr("aria-hidden", true)
					.button({
						classes: {
							"ui-button": ""
						}
					});

				// TODO: Right now button does not support classes this is already updated in button PR
				this._removeClass(this.buttons, "ui-corner-all");

				this._addClass(this.buttons.first(), "ui-spinner-button ui-spinner-up");
				this._addClass(this.buttons.last(), "ui-spinner-button ui-spinner-down");
				this.buttons.first().button({
					"icon": this.options.icons.up,
					"showLabel": false
				});
				this.buttons.last().button({
					"icon": this.options.icons.down,
					"showLabel": false
				});

				// IE 6 doesn't understand height: 50% for the buttons
				// unless the wrapper has an explicit height
				if (this.buttons.height() > Math.ceil(this.uiSpinner.height() * 0.5) &&
					this.uiSpinner.height() > 0) {
					this.uiSpinner.height(this.uiSpinner.height());
				}
			},

			_keydown: function (event) {
				var options = this.options,
					keyCode = $.ui.keyCode;

				switch (event.keyCode) {
					case keyCode.UP:
						this._repeat(null, 1, event);
						return true;
					case keyCode.DOWN:
						this._repeat(null, -1, event);
						return true;
					case keyCode.PAGE_UP:
						this._repeat(null, options.page, event);
						return true;
					case keyCode.PAGE_DOWN:
						this._repeat(null, -options.page, event);
						return true;
				}

				return false;
			},

			_start: function (event) {
				if (!this.spinning && this._trigger("start", event) === false) {
					return false;
				}

				if (!this.counter) {
					this.counter = 1;
				}
				this.spinning = true;
				return true;
			},

			_repeat: function (i, steps, event) {
				i = i || 500;

				clearTimeout(this.timer);
				this.timer = this._delay(function () {
					this._repeat(40, steps, event);
				}, i);

				this._spin(steps * this.options.step, event);
			},

			_spin: function (step, event) {
				var value = this.value() || 0;

				if (!this.counter) {
					this.counter = 1;
				}

				value = this._adjustValue(value + step * this._increment(this.counter));

				if (!this.spinning || this._trigger("spin", event, { value: value }) !== false) {
					this._value(value);
					this.counter++;
				}
			},

			_increment: function (i) {
				var incremental = this.options.incremental;

				if (incremental) {
					return $.isFunction(incremental) ?
						incremental(i) :
						Math.floor(i * i * i / 50000 - i * i / 500 + 17 * i / 200 + 1);
				}

				return 1;
			},

			_precision: function () {
				var precision = this._precisionOf(this.options.step);
				if (this.options.min !== null) {
					precision = Math.max(precision, this._precisionOf(this.options.min));
				}
				return precision;
			},

			_precisionOf: function (num) {
				var str = num.toString(),
					decimal = str.indexOf(".");
				return decimal === -1 ? 0 : str.length - decimal - 1;
			},

			_adjustValue: function (value) {
				var base, aboveMin,
					options = this.options;

				// Make sure we're at a valid step
				// - find out where we are relative to the base (min or 0)
				base = options.min !== null ? options.min : 0;
				aboveMin = value - base;

				// - round to the nearest step
				aboveMin = Math.round(aboveMin / options.step) * options.step;

				// - rounding is based on 0, so adjust back to our base
				value = base + aboveMin;

				// Fix precision from bad JS floating point math
				value = parseFloat(value.toFixed(this._precision()));

				// Clamp the value
				if (options.max !== null && value > options.max) {
					return options.max;
				}
				if (options.min !== null && value < options.min) {
					return options.min;
				}

				return value;
			},

			_stop: function (event) {
				if (!this.spinning) {
					return;
				}

				clearTimeout(this.timer);
				clearTimeout(this.mousewheelTimer);
				this.counter = 0;
				this.spinning = false;
				this._trigger("stop", event);
			},

			_setOption: function (key, value) {
				var prevValue, first, last;

				if (key === "culture" || key === "numberFormat") {
					prevValue = this._parse(this.element.val());
					this.options[key] = value;
					this.element.val(this._format(prevValue));
					return;
				}

				if (key === "max" || key === "min" || key === "step") {
					if (typeof value === "string") {
						value = this._parse(value);
					}
				}
				if (key === "icons") {
					first = this.buttons.first().find(".ui-icon");
					this._removeClass(first, null, this.options.icons.up);
					this._addClass(first, null, value.up);
					last = this.buttons.last().find(".ui-icon");
					this._removeClass(last, null, this.options.icons.down);
					this._addClass(last, null, value.down);
				}

				this._super(key, value);
			},

			_setOptionDisabled: function (value) {
				this._super(value);

				this._toggleClass(this.uiSpinner, null, "ui-state-disabled", !!value);
				this.element.prop("disabled", !!value);
				this.buttons.button(value ? "disable" : "enable");
			},

			_setOptions: spinnerModifer(function (options) {
				this._super(options);
			}),

			_parse: function (val) {
				if (typeof val === "string" && val !== "") {
					val = window.Globalize && this.options.numberFormat ?
						Globalize.parseFloat(val, 10, this.options.culture) : +val;
				}
				return val === "" || isNaN(val) ? null : val;
			},

			_format: function (value) {
				if (value === "") {
					return "";
				}
				return window.Globalize && this.options.numberFormat ?
					Globalize.format(value, this.options.numberFormat, this.options.culture) :
					value;
			},

			_refresh: function () {
				this.element.attr({
					"aria-valuemin": this.options.min,
					"aria-valuemax": this.options.max,

					// TODO: what should we do with values that can't be parsed?
					"aria-valuenow": this._parse(this.element.val())
				});
			},

			isValid: function () {
				var value = this.value();

				// Null is invalid
				if (value === null) {
					return false;
				}

				// If value gets adjusted, it's invalid
				return value === this._adjustValue(value);
			},

			// Update the value without triggering change
			_value: function (value, allowAny) {
				var parsed;
				if (value !== "") {
					parsed = this._parse(value);
					if (parsed !== null) {
						if (!allowAny) {
							parsed = this._adjustValue(parsed);
						}
						value = this._format(parsed);
					}
				}
				this.element.val(value);
				this._refresh();
			},

			_destroy: function () {
				this.element
					.prop("disabled", false)
					.removeAttr("autocomplete role aria-valuemin aria-valuemax aria-valuenow");

				this.uiSpinner.replaceWith(this.element);
			},

			stepUp: spinnerModifer(function (steps) {
				this._stepUp(steps);
			}),
			_stepUp: function (steps) {
				if (this._start()) {
					this._spin((steps || 1) * this.options.step);
					this._stop();
				}
			},

			stepDown: spinnerModifer(function (steps) {
				this._stepDown(steps);
			}),
			_stepDown: function (steps) {
				if (this._start()) {
					this._spin((steps || 1) * -this.options.step);
					this._stop();
				}
			},

			pageUp: spinnerModifer(function (pages) {
				this._stepUp((pages || 1) * this.options.page);
			}),

			pageDown: spinnerModifer(function (pages) {
				this._stepDown((pages || 1) * this.options.page);
			}),

			value: function (newVal) {
				if (!arguments.length) {
					return this._parse(this.element.val());
				}
				spinnerModifer(this._value).call(this, newVal);
			},

			widget: function () {
				return this.uiSpinner;
			}
		});

		// DEPRECATED
		// TODO: switch return back to widget declaration at top of file when this is removed
		if ($.uiBackCompat !== false) {

			// Backcompat for spinner html extension points
			$.widget("ui.spinner", $.ui.spinner, {
				_enhance: function () {
					this.uiSpinner = this.element
						.attr("autocomplete", "off")
						.wrap(this._uiSpinnerHtml())
						.parent()

						// Add buttons
						.append(this._buttonHtml());
				},
				_uiSpinnerHtml: function () {
					return "<span>";
				},

				_buttonHtml: function () {
					return "<a></a><a></a>";
				}
			});
		}

		var widgetsSpinner = $.ui.spinner;


		/*!
		 * jQuery UI Tabs 1.12.1
		 * http://jqueryui.com
		 *
		 * Copyright jQuery Foundation and other contributors
		 * Released under the MIT license.
		 * http://jquery.org/license
		 */

		//>>label: Tabs
		//>>group: Widgets
		//>>description: Transforms a set of container elements into a tab structure.
		//>>docs: http://api.jqueryui.com/tabs/
		//>>demos: http://jqueryui.com/tabs/
		//>>css.structure: ../../themes/base/core.css
		//>>css.structure: ../../themes/base/tabs.css
		//>>css.theme: ../../themes/base/theme.css



		$.widget("ui.tabs", {
			version: "1.12.1",
			delay: 300,
			options: {
				active: null,
				classes: {
					"ui-tabs": "ui-corner-all",
					"ui-tabs-nav": "ui-corner-all",
					"ui-tabs-panel": "ui-corner-bottom",
					"ui-tabs-tab": "ui-corner-top"
				},
				collapsible: false,
				event: "click",
				heightStyle: "content",
				hide: null,
				show: null,

				// Callbacks
				activate: null,
				beforeActivate: null,
				beforeLoad: null,
				load: null
			},

			_isLocal: (function () {
				var rhash = /#.*$/;

				return function (anchor) {
					var anchorUrl, locationUrl;

					anchorUrl = anchor.href.replace(rhash, "");
					locationUrl = location.href.replace(rhash, "");

					// Decoding may throw an error if the URL isn't UTF-8 (#9518)
					try {
						anchorUrl = decodeURIComponent(anchorUrl);
					} catch (error) { }
					try {
						locationUrl = decodeURIComponent(locationUrl);
					} catch (error) { }

					return anchor.hash.length > 1 && anchorUrl === locationUrl;
				};
			})(),

			_create: function () {
				var that = this,
					options = this.options;

				this.running = false;

				this._addClass("ui-tabs", "ui-widget ui-widget-content");
				this._toggleClass("ui-tabs-collapsible", null, options.collapsible);

				this._processTabs();
				options.active = this._initialActive();

				// Take disabling tabs via class attribute from HTML
				// into account and update option properly.
				if ($.isArray(options.disabled)) {
					options.disabled = $.unique(options.disabled.concat(
						$.map(this.tabs.filter(".ui-state-disabled"), function (li) {
							return that.tabs.index(li);
						})
					)).sort();
				}

				// Check for length avoids error when initializing empty list
				if (this.options.active !== false && this.anchors.length) {
					this.active = this._findActive(options.active);
				} else {
					this.active = $();
				}

				this._refresh();

				if (this.active.length) {
					this.load(options.active);
				}
			},

			_initialActive: function () {
				var active = this.options.active,
					collapsible = this.options.collapsible,
					locationHash = location.hash.substring(1);

				if (active === null) {

					// check the fragment identifier in the URL
					if (locationHash) {
						this.tabs.each(function (i, tab) {
							if ($(tab).attr("aria-controls") === locationHash) {
								active = i;
								return false;
							}
						});
					}

					// Check for a tab marked active via a class
					if (active === null) {
						active = this.tabs.index(this.tabs.filter(".ui-tabs-active"));
					}

					// No active tab, set to false
					if (active === null || active === -1) {
						active = this.tabs.length ? 0 : false;
					}
				}

				// Handle numbers: negative, out of range
				if (active !== false) {
					active = this.tabs.index(this.tabs.eq(active));
					if (active === -1) {
						active = collapsible ? false : 0;
					}
				}

				// Don't allow collapsible: false and active: false
				if (!collapsible && active === false && this.anchors.length) {
					active = 0;
				}

				return active;
			},

			_getCreateEventData: function () {
				return {
					tab: this.active,
					panel: !this.active.length ? $() : this._getPanelForTab(this.active)
				};
			},

			_tabKeydown: function (event) {
				var focusedTab = $($.ui.safeActiveElement(this.document[0])).closest("li"),
					selectedIndex = this.tabs.index(focusedTab),
					goingForward = true;

				if (this._handlePageNav(event)) {
					return;
				}

				switch (event.keyCode) {
					case $.ui.keyCode.RIGHT:
					case $.ui.keyCode.DOWN:
						selectedIndex++;
						break;
					case $.ui.keyCode.UP:
					case $.ui.keyCode.LEFT:
						goingForward = false;
						selectedIndex--;
						break;
					case $.ui.keyCode.END:
						selectedIndex = this.anchors.length - 1;
						break;
					case $.ui.keyCode.HOME:
						selectedIndex = 0;
						break;
					case $.ui.keyCode.SPACE:

						// Activate only, no collapsing
						event.preventDefault();
						clearTimeout(this.activating);
						this._activate(selectedIndex);
						return;
					case $.ui.keyCode.ENTER:

						// Toggle (cancel delayed activation, allow collapsing)
						event.preventDefault();
						clearTimeout(this.activating);

						// Determine if we should collapse or activate
						this._activate(selectedIndex === this.options.active ? false : selectedIndex);
						return;
					default:
						return;
				}

				// Focus the appropriate tab, based on which key was pressed
				event.preventDefault();
				clearTimeout(this.activating);
				selectedIndex = this._focusNextTab(selectedIndex, goingForward);

				// Navigating with control/command key will prevent automatic activation
				if (!event.ctrlKey && !event.metaKey) {

					// Update aria-selected immediately so that AT think the tab is already selected.
					// Otherwise AT may confuse the user by stating that they need to activate the tab,
					// but the tab will already be activated by the time the announcement finishes.
					focusedTab.attr("aria-selected", "false");
					this.tabs.eq(selectedIndex).attr("aria-selected", "true");

					this.activating = this._delay(function () {
						this.option("active", selectedIndex);
					}, this.delay);
				}
			},

			_panelKeydown: function (event) {
				if (this._handlePageNav(event)) {
					return;
				}

				// Ctrl+up moves focus to the current tab
				if (event.ctrlKey && event.keyCode === $.ui.keyCode.UP) {
					event.preventDefault();
					this.active.trigger("focus");
				}
			},

			// Alt+page up/down moves focus to the previous/next tab (and activates)
			_handlePageNav: function (event) {
				if (event.altKey && event.keyCode === $.ui.keyCode.PAGE_UP) {
					this._activate(this._focusNextTab(this.options.active - 1, false));
					return true;
				}
				if (event.altKey && event.keyCode === $.ui.keyCode.PAGE_DOWN) {
					this._activate(this._focusNextTab(this.options.active + 1, true));
					return true;
				}
			},

			_findNextTab: function (index, goingForward) {
				var lastTabIndex = this.tabs.length - 1;

				function constrain() {
					if (index > lastTabIndex) {
						index = 0;
					}
					if (index < 0) {
						index = lastTabIndex;
					}
					return index;
				}

				while ($.inArray(constrain(), this.options.disabled) !== -1) {
					index = goingForward ? index + 1 : index - 1;
				}

				return index;
			},

			_focusNextTab: function (index, goingForward) {
				index = this._findNextTab(index, goingForward);
				this.tabs.eq(index).trigger("focus");
				return index;
			},

			_setOption: function (key, value) {
				if (key === "active") {

					// _activate() will handle invalid values and update this.options
					this._activate(value);
					return;
				}

				this._super(key, value);

				if (key === "collapsible") {
					this._toggleClass("ui-tabs-collapsible", null, value);

					// Setting collapsible: false while collapsed; open first panel
					if (!value && this.options.active === false) {
						this._activate(0);
					}
				}

				if (key === "event") {
					this._setupEvents(value);
				}

				if (key === "heightStyle") {
					this._setupHeightStyle(value);
				}
			},

			_sanitizeSelector: function (hash) {
				return hash ? hash.replace(/[!"$%&'()*+,.\/:;<=>?@\[\]\^`{|}~]/g, "\\$&") : "";
			},

			refresh: function () {
				var options = this.options,
					lis = this.tablist.children(":has(a[href])");

				// Get disabled tabs from class attribute from HTML
				// this will get converted to a boolean if needed in _refresh()
				options.disabled = $.map(lis.filter(".ui-state-disabled"), function (tab) {
					return lis.index(tab);
				});

				this._processTabs();

				// Was collapsed or no tabs
				if (options.active === false || !this.anchors.length) {
					options.active = false;
					this.active = $();

					// was active, but active tab is gone
				} else if (this.active.length && !$.contains(this.tablist[0], this.active[0])) {

					// all remaining tabs are disabled
					if (this.tabs.length === options.disabled.length) {
						options.active = false;
						this.active = $();

						// activate previous tab
					} else {
						this._activate(this._findNextTab(Math.max(0, options.active - 1), false));
					}

					// was active, active tab still exists
				} else {

					// make sure active index is correct
					options.active = this.tabs.index(this.active);
				}

				this._refresh();
			},

			_refresh: function () {
				this._setOptionDisabled(this.options.disabled);
				this._setupEvents(this.options.event);
				this._setupHeightStyle(this.options.heightStyle);

				this.tabs.not(this.active).attr({
					"aria-selected": "false",
					"aria-expanded": "false",
					tabIndex: -1
				});
				this.panels.not(this._getPanelForTab(this.active))
					.hide()
					.attr({
						"aria-hidden": "true"
					});

				// Make sure one tab is in the tab order
				if (!this.active.length) {
					this.tabs.eq(0).attr("tabIndex", 0);
				} else {
					this.active
						.attr({
							"aria-selected": "true",
							"aria-expanded": "true",
							tabIndex: 0
						});
					this._addClass(this.active, "ui-tabs-active", "ui-state-active");
					this._getPanelForTab(this.active)
						.show()
						.attr({
							"aria-hidden": "false"
						});
				}
			},

			_processTabs: function () {
				var that = this,
					prevTabs = this.tabs,
					prevAnchors = this.anchors,
					prevPanels = this.panels;

				this.tablist = this._getList().attr("role", "tablist");
				this._addClass(this.tablist, "ui-tabs-nav",
					"ui-helper-reset ui-helper-clearfix ui-widget-header");

				// Prevent users from focusing disabled tabs via click
				this.tablist
					.on("mousedown" + this.eventNamespace, "> li", function (event) {
						if ($(this).is(".ui-state-disabled")) {
							event.preventDefault();
						}
					})

					// Support: IE <9
					// Preventing the default action in mousedown doesn't prevent IE
					// from focusing the element, so if the anchor gets focused, blur.
					// We don't have to worry about focusing the previously focused
					// element since clicking on a non-focusable element should focus
					// the body anyway.
					.on("focus" + this.eventNamespace, ".ui-tabs-anchor", function () {
						if ($(this).closest("li").is(".ui-state-disabled")) {
							this.blur();
						}
					});

				this.tabs = this.tablist.find("> li:has(a[href])")
					.attr({
						role: "tab",
						tabIndex: -1
					});
				this._addClass(this.tabs, "ui-tabs-tab", "ui-state-default");

				this.anchors = this.tabs.map(function () {
					return $("a", this)[0];
				})
					.attr({
						role: "presentation",
						tabIndex: -1
					});
				this._addClass(this.anchors, "ui-tabs-anchor");

				this.panels = $();

				this.anchors.each(function (i, anchor) {
					var selector, panel, panelId,
						anchorId = $(anchor).uniqueId().attr("id"),
						tab = $(anchor).closest("li"),
						originalAriaControls = tab.attr("aria-controls");

					// Inline tab
					if (that._isLocal(anchor)) {
						selector = anchor.hash;
						panelId = selector.substring(1);
						panel = that.element.find(that._sanitizeSelector(selector));

						// remote tab
					} else {

						// If the tab doesn't already have aria-controls,
						// generate an id by using a throw-away element
						panelId = tab.attr("aria-controls") || $({}).uniqueId()[0].id;
						selector = "#" + panelId;
						panel = that.element.find(selector);
						if (!panel.length) {
							panel = that._createPanel(panelId);
							panel.insertAfter(that.panels[i - 1] || that.tablist);
						}
						panel.attr("aria-live", "polite");
					}

					if (panel.length) {
						that.panels = that.panels.add(panel);
					}
					if (originalAriaControls) {
						tab.data("ui-tabs-aria-controls", originalAriaControls);
					}
					tab.attr({
						"aria-controls": panelId,
						"aria-labelledby": anchorId
					});
					panel.attr("aria-labelledby", anchorId);
				});

				this.panels.attr("role", "tabpanel");
				this._addClass(this.panels, "ui-tabs-panel", "ui-widget-content");

				// Avoid memory leaks (#10056)
				if (prevTabs) {
					this._off(prevTabs.not(this.tabs));
					this._off(prevAnchors.not(this.anchors));
					this._off(prevPanels.not(this.panels));
				}
			},

			// Allow overriding how to find the list for rare usage scenarios (#7715)
			_getList: function () {
				return this.tablist || this.element.find("ol, ul").eq(0);
			},

			_createPanel: function (id) {
				return $("<div>")
					.attr("id", id)
					.data("ui-tabs-destroy", true);
			},

			_setOptionDisabled: function (disabled) {
				var currentItem, li, i;

				if ($.isArray(disabled)) {
					if (!disabled.length) {
						disabled = false;
					} else if (disabled.length === this.anchors.length) {
						disabled = true;
					}
				}

				// Disable tabs
				for (i = 0; (li = this.tabs[i]); i++) {
					currentItem = $(li);
					if (disabled === true || $.inArray(i, disabled) !== -1) {
						currentItem.attr("aria-disabled", "true");
						this._addClass(currentItem, null, "ui-state-disabled");
					} else {
						currentItem.removeAttr("aria-disabled");
						this._removeClass(currentItem, null, "ui-state-disabled");
					}
				}

				this.options.disabled = disabled;

				this._toggleClass(this.widget(), this.widgetFullName + "-disabled", null,
					disabled === true);
			},

			_setupEvents: function (event) {
				var events = {};
				if (event) {
					$.each(event.split(" "), function (index, eventName) {
						events[eventName] = "_eventHandler";
					});
				}

				this._off(this.anchors.add(this.tabs).add(this.panels));

				// Always prevent the default action, even when disabled
				this._on(true, this.anchors, {
					click: function (event) {
						event.preventDefault();
					}
				});
				this._on(this.anchors, events);
				this._on(this.tabs, { keydown: "_tabKeydown" });
				this._on(this.panels, { keydown: "_panelKeydown" });

				this._focusable(this.tabs);
				this._hoverable(this.tabs);
			},

			_setupHeightStyle: function (heightStyle) {
				var maxHeight,
					parent = this.element.parent();

				if (heightStyle === "fill") {
					maxHeight = parent.height();
					maxHeight -= this.element.outerHeight() - this.element.height();

					this.element.siblings(":visible").each(function () {
						var elem = $(this),
							position = elem.css("position");

						if (position === "absolute" || position === "fixed") {
							return;
						}
						maxHeight -= elem.outerHeight(true);
					});

					this.element.children().not(this.panels).each(function () {
						maxHeight -= $(this).outerHeight(true);
					});

					this.panels.each(function () {
						$(this).height(Math.max(0, maxHeight -
							$(this).innerHeight() + $(this).height()));
					})
						.css("overflow", "auto");
				} else if (heightStyle === "auto") {
					maxHeight = 0;
					this.panels.each(function () {
						maxHeight = Math.max(maxHeight, $(this).height("").height());
					}).height(maxHeight);
				}
			},

			_eventHandler: function (event) {
				var options = this.options,
					active = this.active,
					anchor = $(event.currentTarget),
					tab = anchor.closest("li"),
					clickedIsActive = tab[0] === active[0],
					collapsing = clickedIsActive && options.collapsible,
					toShow = collapsing ? $() : this._getPanelForTab(tab),
					toHide = !active.length ? $() : this._getPanelForTab(active),
					eventData = {
						oldTab: active,
						oldPanel: toHide,
						newTab: collapsing ? $() : tab,
						newPanel: toShow
					};

				event.preventDefault();

				if (tab.hasClass("ui-state-disabled") ||

					// tab is already loading
					tab.hasClass("ui-tabs-loading") ||

					// can't switch durning an animation
					this.running ||

					// click on active header, but not collapsible
					(clickedIsActive && !options.collapsible) ||

					// allow canceling activation
					(this._trigger("beforeActivate", event, eventData) === false)) {
					return;
				}

				options.active = collapsing ? false : this.tabs.index(tab);

				this.active = clickedIsActive ? $() : tab;
				if (this.xhr) {
					this.xhr.abort();
				}

				if (!toHide.length && !toShow.length) {
					$.error("jQuery UI Tabs: Mismatching fragment identifier.");
				}

				if (toShow.length) {
					this.load(this.tabs.index(tab), event);
				}
				this._toggle(event, eventData);
			},

			// Handles show/hide for selecting tabs
			_toggle: function (event, eventData) {
				var that = this,
					toShow = eventData.newPanel,
					toHide = eventData.oldPanel;

				this.running = true;

				function complete() {
					that.running = false;
					that._trigger("activate", event, eventData);
				}

				function show() {
					that._addClass(eventData.newTab.closest("li"), "ui-tabs-active", "ui-state-active");

					if (toShow.length && that.options.show) {
						that._show(toShow, that.options.show, complete);
					} else {
						toShow.show();
						complete();
					}
				}

				// Start out by hiding, then showing, then completing
				if (toHide.length && this.options.hide) {
					this._hide(toHide, this.options.hide, function () {
						that._removeClass(eventData.oldTab.closest("li"),
							"ui-tabs-active", "ui-state-active");
						show();
					});
				} else {
					this._removeClass(eventData.oldTab.closest("li"),
						"ui-tabs-active", "ui-state-active");
					toHide.hide();
					show();
				}

				toHide.attr("aria-hidden", "true");
				eventData.oldTab.attr({
					"aria-selected": "false",
					"aria-expanded": "false"
				});

				// If we're switching tabs, remove the old tab from the tab order.
				// If we're opening from collapsed state, remove the previous tab from the tab order.
				// If we're collapsing, then keep the collapsing tab in the tab order.
				if (toShow.length && toHide.length) {
					eventData.oldTab.attr("tabIndex", -1);
				} else if (toShow.length) {
					this.tabs.filter(function () {
						return $(this).attr("tabIndex") === 0;
					})
						.attr("tabIndex", -1);
				}

				toShow.attr("aria-hidden", "false");
				eventData.newTab.attr({
					"aria-selected": "true",
					"aria-expanded": "true",
					tabIndex: 0
				});
			},

			_activate: function (index) {
				var anchor,
					active = this._findActive(index);

				// Trying to activate the already active panel
				if (active[0] === this.active[0]) {
					return;
				}

				// Trying to collapse, simulate a click on the current active header
				if (!active.length) {
					active = this.active;
				}

				anchor = active.find(".ui-tabs-anchor")[0];
				this._eventHandler({
					target: anchor,
					currentTarget: anchor,
					preventDefault: $.noop
				});
			},

			_findActive: function (index) {
				return index === false ? $() : this.tabs.eq(index);
			},

			_getIndex: function (index) {

				// meta-function to give users option to provide a href string instead of a numerical index.
				if (typeof index === "string") {
					index = this.anchors.index(this.anchors.filter("[href$='" +
						$.ui.escapeSelector(index) + "']"));
				}

				return index;
			},

			_destroy: function () {
				if (this.xhr) {
					this.xhr.abort();
				}

				this.tablist
					.removeAttr("role")
					.off(this.eventNamespace);

				this.anchors
					.removeAttr("role tabIndex")
					.removeUniqueId();

				this.tabs.add(this.panels).each(function () {
					if ($.data(this, "ui-tabs-destroy")) {
						$(this).remove();
					} else {
						$(this).removeAttr("role tabIndex " +
							"aria-live aria-busy aria-selected aria-labelledby aria-hidden aria-expanded");
					}
				});

				this.tabs.each(function () {
					var li = $(this),
						prev = li.data("ui-tabs-aria-controls");
					if (prev) {
						li
							.attr("aria-controls", prev)
							.removeData("ui-tabs-aria-controls");
					} else {
						li.removeAttr("aria-controls");
					}
				});

				this.panels.show();

				if (this.options.heightStyle !== "content") {
					this.panels.css("height", "");
				}
			},

			enable: function (index) {
				var disabled = this.options.disabled;
				if (disabled === false) {
					return;
				}

				if (index === undefined) {
					disabled = false;
				} else {
					index = this._getIndex(index);
					if ($.isArray(disabled)) {
						disabled = $.map(disabled, function (num) {
							return num !== index ? num : null;
						});
					} else {
						disabled = $.map(this.tabs, function (li, num) {
							return num !== index ? num : null;
						});
					}
				}
				this._setOptionDisabled(disabled);
			},

			disable: function (index) {
				var disabled = this.options.disabled;
				if (disabled === true) {
					return;
				}

				if (index === undefined) {
					disabled = true;
				} else {
					index = this._getIndex(index);
					if ($.inArray(index, disabled) !== -1) {
						return;
					}
					if ($.isArray(disabled)) {
						disabled = $.merge([index], disabled).sort();
					} else {
						disabled = [index];
					}
				}
				this._setOptionDisabled(disabled);
			},

			load: function (index, event) {
				index = this._getIndex(index);
				var that = this,
					tab = this.tabs.eq(index),
					anchor = tab.find(".ui-tabs-anchor"),
					panel = this._getPanelForTab(tab),
					eventData = {
						tab: tab,
						panel: panel
					},
					complete = function (jqXHR, status) {
						if (status === "abort") {
							that.panels.stop(false, true);
						}

						that._removeClass(tab, "ui-tabs-loading");
						panel.removeAttr("aria-busy");

						if (jqXHR === that.xhr) {
							delete that.xhr;
						}
					};

				// Not remote
				if (this._isLocal(anchor[0])) {
					return;
				}

				this.xhr = $.ajax(this._ajaxSettings(anchor, event, eventData));

				// Support: jQuery <1.8
				// jQuery <1.8 returns false if the request is canceled in beforeSend,
				// but as of 1.8, $.ajax() always returns a jqXHR object.
				if (this.xhr && this.xhr.statusText !== "canceled") {
					this._addClass(tab, "ui-tabs-loading");
					panel.attr("aria-busy", "true");

					this.xhr
						.done(function (response, status, jqXHR) {

							// support: jQuery <1.8
							// http://bugs.jquery.com/ticket/11778
							setTimeout(function () {
								panel.html(response);
								that._trigger("load", event, eventData);

								complete(jqXHR, status);
							}, 1);
						})
						.fail(function (jqXHR, status) {

							// support: jQuery <1.8
							// http://bugs.jquery.com/ticket/11778
							setTimeout(function () {
								complete(jqXHR, status);
							}, 1);
						});
				}
			},

			_ajaxSettings: function (anchor, event, eventData) {
				var that = this;
				return {

					// Support: IE <11 only
					// Strip any hash that exists to prevent errors with the Ajax request
					url: anchor.attr("href").replace(/#.*$/, ""),
					beforeSend: function (jqXHR, settings) {
						return that._trigger("beforeLoad", event,
							$.extend({ jqXHR: jqXHR, ajaxSettings: settings }, eventData));
					}
				};
			},

			_getPanelForTab: function (tab) {
				var id = $(tab).attr("aria-controls");
				return this.element.find(this._sanitizeSelector("#" + id));
			}
		});

		// DEPRECATED
		// TODO: Switch return back to widget declaration at top of file when this is removed
		if ($.uiBackCompat !== false) {

			// Backcompat for ui-tab class (now ui-tabs-tab)
			$.widget("ui.tabs", $.ui.tabs, {
				_processTabs: function () {
					this._superApply(arguments);
					this._addClass(this.tabs, "ui-tab");
				}
			});
		}

		var widgetsTabs = $.ui.tabs;

		// DEPRECATED
		// TODO: Switch return back to widget declaration at top of file when this is removed


	}));
});




/* jQuery Validation Plugin
-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- */

/*!
 * jQuery Validation Plugin v1.20.1
 *
 * https://jqueryvalidation.org/
 *
 * Copyright (c) 2024 Jörn Zaefferer
 * Released under the MIT license
 */
(function (factory) {
	if (typeof define === "function" && define.amd) {
		define(["jquery"], factory);
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory(require("jquery"));
	} else {
		factory(jQuery);
	}
}(function ($) {

	$.extend($.fn, {

		// https://jqueryvalidation.org/validate/
		validate: function (options) {

			// If nothing is selected, return nothing; can't chain anyway
			if (!this.length) {
				if (options && options.debug && window.console) {
					console.warn("Nothing selected, can't validate, returning nothing.");
				}
				return;
			}

			// Check if a validator for this form was already created
			var validator = $.data(this[0], "validator");
			if (validator) {
				return validator;
			}

			// Add novalidate tag if HTML5.
			this.attr("novalidate", "novalidate");

			validator = new $.validator(options, this[0]);
			$.data(this[0], "validator", validator);

			if (validator.settings.onsubmit) {

				this.on("click.validate", ":submit", function (event) {

					// Track the used submit button to allow remote validation with it
					validator.submitButton = event.currentTarget;

					// Allow suppressing validation by adding a cancel class to the submit button
					if ($(this).hasClass("cancel")) {
						validator.cancelSubmit = true;
					}

					// Allow suppressing validation by adding the html5 formnovalidate attribute to the submit button
					if ($(this).attr("formnovalidate") !== undefined) {
						validator.cancelSubmit = true;
					}
				});

				// Validate the form on submit
				this.on("submit.validate", function (event) {
					if (validator.settings.debug) {

						// Prevent form submit to be able to see console output
						event.preventDefault();
					}

					function handle() {
						var hidden, result;

						// Insert a hidden input as a replacement for the missing submit button
						// The hidden input is inserted in two cases:
						//   1. When a submit button is missing
						//   2. When form is submitted using `form.submit()` and button name has been
						//      disregarded during form encoding (#1965)
						if (validator.submitButton && (validator.settings.submitHandler || validator.formSubmitted)) {
							hidden = $("<input type='hidden'/>")
								.attr("name", validator.submitButton.name)
								.val($(validator.submitButton).val())
								.appendTo(validator.currentForm);
						}

						if (validator.settings.submitHandler && !validator.settings.debug) {
							result = validator.settings.submitHandler.call(validator, validator.currentForm, event);
							if (hidden) {

								// And clean up afterwards; thanks to no-block-scope, hidden can be referenced
								hidden.remove();
							}
							if (result !== undefined) {
								return result;
							}
							return false;
						}
						return true;
					}

					// Prevent submit for invalid forms or custom submit handlers
					if (validator.cancelSubmit) {
						validator.cancelSubmit = false;
						return handle();
					}
					if (validator.form()) {
						if (validator.pendingRequest) {
							validator.formSubmitted = true;
							return false;
						}
						return handle();
					} else {
						validator.focusInvalid();
						return false;
					}
				});
			}

			return validator;
		},

		// https://jqueryvalidation.org/valid/
		valid: function () {
			var valid, validator, errorList;

			if ($(this[0]).is("form")) {
				valid = this.validate().form();
			} else {
				errorList = [];
				valid = true;
				validator = $(this[0].form).validate();
				this.each(function () {
					valid = validator.element(this) && valid;
					if (!valid) {
						errorList = errorList.concat(validator.errorList);
					}
				});
				validator.errorList = errorList;
			}
			return valid;
		},

		// https://jqueryvalidation.org/rules/
		rules: function (command, argument) {
			var element = this[0],
				isForm,
				settings,
				rules,
				ret;

			if (element == null) {
				if (window.console) {
					console.warn(
						"Called rules on an empty selector, can't get rules, returning nothing."
					);
				}
				return;
			}

			isForm = /form/i.test(element.nodeName);

			if (isForm) {
				settings = $.data(element, "validator").settings;
				rules = settings.rules;
				ret = $.validator.staticRules(element);
				$.each(rules, function (name, rule) {
					rules[name] = $.validator.normalizeRule(rule);
				});
				$.extend(ret, rules);
				return ret;
			}

			settings = $.data(element.form, "validator").settings;
			rules = settings.rules;
			ret = $.validator.staticRules(element);

			if (command) {
				switch (command) {
					case "add":
						$.extend(ret, $.validator.normalizeRule(argument));

						// Remove messages from rules, but allow them to be set separately
						delete ret.messages;
						rules[element.name] = ret;
						if (argument.messages) {
							settings.messages[element.name] = $.extend(settings.messages[element.name], argument.messages);
						}
						break;
					case "remove":
						if (!argument) {
							delete rules[element.name];
							return ret;
						}
						var filtered = {};
						$.each(argument.split(/\s/), function (index, method) {
							filtered[method] = ret[method];
							delete ret[method];
						});
						return filtered;
				}
			}

			return ret;
		}
	});

	// JQuery trim is deprecated, provide a trim function for backward compatibility.
	var trim = function (str) {

		// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/trim
		return str.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, "");
	};

	// Custom selectors
	$.extend($.expr.pseudos || $.expr[":"], { // '|| $.expr[ ":" ]' here enables backwards compatibility to jQuery 1.7.
		// https://jqueryvalidation.org/blank-selector/
		blank: function (a) {
			return !trim("" + $(a).val());
		},

		// https://jqueryvalidation.org/filled-selector/
		filled: function (a) {
			var val = $(a).val();
			return val !== null && !!trim("" + val);
		},

		// https://jqueryvalidation.org/unchecked-selector/
		unchecked: function (a) {
			return !$(a).prop("checked");
		}
	});

	// Constructor for validator
	$.validator = function (options, form) {
		this.settings = $.extend(true, {}, $.validator.defaults, options);
		this.currentForm = form;
		this.init();
	};

	// https://jqueryvalidation.org/jQuery.validator.format/
	$.validator.format = function (source, params) {
		if (arguments.length === 1) {
			return function () {
				var args = $.makeArray(arguments);
				args.unshift(source);
				return $.validator.format.apply(this, args);
			};
		}
		if (params === undefined) {
			return source;
		}
		if (arguments.length > 2 && params.constructor !== Array) {
			params = $.makeArray(arguments).slice(1);
		}
		if (params.constructor !== Array) {
			params = [params];
		}
		$.each(params, function (i, n) {
			source = source.replace(new RegExp("\\{" + i + "\\}", "g"), function () {
				return n;
			});
		});
		return source;
	};

	$.extend($.validator, {

		defaults: {
			messages: {},
			groups: {},
			rules: {},
			errorClass: "error",
			pendingClass: "pending",
			validClass: "valid",
			errorElement: "label",
			focusCleanup: false,
			focusInvalid: true,
			errorContainer: $([]),
			errorLabelContainer: $([]),
			onsubmit: true,
			ignore: ":hidden",
			ignoreTitle: false,
			onfocusin: function (element) {
				this.lastActive = element;

				// Hide error label and remove error class on focus if enabled
				if (this.settings.focusCleanup) {
					if (this.settings.unhighlight) {
						this.settings.unhighlight.call(this, element, this.settings.errorClass, this.settings.validClass);
					}
					this.hideThese(this.errorsFor(element));
				}
			},
			onfocusout: function (element) {
				if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {
					this.element(element);
				}
			},
			onkeyup: function (element, event) {

				// Avoid revalidating the field when pressing one of the following keys
				// Shift       => 16
				// Ctrl        => 17
				// Alt         => 18
				// Caps lock   => 20
				// End         => 35
				// Home        => 36
				// Left arrow  => 37
				// Up arrow    => 38
				// Right arrow => 39
				// Down arrow  => 40
				// Insert      => 45
				// Num lock    => 144
				// AltGr key   => 225
				var excludedKeys = [
					16, 17, 18, 20, 35, 36, 37,
					38, 39, 40, 45, 144, 225
				];

				if (event.which === 9 && this.elementValue(element) === "" || $.inArray(event.keyCode, excludedKeys) !== -1) {
					return;
				} else if (element.name in this.submitted || element.name in this.invalid) {
					this.element(element);
				}
			},
			onclick: function (element) {

				// Click on selects, radiobuttons and checkboxes
				if (element.name in this.submitted) {
					this.element(element);

					// Or option elements, check parent select in that case
				} else if (element.parentNode.name in this.submitted) {
					this.element(element.parentNode);
				}
			},
			highlight: function (element, errorClass, validClass) {
				if (element.type === "radio") {
					this.findByName(element.name).addClass(errorClass).removeClass(validClass);
				} else {
					$(element).addClass(errorClass).removeClass(validClass);
				}
			},
			unhighlight: function (element, errorClass, validClass) {
				if (element.type === "radio") {
					this.findByName(element.name).removeClass(errorClass).addClass(validClass);
				} else {
					$(element).removeClass(errorClass).addClass(validClass);
				}
			}
		},

		// https://jqueryvalidation.org/jQuery.validator.setDefaults/
		setDefaults: function (settings) {
			$.extend($.validator.defaults, settings);
		},

		messages: {
			required: "This field is required.",
			remote: "Please fix this field.",
			email: "Please enter a valid email address.",
			url: "Please enter a valid URL.",
			date: "Please enter a valid date.",
			dateISO: "Please enter a valid date (ISO).",
			number: "Please enter a valid number.",
			digits: "Please enter only digits.",
			equalTo: "Please enter the same value again.",
			maxlength: $.validator.format("Please enter no more than {0} characters."),
			minlength: $.validator.format("Please enter at least {0} characters."),
			rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
			range: $.validator.format("Please enter a value between {0} and {1}."),
			max: $.validator.format("Please enter a value less than or equal to {0}."),
			min: $.validator.format("Please enter a value greater than or equal to {0}."),
			step: $.validator.format("Please enter a multiple of {0}.")
		},

		autoCreateRanges: false,

		prototype: {

			init: function () {
				this.labelContainer = $(this.settings.errorLabelContainer);
				this.errorContext = this.labelContainer.length && this.labelContainer || $(this.currentForm);
				this.containers = $(this.settings.errorContainer).add(this.settings.errorLabelContainer);
				this.submitted = {};
				this.valueCache = {};
				this.pendingRequest = 0;
				this.pending = {};
				this.invalid = {};
				this.reset();

				var groups = (this.groups = {}),
					rules;
				$.each(this.settings.groups, function (key, value) {
					if (typeof value === "string") {
						value = value.split(/\s/);
					}
					$.each(value, function (index, name) {
						groups[name] = key;
					});
				});
				rules = this.settings.rules;
				$.each(rules, function (key, value) {
					rules[key] = $.validator.normalizeRule(value);
				});

				function delegate(event) {

					// Set form expando on contenteditable
					if (!this.form && this.hasAttribute("contenteditable")) {
						this.form = $(this).closest("form")[0];
						this.name = $(this).attr("name");
					}

					var validator = $.data(this.form, "validator"),
						eventType = "on" + event.type.replace(/^validate/, ""),
						settings = validator.settings;
					if (settings[eventType] && !$(this).is(settings.ignore)) {
						settings[eventType].call(validator, this, event);
					}
				}

				$(this.currentForm)
					.on("focusin.validate focusout.validate keyup.validate",
						":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'], " +
						"[type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], " +
						"[type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], " +
						"[type='radio'], [type='checkbox'], [contenteditable], [type='button']", delegate)

					// Support: Chrome, oldIE
					// "select" is provided as event.target when clicking a option
					.on("click.validate", "select, option, [type='radio'], [type='checkbox']", delegate);

				if (this.settings.invalidHandler) {
					$(this.currentForm).on("invalid-form.validate", this.settings.invalidHandler);
				}
			},

			// https://jqueryvalidation.org/Validator.form/
			form: function () {
				this.checkForm();
				$.extend(this.submitted, this.errorMap);
				this.invalid = $.extend({}, this.errorMap);
				if (!this.valid()) {
					$(this.currentForm).triggerHandler("invalid-form", [this]);
				}
				this.showErrors();
				return this.valid();
			},

			checkForm: function () {
				this.prepareForm();
				for (var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++) {
					this.check(elements[i]);
				}
				return this.valid();
			},

			// https://jqueryvalidation.org/Validator.element/
			element: function (element) {
				var cleanElement = this.clean(element),
					checkElement = this.validationTargetFor(cleanElement),
					v = this,
					result = true,
					rs,
					group = v.groups[checkElement.name];

				if (group) {
					$.each(v.groups, function (name, testgroup) {
						if (testgroup === group && name !== checkElement.name) {
							cleanElement = v.validationTargetFor(v.clean(v.findByName(name)));
							if (cleanElement && cleanElement.name in v.invalid) {
								v.currentElements.push(cleanElement);
								result = v.check(cleanElement) && result;
							}
						}
					});
				}

				rs = v.check(checkElement) !== false;
				result = result && rs;
				if (rs) {
					v.invalid[checkElement.name] = false;
				} else {
					v.invalid[checkElement.name] = true;
				}

				if (!v.numberOfInvalids()) {

					// Hide error containers on last error
					v.toHide = v.toHide.add(v.containers);
				}
				v.showErrors();

				// Add aria-invalid status for screen readers
				$(element).attr("aria-invalid", !rs);

				return result;
			},

			// https://jqueryvalidation.org/Validator.showErrors/
			showErrors: function (errors) {
				if (errors) {
					var validator = this;

					// Add items to error list and map
					$.extend(this.errorMap, errors);
					this.errorList = $.map(this.errorMap, function (message, name) {
						return {
							message: message,
							element: validator.findByName(name)[0]
						};
					});

					// Remove items from success list
					this.successList = $.grep(this.successList, function (element) {
						return !(element.name in errors);
					});
				}
				if (this.settings.showErrors) {
					this.settings.showErrors.call(this, this.errorMap, this.errorList);
				} else {
					this.defaultShowErrors();
				}
			},

			// https://jqueryvalidation.org/Validator.resetForm/
			resetForm: function () {
				if ($.fn.resetForm) {
					$(this.currentForm).resetForm();
				}
				this.invalid = {};
				this.submitted = {};
				this.prepareForm();
				this.hideErrors();
				var elements = this.elements()
					.removeData("previousValue")
					.removeAttr("aria-invalid");

				this.resetElements(elements);
			},

			resetElements: function (elements) {
				var i;

				if (this.settings.unhighlight) {
					for (i = 0; elements[i]; i++) {
						this.settings.unhighlight.call(this, elements[i],
							this.settings.errorClass, "");
						this.findByName(elements[i].name).removeClass(this.settings.validClass);
					}
				} else {
					elements
						.removeClass(this.settings.errorClass)
						.removeClass(this.settings.validClass);
				}
			},

			numberOfInvalids: function () {
				return this.objectLength(this.invalid);
			},

			objectLength: function (obj) {
				/* jshint unused: false */
				var count = 0,
					i;
				for (i in obj) {

					// This doesn't try to handle toString methods that may exist on Object.prototype
					// for IE < 9, see https://github.com/jquery-validation/jquery-validation/pull/1540
					if (obj[i] && (obj[i].constructor === Boolean || obj[i].constructor === String)) {
						count++;
					}
				}
				return count;
			},

			hideErrors: function () {
				this.hideThese(this.toHide);
			},

			hideThese: function (errors) {
				errors.not(this.containers).text("");
				this.addWrapper(errors).hide();
			},

			valid: function () {
				return this.size() === 0;
			},

			size: function () {
				return this.errorList.length;
			},

			focusInvalid: function () {
				if (this.settings.focusInvalid) {
					try {
						$(this.findLastActive() || this.errorList.length && this.errorList[0].element || [])
							.filter(":visible")
							.trigger("focus")

							// Manually trigger focusin event; without it, focusin handler isn't called, findLastActive won't have anything to find
							.trigger("focusin");
					} catch (e) {

						// Ignore IE throwing errors when focusing hidden elements
					}
				}
			},

			findLastActive: function () {
				var lastActive = this.lastActive;
				return lastActive && $.grep(this.errorList, function (n) {
					return n.element.name === lastActive.name;
				}).length === 1 && lastActive;
			},

			elements: function () {
				var validator = this,
					rulesCache = {};

				// Select all valid inputs inside the form (no submit or reset buttons)
				return $(this.currentForm)
					.find("input, select, textarea, [contenteditable]")
					.not(":submit, :reset, :image, :disabled")
					.not(this.settings.ignore)
					.filter(function () {
						var name = this.name || $(this).attr("name"); // For contenteditable
						if (!name && validator.settings.debug && window.console) {
							console.error("%o has no name assigned", this);
						}

						// Set form expando on contenteditable
						if (this.hasAttribute("contenteditable")) {
							this.form = $(this).closest("form")[0];
							this.name = name;
						}

						// Select only the first element for each name, and only those with rules specified
						if (name in rulesCache || !validator.objectLength($(this).rules())) {
							return false;
						}

						rulesCache[name] = true;
						return true;
					});
			},

			clean: function (selector) {
				return $(selector)[0];
			},

			errors: function () {
				var errorClass = this.settings.errorClass.split(" ").join(".");
				return $(this.settings.errorElement + "." + errorClass, this.errorContext);
			},

			resetInternals: function () {
				this.successList = [];
				this.errorList = [];
				this.errorMap = {};
				this.toShow = $([]);
				this.toHide = $([]);
			},

			reset: function () {
				this.resetInternals();
				this.currentElements = $([]);
			},

			prepareForm: function () {
				this.reset();
				this.toHide = this.errors().add(this.containers);
			},

			prepareElement: function (element) {
				this.reset();
				this.toHide = this.errorsFor(element);
			},

			elementValue: function (element) {
				var $element = $(element),
					type = element.type,
					val,
					idx;

				if (type === "radio" || type === "checkbox") {
					return this.findByName(element.name).filter(":checked").val();
				} else if (type === "number" && typeof element.validity !== "undefined") {
					return element.validity.badInput ? "NaN" : $element.val();
				}

				if (element.hasAttribute("contenteditable")) {
					val = $element.text();
				} else {
					val = $element.val();
				}

				if (type === "file") {

					// Modern browser (chrome & safari)
					if (val.substr(0, 12) === "C:\\fakepath\\") {
						return val.substr(12);
					}

					// Legacy browsers
					// Unix-based path
					idx = val.lastIndexOf("/");
					if (idx >= 0) {
						return val.substr(idx + 1);
					}

					// Windows-based path
					idx = val.lastIndexOf("\\");
					if (idx >= 0) {
						return val.substr(idx + 1);
					}

					// Just the file name
					return val;
				}

				if (typeof val === "string") {
					return val.replace(/\r/g, "");
				}
				return val;
			},

			check: function (element) {
				element = this.validationTargetFor(this.clean(element));

				var rules = $(element).rules(),
					rulesCount = $.map(rules, function (n, i) {
						return i;
					}).length,
					dependencyMismatch = false,
					val = this.elementValue(element),
					result,
					method,
					rule,
					normalizer;

				if (typeof rules.normalizer === "function") {
					normalizer = rules.normalizer;
				} else if (typeof this.settings.normalizer === "function") {
					normalizer = this.settings.normalizer;
				}

				// The normalizer is invoked here, it transforms the input value to something else.
				// E.g. a checkbox living on the page as value="on" instead of value="true" will cause
				// problems with the required method. The normalizer fixes this.
				if (normalizer) {
					val = normalizer.call(element, val);

					if (typeof val !== "string") {
						delete rules.normalizer;
					} else {

						// Cast the value to string right away, otherwise the required method can't handle cases
						// where the normalizer returns a number, false or null.
						val = trim(val);
					}
				}

				for (method in rules) {
					rule = { method: method, parameters: rules[method] };
					try {
						result = $.validator.methods[method].call(this, val, element, rule.parameters);

						// If a method indicates that the field is optional and therefore valid,
						// don't mark it as valid when there are no other rules
						if (result === "dependency-mismatch" && rulesCount === 1) {
							dependencyMismatch = true;
							continue;
						}
						dependencyMismatch = false;

						if (result === "pending") {
							this.toHide = this.toHide.not(this.errorsFor(element));
							return;
						}

						if (!result) {
							this.formatAndAdd(element, rule);
							return false;
						}
					} catch (e) {
						if (this.settings.debug && window.console) {
							console.log("Exception occurred when checking element " + element.id + ", check the '" + rule.method + "' method.", e);
						}
						if (e instanceof TypeError) {
							e.message += ".  Exception occurred when checking element " + element.id + ", check the '" + rule.method + "' method.";
						}

						throw e;
					}
				}
				if (dependencyMismatch) {
					return;
				}
				if (this.objectLength(rules)) {
					this.successList.push(element);
				}
				return true;
			},

			// Return the custom message for the given element and validation method
			// specified in the element's HTML5 data attribute
			// return the generic message if present and no method specific message is present
			customDataMessage: function (element, method) {
				return $(element).data("msg" + method.charAt(0).toUpperCase() +
					method.substring(1).toLowerCase()) || $(element).data("msg");
			},

			// Return the custom message for the given element name and validation method
			customMessage: function (name, method) {
				var m = this.settings.messages[name];
				return m && (m.constructor === String ? m : m[method]);
			},

			// Return the first defined argument, allowing empty strings
			findDefined: function () {
				for (var i = 0; i < arguments.length; i++) {
					if (arguments[i] !== undefined) {
						return arguments[i];
					}
				}
				return undefined;
			},

			// The rest of the message hierarchy is to check plugin-wide defaults then
			// fall back to the built-in default message.
			defaultMessage: function (element, rule) {
				var message = this.findDefined(
					this.customMessage(element.name, rule.method),
					this.customDataMessage(element, rule.method),

					// 'title' is never undefined, so handle empty string as undefined
					!this.settings.ignoreTitle && element.title || undefined,
					$.validator.messages[rule.method],
					"<strong>Warning: No message defined for " + element.name + "</strong>"
				),
					theregex = /\$?\{(\d+)\}/g;
				if (typeof message === "function") {
					message = message.call(this, rule.parameters, element);
				} else if (theregex.test(message)) {
					message = $.validator.format(message.replace(theregex, "{$1}"), rule.parameters);
				}

				return message;
			},

			formatAndAdd: function (element, rule) {
				var message = this.defaultMessage(element, rule);

				this.errorList.push({
					message: message,
					element: element,
					method: rule.method
				});

				this.errorMap[element.name] = message;
				this.submitted[element.name] = message;
			},

			addWrapper: function (toToggle) {
				if (this.settings.wrapper) {
					toToggle = toToggle.add(toToggle.parent(this.settings.wrapper));
				}
				return toToggle;
			},

			defaultShowErrors: function () {
				var i, elements, error;
				for (i = 0; this.errorList[i]; i++) {
					error = this.errorList[i];
					if (this.settings.highlight) {
						this.settings.highlight.call(this, error.element, this.settings.errorClass, this.settings.validClass);
					}
					this.showLabel(error.element, error.message);
				}
				if (this.errorList.length) {
					this.toShow = this.toShow.add(this.containers);
				}
				if (this.settings.success) {
					for (i = 0; this.successList[i]; i++) {
						this.showLabel(this.successList[i]);
					}
				}
				if (this.settings.unhighlight) {
					for (i = 0, elements = this.validElements(); elements[i]; i++) {
						this.settings.unhighlight.call(this, elements[i], this.settings.errorClass, this.settings.validClass);
					}
				}
				this.toHide = this.toHide.not(this.toShow);
				this.hideErrors();
				this.addWrapper(this.toShow).show();
			},

			validElements: function () {
				return this.currentElements.not(this.invalidElements());
			},

			invalidElements: function () {
				return $(this.errorList).map(function () {
					return this.element;
				});
			},

			showLabel: function (element, message) {
				var place, group, errorID, v,
					error = this.errorsFor(element),
					elementID = this.idOrName(element),
					describedBy = $(element).attr("aria-describedby");

				if (error.length) {

					// Refresh error/success class
					error.removeClass(this.settings.validClass).addClass(this.settings.errorClass);

					// Replace message on existing label
					error.html(message);
				} else {

					// Create error element
					error = $("<" + this.settings.errorElement + ">")
						.attr("id", elementID + "-error")
						.addClass(this.settings.errorClass)
						.html(message || "");

					// Maintain reference to the element to be placed into the DOM
					place = error;
					if (this.settings.wrapper) {

						// Make sure the element is visible, even in IE
						// actually showing the wrapped element is handled elsewhere
						place = error.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
					}
					if (this.labelContainer.length) {
						this.labelContainer.append(place);
					} else if (this.settings.errorPlacement) {
						this.settings.errorPlacement.call(this, place, $(element));
					} else {
						place.insertAfter(element);
					}

					// Link error back to the element
					if (error.is("label")) {

						// If the error is a label, it gets a `for` attribute pointing to the element
						error.attr("for", elementID);

						// If the element is not a child of an associated label, then it's necessary
						// to explicitly apply `aria-describedby`
					} else if (error.parents("label[for='" + this.escapeCssMeta(elementID) + "']").length === 0) {
						errorID = error.attr("id");

						// Respect existing non-empty aria-describedby
						if (!describedBy) {
							describedBy = errorID;
						} else if (!describedBy.match(new RegExp("\\b" + this.escapeCssMeta(errorID) + "\\b"))) {

							// Add to end of list if not already present
							describedBy += " " + errorID;
						}
						$(element).attr("aria-describedby", describedBy);

						group = this.groups[element.name];
						if (group) {
							v = this;
							$.each(v.groups, function (name, testgroup) {
								if (testgroup === group) {
									$("[name='" + v.escapeCssMeta(name) + "']", v.currentForm)
										.attr("aria-describedby", error.attr("id"));
								}
							});
						}
					}
				}
				if (!message && this.settings.success) {
					error.text("");
					if (typeof this.settings.success === "string") {
						error.addClass(this.settings.success);
					} else {
						this.settings.success(error, element);
					}
				}
				this.toShow = this.toShow.add(error);
			},

			errorsFor: function (element) {
				var name = this.escapeCssMeta(this.idOrName(element)),
					describer = $(element).attr("aria-describedby"),
					selector = "label[for='" + name + "'], label[for='" + name + "'] *";

				// 'aria-describedby' should directly reference the error element
				if (describer) {
					selector = selector + ", #" + this.escapeCssMeta(describer).replace(/\s+/g, ", #");
				}

				return this
					.errors()
					.filter(selector);
			},

			// See https://api.jquery.com/category/selectors/, for CSS
			// meta-characters that should be escaped in order to be used with JQuery
			// as a literal part of a name/id or any selector.
			escapeCssMeta: function (string) {
				return string.replace(/([\\!"#$%&'()*+,./:;<=>?@\[\]^`{|}~])/g, "\\$1");
			},

			idOrName: function (element) {
				return this.groups[element.name] || (this.checkable(element) ? element.name : element.id || element.name);
			},

			validationTargetFor: function (element) {

				// If radio/checkbox, validate first element in group instead
				if (this.checkable(element)) {
					element = this.findByName(element.name);
				}

				// Always apply ignore filter
				return $(element).not(this.settings.ignore)[0];
			},

			checkable: function (element) {
				return (/radio|checkbox/i).test(element.type);
			},

			findByName: function (name) {
				return $(this.currentForm).find("[name='" + this.escapeCssMeta(name) + "']");
			},

			getLength: function (value, element) {
				switch (element.nodeName.toLowerCase()) {
					case "select":
						return $("option:selected", element).length;
					case "input":
						if (this.checkable(element)) {
							return this.findByName(element.name).filter(":checked").length;
						}
				}
				return value.length;
			},

			depend: function (param, element) {
				return this.dependTypes[typeof param] ? this.dependTypes[typeof param](param, element) : true;
			},

			dependTypes: {
				"boolean": function (param) {
					return param;
				},
				"string": function (param, element) {
					return !!$(param, element.form).length;
				},
				"function": function (param, element) {
					return param(element);
				}
			},

			optional: function (element) {
				var val = this.elementValue(element);
				return !$.validator.methods.required.call(this, val, element) && "dependency-mismatch";
			},

			startRequest: function (element) {
				if (!this.pending[element.name]) {
					this.pendingRequest++;
					$(element).addClass(this.settings.pendingClass);
					this.pending[element.name] = true;
				}
			},

			stopRequest: function (element, valid) {
				this.pendingRequest--;
				if (this.pendingRequest < 0) {
					this.pendingRequest = 0;
				}
				delete this.pending[element.name];
				$(element).removeClass(this.settings.pendingClass);
				if (valid && this.pendingRequest === 0 && this.formSubmitted && this.form()) {
					$(this.currentForm).submit();

					// Remove the hidden input that was used as a replacement for the missing submit button
					// The hidden input is inserted in `handle()`
					if (this.submitButton) {
						$("input:hidden[name='" + this.submitButton.name + "']", this.currentForm).remove();
					}

					this.formSubmitted = false;
				} else if (!valid && this.pendingRequest === 0 && this.formSubmitted) {
					$(this.currentForm).triggerHandler("invalid-form", [this]);
					this.formSubmitted = false;
				}
			},

			previousValue: function (element, method) {
				method = typeof method === "string" && method || "remote";

				return $.data(element, "previousValue") || $.data(element, "previousValue", {
					old: null,
					valid: true,
					message: this.defaultMessage(element, { method: method })
				});
			},

			// Cleans up all forms and settings, removes client-side validation
			destroy: function () {
				this.resetForm();

				$(this.currentForm)
					.off(".validate")
					.removeData("validator")
					.find(".validate-equalTo-blur")
					.off(".validate-equalTo")
					.removeClass("validate-equalTo-blur")
					.find(".validate-lessThan-blur")
					.off(".validate-lessThan")
					.removeClass("validate-lessThan-blur")
					.find(".validate-lessThanEqual-blur")
					.off(".validate-lessThanEqual")
					.removeClass("validate-lessThanEqual-blur")
					.find(".validate-greaterThanEqual-blur")
					.off(".validate-greaterThanEqual")
					.removeClass("validate-greaterThanEqual-blur")
					.find(".validate-greaterThan-blur")
					.off(".validate-greaterThan")
					.removeClass("validate-greaterThan-blur");
			}

		},

		classRuleSettings: {
			required: { required: true },
			email: { email: true },
			url: { url: true },
			date: { date: true },
			dateISO: { dateISO: true },
			number: { number: true },
			digits: { digits: true },
			creditcard: { creditcard: true }
		},

		addClassRules: function (className, rules) {
			if (className.constructor === String) {
				this.classRuleSettings[className] = rules;
			} else {
				$.extend(this.classRuleSettings, className);
			}
		},

		classRules: function (element) {
			var rules = {},
				classes = $(element).attr("class");

			if (classes) {
				$.each(classes.split(" "), function () {
					if (this in $.validator.classRuleSettings) {
						$.extend(rules, $.validator.classRuleSettings[this]);
					}
				});
			}
			return rules;
		},

		normalizeAttributeRule: function (rules, type, method, value) {

			// Convert the value to a number for number inputs, and for min, max, step attributes
			if (/min|max|step/.test(method) && (type === null || /number|range|text/.test(type))) {
				value = Number(value);

				// Support Opera Mini, which returns NaN for undefined attributes
				if (isNaN(value)) {
					value = undefined;
				}
			}

			if (value || value === 0) {
				rules[method] = value;
			} else if (type === method && type !== "range") {

				// Exception: the jquery validate 'range' method
				// does not test for the html5 'range' type
				rules[method] = true;
			}
		},

		attributeRules: function (element) {
			var rules = {},
				$element = $(element),
				type = element.getAttribute("type"),
				method,
				value;

			for (method in $.validator.methods) {

				// Support for <input required> in both html5 and older browsers
				if (method === "required") {
					value = element.getAttribute(method);

					// Some browsers return an empty string for the required attribute
					// and others return false.  All browsers return null if the attribute is not present.
					if (value === "") {
						value = true;
					}

					// Normalize false representations
					value = !!value;
				} else {
					value = $element.attr(method);
				}

				this.normalizeAttributeRule(rules, type, method, value);
			}

			// 'maxlength' may be returned as -1, 2147483647 ( IE ) and 524288 ( safari ) for text inputs
			if (rules.maxlength && /-1|2147483647|524288/.test(rules.maxlength)) {
				delete rules.maxlength;
			}

			return rules;
		},

		dataRules: function (element) {
			var rules = {},
				$element = $(element),
				type = element.getAttribute("type"),
				method,
				value;

			for (method in $.validator.methods) {
				value = $element.data("rule" + method.charAt(0).toUpperCase() + method.substring(1).toLowerCase());

				// Cast empty attributes like `data-rule-required` to `true`
				if (value === "") {
					value = true;
				}

				this.normalizeAttributeRule(rules, type, method, value);
			}
			return rules;
		},

		staticRules: function (element) {
			var rules = {},
				validator = $.data(element.form, "validator");

			if (validator.settings.rules) {
				rules = $.validator.normalizeRule(validator.settings.rules[element.name]) || {};
			}
			return rules;
		},

		normalizeRules: function (rules, element) {

			// Handle dependency check
			$.each(rules, function (prop, val) {

				// Ignore rule when param is explicitly false, eg. required:false
				if (val === false) {
					delete rules[prop];
					return;
				}
				if (val.param || val.depends) {
					var keepRule = true;
					switch (typeof val.depends) {
						case "string":
							keepRule = !!$(val.depends, element.form).length;
							break;
						case "function":
							keepRule = val.depends.call(element, element);
							break;
					}
					if (keepRule) {
						rules[prop] = val.param !== undefined ? val.param : true;
					} else {
						$.data(element.form, "validator").resetElements($(element));
						delete rules[prop];
					}
				}
			});

			// Evaluate parameters
			$.each(rules, function (rule, parameter) {
				rules[rule] = $.isFunction(parameter) && rule !== "normalizer" ? parameter(element) : parameter;
			});

			// Clean number parameters
			$.each(["minlength", "maxlength"], function () {
				if (rules[this]) {
					rules[this] = Number(rules[this]);
				}
			});
			$.each(["rangelength", "range"], function () {
				var parts;
				if (rules[this]) {
					if ($.isArray(rules[this])) {
						rules[this] = [Number(rules[this][0]), Number(rules[this][1])];
					} else if (typeof rules[this] === "string") {
						parts = rules[this].replace(/[\[\]]/g, "").split(/[\s,]+/);
						rules[this] = [Number(parts[0]), Number(parts[1])];
					}
				}
			});

			if ($.validator.autoCreateRanges) {

				// Create ranges from min, max, minlength, maxlength
				if (rules.min != null && rules.max != null) {
					rules.range = [rules.min, rules.max];
					delete rules.min;
					delete rules.max;
				}
				if (rules.minlength != null && rules.maxlength != null) {
					rules.rangelength = [rules.minlength, rules.maxlength];
					delete rules.minlength;
					delete rules.maxlength;
				}
			}

			return rules;
		},

		// Converts a simple string to a {string: true} rule, e.g., "required" to {required:true}
		normalizeRule: function (data) {
			if (typeof data === "string") {
				var methods = {};
				$.each(data.split(/\s/), function () {
					methods[this] = true;
				});
				data = methods;
			}
			return data;
		},

		// https://jqueryvalidation.org/jQuery.validator.addMethod/
		addMethod: function (name, method, message) {
			$.validator.methods[name] = method;
			$.validator.messages[name] = message !== undefined ? message : $.validator.messages[name];
			if (method.length < 3) {
				$.validator.addClassRules(name, $.validator.normalizeRule(name));
			}
		},

		// https://jqueryvalidation.org/jQuery.validator.methods/
		methods: {

			// https://jqueryvalidation.org/required-method/
			required: function (value, element, param) {

				// Check if dependency is met
				if (!this.depend(param, element)) {
					return "dependency-mismatch";
				}
				if (element.nodeName.toLowerCase() === "select") {

					// Could be an array for select-multiple or a string, both are fine this way
					var val = $(element).val();
					return val && val.length > 0;
				}
				if (this.checkable(element)) {
					return this.getLength(value, element) > 0;
				}
				return value.length > 0;
			},

			// https://jqueryvalidation.org/email-method/
			email: function (value, element) {

				// From https://html.spec.whatwg.org/multipage/forms.html#valid-e-mail-address
				// Retrieved 2014-01-14
				// If you have a problem with this implementation, report a bug against the above spec
				// Or use custom methods to implement your own email validation
				return this.optional(element) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(value);
			},

			// https://jqueryvalidation.org/url-method/
			url: function (value, element) {

				// Copyright (c) 2010-2013 Diego Perini, MIT licensed
				// https://gist.github.com/dperini/729294
				// see also https://mathiasbynens.be/demo/url-regex
				// modified to allow protocol-relative URLs
				return this.optional(element) || /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(value);
			},

			// https://jqueryvalidation.org/date-method/
			date: (function () {
				var called = false;
				return function (value, element) {
					if (!called) {
						called = true;
						if (this.settings.debug && window.console) {
							console.warn(
								"The `date` method is deprecated and will be removed in version '2.0.0'.\n" +
								"Please don't use it, use `dateISO` instead. " +
								"See https://github.com/jquery-validation/jquery-validation/issues/2108"
							);
						}
					}

					return this.optional(element) || !/Invalid|NaN/.test(new Date(value).toString());
				};
			}()),

			// https://jqueryvalidation.org/dateISO-method/
			dateISO: function (value, element) {
				return this.optional(element) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
			},

			// https://jqueryvalidation.org/number-method/
			number: function (value, element) {
				return this.optional(element) || /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value);
			},

			// https://jqueryvalidation.org/digits-method/
			digits: function (value, element) {
				return this.optional(element) || /^\d+$/.test(value);
			},

			// https://jqueryvalidation.org/minlength-method/
			minlength: function (value, element, param) {
				var length = $.isArray(value) ? value.length : this.getLength(value, element);
				return this.optional(element) || length >= param;
			},

			// https://jqueryvalidation.org/maxlength-method/
			maxlength: function (value, element, param) {
				var length = $.isArray(value) ? value.length : this.getLength(value, element);
				return this.optional(element) || length <= param;
			},

			// https://jqueryvalidation.org/rangelength-method/
			rangelength: function (value, element, param) {
				var length = $.isArray(value) ? value.length : this.getLength(value, element);
				return this.optional(element) || (length >= param[0] && length <= param[1]);
			},

			// https://jqueryvalidation.org/min-method/
			min: function (value, element, param) {
				return this.optional(element) || value >= param;
			},

			// https://jqueryvalidation.org/max-method/
			max: function (value, element, param) {
				return this.optional(element) || value <= param;
			},

			// https://jqueryvalidation.org/range-method/
			range: function (value, element, param) {
				return this.optional(element) || (value >= param[0] && value <= param[1]);
			},

			// https://jqueryvalidation.org/equalTo-method/
			equalTo: function (value, element, param) {

				// Bind to the blur event of the target in order to revalidate whenever the target field is updated
				var target = $(param);
				if (this.settings.onfocusout && target.not(".validate-equalTo-blur").length) {
					target.addClass("validate-equalTo-blur").on("blur.validate-equalTo", function () {
						$(element).valid();
					});
				}
				return value === target.val();
			},

			// https://jqueryvalidation.org/remote-method/
			remote: function (value, element, param, method) {
				if (this.optional(element)) {
					return "dependency-mismatch";
				}

				method = typeof method === "string" && method || "remote";

				var previous = this.previousValue(element, method),
					validator,
					data,
					isFunction;

				if (!this.settings.messages[element.name]) {
					this.settings.messages[element.name] = {};
				}
				previous.originalMessage = previous.originalMessage || this.settings.messages[element.name][method];
				this.settings.messages[element.name][method] = previous.message;

				param = typeof param === "string" ? { url: param } : param;
				isFunction = $.isFunction(param.url);

				if (previous.old === value && previous.valid) {
					return previous.valid;
				}

				previous.old = value;
				validator = this;
				this.startRequest(element);

				data = {};
				data[element.name] = value;

				$.ajax($.extend(true, {
					mode: "abort",
					port: "validate" + element.name,
					dataType: "json",
					data: data,
					context: validator.currentForm,
					success: function (response) {
						var valid = response === true || response === "true",
							errors,
							message,
							submitted;

						validator.settings.messages[element.name][method] = previous.originalMessage;

						if (valid) {
							submitted = validator.formSubmitted;
							validator.resetInternals();
							validator.toHide = validator.errorsFor(element);
							validator.formSubmitted = submitted;
							validator.successList.push(element);
							validator.invalid[element.name] = false;
							validator.showErrors();
						} else {
							errors = {};
							message = response || validator.defaultMessage(element, { method: method, parameters: value });
							errors[element.name] = previous.message = message;
							validator.invalid[element.name] = true;
							validator.showErrors(errors);
						}
						previous.valid = valid;
						validator.stopRequest(element, valid);
					}
				}, isFunction ? { url: param.url(value) } : param));
				return "pending";
			}
		}

	});

	// Ajax mode: abort
	// usage: $.ajax({ mode: "abort"[, port: "uniqueport"]});
	// if mode:"abort" is used, the previous request on that port (port can be undefined) is aborted via XMLHttpRequest.abort()

	var pendingRequests = {},
		ajax;

	// Use a prefilter if available (1.5+)
	if ($.ajaxPrefilter) {
		$.ajaxPrefilter(function (settings, _, xhr) {
			var port = settings.port;
			if (settings.mode === "abort") {
				if (pendingRequests[port]) {
					pendingRequests[port].abort();
				}
				pendingRequests[port] = xhr;
			}
		});
	} else {

		// Proxy ajax
		ajax = $.ajax;
		$.ajax = function (settings) {
			var mode = ("mode" in settings ? settings : $.ajaxSettings).mode,
				port = ("port" in settings ? settings : $.ajaxSettings).port;
			if (mode === "abort") {
				if (pendingRequests[port]) {
					pendingRequests[port].abort();
				}
				pendingRequests[port] = ajax.apply(this, arguments);
				return pendingRequests[port];
			}
			return ajax.apply(this, arguments);
		};
	}

	return $;
}));