<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    public function test_array_get_retorna_valor_default_quando_chave_nao_existe()
    {
        $array = ['nome' => 'João', 'idade' => 30];
        
        $this->assertEquals('João', \Illuminate\Support\Arr::get($array, 'nome'));
        $this->assertEquals(30, \Illuminate\Support\Arr::get($array, 'idade'));
        $this->assertEquals('N/A', \Illuminate\Support\Arr::get($array, 'profissao', 'N/A'));
        $this->assertNull(\Illuminate\Support\Arr::get($array, 'endereco'));
    }
    
    public function test_array_has_verifica_se_chave_existe()
    {
        $array = ['user' => ['nome' => 'Maria', 'email' => 'maria@example.com']];
        
        $this->assertTrue(\Illuminate\Support\Arr::has($array, 'user'));
        $this->assertTrue(\Illuminate\Support\Arr::has($array, 'user.nome'));
        $this->assertFalse(\Illuminate\Support\Arr::has($array, 'user.idade'));
    }
    
    public function test_array_pluck_extrai_valores_por_chave()
    {
        $array = [
            ['id' => 1, 'nome' => 'João'],
            ['id' => 2, 'nome' => 'Maria'],
            ['id' => 3, 'nome' => 'Pedro']
        ];
        
        $nomes = \Illuminate\Support\Arr::pluck($array, 'nome');
        $this->assertEquals(['João', 'Maria', 'Pedro'], $nomes);
        
        $nomesComId = \Illuminate\Support\Arr::pluck($array, 'nome', 'id');
        $this->assertEquals([1 => 'João', 2 => 'Maria', 3 => 'Pedro'], $nomesComId);
    }
    
    public function test_array_only_retorna_apenas_chaves_especificadas()
    {
        $array = ['id' => 1, 'nome' => 'João', 'email' => 'joao@example.com', 'senha' => 'secreta'];
        
        $resultado = \Illuminate\Support\Arr::only($array, ['id', 'nome']);
        $this->assertEquals(['id' => 1, 'nome' => 'João'], $resultado);
    }
    
    public function test_array_except_retorna_array_sem_chaves_especificadas()
    {
        $array = ['id' => 1, 'nome' => 'João', 'email' => 'joao@example.com', 'senha' => 'secreta'];
        
        $resultado = \Illuminate\Support\Arr::except($array, ['senha', 'email']);
        $this->assertEquals(['id' => 1, 'nome' => 'João'], $resultado);
    }
} 