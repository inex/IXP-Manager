#! /usr/bin/env bash

# This file will set up Git / SVN externals in library/


# Is SVN installed and in the path?

svn &>/dev/null

if [[ $? -eq 127 ]]; then
    echo ERROR: SVN not installed or not in the path
    exit
fi

git &>/dev/null

if [[ $? -eq 127 ]]; then
    echo ERROR: Git not installed or not in the path
    exit
fi

LIBDIR=`dirname "$0"`/../library
TOPDIR=`dirname "$0"`/..

# Smarty

if [[ -e $LIBDIR/Smarty ]]; then
    echo Smarty exists - skipping!
else
    svn co http://smarty-php.googlecode.com/svn/trunk/distribution/libs/ $LIBDIR/Smarty
fi


# Twitter form decorators
if [[ -e $LIBDIR/Bootstrap-Zend-Framework ]]; then
    echo Bootstrap-Zend-Framework exists - skipping!
else
    git clone git://github.com/inex/Bootstrap-Zend-Framework.git $LIBDIR/Bootstrap-Zend-Framework
fi

# Minifier
if [[ -e $LIBDIR/Minify ]]; then
    echo Minify exists - skipping!
else
    git clone git://github.com/opensolutions/Minify.git $LIBDIR/Minify
fi

# Bootbox
if [[ -e $LIBDIR/Bootbox ]]; then
    echo Bootbox exists - skipping!
else
    git clone https://github.com/makeusabrew/bootbox.git $LIBDIR/Bootbox
fi

# Throbber.js
if [[ -e $LIBDIR/Throbber.js ]]; then
    echo Throbber.js exists - skipping!
else
    git clone https://github.com/aino/throbber.js.git $LIBDIR/Throbber.js
fi


# Zend

if [[ -e $LIBDIR/Zend ]]; then
    echo Zend exists - skipping!
else 
    svn co http://framework.zend.com/svn/framework/standard/branches/release-1.12/library/Zend/ $LIBDIR/Zend
fi 
        

# OSS-Framework
if [[ -e $LIBDIR/OSS-Framework.git ]]; then
    echo OSS-Framework.git exists - skipping!
else
    git clone git://github.com/opensolutions/OSS-Framework.git $LIBDIR/OSS-Framework.git
fi

        
# Doctrine2

# INSTALL VIA PEAR - http://www.doctrine-project.org/projects/orm.html

