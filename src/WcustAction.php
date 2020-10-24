<?php

class WcustAction
{

    public static function init()
    {
        $self = new self;
        // add_action('admin_post_', array($self,'') );
        //add_action('admin_post_nopriv_', array($self,'') );

    }

    public function pfmImportUserData()
    {
        $target_dir  = PFM_PATH . "assets/uploads/";
        $target_file = $target_dir . time() . '_' . basename($_FILES["import-file"]["name"]);
        move_uploaded_file($_FILES["import-file"]["tmp_name"], $target_file);

        $pfmImport = new PfmImport;
        $pfmImport->generate($_POST, $target_file);
    }
}
