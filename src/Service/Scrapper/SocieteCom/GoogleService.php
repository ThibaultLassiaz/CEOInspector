<?php

namespace App\Service\Scrapper\SocieteCom;

use App\Entity\Company;
use App\Service\Enum\Status;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class GoogleService
{
    public function __construct()
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function googleScrap(Company $company): array
    {
        $url = $this->generateGoogleScrapUrl($company);
        $content = $this->generateRequest($url);
        $possibleUrl = $this->findCompanyFromRequest($content);

        if ('Not found' == $possibleUrl) {
            return [$possibleUrl];
        }
        $possibleUrl = 'https://'.$possibleUrl;

        $content = $this->generateRequest($possibleUrl);

        $companyStatus = $this->getCompanyStatus($content);

        if (Status::INACTIVE == $companyStatus) {
            return ['Inactive'];
        }

        return $this->getPersonalCompanyInfos($content);
    }

    public function generateGoogleScrapUrl(Company $company): string
    {
        $companyNameForUrl = $company->getName();

        $companyNameForUrl = str_replace(' ', '+', $companyNameForUrl);
        $companyNameForUrl = str_replace('&', '%26', $companyNameForUrl);
        $companyNameForUrl = str_replace("'", '%27', $companyNameForUrl);
        $companyNameForUrl = str_replace(' ', '+', $companyNameForUrl);

        return 'https://www.google.com/search?q='.$companyNameForUrl.'+'.$company->getPostalCode();
    }

    public function findCompanyFromRequest(string $content): string
    {
        $crawlerPage = new Crawler($content);

        $links = $crawlerPage->filter('a')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $companyFound = array_filter(array_map(function (string $link) {
            if (preg_match('/www.societe.*html/', $link, $matches)) {
                return $matches[0];
            } else {
                return;
            }
        }, $links));

        if (!reset($companyFound)) {
            return 'Not found';
        }

        return reset($companyFound);
    }
}
