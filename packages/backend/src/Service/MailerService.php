<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Report;
use App\Entity\Project;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerService
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function sendProjectToUser(int $userId, int $projectId)
    {
        try {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);
            $report = $this->entityManager->getRepository(Report::class)->findLatestReportByProject($projectId);

            $file = $report->getSource() . '/' . $report->getFilename();
            $fileContent = "";

            if (file_exists($file)) {
                $handle = fopen($file, 'r');
                $fileContent .= fread($handle, filesize($file));
                fclose($handle);
            }

            $email = (new Email())
                ->from('no-reply@codeflow.com')
                ->to($user->getEmail())
                ->subject('Rapport d\'erreur')
                ->text($fileContent);

            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new \Exception("Error to sending mail", $e->getMessage());
        }
    }
}
