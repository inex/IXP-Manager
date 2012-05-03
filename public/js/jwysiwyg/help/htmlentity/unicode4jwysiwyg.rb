#!/usr/bin/env ruby
# encoding: utf-8

# ruby version of Unicode table converter

require 'rubygems'
begin
  require 'bundler/setup'
rescue LoadError
  puts "You should install Bundler using 'gem install bundler' and run 'bundle install'"
end

require 'yajl'
require 'fastercsv'

## These four elements are also valid in XML
## &apos btw is only valid in XML, and is not an HTML entity
## So it works for XHTML documents, but not for HTML/SGML documents
VALID_ESCAPES  = %w[ amp lt gt quot ].freeze
# http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Character_entity_references_in_HTML
UNICODE_DB     = File.expand_path('./db.csv', File.dirname(__FILE__)).freeze
# Reserved words of JavaScript should be quoted in Hash
RESERVED_WORDS = %w[ int ].freeze

csv = FasterCSV.open(
  UNICODE_DB,
  'rb',
  :col_sep => "\t",
  :row_sep => "\n",
  :quote_char => '`',
  :headers => :first_row
)

## We'll create our own pseudo element for non-matches
db = { '__replacement' => 65533 }

csv.each do |row|
  row['Unicode code point (decimal)'] =~ /(\S+)\s+\((\S+)\)/
  uch, dec, name = $1, $2.to_i, row['Name']
  next if VALID_ESCAPES.include?(name)
  db[name] = dec
end

json = Yajl::Encoder.encode(db)

## Actually we need not true JSON, but JavaScript valid hash
json.gsub!('"', '')

RESERVED_WORDS.each { |word| json.sub!(/\b#{Regexp.escape(word)}\:/, "\"#{word}\":") }

puts json

# Paste in jwysiwyg and you're done.
