<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $lineNumber;

    #[ORM\Column]
    private int $file_id;

    #[ORM\Column]
    private string $path;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: Company::class)]
    private Collection $companies;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function setLineNumber(int $lineNumber): void
    {
        $this->lineNumber = $lineNumber;
    }

    public function getFileId(): int
    {
        return $this->file_id;
    }

    public function setFileId(int $file_id): void
    {
        $this->file_id = $file_id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function setCompanies(Collection $companies): void
    {
        $this->companies = $companies;
    }
}
