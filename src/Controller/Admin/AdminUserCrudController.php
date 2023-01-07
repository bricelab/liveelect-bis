<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdminUser::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un utilisateur')
            ->setEntityLabelInPlural('Liste des utilisateurs')
            ->setEntityPermission('ROLE_SUPER_ADMIN')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = [
            'ROLE_USER',
            'ROLE_ADMIN',
            'ROLE_SUPER_ADMIN',
        ];

        yield IdField::new('id', 'Identifiant')->onlyOnDetail();
        yield AssociationField::new('scrutin', 'Scrutin');
        yield TextField::new('nom', 'Nom');
        yield TextField::new('prenoms', 'Prénoms');
        yield EmailField::new('email', 'Adresse mail');
        yield TextField::new('plainPassword', 'Mot de passe')
            ->onlyOnForms()
            ->setFormType(PasswordType::class)
        ;
        yield ChoiceField::new('roles', 'Rôles')
            ->renderAsBadges()
            ->renderExpanded()
            ->setChoices(array_combine($roles, $roles))
            ->allowMultipleChoices()
            ->setPermission('ROLE_SUPER_ADMIN')
        ;
    }
}
