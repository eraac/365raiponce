<?php

namespace LKE\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;

class RemarkController extends Controller
{
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getRemarksUnpublishedAction(Request $request)
    {
        list($limit, $page) = $this->get('lke_core.paginator')->getBorne($request, 20);

        $remarks = $this->getRepository()->getUnpublishedRemark($limit, $page - 1);

        return $remarks;
    }

    /**
     * @View(serializerGroups={"Default", "detail-remark"})
     * @Post("/remarks/{id}/publish")
     */
    public function postRemarkPublishAction($id)
    {
        $remark = $this->get('lke_remark.get_remark')->getRemark($id);

        $remark->setPostedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($remark);
        $em->flush();

        return $remark;
    }

    private function getRepository()
    {
        return $this->getDoctrine()->getRepository('LKERemarkBundle:Remark');
    }
}
