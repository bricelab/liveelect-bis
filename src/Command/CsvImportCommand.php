<?php

namespace App\Command;

use App\Entity\Arrondissement;
use App\Entity\CentreVote;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\PosteVote;
use App\Entity\VillageQuartier;
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
    name: 'app:csv-import',
    description: 'Import a csv file using league/csv',
)]
class CsvImportCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly KernelInterface $kernel, private readonly DecoderInterface $decoder, string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

//        $filename = $this->kernel->getProjectDir().'/imports/liste-arrondissement.csv';
        $filename = $this->kernel->getProjectDir().'/imports/liste-postes-de-vote.csv';

        $io->note(sprintf('Importation des POSTES DE VOTE depuis %s', $filename));

        $start = microtime(true);

        $tuples = $this->decoder->decode(file_get_contents($filename), 'csv', [CsvEncoder::DELIMITER_KEY => ';']);

        $cpt = 0;

        $departementNames = [...array_unique(array_column($tuples, 'DEPARTEMENT'))];

        foreach ($departementNames as $departementName) {
            $departement = new Departement();
            $departement->setNom($departementName);
            $this->em->persist($departement);
            $this->em->flush();

            $communeNames = array_filter($tuples, function ($tuple) use ($departementName) {
                return $tuple['DEPARTEMENT'] === $departementName;
            });

            $communeNames = [...array_unique(array_column($communeNames, 'COMMUNE'))];

            foreach ($communeNames as $communeName) {
                $commune = new Commune();
                $commune
                    ->setNom($communeName)
                    ->setDepartement($departement)
                ;
                $this->em->persist($commune);
                $this->em->flush();

                $arrondissementNames = array_filter($tuples, function ($tuple) use ($departementName, $communeName) {
                    return $tuple['DEPARTEMENT'] === $departementName && $tuple['COMMUNE'] === $communeName;
                });

                $arrondissementNames = [...array_unique(array_column($arrondissementNames, 'ARRONDISSEMENT'))];

                foreach ($arrondissementNames as $arrondissementName) {
                    $arrondissement = new Arrondissement();
                    $arrondissement
                        ->setNom($arrondissementName)
                        ->setCommune($commune)
                    ;
                    $this->em->persist($arrondissement);
//                    $this->em->flush();

                    $villageQuartierNames = array_filter($tuples, function ($tuple) use (
                        $departementName,
                        $communeName,
                        $arrondissementName
                    ) {
                        return $tuple['DEPARTEMENT'] === $departementName &&
                            $tuple['COMMUNE'] === $communeName &&
                            $tuple['ARRONDISSEMENT'] === $arrondissementName;
                    });

                    $villageQuartierNames = [...array_unique(array_column($villageQuartierNames, 'VILLAGE_QUARTIER'))];

                    foreach ($villageQuartierNames as $villageQuartierName) {
                        $villageQuartier = new VillageQuartier();
                        $villageQuartier
                            ->setNom($villageQuartierName)
                            ->setArrondissement($arrondissement)
                        ;
                        $this->em->persist($villageQuartier);
//                        $this->em->flush();

                        $centreVoteNames = array_filter($tuples, function ($tuple) use (
                            $departementName,
                            $communeName,
                            $arrondissementName,
                            $villageQuartierName
                        ) {
                            return $tuple['DEPARTEMENT'] === $departementName &&
                                $tuple['COMMUNE'] === $communeName &&
                                $tuple['ARRONDISSEMENT'] === $arrondissementName &&
                                $tuple['VILLAGE_QUARTIER'] === $villageQuartierName;
                        });

                        $centreVoteNames = [...array_unique(array_column($centreVoteNames, 'CENTRE DE VOTE'))];

                        foreach ($centreVoteNames as $centreVoteName) {
                            $centreVote = new CentreVote();
                            $centreVote
                                ->setNom($centreVoteName)
                                ->setVillageQuartier($villageQuartier)
                            ;
                            $this->em->persist($centreVote);
//                            $this->em->flush();

                            $posteVoteNames = array_filter($tuples, function ($tuple) use (
                                $departementName,
                                $communeName,
                                $arrondissementName,
                                $villageQuartierName,
                                $centreVoteName
                            ) {
                                return $tuple['DEPARTEMENT'] === $departementName &&
                                    $tuple['COMMUNE'] === $communeName &&
                                    $tuple['ARRONDISSEMENT'] === $arrondissementName &&
                                    $tuple['VILLAGE_QUARTIER'] === $villageQuartierName &&
                                    $tuple['CENTRE DE VOTE'] === $centreVoteName;
                            });

                            $posteVoteNames = [...array_unique(array_column($posteVoteNames, 'POSTE DE VOTE'))];

                            foreach ($posteVoteNames as $posteVoteName) {
                                $posteVote = new PosteVote();
                                $posteVote
                                    ->setNom($posteVoteName)
                                    ->setCentreVote($centreVote);
                                $this->em->persist($posteVote);
//                                $this->em->flush();

                                $cpt++;

                                $io->text(
                                    sprintf(
                                        '%d => %s | %s | %s | %s | %s | %s',
                                        $cpt,
                                        $departementName,
                                        $communeName,
                                        $arrondissementName,
                                        $villageQuartierName,
                                        $centreVoteName,
                                        $posteVoteName,
                                    )
                                );
                            }
                        }
                    }
                }
            }
        }

        $this->em->flush();

        $duration = (microtime(true) - $start)/1000000;

        $io->success("Everything went well. ($cpt postes de vote traités) en $duration secondes");

        return Command::SUCCESS;
    }
}
