<?php

/**
* file control
*/

class Config
{
    private $pathConfig = NULL;
    private $listValue;
    private $listKey;

	function __construct($parhCfg)
	{
        $this->pathConfig = $parhCfg;

        if (is_readable ($this->pathConfig))
        {
            $this->readFile();
        }
        else
        {
            $this->create();
        }
    }

    private function create ()
    {
        $cfg = fopen($this->pathConfig, 'r');
        fclose($cfg);
    }

    private function readFile ()
    {
        $cfgFile = fopen($this->pathConfig, 'r');
        $cfg = fread($cfgFile, 4096);
        $cfgLine = explode("\n", $cfg);
        $cfgData = array();
        foreach ($cfgLine as $item)
        {
            if ($item != ""){
                $tempItem = explode(" = ", $item);
                if (1 == count ($tempItem) >= 3)
                {
                    throw new Exception('no correct config file!');
                }

                $cfgData = array_merge($cfgData, $tempItem);
            }
        }

        for ($i=0; $i < count($cfgData); $i++)
        {
            $isKey = $i % 2 === 0;

            if ($isKey)
            {
                $this->listKey[] = $cfgData[$i];
            }
            else
            {
                $this->listValue[] = substr($cfgData[$i], 0, iconv_strlen($cfgData[$i])-1);
            }
        }

        fclose($cfgFile);
    }

    public function get ($key)
    {
        for ($i=0; $i < count($this->listKey); $i++)
        {
            if ($key == $this->listKey[$i])
            {
                $response = explode(", ", $this->listValue[$i] );
                if (count($response) > 1)
                {
                	return $response;
                }
                return $response[0];
            }
        }
        return false;
    }

	public function getKeyList ()
	{
		return $this->listKey;
	}

    /*public function set ()
    {

    }

    public function save ()
    {

    }*/
}

class App
{
	private  $pathToAppFolder = '././app';
	public $app;
	public $appPath;
	public $files;
	function __construct($appName)
	{
		$this->app = $appName;
		$this->appPath = $this->pathToAppFolder . "/" . $appName;
		$this->appPath = realpath ($this->appPath);
		$this->fillingVersionList ();
	}

	public function fillingVersionList()
	{
		$versionListTemp = scandir($this->appPath);
		$versionListTemp = array_values($versionListTemp);

		for ($i=0; $i < count($versionListTemp); $i++)
		{
			if (!is_dir($this->appPath . '/' . $this->versionListTemp[$i]))
			{
				unset ($versionListTemp[$i]);
			}
			else
			{
				if (!$this->checkVersionName($versionListTemp[$i]))
				{
					unset ($versionListTemp[$i]);
				}
			}
		}
		$versionListTemp = array_values($versionListTemp);
		return $versionListTemp;
	}


    public function checkVersionName ($name)
	{
		$versionPath = $this->appPath . "\\" . $name;

		if (is_file($versionPath))
		{
			return "no_cor_path";
		}

		$configVersionPath = $versionPath . "\\data version.ini";
		if (!is_readable ($configVersionPath))
		{
			return false;
		}
		$config = new Config ($configVersionPath);

		$dataVerType = $config->get ("Version_Type");
		$dataVerNum = $config->get ("Version_Number");
		$dataStartFile = $config->get ("Start_File");

		if (  $dataVerType == "pre-alpha"
			||$dataVerType == "alpha"
			||$dataVerType == "beta"
			||$dataVerType == "release")
		{
			if ($dataVerNum != 0)
			{
				$exeVersionPath = $versionPath ."\\". $dataStartFile;
				return file_exists($exeVersionPath);
			}
		}
		return false;
	}

	public function sortVersion($verList)
	{
		$verPreAlpha = array();
		$verAlpha = array();
		$verBeta = array();
		$verRelease = array();

		for ($i=0; $i < count($verList); $i++)
		{

			$type = explode("_", $verList[$i])[0];
			switch ($type)
			{
				case "pre-alpha":
					$verPreAlpha [] = $verList[$i];
					break;
				case 'alpha':
					$verAlpha [] = $verList[$i];
					break;
				case 'beta':
					$verBeta [] = $verList[$i];
					break;
				case 'release':
					$verRelease [] = $verList[$i];
					break;
				default:
					# code...
					break;
			}
		}

		$retList = array();
		if (count($verPreAlpha) != 0) $retList =array_merge($retList, $verPreAlpha);
		if (count($verAlpha) != 0)    $retList =array_merge($retList, $verAlpha);
		if (count($verBeta) != 0)     $retList =array_merge($retList, $verBeta);
		if (count($verRelease) != 0)  $retList =array_merge($retList, $verRelease);
		$retList = array_values($retList);

		return $retList;
	}

	public function getActualVersion()
	{
		$verList = $this->fillingVersionList();
		$ver = $verList;
		$verList = $this->sortVersion($ver);
		$verList = array_values($verList);
		$ret = array_pop ($verList);
		return $ret;
	}

	public function getExeFile($verName)
	{
		if (!$this->checkVersionName($verName))
		{
			return false;
		}

		$versionPath = $this->appPath . "/" . $verName;
		$configVersionPath = $versionPath . "/data version.ini";
		if (!is_readable ($configVersionPath))
		{
			return false;
		}
		$configFile = fopen($configVersionPath, 'r');
		$config = fread($configFile,  4096);
		$datatemp = explode (" ", $config);
		return $datatemp[2];
	}

	public function fillingFileList($dir)
	{
		$tempDir = scandir($dir);
		unset ($tempDir[0]);
		unset ($tempDir[1]);
		$tempDir = array_values($tempDir);
		$dirList = array();
	    global $filesTemp;
		foreach ($tempDir as $temp) {
			$pathTemp = $dir . "/" . $temp;
		 	if (is_dir($pathTemp))
		 	{
		 		array_push($dirList ,$temp);
		 	}
		 	else
		 	{
		 		$filesTemp[] = $pathTemp;
			}
		}

		if ($filesTemp != NULL)
		{
			if ($this->files == NULL)
			{
				$this->files = array();
			}
			$filesTemp = array_values($filesTemp);
			$this->files = array_merge($this->files, $filesTemp);

		}
		$this->files = array_values($this->files);
		foreach ($dirList as $temp) {
			$path = $dir . "/" . $temp;
			$this->fillingFileList($path);
		}

	}

	public function getFileList($verName)
	{

		$path = $this->appPath . '/' . $verName;
		$this->fillingFileList ($path);
		$response = NULL;
		$tempResponse = NULL;
		$tempStr = array_unique ($this->files);

		$remInd = strlen ($path) + 1;
		//var_dump($tempSleh);
		foreach ($tempStr as $temp) {
			$tempResponse[] = substr($temp, $remInd);
		}
		return $tempResponse;
	}
	public function getSizeVersion($verName)
	{
		$this->getFileList($verName);
		$listFile = $this->files;
		$size = NULL;
		foreach ($listFile as $item) {
			$path = $item;
			$temp = filesize ($path);
			$size = $size + $temp;
		}
		$response = $size;
		return $response;
	}
	public function getFileSize($verName,$fileName)
	{
		$path = $this->appPath;
		if ($fileName[0] != "/")
		{
			$fileName = "/" . $fileName;
		}
		$path = $path . "/" . $verName . $fileName;
		//var_dump($path);
		return filesize ($path);
	}

	public function getInfoVersion($verName)
	{
		$response = NULL;
        $pathToConfig = $this->appPath . "/" . $verName . "/data version.ini";
        $cfg = new Config ($pathToConfig);
		$key = $cfg->getKeyList();
		foreach ($key as $item) {
			$response[] = array($item => $cfg->get($item));
		}

		return $response;
	}

	public function getInfoApp()
	{
		$response = NULL;
		$pathToConfig = $this->appPath . "/" . "app.ini";
		$cfg = new Config ($pathToConfig);
		$key = $cfg->getKeyList();
		foreach ($key as $item) {
			$response[] = array($item => $cfg->get($item));
		}

		return $response;
	}
}

$token = $_GET["token"];
$get = $method[1];
$appName = $_GET["app"];
$versionName = $_GET["ver"];
$app = choiceApp($appName);
$fileName = $_GET["file"];

function choiceApp($appName)
{
	switch ($appName) {
		case 'Electrical_Simulator':
			return new App("Electrical_Simulator");
			break;
		case 'Launcher':
			return new App("Launcher");
			break;
		default:
			return false;
			break;
	}
}

switch ($get) {
	case 'actualVersion':
		echo json_encode(get_actual_version($app));
		break;
	case 'versionList':
		echo json_encode(get_version_list($app), JSON_UNESCAPED_SLASHES);
		break;
	case 'applicationList':
		echo json_encode(get_application_list(), JSON_UNESCAPED_SLASHES);	
		break;
	case 'checkVersion':
		echo json_encode(get_check_version($app, $versionName), JSON_UNESCAPED_SLASHES);
		break;
	case 'exeFile':
		echo json_encode(get_exe_file($app, $versionName), JSON_UNESCAPED_SLASHES);
		break;
	case 'fileList':
		echo json_encode(get_file_list($app, $versionName), JSON_UNESCAPED_SLASHES);
		break;
	case 'sizeVersion':
		echo json_encode(get_size_version($app, $versionName), JSON_UNESCAPED_SLASHES);
		break;
	case 'fileSize':
		echo json_encode(get_file_size($app, $versionName, $fileName), JSON_UNESCAPED_SLASHES);
		break;
	case 'versionInfo':
		echo json_encode(get_info_version($app, $versionName), JSON_UNESCAPED_SLASHES);
		break;
	case 'applicationInfo':
		echo json_encode(get_info_application($app), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		break;
	default:
		echo json_encode(errorComand(), JSON_UNESCAPED_SLASHES);
		break;
}

function errorComand()
{
	return "error";
}

function get_actual_version ($app)
{
	$ver = $app->getActualVersion();
	$response = array("response" => array('actualVersion' => $ver));
	return $response;
}

function get_version_list($app)
{
	$sortList = $app->sortVersion($app->fillingVersionList());
	$response = array("response" => array('versionList' => $sortList));
	return $response;

}

function get_file_list($app, $versionName)
{
	$files = $app->getFileList($versionName);
	$response = array("response" => array('filesList' => $files));
	return $response;
}

function get_check_version($app, $versionName)
{
	$ver = $app->checkVersionName($versionName);
	$response = array("response" => array('version' => $ver));
	return $response;
}

function get_exe_file($app, $versionName)
{
	$exe = $app->getExeFile($versionName);
	$response = array("response" => array('exeFile' => $exe));
	return $response;
}

function get_size_version($app, $versionName)
{
	$size = $app->getSizeVersion($versionName);
	$response = array("response" => array('versionSize' => $size));
	return $response;
}

function get_file_size($app, $versionName, $fileName)
{
	$size = $app->getFileSize($versionName, $fileName);
	$response = array("response" => array('fileSize' => $size));
	return $response;
}

function get_info_version($app, $versionName)
{
	$info = $app->getInfoVersion($versionName);
	$response = array("response" => array('info' => $info));
	return $response;
}

function get_info_application($app)
{
	$info = $app->getInfoApp();
	$response = array("response" => array('info' => $info));
	return $response;
}

function get_application_list()
{
	$info = scandir('././app');
	array_shift ($info);
	array_shift ($info);
	$response = array("response" => array('applicationList' => $info));
	return $response;
}
?>