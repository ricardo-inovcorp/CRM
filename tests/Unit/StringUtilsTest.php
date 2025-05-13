<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    public function test_str_limit_retorna_string_truncada_com_sufixo()
    {
        $texto = "Lorem ipsum dolor sit amet consectetur adipiscing elit";
        $resultado = \Illuminate\Support\Str::limit($texto, 10);
        
        $this->assertEquals("Lorem ipsu...", $resultado);
    }

    public function test_str_limit_retorna_string_completa_quando_menor_que_limite()
    {
        $texto = "Lorem ipsum";
        $resultado = \Illuminate\Support\Str::limit($texto, 20);
        
        $this->assertEquals("Lorem ipsum", $resultado);
    }
    
    public function test_str_limit_com_sufixo_personalizado()
    {
        $texto = "Lorem ipsum dolor sit amet";
        $resultado = \Illuminate\Support\Str::limit($texto, 10, '>>>');
        
        $this->assertEquals("Lorem ipsu>>>", $resultado);
    }
    
    public function test_str_contains_retorna_true_quando_contem_substring()
    {
        $texto = "Laravel é um framework PHP";
        
        $this->assertTrue(\Illuminate\Support\Str::contains($texto, 'Laravel'));
        $this->assertTrue(\Illuminate\Support\Str::contains($texto, 'framework'));
        $this->assertFalse(\Illuminate\Support\Str::contains($texto, 'Django'));
    }
    
    public function test_str_starts_with_verifica_inicio_da_string()
    {
        $texto = "Laravel é um framework PHP";
        
        $this->assertTrue(\Illuminate\Support\Str::startsWith($texto, 'Laravel'));
        $this->assertFalse(\Illuminate\Support\Str::startsWith($texto, 'PHP'));
    }
} 