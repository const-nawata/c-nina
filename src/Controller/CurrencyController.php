<?php
namespace App\Controller;

use App\Form\CurrencyForm;
use App\Entity\Currency;

use Omines\DataTablesBundle\Adapter\Doctrine\ORM\SearchCriteriaProvider;
use Doctrine\ORM\QueryBuilder;

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\NumberColumn;
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
			->add('symbol', TextColumn::class,[])
			->add('ratio', NumberColumn::class, ['searchable' => false, 'className' => 'number-list-sell'])

			->createAdapter(ORMAdapter::class, [
				'entity' => Currency::class
			])
			->handleRequest($request);

		if ($table->isCallback()) {
			return $table->getResponse();
		}

		return $this->show($request, 'layouts/base.table.twig', [
			'table'	=> [
				'data'	=> $table,
				'width' => 5,

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
	 * @param Currency $currency
	 * @return FormInterface
	 */
	private function generateCurrencyForm(Currency $currency ): FormInterface
	{
		return $this->createForm(CurrencyForm::class, $currency, [
			'action' => $this->generateUrl('currency_save'),
			'method' => 'POST'
				,'attr' => [
					'id'			=> 'dialog_form',
					'currency_id'	=> $currency->getId() ?? 0,
				]
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
		$repo		= $this->getDoctrine()->getRepository(Currency::class);
		$id			= $request->query->get('id');
		$data		= $repo->getFormData( $id );
		$currency	= $data['entity'];

		$content	= $this->render('dialogs/currency_modal.twig',[
			'currencyForm'	=> $this->generateCurrencyForm($currency)->createView(),
			'currency'		=> $currency,
		])->getContent();

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
		$post	= $request->request->all()['currency_form'];
		$error	= ['message' => '', 'field' => ''];
		$search	= '';

		$con		= $this->getDoctrine()->getManager()->getConnection();
		$con->beginTransaction();

		try {
			$repo		= $this->getDoctrine()->getRepository(Currency::class);
			$data		= $repo->getFormData($post['id']);
			$currency	= $data['entity'];

			$form = $this->generateCurrencyForm($currency);

			$form->handleRequest( $request );

			if( $success = ($form->isSubmitted() && $form->isValid()) ) {
				$repo->saveFormData( $post );
				$search	= $currency->getName();
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
					]
				]
			]
		]);
	}
//______________________________________________________________________________

}
