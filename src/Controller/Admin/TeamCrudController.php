<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TeamCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Team::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideWhenCreating()->setDisabled(true),
            TextField::new('name', 'Название'),
            TextField::new('code', 'Символьный код'),
            AssociationField::new('users', 'Участники')
                ->setCrudController(UserCrudController::class)
                ->setFormTypeOption('by_reference', false)
        ];
    }

}
