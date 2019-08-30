<?php

namespace App\Repository;

use App\Entity\Prodcategory;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Prodcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prodcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prodcategory[]    findAll()
 * @method Prodcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProdcategoryRepository extends ServiceEntityRepository
{
	protected $logger;

    public function __construct( RegistryInterface $registry, LoggerInterface $logger )
    {
    	$this->logger	= $logger;
        parent::__construct($registry, Prodcategory::class);
    }
//______________________________________________________________________________

	/**
	 * @param integer $id
	 * @return array: Prodcategory data
	 */
	public function getFormData( $id=0 ): array
	{
		if( $id > 0){
			$category = $this->find($id);
		}else{
			$category = new Prodcategory();
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
			: new Prodcategory();

		$category->setName($post['name']);
		$category->setDescription($post['description']);
		$category->setIsActive(empty($post['isActive'])?0:$post['isActive']);

		$this->_em->persist( $category );
		$this->_em->flush();
	}
}
