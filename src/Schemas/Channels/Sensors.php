<?php declare(strict_types = 1);

/**
 * Sensors.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     Schemas
 * @since          1.0.0
 *
 * @date           23.10.23
 */

namespace FastyBird\Addon\VirtualThermostat\Schemas\Channels;

use FastyBird\Addon\VirtualThermostat\Entities;
use FastyBird\Addon\VirtualThermostat\Schemas;
use FastyBird\Connector\Virtual\Schemas as VirtualSchemas;
use FastyBird\Library\Metadata\Types as MetadataTypes;

/**
 * Sensors channel entity schema
 *
 * @template T of Entities\Channels\Sensors
 * @extends  VirtualSchemas\Channels\Channel<T>
 *
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     Schemas
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Sensors extends VirtualSchemas\Channels\Channel
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = MetadataTypes\Sources\Addon::VIRTUAL_THERMOSTAT->value . '/channel/' . Entities\Channels\Sensors::TYPE;

	public function getEntityClass(): string
	{
		return Entities\Channels\Sensors::class;
	}

	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
