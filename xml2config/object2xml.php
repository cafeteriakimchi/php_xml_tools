<?php
class object2xml
{
	public static function generateValidXml($toxml, $node_name='array')
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>'.PHP_EOL;

		if (is_object($toxml))
		{
			$array = get_object_vars($toxml);
			$node_block = get_class($toxml);
		}

		$xml .= '<' . $node_block .($node_block == 'array' ?' type="array"':' type="object"').'>'.PHP_EOL;
		$xml .= self::generateXmlFromArray($array, $node_name, 1, '', '');
		$xml .= '</' . $node_block . '>'.PHP_EOL;

		return $xml;
	}

	private static function generateXmlFromArray($array, $node_name, $level, $PHP_EOL)
	{
		$xml = ''.$PHP_EOL;

		if (is_array($array) || is_object($array))
		{
			foreach ($array as $key=>$value)
			{
				$type = '';
				if (is_numeric($key))
				{
					$key = $node_name;
				}
				if (is_object($value))
				{
					$key = get_class($value);
					$type = ' type="object"';
				}
				if (is_array($value))
				{
					$type = ' type="array"';
				}
				if (is_null($value))
				{
					$type = ' type="null"';
				}
				if (is_bool($value))
				{
					$type = ' type="boolean"';
				}
				$recursion = self::generateXmlFromArray($value, $node_name, $level + 1, PHP_EOL);
				$xml .= str_repeat("\t", $level).'<' . $key . $type .'>' . $recursion . ((strpbrk($recursion, '<>') || $recursion == PHP_EOL) ? str_repeat("\t", $level) : '').'</' . $key . '>'.PHP_EOL;
			}
		}
		else
		{
			if (is_bool($array))
			{
				$xml = ($array == false) ? 'false' : 'true';
			}
			else
			{
				$xml = htmlspecialchars($array, ENT_QUOTES);
			}
		}
		return $xml;
	}

}
