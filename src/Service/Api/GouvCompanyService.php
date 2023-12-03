<?php

namespace App\Service\Api;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GouvCompanyService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function searchGouvCompanies(
        string $name,
        string $postalCode,
    ): string {
        $response = $this->client->request('GET', 'https://recherche-entreprises.api.gouv.fr/search', [
            'query' => [
                'q' => $name,
                'code_postal' => $postalCode,
            ],
        ]);

        $content = $response->getContent();
        $data = json_decode($content, true);

        return $this->findCompanyLeaders($data, $name, $postalCode);
    }

    public function findCompanyLeaders(mixed $data, string $name, string $postalCode): string
    {
        $stringLeader = '';
        $dirigeants = [];

        if ([] !== $data['results']) {
            $company = $data['results'][0];

            if ($company['siege']['code_postal'] == $postalCode) {
                if ('A' !== $company['etat_administratif']) {
                    return 'Inactive';
                }

                if ([] == $company['dirigeants']) {
                    return 'Pas de dirigeants';
                }

                foreach ($company['dirigeants'] as $dirigeant) {
                    if (isset($dirigeant['nom'])) {
                        if (str_contains($dirigeant['prenoms'], ' ')) {
                            $dirigeant['prenoms'] = strtok($dirigeant['prenoms'], ' ');
                        }
                        if (null == $dirigeant['qualite']) {
                            $dirigeant['qualite'] = 'Gérant';
                        }
                        $dirigeants[$dirigeant['qualite']] = $dirigeant['nom'].' '.$dirigeant['prenoms'];
                    }
                }

                foreach ($dirigeants as $qualite => $name) {
                    $stringLeader = $stringLeader.$qualite.' : '.$name.' | ';
                }
                $stringLeader = substr($stringLeader, 0, -2);
            } else {
                $stringLeader = 'Not found';
            }
        } else {
            $stringLeader = 'Not found';
        }

        return $stringLeader;
    }
}
