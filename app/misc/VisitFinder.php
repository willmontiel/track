<?php

namespace Sigmamovil\Misc;

class VisitFinder
{
    protected $logger;
    protected $paginator;
    protected $account;
    protected $filter;
    protected $user_filter = "";
    protected $visit_filter = "";
    protected $client_filter = "";
    protected $date_filter = "";
    protected $rows = array();

    public function __construct()
    {
        $this->logger = \Phalcon\DI::getDefault()->get('logger');
    }
    
    public function setAccount(\Account $account)
    {
        $this->account = $account;
    }
    
    public function setPaginator(\Sigmamovil\Misc\PaginationDecorator $paginator)
    {
        $this->paginator = $paginator;
    }
    
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
    
    public function load()
    {
        $this->createFilters();
        $this->countTotalRows();
        $this->modelRows($this->selectRows());
    }
    
    private function createFilters()
    {
        if ($this->filter->user != 0 && !empty($this->filter->user)) {
            $this->user_filter = " AND u.idUser = {$this->filter->user} ";
        }
        
        if ($this->filter->visitType != 0 && !empty($this->filter->visitType)) {
            $this->visit_filter = " AND vt.idVisittype = {$this->filter->visitType} ";
        }
        
        if ($this->filter->client != 0 && !empty($this->filter->client)) {
            $this->client_filter = " AND c.idClient = {$this->filter->client} ";
        }
        
        if ($this->filter->date != 0&& !empty($this->filter->date)) {
            $this->date_filter = " AND v.date = {$this->filter->date} ";
        }
    }
    
    
    private function countTotalRows()
    {
        $sql_count = "SELECT COUNT(v.idVisit) AS total "
                    . "FROM Visit AS v "
                    . " JOIN User AS u ON (u.idUser = v.idUser) "
                    . " JOIN Visittype AS vt ON (vt.idVisittype = v.idVisittype) "
                    . " JOIN Client AS c ON (c.idClient = v.idClient) "
                    . " WHERE u.idAccount = {$this->account->idAccount} "
                    . " {$this->user_filter} {$this->client_filter} {$this->visit_filter} {$this->date_filter} ";
                    
        $this->logger->log($sql_count);
                    
        $modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');      
        $r = $modelsManager->executeQuery($sql_count);
        $result = $r->getFirst()->total;
        
        $this->paginator->setTotalRecords($result);
    }
    
    private function selectRows()
    {
        $sql_rows = "SELECT v.date AS date, u.name AS name, u.lastName AS lastname, vt.name AS visit, c.name AS client, v.battery AS battery, v.latitude AS latitude, v.longitude AS longitude, v.location AS location "
                    . "FROM Visit AS v "
                    . " JOIN User AS u ON (u.idUser = v.idUser) "
                    . " JOIN Visittype AS vt ON (vt.idVisittype = v.idVisittype) "
                    . " JOIN Client AS c ON (c.idClient = v.idClient) "
                    . " WHERE u.idAccount = {$this->account->idAccount} "
                    . " {$this->user_filter} {$this->client_filter} {$this->visit_filter} {$this->date_filter} "
                    . " LIMIT {$this->paginator->getRowsPerPage()} OFFSET {$this->paginator->getStartIndex()} ";
                    
        $this->logger->log($sql_rows);
                    
        $modelsManager = \Phalcon\DI::getDefault()->get('modelsManager');      
        $rows = $modelsManager->executeQuery($sql_rows);
        return $rows;
    }
    
    private function modelRows($rows)
    {
        $crows = count($rows); 
        $this->paginator->setRowsInCurrentPage($crows);
        if ($crows > 0) {
            foreach ($rows as $row) {
                $array = array();
                $array['date'] = date('d/M/Y', $row->date);
                $array['name'] = "{$row->name} {$row->lastname}";
                $array['visit'] = $row->visit;
                $array['client'] = $row->client;
                $array['battery'] = $row->battery;
                $array['latitude'] = $row->latitude;
                $array['longitude'] = $row->longitude;
                $array['location'] = $row->location;
                
                $this->rows[] = $array;
            }
        }
    }
    
    public function getRows()
    {
        $pager = $this->paginator->getPaginationObject();
        $pager['user'] = $this->filter->user;
        $pager['visit'] = $this->filter->visitType;
        $pager['client'] = $this->filter->client;
        $pager['date'] = $this->filter->date;
        
        $response = array(
            'pagination' => $pager,
            'data' => $this->rows,
        );
        
        return $response;
    }
}