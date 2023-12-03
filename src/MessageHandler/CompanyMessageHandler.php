<?php

namespace App\MessageHandler;

use App\Message\CompanyMessage;
use App\Repository\CompanyRepository;
use App\Service\Api\GouvCompanyService;
use App\Service\Utils\EntityService;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

#[AsMessageHandler]
class CompanyMessageHandler
{
    private CompanyRepository $companyRepository;
    private EntityService $entityService;
    private GouvCompanyService $gouvCompanyService;

    public function __construct(
        CompanyRepository $companyRepository,
        EntityService $entityService,
        GouvCompanyService $gouvCompanyService,
    ) {
        $this->companyRepository = $companyRepository;
        $this->entityService = $entityService;
        $this->gouvCompanyService = $gouvCompanyService;
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function __invoke(CompanyMessage $message): void
    {
        $company = $this->companyRepository->find($message->getCompanyId());
        if (!$company) {
            return;
        }
        $company->setTreated(true);

        try {
            $leaders = $this->gouvCompanyService->searchGouvCompanies(
                $company->getName(),
                $company->getPostalCode()
            );
            $company->setLeader($leaders);
        } catch (ClientException) {
            $company->setLeader('Failed to request');
        }

        $this->entityService->flush();
    }
}
