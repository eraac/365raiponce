<?php

namespace LKE\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use LKE\CoreBundle\Controller\CoreController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;

class ResponseController extends CoreController
{
    /**
     * @View(serializerGroups={"Default", "admin-response"})
     */
    public function getResponsesUnpublishedAction(Request $request)
    {
        list($limit, $page) = $this->get('lke_core.paginator')->getBorne($request, 20);

        $responses = $this->getRepository()->getUnpublishedResponses($limit, $page - 1);

        return $responses;
    }

    /**
     * @View(serializerGroups={"Default", "admin-response"})
     * @Post("/responses/{id}/publish")
     */
    public function postResponsePublishAction($id)
    {
        $response = $this->getEntity($id, Access::EDIT);

        $response->setPostedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($response);
        $em->flush();

        return $response;
    }

    final protected function getRepositoryName()
    {
        return 'LKERemarkBundle:Response';
    }
}
