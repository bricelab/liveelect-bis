<?php

namespace App\Controller\Admin;

use App\Service\ScrutinDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class ScrutinDataController extends AbstractController
{

    public function __construct(
        private readonly ScrutinDataService $scrutinDataService,
    ) {
    }

    #[Route('/export-results', name: 'export_results')]
    public function exportResults(): Response
    {
        $filename = $this->scrutinDataService->export();

        return $this->file($filename);
    }

    #[Route('/export-results-par-ce', name: 'export_results_par_ce')]
    public function exportResultsParCE(): Response
    {
        $filename = $this->scrutinDataService->exportParCE();

        return $this->file($filename);
    }

    #[Route('/export-arrondissements-non-remontes', name: 'export_arrondissements_non_remontes')]
    public function exportArrondissementsNonRemontes(): Response
    {
        $filename = $this->scrutinDataService->exportArrondissements();

        return $this->file($filename);
    }

    #[Route('/export-arrondissements-remontes', name: 'export_arrondissements_remontes')]
    public function exportArrondissementsRemontes(): Response
    {
        $filename = $this->scrutinDataService->exportArrondissements(true);

        return $this->file($filename);
    }

    #[Route('/purge-results', name: 'purge_results')]
    public function purgeResults(): Response
    {
        $this->scrutinDataService->purgeData();

        return $this->redirectToRoute('admin_index');
    }
}
