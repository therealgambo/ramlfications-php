<?php

namespace TheRealGambo\Ramlfications\Test;

use Symfony\Component\Yaml\Yaml;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Parser;

class MediaTypeInheritanceTest extends \PHPUnit_Framework_TestCase
{
    private $raml;

    /** @var RootNode $valid */
    private $valid;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

//        $this->raml = Yaml::parse(file_get_contents(__DIR__ . '/raml/1.0/mediatype-inheritance.raml'));
//        $parser = new Parser();
//        $this->valid = $parser->parseRaml($this->raml);
//        $this->valid->validate();
    }
//
//    public function testMediaTypeInheritanceFromRootNode()
//    {
//        $route = $this->valid->getResourceByPathAndMethod('/rootmediatype', 'get');
//        $this->assertArraySubset(['application/json'], $route->getMediaType());
//    }
//
//    public function testMediaTypeInheritanceFromMethod()
//    {
//        $route = $this->valid->getResourceByPathAndMethod('/resource1', 'get');
//        $this->assertArraySubset(['text/html'], $route->getMediaType(), false, print_r($route->getMediaType(), true));
//    }
//
//    public function testMediaTypeInheritanceFromResource()
//    {
//        $route = $this->valid->getResourceByPath('/resource');
//        $this->assertArraySubset(['application/xml', 'text/html'], $route->getMediaType(), false, print_r($route->getMediaType(), true));
//    }
//
//    public function testMediaTypeInheritedFromResourceForMethod()
//    {
//        $route = $this->valid->getResourceByPathAndMethod('/resource1', 'post');
//        $this->assertArraySubset(['application/xml', 'text/html', 'application/json'], $route->getMediaType(), false, print_r($route->getMediaType(), true));
//
//        $route = $this->valid->getResourceByPathAndMethod('/resource1', 'get');
//        $this->assertArraySubset(['text/html'], $route->getMediaType(), false, print_r($route->getMediaType(), true));
//    }
//
//    public function testMediaTypeInheritedFromParentReturnsFromRootNode()
//    {
//        $child  = $this->valid->getResourceByPathAndMethod('/resource1/parent', 'get');
//        $this->assertArraySubset(['application/json'], $child->getMediaType(), false, print_r($child->getMediaType(), true));
//    }
}