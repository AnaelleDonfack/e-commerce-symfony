<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager ;
    private $repository;
    public function __construct( EntityManagerInterface $entityManager, ProductRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    #[Route('/nos-produits', name: 'products')]
    public function index(Request $request): Response
    {

        $products = $this->repository->findAll();

        return $this->render('product/index.html.twig',[
            'products' => $products,
        ]);
    }

    #[Route("produit/{slug}", name:"product")]
    public function show($slug){

        $product = $this->repository->findOneBySlug($slug);

        if(!$product){
            $this->addFlash('message', "Oups...il semblerait que ce produit n'existe pas");
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig',[
            'product' => $product,
        ]);
    }
}
