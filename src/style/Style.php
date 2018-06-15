<?php

namespace linkphp\page\style;

use linkphp\page\Paginator;

class Style extends Paginator
{

    public function render()
    {
        $page_banner = "总记录数为:{$this->total} 页数:{$this->total_pages} " . $this->first() .
            $this->end();
        return $page_banner;
    }

}