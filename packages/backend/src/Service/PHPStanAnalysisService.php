<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Entity\Report;
use App\Entity\Project;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Service to launch a code analysis with PHPStan
 */
class PHPStanAnalysisService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Run the analysis
     *
     * @param  string $sourceDirectory The directory where the code to be analyzed is located
     * @param  int $projectId
     * @return void
     */
    public function run(string $sourceDirectory, int $projectId): void
    {
        $reportDirectory = str_replace('repositories', 'reports', $sourceDirectory);

        $process = new Process([
            'vendor/bin/phpstan',
            'analyse',
            $sourceDirectory
        ]);

        try {
            $process->run();

            if (!$process->isSuccessful() || $process->getExitCode() !==  0) {
                $jsonResult = $process->getOutput();

                $filename = $this->saveToText('phpstan', $jsonResult, $reportDirectory);
                $this->storeReport($projectId, $reportDirectory, $filename);
            }
        } catch (ProcessFailedException) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * Save the analysis report in txt file
     *
     * @param  string $filename
     * @param  string $data
     * @param  string $reportDirectory
     * @return string $filename
     */
    private function saveToText(string $filename, string $data, string $reportDirectory): string
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($reportDirectory)) {
            $filesystem->remove($reportDirectory);
        }

        $filesystem->mkdir($reportDirectory);

        $fileName = $filename . '_' . time() . '.txt';
        $filePath = $reportDirectory . '/' . $fileName;

        if (file_put_contents($filePath, $data) === false) {
            throw new FileException("Unable to save file.");
        }

        return $filename;
    }

    private function storeReport(int $projectId, string $source, string $filename)
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);

        $report = new Report();
        $report->setProject($project);
        $report->setSource($source);
        $report->setFilename($filename);
        $report->setType('phpstan');
        $report->setCreatedAt(new DateTime('now'));

        $this->entityManager->persist($report);
        $this->entityManager->flush();
    }
}
