<?php
/*
 * Smarty plugin to include files based on INEX's IXP Manager
 * skin system.
 */
function smarty_compiler_tmplinclude( $params, $smarty )
{
    if( !isset( $params['file'] ) )
        throw new SmartyCompilerException( "Missing 'file' attribute in tmplinclude tag" );

    $original_values = array();
    
    foreach( $params as $arg => $value )
    {
        if( is_bool( $value ) )
            $params[ $arg ] = $value ? 'true' : 'false';
        
        if( !in_array( $arg, array( 'file', 'assign' ) ) )
        {
            $original_values[ $arg ] = $value;
            $smarty->assign( $arg, $value );
        }
    }
    
    if( substr( $params['file'], 0, 24 ) == '$_smarty_tpl->tpl_vars[\'' )
    {
        $params['file'] = substr( $params['file'], 24 );
        $params['file'] = substr( $params['file'], 0, strpos( $params['file'], '\'' ) );
        $params['file'] = $smarty->getTemplateVars( $params['file'] );
    }
    elseif( substr( $params['file'], 0, 24 ) == '($_smarty_tpl->tpl_vars[' )
    {
        $params['file'] = substr( $params['file'], 24 );
        $params['file'] = substr( $params['file'], 0, strpos( $params['file'], ']' ) );
        $params['file'] = $smarty->getTemplateVars( $params['file'] );
    }
    elseif( substr( $params['file'], 0, 23 ) == '$_smarty_tpl->tpl_vars[' )
    {
        $params['file'] = substr( $params['file'], 23 );
        $params['file'] = substr( $params['file'], 0, strpos( $params['file'], ']' ) );
        $params['file'] = $smarty->getTemplateVars( $params['file'] );
    }
    else
        $params['file'] = str_replace( array( '\'', '"' ), '', $params['file'] );

    if( $smarty->getTemplateVars( '___SKIN' ) )
        $skin = $smarty->getTemplateVars( '___SKIN' );
    else
        $skin = false;
    
    if( $skin && $smarty->templateExists( 'skins/' . $skin . '/' . $params['file'] ) )
        $params['file'] = 'skins/' . $skin . '/' . $params['file'];
    elseif( !$smarty->templateExists( $params['file'] ) )
        throw new SmartyCompilerException( "Template file does not exist - [{$params['file']}]" );
    
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
