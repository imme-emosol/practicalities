<?php
//ob_end_clean();
if (
	isset( $_SERVER , $_SERVER[ 'SERVER_ADDR' ] ) &&
	'127' ===
	$_SERVER[ 'SERVER_ADDR' ]{0} .
	$_SERVER[ 'SERVER_ADDR' ]{1} .
	$_SERVER[ 'SERVER_ADDR' ]{2} .
	''
)
	define(
		'DEV' ,
		isset( $_GET[ 'verbosity' ] )
			? 0 + $_GET[ 'verbosity' ]
			: TRUE
	);

define( 'DB_USER' , 'php' );
define( 'DB_PASS' , 'php' );

if ( defined( 'DEV') )
{
	error_reporting( -1 );
	define(
		'GLOBAL_FW' ,
		__DIR__
		. DIRECTORY_SEPARATOR
		. 'my_lib'
		. DIRECTORY_SEPARATOR
		. 'simplicity.php'
	);
	//set_include_path(
		//. PATH_SEPARATOR
		//. get_include_path()
	//);
	//vd(get_include_path());
}

if ( TRUE )
{
	function my_exception_handler ( $exception )
	{
		echo <<<CSS
<style>
body{width:99%;}
table,tr,td,th{border-collapse:collapse;border:1px solid black;}
pre{white-space:pre-wrap;}
</style>
CSS;
		echo '<table>';
		echo $exception->xdebug_message;
		echo '</table>';
		return TRUE;
	}
	set_exception_handler( 'my_exception_handler' );
}

function cm ( $c )
	{
		$cm  =  get_class_methods( $c );
		vd( $cm );
	}

function evd (){$a=func_get_args();echo call_user_func_array('rvd',$a);}
function rvd ()
	{
		$args  =  func_get_args();
		ob_start();
		call_user_func_array( 'vd' , $args );
		$ob  =  ob_get_contents();
		ob_end_clean();
		return $ob;
	}
function svd ()
	{
		$e  =  ini_get( 'html_errors' );
		$e  =  $e === '1' || $e === 1 || $e === ''
			? TRUE
			: FALSE
		;
		if ( $e )
			ini_set( 'html_errors' , 0 );
		else
			unset( $e );
		$args  =  func_get_args();
		ob_start();
		call_user_func_array( 'vd' , $args );
		$ob  =  ob_get_contents();
		ob_end_clean();
		if ( isset( $e ) )
		ini_set( 'html_errors' , $e );
		return $ob;
	}
function vd ()# $var )
	{
		$e  =  ini_get( 'html_errors' );
		$e  =  $e === '1' || $e === 1 || $e === ''
			? TRUE
			: FALSE
		;
		if($e){echo '<pre style="text-align:left;">';}
		$args  = func_get_args();
		call_user_func_array( 'var_dump' , $args );
		if($e){echo '</pre>';}
	}
function ep($txt){return pre($txt);}
function pre ( $txt )
	{
		echo '<pre' . /**' style=""' . /**/'>' . "\n";
		echo $txt;
		echo '</pre>' . "\n";
	}
function e77 ( $a )
	{
		echo( wordwrap( $a , 77 ) );
	}

function pdb (){dpb();}
function dpb ()
	{
		echo '<pre>';
		debug_print_backtrace();
		echo '</pre>';
	}
function bt_ifArg ( $condition , $i = 0 )
	{
		$backtrace  =  debug_backtrace();
		array_shift($backtrace);//remove the call to this function
		$bt=$backtrace[$i];
		if ( in_array( $condition , $bt[ 'args' ] ) )
			{
				vd( $bt );
			}
		else
			{
				foreach ( $bt[ 'args' ] as $arg )
					{
						//@todo[~immeëmosol, lun 2010-11-22, 01:25.00 CET]
						//  het zoeken binnen een array verbeteren,
						//  wellicht recursie of iteratie( weet 't verschil nog niet zo goed)
						if ( is_array( $arg ) )
							{
								$arg  =  implode( $arg );
							}
						if ( FALSE !== strpos( $arg , $condition ) )
							{
								vd( $bt );
							}
					}
			}
	}
function bt ( $i = 0 )
	{
		$backtrace  =  debug_backtrace();
		//  remove this function from trace
		vd( $backtrace[ $i + 1 ] );
	}
/* {{{ ... : *//*
function b0 ()
	{
		global $whf__b0_debug;
		$whf__b0_debug  =  debug_backtrace();
	}
function b1 ( $i = 1 )
	{
		global $whf__b0_debug;
		$whf__b1_debug  =  debug_backtrace();
		if ( $whf__b1_debug[1] !== $whf__b0_debug[1] )
			{
				vd($whf__b1_debug[1],$whf__b0_debug[1]);
				bt( $i );
			}
	}
/* / ... }}} */

function efl ()
	{
		$bt  =  debug_backtrace();
		$bt  =  $bt[ 0 ];
		e( $bt[ 'file' ] . ':' . $bt[ 'line' ] );
	}

function d ()
	{
		$backtrace  =  debug_backtrace();
		$file  =  $backtrace[0]['file'].(':'.$backtrace[0]['line']);
		die( 'died in ' . $file . ' @' . time() );
	}

function nl ()
	{
		static $eol  =  NULL;
		if ( NULL === $eol )
			{
				$eol  =  '';
				$eol .=  1 ? '<br />' : '';
				$eol .=  PHP_EOL;
			}
		return func_get_arg( 0 ) . $eol;
	}
function e ()
	{
		$args  =  func_get_args();
		echo call_user_func_array( 'nl' , $args );
	}
function evk ()
	{
		e( 'v~'.func_get_arg( 0 ) . ' : ' . func_get_arg( 1 ).'~k' );
	}
function ekv ()
	{
		e( 'k~'.func_get_arg( 0 ) . ' : ' . func_get_arg( 1 ).'~v' );
	}

define( 'BR' , '<br />' );
define( 'NL' , PHP_EOL );

