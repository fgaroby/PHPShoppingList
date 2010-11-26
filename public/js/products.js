function toggleCategories( imgCat )
{
	$( '.cat_' + $( imgCat ).attr( 'name' ) ).toggle();
	
	if( $( imgCat ).attr( 'src' ) == '/public/images/list-add.png' )
	{
		$( imgCat ).attr( 'src', '/public/images/list-remove.png' );
		$( imgCat ).attr( 'alt', 'déplier' );
	}
	else
	{
		$( imgCat ).attr( 'src',  '/public/images/list-add.png' );
		$( imgCat ).attr( 'alt', 'replier' );
	}
}