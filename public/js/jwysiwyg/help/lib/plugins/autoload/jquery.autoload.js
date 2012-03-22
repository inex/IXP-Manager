/**
 * Autoload
 */
(function ($) {
	// Autoload namespace: private properties and methods
	var Autoload = {
		/**
		 * Include necessary CSS file
		 */
		css: function (file, options) {
			var collection = $("link[rel=stylesheet]"),
				path = options.basePath + options.cssPath + file,
				element,
				i;

			for (i = 0; i < collection.length; i += 1) {
				if (path === this.href) {
					// is loaded
					return true;
				}
			}

			if ($.browser.msie) {
				/*
					<style> element
					var styleSheet = document.createElement('STYLE');
					document.documentElement.firstChild.appendChild(styleSheet);
				*/
				element = window.document.createStyleSheet(path);
				$(element).attr({
					"media":	"all"
				});
			} else {
				element = $("<link/>").attr({
					"href":		path,
					"media":	"all",
					"rel":		"stylesheet",
					"type":		"text/css"
				}).appendTo("head");
			}

			return true;
		},

		/**
		 * Search path to js file
		 */
		findPath: function (baseFile) {
			baseFile = baseFile.replace(/\./g, "\\.");

			var collection = $("script"),
				reg = eval("/^(.*)" + baseFile + "$/"),
				i,
				p;

			for (i = 0; i < collection.length; i += 1) {
				p = reg.exec(collection[i].src);

				if (null !== p) {
					return p[1];
				}
			}

			return null;
		},

		/**
		 * Include necessary JavaScript file
		 */
		js: function (file, options) {
			var collection = $("script"),
				path = options.basePath + options.jsPath + file,
				i;

			for (i = 0; i < collection.length; i += 1) {
				if (path === collection[i].src) {
					// is loaded
					return true;
				}
			}

			// When local used in Firefox got [Exception... "Access to restricted URI denied" code: "1012"]
			$.ajax({
				url: path,
				dataType: "script",
				success: function (data, textStatus, XMLHttpRequest) {
					if (options.successCallback) {
						options.successCallback();
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					if (console) {
						console.log(XMLHttpRequest, textStatus, errorThrown);
					}
				}
			});
			return true;
		}
	};

	/*
	 * Autoload namespace: public properties and methods
	 */
	$.autoload = {
		css: function (names, options) {
			var basePath = Autoload.findPath(options.baseFile),
				cssPath = (undefined === options.cssPath) ? "css/" : options.cssPath,
				i;

			options = {"basePath": basePath, "cssPath": cssPath};

			if ("string" === typeof names) {
				names = [names];
			}

			for (i = 0; i < names.length; i += 1) {
				Autoload.css(names[i], options);
			}
		},

		js: function (names, options) {
			var i;

			options.basePath = Autoload.findPath(options.baseFile);
			options.jsPath = (undefined === options.jsPath) ? "plugins/" : options.jsPath;

			if ("string" === typeof names) {
				names = [names];
			}

			for (i = 0; i < names.length; i += 1) {
				Autoload.js(names[i], options);
			}
		}
	};

	//$.wysiwyg.autoload.init();

})(jQuery);