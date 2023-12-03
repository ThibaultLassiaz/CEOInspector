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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @param int $lineNumber
     */
    public function setLineNumber(int $lineNumber): void
    {
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return int
     */
    public function getFileId(): int
    {
        return $this->file_id;
    }

    /**
     * @param int $file_id
     */
    public function setFileId(int $file_id): void
    {
        $this->file_id = $file_id;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return Collection
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Collection $companies
     */
    public function setCompanies(Collection $companies): void
    {
        $this->companies = $companies;
    }
}
