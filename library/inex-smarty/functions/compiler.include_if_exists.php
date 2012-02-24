<?php
/*
 * Smarty plugin
 * Purpose: Similar with "include" function, but only include the
    template file when it exists. Otherwise, a default file passed
    by parameter "else" will be included.
 * Example:
    1   {include_if_exists file="foo.tpl" assign="foo"}
    2   {include_if_exists file="foo.tpl" else="default.tpl"}
 * -------------------------------------------------------------
 */
function smarty_compiler_include_if_exists( $params, $smarty )
{
    if( !isset( $params['file'] ) )
        throw new SmartyCompilerException( "Missing 'file' attribute in tmplinclude tag" );

    $original_values = array();

    foreach( $params as $arg => $value )
    {
        if( is_bool( $value ) )
            $params[ $arg ] = $value ? 'true' : 'false';

        if( !in_array( $arg, array( 'file', 'assign', 'else' ) ) )
        {
            $original_values[ $arg ] = $value;
            $smarty->assign( $arg, $value );
        }
    }

    $params['file'] = str_replace( array( '\'', '"' ), '', $params['file'] );
    $params['else'] = str_replace( array( '\'', '"' ), '', $params['file'] );
    
    if( $smarty->getTemplateVars( '___SKIN' ) )
        $skin = $smarty->getTemplateVars( '___SKIN' );
    else
        $skin = false;

    if( $skin && $smarty->templateExists( 'skins/' . $skin . '/' . $params['file'] ) )
        $params['file'] = 'skins/' . $skin . '/' . $params['file'];
    elseif( $skin && $smarty->templateExists( 'skins/' . $skin . '/' . $params['else'] ) )
        $params['file'] = 'skins/' . $skin . '/' . $params['else'];
    elseif( $smarty->templateExists( $params['file'] ) )
        $params['file'] = $params['file'];
    elseif( $smarty->templateExists( $params['else'] ) )
        $params['file'] = $params['else'];
    else
        throw new SmartyCompilerException( "Template file nor alternative does not exist for all skins - [{$params['file']}]" );
    
    $output = '';

    if( isset( $params['assign'] ) )
        $smarty->assign( $params['assign'], $smarty->fetch( $params['file'] ) );
    else
        $output = $smarty->fetch( $params['file'] );

    foreach( $original_values as $arg => $value )
    {
        $smarty->assign( $arg, $value );
    }

    return $output;
}

