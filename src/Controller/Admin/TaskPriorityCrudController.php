<?php

namespace App\Controller\Admin;

use App\Entity\TaskPriority;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TaskPriorityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TaskPriority::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideWhenCreating()->setDisabled(true),
            TextField::new('name', 'Название'),
            TextField::new('code', 'Символьный код'),
        ];
    }
}
