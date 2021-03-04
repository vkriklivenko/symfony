<?php


namespace App\Controller\Main;

use App\Entity\Creator;
use App\Entity\Post;
use App\Form\SearchForm;
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

        $creator = $em->getRepository(Creator::class)->findAll();
        $forRender = parent::renderDefault();
        $forRender['creator'] = $creator;

        return $this->render('main/index.html.twig', [
            'title' => 'Bibliometr',
            'pagination' => $paginator->paginate($query, $request->query->getInt('page', 1), 3),
            'creator' => $creator
        ]);
    }

    /**
     * @Route("/export", name="export", methods={"POST"})
     * @param Request $request
     * @param PostRepository $postRepository
     * @param CreatorRepository $creatorRepository
     * @return Response
     * @throws Exception
     */
    public function export(Request $request, PostRepository $postRepository, CreatorRepository $creatorRepository)
    {
        $columns = [];
        $all = true;
        $em = $this->getDoctrine()->getManager();

        if (!empty($request->get('title'))) {
            $columns[] = 'a.title AS Tytul';
        }
        if (!empty($request->get('year'))) {
            $columns[] = 'a.year AS Rok';
        }
        if (!empty($request->get('numOfPoints'))) {
            $columns[] = 'a.numOfPoints AS Liczba_punktow';
        }
        if (!empty($request->get('conference'))) {
            $columns[] = 'a.conference AS Konferencja ';
        }

        //Creators
        if (!empty($request->get('creator'))) {
            $columns[] ='c.user AS Kto_stworzyl';
        }
        if (!empty($request->get('participation'))) {
            $columns[] = 'c.participation AS Udzial';
        }
        if (!count($columns)) {
            $columns = [
                'a.title AS Tytul',
                'a.year AS Rok',
                'a.numOfPoints AS Liczba_punktow',
                'a.conference AS Konferencja',
                'c.user AS Kto_stworzyl',
                'c.participation AS Udzial',
            ];
        }

        array_unshift($columns, 'a.id');

        $ids = [];
        for ($i = 1; $i <= 10; $i++) {
            if (null !== $request->get("row_" . $i)) {
                $ids[] = $i;
            }
        }

        $columns = implode(',', $columns);
        $rows = implode(',', $ids);
        $filtersString = $request->request->get('filters', '{}');
        $publishes = $postRepository->findAllToExport($columns, $rows, json_decode($filtersString, true));
        $pubs = [];
        $creators = [];
        foreach ($publishes as $index => $publish) {
            if (isset($publish['Kto_stworzyl'])) {
                $creators[$publish['id']]['creators'][] = $publish['Kto_stworzyl'];
            }

            if (isset($publish['Udzial'])) {
                $creators[$publish['id']]['part'][] = $publish['Udzial'];
            }
        }
//        dump($creators); die();

        $crears = $publishes;

        $columnsToRender = array_keys(array_shift($crears));
        foreach ($publishes as $publish) {
            $isEmpty = true;
            if ($publish) {
                foreach ($publish as $index => $field) {
                    if (in_array($index, ['Udzial', 'Kto_stworzyl'])) {
                        unset($publish[$index]);
//                        if(isset($creators[$publish[$index]])) {
//                            $publish['data'] = $creators[$publish[$index]];
//                        }
                    }
                    if ($field instanceof \DateTime) {
                        $publish['Rok'] = $field->format('Y-m-d');
                    }
                    if ($field) $isEmpty = false;
                }
                if (!$isEmpty) {
                    $pubs[$publish['id']] = [];
                    array_push($pubs[$publish['id']], $publish);
                }
            }
        }

        if ($columns !== 'a') {
            $columns = str_replace('a.', '', $columns);
            $columns = explode(',', $columns);
        }
//
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
                'creators' => $creators,
                'columns' => $columnsToRender,
            ]);

            $section = $phpWord->addSection();


            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);
            // Saving the document as OOXML file...

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            header("Content-Disposition: attachment; filename=export.docx");
            $objWriter->save("php://output");


            return $this->render('admin/post/preview.html.twig', [
                'publishes' => $pubs,
                'creators' => $creators,
                'columns' => $columnsToRender,
            ]);
        }
    }

    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function searchAction(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em)
    {
        /** @var PostRepository $postRepo */
        $postRepo = $this->getDoctrine()->getRepository('App:Post');
        $searchForm = $this->createForm(SearchForm::class);
        $searchForm->handleRequest($request);
        $filters = $this->getRequestData($request);
        if (count($filters) === 0) {
            $filters = $request->query->all();
        }
        $posts = $postRepo->findAllPosts($filters);


        $creator = $em->getRepository(Creator::class)->findAll();
        return $this->render('main/index.html.twig', [
            'title'      => 'Bibliometr',
            'pagination' => $paginator->paginate($posts, $request->query->getInt('page', 1), 2),
            'creator'    => $creator,
            'filters'    => $filters
        ]);

//        return $this->render('main/search.html.twig',[
//            'title' => 'Bibliometr',
//            'posts' => $posts,
//            'form' => $searchForm->createView(),
//        ]);

    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getRequestData(Request $request) {
        return $request->request->all();
    }
}