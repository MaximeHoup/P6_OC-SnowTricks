<?php

namespace App\Pagination;

use Symfony\Component\Routing\RouterInterface;

class PaginationHelper
{
    private $routerInterface;

    public function __construct(RouterInterface $router)
    {
        $this->routerInterface = $router;
    }
    /**
     * @param $page
     * @param $pages
     * @return array
     */
    public function getUrl($page, $pages)
    {
        $paginationLinks = array(
            'page' => $page,
            'pages' => $pages,
            'firstPage' => $this->routerInterface->generate('home', ['page' => '1']),
            'lastPage' => $this->routerInterface->generate('home', ['page' => $pages]),
            'nextPage' => $this->routerInterface->generate('home', ['page' => ($page + 1)]),
            'previousPage' => $this->routerInterface->generate('home', ['page' => ($page - 1)])
        );
        return $paginationLinks;
    }
    /**
     * @param $page
     * @param $pages
     * @param $trickSlug
     * @return array
     */
    public function getCommentUrl($page, $pages, $trickSlug)
    {
        $paginationLinks = array(
            'page' => $page,
            'pages' => $pages,
            'firstPage' => $this->routerInterface->generate('view_trick', ['slug' => $trickSlug, 'page' => '1']),
            'lastPage' => $this->routerInterface->generate('view_trick', ['slug' => $trickSlug, 'page' => $pages]),
            'nextPage' => $this->routerInterface->generate('view_trick', ['slug' => $trickSlug, 'page' => ($page + 1)]),
            'previousPage' => $this->routerInterface->generate('view_trick', ['slug' => $trickSlug, 'page' => ($page - 1)])
        );
        return $paginationLinks;
    }
}
