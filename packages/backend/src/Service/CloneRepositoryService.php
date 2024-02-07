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
    /**
     * @var Container parameter
     */
    private $params;

    /**
     * CloneRepositoryService constructor
     *
     * @param  ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * Clone a repository from github into the user directory
     *
     * @param  string $url
     * @param  string $username
     * @param  string $projectName
     * @return string $targetDirectory
     */
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

    /**
     * Create the user directory for his projects
     *
     * @param  string $username
     * @param  string $projectName
     * @return string $targetDirectory
     */
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
