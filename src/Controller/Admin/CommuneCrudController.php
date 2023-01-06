<?php

namespace App\Controller\Admin;

use App\Entity\Commune;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommuneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commune::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('une commune')
            ->setEntityLabelInPlural('Liste des communes')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield AssociationField::new('departement', 'Département');
        yield TextField::new('nom', 'Nom');
    }
}
