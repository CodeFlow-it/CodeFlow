<?php

namespace App\Message;

class AnalysisMessage
{
    private string $directory;
    private string $type;
    private int $projectId;
    private int $userId;

    public function __construct(string $directory, string $type, int $projectId, int $userId)
    {
        $this->directory = $directory;
        $this->type = $type;
        $this->projectId = $projectId;
        $this->userId = $userId;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
