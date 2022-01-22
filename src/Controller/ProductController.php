<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
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



        $search = new Search();
        $searchForm = $this->createForm(SearchType::class, $search);

        //Traitement du formulaire de la recherche
        $searchForm->handleRequest($request);
        if($searchForm->isSubmitted() && $searchForm->isValid() ){
            $products = $this->repository->findWithSearch($search);

        }else{
            $products = $this->repository->findAll();
        }
        return $this->render('product/index.html.twig',[
            'products' => $products,
            'search_form' => $searchForm->createView(),
        ]);
    }

    #[Route("produit/{slug}", name:"product")]
    public function show($slug){

        $product = $this->repository->findOneBySlug($slug);
        $products = $this->entityManager->getRepository(Product::class)->findBy(['isBest'=>true]);
        if(!$product){
            $this->addFlash('message', "Oups...il semblerait que ce produit n'existe pas");
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig',[
            'product' => $product,
            'products' => $products,
        ]);
    }
}
