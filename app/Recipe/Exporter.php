<?php

namespace App\Recipe;

class Exporter
{
    protected $_title;
    protected $_ingredients = [];
    protected $_directions = [];
    
    public function setTitle($t)
    {
        $this->_title = $t;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function setIngredients(array $i)
    {
        $this->_ingredients = $i;
        return $this;
    }
    
    public function getIngredients()
    {
        return $this->_ingredients;
    }
    
    public function setDirections(array $d)
    {
        $this->_directions = $d;
        return $this;
    }
    
    public function getDirections()
    {
        return $this->_directions;
    }
    
    public function addIngredient($quantity, $unit, $item)
    {
        $item = compact('quantity', 'unit', 'item');
        $this->_ingredients[] = $item;
        return $this;
    }
    
    public function addDirection($dir)
    {
        $this->_directions[] = $dir;
        return $this;
    }
    
    public function reset()
    {
        $this->_title = '';
        $this->_ingredients = [];
        $this->_directions = [];
        
        return $this;
    }
    
    public function toRecipeML(\XMLWriter $writer = null, $exportFile = null)
    {
        if(is_null($writer)) {
            $writer = new \XMLWriter();
            if(is_null($exportFile)) {
                $writer->openMemory();
            } else {
                $writer->openUri($exportFile);
            }
            
            $writer->setIndent(true);
            $writer->startDocument('1.0', 'UTF-8');
            $writer->writeDTD('recipeml', '-//FormatData//DTD RecipeML 0.5//EN', 'http://www.formatdata.com/recipeml/recipeml.dtd');
            $writer->startElement('recipeml');
            $writer->writeAttribute('version', '0.5');
        }
        
        
        $writer->startElement('recipe');
        
        $writer->startElement('head');
        $writer->writeElement('title', $this->getTitle());
        $writer->endElement();
        
        $writer->startElement('ingredients');
        
        foreach($this->getIngredients() as $ingredient) {
            $writer->startElement('ing');
            $writer->startElement('amt');
            $writer->writeElement('qty', $ingredient['quantity']);
            $writer->writeElement('unit', $ingredient['unit']);
            $writer->endElement();
            $writer->writeElement('item', $ingredient['item']);
            $writer->endElement();
        }
        
        $writer->endElement();
        
        $writer->startElement('directions');
        
        foreach($this->getDirections() as $direction) {
            $writer->writeElement('step', $direction);
        }
        
        $writer->endElement();
        $writer->endElement();
        $writer->flush();
        
        return $writer;
    }
}