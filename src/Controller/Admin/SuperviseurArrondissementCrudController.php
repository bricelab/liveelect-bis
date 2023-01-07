<?php

namespace App\Controller\Admin;

use App\Entity\SuperviseurArrondissement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SuperviseurArrondissementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SuperviseurArrondissement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un superviseur')
            ->setEntityLabelInPlural('Liste des superviseurs')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield AssociationField::new('scrutin', 'Scrutin');
        yield TelephoneField::new('telephone', 'Téléphone');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenoms', 'Prénoms');
    }
}
