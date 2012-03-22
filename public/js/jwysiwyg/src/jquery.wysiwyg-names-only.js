/**
 * WYSIWYG - jQuery plugin 0.98 dev
 * (???)
 *
 * Copyright (c) 2008-2009 Juan M Martinez, 2010-2011 Akzhan Abdulin and all contributors
 * https://github.com/akzhan/jwysiwyg
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 */

/*jslint browser: true, forin: true */

(function ($) {
	"use strict";

	var console = window.console ? window.console : {
		log: $.noop,
		error: function (msg) {
			$.error(msg);
		}
	};

	var supportsProp = (('prop' in $.fn) && ('removeProp' in $.fn));  // !(/^[01]\.[0-5](?:\.|$)/.test($.fn.jquery));

	// Big picture
	var Wysiwyg = {
		controls : {},             // shared controls
		defaults : {},
		dialogs  : {},
		dom      : {},
		editor   : {},
		plugins  : {},
		ui       : {},
		utils    : {},

		init     : function (object, options) {}, // call instance
		instance : function (options) {}, // create new object

		activeEditor: null,        // References the active editor instance, useful for having a global toolbar.
		console: console,          // Let our console be available for extensions
		instances: []              // Collection
	};

	// Detailed overview
	Wysiwyg.init = function (object, options) {
		var instance = new this.instance(options);

		object.data("wysiwyg", instance);
		this.instances.push(instance);
		
		return instance;
	};

	Wysiwyg.instance = function (options) {
		console.log(options);
	};

	Wysiwyg.controls = {
		
	};

	Wysiwyg.defaults = {
			
	};

	Wysiwyg.dialogs = {
			
	};

	Wysiwyg.dom = {
			
	};

	Wysiwyg.editor = {
			
	};

	Wysiwyg.plugins = {
			
	};

	Wysiwyg.ui = {
			
	};

	Wysiwyg.utils = {
			
	};

	var WysiwygOld = function () {
		this.editor			= null;
		this.editorDoc		= null;
		this.element		= null;
		this.options		= {};
		this.original		= null;
		this.savedRange		= null;
		this.timers			= [];
		this.validKeyCodes	= [8, 9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46];

		this.isDestroyed	= false;

		this.dom = { // DOM related properties and methods
			ie:		{
			},
			w3c:	{
			}
		};
		this.dom.parent		= this;
		this.dom.ie.parent	= this.dom;
		this.dom.w3c.parent	= this.dom;

		this.ui			= {};	// UI related properties and methods
		this.ui.self	= this;
		this.ui.toolbar	= null;
		this.ui.initialHeight = null; // ui.grow

		this.dom.getAncestor = function (element, filterTagName) {};
		this.dom.getElement = function (filterTagName) {};
		this.dom.ie.getElement = function (filterTagName) {};
		this.dom.w3c.getElement = function (filterTagName) {};

		this.ui.addHoverClass = function () {};
		this.ui.appendControls = function () {};
		this.ui.appendItem = function (name, control) {};
		this.ui.appendItemCustom = function (name, control) {};
		this.ui.appendItemSeparator = function () {};
		this.autoSaveFunction = function () {};
		this.ui.checkTargets = function (element) {};
		this.ui.designMode = function () {};
		this.destroy = function () {};
		this.getRangeText = function () {};
		this.extendOptions = function (options) {};
		this.ui.focus = function () {};
		this.ui.returnRange = function () {};
		this.increaseFontSize = function () {};
		this.decreaseFontSize = function () {};
		this.getContent = function () {};

		this.events = {
			_events : {},
			bind : function (eventName, callback) {},
			trigger : function (eventName, args) {},
			filter : function (eventName, originalText) {}
		};

		this.getElementByAttributeValue = function (tagName, attributeName, attributeValue) {};
		this.getInternalRange = function () {};
		this.getInternalSelection = function () {};
		this.getRange = function () {};
		this.getSelection = function () {};
		this.ui.grow = function () {};
		this.init = function (element, options) {};
		this.ui.initFrame = function () {};
		this.innerDocument = function () {};
		this.insertHtml = function (szHTML) {};
		this.parseControls = function () {};
		this.removeFormat = function () {};
		this.ui.removeHoverClass = function () {};
		this.resetFunction = function () {};
		this.saveContent = function () {};
		this.setContent = function (newContent) {};
		this.triggerControl = function (name, control) {};
		this.triggerControlCallback = function (name) {};
		this.ui.withoutCss = function () {};
		this.wrapInitialContent = function () {};
	};

	/*
	 * jQuery layer
	 */
	$.wysiwyg = Wysiwyg;

	$.wysiwygOld = {
		messages: {},

		addControl: function (object, name, settings) {
			return object.each(function () {
				var oWysiwyg = $(this).data("wysiwyg"),
					customControl = {},
					toolbar;

				if (!oWysiwyg) {
					return this;
				}

				customControl[name] = $.extend(true, {visible: true, custom: true}, settings);
				$.extend(true, oWysiwyg.options.controls, customControl);

				// render new toolbar
				toolbar = $(oWysiwyg.options.toolbarHtml);
				oWysiwyg.ui.toolbar.replaceWith(toolbar);
				oWysiwyg.ui.toolbar = toolbar;
				oWysiwyg.ui.appendControls();
			});
		},

		clear: function (object) {
			return object.each(function () {
				oWysiwyg.setContent("");
			});
		},

		destroy: function (object) {
			return object.each(function () {
				oWysiwyg.destroy();
			});
		},

		"document": function (object) {
			return $(oWysiwyg.editorDoc);
		},

		getContent: function (object) {
			return oWysiwyg.getContent();
		},

		init: function (object, options) {
			return object.each(function () {
				var opts = $.extend(true, {}, options),
					obj;

				// :4fun:
				// remove this textarea validation and change line in this.saveContent function
				// $(this.original).val(content); to $(this.original).html(content);
				// now you can make WYSIWYG editor on h1, p, and many more tags
				if (("textarea" !== this.nodeName.toLowerCase()) || $(this).data("wysiwyg")) {
					return;
				}

				obj = new Wysiwyg();
				obj.init(this, opts);
				$.data(this, "wysiwyg", obj);

				$(obj.editorDoc).trigger("afterInit.wysiwyg");
			});
		},

		insertHtml: function (object, szHTML) {
			return object.each(function () {
				oWysiwyg.insertHtml(szHTML);
			});
		},

		plugin: {
			listeners: {},
			bind: function (Wysiwyg) {},
			exists: function (name) {},
			listen: function (action, handler) {},
			parseName: function (name) {},
			register: function (data) {}
		},

		removeFormat: function (object) {
			return object.each(function () {
				oWysiwyg.removeFormat();
			});
		},

		save: function (object) {
			return object.each(function () {
				oWysiwyg.saveContent();
			});
		},

		selectAll: function (object) {
			oBody = oWysiwyg.editorDoc.body;
			if (window.getSelection) {
				selection = oWysiwyg.getInternalSelection();
				selection.selectAllChildren(oBody);
			} else {
				oRange = oBody.createTextRange();
				oRange.moveToElementText(oBody);
				oRange.select();
			}
		},

		setContent: function (object, newContent) {
			return object.each(function () {
				oWysiwyg.setContent(newContent);
			});
		},

		triggerControl: function (object, controlName) {
			return object.each(function () {
				if (!oWysiwyg.controls[controlName]) {
					console.error("Control '" + controlName + "' not exists");
				}

				oWysiwyg.triggerControl.apply(oWysiwyg, [controlName, oWysiwyg.controls[controlName]]);
			});
		},

		support: {
			prop: supportsProp
		},

		utils: {
			extraSafeEntities: [["<", ">", "'", '"', " "], [32]],
			encodeEntities: function (str) {}
		}
	};

	$.fn.wysiwyg = function (method) {
		var args = arguments, plugin;

		if ("undefined" !== typeof $.wysiwyg[method]) {
			// set argument object to undefined
			args = Array.prototype.concat.call([args[0]], [this], Array.prototype.slice.call(args, 1));
			return $.wysiwyg[method].apply($.wysiwyg, Array.prototype.slice.call(args, 1));
		} else if ("object" === typeof method || !method) {
			Array.prototype.unshift.call(args, this);
			return $.wysiwyg.init.apply($.wysiwyg, args);
		} else if ($.wysiwyg.plugin.exists(method)) {
			plugin = $.wysiwyg.plugin.parseName(method);
			args = Array.prototype.concat.call([args[0]], [this], Array.prototype.slice.call(args, 1));
			return $.wysiwyg[plugin.name][plugin.method].apply($.wysiwyg[plugin.name], Array.prototype.slice.call(args, 1));
		} else {
			console.error("Method '" +  method + "' does not exist on jQuery.wysiwyg.\nTry to include some extra controls or plugins");
		}
	};

	$.fn.getWysiwyg = function () {
		return $.data(this, "wysiwyg");
	};
})(jQuery);
