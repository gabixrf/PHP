<?php

function validarTexto($texto){
    $texto = trim($texto);
    if(!empty ($texto)) {
        return $texto;
    }
    return false;
}

function validarPreco($preco){
    $preco= str_replace(',', '.', $preco);

    if (is_numeric($preco) && $preco >= 0){
        return (float) $preco;
    } 
    return false;
}

function validarQuantidade($quantidade){
    if(is_numeric($quantidade) && $qtd >=0){
        return (int) $quantidade;
    } 
    return false;
}

function validarCategoria($categoria){
    $categoriasPermitidas = ['Eletrônicos' , 'Roupas' , 'Alimentos', 'Outros'];
    
    if(in_array($categoria, $categoriasPermitidas)){
        return $categoria;
    } 
    return false;
}


function validarAno($ano){
    $anoAtual = date('Y');
    if (is_numeric($ano) && $ano <=anoAtual && $ano >= 1900) {
        return (int) $ano;
    }
    return false;
}

function sanitizar ($dado){
    return htmlspecialchars($dado, ENT_QUOTES, 'UTF-8');
}
