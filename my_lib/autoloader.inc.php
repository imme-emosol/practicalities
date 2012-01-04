<?php
/**
**  @file autoloader.inc.php
**  @author immeëmosol (programmer dot willfris at nl) 
**  @date 2010-12-03
**  Created: ven 2010-12-03, 10:01.29 CET
**  Last modified: ven 2011-06-10, 12:02.50 CEST
**/
if ( !defined( 'NAMESPACE_SEPARATOR' ) )
	define( 'NAMESPACE_SEPARATOR' , '\\' );

function mijn_autoloader ( $class_name )
{
	if ( defined( 'DEV' ) && isset( $_GET['show_autoload'] ) )
		var_dump( $class_name );
	//$class_name  =  implode( DIRECTORY_SEPARATOR , array_map( 'lcfirst' , explode( '\\' , $class_name ) ) );
	//@note[~immeëmosol, sab 2010-12-25, 12:41.31 CET]
	//  replace namespace-slashes with direcory_separator for current operating system
	$class_name     =  implode(
		DIRECTORY_SEPARATOR ,
		explode( NAMESPACE_SEPARATOR , $class_name )
	);
	$extensions     =  array_filter( array_map( 'trim' , explode( ',' , spl_autoload_extensions() ) ) );
	$include_paths  =  array_filter( array_map( 'trim' , explode( PATH_SEPARATOR , get_include_path() ) ) );
	foreach ( $include_paths as $path )
	{
		$path .=  ( DIRECTORY_SEPARATOR !== $path[ strlen( $path ) - 1 ] ) ? DIRECTORY_SEPARATOR : '';
		foreach ( $extensions as $extension )
		{
			$file  =  $path . $class_name . $extension;
			if ( $e = file_exists( $file ) && $r = is_readable( $file ) )
			{
				require $file;
				return;
			}
			elseif( defined( 'DEV' ) && isset( $_GET['show_autoload'] ) )
				var_dump($path , $class_name , $extension,@$e,@$r);
		}
	}
	//throw new Exception( _( 'class ' . $class_name . ' could not be found.' ) );
	//@NOTE[~imme]: throwing an exception is unwanted
	//	because of the use of class_exists()
	//		which is a proper check to see if a class is loadable and should therefore not throw an exception
	//	or this should throw an exception but then the exception should be catched everytime class_exists() is used
	//		if it would and other autoloaders in the stack do the same, all those exception need to be catched???
}

spl_autoload_extensions( '.php , .class.php' );
spl_autoload_register();
spl_autoload_register( 'mijn_autoloader' );

#@todo[~immeëmosol, dim 2010-12-26, 13:14.08 CET]
# ooit nog 's 't verschil uitzoeken tussen deze en die hierboven ...
/* * function mijn_autoloader ( $class_name )
	{
		$include_paths  =  explode( PATH_SEPARATOR , get_include_path() );
		$extensions  =  array_map(
			'trim' ,
			explode(
				',' ,
				spl_autoload_extensions()
			)
		);
		if ( '\\' !== DIRECTORY_SEPARATOR )
			{
				$class_name  =  str_replace( '\\' , DIRECTORY_SEPARATOR , $class_name );
			}
		foreach ( $include_paths as $include_path )
			{
				foreach ( $extensions as $extension )
					{
						$file  =  $include_path . DIRECTORY_SEPARATOR
							. $class_name . $extension;
						if ( file_exists( $file ) && is_readable( $file ) )
							{
								require $file;
								return TRUE;
							}#else{echo $file.'<br>';}
					}
			}
		trigger_error( 'Could not load class: `' . $class_name . '`.' , E_USER_ERROR );
		return FALSE;
	}/**/
