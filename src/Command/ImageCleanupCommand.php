<?php
declare(strict_types=1);

namespace App\Command;

use App\Alert\Alert;
use App\Alert\ErrorAlert;
use App\Model\Entity\Image;
use Aws\S3\S3Client;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use EmailQueue\EmailQueue;

class ImageCleanupCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/4/en/console-commands/commands.html#defining-arguments-and-options
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Deletes all orphaned images
     *
     * Orphaned images are project image files not associated with a project, which could be caused by an unsubmitted application
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $imagesTable = TableRegistry::getTableLocator()->get('Images');
        $images = $imagesTable
            ->find()
            ->select(['filename'])
            ->all()
            ->toArray();
        $goodFilenames = array_map(function (Image $image) {
            return $image->filename;
        }, $images);

        $files = scandir(Image::PROJECT_IMAGES_DIR);
        if ($files === false) {
            $io->err("Failed to read directory: " . Image::PROJECT_IMAGES_DIR);
            return;
        }

        $filesToDelete = [];
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $checkFilename = str_contains($file, Image::THUMB_PREFIX)
                ? str_replace(Image::THUMB_PREFIX, '', $file)
                : $file;
            if (!in_array($checkFilename, $goodFilenames, true)) {
                $filesToDelete[] = $file;
            }
        }

        if (empty($filesToDelete)) {
            $io->out('No files to delete.');
            return;
        }

        $io->out('Files to delete: ' . print_r($filesToDelete, true));

        foreach ($filesToDelete as $filename) {
            $filepath = Image::PROJECT_IMAGES_DIR . DS . $filename;
            if (unlink($filepath)) {
                $io->success("[+] $filename deleted");
            } else {
                $io->err("[-] Failed to delete $filename");
            }
        }
    }
}
