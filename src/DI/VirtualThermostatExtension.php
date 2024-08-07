<?php declare(strict_types = 1);

/**
 * VirtualThermostatExtension.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           24.08.22
 */

namespace FastyBird\Addon\VirtualThermostat\DI;

use Contributte\Translation;
use Doctrine\Persistence;
use FastyBird\Addon\VirtualThermostat;
use FastyBird\Addon\VirtualThermostat\Commands;
use FastyBird\Addon\VirtualThermostat\Drivers;
use FastyBird\Addon\VirtualThermostat\Helpers;
use FastyBird\Addon\VirtualThermostat\Hydrators;
use FastyBird\Addon\VirtualThermostat\Schemas;
use FastyBird\Library\Application\Boot as ApplicationBoot;
use FastyBird\Library\Metadata;
use FastyBird\Library\Metadata\Documents as MetadataDocuments;
use Nette\Bootstrap;
use Nette\DI;
use Nettrine\ORM as NettrineORM;
use function array_keys;
use function array_pop;
use const DIRECTORY_SEPARATOR;

/**
 * Virtual connector
 *
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class VirtualThermostatExtension extends DI\CompilerExtension implements Translation\DI\TranslationProviderInterface
{

	public const NAME = 'fbVirtualThermostatAddon';

	public static function register(
		ApplicationBoot\Configurator $config,
		string $extensionName = self::NAME,
	): void
	{
		$config->onCompile[] = static function (
			Bootstrap\Configurator $config,
			DI\Compiler $compiler,
		) use ($extensionName): void {
			$compiler->addExtension($extensionName, new self());
		};
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$logger = $builder->addDefinition($this->prefix('logger'), new DI\Definitions\ServiceDefinition())
			->setType(VirtualThermostat\Logger::class)
			->setAutowired(false);

		/**
		 * DRIVERS
		 */

		$builder->addFactoryDefinition($this->prefix('drivers.thermostat'))
			->setImplement(Drivers\ThermostatFactory::class)
			->getResultDefinition()
			->setType(Drivers\Thermostat::class)
			->setArguments([
				'logger' => $logger,
			]);

		/**
		 * JSON-API SCHEMAS
		 */

		$builder->addDefinition(
			$this->prefix('schemas.device'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Schemas\Devices\Device::class);

		$builder->addDefinition($this->prefix('schemas.channel.actors'), new DI\Definitions\ServiceDefinition())
			->setType(Schemas\Channels\Actors::class);

		$builder->addDefinition(
			$this->prefix('schemas.channel.configuration'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Schemas\Channels\Configuration::class);

		$builder->addDefinition(
			$this->prefix('schemas.channel.preset'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Schemas\Channels\Preset::class);

		$builder->addDefinition(
			$this->prefix('schemas.channel.sensors'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Schemas\Channels\Sensors::class);

		$builder->addDefinition(
			$this->prefix('schemas.channel.state'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Schemas\Channels\State::class);

		/**
		 * JSON-API HYDRATORS
		 */

		$builder->addDefinition(
			$this->prefix('hydrators.device'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Devices\Device::class);

		$builder->addDefinition(
			$this->prefix('hydrators.channel.actors'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Channels\Actors::class);

		$builder->addDefinition(
			$this->prefix('hydrators.channel.configuration'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Channels\Configuration::class);

		$builder->addDefinition(
			$this->prefix('hydrators.channel.preset'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Channels\Preset::class);

		$builder->addDefinition(
			$this->prefix('hydrators.channel.sensors'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Channels\Sensors::class);

		$builder->addDefinition(
			$this->prefix('hydrators.channel.state'),
			new DI\Definitions\ServiceDefinition(),
		)
			->setType(Hydrators\Channels\State::class);

		/**
		 * HELPERS
		 */

		$builder->addDefinition($this->prefix('helpers.device'), new DI\Definitions\ServiceDefinition())
			->setType(Helpers\Device::class);

		/**
		 * COMMANDS
		 */

		$builder->addDefinition($this->prefix('commands.install'), new DI\Definitions\ServiceDefinition())
			->setType(Commands\Install::class)
			->setArguments([
				'logger' => $logger,
			]);
	}

	/**
	 * @throws DI\MissingServiceException
	 */
	public function beforeCompile(): void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		/**
		 * DOCTRINE ENTITIES
		 */

		$services = $builder->findByTag(NettrineORM\DI\OrmAttributesExtension::DRIVER_TAG);

		if ($services !== []) {
			$services = array_keys($services);
			$ormAttributeDriverServiceName = array_pop($services);

			$ormAttributeDriverService = $builder->getDefinition($ormAttributeDriverServiceName);

			if ($ormAttributeDriverService instanceof DI\Definitions\ServiceDefinition) {
				$ormAttributeDriverService->addSetup(
					'addPaths',
					[[__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Entities']],
				);

				$ormAttributeDriverChainService = $builder->getDefinitionByType(
					Persistence\Mapping\Driver\MappingDriverChain::class,
				);

				if ($ormAttributeDriverChainService instanceof DI\Definitions\ServiceDefinition) {
					$ormAttributeDriverChainService->addSetup('addDriver', [
						$ormAttributeDriverService,
						'FastyBird\Addon\VirtualThermostat\Entities',
					]);
				}
			}
		}

		/**
		 * APPLICATION DOCUMENTS
		 */

		$services = $builder->findByTag(Metadata\DI\MetadataExtension::DRIVER_TAG);

		if ($services !== []) {
			$services = array_keys($services);
			$documentAttributeDriverServiceName = array_pop($services);

			$documentAttributeDriverService = $builder->getDefinition($documentAttributeDriverServiceName);

			if ($documentAttributeDriverService instanceof DI\Definitions\ServiceDefinition) {
				$documentAttributeDriverService->addSetup(
					'addPaths',
					[[__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Documents']],
				);

				$documentAttributeDriverChainService = $builder->getDefinitionByType(
					MetadataDocuments\Mapping\Driver\MappingDriverChain::class,
				);

				if ($documentAttributeDriverChainService instanceof DI\Definitions\ServiceDefinition) {
					$documentAttributeDriverChainService->addSetup('addDriver', [
						$documentAttributeDriverService,
						'FastyBird\Addon\VirtualThermostat\Documents',
					]);
				}
			}
		}
	}

	/**
	 * @return array<string>
	 */
	public function getTranslationResources(): array
	{
		return [
			__DIR__ . '/../Translations/',
		];
	}

}
