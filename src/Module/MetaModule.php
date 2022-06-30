<?php

namespace App\Module;

/**
 * Class MetaModule
 * @package App\Module
 * @author Didier Moindreau <dmoindreau@gmail.com> on 07/11/2019.
 */
class MetaModule extends Module
{
	protected static bool $isMetamodule = true;
	public function __construct()
	{
		parent::__construct();
	}
}
