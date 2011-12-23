<?php

function vd ()
{
	return call_user_func_array( 'var_dump' , func_get_args() );
}
function pvd ()
{
	echo '<pre>';
	call_user_func_array( 'var_dump' , func_get_args() );
	echo '</pre>';
}

