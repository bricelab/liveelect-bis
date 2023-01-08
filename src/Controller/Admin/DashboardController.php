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
use App\Repository\CirconscriptionRepository;
use App\Repository\ResultatParArrondissementRepository;
use App\Repository\SuffragesObtenusRepository;
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
        private readonly CirconscriptionRepository $circonscriptionRepository,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
        private readonly SuffragesObtenusRepository $suffragesObtenusRepository,
    ) {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $circonscriptions = $this->circonscriptionRepository->findBy([], ['nom' => 'ASC']);
        $circonscriptionsData = [];
        $suffragesNational = $this->suffragesObtenusRepository->suffragesExprimesNational();
        foreach ($circonscriptions as $circonscription) {
//            dump($circonscription, $this->calculerNbSieges($circonscription, $suffragesNational));
            $circonscriptionsData[] = [
                'circonscription' => $circonscription,
                'tauxDepouillement' => $this->tauxDepouillement($circonscription),
                'tauxParticipation' => $this->tauxParticipation($circonscription),
            ];
        }
        return $this->render('admin/dashboard.html.twig', [
            'taux_remontes' => $this->tauxRemontesData(),
            'taux_participation_national' => $this->resultatParArrondissementRepository->tauxParticipationNational(),
            'taux_votes_nuls' => $this->resultatParArrondissementRepository->tauxVotesNuls(),
            'taux_depouillement' => $this->tauxDepouillement(),
            'nb_remontes' => $this->nbRemontesData(),
            'circonscriptionsData' => $circonscriptionsData,
            'arrondissements_restants' => $this->arrondissementRepository->count(['estRemonte' => false]),
            'arrondissements_remontes' => $this->arrondissementRepository->count(['estRemonte' => true]),
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
        $circonscriptions = $this->circonscriptionRepository->findBy([], ['nom' => 'ASC']);

        $data = [];

        foreach ($circonscriptions as $circonscription) {
            $data[$circonscription->getNom()] = $this->tauxDepouillement($circonscription);
        }

        return [
            'xAxis' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Taux de remontée (en %)',
                    'data' => array_values($data)
                ]
            ],
        ];
    }

    private function tauxRemontesData2(): array
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
                    'label' => 'Taux de remontée (en %)',
                    'data' => array_values($data)
                ]
            ],
        ];
    }

    private function nbRemontesData(): array
    {

        $circonscriptions = $this->circonscriptionRepository->findBy([], ['nom' => 'ASC']);

        $all = [];
        $data = [];

        foreach ($circonscriptions as $circonscription) {
            $arrondissements = $circonscription->getArrondissements();

            $nbRem = 0;

            foreach ($arrondissements as $arrondissement) {
                if ($arrondissement->getEstRemonte()) {
                    $nbRem++;
                }
            }
            $all[$circonscription->getNom()] = sizeof($arrondissements);
            $data[$circonscription->getNom()] = $nbRem;
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

    private function nbRemontesData2(): array
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

    private function tauxDepouillement(?Circonscription $circonscription = null): float
    {
        if ($circonscription === null) {
            $nbArr = $this->arrondissementRepository->count([]);
            $nbRem = $this->arrondissementRepository->count(['estRemonte' => true]);
        } else {
            $arrondissements = $circonscription->getArrondissements();

            $nbRem = 0;

            foreach ($arrondissements as $arrondissement) {
                if ($arrondissement->getEstRemonte()) {
                    $nbRem++;
                }
            }

            $nbArr = sizeof($arrondissements);
        }

        return $nbArr === 0 ? 0 : round($nbRem * 100 / $nbArr, 2);
    }

    private function tauxParticipation(Circonscription $circonscription): float
    {
        $arrondissements = $circonscription->getArrondissements();

        $nbVotTotal = $nbInsTotal = 0;

        foreach ($arrondissements as $arrondissement) {
            $nbInscritsEtVotantsParArrondissement = $this->resultatParArrondissementRepository->nbInscritsEtVotantsParArrondissement($arrondissement);

            $nbVotTotal += $nbInscritsEtVotantsParArrondissement['nbVotants'];
            $nbInsTotal += $nbInscritsEtVotantsParArrondissement['nbInscrits'];
        }

        return $nbInsTotal === 0 ? 0 : round($nbVotTotal * 100 / $nbInsTotal, 2);
    }

//    private function calculerNbSieges(Circonscription $circonscription, int $suffragesNational): array
//    {
//        $suffragesExprimes = $this->suffragesObtenusRepository->suffragesExprimesParCirconscription($circonscription);
//        $suffragesObtenus = $this->suffragesObtenusRepository->suffragesObtenusParCandidatParCirconscription($circonscription);
//
//        $quotientElectoral = $suffragesExprimes / $circonscription->getSiege();
//        $siegesObtenus = [];
//        foreach ($suffragesObtenus as $suffragesObtenu) {
//            $tauxNational = $suffragesNational === 0 ? 0 : round($suffragesObtenu['suffrages_obtenus'] * 100 / $suffragesNational, 2);
//            if ($tauxNational >= 10.) {
//                $siegesObtenus[($suffragesObtenu['sigle'])] = intval($suffragesObtenu['suffrages_obtenus'] / $quotientElectoral);
//            }
//        }
//
////        dump($suffragesObtenus, $siegesObtenus);
//
////        do {
//            $siegeRestant = $circonscription->getSiege() - array_reduce($siegesObtenus, function ($acc, $siege) {
//                return $acc + $siege;
//            });
//
//            if($siegeRestant > 0) {
//                $quotients = [];
//                foreach ($suffragesObtenus as $suffragesObtenu) {
//                    $tauxNational = $suffragesNational === 0 ? 0 : round($suffragesObtenu['suffrages_obtenus'] * 100 / $suffragesNational, 2);
//                    if ($tauxNational >= 10.) {
////                        dump($siegeRestant);
//                        $quotients[($suffragesObtenu['sigle'])] = isset($siegesObtenus[$suffragesObtenu['sigle']]) && $siegesObtenus[$suffragesObtenu['sigle']] !== 0 ?
//                            ($suffragesObtenu['suffrages_obtenus'] / $siegesObtenus[$suffragesObtenu['sigle']]) + 1 : 0;
//                    }
//                }
//                asort($quotients);
//                $candidatIds = array_keys($quotients);
//                $cid = array_pop($candidatIds);
//                $siegesObtenus[$cid]++;
//            }
////        }while($siegeRestant > 0);
//
//        return $siegesObtenus;
//    }
}
