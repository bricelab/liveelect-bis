<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\CirconscriptionRepository;
use App\Repository\ResultatParArrondissementRepository;
use App\Repository\SuffragesObtenusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

final class ScrutinDataService
{

    public function __construct(
        private readonly EncoderInterface $encoder,
        private readonly CirconscriptionRepository $circonscriptionRepository,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
        private readonly SuffragesObtenusRepository $suffragesObtenusRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function export(): string
    {
        $results = $this->resultatParArrondissementRepository->findBy([]);
        $data = [];
        $cpt = 1;

        foreach ($results as $result) {
            $suffrages = $this->suffragesObtenusRepository->findBy(['resultatParArrondissement' => $result]);
            $suffrageData = [];
            foreach ($suffrages as $suffrage) {
                $suffrageData[$suffrage->getCandidat()->getSigle()] = $suffrage->getNbVoix();
            }

            $data[] = array_merge([
                '#' => $cpt++,
                'Département' => $result->getArrondissement()->getCommune()->getDepartement()->getNom(),
                'Commune' => $result->getArrondissement()->getCommune()->getNom(),
                'Arrondissement' => $result->getArrondissement()->getNom(),
                'Inscrits' => $result->getNbInscrits(),
                'Votants' => $result->getNbVotants(),
                'Bulletins nuls' => $result->getNbBulletinsNuls(),
            ], $suffrageData);
        }

        $filename = 'export_resultats_scrutin.csv';

        file_put_contents($filename, $this->encoder->encode($data, 'csv', [CsvEncoder::DELIMITER_KEY => ';']));
        return $filename;
    }

    public function purgeData(): void
    {
        $results = $this->resultatParArrondissementRepository->findBy([]);

        foreach ($results as $result) {
            $suffrages = $this->suffragesObtenusRepository->findBy(['resultatParArrondissement' => $result]);

            foreach ($suffrages as $suffrage) {
                $this->entityManager->remove($suffrage);
            }

            $result->getArrondissement()->setEstRemonte(false);

            $this->entityManager->remove($result);
        }

        $this->entityManager->flush();
    }

    public function exportParCE(): string
    {
        $circonscriptions = $this->circonscriptionRepository->findBy([]);

        $data = [];
        $cpt = 1;

        foreach ($circonscriptions as $circonscription) {
            $arrondissements = $circonscription->getArrondissements();
            foreach ($arrondissements as $arrondissement) {
                $results = $this->resultatParArrondissementRepository->findBy(['arrondissement' => $arrondissement]);
                foreach ($results as $result) {
                    $suffrages = $this->suffragesObtenusRepository->findBy(['resultatParArrondissement' => $result]);
                    $suffrageData = [];
                    foreach ($suffrages as $suffrage) {
                        $suffrageData[$suffrage->getCandidat()->getSigle()] = $suffrage->getNbVoix();
                    }

                    $data[] = array_merge([
                        '#' => $cpt++,
                        'Département' => $result->getArrondissement()->getCommune()->getDepartement()->getNom(),
                        'CE' => $circonscription->getNom(),
                        'Commune' => $result->getArrondissement()->getCommune()->getNom(),
                        'Arrondissement' => $result->getArrondissement()->getNom(),
                        'Inscrits' => $result->getNbInscrits(),
                        'Votants' => $result->getNbVotants(),
                        'Bulletins nuls' => $result->getNbBulletinsNuls(),
                    ], $suffrageData);
                }
            }

        }

        $filename = 'export_resultats_scrutin_par_ce.csv';

        file_put_contents($filename, $this->encoder->encode($data, 'csv', [CsvEncoder::DELIMITER_KEY => ';']));

        return $filename;
    }
}
