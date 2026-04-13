<?php

namespace App\Controller\Admin;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\VoyageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBudgetController extends AbstractController
{
    #[Route('/admin/budget/{id}', name: 'admin_budget_show')]
    public function show(Budget $budget): Response
    {
        $depenses = $budget->getDepenses();
        $totalDepenses = 0;
        $parCategorie = [];

        foreach ($depenses as $depense) {
            $montant = $depense->getMontantDepense();
            $totalDepenses += $montant;
            $cat = $depense->getCategorieDepense() ?? 'Autre';
            $parCategorie[$cat] = ($parCategorie[$cat] ?? 0) + $montant;
        }

        $montantTotal = (float) $budget->getMontantTotal();
        $reste = $montantTotal - $totalDepenses;
        $progression = $montantTotal > 0 ? round(($totalDepenses / $montantTotal) * 100, 1) : 0;

        return $this->render('admin/admin_budget/show.html.twig', [
            'budget'        => $budget,
            'depenses'      => $depenses,
            'totalDepenses' => $totalDepenses,
            'reste'         => $reste,
            'nbDepenses'    => count($depenses),
            'progression'   => $progression,
            'parCategorie'  => $parCategorie,
        ]);
    }

    #[Route('/admin/budgets', name: 'admin_budget_index')]
    public function index(Request $request, BudgetRepository $budgetRepository, VoyageRepository $voyageRepository): Response
    {
        $voyages = $voyageRepository->findAll();
        $voyageId = $request->query->get('voyage');

        if ($voyageId) {
            $budgets = $budgetRepository->findBy(['voyage' => $voyageId]);
        } else {
            $budgets = $budgetRepository->findAll();
        }

        return $this->render('admin/admin_budget/index.html.twig', [
            'budgets'          => $budgets,
            'voyages'          => $voyages,
            'selectedVoyageId' => $voyageId,
        ]);
    }
}