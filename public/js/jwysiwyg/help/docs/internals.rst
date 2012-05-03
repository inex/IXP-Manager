=========
Internals
=========

.. contents::

Workflow
========

::

    // example.html
    $("textarea").wysiwyg();

Wysiwyg.init
    Wysiwyg.initFrame
        Try to switch on this.editorDoc.designMode (Wysiwyg.designMode)

Wysiwyg object
==============

Properties
----------

editor
    jQuery("iframe") object

editorDoc
    iframe.document element

element
    jQuery("<div class="wysiwyg">") object

original
    textarea element provided by $("textarea").wysiwyg()

ui
    UI related properties and methods

ui.toolbar
    jQuery("<ul class="toolbar">") object

ui.self
    link to parent object (Wysiwyg)

Methods
-------

init
    create new instance of Wysiwyg object

TODO

$.wysiwyg object
================

Defines several methods::

    $.wysiwyg.methodName($(selector), args)
    $.wysiwyg.pluginName.methodName($(selector), args)

Also provides method to register your own plugins::

    $.wysiwyg.plugin.register(YourPlugin)

To learn more about plugins read `help/docs/plugins.rst <plugins.rst>`_

$.fn.wysiwyg object
===================

Provides method to wrap around $.wysiwyg object, that provide ability to call
its methods in another way::

    $(selector).wysiwyg("methodName", args)
    $(selector).wysiwyg("pluginName.methodName", args)

