<?php
/**
* phpBB Directory extension
* Migration for banner optimization and local storage
*/

namespace nextgen\phpbbdirectory\migrations\v20x;

class m5_banner_optimization extends \phpbb\db\migration\migration
{
    /**
    * Verificamos si ya existen las configuraciones para no duplicar
    */
    public function effectively_installed()
    {
        return isset($this->config['dir_banner_width']) && 
               isset($this->config['dir_banner_height']) &&
               version_compare($this->config['nextgen_phpbbdirectory_version'], '2.0.0', '>=');
    }

    static public function depends_on()
    {
        // Esto asegura que se ejecute después de tus migraciones anteriores de la v2.0.0
        return array('\nextgen\phpbbdirectory\migrations\v20x\v2_0_0');
    }

    public function update_config()
    {
        return array(
            // Añadimos las variables si no existen (valor 0 = auto)
            array('config.add', array('dir_banner_width', 0)),
            array('config.add', array('dir_banner_height', 80)),
            // Actualizamos la versión oficial en la DB
            array('config.set', array('nextgen_phpbbdirectory_version', '2.0.0')),
        );
    }

    public function update_data()
    {
        return array(
            array('custom', array(array($this, 'prepare_directory_structure'))),
        );
    }

    /**
    * Crea la carpeta física y protege el acceso directo
    */
    public function prepare_directory_structure()
    {
        $path = $this->phpbb_root_path . 'images/directory/banners/';
        
        if (!file_exists($path))
        {
            @mkdir($path, 0755, true);
        }

        // Creamos el index.htm de protección si no existe
        if (!file_exists($path . 'index.htm'))
        {
            @file_put_contents($path . 'index.htm', '');
        }
    }
}