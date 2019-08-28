<?php

namespace App\Repository;

use App\Entity\ProductCategory;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCategory[]    findAll()
 * @method ProductCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCategoryRepository extends ServiceEntityRepository
{
	protected $logger;

    public function __construct( RegistryInterface $registry, LoggerInterface $logger )
    {
    	$this->logger	= $logger;
        parent::__construct($registry, ProductCategory::class);
    }
//______________________________________________________________________________

	/**
	 * @param integer $id
	 * @return array: ProductCategory data
	 */
	public function getFormData( $id=0 ): array
	{
		if( $id > 0){
			$category = $this->find($id);
		}else{
			$category = new ProductCategory();
		}

		return [
			'entity'	=> $category
		];
	}
//______________________________________________________________________________

	/**
	 * @param array $post
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function saveFormData( array $post ): void
	{
		$category	= ( $post['id'] > 0 )
			? $this->find( $post['id'] )
			: new ProductCategory();

		$category->setName($post['name']);
		$category->setDescription($post['description']);
		$category->setIsActive(empty($post['isActive'])?0:$post['isActive']);

		$this->_em->persist( $category );
		$this->_em->flush();
	}
}
