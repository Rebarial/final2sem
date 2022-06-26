<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Buy;
use App\Entity\Profile;
use App\Repository\ProductRepository;
use App\Repository\BuyRepository;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    private Buy $Buy;
    /**
     * @Route("/shop", name="app_shop")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    /**
     * @Route("/shop_add", name="app_additem")
     */
    public function addindex(Request $request, ProductRepository $productRepository, BuyRepository $buyRepository): Response
    {
        $Buy = $buyRepository->findOneBy(array("user" => $this->getUser()));
        if (!$Buy){
            $Buy = new Buy();
            $Buy->setUser($this->getUser());
        }
        $prod = $productRepository->find($request->get('id'));
        $Buy->addProduct($prod);
        $buyRepository->add($Buy, true);
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    /**
     * @Route("/basket", name="app_basket")
     */
    public function basket(ProductRepository $productRepository, BuyRepository $buyRepository,
    ProfileRepository $profRep) : Response
    {

        $Buy = $buyRepository->findOneBy(array("user" => $this->getUser()));
        if ($Buy) {
            $k = 0;
            $pr = $Buy->getProduct();
            foreach ($pr as &$pro)
            {
                $k += $pro->getPrice();
            }
            return $this->render('shop/show.html.twig', [
                'buy' => $Buy,
                'products' => $Buy->getProduct(),
                'sumPrice' => $k,
                'balance' => $profRep->findOneBy(array("User" => $this->getUser()))->getMoney(),
            ]);
        }
        else {
            $Buy = new Buy();
            $Buy->setUser($this->getUser());
            $buyRepository->add($Buy, true);
            return $this->render('shop/show.html.twig', [
                'buy' => $Buy,
                'products' => $Buy->getProduct(),
                'sumPrice' => 0,
                'balance' => $profRep->findOneBy(array("User" => $this->getUser()))->getMoney(),
            ]);
        }
    }
    /**
    * @Route("/basket_del, name="app_delitem")
    */
    public function delitem(Request $request, ProductRepository $productRepository,
                            BuyRepository $buyRepository, ProfileRepository $profRep): Response
    {
        $Buy = $buyRepository->findOneBy(array("user" => $this->getUser()));
        if ($request->get('id')) {

            $prod = $productRepository->find($request->get('id'));
            $Buy->removeProduct($prod);
            $buyRepository->add($Buy, true);
            $k = 0;
            $pr = $Buy->getProduct();
            foreach ($pr as &$pro)
            {
                $k += $pro->getPrice();
            }
            return $this->render('shop/show.html.twig', [
                'buy' => $Buy,
                'products' => $Buy->getProduct(),
                'sumPrice' => $k,
                'balance' => $profRep->findOneBy(array("User" => $this->getUser()))->getMoney(),
            ]);
        }
        else {
            return $this->render('shop/show.html.twig', [
                'buy' => $Buy,
                'products' => $Buy->getProduct(),
                'sumPrice' => 0,
                'balance' => $profRep->findOneBy(array("User" => $this->getUser()))->getMoney(),
            ]);
        }
    }
}
