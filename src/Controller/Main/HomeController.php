<?php


namespace App\Controller\Main;

use App\Entity\Post;
use App\Repository\CreatorRepository;
use App\Repository\CreatorRepositoryInterface;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\Expr\Join;

class HomeController extends BaseController
{
    private $creatorRepository;

    public function __construct(CreatorRepositoryInterface $creatorRepository)
    {
        $this->creatorRepository = $creatorRepository;
    }


    /**
     * @Route("/", name="home")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        $dql = "SELECT a FROM App:Post a";
        $query = $em->createQuery($dql);

        $post = $em->getRepository(Post::class)->findAll();
        $creator = $this->creatorRepository->getAllCreator();
        $forRender = parent::renderDefault();
        $forRender['post'] = $post;
        $forRender['creator'] = $this->creatorRepository->getAllCreator();
        return $this->render('main/index.html.twig', [
            'title' => 'Bibliometr',
            'post' => $post,
            'creator' => $creator,
            'pagination' => $paginator->paginate($query, $request->query->getInt('page', 1), 3)
        ]);
    }

    /**
     * @Route("/export", name="export", methods={"POST"})
     * @param Request $request
     * @param PostRepository $postRepository
     * @param EntityManagerInterface $em
     * @param CreatorRepository $creatorRepository
     * @return Response
     * @throws Exception
     */
    public function export(Request $request, PostRepository $postRepository, EntityManagerInterface $em, CreatorRepository $creatorRepository)
    {
        $columns = ' ';
        $all = true;



        if (!empty($request->get('title'))) {
            $columns = 'a.title,';
        }
        if (!empty($request->get('year'))) {
            $columns = $columns.'a.year,';
        }
           if (!empty($request->get('numOfPoints'))) {
            $columns = $columns.' a.numOfPoints,';
        }
        if (!empty($request->get('Konferencja'))) {
            $columns = $columns.' a.conference,';
        }
        //Creators
        if (!empty($request->get('user'))) {
            $columns = $columns.' c.user,';
        }
        if (!empty($request->get('participation'))) {
            $columns = $columns.' c.participation,';
        }
        if (empty($columns)) {
            $columns = 'a';
        } else {
            $all = false;
            $columns = rtrim($columns, ',');
        }
        if (empty($columns)) {
            $columns = 'c';
        } else {
            $all = false;
            $columns = rtrim($columns, ',');
        }

        $ids = '';
        for ($i = 1; $i <= 10; $i++) {
            if (null !== $request->get("row_" . $i)) {
                if ($i > 8) $ids = $i;
                $ids = ' ' . $ids . '' . $i . ',';
            }
        }
        if (empty($ids)) {
            $rows = '';
        } else {
            $rows = rtrim($ids, ',');
        }
        $publishes = $postRepository->createQueryBuilder('a')->select($columns);
//        $creators = $creatorRepository->createQueryBuilder('c')->select($columns);

        $publishes = $publishes->getQuery()->getResult();
//        $creators = $creators->getQuery()->getResult();

        $pubs = [];
        $crears = [];

        foreach ($publishes as $publish) {
            $isEmpty = true;
            foreach ($publish as $field) {
                if ($field instanceof \DateTime) {
                    $publish['year'] = $field->format('Y-m-d');
                }
                if ($field) $isEmpty = false;
            }
            if (!$isEmpty) array_push($pubs, $publish);
        }
//
//        foreach ($creators as $creator) {
//            $isEmpty = true;
//            foreach ($creators as $field) {
//                if ($field instanceof \DateTime) {
//                    $creators['year'] = $field->format('Y-m-d');
//                }
//                if ($field) $isEmpty = false;
//            }
//            if (!$isEmpty) array_push($crears, $creator);
//        }

        if ($columns !== 'a') {
            $columns = str_replace('a.', '', $columns);
            $columns = explode(',', $columns);
        }

//        if ($columns !== 'c') {
//            $columns = str_replace('c.', '', $columns);
//            $columns = explode(',', $columns);
//        }

        if($columns) {

            // Instantiate Dompdf with our options
            $phpWord = new phpWord();

            $twig = $this->get('twig');
            /** @var \Twig_Template $template */
            $template = $twig->load('admin/post/preview.html.twig');

            // Retrieve the HTML generated in our twig file
            $html = $template->renderBlock('body',[
                'publishes' => $pubs,
//                'creators' => $crears,
                'columns' => $columns,
            ]);

            $section = $phpWord->addSection();

            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);
            // Saving the document as OOXML file...

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            header("Content-Disposition: attachment; filename=export.docx");
            $objWriter->save("php://output");


            return $this->render('admin/post/preview.html.twig', [
                'publishes' => $pubs,
//                'creators' => $crears,
                'columns' => $columns,
            ]);
        }
    }
}