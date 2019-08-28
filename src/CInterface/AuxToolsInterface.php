<?php
namespace App\CInterface;

/**
 * Interface AuxToolsInterface
 * @package App\CInterface
 * @author Kostiantyn Kolienchenko <const.nawata@gmail.com>
 */
interface AuxToolsInterface
{
	/**
	 * adjusts width and height of image file
	 *
	 * @param string $file - path to file
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @return bool
	 */
	public function fitProductImage( string $file, int $maxWidth, int $maxHeight ): bool;

	/**
	 * saves application state
	 *
	 * @param array $params - parameters to save
	 * @return void
	 */
	public function saveState( array $params ): void;

	/**
	 * gets application state
	 *
	 * @return array
	 */
	public function getState(): array;
}
