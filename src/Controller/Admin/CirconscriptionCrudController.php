<?php

namespace App\Controller\Admin;

use App\Entity\Circonscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CirconscriptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Circonscription::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('une circonscription')
            ->setEntityLabelInPlural('Liste des circonscriptions')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::BATCH_DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield TextField::new('nom', 'Nom');
        yield IntegerField::new('siege', 'Nombre de sièges');
        yield AssociationField::new('arrondissements', 'Arrondissements')
            ->setTemplatePath('admin/circonscription/arrondissement.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('nom')
            ->add('siege')
            ->add('arrondissements')
        ;
    }
}
