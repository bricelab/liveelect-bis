<?php

namespace App\Controller\Admin;

use App\Entity\Arrondissement;
use App\Entity\Candidat;
use App\Entity\Circonscription;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\Scrutin;
use App\Repository\ArrondissementRepository;
use App\Repository\DepartementRepository;
use App\Repository\ResultatParArrondissementRepository;
use App\Repository\SuffragesObtenusRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly DecoderInterface $decoder,
        private readonly EncoderInterface $encoder,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
        private readonly SuffragesObtenusRepository $suffragesObtenusRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
//            'rapportOuvertureGraphe' => $this->buildRapportOuvertureGraphe(),
        ]);
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

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Elect Manager');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
//            ->showEntityActionsInlined()
//            ->setDefaultSort(['nom' => 'ASC'])
        ;
    }

   public function configureActions(): Actions
    {
        return Actions::new()
            ->addBatchAction(Action::BATCH_DELETE)
            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)

            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, Action::INDEX)

            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');

//        yield MenuItem::section('Visualisation');
//        yield MenuItem::linkToCrud('Rapports d\'ouverture', 'fas fa-list', RapportOuverture::class);
//        yield MenuItem::linkToCrud('Incidents signalés', 'fas fa-list', RapportOuverture::class);
//        yield MenuItem::linkToCrud('Résultats du scrutin', 'fas fa-list', RapportOuverture::class);

        yield MenuItem::section('Données du scrutin');
        yield MenuItem::linkToCrud('Scrutins', 'fas fa-list', Scrutin::class);
        yield MenuItem::linkToCrud('Candidats', 'fas fa-list', Candidat::class);
//        yield MenuItem::linkToCrud('Rapports d\'ouverture', 'fas fa-list', RapportOuverture::class);
//        yield MenuItem::linkToCrud('Incidents signalés', 'fas fa-list', IncidentSignale::class);

        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Arrondissements', 'fas fa-list', Arrondissement::class);
        yield MenuItem::linkToCrud('Circonscriptions', 'fas fa-list', Circonscription::class);
        yield MenuItem::linkToCrud('Communes', 'fas fa-list', Commune::class);
        yield MenuItem::linkToCrud('Départements', 'fas fa-list', Departement::class);
//        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', Utilisateur::class);
    }

    private function buildRapportOuvertureGraphe(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);

        $all = $this->arrondissementRepository->countAllByDepartement();
        $allRemonted = $this->arrondissementRepository->countAllRemontedByDepartement();

        $chart->setData([
            'labels' => array_map(static function(Departement $departement) {
                return $departement->getNom();
            }, $this->departementRepository->findBy([])),
            'datasets' => [
                [
                    'label' => 'Paiements reçus',
                    'backgroundColor' => '#1ea471',
                    'borderColor' => '#1ea471',
                    'data' => array_map(function (array $value) use ($allRemonted) {
                        $arr = array_filter($allRemonted, function (array $v) use ($value) {
                            return $v['nom'] === $value['nom'];
                        });
                        if (sizeof($arr) === 0) {
                            return 0;
                        } elseif (sizeof($arr) === 1) {
                            return round($arr[0][1] *100 / $value[1], 2);
                        }
                        throw new \LogicException();
                    }, $all),
                ],
//                [
//                    'label' => 'Impayés',
//                    'backgroundColor' => '#ef4444',
//                    'borderColor' => '#ef4444',
//                    'data' => array_values($nbUnpaidPerMonth),
//                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $chart;
    }
}
