<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private string $postalCode;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $leader = null;

    #[ORM\ManyToOne(targetEntity: File::class, inversedBy: 'companies')]
    private File $file;

    #[ORM\Column]
    private bool $treated;

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
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getLeader(): ?string
    {
        return $this->leader;
    }

    /**
     * @param string|null $leader
     */
    public function setLeader(?string $leader): void
    {
        $this->leader = $leader;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return bool
     */
    public function isTreated(): bool
    {
        return $this->treated;
    }

    /**
     * @param bool $treated
     */
    public function setTreated(bool $treated): void
    {
        $this->treated = $treated;
    }
}
