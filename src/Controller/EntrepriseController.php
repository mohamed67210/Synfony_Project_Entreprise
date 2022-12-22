<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'app_entreprise')]
    public function index(ManagerRegistry $doctrine): Response
    {
        // recuperer toutes les entreprises de la base de données
        $entreprises = $doctrine->getRepository(Entreprise::class)->findBy([],['dateCreation' => 'DESC']);
        $tableau = ['valeur1', 'valeur2', 'valeur3'];
        return $this->render('entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    #[Route('/entreprise/add', name: 'add_entreprise')]
    public function add(ManagerRegistry $doctrine, Entreprise $entreprise = null, Request $request): Response
    {
        // construire un formulaire qui va se baser sur le $bulder dans entrepriseType
        $form = $this->createForm(EntrepriseType::class, $entreprise);
        $form->handleRequest($request);

        // isValid () remplace les filter input 
        if ($form->isSubmitted() && $form->isValid()) {

            // recuperer les données inserer dans le formilaire et les injecter dans l'objet entreprise grace au seter
            $entreprise = $form->getData();

            // on recupere le managere doctrine
            $entityManager = $doctrine->getManager();

            // persist remplace prepare en pdo , on prepare l'objet entreprise 
            $entityManager->persist($entreprise);

            // execute,inserer les données dans la bdd
            $entityManager->flush();

            // retourner a la page qui affiche toutes les entreprises

            return $this->redirectToRoute('app_entreprise');
        }

        // vue pour afficher le formulare d'ajout
        return $this->render('entreprise/add.html.twig', [
            'formAddEntreprise' => $form->createView(),
        ]);
    }

    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    public function show(Entreprise $entreprise): Response
    {
        return $this->render('entreprise/show.html.twig', [
            'entreprise' => $entreprise,
        ]);
    }
}
