<?php

namespace App\Service\Utils;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

final class FileService
{

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function openCsvFile(string $filePath): array
    {
        $content = array_map('str_getcsv', file($filePath));

        $content = array_map(function (array $row) {
            if ($row[3]) {
                return $row;
            }
        }, $content);

        return array_filter($content);
    }

    public function createXlsx(string $fileContent, string $filePath): void
    {
        file_put_contents($filePath, $fileContent);
    }

    public function createCsv(string $filePath, string $csvPath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getSheet(0);
        $dataArray = $worksheet->toArray();

        $csvFile = fopen($csvPath, 'w');
        foreach ($dataArray as $row) {
            fputcsv($csvFile, $row);
        }

        fclose($csvFile);
        unlink($filePath);
    }
}