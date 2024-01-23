<?php
/**
 * @throws Exception
 */
function getFilesDownloads($path = 'Downloads'): array
{
    $downloadsPath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . $path;
    if (!is_dir($downloadsPath)) {
        throw new Exception("The passed path $path is not valid");
    }

    return searchFiles($downloadsPath);
}

/**
 * @throws Exception
 */
function searchFiles($directory): array
{
    $files = [];

    $open = opendir($directory);
    if (!$open) {
        throw new Exception("Can't open dir: $directory");
    }

    while (($fileName = readdir($open)) !== false) {
        if ($fileName !== '.' && $fileName !== '..') {
            $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

            if (is_dir($filePath)) {
                $files = array_merge($files, searchFiles($filePath));
            } else {
                $changeDateFormat = date('Y-m-d H:i:s', filemtime($filePath));
                $files[] = [
                    'name' => $fileName,
                    'path' => $filePath,
                    'changeDate' => $changeDateFormat,
                ];
            }
        }
    }
    closedir($open);

    return $files;
}

function sortFiles($files, $archivePath): void
{
    foreach ($files as $file) {
        $downloadsDirectory = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . 'Downloads';

        $pathInfo = pathinfo($file['name'], PATHINFO_EXTENSION);
        $pathToFolder = $downloadsDirectory . DIRECTORY_SEPARATOR . $pathInfo;

        if (!is_dir($pathToFolder)) {
            mkdir($pathToFolder, 0755, true);
        }

        $newFilePath = $pathToFolder . DIRECTORY_SEPARATOR . $file['name'];

        if (!strpos($file['path'], $archivePath)) {
            if (!file_exists($newFilePath)) {
                rename($file['path'], $newFilePath);
            }
        }
    }
}


function archiveOldFiles($files): void
{
    $archivePath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . 'Downloads/ArchiveOldFiles';

    if (!is_dir($archivePath)) {
        mkdir($archivePath, 0755);
    }

    $sixMonths = 6;
    $zip = new ZipArchive();

    foreach ($files as $file) {
        $fileChangeDate = strtotime($file['changeDate']);
        $sixMonthsAgo = strtotime("-$sixMonths months");

        if ($fileChangeDate < $sixMonthsAgo) {
            $newFilePathForArchive = $archivePath . DIRECTORY_SEPARATOR . $file['name'];

            if (!file_exists($newFilePathForArchive)) {
                rename($file['path'], $newFilePathForArchive);

                $zipFileName = $archivePath . DIRECTORY_SEPARATOR . 'archive.zip';
                $zip->open($zipFileName, ZipArchive::CREATE);
                $zip->addFile($newFilePathForArchive, $file['name']);
                $zip->close();

                unlink($newFilePathForArchive);
            }
        }
    }
}

try {
    $downloadsPath = $_SERVER['HOME'] . DIRECTORY_SEPARATOR . 'Downloads';
    $filesInDownloads = getFilesDownloads();
    archiveOldFiles($filesInDownloads);
    sortFiles($filesInDownloads, $downloadsPath . DIRECTORY_SEPARATOR . 'ArchiveOldFiles');
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
