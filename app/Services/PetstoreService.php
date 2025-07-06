<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PetstoreService
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPetsByStatus($status = 'available')
    {
        try {
            $response = $this->client->get("pet/findByStatus", ['query' => ['status' => $status]]);
            return json_decode($response->getBody(), true);
        } catch(RequestException $e) {
            return $this->handleApiError($e, []);
        }
    }

    public function getPet($id)
    {
        try {
            $response = $this->client->get("pet/{$id}");
            return json_decode($response->getBody(), true);
        } catch(RequestException $e) {
            return $this->handleApiError($e, null);
        }
    }

    public function createPet($data)
    {
        try {
            $response = $this->client->post("pet", ['json' => $data]);
            return json_decode($response->getBody(), true);
        } catch(RequestException $e) {
            return $this->handleApiError($e, null);
        }
    }

    public function updatePet($data)
    {
        try {
            $response = $this->client->put("pet", ['json' => $data]);
            return json_decode($response->getBody(), true);
        } catch(RequestException $e) {
            return $this->handleApiError($e, null);
        }
    }

    public function deletePet($id)
    {
        try {
            $this->client->delete("pet/{$id}");
            return true;
        } catch(RequestException $e) {
            return $this->handleApiError($e, false);
        }
    }

    private function handleApiError(RequestException $e, $default)
    {
        $message = $e->hasResponse()
            ? $e->getResponse()->getBody()->getContents()
            : $e->getMessage();
        Log::error("Petstore API error: " . $message);
        return $default;
    }


    public function uploadImage($id, $path, $filename, $mime)
    {
        try {
            $response = $this->client->post("pet/{$id}/uploadImage", [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen($path, 'r'),
                        'filename' => $filename,
                        'headers'  => ['Content-Type' => $mime],
                    ],
                ],
            ]);
            return json_decode($response->getBody(), true);
        } catch(RequestException $e) {
            return $this->handleApiError($e, null);
        }
    }

}