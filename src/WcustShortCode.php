<?php

class WcustShortCode
{

    public static function init()
    {
        $self = new self;
        add_shortcode('view-events', array($self, 'ViewEvents'));
    }

    public function PfmMember()
    {
        ob_start();
        include_once PFM_VIEW_PATH . "content-member.php";
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

}
