#!/bin/bash

# This file will set up SVN / Git externals in library/

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

cd $LIBDIR/Bootstrap-Zend-Framework
git pull
cd -

cd $LIBDIR/Minify
git pull
cd -

cd $LIBDIR/Bootbox
git pull
cd -

for name in Smarty Zend Doctrine; do
    echo -e "\n\n\n\n\n-------------\n\nUpdating $name..."
    cd $LIBDIR/$name
    svn up
    cd -
done

