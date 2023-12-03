<?php

namespace App\Service\Scrapper\SocieteCom;

use App\Service\Enum\Status;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

///////// THIS SERVICE IS OBSOLETE ///////////
    ///////// SOCIETE COM BANNED THE IP SO WE ARE USING GOUV API WHICH IS FREE ///////////
final class SocieteComService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function generateCompanySearchUrl(string $companyName): string
    {
        $companyNameForUrl = str_replace(" ", "+", $companyName);
        $companyNameForUrl = str_replace("&", "%26", $companyNameForUrl);
        $companyNameForUrl = str_replace("'", "%27", $companyNameForUrl);

        return "https://www.societe.com/cgi-bin/search?champs=" . $companyNameForUrl;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCompaniesInfos(string $url): array
    {
        $content = $this->generateRequest($url);
        $crawlerPage = new Crawler($content);

        $infos = $crawlerPage->filter('#result_rs_societe > * .txt')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $links = $crawlerPage->filter('#result_rs_societe > * .ResultBloc__link__content')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $infosDeno = $crawlerPage->filter('#result_deno_societe > * .txt')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $linksDeno = $crawlerPage->filter('#result_deno_societe > * .ResultBloc__link__content')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $infosEtab = $crawlerPage->filter('#result_etab_societe > * .txt')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $linksEtab = $crawlerPage->filter('#result_etab_societe > * .ResultBloc__link__content')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $infos = array_chunk($infos, 3);
        $infosDeno = array_chunk($infosDeno, 3);
        $infosEtab = array_chunk($infosEtab, 3);

        $infos = array_merge($infos, $infosDeno, $infosEtab);
        $links = array_merge($links, $linksDeno, $linksEtab);

        return [
            "infos" => $infos,
            "links" => $links
        ];
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function generateRequest(string $url): string
    {
        $response = $this->client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'cookie' => 'CONSENT=YES+cb.20210706-13-p0.fr+FX+241;',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
                    'Host: www.societe.com',
                    'Accept: text/html'
                ],
            ]
        );

        return $response->getContent();
    }

    public function generatePersonalCompanyLink(string $link): string
    {
        return "https://www.societe.com/" . $link;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCompanyStatus(string $content): Status
    {
        $crawlerPage = new Crawler($content);
        $inactive = $crawlerPage->filter('.inactive')->count();

        if ($inactive >= 1) {
            return Status::INACTIVE;
        }

        return Status::ACTIVE;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getPersonalCompanyInfos(string $content): array
    {
        $crawlerPage = new Crawler($content);

        $leadersTab = $crawlerPage->filter('#tabledir > * span')->each(function (Crawler $node, $i) {
            if ($node->text() == "En savoir add" || $node->text() == "Afficher tous les dirigeants") {
                return;
            }
            return $node->text();
        });

        if (count(array_filter($leadersTab)) > 0) {
            return array_filter($leadersTab);
        } else {
            $societeName = $crawlerPage->filter('.nomSociete')->each(function (Crawler $node, $i) {
                return $node->text();
            });

            if (strtolower(strtok($societeName[0], " ")) == "monsieur" || strtolower(strtok($societeName[0], " ")) == "madame") {
                return $societeName;
            }
        }

        return [];
    }

    public function getLeaderListString(array $leaderList): string
    {
        $stringLeader = "";
        if (count($leaderList) > 0) {
            foreach ($leaderList as $leader) {
                $stringLeader .= $leader . " | ";
            }
            $stringLeader = substr($stringLeader, 0, -3);
        } else {
            $stringLeader = "Not found";
        }

        return $stringLeader;
    }
}