<?php
namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

//	Annotations
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\ProductCategory;
use App\Entity\Product;

/**
 * @author Nawata
 * @Route("/dashboard")
 */
class DashboardController extends ControllerCore
{

/**
 * @author Nawata
 * @Route("/", name="dashboard")
 * @param Request $request,
 * @return Response
 */
	public function dashboard(Request $request):Response
	{
/*
		$em = $this->getDoctrine()->getManager();

//TODO: Implement categories functionality like for Products.

		$categories = $em->createQueryBuilder()
			->select('c')
			->from(ProductCategory::class, 'c')
			->where('c.isActive = :is_active')
			->setParameter('is_active', 1)
			->orderBy('c.name', 'ASC')
			->getQuery()->getResult()
		;

		$categories_dis = $em->createQueryBuilder()
			->select('c')
			->from(ProductCategory::class, 'c')
			->where('c.isActive = :is_active')
			->setParameter('is_active', 0)
			->orderBy('c.name', 'ASC')
			->getQuery()->getResult()
		;

		$products	= $em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->orderBy('p.name', 'ASC')
			->getQuery()->getResult()
		;
*/


		return $this->show($request,'pages/dashboard/edashboard.twig', [
//			'categories'	=> $categories,
//			'categories_dis'=> $categories_dis,
//			'products'		=> $products,
			'scope'			=> $request->query->has('scope') ? $request->query->get('scope') : 'category'
		]);
	}
//______________________________________________________________________________

}
