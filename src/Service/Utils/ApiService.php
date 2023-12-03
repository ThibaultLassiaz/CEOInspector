<?php

namespace App\Service\Utils;

use App\Entity\File;
use App\Repository\CompanyRepository;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

final class ApiService
{
    protected EntityManagerInterface $entityManager;
    protected CompanyRepository $companyRepository;
    protected FileRepository $fileRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CompanyRepository $companyRepository,
        FileRepository $fileRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->companyRepository = $companyRepository;
        $this->fileRepository = $fileRepository;
    }

    public function findMaxFileId(): int
    {
        return $this->fileRepository->findMaxFileId();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findFiles(): array
    {
        $result = [];
        $files = $this->fileRepository->findAll();
        foreach ($files as $file) {
            $count = $this->countTreated($file);
            $result[] = ['file' => $file, 'count' => $count];
        }

        return $result;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countTreated(File $file): array
    {
        return $this->companyRepository->countTreatedAndNonTreatedByFileId($file);
    }

    public function findFileById(int $fileId): File
    {
        return $this->fileRepository->findOneBy(['file_id' => $fileId]);
    }

    public function findByFilePath(string $filePath): array
    {
        $file = $this->findFileByPath($filePath);

        $companies = $this->companyRepository->findBy([
            'file' => $file,
        ]);

        return
            [
                'companies' => $companies,
                'file' => $file,
            ];
    }

    public function findFileByPath(string $path): File
    {
        return $this->fileRepository->findOneBy(['path' => $path]);
    }

    public function deleteFileByPath(string $path): void
    {
        $file = $this->findFileByPath($path);

        $query = $this->companyRepository->createQueryBuilder('c')
            ->where('c.file = :file')
            ->setParameter('file', $file)
            ->getQuery();

        $companies = $query->getResult();

        foreach ($companies as $company) {
            $this->entityManager->remove($company);
        }

        $this->fileRepository->remove($file);
        $this->entityManager->flush();
    }
}
