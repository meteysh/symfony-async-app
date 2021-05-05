<?php

namespace App\Message;

class PackageData
{
    private $content;

    public function __construct(array $content)
    {
        $this->content = $content;
    }

    public function getContent(): array
    {
        return $this->content;
    }
}
