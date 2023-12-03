<?php

namespace App\Message;

class CompanyMessage
{
    public function __construct(private int $companyId)
    {
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): void
    {
        $this->companyId = $companyId;
    }
}
