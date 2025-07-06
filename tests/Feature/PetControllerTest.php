<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\PetstoreService;

class PetControllerTest extends TestCase
{
    public function testIndexLoadsCorrectly()
    {
        $mock = Mockery::mock(PetstoreService::class);
        $mock->shouldReceive('getPetsByStatus')->andReturn([
            ['id' => 1, 'name' => 'Doggie', 'status' => 'available']
        ]);

        $this->app->instance(PetstoreService::class, $mock);

        $response = $this->get('/pets');

        $response->assertStatus(200);
        $response->assertSee('Doggie');
    }

    public function testCreatePetRedirectsOnSuccess()
    {
        $mock = Mockery::mock(PetstoreService::class);
        $mock->shouldReceive('createPet')->andReturn(true);
        $mock->shouldReceive('uploadImage')->andReturn(['message' => 'OK']);

        $this->app->instance(PetstoreService::class, $mock);

        Storage::fake('images');
        $file = UploadedFile::fake()->image('pet.jpg');

        $response = $this->post('/pets', [
            'name' => 'Test Pet',
            'status' => 'available',
            'category' => 'dogs',
            'tags' => 'tag1, tag2',
            'photoUrls' => 'https://example.com/image.jpg',
            'imageFile' => $file,
        ]);

        $response->assertRedirect('/pets');
        $response->assertSessionHas('success', 'Pet created.');
    }

    public function testUpdatePetRedirectsOnSuccess()
    {
        $mock = Mockery::mock(PetstoreService::class);
        $mock->shouldReceive('updatePet')->andReturn(true);
        $mock->shouldReceive('uploadImage')->andReturn(['message' => 'OK']);

        $this->app->instance(PetstoreService::class, $mock);

        $file = UploadedFile::fake()->image('updated.jpg');

        $response = $this->put('/pets/123', [
            'name' => 'Updated Pet',
            'status' => 'pending',
            'category' => 'cats',
            'tags' => 'updatedTag',
            'photoUrls' => '',
            'imageFile' => $file,
        ]);

        $response->assertRedirect('/pets');
        $response->assertSessionHas('success', 'Pet updated.');
    }
}
