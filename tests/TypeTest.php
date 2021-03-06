<?php

namespace TheRealGambo\Ramlfications\Test;

use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Parser;

class TypeTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    private function loadRaml(string $file): RootNode
    {
        $parser = new Parser();
        $data = $parser->parseFile($file, __DIR__ . '/raml/1.0/types/');
        $raml = $parser->parseRaml($data);
        return $raml;
    }

    /** @test */
    public function shouldCorrectlyValidateCorrectType()
    {
        $raml     = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method   = $resource->getMethod('get');
        $response = $method->getResponse(200);
        $body     = $response->getBodyByType('application/json');
        $type     = $body->getType();
        $type->validate(json_decode('{"title":"Good Song","artist":"An artist"}', true));

//        $this->assertTrue($type->isValid());
    }

    /** @test */
    public function shouldCorrectlyValidateCorrectTypeMissingUnrequired()
    {
        $raml     = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method   = $resource->getMethod('get');
        $response = $method->getResponse(200);
        $body     = $response->getBodyByType('application/json');
        $type     = $body->getType();
        $type->validate(json_decode('{"title":"Good Song"}', true));

//        $this->assertTrue($type->isValid());
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeMissingRequiredPropertyException
     */
    public function shouldCorrectlyValidateCorrectTypeMissingRequired()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(200);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"artist":"An artist"}', true));
        } catch (\Exception $e) {
//            $this->assertFalse($type->isValid());
            throw $e;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeMissingRequiredPropertyException
     */
    public function shouldCorrectlyValidateIncorrectType()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(200);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate([]);
        } catch (\Exception $e) {
            $this->assertEquals(
                'Missing required property: "title"',
                $e->getMessage(),
                get_class($e)
            );
            throw $e;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeAdditionalPropertyException
     */
    public function shouldCorrectlyValidateAdditionalProperties()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(200);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"title": "Good Song", "duration":"3:09"}', true));
        } catch (\Exception $e) {
//            $this->assertFalse($type->isValid());
            throw $e;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException
     */
    public function shouldCorrectlyValidateNullTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(204);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"var": null}', true));

        try {
            $type->validate(json_decode('{"var": 10}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected null for property \'var\', got (integer) "10"',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateRightDateTimeOnlyTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(205);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"datetimeOnly": "2017-12-07T15:50:48"}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException
     */
    public function shouldCorrectlyValidateWrongDateTimeOnlyTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(205);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"datetimeOnly": "2017-12 15:50:48"}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected datetime-only for property datetimeOnly, got (string) "2017-12 15:50:48"',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateRightDateOnlyTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(205);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"dateOnly": "2016-02-28"}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidValueForTypeException
     */
    public function shouldCorrectlyValidateWrongDateOnlyTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(205);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"dateOnly": "2017-12-07T15:50:48"}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected date-only for property dateOnly, got (string) "2017-12-07T15:50:48"',
                $e->getMessage());
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateArrayIntegerRightTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"intArray": [1,2,3]}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArrayValueTypeException
     */
    public function shouldCorrectlyValidateArrayIntegerWrongTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"intArray": [1,2,"str"]}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected array element type intArray, got (string) "str"',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateArrayStringRightTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"strArray": ["one", "two"]}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArrayValueTypeException
     */
    public function shouldCorrectlyValidateArrayStringWrongTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"strArray": [1, "two"]}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected array element type strArray, got (integer) "1"',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateArrayBooleanRightTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"boolArray": [true, false]}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArrayValueTypeException
     */
    public function shouldCorrectlyValidateArrayBooleanWrongTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"boolArray": [true, 0]}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected array element type boolArray, got (integer) "0"',
                $e->getMessage()
            );
            throw $e;
        }
    }

    /** @test */
    public function shouldCorrectlyValidateArrayNumberRightTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        $type->validate(json_decode('{"numberArray": [12, 13.5, 0]}', true));
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\Types\TypeInvalidArrayValueTypeException
     */
    public function shouldCorrectlyValidateArrayNumberWrongTypes()
    {
        $raml = $this->loadRaml('simple.raml');
        $resource = $raml->getResourceByPath('/songs');
        $method = $resource->getMethod('get');
        $response = $method->getResponse(206);
        $body = $response->getBodyByType('application/json');
        $type = $body->getType();

        try {
            $type->validate(json_decode('{"numberArray": ["12", 0]}', true));
        } catch (\Exception $e) {
            $this->assertEquals(
                'Expected array element type numberArray, got (string) "12"',
                $e->getMessage()
            );
            throw $e;
        }
    }
}
