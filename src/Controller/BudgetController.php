<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\Depense;
use App\Form\BudgetType;
use App\Repository\BudgetRepository;
use App\Repository\VoyageRepository;
use App\Repository\DepenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/budget')]
class BudgetController extends AbstractController
{
    /* ════════════════════════════════════════════════════════
       BUDGET CRUD
    ════════════════════════════════════════════════════════ */

    #[Route('/', name: 'app_budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository): Response
    {
        return $this->render('budget/index.html.twig', [
            'budgets' => $budgetRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, VoyageRepository $voyageRepository): Response
    {
        $budget = new Budget();
        if ($this->getUser()) {
            $budget->setUser($this->getUser());
        }
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($budget);
            $entityManager->flush();
            $this->addFlash('success', 'Budget créé avec succès !');
            return $this->redirectToRoute('app_budget_index');
        }
        return $this->render('budget/new.html.twig', [
            'budget'  => $budget,
            'form'    => $form,
            'voyages' => $voyageRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_budget_show', methods: ['GET'])]
    public function show(Budget $budget): Response
    {
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Budget $budget, EntityManagerInterface $entityManager, VoyageRepository $voyageRepository): Response
    {
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Budget modifié avec succès !');
            return $this->redirectToRoute('app_budget_index');
        }
        return $this->render('budget/edit.html.twig', [
            'budget'  => $budget,
            'form'    => $form,
            'voyages' => $voyageRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $budget->getIdBudget(), $request->request->get('_token'))) {
            $entityManager->remove($budget);
            $entityManager->flush();
            $this->addFlash('success', 'Budget supprimé avec succès !');
        }
        return $this->redirectToRoute('app_budget_index');
    }

    /* ════════════════════════════════════════════════════════
       DÉPENSE CRUD
    ════════════════════════════════════════════════════════ */

    #[Route('/{budgetId}/depense/new', name: 'app_depense_new', methods: ['POST'])]
    public function newDepense(
        int $budgetId,
        Request $request,
        EntityManagerInterface $entityManager,
        BudgetRepository $budgetRepository
    ): Response {
        $budget = $budgetRepository->find($budgetId);
        if (!$budget) {
            $this->addFlash('error', 'Budget introuvable.');
            return $this->redirectToRoute('app_budget_index');
        }
        if (!$this->isCsrfTokenValid('depense_new', $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
        }
        $depense = new Depense();
        $depense->setBudget($budget);
        $errors = $this->hydrateDepense($depense, $request);
        if (!empty($errors)) {
            foreach ($errors as $err) { $this->addFlash('error', $err); }
            return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
        }
        $entityManager->persist($depense);
        $entityManager->flush();
        $this->addFlash('success', 'Dépense ajoutée avec succès !');
        return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
    }

    #[Route('/depense/{id}/edit', name: 'app_depense_edit', methods: ['POST'])]
    public function editDepense(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DepenseRepository $depenseRepository
    ): Response {
        $depense = $depenseRepository->find($id);
        if (!$depense) {
            $this->addFlash('error', 'Dépense introuvable.');
            return $this->redirectToRoute('app_budget_index');
        }
        $budgetId = $depense->getBudget()?->getIdBudget();
        $errors   = $this->hydrateDepense($depense, $request);
        if (!empty($errors)) {
            foreach ($errors as $err) { $this->addFlash('error', $err); }
            return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Dépense modifiée avec succès !');
        return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
    }

    #[Route('/depense/{id}/delete', name: 'app_depense_delete', methods: ['POST'])]
    public function deleteDepense(
        int $id,
        EntityManagerInterface $entityManager,
        DepenseRepository $depenseRepository
    ): Response {
        $depense = $depenseRepository->find($id);
        if (!$depense) {
            $this->addFlash('error', 'Dépense introuvable.');
            return $this->redirectToRoute('app_budget_index');
        }
        $budgetId = $depense->getBudget()?->getIdBudget();
        $entityManager->remove($depense);
        $entityManager->flush();
        $this->addFlash('success', 'Dépense supprimée avec succès !');
        return $this->redirectToRoute('app_budget_show', ['id' => $budgetId]);
    }

    /* ════════════════════════════════════════════════════════
       HELPER — hydration + validation dépense
    ════════════════════════════════════════════════════════ */

    private function hydrateDepense(Depense $depense, Request $request): array
    {
        $errors = [];

        $libelle = trim($request->request->get('libelle_depense', ''));
        if ($libelle === '') { $errors[] = 'Le libellé est requis.'; }
        elseif (mb_strlen($libelle) > 60) { $errors[] = 'Le libellé ne doit pas dépasser 60 caractères.'; }
        else { $depense->setLibelleDepense($libelle); }

        $montant = (float) $request->request->get('montant_depense', 0);
        if ($montant <= 0) { $errors[] = 'Le montant doit être supérieur à 0.'; }
        else { $depense->setMontantDepense($montant); }

        $categorie   = trim($request->request->get('categorie_depense', ''));
        $validCats   = ['Hébergement', 'Transport', 'Restauration', 'Loisirs', 'Achats', 'Santé', 'Autre'];
        if (!in_array($categorie, $validCats, true)) { $errors[] = 'Catégorie invalide.'; }
        else { $depense->setCategorieDepense($categorie); }

        $desc = trim($request->request->get('description_depense', ''));
        if ($desc === '') { $errors[] = 'La description est requise.'; }
        elseif (mb_strlen($desc) > 255) { $errors[] = 'La description ne doit pas dépasser 255 caractères.'; }
        else { $depense->setDescriptionDepense($desc); }

        $devise = trim($request->request->get('devise_depense', ''));
        $depense->setDeviseDepense($devise ?: null);

        $type       = trim($request->request->get('type_paiement', ''));
        $validTypes = ['Espèces', 'Carte bancaire', 'Virement', 'Mobile Pay', 'Autre'];
        if (!in_array($type, $validTypes, true)) { $errors[] = 'Type de paiement invalide.'; }
        else { $depense->setTypePaiement($type); }

        $dateStr = trim($request->request->get('date_creation', ''));
        if ($dateStr === '') { $errors[] = 'La date est requise.'; }
        else {
            $date = \DateTime::createFromFormat('Y-m-d', $dateStr);
            if (!$date) { $errors[] = 'Format de date invalide.'; }
            else { $depense->setDateCreation($date); }
        }

        return $errors;
    }
}