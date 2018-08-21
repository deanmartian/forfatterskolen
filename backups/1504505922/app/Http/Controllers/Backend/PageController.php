<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\CoursesTaken;
use App\WorkshopsTaken;
use App\Manuscript;
use App\ShopManuscriptsTaken;
use Artisan;
require '../app/Http/BackupDB/MySQLDump.php';

class PageController extends Controller
{
   
    public function dashboard()
    {
        $pending_courses = CoursesTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $pending_shop_manuscripts = ShopManuscriptsTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $pending_workshops = WorkshopsTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $assigned_course_manuscripts = Manuscript::where('feedback_user_id', Auth::user()->id)->get();
        $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)->get();
        return view('backend.dashboard', compact('pending_courses', 'pending_shop_manuscripts', 'pending_workshops', 'assigned_course_manuscripts', 'assigned_shop_manuscripts'));
    }





    public function calendar()
    {
        $event_1 = [
            'id' => 1,
            'title' => 'Event 1',
            'url' => 'http://example.coms',
            'class' => 'event-important',
            'start' => '1494259200000',
            'end' => '1494518400000',
        ];
        $event_2 = [
            'id' => 2,
            'title' => 'Event 2',
            'url' => 'http://example.coms',
            'class' => 'event-success',
            'start' => '1494259200000',
            'end' => '1494518400000',
        ];
        $events = [];
        $events[] = $event_1;
        $events[] = $event_2;

        return view('backend.calendar', compact('events'));
    }



    public function backup()
    {


        $time = time();
        $backupDir = '../backups/'.$time;

        if( !file_exists($backupDir) ) :
            mkdir($backupDir);
        endif;

        $dump = new \MySQLDump(new \mysqli('forfatterskolen3.mysql.domeneshop.no', 'forfatterskolen3', '2KJM8yuQoWL7Zkg', 'forfatterskolen3'));
        //$dump = new \MySQLDump(new \mysqli('localhost', 'root', 'root', 'forfatterskolen_laravel'));

        $dump->save($backupDir.'/'.$time.'.sql');

        $folders = ['app', 'config', 'public', 'resources', 'routes', 'storage'];
        foreach( $folders as $folder ) :
            $destination = $backupDir.'/'.$folder;
            if( file_exists('../'.$folder) ) :
                $this->xcopy('../'.$folder, $destination);
            endif;
        endforeach;

        $files = ['composer.json', 'package.json'];
        foreach( $files as $file ) :
            $destination = $backupDir;
            if( file_exists('../'.$file) ) :
                copy('../'.$file, $destination.'/'.basename($file));
            endif;
        endforeach;
        //$this->Zip($backupDir, $backupDir.'.zip');
        //$this->deleteDirectory($backupDir);

        /*try{
            $directory = '../backups/'.time();
            $dbBackupObj = new \DbBackup($config);
            $dbBackupObj->setBackupDirectory($directory); //CustomFolderName
            $dbBackupObj->setDumpType(0);
            $dbBackupObj->executeBackup();//Start the actual backup process using the user specified settings and options

            $folders = ['app', 'config', 'public', 'resources', 'routes', 'storage'];
            foreach( $folders as $folder ) :
                $destination = $directory.'/'.$folder;
                if( file_exists('../'.$folder) ) :
                    $this->xcopy('../'.$folder, $destination);
                endif;
            endforeach;

            $files = ['composer.json', 'package.json'];
            foreach( $files as $file ) :
                $destination = $directory;
                if( file_exists('../'.$file) ) :
                    copy('../'.$file, $destination.'/'.basename($file));
                endif;
            endforeach;
            $this->Zip($directory, $directory.'.zip');
            $this->deleteDirectory($directory);
        }catch(Exception $e){
                echo $e->getMessage();
        }*/
    }




    public function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();
        return true;
    }




    public function Zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                else if (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }



    public function deleteDirectory($dir)
    {
        if (is_link($dir)) {
            unlink($dir);
        } elseif (!file_exists($dir)) {
            return;
        } elseif (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->deleteDirectory("$dir/$file");
                }
            }
            rmdir($dir);
        } elseif (is_file($dir)) {
            unlink($dir);
        }
    }
    
}
