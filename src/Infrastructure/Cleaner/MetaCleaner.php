<?php

declare(strict_types=1);


namespace App\Infrastructure\Cleaner;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use App\Infrastructure\Cleaner\Exiftool;

final class MetaCleaner
{
    private const EXIFTOOL_SUPPORTED_EXTENSIONS = [
        'pdf',
        'dcp',
        'exv',
        'gif',
        'hdp',
        'wdp',
        'jxr',
        'jp2',
        'icc',
        'icm',
        'jpf',
        'j2k',
        'jpm',
        'jpx',
        'jpeg',
        'jpg',
        'jpe',
        'mie',
        'mos',
        'mpo',
        'png',
        'jng',
        'mng',
        'psd',
        'psb',
        'thm',
        'tiff',
        'tif',
        'vrd',
        'dng',
        'arw',
        'cr2',
        'dng',
        'fff',
        'cs1',
        'erf',
        'iiq',
        'mef',
        'mrw',
        'nef',
        'nrw',
        'orf',
        'pef',
        'raf',
        'raw',
        'rw2',
        'rwl',
        'sr2',
        'srw',
        'x3f',
        '3g2',
        '3gp2',
        '3gp',
        '3gpp',
        'dvb',
        'f4a',
        'f4b',
        'f4p',
        'f4v',
        'm4a',
        'm4b',
        'm4p',
        'm4v',
        'mov',
        'qt',
        'mp4',
        'mqv',
        'qtif',
        'qti',
        'qif',
    ];
    private const EXIFTOOL_DEFAULT_METADATA = [
        'ExifTool Version Number',
        'Exif Version',
        'Exif Byte Order',
        'Exif Image Width',
        'Image Height',
        'Image Width',
        'Image Size',
        'Exif Image Height',
        'File Name',
        'Directory',
        'File Size',
        'File Modification Date/Time',
        'File Access Date/Time',
        'File Inode Change Date/Time',
        'File Permissions',
        'File Type',
        'File Type Extension',
        'MIME Type',
        'PDF Version',
        'Linearized',
        'Page Count',
        'Encoding Process',
        'Bits Per Sample',
        'Color Type',
        'Color Components',
        'Background Color',
        'Bit Depth',
        'Compression',
        'Filter',
        'Interlace',
        'Megapixels'
    ];
    private const EXIFTOOL_DEFAULT_METADATA_API = [
        "ExifToolVersion",
        'ExifVersion',
        'ExifByteOrder',
        'ExifImageWidth',
        'ExifImageHeight',
        "FileName",
        "Directory",
        "FileSize",
        "FileModifyDate",
        "FileAccessDate",
        "FileInodeChangeDate",
        "FilePermissions",
        "FileType",
        "FileTypeExtension",
        "MIMEType",
        'PDFVersion',
        'Linearized',
        'PageCount',
        "ImageWidth",
        "ImageHeight",
        "BitDepth",
        "ColorType",
        'EncodingProcess',
        'BitsPerSample',
        "Compression",
        'ColorComponents',
        'BackgroundColor',
        "Filter",
        "Interlace",
        "ImageSize",
        "Megapixel"
    ];
    private $filesystem;
    private $logger;
    private $exiftool;

    /**
     * MetaCleaner constructor.
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     */
    public function __construct(Filesystem $filesystem, LoggerInterface $logger, Exiftool $exiftool)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->exiftool = $exiftool;
    }

    /**
     * Get temp file.
     * @param string $originalFilename
     * 
     * @return string
     */
    private function getTempFile(string $originalFilename): string
    {
        $fileExtension = 'pdf';
        $fileNameParts = explode('.', $originalFilename);

        $tempFilePath = $this->filesystem->tempnam(
            sys_get_temp_dir(),
            'metacleaner'
        );
        $this->filesystem->remove($tempFilePath);

        if ($fileNameParts) {
            $tempFileExtension = end($fileNameParts);

            if (preg_match('/^[a-zA-Z]{2,4}$/', $tempFileExtension)) {
                $fileExtension = $tempFileExtension;
            }
        }

        $finalTempFilePath = implode('.', [$tempFilePath, $fileExtension]);

        return $finalTempFilePath;
    }

    /**
     * Clean all metadata with ExifTool or Metacleaner.
     *
     * @param string $originalFilename
     * @param string $originalContent
     *
     * @return string
     */
    public function getCleanFile(string $originalFilename, string $originalContent): string
    {
        $finalTempFilePath = $this->getTempFile($originalFilename);

        $fileNameParts = explode('.', $originalFilename);
        $extension = end($fileNameParts);

        $this->filesystem->dumpFile($finalTempFilePath, $originalContent);

        $process = '';

        $checkSignature = new Process(sprintf('pdfsig -nocert "%s"', $finalTempFilePath));
        $checkSignature->run();

        // Check if file is digitally signed
        if (((int)$checkSignature->getExitCode()) !== 0) {

            if (in_array($extension, self::EXIFTOOL_SUPPORTED_EXTENSIONS)) {
                $process = new Process(sprintf('exiftool -all:all= "%s"', $finalTempFilePath));
                $process->run();

                if (((int)$process->getExitCode()) !== 0) {

                    $this->logger->error('Cleaner error', [
                        'cleaner' => 'exiftool',
                        'output' => $process->getOutput(),
                        'error' => $process->getErrorOutput(),
                    ]);
                }
            } else {
                $process = new Process(sprintf('java -jar /metacleaner/metacleaner.jar %s', $finalTempFilePath));
                $process->run();

                if (((int)$process->getExitCode()) !== 0) {

                    $this->logger->error('Cleaner error', [
                        'cleaner' => 'metacleaner',
                        'output' => $process->getOutput(),
                        'error' => $process->getErrorOutput(),
                    ]);
                }
            }

            $this->logger->info('File completed', [
                'file_name' => $originalFilename,
                'output' => $process->getOutput(),
            ]);

        } else {
            $this->logger->info('File is digitally signed', [
                'file_name' => $originalFilename,
                'output' => $process
            ]);
        }

        $finalContent = file_get_contents($finalTempFilePath);

        $this->filesystem->remove($finalTempFilePath);

        return $finalContent;
    }

    /**
     * Get all metadata from file using ExifToll or Metacleaner.
     *
     * @param string $originalFilename
     * @param string $originalContent
     *
     * @return array
     */
    public function getAnalytics(string $originalFilename, string $originalContent): array
    {
        $finalTempFilePath = $this->getTempFile($originalFilename);

        $fileNameParts = explode('.', $originalFilename);
        $extension = end($fileNameParts);

        $this->filesystem->dumpFile($finalTempFilePath, $originalContent);

        $process = '';
        $result = [];

        $checkSignature = new Process(sprintf('pdfsig -nocert "%s"', $finalTempFilePath));
        $checkSignature->run();

        // Check if file is digitally signed
        if (((int)$checkSignature->getExitCode()) !== 0) {

            if (in_array($extension, self::EXIFTOOL_SUPPORTED_EXTENSIONS)) {

                $process = new Process(sprintf('exiftool "%s"', $finalTempFilePath));
                $process->run();

                $output = $process->getOutput();
                preg_match_all('#^(([^\s:]|\s{1}(?!\s{2,}))+)\s+:\s+(.+)#m', $output, $matches);

                $sProcess = new Process(sprintf('exiftool -s "%s"', $finalTempFilePath));
                $sProcess->run();

                $sOutput = $sProcess->getOutput();
                preg_match_all('#^(([^\s:]|\s{1}(?!\s{2,}))+)\s+:\s+(.+)#m', $sOutput, $apiMatches);

                $allMetadata = array_combine($matches[1], $matches[3]);
                $apiMetadata = [];
                foreach($apiMatches[1] as $index => $key){
                    $locked = in_array($key, $this->exiftool->EXIFTOOL_WRITABLE_TAGS);
                    $apiMetadata[$matches[1][$index]] = [$key, $locked];
                }
                
                $resMetadata = array_merge_recursive($allMetadata,$apiMetadata);

                for ($i = 0; $i < count(self::EXIFTOOL_DEFAULT_METADATA); ++$i) {
                    unset($resMetadata[self::EXIFTOOL_DEFAULT_METADATA[$i]]);
                }
                $result = $resMetadata;

            } else {
                $process = new Process(sprintf('java -jar /metacleaner/metacleaner.jar %s', $finalTempFilePath));
                $process->run();
                $useMetacleaner = ((int)$process->getExitCode()) !== 0;

                if (!$useMetacleaner) {
                    $result = json_decode($process->getOutput(), true);
                } else {
                    $this->logger->error('Error while analyzing file', [
                        'file_name' => $originalFilename,
                        'output' => $process->getOutput(),
                        'error' => $process->getErrorOutput(),
                    ]);
                }
            }

            $this->logger->info('File analyzed', [
                'file_name' => $originalFilename,
                'output' => $process->getOutput()
            ]);

        } else {
            $this->logger->info('File is digitally signed', [
                'file_name' => $originalFilename,
                'output' => $process
            ]);
        }

        $this->filesystem->remove($finalTempFilePath);

        return $result;
    }

    /**
     * Update metadata with ExifTool or Metacleaner.
     *
     * @param string $originalFilename
     * @param string $originalContent
     * @param array $metadata
     *
     * @return string
     */
    public function getUpdatedFile(string $originalFilename, string $originalContent, array $metadata): string
    {
        $finalTempFilePath = $this->getTempFile($originalFilename);

        $fileNameParts = explode('.', $originalFilename);
        $extension = end($fileNameParts);

        $this->filesystem->dumpFile($finalTempFilePath, $originalContent);

        $updateProcess = '';

        $checkSignature = new Process(sprintf('pdfsig -nocert "%s"', $finalTempFilePath));
        $checkSignature->run();

        // Check if file is digitally signed
        if (((int)$checkSignature->getExitCode()) !== 0) {

            if (in_array($extension, self::EXIFTOOL_SUPPORTED_EXTENSIONS)) {
                
                $getProcess = new Process(sprintf('exiftool -s "%s"', $finalTempFilePath));
                $getProcess->run();

                $output = $getProcess->getOutput();
                preg_match_all('#^(([^\s:]|\s{1}(?!\s{2,}))+)\s+:\s+(.+)#m', $output, $matches);

                if (((int)$getProcess->getExitCode()) !== 0) {

                    $this->logger->error('Cleaner error', [
                        'cleaner' => 'exiftool',
                        'output' => $getProcess->getOutput(),
                        'error' => $getProcess->getErrorOutput(),
                    ]);
                }

                $command = "exiftool $finalTempFilePath";
                foreach($metadata as $mData){
                    if($mData[1][1] == "")
                        $tagName = str_replace(" ", "", $mData[0]);
                    else
                        $tagName = str_replace(" ", "", $mData[1][1]);
                    $tagValue = $mData[1][0];
                    if(strtotime($tagValue)){
                        $tagValue = date('Y:m:d H:i:sP', strtotime($tagValue));
                    }
                    $locked = in_array($tagName, $this->exiftool->EXIFTOOL_WRITABLE_TAGS);
                    if($locked)
                        $command .= ' -'.$tagName.'="'.$tagValue.'"';
                }
                
                $updateProcess = new Process($command);
                $updateProcess->run();
                $command = "exiftool $finalTempFilePath";
                foreach($matches[1] as $key => $match){
                    $checkRemove = array_filter($metadata, function($k) use ($match){
                        return $k[1][1] == $match;
                    });
                    if(($checkRemove == null || count($checkRemove) == 0) && !in_array($match, self::EXIFTOOL_DEFAULT_METADATA_API)) {
                        // $command .= ' -'.$match.'-="'.$matches[3][$key].'"';

                        $locked = in_array($match, $this->exiftool->EXIFTOOL_WRITABLE_TAGS);
                        if($locked)
                            $command .= ' -'.$match.'=';
                    }
                }

                $updateProcess = new Process($command);
                $updateProcess->run();
                if (((int)$updateProcess->getExitCode()) !== 0) {

                    $this->logger->error('Cleaner error', [
                        'cleaner' => 'exiftool',
                        'output' => $updateProcess->getOutput(),
                        'error' => $updateProcess->getErrorOutput()
                    ]);
                }
            } else {
                
            }

            $this->logger->info('File completed', [
                'file_name' => $originalFilename,
                'output' => $updateProcess->getOutput(),
            ]);

        } else {
            $this->logger->info('File is digitally signed', [
                'file_name' => $originalFilename,
                'output' => $updateProcess
            ]);
        }

        $finalContent = file_get_contents($finalTempFilePath);

        $this->filesystem->remove($finalTempFilePath);

        return $finalContent;
    }
}