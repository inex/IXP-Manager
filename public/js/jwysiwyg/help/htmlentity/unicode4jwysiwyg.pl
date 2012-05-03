#!/usr/bin/env perl
use strict;
use warnings;

use Readonly;
use FindBin qw( $Bin );
use File::Spec qw();
use Text::CSV;
use IO::File;
use JSON::XS;

use feature ':5.10';

## These four elements are also valid in XML
## &apos btw is only valid in XML, and is not an HTML entity
## So it works for XHTML documents, but not for HTML/SGML documents
Readonly my @VALID_ESCAPES  => qw[ amp lt gt quot ];
# http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Character_entity_references_in_HTML
Readonly my $UNICODE_DB     => File::Spec->catfile($Bin, 'db.csv');
# Reserved words of JavaScript should be quoted in Hash
Readonly my @RESERVED_WORDS => qw[ int ];

my $fh = IO::File->new($UNICODE_DB);
my $csv = Text::CSV->new({ binary=>1, sep_char=>"\t", quote_char=>undef, escape_char=>undef });
$csv->column_names( map lc, @{ $csv->getline($fh) } );

## We'll create our own pseudo element for non-matches
my $db = { __replacement => int 65533 };
while ((my $row = $csv->getline_hr($fh))) {
	my ($unicode, $dec) = $row->{'unicode code point (decimal)'} =~ m/ (\S+) \s+ \( (\S+) \) /x;
	$db->{$row->{name}} = int $dec;
}

delete $db->{$_} for @VALID_ESCAPES;

my $json = JSON::XS->new->utf8->encode($db);

## Actually we need not true JSON, but JavaScript valid hash
$json =~ tr/"//d;

for my $word (@RESERVED_WORDS)
{
	my $escaped_word = quotemeta($word);
	$json =~ s/\b$escaped_word\:/"$word":/;
}

print "$json\n";

# Paste in jwysiwyg and you're done.

