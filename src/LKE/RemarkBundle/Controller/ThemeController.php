<?php

namespace LKE\RemarkBundle\Controller;

use LKE\CoreBundle\Controller\CoreController;
use LKE\UserBundle\Service\Access;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\View;

class ThemeController extends CoreController
{
    /**
     * @View(serializerGroups={"Default"})
     */
    public function getThemesAction(Request $request)
    {
        list($limit, $page) = $this->get('lke_core.paginator')->getBorne($request, 20);

        $themes = $this->getRepository()->getThemes($limit, $page - 1);

        return $themes;
    }

    /**
     * @View(serializerGroups={"Default"})
     */
    public function getThemeAction($slug)
    {
        return $this->getEntity($slug, Access::READ);
    }

    final protected function getRepositoryName()
    {
        return "LKERemarkBundle:Theme";
    }
}
