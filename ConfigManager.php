<?php

class Alert {
    public $email;
    public $id;
    public $title;
    public $url;
    public $interval = 30;
    public $time_last_ad = 0;
    public $time_updated = 0;

    public function fromArray(array $values)
    {
        foreach ($values AS $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray()
    {
        return array(
            "email" => $this->email,
            "id" => $this->id,
            "title" => $this->title,
            "url" => $this->url,
            "interval" => $this->interval,
            "time_last_ad" => $this->time_last_ad,
            "time_updated" => $this->time_updated
        );
    }
}

class ConfigManager
{
    public static function getConfigFile()
    {
        return dirname(__FILE__)."/configs/config.csv";
    }

    public static function load()
    {
        if (!is_file(self::getConfigFile())) {
            return array();
        }
        $fp = fopen(self::getConfigFile(), "r");
        if (!$header = fgetcsv($fp, 0, ",", '"')) {
            return array();
        }
        $nb = count($header);
        $config = array();
        while (false !== $a = fgetcsv($fp, 0, ",", '"')) {
            $alert = new Alert();
            for ($i = 0; $i < $nb; $i++) {
                if (isset($a[$i])) {
                    $alert->$header[$i] = $a[$i];
                }
            }
            $config[] = $alert;
        }
        fclose($fp);
        return $config;
    }

    public static function save(array $config)
    {
        $filename = self::getConfigFile();
        if (!is_file($filename)) {
            $dir = dirname($filename);
            if ($dir == $filename) {
                throw new Exception("Permission d'écrire sur le fichier de configuration non autorisée.");
            }
            if (!is_writable($dir)) {
                throw new Exception("Permission d'écrire sur le fichier de configuration non autorisée.");
            }
        } elseif (!is_writable(self::getConfigFile())) {
            throw new Exception("Permission d'écrire sur le fichier de configuration non autorisée.");
        }
        $fp = fopen($filename, "w");
        if ($config && is_array($config)) {
            $keys = array_keys($config[0]->toArray());
            fputcsv($fp, $keys, ",", '"');
            foreach ($config AS $alert) {
                fputcsv($fp, array_values($alert->toArray()), ",", '"');
            }
        }
        fclose($fp);
    }
}
