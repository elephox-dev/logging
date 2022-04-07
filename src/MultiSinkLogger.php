<?php
declare(strict_types=1);

namespace Elephox\Logging;

use Elephox\Logging\Contract\LogLevel;
use Stringable;
use Throwable;

class MultiSinkLogger extends AbstractLogger
{
	/**
	 * @var list<Contract\Sink>
	 */
	private array $sinks;

	public function __construct()
	{
		$this->sinks = [];
	}

	public function addSink(Contract\Sink $sink): void
	{
		$this->sinks[] = $sink;
	}

	public function log(Throwable|Stringable|string $message, LogLevel $level, array $metaData = []): void
	{
		if ($message instanceof Throwable) {
			$message = $message->getMessage();
			if (!array_key_exists('exception', $metaData)) {
				$metaData['exception'] = $message;
			}
		} elseif ($message instanceof Stringable) {
			$message = $message->__toString();
		}

		foreach ($this->sinks as $sink) {
			$sink->write($message, $level, $metaData);
		}
	}
}
