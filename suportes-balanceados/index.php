<?php
function validarColchetes($s)
{
    $pilha = [];  // Vamos usar um array para armazenar os parênteses de abertura
    $mappingChar = [
        ')' => '(',
        '}' => '{',
        ']' => '['
    ];

    for ($i = 0; $i < strlen($s); $i++) {
        $character = $s[$i];  // Pega o caractere atual
        // Verifica se o caractere é um parêntese de fechamento
        if ($character == ')' || $character == '}' || $character == ']') {
            // Se a pilha não estiver vazia e o último parêntese de abertura for correspondente
            if (!empty($pilha) && end($pilha) == $mappingChar[$character]) {
                array_pop($pilha);  // Remove o parêntese de abertura da pilha
            } else {
                return false;  // Se não tiver correspondência, retorna falso
            }
        } elseif ($character == '(' || $character == '{' || $character == '[') {
            // Se for um parêntese de abertura, coloca na pilha
            array_push($pilha, $character);
        }
    }
    // Se a pilha estiver vazia, significa que todos os parênteses foram fechados corretamente
    return empty($pilha);
}

// Exemplos de uso:
var_dump(validarColchetes("(){}[]"));  // true
var_dump(validarColchetes("[{()}](){}"));  // true
var_dump(validarColchetes("[]{()"));  // false
var_dump(validarColchetes("[{)]"));  // false
