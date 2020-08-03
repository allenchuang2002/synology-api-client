<?php

namespace Synology\Api\Client;

/**
 * Class Client.
 */
class FileStationClient extends Client
{

    const API_SERVICE_NAME = 'FileStation';

    const API_NAMESPACE = 'SYNO';

    /**
     * Info API setup
     *
     * @param string $address
     * @param int $port
     * @param string $protocol
     * @param int $version
     * @param boolean $verifySSL
     */
    public function __construct(
        $address,
        $port = null,
        $protocol = null,
        $version = 1,
        $verifySSL = false
    ) {
        parent::__construct(
            self::API_SERVICE_NAME,
            self::API_NAMESPACE,
            $address,
            $port,
            $protocol,
            $version,
            $verifySSL
        );
    }

    /**
     * Return Information about VideoStation
     * - is_manager
     * - version
     * - version_string
     * @throws SynologyException
     */
    public function getInfo()
    {
        return $this->request(self::API_SERVICE_NAME, 'Info', 'FileStation/info.cgi', 'getinfo');
    }

    /**
     * Get Available Shares
     *
     * @param bool $onlywritable
     * @param int $limit
     * @param int $offset
     * @param string $sortby
     * @param string $sortdirection
     * @param bool $additional
     * @return array
     * @throws SynologyException
     */
    public function getShares(
        $onlywritable = false,
        $limit = 25,
        $offset = 0,
        $sortby = 'name',
        $sortdirection = 'asc',
        $additional = false
    ) {
        return $this->request(
            self::API_SERVICE_NAME,
            'List',
            'entry.cgi',
            'list_share',
            [
                'onlywritable' => $onlywritable,
                'limit' => $limit,
                'offset' => $offset,
                'sort_by' => $sortby,
                'sort_direction' => $sortdirection,
                'additional' => $additional ? 'real_path,owner,time,perm,volume_status' : '',
            ]
        );
    }

    /**
     * Get info about an object
     *
     * @param string $type (List|Sharing)
     * @param string $id
     * @return array
     * @throws SynologyException
     */
    public function getObjectInfo($type, $id)
    {
        switch ($type) {
            case 'List':
                $path = 'entry.cgi';
                break;
            case 'Sharing':
                $path = 'FileStation/file_sharing.cgi';
                break;
            default:
                throw new SynologyException('Unknow "'.$type.'" object');
        }

        return $this->request(self::API_SERVICE_NAME, $type, $path, 'getinfo', ['id' => $id]);
    }

    /**
     * Get a list of files/directories in a given path
     *
     * @param string $path like '/home'
     * @param int $limit
     * @param int $offset
     * @param string $sortby (name|size|user|group|mtime|atime|ctime|crtime|posix|type)
     * @param string $sortdirection
     * @param string $pattern
     * @param string $filetype (all|file|dir)
     * @param bool $additional
     * @return mixed
     * @throws SynologyException
     */
    public function getList(
        $path = '/home',
        $limit = 25,
        $offset = 0,
        $sortby = 'name',
        $sortdirection = 'asc',
        $pattern = '',
        $filetype = 'all',
        $additional = 1
    ) {
        return $this->request(
            self::API_SERVICE_NAME,
            'List',
            'entry.cgi',
            'list',
            [
                'folder_path' => $path,
                'limit' => $limit,
                'offset' => $offset,
                'sort_by' => $sortby,
                'sort_direction' => $sortdirection,
                'pattern' => $pattern,
                'filetype' => $filetype,
                'additional' => $additional ? 'real_path,size,owner,time,perm' : '',
                //'additional' => $additional ? 'real_path,time' : ''
            ],1
        );
    }

    /**
     * Upload file to given path
     *
     * @param $file
     * @param $filename
     * @param $remoteDir where to upload
     * @return mixed
     * @throws SynologyException
     */
    public function uploadFile($file, $filename,$remoteDir)
    {
        return $this->request(
            self::API_SERVICE_NAME,
            'Upload',
            'entry.cgi',
            'upload',
            [
                'path' => $remoteDir,
                'overwrite' => 'true',
                'create_parents' => 'true',
                'filename' => $filename,
            ],
            2,
            'post',
            $file
        );
    }

    /**
     * Search for files/directories in a given path
     *
     * @param string $pattern
     * @param string $path like '/home'
     * @param int $limit
     * @param int $offset
     * @param string $sortby (name|size|user|group|mtime|atime|ctime|crtime|posix|type)
     * @param string $sortdirection (asc|desc)
     * @param string $filetype (all|file|dir)
     * @param bool $additional
     * @return array
     * @throws SynologyException
     */
    public function search(
        $pattern,
        $path = '/home',
        $limit = 25,
        $offset = 0,
        $sortby = 'name',
        $sortdirection = 'asc',
        $filetype = 'all',
        $additional = false
    ) {
        return $this->request(
            self::API_SERVICE_NAME,
            'List',
            'entry.cgi',
            'list',
            [
                'folder_path' => $path,
                'limit' => $limit,
                'offset' => $offset,
                'sort_by' => $sortby,
                'sort_direction' => $sortdirection,
                'pattern' => $pattern,
                'filetype' => $filetype,
                'additional' => $additional ? 'real_path,size,owner,time,perm' : '',
                //'additional' => $additional ? 'time,perm' : '',
            ],1
        );
    }

    /**
     * Download a file
     *
     * @param string $path (comma separated)
     * @param string $mode
     * @return array
     * @throws SynologyException
     */
    public function download($path, $mode = 'open')
    {
        return $this->request(
            self::API_SERVICE_NAME,
            'Download',
            'entry.cgi',
            'download',
            [
                'path' => $path,
                'mode' => $mode,
            ],
            2
        );
    }


    /**
     * Delete file from a given path
     * modify by Allen.Chuang 2020.7.23
     * for safety change the API do not recursive delete sub-folders and files.
     * 
     * @param string $path like '/home'
     * @return mixed
     * @throws SynologyException
     */
    public function delete($path) {
        return $this->request(
            self::API_SERVICE_NAME,
            'Delete',
            'entry.cgi',
            'delete',
            ['path' => $path,
            'recursive'=>false],
            1
        );
    }
    /** Create Folder
     * $synfile->createFolder('/iCatch_Drive','createByBDCRM');
     */
    
    public function createFolder($folder_path, $name, $force_parent = false, $additional = false)
    {
        return $this->request(
            self::API_SERVICE_NAME,
            'CreateFolder', 
            'entry.cgi', 
            'create', 
            ['folder_path'  => $folder_path,
            'name'         => $name,
            'force_parent' => $force_parent,
            'additional'   => $additional ? 'real_path,size,owner,time,perm' : ''],
            1
        );
    }
}
