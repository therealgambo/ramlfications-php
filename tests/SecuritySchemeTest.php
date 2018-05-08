<?php


namespace TheRealGambo\Ramlfications\Test;

use Symfony\Component\Yaml\Yaml;
use TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException;
use TheRealGambo\Ramlfications\Nodes\ResourceNode;
use TheRealGambo\Ramlfications\Nodes\RootNode;
use TheRealGambo\Ramlfications\Parameters\Content;
use TheRealGambo\Ramlfications\Parameters\Header;
use TheRealGambo\Ramlfications\Parameters\QueryParameter;
use TheRealGambo\Ramlfications\Parameters\Response;
use TheRealGambo\Ramlfications\Parameters\SecurityScheme;
use TheRealGambo\Ramlfications\Parser;

class SecuritySchemeTest extends \PHPUnit_Framework_TestCase
{
    /** @var RootNode $raml */
    private $raml;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->raml = $this->loadRaml('api.raml');
    }

    private function loadRaml(string $file): RootNode
    {
        $parser = new Parser();
        $data = $parser->parseFile($file, __DIR__ . '/raml/1.0/securityschemes/');
        $raml = $parser->parseRaml($data);
        return $raml;
    }

    public function testSecuritySchemes()
    {
        $this->assertCount(6, $this->raml->getSecuritySchemes());

        $basic   = $this->raml->getSecurityScheme('basic');
        $jwt     = $this->raml->getSecurityScheme('custom');
        $oauth20 = $this->raml->getSecurityScheme('oauth20');

        $this->assertInstanceOf(SecurityScheme::class, $basic);
        $this->assertInstanceOf(SecurityScheme::class, $jwt);
        $this->assertInstanceOf(SecurityScheme::class, $oauth20);
        $this->assertFalse($this->raml->getSecurityScheme('invalid'));
        $this->assertInstanceOf(RootNode::class, $basic->getRootNode());

    }

    public function testSecuritySchemeBasic()
    {
        /** @var SecurityScheme $basic */
        $basic = $this->raml->getSecurityScheme('basic');

        $this->assertEquals('basic', $basic->getName());
        $this->assertEquals('Basic Authentication', $basic->getType());
        $this->assertEquals('Basic Authentication', $basic->getDisplayName());
        $this->assertEquals('Basic Authentication via HTTP Authentication header', $basic->getDescription());

        /** @var  $describedBy */
        $describedBy = $basic->getDescribedBy();
        $this->assertNotNull($describedBy);

        $this->assertArrayHasKey('headers', $describedBy);

        /** @var Header $header */
        $header = $basic->getHeader('Authorization');
        $this->assertInstanceOf(Header::class, $header);
        $this->assertInstanceOf(Content::class, $header->getDescription());
        $this->assertEquals('Basic authentication required header name', $header->getDescription());
        $this->assertEquals('string', $header->getType());
        $this->assertTrue($header->getRequired());

        $this->assertCount(2, $basic->getResponses());
        $this->assertInstanceOf(Response::class, $basic->getResponse(401));
        $this->assertFalse($basic->getResponse(418));
    }

    public function testSecuritySchemeCustom()
    {
        /** @var SecurityScheme $jwt */
        $jwt = $this->raml->getSecurityScheme('custom');

        $this->assertEquals('custom', $jwt->getName());
        $this->assertEquals('x-custom', $jwt->getType());
        $this->assertEquals('JWT Authentication', $jwt->getDisplayName());
        $this->assertInstanceOf(Content::class, $jwt->getDescription());
        $this->assertEquals(
            'JWT Authentication using either Authorization header or access_token query parameter',
            $jwt->getDescription()->raw()
        );

        /** @var array $describedBy */
        $describedBy = $jwt->getDescribedBy();
        $this->assertNotNull($describedBy);
        $this->assertArrayHasKey('headers', $describedBy);
        $this->assertArrayHasKey('queryParameters', $describedBy);
        $this->assertArrayHasKey('responses', $describedBy);

        /** @var Header $header */
        $header = $jwt->getHeader('Authorization');
        $this->assertCount(1, $jwt->getHeaders());
        $this->assertInstanceOf(Header::class, $header);
        $this->assertInstanceOf(Content::class, $header->getDescription());
        $this->assertEquals('Used to send a valid JWT access token.', $header->getDescription()->raw());
        $this->assertEquals('string', $header->getType());
        $this->assertEquals('Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.XbPfbIHMI6arZ3Y922BhjWgQzWXcXNrz0ogtVhfEd2o', $header->getExample());
        $this->assertRegExp('/' . $header->getPattern() . '/', 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.XbPfbIHMI6arZ3Y922BhjWgQzWXcXNrz0ogtVhfEd2o');
        $this->assertFalse($header->getRequired());

        /** @var QueryParameter $query */
        $query = $jwt->getQueryParameterByKey('access_token');
        $this->assertCount(1, $jwt->getQueryParameters());
        $this->assertInstanceOf(QueryParameter::class, $query);
        $this->assertInstanceOf(Content::class, $query->getDescription());
        $this->assertEquals('Used to send a valid JWT access token.', $query->getDescription()->raw());
        $this->assertEquals('string', $query->getType());
        $this->assertEquals('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.XbPfbIHMI6arZ3Y922BhjWgQzWXcXNrz0ogtVhfEd2o', $query->getExample());
        $this->assertRegExp('/' . $query->getPattern() . '/', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.XbPfbIHMI6arZ3Y922BhjWgQzWXcXNrz0ogtVhfEd2o');
        $this->assertFalse($query->getRequired());

        $query->setRequired(true);
        $this->assertTrue($query->getRequired());

        $this->assertCount(2, $jwt->getResponses());
        $this->assertArrayHasKey(401, $jwt->getResponses());
        $this->assertArrayHasKey(403, $jwt->getResponses());

        /** @var Response $response401 */
        $response401 = $jwt->getResponse(401);
        $this->assertInstanceOf(Response::class, $response401);
        $this->assertEquals(
            'The request could not be processed because your authentication credentials are invalid.',
            $response401->getDescription()->raw()
        );
        $this->assertEquals(401, $response401->getCode());

        /** @var Response $response403 */
        $response403 = $jwt->getResponse(403);
        $this->assertInstanceOf(Response::class, $response403);
        $this->assertEquals(
            'The request could not be processed because you do not have access to this protected resource.',
            $response403->getDescription()->raw()
        );
//        $this->assertEquals(403, $response403->getCode());
//        $this->assertArrayHasKey('application/xml', $response403->getBody());
//        $this->assertEquals(
//            'application/xml',
//            $response403->getBodyByMimeType('application/xml')->getMimeType()
//        );
    }

    public function testSecuritySchemeOauth20()
    {
        /** @var SecurityScheme $oauth */
        $oauth = $this->raml->getSecurityScheme('oauth20');

        $this->assertEquals('oauth20', $oauth->getName());
        $this->assertEquals('OAuth 2.0', $oauth->getType());
        $this->assertEquals('OAuth 2.0 Authentication', $oauth->getDisplayName());
        $this->assertInstanceOf(Content::class, $oauth->getDescription());
        $this->assertEquals(
            'OAuth2 is a protocol that lets external apps request authorization to private
details in a user\'s account without getting their password. This is
preferred over Basic Authentication because tokens can be limited to specific
types of data, and can be revoked by users at any time.
',
            $oauth->getDescription()
        );

        /** @var array $describedBy */
        $describedBy = $oauth->getDescribedBy();
        $this->assertNotNull($describedBy);
        $this->assertArrayHasKey('headers', $describedBy);
        $this->assertArrayHasKey('queryParameters', $describedBy);
        $this->assertArrayHasKey('responses', $describedBy);

        /** @var Header $header */
        $header = $oauth->getHeader('Authorization');
        $this->assertCount(1, $oauth->getHeaders());
        $this->assertInstanceOf(Header::class, $header);
        $this->assertInstanceOf(Content::class, $header->getDescription());
        $this->assertEquals(
            'Used to send a valid OAuth 2 access token. Do not use
with the "access_token" query string parameter.
', $header->getDescription()->raw());
        $this->assertEquals('string', $header->getType());
        $this->assertFalse($header->getRequired());

        /** @var QueryParameter $query */
        $query = $oauth->getQueryParameterByKey('access_token');
        $this->assertCount(1, $oauth->getQueryParameters());
        $this->assertInstanceOf(QueryParameter::class, $query);
        $this->assertInstanceOf(Content::class, $query->getDescription());
        $this->assertEquals('Used to send a valid OAuth 2 access token. Do not use with
the "Authorization" header.
', $query->getDescription()->raw());
        $this->assertEquals('string', $query->getType());
        $this->assertFalse($query->getRequired());

        $query->setRequired(true);
        $this->assertTrue($query->getRequired());

        $responses = $oauth->getResponses();
        $this->assertCount(2, $responses);
        $this->assertArrayHasKey(401, $responses);
        $response401 = $oauth->getResponse(401);
        $this->assertEquals(
            'Bad or expired token. To fix, re-authenticate the user.',
            $response401->getDescription()
        );

        $this->assertCount(4, $oauth->getSettings());
        $this->assertArrayHasKey('authorizationUri', $oauth->getSettings());
        $this->assertEquals('https://api.example.com/v1/oauth2/authorize', $oauth->getSetting('authorizationUri'));
        $this->assertArrayHasKey('accessTokenUri', $oauth->getSettings());
        $this->assertEquals('https://api.example.com/v1/oauth2/token', $oauth->getSetting('accessTokenUri'));
        $this->assertArrayHasKey('authorizationGrants', $oauth->getSettings());
        $this->assertCount(2, $oauth->getSetting('authorizationGrants'));
        $this->assertArrayHasKey('scopes', $oauth->getSettings());
        $this->assertCount(9, $oauth->getSetting('scopes'));
    }
//
//    public function testRootNodeSecuredBy()
//    {
//        $schemes = $this->valid->getSecuredBy();
//
//        $this->assertCount(4, $schemes);
//        $this->assertContains('jwt', $schemes);
//        $this->assertContains('basic', $schemes);
//        $this->assertContains('oauth_2_0', $schemes);
//        $this->assertContains('null', $schemes);
//    }
//
//    public function testResourceNodeSecuredByRootNode()
//    {
//        $rootSecuredBy = $this->valid->getSecuredBy();
//        $this->assertCount(4, $rootSecuredBy);
//        $this->assertArraySubset(['oauth_2_0', 'basic', 'jwt', 'null'], $rootSecuredBy);
//
//        $resource = $this->valid->getResourceByPath('/search');
//        $this->assertNotNull($resource);
//        $this->assertInstanceOf(ResourceNode::class, $resource);
//
//        $resourceSecuritySchemes = $resource->getSecuritySchemes();
//        $this->assertCount(3, $resourceSecuritySchemes);
//
//        $resourceSecuredBy = $resource->getSecuredBy();
//        $this->assertCount(4, $resourceSecuredBy);
//        $this->assertArraySubset(['oauth_2_0', 'basic', 'jwt', 'null'], $resourceSecuredBy);
//
//        $oauth2 = $resource->getSecuritySchemeByKey('oauth_2_0');
//        $this->assertInstanceOf(SecurityScheme::class, $oauth2);
//    }
//
//    public function testGetRootNodeFromSecurityScheme()
//    {
//        /** @var SecurityScheme $scheme */
//        $scheme = $this->valid->getSecuritySchemeByKey('basic');
//        $this->assertInstanceOf(RootNode::class, $scheme->getRootNode());
//    }
//
//    public function testGetRawRamlFromSecurityScheme()
//    {
//        /** @var SecurityScheme $scheme */
//        $scheme = $this->valid->getSecuritySchemeByKey('basic');
//        $raml = $this->valid->getRaw()['securityScheme']['basic'];
//        $this->assertArraySubset($raml, $scheme->getRaw());
//    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeInvalidType()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_2_0' => [
                    'type' => 'invalid',
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Missing or invalid required securityScheme parameter: type',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\MutuallyExclusiveException
     */
    public function testSecuritySchemeMutuallyExclusive()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_2_0' => [
                    'type' => 'OAuth 2.0',
                    'describedBy' => [
                        'queryParameters' => [],
                        'queryString' => []
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Parameters \'queryParameters\' and \'queryString\' are mutually exclusive.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth20MissingAuthorizationGrants()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_2_0' => [
                    'type' => 'OAuth 2.0',
                    'settings' => [
                        'authorizationUri' => '',
                        'accessTokenUri' => ''
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 2.0\' is missing required setting \'authorizationGrants\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth20MissingAccessTokenUri()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_2_0' => [
                    'type' => 'OAuth 2.0',
                    'settings' => [
                        'authorizationUri' => '',
                        'authorizationGrants' => ['toto']
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 2.0\' is missing required setting \'accessTokenUri\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth20MissingAuthorizationUri()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_2_0' => [
                    'type' => 'OAuth 2.0',
                    'settings' => [
                        'accessTokenUri' => '',
                        'authorizationGrants' => ['toto']
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 2.0\' is missing required setting \'authorizationUri\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth10MissingRequestTokenUri()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_1_0' => [
                    'type' => 'OAuth 1.0',
                    'settings' => [
                        'authorizationUri' => '',
                        'tokenCredentialsUri' => ''
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 1.0\' is missing required setting \'requestTokenUri\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth10MissingAuthorizationUri()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_1_0' => [
                    'type' => 'OAuth 1.0',
                    'settings' => [
                        'requestTokenUri' => '',
                        'tokenCredentialsUri' => ''
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 1.0\' is missing required setting \'authorizationUri\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    /**
     * @expectedException \TheRealGambo\Ramlfications\Exceptions\InvalidSecuritySchemeException
     */
    public function testSecuritySchemeOAuth10MissingTokenCredentialsUri()
    {
        $raml = [
            'title' => 'toto',
            'securitySchemes' => [
                'oauth_1_0' => [
                    'type' => 'OAuth 1.0',
                    'settings' => [
                        'authorizationUri' => '',
                        'requestTokenUri' => ''
                    ]
                ]
            ]
        ];

        try {
            new RootNode($raml);
        } catch (\Exception $exception) {
            $this->assertEquals(
                'Security scheme \'OAuth 1.0\' is missing required setting \'tokenCredentialsUri\'.',
                $exception->getMessage()
            );
            throw $exception;
        }
    }

    public function testRootNodeSecuredBy()
    {
        $this->assertCount(3, $this->raml->getSecuredBy());
        $this->assertArraySubset(['basic', 'custom', 'null'], $this->raml->getSecuredBy());
    }
}
