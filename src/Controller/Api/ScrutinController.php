<?php

namespace App\Controller\Api;

use App\Entity\Arrondissement;
use App\Entity\ResultatParArrondissement;
use App\Entity\Scrutin;
use App\Entity\SuffragesObtenus;
use App\Exception\ArrondissementNotFoundException;
use App\Exception\BadInputException;
use App\Repository\ArrondissementRepository;
use App\Repository\CandidatRepository;
use App\Repository\CommuneRepository;
use App\Repository\DepartementRepository;
use App\Repository\ScrutinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/scrutin', name: 'app_api_scrutin_')]
class ScrutinController extends AbstractController
{
    public function __construct(
        private readonly ScrutinRepository $scrutinRepository,
        private readonly CandidatRepository $candidatRepository,
        private readonly DepartementRepository $departementRepository,
        private readonly CommuneRepository $communeRepository,
        private readonly ArrondissementRepository $arrondissementRepository,
    ) {
    }

    #[Route('/{id}/data', name: 'data', methods: Request::METHOD_GET)]
    public function data(Scrutin $scrutin): Response
    {
        return $this->json(
            [
                'scrutin' => $scrutin,
                'candidats' => $this->candidatRepository->findBy(['scrutin' => $scrutin]),
                'departements' => $this->departementRepository->findBy([]),
                'communes' => $this->communeRepository->findBy([]),
                'arrondissements' => $this->arrondissementRepository->findBy(['estRemonte' => false]),
            ],
            Response::HTTP_OK,
            [],
            ['groups' => ['read:Scrutin:Data']]
        );
    }

    #[Route('/{id}/resultats/remonter-par-arrondissement', name: 'remonter_resultats', methods: Request::METHOD_POST)]
    public function remonterResultatsParArrondissement(Request $request, Scrutin $scrutin, EntityManagerInterface $entityManager): Response
    {
        $payload = json_decode($request->getContent(), true);

        $arrondissement = $this->arrondissementRepository->find(intval($payload['arrondissement'] ?? ''));
        if (!$arrondissement instanceof Arrondissement) {
            throw new BadInputException('Arrondissement non trouvé !');
        }

        if ($arrondissement->getEstRemonte()) {
            throw new BadInputException('Les résultats de cet arrondissement ont déjà été remontés !');
        }

        $inscrits = intval($payload['inscrits'] ?? '0');
        $votants = intval($payload['votants'] ?? '0');
        $nuls = intval($payload['nuls'] ?? '0');
        $suffrages = $payload['suffrages'];

        if (!is_array($suffrages)) {
            throw new BadRequestException('Les résultats sont erronés');
        }

        $candidats = $this->candidatRepository->findBy(['scrutin' => $scrutin]);
        $total = $nuls;

        foreach ($candidats as $candidat) {
            $total += $suffrages[$candidat->getId()];
        }

        if ($inscrits < $votants || $votants !== $total) {
            throw new BadInputException('Les résultats sont erronés');
        }

        $donneesRemontees = new ResultatParArrondissement();
        $donneesRemontees
            ->setArrondissement($arrondissement)
            ->setNbInscrits($inscrits)
            ->setNbVotants($votants)
            ->setNbBulletinsNuls($nuls)
        ;

        $entityManager->persist($donneesRemontees);

        foreach ($candidats as $candidat) {
            $suffrage = new SuffragesObtenus();
            $suffrage
                ->setCandidat($candidat)
                ->setNbVoix(
                    intval($suffrages[$candidat->getId()])
                )
            ;

            $donneesRemontees->addSuffrage($suffrage);

            $entityManager->persist($suffrage);
        }

        $arrondissement->setEstRemonte(true);

        $entityManager->flush();

        return $this->json('OK');
    }
}
