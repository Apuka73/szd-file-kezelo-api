<?php

namespace Controller\Sample;

use Controller\AuthenticatedApiController;
use Exception\NotFound;
use Model\Sample\Content;
use Service\Filter;

class ContentController extends AuthenticatedApiController
{
    public function listAction()
    {
        return Content::find();
    }

    public function getAction()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            throw new NotFound();
        }

        $item = Content::getById($id);
        if (!$item) {
            throw new NotFound();
        }

        return $item;
    }

    public function deleteAction()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            throw new NotFound();
        }

        $item = Content::getById($id);
        if (!$item) {
            throw new NotFound();
        }

        return $item->delete();
    }

    public function saveAction()
    {
        $id = $this->request->getPut('id', [Filter::FILTER_INT]);
        $name = $this->request->getPut('name', Filter::FILTER_SAFE_STRING);
        $content = $this->request->getPut('content', Filter::FILTER_SAFE_STRING);

        if (!empty($id)) {
            $item = Content::getById($id);
        }
        if (empty($item)) {
            $item = new Content();
        }

        $item->setName($name);
        $item->setContent($content);

        return $item->save();
    }
}
