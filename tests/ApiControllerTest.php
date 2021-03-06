<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    const NEW_REQUEST_MESSAGE = 'New request saved. It will be loaded next time.';
    const REQUEST_FOUND_MESSAGE = 'Request found, but no response set yet.';

    /**
     * Test GET an new request and find it later.
     */
    public function testGetRequest()
    {
        $requestUrl = '/api/test/get-token?somevar=hello';
        $client = $this->makeRequest('GET', $requestUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);

        $client = $this->makeRequest('GET', $requestUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::REQUEST_FOUND_MESSAGE);
    }

    /**
     * Test creating two slightly different GET requests.
     */
    public function testTwoNewGETs()
    {
        $client = $this->makeRequest('GET', '/api/test/token1?somevar=hello');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);

        $client = $this->makeRequest('GET', '/api/test/token2?somevar=world');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);
    }

    /**
     * Test POST request
     */
    public function testPostRequest()
    {
        $requestUrl = '/api/testGET/get/token?somevar=hello';
        $postParameters = [
            'pre_name'  => 'John',
            'last_name' => 'Doe',
        ];

        $client = $this->makeRequest('POST', $requestUrl, $postParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);

        $client = $this->makeRequest('POST', $requestUrl, $postParameters);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::REQUEST_FOUND_MESSAGE);
    }

    /**
     * Test creating two slightly different POST requests.
     */
    public function testTwoNewPOSTs()
    {
        $requestUrl = '/api/testPOST/post/token?somevar=hello';

        $client = $this->makeRequest('POST', $requestUrl, ['a' => '1']);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);

        $client = $this->makeRequest('POST', $requestUrl, ['a' => '2']);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSelectorTextContains('body', self::NEW_REQUEST_MESSAGE);
    }

    /**
     * Test result by content.
     */
    public function testRequestSimilarContents()
    {
        $requestUrl = '/api/contentTest/content';
        $requestContent = '<request><text><title>Request with titles</title></text></request>';
        $client = $this->makeRequest('POST', $requestUrl, [], $requestContent);
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSelectorTextContains('', 'Response with title');
    }

    /**
     * Request a URL and return the Client object.
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param string|null $requestBody
     *
     * @return KernelBrowser
     */
    private function makeRequest(string $method, string $url, array $params = [], string $requestBody = null)
    {
        $client = static::createClient();
        $client->request($method, $url, $params, [], [], $requestBody);
        return $client;
    }
}
