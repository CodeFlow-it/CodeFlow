<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Service for cloning project from github
 */
class CloneRepositoryService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function clone(string $url, string $username, string $projectName): string
    {
        $targetDirectory = $this->createUserDirectory($username, $projectName);
        $process = new Process(['git', 'clone', $url, $targetDirectory]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $targetDirectory;
    }

    private function createUserDirectory(string $username, string $projectName): string
    {
        $targetDirectory = $this->params->get('kernel.project_dir') . '/repositories/' . '/' .  $username . '/' . $projectName;
        $filesystem = new Filesystem();

        if ($filesystem->exists($targetDirectory)) {
            $filesystem->remove($targetDirectory);
        }

        $filesystem->mkdir($targetDirectory);

        return $targetDirectory;
    }
}