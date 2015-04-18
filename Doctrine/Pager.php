<?php

namespace Maalls\Pager\Doctrine;

use Doctrine\ORM\Tools\Pagination\Paginator;


class Pager implements \IteratorAggregate {

    protected $query;
    protected $paginator;
    protected $page;
    protected $limit;
    protected $linkCount;
    protected $total;
    protected $pageCount;
    protected $url;
    protected $urlParameters;
    protected $pageUrlFieldName;

    public function __construct($query, $page = 1, $limit = 20, $linkCount = 5, $url = "/", $urlParameters = array(), $pageUrlFieldName = "page") {

        $this->setQuery($query);
        $this->page = $page;
        $this->limit = $limit;
        $this->linkCount = $linkCount;
        $this->urlParameters = $urlParameters;
        $this->pageUrlFieldName = $pageUrlFieldName;
        $this->init();

    }

    public function init() {

        if($this->query) {
        
            $this->paginator = new Paginator($this->query);
            $this->paginator->getQuery()
                ->setFirstResult($this->limit * ($this->page - 1))
                ->setMaxResults($this->limit);

            $this->total = count($this->paginator);
            $this->pageCount = ceil($this->total / $this->limit);

        }
        else {

            throw new \Exception("Query required.");

        }

    }

    public function setQuery($query) {

        $this->query = $query;

    }

    public function getLinks() {

        $middle = \round($this->linkCount / 2);

        $min = max(1, $this->page - $middle);
        $max = min($this->pageCount, $this->page + $middle);

        $links = array();

        for($i = $min; $i <= $max; $i++) {

            
            $links[] = array(
                "page" => $i, 
                "active" => $i == $this->page,
                "uri" => $this->uri($i));

        }

        return $links;

    }

    public function uri($page) {

        $params = $this->urlParameters;
        $params[$this->pageUrlFieldName] = $page; 

        return $this->url . "?" . http_build_query($params);

    }

    public function urlQuery() {

        return $this->urlParameters;

    }



    public function getIterator() {

        return $this->paginator->getIterator();

    }

    public function pageCount() {

        return $this->pageCount;

    }

    public function getPage() {

        return $this->page;

    }

    public function first() {

        return $this->limit * ($this->page - 1) + 1;

    }

    public function last() {

        return $this->limit * ($this->page - 1) + count($this->getIterator());

    }

    public function total() {

        return $this->total;

    }

}