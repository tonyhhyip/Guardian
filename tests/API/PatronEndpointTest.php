<?php

namespace App\Tests\API;

use Ramsey\Uuid\Uuid;
use Guardian\Models\Branch;

class PatronEndpointTest extends \TestCase
{

    private static $str;

    public static function setUpBeforeClass()
    {
        static::$str = str_random(6);
    }

    /**
     * Listing all the patron.
     */
    public function testListing()
    {
        $this->get('/api/v1/patrons');
        $this->assertResponseOk();
        $this->shouldBeJsonEndpoint();
        $this->seeJsonKey('data', 'result');
        $this->seeJson(['result' => 'success']);
    }

    public function testAddingPatron()
    {
        $branch = Uuid::uuid4()->toString();
        Branch::create([
            'id' => $branch,
            'name' => 'Branch ' . static::$str
        ]);

        $data = [
            'library_card_number' => 'CARD-' . static::$str,
            'name' => 'Reader ' . static::$str,
            'birthday' => date("Y-m-d H:i:s", rand(1262055681, 1262055681)),
            'branch_id' => $branch,
        ];
        $this->json('POST', '/api/v1/patrons', $data);
        $this->assertResponseStatus(201);
        $this->shouldBeJsonEndpoint();
        $this->seeInDatabase('patrons', $data);
    }

    public function testEmptyAdding()
    {
        $this->json('POST', '/api/v1/patrons', []);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    public function testExistsPatron()
    {
        $this->get('/api/v1/patrons');
        $content = $this->shouldBeJsonEndpoint();
        $this->seeJsonKey('data', 'result');
        $this->assertGreaterThan(0, count($content['data']));
        $data = $content['data'][0];
        $this->assertEquals('Reader ' . static::$str, $data['name']);
        return $data;
    }

    /**
     * @depends testExistsPatron
     * @param array $data
     * @return array
     */
    public function testUpdatePatron(array $data)
    {
        $url = sprintf('/api/v1/patrons/%s', $data['id']);
        $param = [
            'library_card_number' => 'CARD-'.static::$str.'(Updated)',
            'name' => 'Reader '.static::$str.'(Updated)',
            'birthday' => date("Y-m-d H:i:s", rand(1262055681, 1262055681)),
            'branch_id' => $data['branch_id'],
        ];
        $this->json('PUT', $url, $param);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseOk();
        return $data;
    }

    public function testUpdateInvalidFormat()
    {
        $url = sprintf('/api/v1/patrons/%s', '0000-0000');
        $data = ['name' => 'Test'];
        $this->json('PUT', $url, $data);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    public function testUpdateNotExists()
    {
        $url = sprintf('/api/v1/patrons/%s', Uuid::NIL);
        $data = [
          'library_card_number' => 'CARD-'.static::$str.'(Updated)',
          'name' => 'Reader '.static::$str.'(Updated)',
          'birthday' => date("Y-m-d H:i:s", rand(1262055681,1262055681)),
          'branch_id' => Uuid::uuid4()->toString(),
        ];
        $this->json('PUT', $url, $data);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(404);
    }

    public function testDeleteNotExists()
    {
        $url = sprintf('/api/v1/patrons/%s', Uuid::NIL);
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseOk();
    }

    public function testDeleteInvalid()
    {
        $url = sprintf('/api/v1/patrons/%s', '0000-0000');
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(422);
    }

    /**
     * @depends testUpdatePatron
     * @param array $data
     */
    public function testDelete(array $data)
    {
        $url = sprintf('/api/v1/patrons/%s', $data['id']);
        $this->json('DELETE', $url);
        $this->shouldBeJsonEndpoint();
        $this->assertResponseStatus(200);
    }
}
