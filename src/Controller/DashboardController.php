<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $searchTerms = $this->parseSearchTermsFromRequest($request);
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'search_terms'    => $searchTerms,
            'search_string'   => $this->renderSearchString($searchTerms),
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function parseSearchTermsFromRequest(Request $request)
    {
        return [
            'alias'    => $request->query->get('alias'),
            'token'    => $request->query->get('token'),
            'category' => $request->query->get('category'),
        ];
    }

    /**
     * @param $searchTerms[]
     * @return string
     */
    private function renderSearchString($searchTerms)
    {
        $searchString = '';

        if (!empty($searchTerms['alias'])) {
            $searchString .= ('' !== $searchString) ? '&' : '';
            $searchString .= 'alias=' . $searchTerms['alias'];
        }
        if (!empty($searchTerms['category'])) {
            $searchString .= ('' !== $searchString) ? '&' : '';
            $searchString .= 'category=' . $searchTerms['category'];
        }
        if (!empty($searchTerms['token'])) {
            $searchString .= ('' !== $searchString) ? '&' : '';
            $searchString .= '&token=' . $searchTerms['token'];
        }

        if ('' !== $searchString) {
            $searchString = '?' . $searchString;
        }

        return $searchString;
    }
}
