<?php declare(strict_types = 1);

namespace FastyBird\Addon\VirtualThermostat\Tests\Fixtures\Dummy;

use FastyBird\Library\Metadata\Types as MetadataTypes;
use FastyBird\Module\Devices\Schemas as DevicesSchemas;

final class DummyChannelSchema extends DevicesSchemas\Channels\Channel
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = MetadataTypes\Sources\Addon::VIRTUAL_THERMOSTAT->value . '/channel/' . DummyChannelEntity::TYPE;

	public function getEntityClass(): string
	{
		return DummyChannelEntity::class;
	}

	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
