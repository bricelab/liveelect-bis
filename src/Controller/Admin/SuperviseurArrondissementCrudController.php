<?php

namespace App\Controller\Admin;

use App\Entity\SuperviseurArrondissement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

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

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::BATCH_DELETE, 'ROLE_SUPER_ADMIN')
            ->remove(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield AssociationField::new('scrutin', 'Scrutin')->setPermission('ROLE_SUPER_ADMIN');
        yield TelephoneField::new('telephone', 'Téléphone');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenoms', 'Prénoms');
    }

    public function configureFilters(Filters $filters): Filters
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $filters->add(EntityFilter::new('scrutin', 'Scrutin'));
        }
        return $filters
            ->add('telephone')
            ->add('nom')
            ->add('prenoms')
        ;
    }
}
