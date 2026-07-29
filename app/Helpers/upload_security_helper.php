<?php

if (!function_exists('is_safe_upload')) {
    /**
     * Secures file uploads against various attacks including double-extensions,
     * executable files, disguised scripts, and invalid MIME types.
     *
     * @param \CodeIgniter\HTTP\Files\UploadedFile|null $file
     * @return bool True if safe, false if potentially dangerous.
     */
    function is_safe_upload($file)
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return true; // Return true if no file uploaded, handle requiredness in validation rules
        }

        $filename = $file->getName();
        $ext = strtolower($file->getExtension());
        $mime = $file->getMimeType();

        // 1. Blacklist dangerous extensions anywhere in the filename
        // This prevents double extensions like "invoice.pdf.exe" or "script.php.jpg"
        $dangerous_extensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
            'exe', 'bat', 'cmd', 'sh', 'bash', 'csh', 'tcsh',
            'js', 'vbs', 'scr', 'vbe', 'wsf', 'wsh', 'hta', 'htp',
            'apk', 'msi', 'bin', 'com',
            'docm', 'xlsm', 'pptm', 'dotm', // Macro-enabled documents
            'py', 'pyc', 'pl', 'rb', 'jar', 'app', 'so', 'dll',
            'zip', 'rar', '7z', 'tar', 'gz', 'bz2', // Reject compressed files as requested
            'env', 'htaccess', 'ini', 'config', 'log' // Config and hidden files
        ];

        // Explode the filename by dot and check all parts
        $parts = explode('.', strtolower($filename));
        array_shift($parts); // Remove base name

        foreach ($parts as $part) {
            if (in_array($part, $dangerous_extensions)) {
                return false;
            }
        }

        // 2. Validate MIME type strictly based on safe extensions
        $safe_mime_map = [
            'jpg'  => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png'  => ['image/png'],
            'gif'  => ['image/gif'],
            'bmp'  => ['image/bmp', 'image/x-windows-bmp'],
            'webp' => ['image/webp'],
            'pdf'  => ['application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'text/x-pdf'],
            'doc'  => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls'  => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'ppt'  => ['application/vnd.ms-powerpoint'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'txt'  => ['text/plain'],
            'csv'  => ['text/csv', 'text/plain', 'application/csv']
        ];

        if (!array_key_exists($ext, $safe_mime_map)) {
            // Extension is not in our safe list
            return false;
        }

        if (!in_array($mime, $safe_mime_map[$ext])) {
            // Mime type doesn't match the expected types for this extension
            return false;
        }

        // 3. Image specific checks (to detect bad headers)
        $is_image = strpos($mime, 'image/') === 0;
        if ($is_image) {
            $tempPath = $file->getTempName();
            if (file_exists($tempPath)) {
                $imageInfo = @getimagesize($tempPath);
                if ($imageInfo === false) {
                    return false;
                }
            }
        }

        return true;
    }
}
