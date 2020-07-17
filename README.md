Synology FileStation Client API
=================

This is a PHP Library that consume Synology FileStation APIs

* SYNO.Api :
    * connect
    * disconnect
    * getAvailableApi

* SYNO.FileStation:
    * connect
    * disconnect
    * getInfo
    * getShares
    * getObjectInfo
    * getList
    * search
    * download

Usage for FileStationClient Synology Api:
```php
$synology = new FileStationClient('192.168.10.5', 5000, 'http', 2);
$synology->activateDebug(); // debug
try {
    $synology->connect('user', 'your password','filestation');
} catch (SynologyException $e) {
    echo $e->getMessage();
}

$list = json_decode($synology->getList('/my_Drive'));
$filelist =  $list->{'data'}->{'files'};
foreach ($filelist as $obj){
    $mtime = date('Y-m-d H:i:s',$obj->{'additional'}->{'time'}->{'mtime'});

    if($obj->{'isdir'})
        echo "[D] $mtime ";
    else
        echo "[ ] $mtime ";
    echo $obj->{'name'}."\n";
}
// upload
$nas->uploadFile('./test1','text1.txt','/my_Drive');


``` 
