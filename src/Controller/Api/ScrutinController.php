<?php

namespace App\Controller\Api;

use App\Entity\Arrondissement;
use App\Entity\ResultatParArrondissement;
use App\Entity\SuffragesObtenus;
use App\Entity\SuperviseurArrondissement;
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
        private readonly CandidatRepository $candidatRepository,
        private readonly DepartementRepository $departementRepository,
        private readonly CommuneRepository $communeRepository,
        private readonly ArrondissementRepository $arrondissementRepository,
    ) {
    }

    #[Route('/data', name: 'data', methods: Request::METHOD_GET)]
    public function data(): Response
    {
        $superviseur = $this->getUser();

        if (!$superviseur instanceof SuperviseurArrondissement) {
            throw $this->createAccessDeniedException();
        }
        $scrutin = $superviseur->getScrutin();

        return $this->json(
            [
                'superviseur' => $superviseur,
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

    #[Route('/resultats/remonter-par-arrondissement', name: 'remonter_resultats', methods: Request::METHOD_POST)]
    public function remonterResultatsParArrondissement(Request $request, EntityManagerInterface $entityManager): Response
    {
        $superviseur = $this->getUser();

        if (!$superviseur instanceof SuperviseurArrondissement) {
            throw $this->createAccessDeniedException();
        }
        $scrutin = $superviseur->getScrutin();

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

        if ($inscrits < 0 || $votants < 0 || $nuls < 0 || !is_array($suffrages)) {
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
            $nbVoix = $suffrages[$candidat->getId()];

            if ($nbVoix < 0) {
                throw new BadRequestException('Les résultats sont erronés');
            }

            $suffrage = new SuffragesObtenus();

            $suffrage
                ->setCandidat($candidat)
                ->setNbVoix(
                    intval($nbVoix)
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
