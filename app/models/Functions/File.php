<?php
/**
 * Common file operation functions.
 */
class Functions_File
{
    /**
     * Copy directory.
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Copy directory into a cloud.
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function recurse_copy_cloud($src, $dst)
    {
        //$finfo = finfo_open(FILEINFO_MIME_TYPE);
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::recurse_copy_cloud($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    $ext = self::getFileExtension($file);
                    $contentType = self::getContentType($ext);
                    if (empty($contentType)) {
                        $contentType = mime_content_type($src . DS . $file);
                    }
                    Manager_File_Rackspace::getInstance()->saveFile(
                        $src . DS . $file,
                        $dst . '/' . $file,
                        false,
                        $contentType ? $contentType : null
                    );
                }
            }
        }
        closedir($dir);
    }

    /**
     * Copy cloud folder to cloud folder.
     *
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function copy_cloud_to_cloud($container, $src, $dst)
    {
        $objectList = Manager_File_Rackspace::getInstance()->getObjectList($src, $container);
        foreach ($objectList as $_o) {
            $_srcName = $_o->getName();
            $_dstName = str_replace($src, $dst, $_srcName);
            Manager_File_Rackspace::getInstance()->copyFile($_srcName, $_dstName, $container);
        }
    }

    /**
     * Remove a directory with content.
     *
     * @param mixed $dir
     * @return void
     */
    public static function rrmdir($dir)
    {
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($dir);
    }

    /**
     * Get file size.
     *
     * @param mixed $filePath
     */
    public static function getFileSizes($filePath)
    {
        require_once LIBRARY_PATH . DS . 'Getid3' . DS . 'getid3.php';
        $getID3 = new getID3;
        $fileInfo = $getID3->analyze($filePath);
        $info = array();
        if (isset($fileInfo['filesize'])) {
            $info['filesize'] = $fileInfo['filesize'];
        }
        if (isset($fileInfo['mime_type'])) {
            $info['mime_type'] = $fileInfo['mime_type'];
        }
        if (isset($fileInfo['video']) && isset($fileInfo['video']['resolution_x'])) {
            $info['width'] = $fileInfo['video']['resolution_x'];
            $info['height'] = $fileInfo['video']['resolution_y'];
        }
        return $info;
    }

    /**
     * File extension string.
     *
     * @param mixed $fileName
     * @return string
     */
    public static function getFileExtension($fileName)
    {
        $fileInfo = explode('.', $fileName);
        return strtolower(array_pop($fileInfo));
    }

    /**
     * Get file type based on filename.
     *
     * @param string $filename
     * @return string
     */
    public static function getFileTypeBasedOnExtension($filename)
    {
        $fileInfo = explode('.', $filename);
        $fileExtension = strtolower(array_pop($fileInfo));
        $type = 'file';
        switch ($fileExtension) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'jpeg':
            case 'svg':
            case 'svgx':
                $type = 'image';
                break;
            case 'mp4':
            case 'webm':
                $type = 'video';
                break;
            case 'wav':
            case 'mp3':
                $type = 'audio';
                break;
            case 'pdf':
                $type = 'pdf';
                break;
            default:
                break;
        }
        return $type;
    }

    /**
     * Get content type from extension.
     *
     * @param string $extension
     * @return string
     */
    public static function getContentType($extension)
    {
        $contentType = null;
        switch ($extension) {
            case 'png':
                $contentType = 'image/png';
                break;
            case 'html':
                $contentType = 'text/html';
                break;
            case 'svg':
            case 'svgx':
                $contentType = 'image/svg+xml';
                break;
            case 'pdf':
                $contentType = 'application/pdf';
                break;
            case 'js':
                $contentType = 'application/javascript';
                break;
            case 'css':
                $contentType = 'text/css';
                break;
            default:
                break;
        }
        return $contentType;
    }
}