<?php

require_once __DIR__ . '/../vendor/autoload.php';

//$raml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(__DIR__ . '/raml/1.0/complete-rootnode-valid.raml'));
//$parser = new \TheRealGambo\Ramlfications\Parser();
//$valid = $parser->parseRaml($raml);
//$valid->validate();
//
//$r = $valid->getResources();
//$p = [];
///** @var \TheRealGambo\Ramlfications\Nodes\ResourceNode $v **/
//foreach ($r as $k => $v) {
//    $p[$k] = $v->getAbsoluteUri();
//}

$parser = new \TheRealGambo\Ramlfications\Parser();
$data = $parser->parseFile( '/raml/1.0/bookstore/api.raml', __DIR__);
$raml = $parser->parseRaml($data);
//$data = $parser->parseUrl('https://raw.githubusercontent.com/spotify/ramlfications/master/tests/data/examples/cyclic_includes.raml');
//print_r($data);