<?php
namespace App\Controller;

//use App\Form\ProductCategoryForm;
use App\Entity\Currency;

use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Doctrine\ORM\QueryBuilder;

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;

/**
 * Class CurrencyController
 * @Route("/currency")
 * @package App\Controller
 */
class CurrencyController extends ControllerCore
{

/**
 * @Route("/list", name="currency_list")
 * @param Request $request
 * @return Response
 */
	public function getCurrencyList(Request $request): Response
	{
		$post	= $request->request->all();

		$table = $this->createDataTable([])
			->setName('list_category')
			->setTemplate('pages/currency/table.template.twig')
			->add('name', TextColumn::class,[])

			->createAdapter(ORMAdapter::class, [
				'entity' => Currency::class,
			])
			->handleRequest($request);

		if ($table->isCallback()) {
			return $table->getResponse();
		}

		return $this->show($request, 'layouts/base.table.twig', [
			'table'	=> [
				'data'	=> $table,
				'width' => 6,

				'input'		=> [
					'search'=> [
						'value'	=> empty($post['searchStr']) ? '' : $post['searchStr']
					]
				]
			],

			'headerTitle'	=> 'title.currency',
			'itemPath'		=> 'currency_form',
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/form", name="currency_form")
 * @param Request $request
 * @return JsonResponse
 */
	public function getCurrencyForm(Request $request):JsonResponse
	{

/*
		$id	= $request->query->get('id');
		$prod_cat_repo	= $this->getDoctrine()->getRepository(ProductCategory::class);

		$data		= $prod_cat_repo->getFormData( $id );
		$category	= $data['entity'];

		$form = $this->generateProdCatForm($category);

		$content	= $this->render('dialogs/category_modal.twig',[
			'categoryForm'	=> $form->createView(),
			'category'		=> $category,
		])->getContent();

/*     */

$content	= '<div>Currency form</div>';

		return new JsonResponse([ 'success'	=> true, 'html' => $content ]);
	}
//______________________________________________________________________________

/**
 * @Route("/save", name="currency_save")
 * @param Request $request
 * @return JsonResponse
 */
	public function saveCurrency(Request $request): JsonResponse
	{
	/*
		$post	= $request->request->all()['prodcategory_form'];
		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con		= $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo		= $this->getDoctrine()->getRepository(ProductCategory::class);
			$data		= $repo->getFormData($post['id']);
			$category	= $data['entity'];

			$form = $this->generateProdCatForm($category);

			$form->handleRequest( $request );

			if( $success = ($form->isSubmitted() && $form->isValid()) ) {
				$repo->saveFormData( $post );
				$search	= $category->getName();
				$con->commit();
			}else{
				$error_content	= $this->getFormError( $form );;
				throw new \Exception(serialize( $error_content ), 1);
			}
		} catch ( \Exception $e) {
			$success	= false;
			$message	= $e->getMessage();

			$error	=  ( $e->getCode() == 1 )
				? unserialize( $message )
				: ['message' => $message.' / '.$e->getCode(), 'field' => 'general'];

			$con->rollBack();
		}

		return new JsonResponse([
			'success'	=> $success,
			'error'		=> $error,
			'searchStr'	=> $search,
			'showActive'=> ($category->getIsActive() ? 'checked' : '')
		]);



		*/
	}
//______________________________________________________________________________

}
