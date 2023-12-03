<?php

namespace App\Service\Utils;

use App\Entity\Company;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;

final class EntityService
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array<int, mixed> $line
     * @param File $file
     * @return int
     */
    public function createCompany(array $line, File $file): int
    {
        $company = new Company();
        $company->setName($line[3]);
        $company->setPostalCode($line[11]);
        $company->setFile($file);
        $company->setTreated(false);

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $company->getId();
    }

    public function createFile(int $maxFileId, int $companyNumber, string $fileName): File
    {
        $fileName = str_replace(' ', '', $fileName);
        $parts = explode('.xlsx', $fileName);

        $file = new File();
        $file->setFileId($maxFileId + 1);
        $file->setName($parts[0].'_'.$file->getFileId());
        $file->setLineNumber($companyNumber);
        $file->setPath('/files/output/'.$parts[0].'_'.$file->getFileId());

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
