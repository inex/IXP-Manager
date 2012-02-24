#!/bin/bash

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
                            