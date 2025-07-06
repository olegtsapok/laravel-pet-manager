<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PetstoreService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class PetstoreServiceTest extends TestCase
{
    protected function createServiceWithMockedResponse($responses)
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        return new PetstoreService($client);
    }

    public function testGetPetsByStatusReturnsData()
    {
        $body = json_encode([['id' => 1, 'name' => 'Fluffy']]);
        $service = $this->createServiceWithMockedResponse([new Response(200, [], $body)]);

        $result = $service->getPetsByStatus('available');

        $this->assertIsArray($result);
        $this->assertEquals('Fluffy', $result[0]['name']);
    }

    public function testGetPetHandlesError()
    {
        $service = $this->createServiceWithMockedResponse([
            new RequestException("Error", new Request('GET', 'pet/1'))
        ]);

        $result = $service->getPet(1);
        $this->assertNull($result);
    }

    public function testCreatePetReturnsData()
    {
        $responseData = ['id' => 100, 'name' => 'Buddy'];
        $service = $this->createServiceWithMockedResponse([
            new Response(200, [], json_encode($responseData))
        ]);

        $result = $service->createPet(['name' => 'Buddy']);
        $this->assertEquals(100, $result['id']);
    }

    public function testDeletePetReturnsTrue()
    {
        $service = $this->createServiceWithMockedResponse([
            new Response(200)
        ]);

        $this->assertTrue($service->deletePet(99));
    }

    public function testDeletePetHandlesError()
    {
        $service = $this->createServiceWithMockedResponse([
            new RequestException("Error", new Request('DELETE', 'pet/99'))
        ]);

        $this->assertFalse($service->deletePet(99));
    }

    public function testUploadImage()
    {
        $responseBody = json_encode(['message' => 'File uploaded']);
        $service = $this->createServiceWithMockedResponse([
            new Response(200, [], $responseBody)
        ]);

        $path = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($path, 'dummy content');

        $result = $service->uploadImage(1, $path, 'test.jpg', 'image/jpeg');

        unlink($path);
        $this->assertIsArray($result);
        $this->assertEquals('File uploaded', $result['message']);
    }

}