<?php

namespace TaggersIo\LaravelCommunicableQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class CommunicableJob implements ShouldQueue
{
	use Queueable;

	public static $name;
}
