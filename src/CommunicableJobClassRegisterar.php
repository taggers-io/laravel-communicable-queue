<?php

namespace TaggersIo\LaravelCommunicableQueue;

class CommunicableJobClassRegisterar
{
	private static $classes = [];

	public static function register(string $name, string $class)
	{
		self::$classes[$name] = $class;
	}

	public static function get(string $name): string
	{
		return self::$classes[$name];
	}
}
