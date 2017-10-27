<?php

namespace TaggersIo\LaravelCommunicableQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use ReflectionClass;
use RuntimeException;

class CommunicateWrapperJob implements ShouldQueue
{
	use Queueable;

	private $job_name;
	private $serialized;

	public function __construct(CommunicableJob $job)
	{
		$cloned_job = clone $job;
		$reflect = new ReflectionClass($cloned_job);
		foreach ($reflect->getProperties() as $property) {
			$property->setAccessible(true);
			$this->{$property->getName()} = $property->getValue($cloned_job);
		}

		$this->job_name = $job::$name;
		$this->serialized = serialize(clone $job);
	}

	public static function wrap(CommunicableJob $job)
	{
		return new self($job);
	}

	public function handle()
	{
		$class = CommunicableJobClassRegisterar::get($this->job_name);
		if (!$class) {
			throw new RuntimeException('Unkown communicable job');
		}

		$instance = $this->unserializeWithNewClass($class);
		if (!method_exists($instance, 'handle')) {
			throw new RuntimeException('The job must have `handle` method');
		}
		$instance->handle();
	}

	private function unserializeWithNewClass(string $class)
	{
		return unserialize(preg_replace(
			"/^O:\\d+:\".+\"/U",
			"O:" . strlen($class) . ":\"{$class}\"",
			$this->serialized
		));
	}
}
