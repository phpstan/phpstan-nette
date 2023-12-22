<?php declare(strict_types = 1);

namespace PHPStan\Exceptions;

use Throwable;
use function sprintf;

class InvalidLinkDestinationException extends InvalidLinkException
{

	/** @var string */
	private $destination;

	public function __construct(string $destination, int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct(sprintf("Invalid link destination '%s'", $destination), $code, $previous);
		$this->destination = $destination;
	}

	public function getDestination(): string
	{
		return $this->destination;
	}

}
