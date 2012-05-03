============
Contributing
============

Feel free to contribute everywhere

.. contents::

Code
----

Read document `help/docs/code_style.rst <code_style.rst>`_

Internationalization
--------------------

You can contribute in internationalization. Go under *i18n* directory and copy
lang.\ **en**\ .js file. Rename **langName** part to your language.
Open this file, and change following lines

.. parsed-literal::


    if (undefined === $.wysiwyg) {
        throw "lang.\ **langName**\ .js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
        throw "lang.\ **langName**\ .js depends on $.wysiwyg.i18n";
    }
    
    $.wysiwyg.i18n.lang.\ **langName**\  = {
        controls: {
            ...
        },
        
        dialogs: {
            ...
        }
    }

to your language then translate control tooltips and dialog messages.

Plugins
-------

Read document `help/docs/plugins.rst <plugins.rst>`_

UML scheme
----------

Read document `help/docs/get_dia.rst <get_dia.rst>`_ and take a look at picture
`help/docs/scheme.png <scheme.png>`_