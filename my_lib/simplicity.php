<?php
/**
 *  @file simplicity.php
 *  @author immeëmosol (programmer dot willfris at nl) 
 *  @date 2011-06-13
 *  Created: lun 2011-06-13, 15:09.08 CEST
 *  Last modified: ĵaŭ 2011-06-16, 19:06.39 CEST
**/

$n            =  "\n";
$t            =  "\t";
$br           =  '<br />';
$brn          =  $br . $n;

$request_uri  =  $_SERVER[ 'REQUEST_URI' ];
define( 'CURRENT_URI' , $request_uri );
//vd($_SERVER);
//extract($_SERVER);
//vd($REQUEST_URI);

class Html
{
	public function __construct ( $name , $atts = array() , $content = NULL )
	{
		global $n , $t , $br , $brn , $request_uri;
		$html  =  '';
		$html .=  ''
			. '<'
			. $name
			. $this->atts( $atts )
		;
		if ( FALSE === $content )
			$html .=  ''
				. ' /'
			;
		$html .=  ''
			. '>'
		;
		if ( FALSE !== $content )
			$html .=  ''
				. $n
				. con( $content )
				. '</' . $name . '>'
				. $n
			;
		$this->html  =  $html;
	}
	private function atts ( $a )
	{
		return
			implode(
				array_map(
					create_function(
						' $k , $v ' ,
						'return '
							. '\' \' . $k . \'="\' . '
							. '( is_array( $v ) ? implode( $v ) : $v ) . '
							. '\'"\';'
					) ,
					array_keys( $a ) ,
					array_values( $a )
				)
			)
		;
	}
	public function __toString ()
	{
		return con( $this->html );
	}
}
class Fieldsets implements ArrayAccess
{
	function __construct ( $datasets )
	{
		$form_content    =  array();
		do
		{
			$id    =  key( $datasets );
			$d     =& $datasets[ $id ];

			$legend  =& $d[ 'name' ];
			$data  =& $d[ 'data' ];

			$fieldset    =  array();
			$fieldset[]  =  new Html(
				'legend' ,
				array(
				) ,
				$legend
			);
			if ( is_array( $data ) && 0 < count( $data ) )
			{
				$dl  =  array();
				do
				{
					$def_id  =  key( $data );
					$def  =& $data[ $def_id ];
					$def_id  =  $id . '[' . $def_id . ']';
					if ( !is_array( $def ) )
						//{vd('--',$def_id,$def);continue;}
						throw new Exception( 'something wrong' );

					if ( isset( $def[ 'name' ] ) )
					{
						$fieldset[]  =  new Fieldsets(
							array(
								$def_id => $def ,
							)
						);
						continue;
					}

					$input_atts  =  array();

					$input_atts[ 'name' ]   =  $def_id;
					$input_atts[ 'id' ]     =  str_replace(
						']' ,
						'' ,
						str_replace(
							'[' ,
							'_' ,
							str_replace(
								'][' ,
								'_' ,
								$def_id
							)
						)
					);

					$input_atts[ 'type' ]   =  isset( $def[ 'type' ] ) ? $def[ 'type' ] : 'text';

					$desc   =  isset( $def[ 'desc' ] ) ? $def[ 'desc' ] : NULL;

					if ( isset( $def[ 'value' ] ) )
						$input_atts[ 'value' ]  =  $def[ 'value' ];

					if ( isset( $def[ 'placeholder' ] ) )
						$input_atts[ 'placeholder' ]  =  $def[ 'placeholder' ];

					$label  =  new Html(
						'label' ,
						array( 'for' => $input_atts[ 'id' ] , ) ,
						$desc
					);
					if ( 'textarea' === $input_atts[ 'type' ] )
					{
						$t  =  $input_atts[ 'type' ];
						unset( $input_atts[ 'type' ] );
						if ( isset( $input_atts[ 'value' ] ) )
						{
							$v  =  $input_atts[ 'value' ];
							unset( $input_atts[ 'value' ] );
						}
						$input  =  new Html(
							$t ,
							$input_atts ,
							( isset( $v ) ? $v : NULL )
						);
						unset( $t , $v );
					}
					elseif ( 'file' === $input_atts[ 'type' ] )
						$input  =  array(
							new Html(
								'input' ,
								$input_atts ,
								FALSE
							) ,
							new Html(
								'input' ,
								array(
									'type' => 'hidden' ,
									'name' => $input_atts[ 'name' ] ,
									'value'  =>  '' . $input_atts[ 'name' ] . '' ,
								) ,
								FALSE
							) ,
						);
					else
						$input  =  new Html(
							'input' ,
							$input_atts ,
							FALSE
						);

					$dl[]  =  new Html(
						'dt' ,
						array(
						) ,
						$label
					);
					$dl[]  =  new Html(
						'dd' ,
						array(
						) ,
						$input
					);
				}
				while ( next( $data ) );
				$fieldset[]  =  new Html(
					'dl' ,
					array(
					) ,
					$dl
				);
			}
			$form_content[]  =  new Html(
				'fieldset' ,
				array(
					'id' => $id ,
				) ,
				$fieldset
			);
		}
		while ( next( $datasets ) );
		$this->form_content  =  $form_content;
	}
	public function getArray()
	{
		return $this->form_content;
	}
	public function __toString ()
	{
		return con( $this->form_content );
	}

	public function offsetExists( $offset )
	{
		return isset( $this->form_content[ $offset ] );
	}
	public function offsetGet ( $offset )
	{
		return $this->form_content[ $offset ];
	}
	public function offsetSet ( $offset , $value )
	{
		if ( NULL === $offset )
			$this->form_content[]  =  $value;
		else
			$this->form_content[ $offset ]  =  $value;
	}
	public function offsetUnset ( $offset )
	{
		unset( $this->form_content[ $offset ] );
	}
}
class Form implements ArrayAccess
{
	public function __construct ( &$datasets , $request_uri = CURRENT_URI )
	{
		$this->request_uri  =  $request_uri;
		reset( $datasets );
		if ( !is_array( $datasets ) || 0 >= count( $datasets ) )
			return FALSE;

		$form_content  =  new Fieldsets( $datasets );
		$form_content[]  =  new Html(
			'input' ,
			array(
				'type'  => 'submit' ,
				'value' => 'Profiel bijwerken' ,
			) ,
			FALSE
		);
		$this->form_content  =  $form_content;
	}
	public function __toString ()
	{
		return '' . new Html(
			'form' ,
			array(
				'action'  => $this->request_uri ,
				'method'  => 'POST' ,
				'enctype' => 'multipart/form-data' ,
			) ,
			$this->form_content
		);
	}

	public function offsetExists( $offset )
	{
		return isset( $this->form_content[ $offset ] );
	}
	public function offsetGet ( $offset )
	{
		return $this->form_content[ $offset ];
	}
	public function offsetSet ( $offset , $value )
	{
		if ( NULL === $offset )
			$this->form_content[]  =  $value;
		else
			$this->form_content[ $offset ]  =  $value;
	}
	public function offsetUnset ( $offset )
	{
		unset( $this->form_content[ $offset ] );
	}
}
function con ( $c )
{
	if ( is_array( $c ) )
		return implode( array_map( 'con' , $c ) );
	return $c;
}

