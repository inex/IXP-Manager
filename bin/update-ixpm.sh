#!/bin/sh

## TODO:
##   * Perhaps add update_submodules(), restart_memcached(), restart_php5fpm(),
##     etc (as option-flags?).
##   * Implement a snapshot() phase at the beginning, which backs up files and
##     DB to a temp-archive and alerts the user to the files' location before
##     doing the other phases, if in no-dry-run mode.
##   * update_schemas(): the fast and silly way would be to maintain a schema
##     change instructions file and to grok it based on the commits, but the
##     better way would be to use the doctrine tool in "do it" mode for
##     $dryrun=1 mode and in "show the instructions" mode for $dryrun=0, the
##     way INEX do when they create the db instructions for the instructions
##     accompanying each release.
##   * Over time update_dot_dists() and update_skin() have become *almost* the
##     same (with some key differences). Factor common code out into a single
##     function to make it more DRY.

set -e

usage() {
	cat <<EOH
Usage: update-ixpm.sh OPTIONS [--] [new-commit-or-tag-or-branch]

OPTIONS:
 --help, -h            : This message.
 --no-dry-run, -n      : Don't just output what would be done, actually do it.
 --phases, -p "x"      : Space-separated list of phases to run (from "schemas",
                         "dot_dists", "skin", def: "schemas dot_dists skin").
 --from-commit, -f "x" : Commit/tag/branch to begin update from (def: HEAD).
 --skin-name, -s "x"   : Skin name (e.g. inex, def: <empty>=skip phase).
 --diff-editor, -d "x" : "diff-edit" tool (e.g. meld, def: <empty>).

The default new-commit-or-tag-or-branch if unspecified is "master", meaning the
update will be made up to the master branch's HEAD.

WARNING: ALWAYS do a dry-run before doing a for-real run (with -n) if at all,
         to check that it will behave sanely, and even then it is recommended
         to backup all files and take a database dump beforehand too...
EOH
}

# presets
basedir="$(readlink -e "$(pwd)")" || { printf "Couldn't deduce present directory. Aborting.\n" >&2; exit 1; }
dryrun=1
phases="schemas dot_dists skin"
fromcommit=HEAD
skinname=
diffeditor=meld

# getopts/args
while test $# -gt 0; do
	case "$1" in
	--help|-h)
		usage
		exit 0
		;;
	--no-dry-run|-n)
		dryrun=0
		shift
		continue
		;;
	--phases|-p)
		phases="$2"
		shift 2
		continue
		;;
	--from-commit|-f)
		fromcommit="$2"
		shift 2
		continue
		;;
	--skin-name|-s)
		skinname="$2"
		shift 2
		continue
		;;
	--diff-editor|-d)
		diffeditor="$2"
		shift 2
		continue
		;;
	--)
		shift
		break
		;;
	-*)
		usage >&2
		printf 'Unknown option specified. Aborting.\n' >&2
		exit 1
		;;
	*)
		break
		;;
	esac
done
tocommit="${1:-master}"

# sanity checks
fromcommitstamp="`git show --format=%ct "$fromcommit" | head -n 1`"
tocommitstamp="`git show --format=%ct "$tocommit" | head -n 1`"
if test $fromcommitstamp -gt $tocommitstamp; then
	printf 'Impossible to update to an older commit than the present one. Aborting.\n' >&2
	exit 1
fi
if test -n "`printf '%s' "$phases" | sed -e 's/\<\(schemas\|dot_dists\|skin\)\>\|[ \t]//g' || true`"; then
	printf '$phases has unrecognised values. Aborting.\n' >&2
	exit 1
fi
if test -n "$skinname"; then
	if ! test -d "application/views/_skins/$skinname" || ! test -r "application/views/_skins/$skinname" || ! test -x "application/views/_skins/$skinname"; then
		printf 'There is no readable, executable directory for skin name "%s". Aborting.\n' "$skinname" >&2
		exit 1
	fi
	(
		cd application/modules
		for modulename in *; do
			if test -d "${modulename}/views/_skins/$skinname" && ! { test -r "${modulename}/views/_skins/$skinname" && test -x "${modulename}/views/_skins/$skinname"; }; then
				printf 'The skin directory for module "%s" and skin name "%s" is not readable or not executable. Aborting.\n' "$modulename" "$skinname" >&2
				exit 1
			fi
		done
	)
fi
if test $dryrun -eq 0 && printf '%s' "$phases" | grep -q '\<dot_dists\|skin\>'; then
	if test -z "$diffeditor" || ! test -x "`which "$diffeditor" 2>/dev/null || true`"; then
		printf 'Unable to find "%s" as an executable in the $PATH. Aborting.\n' "$diffeditor" >&2
		exit 1
	fi
fi

# functions

maint_mode_on() {
	printf '** Starting maint_mode_on().\n' >&2
	cd "$basedir"
	! test $dryrun -eq 0 || touch MAINT_MODE_ENABLED
	printf '== Maintenance mode on.\n\n' >&2
}

maint_mode_off() {
	printf '** Starting maint_mode_off().\n' >&2
	cd "$basedir"
	! test $dryrun -eq 0 || rm MAINT_MODE_ENABLED
	printf '== Maintenance mode off.\n\n' >&2
}

update_schemas() {
	printf '** Starting update_schemas().\n' >&2
	cd "$basedir"
	cat <<EOM
TODO: The update_schemas() function is yet to be implemented. For now it must
      still be done manually.
EOM
	if test $dryrun -eq 0; then
		cat <<EOM
      Entering a sub-shell for you to do that now. When finished exit the shell
      (type "exit") and this script will continue.
EOM
		${SHELL:-/bin/sh}
	fi
	printf '== Database schemas updated.\n\n' >&2
}

update_dot_dists() {
	printf '** Starting update_dot_dists().\n' >&2
	cd "$basedir"
	filelist="$(git diff --name-only "$fromcommit" "$tocommit" | grep '\.dist$\|\.dist\.php$' || true)"
	test $dryrun -eq 0 || printf 'Files whose .dist equivalents have changed:\n'
	printf '%s\n' "$filelist" | \
	 {
		hasnewfile=0
		while read filename; do
			newfilename="$(printf '%s' "$filename" | sed -e 's/\.dist\.php$/\.php/' -e 's/\.dist$//' || true)"
			if test -e "$newfilename"; then
				if test $dryrun -eq 0; then
					if ! test -e "$filename"; then
						printf '"%s" deleted, should I delete "%s"? ' "$filename" "$newfilename"
						read response
						case "$response" in
						y|Y) rm -f "$newfilename";;
						*) printf 'Not deleting "%s".\n' "$newfilename";;
						esac
					else
						"$diffeditor" "$filename" "$newfilename" || true
					fi
				else
					printf '%s\n' "$newfilename"
					hasnewfile=1
				fi
			fi
		done
		test $dryrun -eq 0 || test $hasnewfile -eq 1 || printf '[ NONE ]\n'
	 }
	if printf '%s' "$filelist" | grep -q '^bin/fixtures.php.dist$'; then
		cat >&2 <<EOM
== bin/fixtures.php.dist has been updated. The file derived from this is used
   for initial database setup among other things. Hopefully the schema updates
   with each update have kept this in sync, but if you want to double-check it
   can't hurt, if you know what you're doing...
EOM
	fi
	printf '== Files derived from .dist files updated.\n\n' >&2
}

update_skin() {
	printf '** Starting update_skin().\n' >&2
	cd "$basedir"
	if test -n "$skinname"; then
		filelist="$(git diff --name-only "$fromcommit" "$tocommit" | grep 'application\(/modules/[^/]\+\)\?/views/[^_]' || true)"
		test $dryrun -eq 0 || printf 'Skin files whose base equivalents have changed:\n'
		printf '%s\n' "$filelist" | \
		 {
			hasnewfile=0
			while read filename; do
				newfilename="$(printf '%s' "$filename" | sed -e "s:^application\\(/modules/[^/]\\+\\)\\?/views/:application\\1/views/_skins/${skinname}/:" || true)"
				if test -e "$newfilename"; then
					if test $dryrun -eq 0; then
						if ! test -e "$filename"; then
							printf '"%s" deleted, should I delete "%s"? ' "$filename" "$newfilename"
							read response
							case "$response" in
							y|Y) rm -f "$newfilename";;
							*) printf 'Not deleting "%s".\n' "$newfilename";;
							esac
						else
							"$diffeditor" "$filename" "$newfilename" || true
						fi
					else
						printf '%s\n' "$newfilename"
						hasnewfile=1
					fi
				fi
			 done
			test $dryrun -eq 0 || test $hasnewfile -eq 1 || printf '[ NONE ]\n'
		 }
		printf '== Skin files updated.\n\n' >&2
	else
		printf '== Skin name empty. Skipped updating skin files.\n\n' >&2
	fi
}

# main
maint_mode_on
for phase in $phases; do update_$phase; done
maint_mode_off
