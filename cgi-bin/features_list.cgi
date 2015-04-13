#!/usr/bin/perl
use CGI qw(:standard);
use CGI::Carp qw( fatalsToBrowser );
use strict;
use Bio::Perl;

my @sequences = read_all_sequences("common_features_db/common_features.fasta");

print "Content-type: text/html\n\n";
print 
"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Common Plasmid Features</title>
	</head>
	<body>";

print "<p>The core of these sequences was obtained from <a href='http://www.addgene.org/plfeatures.html'>AddGene</a>, who obtained a portion of their sequences from the <a href='http://wishart.biology.ualberta.ca/PlasMapper/'>PlasMapper</a> website (See Xiaoli Dong, Paul Stothard, Ian J. Forsythe, and David S. Wishart, PlasMapper: a web server for drawing and auto-annotating plasmid maps, Nucleic Acids Res. 2004 Jul 1;32(Web Server issue):W660-4.) </p>
		<p>If you have any suggested additions/corrections please <a href='mailto:admin\@rf-cloning.org'>let me know!!</a></p>";	
	
foreach (@sequences)
	{
	print $_->display_id."<br />";
	print $_->seq."<br /><br />";
	}


print	"</body>";