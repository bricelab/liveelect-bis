<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\Arrondissement;
use App\Entity\Candidat;
use App\Entity\Circonscription;
use App\Entity\Commune;
use App\Entity\Departement;
use App\Entity\Scrutin;
use App\Entity\SuperviseurArrondissement;
use App\Repository\ArrondissementRepository;
use App\Repository\ResultatParArrondissementRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly ArrondissementRepository $arrondissementRepository,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'taux_remontes' => $this->tauxRemontesData(),
            'nb_remontes' => $this->nbRemontesData(),
        ]);
    }



    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Elect Manager');
    }

   public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');

        yield MenuItem::section('Données du scrutin');
        yield MenuItem::linkToCrud('Scrutins', 'fas fa-list', Scrutin::class)->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Candidats', 'fas fa-list', Candidat::class);
        yield MenuItem::linkToCrud('Superviseurs', 'fas fa-list', SuperviseurArrondissement::class);

        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Départements', 'fas fa-list', Departement::class);
        yield MenuItem::linkToCrud('Communes', 'fas fa-list', Commune::class);
        yield MenuItem::linkToCrud('Arrondissements', 'fas fa-list', Arrondissement::class);
        yield MenuItem::linkToCrud('Circonscriptions', 'fas fa-list', Circonscription::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', AdminUser::class)->setPermission('ROLE_SUPER_ADMIN');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin')
        ;
    }

    private function tauxRemontesData(): array
    {
        $all = $this->arrondissementRepository->countAllByDepartement();
        $remontes = $this->arrondissementRepository->countAllRemontesByDepartement();

        $data = [];

        foreach ($all as $key => $value) {
            $data[$key] = round(($remontes[$key] ?? 0) * 100 / $value, 2);
        }

        return [
            'xAxis' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Taux de remontée',
                    'data' => array_values($data)
                ]
            ],
        ];
    }

    private function nbRemontesData(): array
    {
        $all = $this->arrondissementRepository->countAllByDepartement();
        $remontes = $this->arrondissementRepository->countAllRemontesByDepartement();

        $data = [];

        foreach ($all as $key => $value) {
            $data[$key] = $remontes[$key] ?? 0;
        }

        return [
            'xAxis' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => array_values($all)
                ],
                [
                    'label' => 'Nombre remonté',
                    'data' => array_values($data)
                ],
            ],
        ];
    }
}
