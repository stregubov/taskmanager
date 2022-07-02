<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Security;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideWhenCreating()->setDisabled(true);
        yield EmailField::new('email');
        yield TextField::new('password')->hideOnIndex()->hideWhenUpdating();

        yield AssociationField::new('groups', 'Роли')
            ->setCrudController(RoleCrudController::class)
            ->setFormTypeOption('by_reference', false);

        yield TextField::new('lastName', 'Фамилия');
        yield TextField::new('firstName', 'Имя');
        yield TextField::new('secondName', 'Отчество');
        yield DateField::new('birthDate', 'Дата рождения');
        yield TextField::new('position', 'Должность');
    }

}
