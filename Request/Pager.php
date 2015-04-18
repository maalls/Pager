<?php

namespace Maalls\Pager\Request;

use Maalls\Pager\Doctrine\Pager as DoctrinePager;


class Pager extends DoctrinePager {


    public function __construct(
        $request, 
        $linkCount = 5, 
        $fields = array("page" => 1, "limit" => 20, "search" => ""),
        $queryBuilder,
        $searchAttributes = array()

    ) 
    {

        list($pageField, $limitField, $searchField) = array_keys($fields);

        $query = $request->query->all();
        $query[$limitField] = $request->query->get($limitField, $fields[$limitField]);
        $query[$pageField] = $request->query->get($pageField, $fields[$pageField]);
        $query[$searchField] = $request->query->get($searchField, $fields[$searchField]);

        if($query[$searchField] && $searchAttributes) {

            $searchQueries = array();
            foreach($searchAttributes as $attribute) {

                $searchQueries[] = "e." . $attribute . " LIKE :search";

            }
            
            $queryBuilder->andWhere(implode(" OR ", $searchQueries))->setParameter('search', "%" . $query["search"] . "%");

        }

        $q = $queryBuilder->getQuery();

        return parent::__construct($q, $query[$pageField], $query[$limitField], $linkCount, $request->getBaseUrl(), $query);

    }


}