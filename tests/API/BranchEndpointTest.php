<?php

namespace App\Tests\API;

use Ramsey\Uuid\Uuid;

class BranchEndpointTest extends \TestCase
{
    /**
     * Listing all the branch.
     */
    public function testListing()
    {
        $this->get('/api/v1/branches');
        $this->assertResponseOk();
        $content = $this->shouldBeJsonEndpoint();
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('result', $content);
        $this->seeJson(['result' => 'success']);
    }

    public function testAddingBranch()
    {
        $data = [
            'name' => 'Testing',
            'foo' => 'bar'
        ];
        $this->json('POST', '/api/v1/branches', $data);
        $this->assertResponseStatus(201);
        $this->shouldBeJsonEndpoint();
        $this->seeInDatabase('branches', ['name' => 'Testing']);
    }

    public function testEmptyAdding()
    {
        $this->json('POST', '/api/v1/branches');
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    public function testExistsBranch()
    {
        $this->json('GET', '/api/v1/branches');
        $content = $this->shouldBeJsonEndpoint();
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('result', $content);
        $this->seeJson(['result' => 'success']);
        $data = $content['data'][0];
        $this->assertEquals('Testing', $data['name']);
        return $data;
    }

    /**
     * @depends testExistsBranch
     * @param array $data
     * @return array
     */
    public function testUpdateBranch(array $data)
    {
        $url = sprintf('/api/v1/branches/%s', $data['id']);
        $param = ['name' => 'Test'];
        $this->json('PUT', $url, $param);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseOk();
        return $data;
    }

    public function testUpdateInvalidFormat()
    {
        $url = sprintf('/api/v1/branches/%s', '0000-0000');
        $data = ['name' => 'Test'];
        $this->json('PUT', $url, $data);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    public function testUpdateNotExists()
    {
        $url = sprintf('/api/v1/branches/%s', Uuid::NIL);
        $data = ['name' => 'Test'];
        $this->json('PUT', $url, $data);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(404);
    }

    public function testDeleteNotExists()
    {
        $url = sprintf('/api/v1/branches/%s', Uuid::NIL);
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseOk();
    }

    public function testDeleteInvalid()
    {
        $url = sprintf('/api/v1/branches/%s', '0000-0000');
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    /**
     * @depends testUpdateBranch
     * @param array $data
     */
    public function testDelete(array $data)
    {
        $url = sprintf('/api/v1/branches/%s', $data['id']);
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseOk();
    }
}