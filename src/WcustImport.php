<?php


class WcustImport
{

    public function __construct()
    {
        //
    }

    public function generate($post, $target_file)
    {
        $dataType   = esc_sql($_POST['import-data-type']);
        $importData = esc_sql($_POST['import-data']);
        if ($dataType == 'json' && $importData == 'user-data') {
            $this->importFromJSON($target_file);
        } else if ($dataType == 'csv' && $importData == 'user-data') {
            $this->importFromCSV($target_file);
        }
    }

    public function importFromCSV($target_file)
    {
        $file = new \SplFileObject($target_file);
        $file->setFlags(\SplFileObject::READ_CSV);

        $cnt       = 0;
        $meta_keys = [];
        $user_data = [];

        foreach ($file as $row) {
            $cnt++;

            if ($cnt == 1) {
                $meta_keys = $row;
                continue;
            }

            if (empty($row[0])) {
                continue;
            }

            if (!in_array('user_login', $meta_keys)) {
                continue;
            }

            $index = array_search('user_login', $meta_keys);

            $user_id = username_exists($row[$index]);

            $user_data = [
                'user_pass'  => $row['user_pass'][0],
                'user_url'   => $row['user_url'][0],
                'user_email' => $row['user_email'][0],
                'nickname'   => $row['user_nickname'][0],
                'first_name' => $row['first_name'][0],
                'last_name'  => $row['last_name'][0],
                'role'       => $row['roles'][0][0],
            ];
        }
    }
}
