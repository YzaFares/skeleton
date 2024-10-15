<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $recipes = $repository->findWithDurationLowerThan(20);
        $totalDuration = $repository->findTotalDuration();

        /*On peut remplacer l'objet RecipeRepository par l'objet EntityManagerInterface*/
        /* $recipes = $entityManager->getRepository(Recipe::class)->findWithDurationLowerThan(20);*/

        /*Créer un nouvel élément en bdd*/
        /*$recipe = new Recipe();
        $recipe->setTitle('Crépes au nutella')
            ->setSlug("crepe-nutella")
            ->setContent("Meter du nutella")
            ->setDuration(8)
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdateAt(new DateTimeImmutable());
        $entityManager->persist($recipe);
        $entityManager->flush();*/
        /* UpDate*/
        /*$recipes[5]->setTitle("Fish & ships")->setSlug("fish")->setContent("Meter du poisson et des frittes");
        $entityManager->flush();*/
        /*$recipes = $repository->findAll();*/
        /*Delete*/
        /*$entityManager->remove($recipes[4]);
        $entityManager->flush();*/
        return $this->render('recipe/index.html.twig', ['recipes' => $recipes, 'totalDuration' => $totalDuration]);
        /*return new Response('Recettes');*/
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'person' => [
                'firstname' => 'John',
                'lastname' => 'Do'
            ]
        ]);
        /*return $this->json([
            'slug' => $slug,
            'id' => $id
        ]);*/
        /*return new JsonResponse([
            'slug' => $slug,
            'id' => $id
        ]);*/
        /*return new Response('Recette: ' . $slug);*/
        /*dd($slug, $id);
        dd($request->attributes->get('slug'), $request->attributes->getInt('id'));*/
    }

    #[Route('/recette/{id}/edit', name: 'recipe.edit', requirements: ['id' => '\d+'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() & $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','la recette a bien été modifié');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'recipeform' => $form
        ]);
    }
}
