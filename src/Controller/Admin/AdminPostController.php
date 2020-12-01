<?php


namespace App\Controller\Admin;


use App\Entity\Creator;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CreatorRepository;
use App\Repository\CreatorRepositoryInterface;
use App\Repository\PostRepository;
use App\Repository\PostRepositoryInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostController extends AdminBaseController
{
    private $creatorRepository;
    private $postRepository;

    public function __construct(CreatorRepositoryInterface $creatorRepository, PostRepositoryInterface $postRepository)
    {
        $this->creatorRepository = $creatorRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("/admin/post", name="admin_post")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(PostRepositoryInterface $post, EntityManagerInterface $em)
    {
        $post = $em->getRepository(Post::class)->findAll();
        $checkCreator = $em->getRepository(Creator::class)->findAll();
        $forRender = parent::renderDefault();
        $forRender['check_creator'] = $checkCreator;

        return $this->render('admin/post/index.html.twig', [
            'title' => 'Wszystkie publikacje',
            'post' => $post,
            'check_creator' => $checkCreator
        ]);
    }

    /**
     * @Route("/admin/post/create", name="admin_post_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->postRepository->setCreatePost($post);
            $this->addFlash('success', 'Dodano'); //dodaje message
            return $this->redirectToRoute('admin_post');
        }
        $forRender = parent::renderDefault();
        $forRender['title'] = 'Tworzenie wpisu';
        $forRender['form'] = $form->createView();
        return $this->render('admin/post/form.html.twig', $forRender);
    }

    /**
     * @Route("/admin/post/update/{id}", name="admin_post_update")
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function update($id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        /**
         * @var $post Post
         */
        $post = $em->getRepository('App:Post')->findOneBy(['id' => $id]);

        //original Creators
        $originalCreators = new ArrayCollection();

        foreach ($post->getCreator() as $creator)
        {
            $originalCreators->add($creator);
        }

        $form  = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            //get rid of the ones that the user got rid of in the interface (DOM)
            foreach ($originalCreators as $creator)
            {
                //chek if the creator is in the post->getCreator()
                if ($post->getCreator()->contains($creator) == false)
                {
                    $em->remove($creator);
                }
            }
            if($form->get('delete')->isClicked())
            {
                $this->postRepository->setDeletePost($post);
                $this->addFlash('success', 'Publikacja zostala usunieta');
            }

            $em->persist($post);
            $em->flush();
        }

        return $this->render('admin/post/form.html.twig',[
            'form' => $form->createView()
        ]);
    }
}