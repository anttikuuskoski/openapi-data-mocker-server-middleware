<?php

/**
 * OpenApiDataMockerRouteMiddlewareTest
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */

/**
 * OpenAPIServer
 *
 * This spec is mainly for testing Petstore server and contains fake endpoints, models. Please do not use this for any other purpose. Special characters: \" \\
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://github.com/openapitools/openapi-generator.git
 */

/**
 * NOTE: This class is auto generated by the openapi generator program.
 * https://github.com/openapitools/openapi-generator
 * Do not edit the class manually.
 */
namespace OpenAPIServer\Mock;

use OpenAPIServer\Mock\OpenApiDataMockerRouteMiddleware;
use OpenAPIServer\Mock\OpenApiDataMocker;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use StdClass;

/**
 * OpenApiDataMockerRouteMiddlewareTest Class Doc Comment
 *
 * @package OpenAPIServer\Mock
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 * @coversDefaultClass \OpenAPIServer\Mock\OpenApiDataMockerRouteMiddleware
 */
class OpenApiDataMockerRouteMiddlewareTest extends TestCase
{
    /**
     * @covers ::__construct
     * @dataProvider provideConstructCorrectArguments
     */
    public function testConstructor(
        $mocker,
        $responses,
        $responseFactory,
        $getMockStatusCodeCallback,
        $afterCallback
    ) {
        $middleware = new OpenApiDataMockerRouteMiddleware($mocker, $responses, $responseFactory, $getMockStatusCodeCallback, $afterCallback);
        $this->assertInstanceOf(OpenApiDataMockerRouteMiddleware::class, $middleware);
        $this->assertNotNull($middleware);
    }

    public function provideConstructCorrectArguments()
    {
        $getMockStatusCodeCallback = function () {
            return false;
        };
        $afterCallback = function () {
            return false;
        };
        return [
            [new OpenApiDataMocker(), [], new Psr17Factory(), null, null],
            [new OpenApiDataMocker(), [], new Psr17Factory(), $getMockStatusCodeCallback, $afterCallback],
        ];
    }

    /**
     * @covers ::__construct
     * @dataProvider provideConstructInvalidArguments
     * @expectedException \InvalidArgumentException
     * @expectedException \TypeError
     */
    public function testConstructorWithInvalidArguments(
        $mocker,
        $responses,
        $responseFactory,
        $getMockStatusCodeCallback,
        $afterCallback
    ) {
        $middleware = new OpenApiDataMockerRouteMiddleware($mocker, $responses, $responseFactory, $getMockStatusCodeCallback, $afterCallback);
    }

    public function provideConstructInvalidArguments()
    {
        return [
            'getMockStatusCodeCallback not callable' => [
                new OpenApiDataMocker(), [], new Psr17Factory(), 'foobar', null,
            ],
            'afterCallback not callable' => [
                new OpenApiDataMocker(), [], new Psr17Factory(), null, 'foobar',
            ],
            'responses not an array or object' => [
                new OpenApiDataMocker(), 'foobar', new Psr17Factory(), null, null,
            ],
        ];
    }

    /**
     * @covers ::process
     * @dataProvider provideProcessArguments
     */
    public function testProcess(
        $mocker,
        $responses,
        $responseFactory,
        $getMockStatusCodeCallback,
        $afterCallback,
        $request,
        $expectedStatusCode,
        $expectedHeaders,
        $notExpectedHeaders,
        $expectedBody
    ) {

        // Create a stub for the RequestHandlerInterface interface.
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn($responseFactory->createResponse());

        $middleware = new OpenApiDataMockerRouteMiddleware(
            $mocker,
            $responses,
            $responseFactory,
            $getMockStatusCodeCallback,
            $afterCallback
        );
        $response = $middleware->process($request, $handler);

        // check status code
        $this->assertSame($expectedStatusCode, $response->getStatusCode());

        // check http headers in request
        foreach ($expectedHeaders as $expectedHeader => $expectedHeaderValue) {
            $this->assertTrue($response->hasHeader($expectedHeader));
            if ($expectedHeaderValue !== '*') {
                $this->assertSame($expectedHeaderValue, $response->getHeader($expectedHeader)[0]);
            }
        }
        foreach ($notExpectedHeaders as $notExpectedHeader) {
            $this->assertFalse($response->hasHeader($notExpectedHeader));
        }

        // check body
        if (is_array($expectedBody)) {
            // random values, check keys only
            foreach ($expectedBody as $attribute => $value) {
                $this->assertObjectHasAttribute($attribute, json_decode((string) $response->getBody(), false));
            }
        } else {
            $this->assertEquals($expectedBody, (string) $response->getBody());
        }
    }

    public function provideProcessArguments()
    {
        $mocker = new OpenApiDataMocker();
        $responseFactory = new Psr17Factory();
        $isMockResponseRequired = function (ServerRequestInterface $request) {
            $mockHttpHeader = 'X-OpenAPIServer-Mock';
            return $request->hasHeader($mockHttpHeader)
                && $request->getHeader($mockHttpHeader)[0] === 'ping';
        };

        $getMockStatusCodeCallback = function (ServerRequestInterface $request, $responses) use ($isMockResponseRequired) {
            if ($isMockResponseRequired($request)) {
                $responses = (array) $responses;
                if (array_key_exists('default', $responses)) {
                    return 'default';
                }

                // return status code of the first response
                return array_key_first($responses);
            }

            return false;
        };

        $afterCallback = function ($request, $response) use ($isMockResponseRequired) {
            if ($isMockResponseRequired($request)) {
                $response = $response->withHeader('X-OpenAPIServer-Mock', 'pong');
            }

            return $response;
        };

        $responses = [
            '400' => [
                'description' => 'Bad Request Response',
                'content' => new StdClass(),
            ],
            'default' => [
                'description' => 'Success Response',
                'headers' => [
                    'X-Location' => ['schema' => ['type' => 'string']],
                    'X-Created-Id' => ['schema' => ['type' => 'integer']],
                ],
                'content' => [
                    'application/json;encoding=utf-8' => ['schema' => ['type' => 'object', 'properties' => ['id' => ['type' => 'integer'], 'className' => ['type' => 'string'], 'declawed' => ['type' => 'boolean']]]],
                ],
            ],
        ];

        $responsesXmlOnly = [
            'default' => [
                'description' => 'Success Response',
                'content' => [
                    'application/xml' => [
                        'schema' => [
                            'type' => 'string',
                        ],
                    ],
                ],
            ],
        ];

        $responsesObj = json_decode(
            '{
                "400": {
                    "description": "Bad Request Response",
                    "content": {}
                },
                "default": {
                    "description": "Success Response",
                    "headers": {
                        "X-Location": {
                            "schema": {
                                "type": "string"
                            }
                        },
                        "X-Created-Id": {
                            "schema": {
                                "type": "integer"
                            }
                        }
                    },
                    "content": {
                        "application/json;encoding=utf-8": {
                            "schema": {
                                "type": "object",
                                "properties": {
                                    "id": {
                                        "type": "integer"
                                    },
                                    "className": {
                                        "type": "string"
                                    },
                                    "declawed": {
                                        "type": "boolean"
                                    }
                                }
                            }
                        }
                    }
                }
            }'
        );

        return [
            'callbacks null' => [
                $mocker,
                $responses,
                $responseFactory,
                null,
                null,
                $responseFactory->createServerRequest('GET', '/phpunit'),
                200,
                [],
                ['X-OpenAPIServer-Mock', 'x-location', 'x-created-id'],
                '',
            ],
            'xml not supported' => [
                $mocker,
                $responsesXmlOnly,
                $responseFactory,
                $getMockStatusCodeCallback,
                $afterCallback,
                $responseFactory->createServerRequest('GET', '/phpunit')
                    ->withHeader('X-OpenAPIServer-Mock', 'ping'),
                200,
                ['X-OpenAPIServer-Mock' => 'pong', 'content-type' => '*/*'],
                ['x-location', 'x-created-id'],
                'Mock feature supports only "application/json" content-type!',
            ],
            'mock response default schema' => [
                $mocker,
                $responses,
                $responseFactory,
                $getMockStatusCodeCallback,
                $afterCallback,
                $responseFactory->createServerRequest('GET', '/phpunit')
                    ->withHeader('X-OpenAPIServer-Mock', 'ping'),
                200,
                ['X-OpenAPIServer-Mock' => 'pong', 'content-type' => 'application/json', 'x-location' => '*', 'x-created-id' => '*'],
                [],
                [
                    'id' => 1,
                    'className' => 'cat',
                    'declawed' => false,
                ],
            ],
            'mock response default schema with responses as object' => [
                $mocker,
                $responsesObj,
                $responseFactory,
                $getMockStatusCodeCallback,
                $afterCallback,
                $responseFactory->createServerRequest('GET', '/phpunit')
                    ->withHeader('X-OpenAPIServer-Mock', 'ping'),
                200,
                ['X-OpenAPIServer-Mock' => 'pong', 'content-type' => 'application/json', 'x-location' => '*', 'x-created-id' => '*'],
                [],
                [
                    'id' => 1,
                    'className' => 'cat',
                    'declawed' => false,
                ],
            ],
        ];
    }
}
