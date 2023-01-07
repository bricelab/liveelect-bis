<?php

namespace App\Command;

use App\Entity\Arrondissement;
use App\Entity\CentreVote;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\PosteVote;
use App\Entity\SuperviseurArrondissement;
use App\Entity\VillageQuartier;
use App\Repository\ScrutinRepository;
use App\Repository\SuperviseurArrondissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

#[AsCommand(
    name: 'app:csv-import-superviseurs',
    description: 'Import a csv file',
)]
class CsvImportSuperviseursCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly KernelInterface $kernel,
        private readonly DecoderInterface $decoder,
        private readonly ScrutinRepository $scrutinRepository,
        private readonly SuperviseurArrondissementRepository $superviseurArrondissementRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $this->kernel->getProjectDir().'/imports/liste-superviseurs.csv';

        $io->note(sprintf('Importation des SUPERVISEURS D\'ARRONDISSEMENT depuis %s', $filename));

        $start = time();

        $tuples = $this->decoder->decode(file_get_contents($filename), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);

        $cpt = 0;

        $scrutin = $this->scrutinRepository->find(1);

        $doublons = [];
        $telephoneSA1Vide = [];
        $telephoneSA2Vide = [];

        foreach ($tuples as $tuple) {
            if (empty(trim($tuple['Tél SA1']))) {
                $telephoneSA1Vide[] = $tuple;
                continue;
            }

            if (empty(trim($tuple['Tél SA2']))) {
                $telephoneSA2Vide[] = $tuple;
                continue;
            }

            $telephoneSA1 = explode('/', $tuple['Tél SA1'], 2)[0];
            $superviseurSA1Existant = $this->superviseurArrondissementRepository->findOneBy(['telephone' => $telephoneSA1]);
            if ($superviseurSA1Existant instanceof SuperviseurArrondissement) {
                $doublons[] = $tuple;
            } else {
                $superviseur1 = new SuperviseurArrondissement();
                $superviseur1->setNom($tuple['Nom SA1']);
                $superviseur1->setPrenoms($tuple['Prénom SA1']);
                $superviseur1->setTelephone($telephoneSA1);
                $superviseur1->setScrutin($scrutin);
                $superviseur1->setRoles(['ROLE_SUPERVISEUR_ARRONDISSEMENT']);
                $this->em->persist($superviseur1);
                $cpt++;
            }

            $telephoneSA2 = explode('/', $tuple['Tél SA2'], 2)[0];
            $superviseurSA2Existant = $this->superviseurArrondissementRepository->findOneBy(['telephone' => $telephoneSA2]);
            if ($superviseurSA2Existant instanceof SuperviseurArrondissement) {
                $doublons[] = $tuple;
            } else {
                $superviseur2 = new SuperviseurArrondissement();
                $superviseur2->setNom($tuple['Nom SA2']);
                $superviseur2->setPrenoms($tuple['Prénom SA2']);
                $superviseur2->setTelephone($telephoneSA2);
                $superviseur2->setScrutin($scrutin);
                $superviseur2->setRoles(['ROLE_SUPERVISEUR_ARRONDISSEMENT']);
                $this->em->persist($superviseur2);
                $cpt++;
            }
            $this->em->flush();
        }

        $duration = time() - $start;

        $nbDoublons = sizeof($doublons);
        $nbTelephoneSA1Vide = sizeof($telephoneSA1Vide);
        $nbTelephoneSA2Vide = sizeof($telephoneSA2Vide);
        if ($nbDoublons === 0 && $nbTelephoneSA1Vide === 0 && $nbTelephoneSA2Vide === 0) {
            $io->success("Everything went well. ($cpt superviseurs traités) en $duration secondes");
        } else {
            $io->info('Certaines anomalies détectées dans le fichier :');
            $io->warning(sprintf('%d superviseurs SA1 avec des numéros vides', $nbTelephoneSA1Vide));
            foreach ($telephoneSA1Vide as $value) {
                $io->text(implode('|', $value));
            }
            $io->warning(sprintf('%d superviseurs SA2 avec des numéros vides', $nbTelephoneSA2Vide));
            foreach ($telephoneSA2Vide as $value) {
                $io->text(implode('|', $value));
            }
            $io->warning(sprintf('%d doublons détectés', $nbDoublons));
            foreach ($doublons as $value) {
                $io->text(implode('|', $value));
            }

            $io->success("$cpt superviseurs traités en $duration secondes");
        }

        return Command::SUCCESS;
    }
}
