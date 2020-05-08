<?php
add_action("wp_loaded", "wpBlocksRegisterTax");
function wpBlocksRegisterTax()
{
    $postTypes = get_post_types(["exclude_from_search" => false], "names");

    $labels = [
        "name" => "Blocos",
        "singular_name" => "Bloco",
        "menu_name" => "Blocos",
        "all_items" => "Todos os Blocos",
        "parent_item" => "Bloco pai",
        "parent_item_colon" => "Bloco pai:",
        "new_item_name" => "Nome do novo bloco",
        "add_new_item" => "Adicionar novo bloco",
        "edit_item" => "Editar bloco",
        "update_item" => "Atualizar bloco",
        "separate_items_with_commas" => "Separado por vírgula",
        "search_items" => "Procurar blocos",
        "add_or_remove_items" => "Adicionar ou remover blocos",
        "choose_from_most_used" => "Escolha entre os blocos mais usados",
        "not_found" => "Não encontrado",
    ];

    $capabilities = [
        "manage_terms" => "manage_blocks",
        "assign_terms" => "assign_blocks",
        "edit_terms"   => "edit_blocks",
        "delete_terms" => "delete_blocks"
    ];

    $args = [
        "labels" => $labels,
        "hierarchical" => true,
        "public" => true,
        "show_ui" => true,
        "show_admin_column" => true,
        "show_in_nav_menus" => true,
        "show_tagcloud" => true,
        "capabilities" => $capabilities,
    ];

    register_taxonomy("blocos", $postTypes, $args);
}