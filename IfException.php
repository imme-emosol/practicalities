<?php
/**
 *  @file IfException.php
 *  @author
 *    - immeëmosol (programmer dot willfris at nl)
 *  @date 2011-09-22
 *  Created: ĵaŭ 2011-09-22, 02:14.46 CEST
 *  Last modified: ĵaŭ 2011-09-22, 05:02.52 CEST
**/

class IfException extends Exception
{
	public function __construct ()
	{
		$db  =  debug_backtrace();
		$db  =  $db[0];
		$f  =  file_get_contents( $db[ 'file' ] );
		$f  =  explode( "\n" , $f );
		for ( $i = 2; ; $i++ )
		{
			if (
				FALSE !==
				$pos  =  strpos(
					$f[ ( $db[ 'line' ] - $i ) ]
					, 'if'
				)
			)
				break;
			if ( $i > 10 )
				throw new Exception(
					'if-statement in code is too big( many lines upwards)'
				);
		}
		$txt  =  implode( ' ' , array_slice( $f , $db['line'] - $i , 10 ) );
		preg_match_all(
'/\((((?>[^()]+)|(?R))*)\)/m' ,
			$txt ,
			$cond ,
			PREG_PATTERN_ORDER ,
			$pos - 2
		);
		$cond  =  trim( $cond[1][0] );
		$cond  =  str_replace( "\t" , '' , $cond );
		$cond  =  str_replace( '  ' , ' ' , $cond );
		echo ''
			. '<pre>'
			. '<em style="font-style:normal;color:darkred;">'
			. str_pad( ' ' , strlen( $db[ 'line' ] ) + 3 )
			. 'If-Exception :::&gt;&gt;&gt; (' . $cond . ') : '
			. '</em>'
			. "\n"
		;
		for ( ; $i > 0 ; $i-- )
			echo ''
				. ( $db[ 'line' ] - $i ) . ' : ' . $f[ ( $db[ 'line' ] - $i ) ]
				. "\n"
			;
		echo ''
			. '</pre>'
		;
	}
}

