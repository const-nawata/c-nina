<?php
namespace App\Repository;

use App\Entity\Currency;
use App\Entity\Prodcategory;
use App\Entity\Product;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
	protected $logger;

	public function __construct(RegistryInterface $registry, LoggerInterface $logger)
    {
    	$this->logger	= $logger;
		parent::__construct($registry, Product::class);
	}

	/**
	 * @param integer $id
	 * @return array: Product data including categories
	 */
	public function getProductFormData( $id=0 ): ?array
	{
		if( $id > 0){
			$product = $this->find($id);
		}else{
			$product = new Product();
			$product->setInPack(0);
			$product->setPacks(0);
			$product->setOutPack(0);
			$product->setPrice(0.0);
			$product->setTradePrice(0.0);
		}

		$categories	= $this->_em->getRepository(Prodcategory::class)->findBy([],['name'=>'ASC']);

		$form_categories	= [];

		foreach ( $categories as $cat )
			$form_categories[$cat->getName()]	= $cat->getId();


		$currencies	= $this->_em->getRepository(Currency::class)->findBy([],['name'=>'ASC']);

		$form_currencies	= [];

		foreach ( $currencies as $currency )
			$form_currencies[$currency->getName()]	= $currency->getId();

		return [
			'product'			=> $product,
			'form_categories'	=> $form_categories,
			'form_currencies'	=> $form_currencies
		];
	}
//______________________________________________________________________________

	private function processProductCategories( Product $product, $formCategories ){

		$old_categories	= (empty( $product->getId()) ) ? [] : $product->getCategories();

		foreach( $old_categories as $old_category )
			$product->removeCategory( $old_category );

		foreach( $formCategories as $ategory_id )
			$product->addCategory($this->_em->getRepository(Prodcategory::class)->find($ategory_id));

		return $product;
	}
//______________________________________________________________________________

	private function replaceProductImage( $oldId, $newId ){
		$path	= __DIR__.'/../../public/images/uploads/';

		file_exists($path.'temp/product_image_'.$oldId)
			? rename($path.'temp/product_image_'.$oldId, $path.'product_image_'.$newId):null;
	}
//______________________________________________________________________________

	public function saveProductFormData( $post ){
		$product	= ( $post['id'] > 0 )
			? $this->find( $post['id'] )
			: new Product();

		$form_categories	= empty($post['formCategories']) ? [] : $post['formCategories'];
		$this->processProductCategories( $product, $form_categories );

		$currency	= $this->_em->getRepository(Currency::class)->find($post['currency']);

		$product->setName($post['name']);
		$product->setPrice( str_replace(',', '.', $post['price']) / $currency->getRatio() );
		$product->setTradePrice( str_replace(',', '.', $post['tradePrice']) / $currency->getRatio()  );
		$product->setPacks($post['packs']);
		$product->setInPack($post['inPack']);
		$product->setOutPack($post['outPack']);
		$product->setArticle($post['article']);
		$product->setIsActive(true);	//TODO: Must be checked the previous value for editing process.

		$this->_em->persist($product);
		$this->_em->flush();

		$this->replaceProductImage( $post['id'], $product->getId() );
	}
//______________________________________________________________________________

	/**
	 * @param Currency|int $currency
	 * @return float;
	 */
	public function getConvertedPrice( $currency )
	{
		$price	= 1.11;

		$currency	= is_int($currency)
			? $this->_em->getRepository(Currency::class)->find($currency)
			: $currency;



		return $price;
	}
//______________________________________________________________________________

}
