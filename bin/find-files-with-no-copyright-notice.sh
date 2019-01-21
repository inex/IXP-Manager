#!/bin/sh

find . -type f \
	\! -path ./.\*				\
	\! -path ./resources/\*			\
	\! -path \*/library/OSS/\*		\
	\! -path \*/jwysiwyg/\*			\
	\! -path \*/database/Proxies/\*		\
	\! -path \*/database/xml/\*		\
	\! -path \*/public/fonts/\*		\
	\! -name \*.foil.php			\
	\! -name \*.png				\
	\! -name \*.gif				\
	\! -name \*.jpg				\
	\! -name \*.ttf				\
	\! -name \*.svg				\
	\! -name \*.pdf				\
	\! -name \*.log				\
	\! -name .gitignore			\
	\! -name .hellogit			\
	\! -name README.md			\
	-print0	|				\
	xargs -0 grep -Li 'copyright.*internet neutral exchange'
