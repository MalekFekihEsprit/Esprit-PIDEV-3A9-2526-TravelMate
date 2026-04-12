<?php

namespace App\Controller\Admin;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBudgetController extends AbstractController
{

    #[Route('/admin/budget/{id}', name: 'admin_budget_show')]
    public function show(Budget $budget): Response
    {
        $depenses = $budget->getDepenses();

        $totalDepenses = 0;

        foreach ($depenses as $depense) {
            $totalDepenses += $depense->getMontantDepense();
        }

        $montantTotal = (float) $budget->getMontantTotal();
        $reste = $montantTotal - $totalDepenses;

        $nbDepenses = count($depenses);

        return $this->render('admin/admin_budget/show.html.twig', [
            'budget' => $budget,
            'depenses' => $depenses,
            'totalDepenses' => $totalDepenses,
            'reste' => $reste,
            'nbDepenses' => $nbDepenses
        ]);
    }

    #[Route('/admin/budgets', name: 'admin_budget_index')]
    public function index(BudgetRepository $budgetRepository): Response
    {
        $budgets = $budgetRepository->findAll();

        return $this->render('admin/admin_budget/index.html.twig', [
            'budgets' => $budgets
        ]);
    }
}