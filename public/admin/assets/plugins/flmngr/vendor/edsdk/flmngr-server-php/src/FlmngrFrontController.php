<?php
namespace EdSDK\FlmngrServer;
use EdSDK\FlmngrServer\lib\CommonRequest;
use EdSDK\FlmngrServer\fs\FMDiskFileSystem;

class FlmngrFrontController
{
    public $request;

    public $filesystem;

    public function __construct($config)
    {
        if (isset($config['adapter'])) {
            $adapter_class_name =
                'EdSDK\FlmngrServer\lib\\' . $config['adapter'];
            if (class_exists($adapter_class_name)) {
                $request = new $adapter_class_name($config);
            } else {
                die('Request adapter not found');
            }
        } else {
            $request = new CommonRequest();
        }
        $request->parseRequest();
        $this->request = $request;

        $this->filesystem = new FMDiskFileSystem($config);
    }
}

?>
