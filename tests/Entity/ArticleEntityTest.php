<?php

namespace App\Tests\Entity;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;

class ArticleEntityTest extends TestCase
{
    public function testGetText()
    {
        $article = new Article();

        $article->setText('text');

        $result = $article->getText();

        $this->assertEquals('text', $result);
    }
}
