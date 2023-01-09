<?php

namespace App\Controller\Admin;

use App\Entity\Arrondissement;
use App\Repository\ResultatParArrondissementRepository;
use App\Repository\SuffragesObtenusRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\Response;

class ArrondissementCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly SuffragesObtenusRepository $suffragesObtenusRepository,
        private readonly ResultatParArrondissementRepository $resultatParArrondissementRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Arrondissement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un arrondissement')
            ->setEntityLabelInPlural('Liste des arrondissements')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $resetDataAction = Action::new('resetResultatData', 'Réinitialiser')
            ->linkToCrudAction('resetResultatData')
            ->addCssClass('text-warning')
            ->displayIf(fn (Arrondissement $entity) => $entity->getEstRemonte() === true)
        ;
        return $actions
            ->add(Crud::PAGE_INDEX, $resetDataAction)
            ->setPermission(Action::BATCH_DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->remove(Crud::PAGE_INDEX, Action::DETAIL)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield AssociationField::new('commune', 'Commune');
        yield TextField::new('nom', 'Nom');
        yield BooleanField::new('estRemonte', 'Est remonté ?')->renderAsSwitch(false)->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('commune', 'Commune'))
            ->add('nom')
            ->add('estRemonte')
        ;
    }

    public function resetResultatData(AdminContext $context): Response
    {
        /** @var Arrondissement|null $arrondissement */
        $arrondissement = $context->getEntity()->getInstance();

        if ($arrondissement instanceof Arrondissement) {
            $resultat = $this->resultatParArrondissementRepository->findOneBy(['arrondissement' => $arrondissement]);

            if ($resultat) {
                $suffrages = $this->suffragesObtenusRepository->findBy(['resultatParArrondissement' => $resultat]);

                foreach ($suffrages as $suffrage) {
                    $this->entityManager->remove($suffrage);
                }

                $this->entityManager->remove($resultat);

                $arrondissement->setEstRemonte(false);

                $this->entityManager->flush();
            }
        }

        if ($context->getReferrer()) {
            return $this->redirect($context->getReferrer());
        }

        return $this->redirectToRoute('admin_index');
    }
}
