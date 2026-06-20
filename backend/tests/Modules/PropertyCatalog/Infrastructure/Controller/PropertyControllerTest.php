<?php

declare(strict_types=1);

namespace App\Tests\Modules\PropertyCatalog\Infrastructure\Controller;

use App\Modules\IdentityAccess\Domain\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PropertyControllerTest extends WebTestCase
{
    protected function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();

        $user = new User(Uuid::v4(), 'test@example.com', 'password');

        $client->loginUser($user, 'api');

        return $client;
    }

    public function testListProperties(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/properties');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
    }

    public function testCreateProperty(): void
    {
        $client = $this->createAuthenticatedClient();

        $payload = [
            'title' => 'Beautiful Apartment in City Center',
            'price' => 500000,
            'area' => 75.5,
            'type' => 'apartment',
        ];

        $client->request(
            'POST',
            '/api/properties',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    public function testUploadPhotoWithoutFileReturnsBadRequest(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/api/properties/123e4567-e89b-12d3-a456-426614174000/photo'
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $data = json_decode($responseContent, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Brak pliku', $data['error']);
    }
}
