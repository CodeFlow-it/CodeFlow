<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PHPStanAnalysisService
{
    private $outputFormat = 'prettyJson';

    public function run(string $sourceDirectory): void
    {
        $process = new Process([
            'vendor/bin/phpstan',
            'analyse',
            '--error-format=' . $this->outputFormat,
            $sourceDirectory
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResult = $process->getOutput();

        $this->saveToJson('phpstan', $jsonResult, $sourceDirectory);
    }

    private function saveToJson(string $filename, string $data, string $sourceDirectory): void
    {
        $info = pathinfo($sourceDirectory);
        $destination = $info['dirname'] . '/reports';

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        if (!is_writable($destination)) {
            throw new FileException("The destination directory is not writable.");
        }

        $fileName = $filename . '_' . time() . '.' . 'json';
        $filePath = $destination . '/' . $fileName;

        if (file_put_contents($filePath, $data) === false) {
            throw new FileException("Unable to save file.");
        }
    }
}
