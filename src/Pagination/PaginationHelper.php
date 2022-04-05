<?php

namespace App\Pagination;

use Symfony\Component\Routing\RouterInterface;

class PaginationHelper
{
    private $routerInterface;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
            'firstPage' => $this->router->generate('home', ['page' => '1']),
            'lastPage' => $this->router->generate('home', ['page' => $pages]),
            'nextPage' => $this->router->generate('home', ['page' => ($page + 1)]),
            'previousPage' => $this->router->generate('home', ['page' => ($page - 1)])
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
            'firstPage' => $this->router->generate('view_trick', ['slug' => $trickSlug, 'page' => '1']),
            'lastPage' => $this->router->generate('view_trick', ['slug' => $trickSlug, 'page' => $pages]),
            'nextPage' => $this->router->generate('view_trick', ['slug' => $trickSlug, 'page' => ($page + 1)]),
            'previousPage' => $this->router->generate('view_trick', ['slug' => $trickSlug, 'page' => ($page - 1)])
        );
        return $paginationLinks;
    }
}
