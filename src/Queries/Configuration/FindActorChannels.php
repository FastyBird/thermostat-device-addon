<?php declare(strict_types = 1);

/**
 * FindActorChannels.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     Queries
 * @since          1.0.0
 *
 * @date           26.10.23
 */

namespace FastyBird\Addon\VirtualThermostat\Queries\Configuration;

use FastyBird\Addon\VirtualThermostat\Documents;
use FastyBird\Addon\VirtualThermostat\Exceptions;
use FastyBird\Addon\VirtualThermostat\Types;
use FastyBird\Connector\Virtual\Queries as VirtualQueries;
use function sprintf;

/**
 * Find device actors channels entities query
 *
 * @template T of Documents\Channels\Actors
 * @extends  VirtualQueries\Configuration\FindChannels<T>
 *
 * @package        FastyBird:VirtualThermostatAddon!
 * @subpackage     Queries
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FindActorChannels extends VirtualQueries\Configuration\FindChannels
{

	/**
	 * @phpstan-param Types\ChannelIdentifier $identifier
	 *
	 * @throws Exceptions\InvalidArgument
	 */
	public function byIdentifier(Types\ChannelIdentifier|string $identifier): void
	{
		if (!$identifier instanceof Types\ChannelIdentifier) {
			throw new Exceptions\InvalidArgument(
				sprintf('Only instances of: %s are allowed', Types\ChannelIdentifier::class),
			);
		}

		parent::byIdentifier($identifier->value);
	}

}
