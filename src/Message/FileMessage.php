<?php

namespace App\Message;

class FileMessage
{
    public function __construct(
        private int $fileId,
        private string $filePath,
    ) {
    }

    public function getFileId(): int
    {
        return $this->fileId;
    }

    public function setFileId(int $fileId): void
    {
        $this->fileId = $fileId;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }
}
