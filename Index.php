<?php
/**
 * @throws Exception
 */
function getFilesDownloads($path = '/home/chudishe/Downloads')
{
    if (!is_dir($path)) {
        throw new Exception("The passed path $path is not valid");
    }

    function searchFiles($directory)
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
                    // Рекурсивно ищем файлы в поддиректории
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

    return searchFiles($path);
}

function sortFiles($files, $archivePath)
{
    foreach ($files as $file) {

        $pathInfo = pathinfo($file['name'], PATHINFO_EXTENSION);
        $pathToFolder = '/home/chudishe/Downloads/' . $pathInfo;

        if (!is_dir($pathToFolder)) {
            mkdir($pathToFolder, 0755);
        }
        $newFilePath = $pathToFolder . DIRECTORY_SEPARATOR . $file['name'];
        //проверка на то есть ли файл уже в архиве
        if (strpos($file['path'], $archivePath) === false) {
            if (!file_exists($newFilePath)) {
                rename($file['path'], $newFilePath);
                echo 'File ' . $file['name'] . ' moved to ' . $pathToFolder . PHP_EOL;
            } else {
                echo 'File ' . $file['name'] . ' already exists in ' . $pathToFolder . PHP_EOL;
            }
        } else {
            echo 'File ' . $file['name'] . ' is already in ArchiveOldFiles, skipping.' . PHP_EOL;
        }
    }
}

function archiveOldFiles($files)
{
    $archivePath = '/home/chudishe/Downloads/ArchiveOldFiles';

    if (!is_dir($archivePath)) {
        mkdir($archivePath, 0755);
    }

    $sixMonths = 6;
    $zipFileName = $archivePath . DIRECTORY_SEPARATOR . 'archive.zip';
    $zip = new ZipArchive();

    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo 'Failed to open zip archive';
        return;
    }

    foreach ($files as $file) {
        $fileChangeDate = strtotime($file['changeDate']);
        $sixMonthsAgo = strtotime("-$sixMonths months");

        if ($fileChangeDate < $sixMonthsAgo) {
            $newFilePathForArchive = $archivePath . DIRECTORY_SEPARATOR . $file['name'];

            if (!file_exists($newFilePathForArchive)) {
                rename($file['path'], $newFilePathForArchive);
                echo 'File ' . $file['name'] . ' archived to ' . $archivePath;

                $zip->addFile($newFilePathForArchive, $file['name']);

                unlink($newFilePathForArchive);

                echo 'File ' . $file['name'] . ' added to zip archive in ' . $archivePath . PHP_EOL;
            } else {
                echo 'File ' . $file['name'] . ' already exists in the archive.' . PHP_EOL;
            }
        }
    }

    $zip->close();
}

try {
    $filesInDownloads = getFilesDownloads();
    print_r($filesInDownloads);
    archiveOldFiles($filesInDownloads);
    sortFiles($filesInDownloads, '/home/chudishe/Downloads/ArchiveOldFiles'); // передаем $archivePath
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
