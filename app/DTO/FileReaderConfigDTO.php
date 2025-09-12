<?php

namespace App\DTO;

class FileReaderConfigDTO
{
    private string $filePath;

    private ?string $separator = null;

    private ?string $enclosure = null;

    private ?string $escape = null;

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): FileReaderConfigDTO
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getSeparator(): ?string
    {
        return $this->separator;
    }

    public function setSeparator(?string $separator): FileReaderConfigDTO
    {
        $this->separator = $separator;

        return $this;
    }

    public function getEnclosure(): ?string
    {
        return $this->enclosure;
    }

    public function setEnclosure(?string $enclosure): FileReaderConfigDTO
    {
        $this->enclosure = $enclosure;

        return $this;
    }

    public function getEscape(): ?string
    {
        return $this->escape;
    }

    public function setEscape(?string $escape): FileReaderConfigDTO
    {
        $this->escape = $escape;

        return $this;
    }
}
