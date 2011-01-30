<?php

namespace Application\HelloBundle\Menu;
use Bundle\MenuBundle\Menu;
use Symfony\Component\HttpFoundation\Request,
	Symfony\Component\Routing\Router;

class MainMenu extends Menu
{
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Home', $router->generate('Accueil', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Bundles', $router->generate('Imaginons', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Projects', $router->generate('Sites', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Developers', $router->generate('Communiquons', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Search', $router->generate('Outils', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Api', $router->generate('Photos_Scan', array()));
        $this->setCurrentUri($request->getRequestUri());
		$this->addChild('Feed', $router->generate('Jouons', array()));
    }
}
