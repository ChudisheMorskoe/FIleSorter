<?php
/**
 * @throws Exception
 */

function getFilesFromDirectory(string $path = 'Downloads'): array
{
    $downloadsPath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . $path;
    if (!is_dir($downloadsPath)) {
        throw new Exception("The passed path $path is not valid");
    }
    return retrieveFiles($downloadsPath);
}

/**
 * @throws Exception
 */
function retrieveFiles(string $directory): array
{
    $files = [];
    $months = 6;
    $open = opendir($directory);
    if (!$open) {
        throw new Exception("Can't open dir: $directory");
    }

    while (($fileName = readdir($open)) !== false) {
        if ($fileName !== '.' && $fileName !== '..') {
            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

            if (is_dir($filePath)) {
                $files = array_merge($files, retrieveFiles($filePath));
            } else {
                $lastAccessTime = fileatime($filePath);
                $monthsAgo = strtotime(" -$months  months");
                if ($lastAccessTime < $monthsAgo) {
                    $files[] = [
                        'name' => $fileName,
                        'path' => $filePath,
                        'lastAccessTime' => date('Y-m-d H:i:s', $lastAccessTime)
                    ];
                }
            }
        }
    }
    closedir($open);

    return $files;
}

function archiveOldFiles(array $files, string $archivePath, int $months = 6): void
{
    $zipFileName = $archivePath . DIRECTORY_SEPARATOR . 'archive.zip';
    $zip = new ZipArchive();
    $zip->open($zipFileName, ZipArchive::CREATE);

    foreach ($files as $file) {
        $lastAccessTime = strtotime($file['lastAccessTime']);
        $archiveThreshold = strtotime("-$months months");
        if ($lastAccessTime < $archiveThreshold) {
            if (!file_exists($archivePath . DIRECTORY_SEPARATOR . $file['name'])) {
                $zip->addFile($file['path'], $file['name']);
            }
        }
    }
    $zip->close();
    foreach ($files as $file) {
        $lastAccessTime = strtotime($file['lastAccessTime']);
        $archiveThreshold = strtotime("-$months months");
        if ($lastAccessTime < $archiveThreshold) {
            unlink($file['path']);
        }
    }
}

try {
    $downloadsPath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . 'Downloads';
    $filesInDirectory = getFilesFromDirectory();
    archiveOldFiles($filesInDirectory, $downloadsPath . DIRECTORY_SEPARATOR . 'ArchiveOldFiles');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
