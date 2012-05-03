============================
Programming Style Guidelines
============================

.. contents::

Naming Conventions
------------------

* Iterator variables should be called i, j, k etc.
* The prefix is should be used for boolean variables and methods ::
    
        isSet, isVisible, isFinished, isFound, isOpen

    There are a few alternatives to the is prefix that fit better in some situations.
    These are the has, can and should prefixes::

        bool hasLicense();
        bool canEvaluate();
        bool shouldSort();

* Negated boolean variable names must be avoided ::

        bool isError; // NOT: isNoError
        bool isFound; // NOT: isNotFound

* Exception classes should be suffixed with Exception ::

        AccessException

Files
-----

* bla bla bla


Statements
----------

* Variables should be initialized where they are declared
* Use of global variables should be minimized???
* do-while loops can be avoided???


Layout
------

* Basic indentation should be 4???


Comments
--------

* Comments should be included relative to their position in the code ::

        if (true) {
            // Do something
            something();
        }

* Class and method header comments should follow the JavaDoc conventions


Plugin Namespace
----------------

::

    element.bind("action" + ".wysiwyg");
    element.unbind(".wysiwyg");
