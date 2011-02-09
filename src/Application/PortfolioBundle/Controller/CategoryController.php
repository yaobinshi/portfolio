<?php

namespace Application\PortfolioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Application\PortfolioBundle\Entity\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{

    /**
     * Categories list
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery('SELECT c FROM PortfolioBundle:Category c');
        $categories = $query->getArrayResult();

        return $this->render('PortfolioBundle:Category:index.html.php', array(
            'categories' => $categories
        ));
    }

    /**
     * Create new category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $category = new Category();

        $form = new \Application\PortfolioBundle\Form\Category('category',
                        array(
                            'data' => $category,
                            'validator' => $this->get('validator'),
                        ));

        $form->bind($this->get('request'));

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($category);
            $em->flush();

            $this->get('request')->getSession()->setFlash('notice',
                    'Congratulations, your category is successfully created!');
            return $this->redirect($this->generateUrl('portfolioCategoryIndex'));
        }

        return $this->render('PortfolioBundle:Category:create.html.php', array(
            'form' => $form
        ));
    }

    /**
     * Edit category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        // try find category by id
        $category = $em->find("PortfolioBundle:Category", $id);
        if (!$category) {
            throw new NotFoundHttpException('The category does not exist.');
        }

        $form = new \Application\PortfolioBundle\Form\Category('category',
                        array(
                            'data' => $category,
                            'validator' => $this->get('validator'),
                        ));

        $form->bind($this->get('request'));
        if ($form->isValid()) {
            // save category
            $em->persist($category);
            $em->flush();

            $this->get('request')->getSession()->setFlash('notice', 'Congratulations, your category is successfully updated!');
            return $this->redirect($this->generateUrl('portfolioCategoryIndex'));
        }

        return $this->render('PortfolioBundle:Category:edit.html.php', array(
            'form' => $form,
            'category' => $category
        ));
    }

    public function viewAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        // try find category by id
        $category = $em->find("PortfolioBundle:Category", $id);
        if (!$category) {
            throw new NotFoundHttpException('The category does not exist.');
        }

        return $this->render('PortfolioBundle:Category:view.html.php', array(
            'category' => $category
        ));
    }

    /**
     * Delete category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        // try find category by id
        $category = $em->find("PortfolioBundle:Category", $id);
        if (!$category) {
            throw new NotFoundHttpException('The category does not exist.');
        }

        $em->remove($category);
        $em->flush();

        $this->get('request')->getSession()->setFlash('notice', 'Your category is successfully delete.');
        return $this->redirect($this->generateUrl('portfolioCategoryIndex'));
    }
}