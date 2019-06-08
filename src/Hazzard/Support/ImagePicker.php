<?php 

namespace Hazzard\Support;

use stdClass;

class ImagePicker
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $errorMessages = array(
        // File upload errors codes
        // http://www.php.net/manual/en/features.file-upload.errors.php
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'gd' => 'PHP GD library is NOT installed on your web server',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_width'  => 'Image exceeds maximum width of ',
        'min_width'  => 'Image requires a minimum width of ',
        'max_height' => 'Image exceeds maximum height of ',
        'min_height' => 'Image requires a minimum height of ',
        'upload_failed' => 'Failed to upload the file',
        'move_failed' => 'Failed to upload the file',
        'invalid_image' => 'Invalid image',
        'image_resize' => 'Failed to resize image',
        'not_exists' => 'Failed to load the image'
    );

    /**
     * Create a new instance.
     *
     * @param  array $options
     * @param  array $errorMessages
     * @return void
     */
    public function __construct($options = array(), $errorMessages = array())
    {
        $this->options = array(
            // Upload directory path:
            'upload_dir' => __DIR__ . '/files/',

            // Upload directory url:
            'upload_url' => $this->getFullUrl() . '/files/',

            // Accepted file types:
            'accept_file_types' => 'png|jpg|jpeg|gif',

            // Directory mode:
            'mkdir_mode' => 0755,

            // File size restrictions (in bytes):
            'max_file_size' => null,
            'min_file_size' => 1,

             // Image resolution restrictions (in px):
            'max_width'  => null,
            'max_height' => null,
            'min_width'  => 1,
            'min_height' => 1,

            // Auto orient image based on EXIF data:
            'auto_orient' => true,

            // Image versions
            'versions' => array(
                //'' => array(
                    //'upload_dir' => '',
                    //'upload_url' => '',
                    // Create square images
                //    'crop' => true,
                //  'max_width' => 200,
               //     'max_height' => 200,
                //),

                // 'avatar' => array(
                //  'crop' => true,
                //  'max_width' => 200,
                //  'max_height' => 200
                // ),

                // 'small' => array(
                //  'crop' => true,
                //  'max_width' => 100
                // )
            )
        );

        $this->options = $options + $this->options;

        $this->errorMessages = $errorMessages + $this->errorMessages;

        $this->initialize();
    }

    /**
     * Initialize upload and crop actions.
     *
     * @return void
     */
    protected function initialize()
    {
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $this->error = $this->getErrorMessage('gd');
            return false;
        }

        $action = isset($_REQUEST['_action']) ? $_REQUEST['_action'] : null;

        if (method_exists($this, $action.'Action')) {
            return $this->{$action.'Action'}();
        }
    }

    /**
     * Load action.
     *
     * @return void
     */
    protected function loadAction()
    {
        if (!isset($this->options['load'])) {
            return;
        }

        $files = call_user_func($this->options['load'], $this);

        if (!$files) {
            return;
        }

        if (!is_array($files)) {
            $files  = array($files);
            $single = true;
        }

        $images = array();

        foreach ($files as $file) {
            $image = new stdClass();
            $image->path = $this->getUploadPath($file);

            if (!file_exists($image->path)) {
                continue;
            }

            $image->name = $file;
            $image->type = $this->getFileExtension($image->name);
            $image->url  = $this->getUploadUrl($image->name);

            list($image->width, $image->height) = @getimagesize($image->path);

            foreach ($this->options['versions'] as $version => $options) {
                $filename = $this->getVersionFilename($image->name, $version);
                $filepath = $this->getUploadPath($filename, $version);

                list($width, $height) = @getimagesize($filepath);

                $image->versions[$version] = array(
                    'url'    => $this->getUploadUrl($filename, $version),
                    'width'  => $width,
                    'height' => $height
                );
            }

            unset($image->path);

            if (isset($single)) {
                $images = $image;
            } else {
                $images[] = $image;
            }
        }

        $this->generateResponse($images);
    }

    /**
     * Preview action.
     *
     * @return void
     */
    protected function previewAction()
    {
        $filename = basename(@$_GET['file']);
        $width    = @$_GET['width'];
        $rotate   = @$_GET['rotate'];

        $filepath = $this->getUploadPath($filename);
        $filetype = $this->getFileExtension($filename);

        if (file_exists($filepath)) {
            list($src_w, $src_h) = @getimagesize($filepath);

            $dst_w = $src_w;
            $dst_h = $src_h;

            if (is_numeric($width) && $width > 0) {
                $dst_w = $width;
                $dst_h = $src_h / $src_w * $width;
            }

            $dst_path = $this->getUploadPath(md5($filename).'.'.$filetype);

            $this->resizeImage($filepath, $dst_path, 0, 0, $dst_w, $dst_h, $src_w, $src_h);

            if (in_array(abs($rotate), array(90, 180, 270))) {
                $angle = ($rotate < 0) ? abs($rotate) : 360 - $rotate;
                $this->rotateImage($dst_path, $angle);
            }

            header('Content-Type: image/jpeg');
            header('Content-Length: ' . filesize($dst_path));
            readfile($dst_path);
            @unlink($dst_path);
        }
    }

    /**
     * Delete action
     *
     * @return void
     */
    protected function deleteAction()
    {
        if (!isset($this->options['delete'])) {
            return;
        }

        $filename = basename(@$_POST['file']);
        $filepath = $this->getUploadPath($filename);

        if (file_exists($filepath) && call_user_func($this->options['delete'], $filename, $this)) {
            foreach ($this->options['versions'] as $version => $options) {
                $name = $this->getVersionFilename($filename, $version);
                $path = $this->getUploadPath($name, $version);
                @unlink($path);
            }

            @unlink($filepath);
        }
    }

    /**
     * Upload action.
     *
     * @return void
     */
    protected function uploadAction()
    {
        $upload = isset($_FILES['file']) ? $_FILES['file'] : null;

        $file = $this->handleFileUpload(
            @$upload['tmp_name'],
            @$upload['name'] == 'blob' ? md5(mt_rand()).'.jpg' : @$upload['name'],
            @$upload['size'],
            @$upload['error']
        );

        $this->generateResponse($file);
    }

    /**
     * Handle file upload.
     *
     * @param  string  $uploaded_file
     * @param  string  $name
     * @param  integer $size
     * @param  integer $error
     * @return stdClass
     */
    protected function handleFileUpload($uploaded_file, $name, $size, $error)
    {
        $image = new stdClass();
        $image->name = $this->getFilename($name);
        $image->type = $this->getFileExtension($name);
        $image->size = $this->fixIntOverflow(intval($size));
        $image->path = $this->getUploadPath($image->name);
        $image->url  = $this->getUploadUrl($image->name);
        list($image->width, $image->height) = @getimagesize($uploaded_file);

        if (!$this->validate($uploaded_file, $image, $error)) {
            return $image;
        }

        $upload_dir = $this->getUploadPath();
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, $this->options['mkdir_mode'], true);
        }

        //Upload start callback
        if (isset($this->options['upload_start'])) {
            call_user_func($this->options['upload_start'], $image, $this);
        }

        $image->path = $this->getUploadPath($image->name);
        $image->url  = $this->getUploadUrl($image->name);

        if (!move_uploaded_file($uploaded_file, $image->path)) {
            $image->error = $this->getErrorMessage('move_failed');
            return $image;
        }

        // Orient the image
        if (!empty($this->options['auto_orient'])) {
            $this->orientImage($image->path);
        }

        list($image->width, $image->height) = @getimagesize($image->path);

        // Generate image versions
        $image->versions = $this->generateVersions($image, true);

        // Upload complete callback
        if (isset($this->options['upload_complete'])) {
            call_user_func($this->options['upload_complete'], $image, $this);
        }

        unset($image->path);

        return $image;
    }

    /**
     * Crop action.
     *
     * @return void
     */
    protected function cropAction()
    {
        $filename = basename(@$_POST['image']);
        $rotate   = @$_POST['rotate'];

        $image = new stdClass();
        $image->name = $filename;
        $image->type = $this->getFileExtension($image->name);
        $image->path = $this->getUploadPath($image->name);
        $image->url  = $this->getUploadUrl($image->name);

        if (!file_exists($image->path)) {
            return $this->generateResponse(array('error'=>$this->getErrorMessage('not_exists')));
        }

        if (!preg_match('/.('.$this->options['accept_file_types'].')+$/i', $image->name)) {
            return;
        }

        list($image->width, $image->height) = @getimagesize($image->path);

        @list($src_x, $src_y, $x2, $y2, $src_w, $src_h) = @array_values(@$_POST['coords']);

        if (isset($this->options['crop_start'])) {
            call_user_func($this->options['crop_start'], $image, $this);
        }

        $image->url  = $this->getUploadUrl($image->name);

        if (empty($src_w) || empty($src_h)) {
            $src_w = $image->width;
            $src_h = $image->height;
        }

        if (empty($src_x) && empty($src_y)) {
            $src_x = $src_y = 0;
        }

        $dst_w = $src_w;
        $dst_h = $src_h;

        $tmp = clone $image;
        $tmp->path = $this->getUploadPath(md5($tmp->name).'.'.$tmp->type);

        @copy($image->path, $tmp->path);

        if (in_array(abs($rotate), array(90, 180, 270))) {
            $angle = ($rotate < 0) ? abs($rotate) : 360 - $rotate;
            $this->rotateImage($tmp->path, $angle);
        }

        $this->resizeImage($tmp->path, null, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        list($tmp->width, $tmp->height) = @getimagesize($tmp->path);

        $image->versions = $this->generateVersions($tmp);

        @unlink($tmp->path);

        if (!isset($this->options['versions'][''])) {
            @rename($image->path, $this->getUploadPath($image->name));
        }

        list($image->width, $image->height) = @getimagesize($this->getUploadPath($image->name));

        if ($image->path != $this->getUploadPath($image->name)) {
            foreach ($this->options['versions'] as $version => $options) {
                $filename = $this->getVersionFilename(basename($image->path), $version);
                @unlink($this->getUploadPath($filename, $version));
            }
        }

        // Crop complete callback
        if (isset($this->options['crop_complete'])) {
            call_user_func($this->options['crop_complete'], $image, $this);
        }

        unset($image->path);

        // Generate json response
        $this->generateResponse($image);
    }

    /**
     * Generate image versions.
     *
     * @param  stdClass $image
     * @param  bool     $is_upload
     * @return array
     */
    protected function generateVersions($image, $is_upload = false)
    {
        $versions = array();
        foreach ($this->options['versions'] as $version => $options) {
            $dst_w = $src_w = $image->width;
            $dst_h = $src_h = $image->height;
            $src_x = $src_y = 0;

            $max_width  = @$options['max_width'];
            $max_height = @$options['max_height'];
            $crop       = isset($options['crop']) && $options['crop'] === true;

            if ($crop) {
                $min   = min($src_w, $src_h);
                $src_x = ($src_w - $min)/2;
                $src_y = ($src_h - $min)/2;
                $dst_w = $dst_h = $src_w = $src_h = $min;
            }

            if (!empty($max_width) && $src_w > $max_width || ($src_w < $max_width && $crop)) {
                $dst_w = $max_width;
                $dst_h = $src_h / $src_w * $max_width;
            } else if (!empty($max_height) && $src_h > $max_height || ($src_h < $max_height && $crop)) {
                $dst_h = $max_height;
                $dst_w = $src_w / $src_h * $max_height;
            }

            $filename = $this->getVersionFilename($image->name, $version);
            $filepath = $this->getUploadPath($filename, $version);
            $upload_dir = $this->getUploadPath('', $version);

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, $this->options['mkdir_mode'], true);
            }

            if (!$is_upload || ($is_upload && $version != '')) {
                $success = $this->resizeImage($image->path, $filepath, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            }

            if (!empty($success)) {
                $versions[$version] = array(
                    'url'    => $this->getUploadUrl($filename, $version),
                    'width'  => $dst_w,
                    'height' => $dst_h
                );
            }
        }

        return $versions;
    }

    /**
     * Validate uploaded file.
     *
     * @param  string   $uploaded_file
     * @param  stdClass $name
     * @param  string   $error
     * @return boolean
     */
    protected function validate($uploaded_file, $file, $error)
    {
        if (!$uploaded_file) {
            $file->error = $this->getErrorMessage(4);
            return false;
        }

        if ($error) {
            $file->error = $this->getErrorMessage($error);
            return false;
        }

        $content_length = $this->fixIntOverflow(intval($_SERVER['CONTENT_LENGTH']));
        $post_max_size  = $this->getConfigBytes(ini_get('post_max_size'));

        if ($post_max_size && $content_length > $post_max_size) {
            $file->error = $this->getErrorMessage('post_max_size');
            return false;
        }

        if ($this->options['max_file_size'] && $file->size > $this->options['max_file_size']) {
            $file->error = $this->getErrorMessage('max_file_size');
            return false;
        }

        if ($this->options['min_file_size'] && $file->size < $this->options['min_file_size']) {
            $file->error = $this->getErrorMessage('min_file_size');
            return false;
        }

        if (!preg_match('/.('.$this->options['accept_file_types'].')+$/i', $file->name)) {
            $file->error = $this->getErrorMessage('accept_file_types');
            return false;
        }

        if (empty($file->width) || empty($file->height)) {
            $file->error = $this->getErrorMessage('invalid_image');
            return false;
        }

        $max_width  = @$this->options['max_width'];
        $max_height = @$this->options['max_height'];
        $min_width  = @$this->options['min_width'];
        $min_height = @$this->options['min_height'];

        if ($max_width || $max_height || $min_width || $min_height) {
            if ($max_width && $file->width > $max_width) {
                $file->error = $this->getErrorMessage('max_width').$max_width.'px';
                return false;
            }

            if ($max_height && $file->height > $max_height) {
                $file->error = $this->getErrorMessage('max_height').$max_height.'px';
                return false;
            }

            if ($min_width && $file->width < $min_width) {
                $file->error = $this->getErrorMessage('min_width').$min_width.'px';
                return false;
            }

            if ($min_height && $file->height < $min_height) {
                $file->error = $this->getErrorMessage('min_height').$min_height.'px';
                return false;
            }
        }

        return true;
    }

    /**
     * Get upload directory path.
     *
     * @param  string $filename
     * @param  string $version
     * @return string
     */
    public function getUploadPath($filename = '', $version = '')
    {
        $upload_dir = $this->options['upload_dir'];

        if ($version != '') {
            $dir = @$this->options['versions'][$version]['upload_dir'];

            if (!empty($dir)) {
                $upload_dir = $dir;
            }
        }

        return $upload_dir . $filename;
    }

    /**
     * Get upload directory url.
     *
     * @param  string $filename
     * @param  string $version
     * @return string
     */
    public function getUploadUrl($filename = '', $version = '')
    {
        $upload_url = $this->options['upload_url'];

        if ($version != '') {
            $url = @$this->options['versions'][$version]['upload_url'];

            if (!empty($url)) {
                $upload_url = $url;
            }
        }

        return $upload_url . $filename;
    }

    /**
     * Get full url.
     *
     * @return string
     */
    protected function getFullUrl() {
        $https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0;

        return
            ($https ? 'https://' : 'http://').
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
            substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

    /**
     * Get file name.
     *
     * @param  string
     * @return string
     */
    public function getFilename($name)
    {
        return $this->getUniqueFilename($name);
    }

    /**
     * Get version name.
     *
     * @param  string
     * @return string
     */
    public function getVersionFilename($filename, $version)
    {
        $ext = $this->getFileExtension($filename);

        if ($version == '') {
            return $filename;
        }

        return str_replace('.'.$ext, "-$version.$ext", $filename);
    }

    /**
     * Get unique file name.
     *
     * @param  string
     * @return string
     */
    public function getUniqueFilename($name)
    {
        while (is_dir($this->getUploadPath($name))) {
            $name = $this->upcountName($name);
        }

        while (is_file($this->getUploadPath($name))) {
            $name = $this->upcountName($name);
        }

        return $name;
    }

    /**
     * Get file extension.
     *
     * @param  string $filename
     * @return string
     */
    public function getFileExtension($filename)
    {
        return pathinfo(strtolower($filename), PATHINFO_EXTENSION);
    }

    /**
     * Generate json response.
     *
     * @param  mixed $response
     * @return string
     */
    public function generateResponse($response)
    {
        echo json_encode($response);
    }

    /**
     * Get error message.
     *
     * @param  string $error
     * @return string
     */
    public function getErrorMessage($error)
    {
        return isset($this->errorMessages[$error]) ? $this->errorMessages[$error] : $error;
    }

    /**
     * Resize image.
     *
     * @param  string       $src_path  Source image path
     * @param  string|null  $dst_path  Destination image path
     * @param  integer      $src_x     x-coordinate of source point
     * @param  integer      $src_y     y-coordinate of source point
     * @param  integer      $dst_w     Destination width
     * @param  integer      $dst_h     Destination height
     * @param  integer      $src_w     Source width
     * @param  integer      $src_h     Source height
     * @return bool
     */
    public function resizeImage($src_path, $dst_path = null, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
    {
        $src_x = ceil($src_x);
        $src_y = ceil($src_y);
        $dst_w = ceil($dst_w);
        $dst_h = ceil($dst_h);
        $src_w = ceil($src_w);
        $src_h = ceil($src_h);

        $dst_path  = ($dst_path) ? $dst_path : $src_path;
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);
        $extension = $this->getFileExtension($src_path);

        if (!$dst_image) {
            return false;
        }

        switch ($extension) {
            case 'gif':
                $src_image = imagecreatefromgif($src_path);
                break;
            case 'jpeg':
            case 'jpg':
                $src_image = imagecreatefromjpeg($src_path);
                break;
            case 'png':
                imagealphablending($dst_image, false);
                imagesavealpha($dst_image, true);
                $src_image = imagecreatefrompng($src_path);
                @imagealphablending($src_image, true);
                break;
        }

        if (isset($src_image) && !$src_image) {
            return false;
        }

        if (!imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)) {
            return false;
        }

        switch ($extension) {
            case 'gif':
                return imagegif($dst_image, $dst_path);
                break;
            case 'jpeg':
            case 'jpg':
                return imagejpeg($dst_image, $dst_path);
                break;
            case 'png':
                return imagepng($dst_image, $dst_path);
                break;
        }
    }

    /**
     * Rotate image.
     *
     * @param  string  $src_path
     * @param  integer $angle
     * @return void
     */
    public function rotateImage($src_path, $angle)
    {
        $type = $this->getFileExtension($src_path);

        switch ($type) {
            case 'gif':
                $source = imagecreatefromgif($src_path);
                break;
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($src_path);
                break;
            case 'png':
                $source = imagecreatefrompng($src_path);
                break;
        }

        $image = imagerotate($source, $angle, 0);

        switch ($type) {
            case 'gif':
                imagegif($image, $src_path);
                break;
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, $src_path);
                break;
            case 'png':
                imagepng($image, $src_path);
                break;
        }

        imagedestroy($source);
        imagedestroy($image);
    }

    /**
     * Orient image based on EXIF orientation data.
     *
     * @param  string $filepath
     * @return void
     */
    protected function orientImage($filepath)
    {
        if (!preg_match('/\.(jpe?g)$/i', $filepath)) {
            return;
        }

        if (!function_exists('exif_read_data')) {
            return;
        }

        $exif = @exif_read_data($filepath);

        if (!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 3: $angle = 180; break;
                case 6: $angle = -90; break;
                case 8: $angle = 90; break;
            }

            if (isset($angle)) {
                $this->rotateImage($filepath, $angle);
            }
        }
    }

    protected function upcountName($name)
    {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcountNameCallback'),
            $name,
            1
        );
    }

    protected function upcountNameCallback($matches)
    {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';

        return ' ('.$index.')'.$ext;
    }

    protected function getConfigBytes($val)
    {
        $val  = trim($val);
        $last = strtolower($val[strlen($val)-1]);

        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $this->fixIntOverflow($val);
    }

    protected function fixIntOverflow($size)
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }

        return $size;
    }
}
