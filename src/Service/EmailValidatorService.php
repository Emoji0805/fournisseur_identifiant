<?php 
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailValidatorService
{
    private $apiKey='4de3d3355e7f42beb2b87640337bde33';
    private $apiUrl = 'https://api.zerobounce.net/v2/validate';

    public function validateEmail(string $email): bool
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $this->apiUrl, [
            'query' => [
                'api_key' => $this->apiKey,
                'email'   => $email,
            ],
        ]);

        $data = $response->toArray();

        return isset($data['status']) && $data['status'] === 'valid';
    }

    public function showEmail(string $email)
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $this->apiUrl, [
            'query' => [
                'api_key' => $this->apiKey,
                'email'   => $email,
            ],
        ]);

        $data = $response->toArray();

        return $data;
    }
} 

?>
