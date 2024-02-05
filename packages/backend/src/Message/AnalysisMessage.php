<?php

namespace App\Message;

class AnalysisMessage
{
    private string $directory;
    private string $type;

    public function __construct(string $directory, string $type)
    {
        $this->directory = $directory;
        $this->type = $type;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getType()
    {
        return $this->type;
    }
}
