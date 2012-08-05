<?php

class hdio2c_newsletter extends hdio2c_newsletter_parent
{

    protected $_sThisTemplate = "newsletter.tpl";

    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

}
?>
