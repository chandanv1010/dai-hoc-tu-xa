<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PostContentWrapper extends Component
{
    public $postName;
    public $postDescription;
    public $postContent;
    public $contentWithToc;
    public $postPivot;
    public $postUrl;
    public $encodedUrl;
    public $encodedTitle;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $postName = '',
        $postDescription = '',
        $postContent = '',
        $contentWithToc = '',
        $postPivot = null
    ) {
        $this->postName = $postName;
        $this->postDescription = $postDescription;
        $this->postContent = $postContent;
        $this->contentWithToc = $contentWithToc;
        $this->postPivot = $postPivot;
        
        // Generate URLs for social sharing
        $postCanonical = $postPivot->canonical ?? '';
        $this->postUrl = $postCanonical ? write_url($postCanonical) : url()->current();
        $this->encodedUrl = urlencode($this->postUrl);
        $this->encodedTitle = urlencode($postName);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.post-content-wrapper');
    }
}

