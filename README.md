# dmnodes
PHP Data Mining libraries

The initial commit has been made. No optimization or improvments haven't been made yet.
Some improvments will be made soon.

Only K-Means algorithm yet implemented on top of DMNodes (KMP)

Directories structure:
	* DBstore - Libraries related to DataTables and DataBases working. 
	  In common sense it is Source-working Libs
	* includes - just files for a simple inclusion of exact algorithm
	* samples - dir of simple samples, mostly in text/plain format
	* Utilities - Some extra libraries
	
The current version have only TxtTemplate database libs. Other Sources will be added later.

Simplest way to check algorithm:

	// Defines basic dir (current dir)
	define(HERE, __DIR__);

	// Includes all needed for K-Means algorithm
	include 'includes/kmp.include';
	
	// Creating an Object-Source (TxtTemplate)
	$store = new StoreText('TxtTemplate');
	// Retrieving data from source (define the file or database from where to obtain data)
	$store->retrieveData('samples/weightheight.txt');
	// Creating Object-Algorithm and provide an array of data from Object-Source (Will be deprecated soon)
	$tree = new KMP($store->toArray());
	// Analyse data by algorithm and print the resulting array
	print_r($tree->buildMap());

Please keep in mind that this version is basicly academic.
From version 0.1.0 will be more effective algorithms and libs.

Data retrieved from source fully loading into a memory, till the version 0.1.0. 
So unable to analyse BigData.

All good improvements from 0.1.0
