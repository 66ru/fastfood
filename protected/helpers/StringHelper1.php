<?php

class StringHelper1
{
	public static function plural($n, $plurals)
	{
		if ( $n % 10 == 1 && $n % 100 != 11 )
	    {
	        return $plurals[0];
	    }

	    if ( $n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 10 || $n % 100 >= 20 ) )
	    {
	        return $plurals[1];
	    }

	    return $plurals[2];
	}
}
