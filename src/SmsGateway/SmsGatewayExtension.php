<?php
namespace ProfiCloS\SmsGateway;

use Nette\DI\CompilerExtension;

class SmsGatewayExtension extends CompilerExtension
{

	private $defaults = [
		'login' => null,
		'password' => null,
		'mode' => Gateway::MODE_DEVELOPMENT
	];

	public function loadConfiguration(): void
	{
		$this->validateConfig($this->defaults);

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('auth'))
			->setFactory(Gateway::class, [ $this->config['login'], $this->config['password'] ])
			->addSetup('setMode', [ $this->config['mode'] ]);
	}

}
