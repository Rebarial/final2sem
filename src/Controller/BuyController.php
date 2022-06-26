<?php

namespace App\Controller;

use App\Entity\Buy;
use App\Form\BuyType;
use App\Repository\BuyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/buy")
 */
class BuyController extends AbstractController
{
    /**
     * @Route("/", name="app_buy_index", methods={"GET"})
     */
    public function index(BuyRepository $buyRepository): Response
    {
        return $this->render('buy/index.html.twig', [
            'buys' => $buyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_buy_new", methods={"GET", "POST"})
     */
    public function new(Request $request, BuyRepository $buyRepository): Response
    {
        $buy = new Buy();
        $form = $this->createForm(BuyType::class, $buy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $buyRepository->add($buy, true);

            return $this->redirectToRoute('app_buy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('buy/new.html.twig', [
            'buy' => $buy,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_buy_show", methods={"GET"})
     */
    public function show(Buy $buy): Response
    {
        return $this->render('buy/show.html.twig', [
            'buy' => $buy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_buy_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Buy $buy, BuyRepository $buyRepository): Response
    {
        $form = $this->createForm(BuyType::class, $buy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $buyRepository->add($buy, true);

            return $this->redirectToRoute('app_buy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('buy/edit.html.twig', [
            'buy' => $buy,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_buy_delete", methods={"POST"})
     */
    public function delete(Request $request, Buy $buy, BuyRepository $buyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$buy->getId(), $request->request->get('_token'))) {
            $buyRepository->remove($buy, true);
        }

        return $this->redirectToRoute('app_buy_index', [], Response::HTTP_SEE_OTHER);
    }
}
