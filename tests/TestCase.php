<?php

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function getJSON()
    {
        $content = json_decode($this->response->getContent(), true);
        $this->assertEquals(JSON_ERROR_NONE, json_last_error());
        $this->assertTrue($content !== false);
        return $content;
    }

    /**
     * Check endpoint response is JSON Response
     *
     * @return array Content of decoded data
     */
    protected function shouldBeJsonEndpoint()
    {
        $response = $this->response;
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        return $this->getJSON();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Http\Response
     */
    public function json($method, $uri, array $data = [], array $headers = [])
    {
        parent::json($method, $uri, $data, $headers);
        return $this->response;
    }

    protected function seeJsonKey($keys)
    {
        $keys = func_num_args() === 1 ? $keys : func_get_args();
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        $content = $this->getJSON();

        foreach ($keys as $key)
            $this->assertArrayHasKey($key, $content);
    }
}
