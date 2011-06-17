<?php

namespace Application\DefaultBundle\Controller;

use Application\PortfolioBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * Categories/projects lilowerst
     *
     * @return array()
     * @Cache(expires="tomorrow")
     * @Route("/{_locale}", name="homepage", defaults={"_locale"="ru"}, requirements={"_locale"="ru|en"})
     * @Template()
     */
    public function indexAction()
    {
        $categories = $this->get('doctrine')->getEntityManager()
                ->getRepository("PortfolioBundle:Category")->getAllCategories();

        \Zend\Feed\Reader\Reader::setCache($this->get('zend.cache_manager')->getCache('slow_cache'));
        $feed = \Zend\Feed\Reader\Reader::import('http://www.google.com/reader/public/atom/user%2F14849984795491019190%2Fstate%2Fcom.google%2Fbroadcast');
        
        return array('categories' => $categories, 'feed' => $feed);
    }

    /**
     * Show last twitts
     *
     * @return array()
     * @Template()
     */
    public function twitterAction($count = 1)
    {
        $statuses[] = (object) array(
            'text' => (string) 'Unable to Connect to tcp://api.twitter.com:80',
            'time' => (string) \time()
        );

        $cache = $this->get('zend.cache_manager')->getCache('slow_cache');
        if (false === ($statuses = $cache->load('dc_twitter_' . $count))) {
            try {
                $twitter = new \Zend_Service_Twitter();

                // @todo: add try/catch
                $result = $twitter->statusUserTimeline(array('id' => 'stfalcon', 'count' => $count));
                $statuses = array();
                foreach ($result->status as $status) {
                    $statuses[] = (object) array(
                        'text' => (string) $status->text,
                        'time' => (string) $status->created_at
                    );
                }
                $cache->save($statuses, 'dc_twitter_' . $count);
            } catch (\Zend_Http_Client_Adapter_Exception $e) {
                $statuses = array();
                $statuses[] = (object) array(
                    'text' => (string) 'Unable to Connect to tcp://api.twitter.com:80',
                    'time' => (string) \time()
                );
            }
        }

        return array('statuses' => $statuses);
    }

    /**
     * Contacts page
     *
     * @return array()
     * @Template()
     * @Route("/{_locale}/contacts", name="contacts", defaults={"_locale"="ru"}, requirements={"_locale"="ru|en"})
     */
    public function contactsAction()
    {
        // @todo: refact
        $breadcrumbs = $this->get('menu.breadcrumbs');
        $breadcrumbs->addChild('Контакты')->setIsCurrent(true);

        return array();
    }

}