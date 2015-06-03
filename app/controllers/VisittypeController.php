<?php

class VisittypeController extends ControllerBase
{ 
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', null, 1);
        $builder = $this->modelsManager->createBuilder()
            ->from('Visittype')
//            ->where("idAccount = {$this->user->idAccount}")
            ->orderBy('Visittype.created');

        $paginator = new Phalcon\Paginator\Adapter\QueryBuilder(array(
            "builder" => $builder,
            "limit"=> 15,
            "page" => $currentPage
        ));
        
        $page = $paginator->getPaginate();
        $this->view->setVar("page", $page);
    }
    
    public function addAction()
    {
        $vtype = new Visittype();
        $form = new VisittypeForm($vtype);
        $this->view->setVar('form', $form);
        
        if ($this->request->isPost()) {
            try {
                $form->bind($this->request->getPost(), $vtype);
                $vtype->created = time();
                $vtype->updated = time();
    //            $client->idAccount = $this->user->idAccount;
                $vtype->idAccount = 1;
                
                if (!$vtype->save()) {
                    foreach ($vtype->getMessages() as $msg) {
                        throw new Exception($msg);
                    }
                }
                
                $this->flashSession->success('Se ha creado el tipo de visita exitosmante');
                return $this->response->redirect('visittype');
            } 
            catch (Exception $ex) {
                $this->flashSession->error($ex->getMessage());
            }
        }
    }
    
    public function editAction($idVisittype)
    {
        $vtype = Visittype::findFirst(array(
            'conditions' => 'idVisittype = ?1',
            'bind' => array(1 => $idVisittype),
        ));
        
        if (!$vtype) {
            $this->flashSession->error("No se encontró el tipo de visita, por favor valide la información");
            return $this->response->redirect('visittype');
        }
        
        $form = new VisittypeForm($vtype);
        $this->view->setVar('form', $form);
        $this->view->setVar('vtype', $vtype);
        
        if ($this->request->isPost()) {
            try {
                $form->bind($this->request->getPost(), $vtype);
                $client->updated = time();
                
                if (!$vtype->save()) {
                    foreach ($vtype->getMessages() as $msg) {
                        throw new Exception($msg);
                    }
                }
                
                $this->flashSession->notice("Se ha editado el tipo de visita: <strong>{$vtype->name}</strong>,  exitosmante");
                return $this->response->redirect('visittype');
            } 
            catch (Exception $ex) {
                $this->flashSession->error($ex->getMessage());
            }
        }
    }
    
    public function removeAction($idVisittype)
    {
        $vtype = Visittype::findFirst(array(
            'conditions' => 'idVisittype = ?1',
            'bind' => array(1 => $idVisittype),
        ));
        
        if (!$vtype) {
            $this->flashSession->error("No se encontró el tipo de visita, por favor valide la información");
            return $this->response->redirect('visittype');
        }
        
        try {
            $vtype->delete();
            $this->flashSession->warning("Se ha eliminado el cliente exitosamente");
//            return $this->response->redirect('client');
        } 
        catch (Exception $ex) {
            $this->logger->log("Exception: {$ex}");
            $this->flashSession->error("Ocurrió un error, por favor contacte al administrador");
//            return $this->response->redirect('client');
        }
        
        return $this->response->redirect('visittype');
    }
}
