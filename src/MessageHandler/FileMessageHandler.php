<?php

namespace App\MessageHandler;

use App\Message\CompanyMessage;
use App\Message\FileMessage;
use App\Service\Utils\ApiService;
use App\Service\Utils\EntityService;
use App\Service\Utils\FileService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class FileMessageHandler
{
    private ApiService $apiService;
    private EntityService $entityService;
    private FileService $fileService;
    private MessageBusInterface $bus;


    public function __construct(
        ApiService          $apiService,
        EntityService       $entityService,
        FileService         $fileService,
        MessageBusInterface $bus
    )
    {
        $this->apiService = $apiService;
        $this->entityService = $entityService;
        $this->fileService = $fileService;
        $this->bus = $bus;
    }

    public function __invoke(FileMessage $message): void
    {
        $csvPath = $message->getFilePath();
        $file = $this->apiService->findFileById($message->getFileId());
        $csv = $this->fileService->openCsvFile($csvPath);

        foreach ($csv as $line) {
            if (!$line[3]) {
                continue;
            }
            $companyId = $this->entityService->createCompany($line, $file);

            $this->bus->dispatch(new CompanyMessage($companyId));
        }

        unlink($csvPath);
    }
}