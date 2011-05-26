<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Author: Liu Song - loosen.copen@gmail.com
 * File: compiler.include_if_exists.php
 * Type: compiler
 * Name: include_if_exists
 * Version: 1.0.0
 * Source: http://code.google.com/p/smartyplugin-include-if-exists/
 * License: GNU LESSER GENERAL PUBLIC LICENSE
 * Purpose: Similar with "include" function, but only include the
    template file when it exists. Otherwise, a default file passed
    by parameter "else" will be included.
 * Example:
    1   {include_if_exists file="foo.tpl" assign="foo"}
    2   {include_if_exists file="foo.tpl" else="default.tpl"}
 * -------------------------------------------------------------
 */
function smarty_compiler_include_if_exists( $tag_attrs, &$compiler )
{
    $_params = $compiler->_parse_attrs($tag_attrs);
    $arg_list = array();
    if(!isset($_params['file'])) {
        $compiler->_syntax_error("missing 'file' attribute in include_exists tag", E_USER_ERROR, __FILE__, __LINE__);
        return;
    }

    foreach($_params as $arg_name => $arg_value) {
        if($arg_name == 'file') {
            $include_file = $arg_value;
            continue;
        } else if($arg_name == 'else') {
            $include_file_else = $arg_value;
            continue;
        } else if($arg_name == 'assign') {
            $assign_var = $arg_value;
            continue;
        }
        if(is_bool($arg_value)) {
            $arg_value = $arg_value ? 'true' : 'false';
        }
        $arg_list[] = "'$arg_name' => $arg_value";
    }

    if($include_file_else) {
        $output = "\n\$_include_file = (\$this->template_exists({$include_file})) ? {$include_file} : {$include_file_else};\n";
    } else {
        $output = "\nif(\$this->template_exists({$include_file})) {\n";
    }

    if(isset($assign_var)) {
        $output .= "ob_start();\n";
    }

    $output .= "\$_smarty_tpl_vars = \$this->_tpl_vars;\n";

    if($include_file_else) {
        $params = "array('smarty_include_tpl_file' => \$_include_file, 'smarty_include_vars' => array(".implode(',', (array)$arg_list)."))";
    } else {
        $params = "array('smarty_include_tpl_file' => {$include_file}, 'smarty_include_vars' => array(".implode(',', (array)$arg_list)."))";
    }
    $output .= "\$this->_smarty_include($params);\n" .
        "\$this->_tpl_vars = \$_smarty_tpl_vars;\n" .
        "unset(\$_smarty_tpl_vars);\n";

    if(isset($assign_var)) {
        $output .= "\$this->assign(" . $assign_var . ", ob_get_contents()); ob_end_clean();\n";
    }

    if($include_file_else) {
        $output .= "unset(\$_include_file);\n";
    } else {
        $output .= "}\n";
    }

    return $output;
}
