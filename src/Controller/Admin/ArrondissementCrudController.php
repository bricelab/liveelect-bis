<?php

namespace App\Controller\Admin;

use App\Entity\Arrondissement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArrondissementCrudController extends AbstractCrudController
{
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield AssociationField::new('commune', 'Commune');
        yield TextField::new('nom', 'Nom');
        yield BooleanField::new('estRemonte', 'Est remonté ?')->renderAsSwitch(false)->hideOnForm();
    }
}
