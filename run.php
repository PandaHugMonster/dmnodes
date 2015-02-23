#!/bin/php
<?php

define(HERE, __DIR__);
include 'includes/kmp.include';

$store = new StoreText('TxtTemplate');

$store->retrieveData('samples/weightheight.txt');

$tree = new KMP($store->toArray());

print_r($tree->buildMap());