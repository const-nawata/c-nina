<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
	protected $logger;

    public function __construct( ManagerRegistry $registry, LoggerInterface $logger )
    {
    	$this->logger	= $logger;
        parent::__construct($registry, Currency::class);
    }
//______________________________________________________________________________

	/**
	 * @param integer $id
	 * @return array: Currency data
	 */
	public function getFormData( $id=0 ): array
	{
		return [
			'entity'	=> ( $id > 0 ?  $this->find($id) : new Currency())
		];
	}
//______________________________________________________________________________

}
