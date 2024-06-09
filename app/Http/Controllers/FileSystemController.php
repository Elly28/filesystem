<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileSystemController extends Controller
{
    private $fileSystem = [];

    // Helper function to find a directory
    private function &findDirectory($path)
    {
        $parts = array_filter(explode('/', trim($path, '/')));
        $current = &$this->fileSystem;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                $null = null;
                return $null;
            }
            $current = &$current[$part];
        }

        return $current;
    }

    /*
    Function that creates the directory
    */
    public function createDirectory(Request $request)
    {
        $path = $request->input('path');

        if (!$path) {
            return response()->json(['error' => 'Path is required']);
        }

        $parts = array_filter(explode('/', trim($path, '/')));
        $current = &$this->fileSystem;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }

        return response()->json(['message' => 'Directory created']);
    }

    /*
    Function that adds the file
    */
    public function addFile(Request $request)
    {
        $path = $request->input('path');
        $fileName = $request->input('file_name');
        $content = $request->input('content', '');

        if (!$path || !$fileName) {
            return response()->json(['error' => 'Path and file_name are required']);
        }

        $directory = &$this->findDirectory($path);
        if (is_null($directory)) {
            return response()->json(['error' => 'Directory does not exist']);
        }

        if (isset($directory[$fileName])) {
            return response()->json(['error' => 'File already exists']);
        }

        $directory[$fileName] = $content;
        return response()->json(['message' => 'File created']);
    }

    /*
    Function that deletes the file
    */
    public function deleteFile(Request $request)
    {
        $path = $request->input('path');
        $fileName = $request->input('file_name');

        if (!$path || !$fileName) {
            return response()->json(['error' => 'Path and file_name are required']);
        }

        $directory = &$this->findDirectory($path);
        if (is_null($directory) || !isset($directory[$fileName])) {
            return response()->json(['error' => 'File does not exist']);//fail message to show file does not exist
        }

        unset($directory[$fileName]);
        return response()->json(['message' => 'File deleted']);//success message to show file is deleted
    }

    /*
    Function that deletes the directory
    */
    public function deleteDirectory(Request $request)
    {
        $path = $request->input('path');

        if (!$path) {
            return response()->json(['error' => 'Path is required']);
        }

        $parts = array_filter(explode('/', trim($path, '/')));
        $current = &$this->fileSystem;
        $parent = null;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return response()->json(['error' => 'Directory does not exist']);// returns error message if directory cannot be found
            }
            $parent = &$current;
            $current = &$current[$part];
        }

        unset($parent[$parts[array_key_last($parts)]]); //unset the array to free memory used
        return response()->json(['message' => 'Directory deleted']); //return message with
    }
}


