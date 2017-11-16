<?php
/**
 * UwinCMS
 *
 * Файл содержащий контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 * @version   $Id$
 */

// Объявление псевдонимов для всех используемых классов в данном файле
use \Uwin\Controller\Action;
use \Uwin\Layout;
use \Uwin\Registry;
use \Uwin\Exception\Route   as RouteException;

/**
 * Контроллер главной страницы, страниц ошибок и тизера
 *
 * @author    Khmelevskii Yurii (y@uwinart.com)
 * @copyright Copyright (c) 2009-2011 UwinArt Studio (http://uwinart.com)
 */
class DirectoriesController extends Action
{
	public function indexAction() {
        return $this;
    }
}