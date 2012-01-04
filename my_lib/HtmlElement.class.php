<?php
/**
**  @file HtmlElement.class.php
**  @author immeëmosol (programmer dot willfris at nl) 
**  @date 2010-10-26
**  Created: Tue 2010-10-26, 12:17.21 CEST
**  Last modified: ven 2011-01-07, 15:41.41 CET
**/

namespace output
{
	class HtmlElement implements \ArrayAccess , \Countable #, Iterator
	{
		protected $element_name  =  '';

		protected $attributes  =  array();

		protected $contents  =  array( 'before_children' => '' , 'after_children' => '' , );
		private $children  =  array();

		public           function __construct ( $element_name , $args )
		{
			$this->element_name  =  $element_name;
			foreach ( $args as $arg )
			{
				if ( is_array( $arg ) )
				{
					foreach ( $arg as $attribute_name => $attribute_value )
					{
						$this->attributes[ $attribute_name ]  =  $attribute_value;
					}
				}
				elseif ( $arg instanceof HtmlElement )
				{
					$this->children[]  =  $arg;
				}
				else
				{
					//@TODO[~immeëmosol, Tue 2010-10-26, 14:02.41 CEST]
					//	 implement mechanism to differentiate between content before and after child-elements
					$this->contents[ 'before_children' ]  =  $arg;
				}
			}
		}

		public function expose ()
		{
			return $this->__toString();
		}

		public function __toString ()
		{
			$do_element  =  !empty( $this->element_name );
			$uitvoer  =  '';
			if ( $do_element )
			{
				$uitvoer .=  '<';
				$uitvoer .=  $this->element_name;
				foreach ( $this->attributes as $attribute_name => $attribute_value )
				{
					if ( is_array( $attribute_value ) )
					{
						//@TODO[~immeëmosol, ĵaŭ 2010-11-04, 15:39.19 CET]
						//  uitzoeken of het gewenst is dat hier voor de attribuutwaarde
						//  ALTIJD de elementen bij elkaar worden gevoegd d.m.v. een spatie
						//  (wanneer het om een array gaat).
						//@TODO[~immeëmosol, ĵaŭ 2010-11-04, 15:41.48 CET]
						//  wanneer een atribuutwaarde meerdere keren in de array voorkomt,
						//  is dat dan een fout?
						//  in dat geval moeten de array-waardes eerst nog even uniek gemaakt worden.
						$attribute_value  =  implode( ' ' , $attribute_value );
					}
					$uitvoer .=  ' ' . $attribute_name . '="' . $attribute_value . '"';
				}
				$uitvoer .=  '>';
				//$uitvoer .=  "\n";
			}
			$uitvoer .=  $this->contents[ 'before_children' ];
			$uitvoer .=  $this->parse( $this->children );
			$uitvoer .=  $this->contents[ 'after_children' ];
			if ( $do_element )
			{
				$uitvoer .=  '</' . $this->element_name . '>';
				//$uitvoer .=  "\n";
			}
			return $uitvoer;
		}
		private function parse ( $array )
		{
			$uitvoer  =  '';
			foreach ( $array as $child )
			{
				if ( $child instanceof HtmlElement )
				{
					$child  =  $child->expose();
				}

				if ( is_array( $child ) )
				{
					$child  =  $this->parse( $child );
				}

				$uitvoer .=  $child;
			}
			return $uitvoer;
		}

		// {{{ Countable
		/**
		**  Count the elements for this object.
		**  @param void
		**  @return int The amount of elements this object holds.
		**/
		public function count ()
		{
			return count( $this->children );
		}
		// /Countable }}}
		// {{{ ArrayAccess
		/**
		**  @param $offset mixed
		**  @return boolean
		**/
		public function offsetExists ( $offset )
		{
			return isset( $this->children[ $offset ] );
		}
		/**
		**  @param $offset mixed
		**  @return mixed
		**/
		public function offsetGet ( $offset )
		{
			return $this->children[ $offset ];
		}
		/**
		**  @param $offset mixed
		**  @param $value mixed
		**  @return void
		**/
		public function offsetSet ( $offset , $value )
		{
			if ( NULL === $offset )
			{
				$this->children[]  =  $value;
				return;
			}
			$this->children[ $offset ]  =  $value;
		}
		/**
		**  @param $offset mixed
		**  @return void
		**/
		public function offsetUnset ( $offset )
		{
			unset( $this->children[ $offset ] );
		}
		// /ArrayAccess }}}

		// {{{ Iterator
		/**
		**  @return mixed
		**/
		public function current ()
		{
		}
		/**
		**  @return scalar
		**/
		public function key ()
		{
		}
		/**
		**  @return void
		**/
		public function next ()
		{
		}
		/**
		**  @return void
		**/
		public function rewind ()
		{
		}
		/**
		**  @return boolean
		**/
		public function valid ()
		{
		}
		// /Iterator }}}

	}
}


