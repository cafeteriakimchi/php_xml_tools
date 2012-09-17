<?php
class xml2object
{
	public function generateFromFileObject($xmlfile)
	{
		$xml = file_get_contents($xmlfile);
		$xml = pack("CCC", 0xef, 0xbb, 0xbf) === substr($xml, 0, 3) ? substr($xml, 3) : $xml;
		return $this->parser(simplexml_load_string($xml));
	}

	protected function parser($xml)
	{
		$class = $xml->getName();
		$attributes = $xml->attributes();
		$type = (string) $attributes->type;

		if(isset($attributes->key))
		{
			$id_key = (string) $attributes->key;
		}

		if ($type == 'object')
		{
			$return = new $class;
		}
		elseif ($type == 'array')
		{
			$return = array();
		}
		elseif ($type == 'null')
		{
			$return = null;
		}
		elseif ($type == 'boolean')
		{
			if (strtolower(trim((string) $xml)) == 'false')
			$return = false;
			else
			$return = (boolean) ((string) $xml);
		}
		else
		{
			$return = trim((string) $xml);
		}

		foreach ($xml->children() as $key => $value)
		{

			if ($value instanceof SimpleXMLElement)
			{
				if($value->count() > 0)
				{
					$value = $this->parser($value);
				}
				else
				{
					$ctype = $value->attributes();
					$ctype = (string) $ctype->type;
					if ($ctype == 'array')
					{
						$value = array();
					}
					elseif ($ctype == 'null')
					{
						$value = null;
					}
					elseif ($ctype == 'boolean')
					{
						if (strtolower(trim((string) $value)) == 'false')
						$value = false;
						else
						$value = (boolean) ((string) $value);
					}
					else
					{
						$value = trim((string) $value);
					}
				}
			}

			if ($type == 'object')
			{
				$return->$key = $value;
			}
			elseif ($type == 'array')
			{
				if (!isset($id_key) && isset($value->id))
				{
					$id_key = 'id';
				}
				if(isset($id_key))
				{
					if (!isset($value->$id_key) || $value->$id_key == '')
					{
						var_dump($value);
						throw new IOException('[xml] file:'.basename($this->xmlfile).' key:'.$id_key.' not found or empty');
					}
					if (array_search($value->$id_key, array_keys($return)) !== false)
					{
						var_dump($value);
						throw new IOException('[xml] file:'.basename($this->xmlfile).' key:'.$id_key.' duplicate entry');
					}
					$return[$value->$id_key] = $value;
				}
				else
				{
					$return[] = $value;
				}
			}
			else
			{
				return $value;
			}
		}
		return $return;
	}
}
