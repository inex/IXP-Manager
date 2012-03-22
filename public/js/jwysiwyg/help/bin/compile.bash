#!/bin/bash

#
# Compile & minimize
#

NAME=$(basename $0)
JWYSIWYG_ROOT_DIR=$PWD/$(dirname $0)/../..
JWYSIWYG_BIN_DIR=$JWYSIWYG_ROOT_DIR/help/bin
JWYSIWYG_OUTFILE="jquery.wysiwyg.full.js"
UGLIFYJS_API_URL="http://marijnhaverbeke.nl/uglifyjs"

jwysiwyg_help() {
	echo
	echo "jWYSIWYG compile"
	echo
	echo "Usage: $NAME full [outfile] </path/to/compile.conf"
	echo
	echo "Compile all js files into new one"
	echo "    (default: jquery.wysiwyg.full.js)"
	echo "and try to compress it with UglifyJS through HTTP API using curl"
	echo "    (default: jquery.wysiwyg.full.min.js)"
	echo
}

jwysiwyg_help_compressor() {
	echo
	echo "To minimize file:"
	echo "1. Install curl"
	echo
}

jwysiwyg_title() {
	echo "//=================" >> $2
	echo "//" >> $2
	echo "// File: $1" >> $2
	echo "//" >> $2
	echo "//=================" >> $2
	echo -e "\n" >> $2
	cat $1 >> $2
	echo -e "\n\n\n" >> $2
}

case $1 in
	full)
		outfile=$JWYSIWYG_OUTFILE
		if [ -n "$2" ]; then
			outfile="$2"
		fi

		outfile=$JWYSIWYG_ROOT_DIR/$outfile
		echo -e "Using $outfile"
		
		if [ ! -e "$outfile" ]; then
			echo "File $outfile not exists. Create..."
			touch $outfile
		else
			echo "File $outfile exists. Clear..."
			echo > $outfile
		fi

		echo "Read config..."
		while read path; do
			echo "+ Read $path..."
			for filename in $(find $JWYSIWYG_ROOT_DIR/$path); do
				jwysiwyg_title $filename $outfile
			done
		done

		minified=${outfile%"js"}min.js

		if [ ! -e "$minified" ]; then
			echo "File $minified not exists. Create..."
			touch $minified
		else
			echo "File $minified exists. Clear..."
			echo > $minified
		fi

		curl -f -X POST --data-urlencode js_code@$outfile -o $minified $UGLIFYJS_API_URL

		echo "Done"
	;;
	help)
		jwysiwyg_help
		exit 0
	;;
	*)
		jwysiwyg_help
		exit 1
	;;
esac