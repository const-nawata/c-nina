<?php
namespace App\Controller;

use App\Form\ProductForm;
use App\Entity\Product;

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\TextColumn;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;

use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ProductController
 * @Route("/product")
 * @package App\Controller
 */
class ProductController extends ControllerCore
{
	/**
	 * @param $productData
	 * @return FormInterface
	 */
	private function generateProductForm( $productData ): FormInterface
	{
		return $this->createForm(ProductForm::class, $productData['product'], [
			'action' => $this->generateUrl('product_save'),
			'method' => 'POST',
			'attr' => [
				'id'	=> 'dialog_form',
				'product_id'			=> $productData['product']->getId() ?? 0,
				'formCategories'=> serialize($productData['form_categories'])
			]
		])
		;
	}
//______________________________________________________________________________

/**
 * @Route("/form", name="product_form")
 * @param Request $request
 * @return JsonResponse
 */
	public function getProductForm(Request $request ):JsonResponse
	{
		$id		= $request->query->get('id');
		$data 	= $this->getDoctrine()->getRepository(Product::class)->getProductFormData( $id );

		$filename	= 'product_image_'.$id;
		$path		= __DIR__.'/../../public/images/uploads/';

		 $content	= $this->show($request, 'dialogs/product_modal.twig',[
		 	'productForm'	=> $this->generateProductForm( $data )->createView(),
		 	'product'		=> $data['product'],
		 	'image'			=> file_exists( $path.$filename ) ? $filename : 'default.png'
		 ])->getContent();

		return new JsonResponse([ 'success'	=> true, 'html' => $content ]);
	}
//______________________________________________________________________________

/**
 * @Route("/list", name="product_list")
 * @param Request $request
 * @return JsonResponse
 */
	public function getProductList(Request $request): Response
	{
		$post	= $request->request->all();

		$table = $this->createDataTable([])
			->setName('list_product')
			->setTemplate('pages/product/table.template.twig')
			->add('name', TextColumn::class,[])
			->add('article', TextColumn::class,[])
			->add('tradePrice', NumberColumn::class,['searchable' => false])
			->add('price', NumberColumn::class,['searchable' => false])
			->add('packs', NumberColumn::class,['searchable' => false])
			->add('inPack', NumberColumn::class,['searchable' => false])
			->add('outPack', NumberColumn::class,['searchable' => false])

			->createAdapter(ORMAdapter::class, [
				'entity' => Product::class,
				'query' => function (QueryBuilder $builder) {
					$builder
						->select('p')
						->from(Product::class, 'p')
					;
				},
				'criteria' => [
					function (QueryBuilder $builder) use ($post) {
						empty( $post['showActive'])
							? $builder->andWhere('p.packs = 0')->andWhere('p.outPack = 0')
							: $builder->andWhere('p.packs > 0 OR p.outPack > 0');
					},
					new SearchCriteriaProvider(),
				],
			])
			->handleRequest($request);

		if ($table->isCallback()) {
			$response	= $table->getResponse();
			return $response;
		}

		return $this->show($request, 'layouts/base.table.twig', [
			'table'	=> [
				'data'	=> $table,

				'input'		=> [
					'search'=> [
						'value'	=> empty($post['searchStr']) ? '' : $post['searchStr']
					],

					'isActive'	=> [
						'title'	=> 'title.showInStock',
						'value'	=> empty($post['showActive']) ? '' : $post['showActive']
					]
				]
			],

			'headerTitle'	=> 'title.product.pl',
			'itemPath'		=> 'product_form',
			'modalWidth'	=> 900,
		]);
	}

/**
 * @Route("/save", name="product_save")
 * @param Request $request
 * @return JsonResponse
 */
	public function saveProduct(Request $request):JsonResponse
	{
		$post	= $request->request->all()['product_form'];
		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con = $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo	= $this->getDoctrine()->getRepository(Product::class);
			$data	= $repo->getProductFormData($post['id']);
			$product= $data['product'];

			$form	= $this->generateProductForm( $data );
			$form->handleRequest( $request );

			if( $success = ($form->isSubmitted() && $form->isValid()) ) {
				$repo->saveProductFormData( $post );
				$search	= $product->getArticle();
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
				: ['message' => $message." / ".$e->getCode(), 'field' => 'general'];

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
						'value'	=> ($product->getPacks() == 0 && $product->getOutPack() == 0 ? '' : 'checked')
					]
				]
			]
		]);
	}
//______________________________________________________________________________

/**
 * @Route("/uploadfile", name="uploadfile")
 * @param Request $request
 * @return JsonResponse
 */
     public function uploadFile( Request $request ): JsonResponse
    {
   		$token = $request->get('token');

        if (!$this->isCsrfTokenValid('fileupload043secret', $token)){
            return new JsonResponse([
            	'success'	=> false,
            	'file'		=> '',
				'message'	=> 'CSRF token missing or incorrect.'
			]);
        }

		$product_id	= $request->get('product_id');

		$file	= $request->files->get('file_uploaded');

		if(empty($file)){
            return new JsonResponse([
            	'success'	=> false,
            	'file'		=> '',
				'message'	=> 'Image file missing or incorrect.'
			]);
		}

		$filename	= 'product_image_'.$product_id;
		$path		= '/images/uploads/temp';
		$file->move('../public'.$path, $filename);

		if( !$this->tools->fitProductImage('../public'.$path.'/'.$filename, 250, 300 )){
            return new JsonResponse([
            	'success'	=> false,
            	'file'		=> '',
				'message'	=> 'Image file resizing failed.'
			]);
		}

		return new JsonResponse([
			'success'	=> true,
			'file'		=> $path.'/'.$filename.'?'.strtotime('now'),	// Fictive Unix time is necessary to refresh view if the same image file name is applied.
			'message'	=> ''
		]);
    }
//______________________________________________________________________________

}//Class end
