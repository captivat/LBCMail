<?php

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
            $alert = array();
            for ($i = 0; $i < $nb; $i++) {
                $alert[$header[$i]] = $a[$i];
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
            fputcsv($fp, array_keys($config[0]), ",", '"');
            foreach ($config AS $i => $alert) {
                fputcsv($fp, array_values($alert), ",", '"');
            }
        }
        fclose($fp);
    }
}
