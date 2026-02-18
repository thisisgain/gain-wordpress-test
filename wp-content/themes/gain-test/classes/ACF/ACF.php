<?php

namespace GainTest\ACF;

use GainTest\ACF\Blocks\Content\ContentWithImage;

/**
 * Class ACF
 * @package Pryor\ACF
 */
class ACF
{
	/**
	 * ACF Constructor
	 */
	public function __construct()
	{
		new ContentWithImage();
	}
}
