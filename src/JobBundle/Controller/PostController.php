<?php
/**
 * Created by PhpStorm.
 * User: LAPTOP
 * Date: 12/3/2018
 * Time: 7:17 PM
 */

namespace JobBundle\Controller;


use JobBundle\Form\Type\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use JobBundle\Entity\Post;

class PostController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/posts")
     */
    public function getPostsAction(Request $request)
    {
        $posts = $this->get('doctrine.orm.entity_manager')
            ->getRepository('JobBundle:Post')
            ->findAll();

        return $posts;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/posts/{id}")
     */
    public function getPostAction(Request $request)
    {
        $post = $this->get('doctrine.orm.entity_manager')
            ->getRepository('JobBundle:Post')
            ->find($request->get('id'));

        if (empty($post)) {
            return $this->postNotFound();
        }

        return $post;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/posts/{keyWords}/{city}")
     */
    public function findPostsAction(Request $request)
    {
        $keyWords = preg_split('/\s+/', $request->get('keyWords'));
        $city = $request->get('city');

        $sql = array('0'); // Stop errors when $words is empty

        foreach ($keyWords as $keyWord) {
            $sql[] = 'title LIKE "%'.$keyWord.'%" OR description LIKE "%'.$keyWord.'%" OR companyName LIKE "%'.$keyWord.'%"';
        }

        $sql = 'SELECT * FROM posts WHERE (city LIKE "%'.$city.'%") AND ('.implode(" OR ", $sql).')';

        $em = $this->getDoctrine()->getManager();

        $statement = $em->getConnection()->prepare($sql);
        $statement->execute();

        $posts = $statement->fetchAll();

        return $posts;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/posts")
     */
    public function postPostsAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->submit($request->request->all()); // data validation

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($post);
            $em->flush();
            return $post;

        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/posts/{id}")
     */
    public function putPostAction(Request $request)
    {
        $this->updatePost($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/posts/{id}")
     */
    public function patchPostAction(Request $request)
    {
        $this->updatePost($request, false);
    }

    // $clear missing is either true or false
    private function updatePost(Request $request, $clearMissing)
    {
        $post = $this->get('doctrine.orm.entity_manager')
            ->getRepository('JobBundle:Post')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire

        if (empty($post)) {
            return $this->postNotFound();
        }

        $form = $this->createForm(PostType::class, $post);

        // Le paramètre false dit à Symfony de garder les valeurs dans notre
        // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($post);
            $em->flush();
            return $post;

        } else {
            return $form;
        }
    }

    private function postNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
    }
}