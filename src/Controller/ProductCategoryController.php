<?php
namespace App\Controller;

use App\Form\ProductCategoryForm;
use App\Entity\Prodcategory;

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
 * Class ProductCategoryController
 * @Route("/prodcategory")
 * @package App\Controller
 */
class ProductCategoryController extends ControllerCore
{

/**
 * @Route("/list", name="prodcategory_list")
 * @param Request $request
 * @return Response
 */
	public function getProdcategoryList(Request $request): Response
	{
		$post	= $request->request->all();

		$table = $this->createDataTable([])
			->setName('list_category')
			->setTemplate('pages/product_category/table.template.twig')
			->add('name', TextColumn::class,[])


//	----------  Left as example to create column with HTML content. See "templates/pages/product_category/table.template.twig"

//			->add('isActive', TextColumn::class,[
//				'render' => function($value, $context){
//					return '<input type="checkbox" value="'.$value.'"/>';
//				}
//			])

			->createAdapter(ORMAdapter::class, [
				'entity' => Prodcategory::class,
				'query' => function (QueryBuilder $builder) {
					$builder
						->select('pc')
						->from(Prodcategory::class, 'pc')
					;
				},
				'criteria' => [
					function (QueryBuilder $builder) use ($post) {
						$builder->andWhere('pc.isActive = '.(int)(!empty( $post['showActive'])));
					},
					new SearchCriteriaProvider(),
				],
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
					],

					'isActive'	=> [
						'title'		=> 'title.showActive',
						'value'	=> empty($post['showActive']) ? '' : $post['showActive']
					]
				]
			],

			'headerTitle'	=> 'title.category.pl',
			'itemPath'		=> 'prodcategory_form',
		]);
	}
//______________________________________________________________________________

	/**
	 * @param Prodcategory $category
	 * @return FormInterface
	 */
	private function generateProdcategoryForm(Prodcategory $category ): FormInterface
	{
		return $this->createForm(ProductCategoryForm::class, $category, [
			'action' => $this->generateUrl('category_save'),
			'method' => 'POST'
				,'attr' => [
					'id'			=> 'dialog_form',
					'category_id'	=> $category->getId() ?? 0,
				]
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/form", name="prodcategory_form")
 * @param Request $request
 * @return JsonResponse
 */
	public function getProdcategoryForm(Request $request):JsonResponse
	{
		$id	= $request->query->get('id');
		$prod_cat_repo	= $this->getDoctrine()->getRepository(Prodcategory::class);

		$data		= $prod_cat_repo->getFormData( $id );
		$category	= $data['entity'];

		$form = $this->generateProdcategoryForm($category);

		$content	= $this->render('dialogs/category_modal.twig',[
			'categoryForm'	=> $form->createView(),
			'category'		=> $category,
		])->getContent();

		return new JsonResponse([ 'success'	=> true, 'html' => $content ]);
	}
//______________________________________________________________________________

/**
 * @Route("/save", name="category_save")
 * @param Request $request
 * @return JsonResponse
 */
	public function saveCategory(Request $request): JsonResponse
	{


//$pppsot	= $request->request->all();
//
//$this->logger->info(print_r(  $pppsot,1),[__FILE__]);

		$psot	= $request->request->all()['prodcategory_form'];



		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con		= $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo		= $this->getDoctrine()->getRepository(Prodcategory::class);
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

			'table'	=> [
				'input'	=> [
					'search'=> [
						'value'	=> $search
					],

					'isActive'	=> [
						'value'	=> ($category->getIsActive() ? 'checked' : '')
					]
				]
			]
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/status", name="category_change_status")
 * @param Request $request
 * @return JsonResponse
 */
	public function changeCategoryStatus(Request $request):JsonResponse
	{
		$post	= $request->request->all();
		$id		= $post['category_id'];

		$em			= $this->getDoctrine()->getManager();
		$category	= $em->find(Prodcategory::class, $id);
		$is_active	= !$category->getIsActive();
		$category->setIsActive( $is_active );

		$em->persist($category);
		$em->flush();

		return new JsonResponse([ 'success'	=> true, 'scope'=>'category' ]);
	}
//______________________________________________________________________________

}
