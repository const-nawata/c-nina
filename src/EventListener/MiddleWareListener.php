<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

use Psr\Log\LoggerInterface;
use App\CInterface\AuxToolsInterface;

/**
 * Class EventListener
 *
 * @author Constantine Nawata <const.nawata@gmail.com>
 * @package App\Service
 */
class MiddleWareListener
{

	private $logger;

	protected $tools;

    /**
     * @param LoggerInterface $logger
	 * @param AuxToolsInterface $tools
     */
    public function __construct( LoggerInterface $logger, AuxToolsInterface $tools )
    {
        $this->logger	= $logger;
        $this->tools	= $tools;
    }
//______________________________________________________________________________

    public function onKernelController( ControllerEvent $event ){
//    	$controller	= $event->getController();
//		$class	= get_class($controller[0]);
//		$method	= $controller[1];

//		$namedArguments = $event->getRequest()->attributes->all();
//		$controllerArguments = $event->getArguments();
    }
//______________________________________________________________________________

	/**
	 * @param string $res
	 * @param array $args
	 */
	private function processIndex( string $res, array $args ): void
	{
		list($entity, $ind )	= explode(':', $res);

		if( $args[0]->request->has("$ind")){
			$value	= $args[0]->request->get("$ind");
			$this->tools->saveState(["$ind" => ["$entity" => $value]]);
		}else{
			$state	= $this->tools->getState();
			$value	= $state["$ind"]["$entity"] ?? $state["$ind"]["$entity"];
		}

		$args[0]->request->add(["$ind" => $value]);
	}
//______________________________________________________________________________

	public function onKernelControllerArguments( ControllerArgumentsEvent $event )
	{
		$args	= $event->getArguments();
		$method	= $args[0]->attributes->get('_controller');

		switch( $method ){
			case 'App\Controller\ProductController::getProductList':
				$this->processIndex('product:showActive', $args);
				$this->processIndex('product:currency', $args);
			break;

			case 'App\Controller\ProdcategoryController::getProdcategoryList':
				$this->processIndex('prodcategory:showActive', $args);
			break;
		}

		$event->setArguments( $args );
	}
//______________________________________________________________________________

	public function onKernelResponse( ResponseEvent $event )
	{
	}
//______________________________________________________________________________

}//Class end