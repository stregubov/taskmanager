<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\TaskPriority;
use App\Entity\TaskType;
use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/default/dashboard.html.twig');
    }

    #[Route('/kanban', name: 'kanban')]
    public function kanban(): Response
    {
        return $this->json(['name' => 'kanban']);
    }

    #[Route('/my-tasks', name: 'my-tasks')]
    public function myTasks(): Response
    {
        return $this->json(['name' => 'my-tasks']);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TaskManager');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        if ($this->security->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToRoute('Канбан', 'fas fa-users', 'kanban');
            yield MenuItem::linkToCrud('Задачи', 'fas fa-users', Task::class);

            yield MenuItem::linkToCrud('Пользователи', 'fas fa-users', User::class);

            yield MenuItem::section('Отчеты');
            yield MenuItem::linkToCrud('Ресурсный учёт по задачам', 'fas fa-users', User::class);
            yield MenuItem::linkToCrud('Ресурсный учёт по исполнителям', 'fas fa-user-secret', Role::class);

            yield MenuItem::section('Справочники');
            yield MenuItem::linkToCrud('Проекты', 'fas fa-users', Project::class);
            yield MenuItem::linkToCrud('Команды', 'fas fa-user-secret', Team::class);
            yield MenuItem::linkToCrud('Статусы', 'fas fa-user-secret', Status::class);
            yield MenuItem::linkToCrud('Типы задач', 'fas fa-user-secret', TaskType::class);
            yield MenuItem::linkToCrud('Приоритеты задач', 'fas fa-user-secret', TaskPriority::class);
            yield MenuItem::linkToCrud('Роли', 'fas fa-user-secret', Role::class);
        } else {
            yield MenuItem::linkToRoute('Мои задачи', 'fas fa-users', 'kanban');
        }
    }
}
