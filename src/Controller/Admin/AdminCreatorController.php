<?php


namespace App\Controller\Admin;

use App\Entity\Creator;
use App\Form\CreatorType;
use App\Repository\CreatorRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class AdminCreatorController extends AdminBaseController
{
    private $creatorRepository;

    public function __construct(CreatorRepositoryInterface $creatorRepository)
    {
        $this->creatorRepository = $creatorRepository;
    }

    /**
     * @Route("/admin/creator", name="admin_creator")
     */
    public function index()
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Wszystkie publikacje';
        $forRender['creator'] = $this->creatorRepository->getAllCreator();
        return $this->render('admin/creator/index.html.twig', $forRender);
    }

    /**
     * @Route("/admin/creator/create", name="admin_creator_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $creator = new Creator();
        $form = $this->createForm(CreatorType::class, $creator);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->creatorRepository->setCreateCreator($creator);
            $this->addFlash('success', 'Dodano');
            return $this->redirectToRoute('admin_creator');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Tworzenie wpisu';
        $forRender['form'] = $form->createView();
        return $this->render('admin/creator/form.html.twig', $forRender);

    }

    /**
     * @Route("/admin/creator/update/{id}", name="admin_creator_update")
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update(int $id, Request $request)
    {
        $creator = $this->creatorRepository->getOneCreator($id);
        $form = $this->createForm(CreatorType::class, $creator);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($form->get('save')->isClicked())
            {
                $this->creatorRepository->setUpdateCreator($creator);
                $this->addFlash('success', 'Wspolautor zostal zmodyfikowany');
            }
            if($form->get('delete')->isClicked())
            {
                $this->creatorRepository->setDeleteCreator($creator);
                $this->addFlash('success', 'Wspolautor zostal usuniety');
            }
            return $this->redirectToRoute('admin_creator');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Modyfikacja wspolautora';
        $forRender['form'] = $form->createView();
        return $this->render('admin/creator/form.html.twig', $forRender);

    }

}