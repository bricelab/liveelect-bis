<?php

namespace App\Controller\Admin;

use App\Entity\Circonscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield TextField::new('nom', 'Nom');
        yield AssociationField::new('arrondissements', 'Arrondissements');
    }
}
