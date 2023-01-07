<?php

namespace App\Controller\Admin;

use App\Repository\ResultatParArrondissementRepository;
use App\Repository\SuffragesObtenusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

#[Route('/admin', name: 'admin_')]
class ExportController extends AbstractController
{

    public function __construct(
        private readonly EncoderInterface $encoder,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
        private readonly SuffragesObtenusRepository $suffragesObtenusRepository,
    ) {
    }

    #[Route('/export-results', name: 'export_results')]
    public function exportResults(): Response
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

        return $this->file($filename);
    }
}
