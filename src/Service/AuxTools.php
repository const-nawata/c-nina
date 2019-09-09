<?php
namespace App\Service;

use phpDocumentor\Reflection\Types\Boolean;
use Psr\Log\LoggerInterface;
use App\CInterface\AuxToolsInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Currency;

/**
 * Class AuxTools
 *
 * @author Constantine Nawata <const.nawata@gmail.com>
 * @package App\Service
 */
class AuxTools implements AuxToolsInterface
{

	const _state_file	= __DIR__.'/../../public/data/state';
	protected $logger;
	protected $em;

	public function __construct( LoggerInterface $logger, EntityManagerInterface $em ){
		$this->logger	= $logger;
		$this->em		= $em;
	}
//______________________________________________________________________________

	private function openImage($file)
	{
		$mime_type	= mime_content_type( $file );

		switch( $mime_type ) {
			case 'image/jpeg': $img = @imagecreatefromjpeg($file); break;
			case 'image/gif': $img = @imagecreatefromgif($file); break;
			case 'image/png': $img = @imagecreatefrompng($file); break;

			case 'image/bmp':
			case 'image/x-ms-bmp':
			case 'image/x-windows-bmp': $img = @imagecreatefrombmp($file); break;

			default: $img = false;
		}

		return $img;
	}
//______________________________________________________________________________

	public function fitProductImage( string $file, int $maxWidth, int $maxHeight ): bool
	{
		$image = $this->openImage( $file );

        if ($image == false) {
        	$this->logger->debug("Failed processing image file.",[__FILE__]);//TODO: Change to throw exception.
            return false;
        }

		$width	= imagesx($image);
		$height	= imagesy($image);

		$width_scale	= $maxWidth / $width;
		$height_scale	= $maxHeight / $height;

		$scale	= ($width_scale < $height_scale) ? $width_scale : $height_scale;

		$imageResized = imagescale($image, round($width * $scale), round($height * $scale) );
		$write = imagepng($imageResized, $file);

		return true;
	}
//______________________________________________________________________________

	public function saveState( array $params ): void
	{
		$state	= ( !file_exists(self::_state_file) )
			? $this->getDefaultState()
			: unserialize( file_get_contents(self::_state_file) );

		foreach( $params as $flag => $data )
			foreach( $data as $entity => $value )
				$state["$flag"]["$entity"]	= $value;

		$state	= serialize($state);
		file_put_contents(self::_state_file, $state);
	}
//______________________________________________________________________________

	/**
	 * @return array
	 */
	private function getDefaultState(): array
	{
		$currency_repo	= $this->em->getRepository(Currency::class);
		$default_currency	= $currency_repo->getDefault();

		return [
			'showActive'	=> [
				'product'	=> 'checked',
				'prodcategory'	=> 'checked'
			],

			'currency'	=> [
				'product'	=> $default_currency['id']
			]
		];
	}
//______________________________________________________________________________

	public function getState(): array
	{
		!file_exists(self::_state_file) ? $this->saveState([]):null;

		$state	= file_get_contents(self::_state_file);
		$state	= unserialize( $state );

		if( $state['currency']['product'] == 0 ){
			$currency_repo	= $this->em->getRepository(Currency::class);
			$default_currency	= $currency_repo->getDefault();

			if($default_currency['id'] != 0){
				$state['currency']['product']	= $default_currency['id'];
				$this->saveState($state);
			}
		}

		return $state;
	}
//______________________________________________________________________________

}//Class end
