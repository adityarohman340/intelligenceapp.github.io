<?php
require_once 'vendor/autoload.php';
require_once "./random-string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=intelligenceapp;AccountKey=vML7N1V/1f9GKiMDaU+4/gNynXtTSg9OluoNt7CwfLkYi1JuAZ2ZBwYQJxwaAdNKxasOiadO6vMSQfbdOnsP3w==;";
$containerName = "resources-img";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="icon.ico">
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <title>IWP Upload File</title>
</head>
<body>
      <!-- Navbar -->
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" width="30" height="30" class="d-inline-block align-top" alt="">
            <span style="color: #4db6ac">Intelligence Image Analysis</span>
        </a>
    </nav>
      <div class="upload-file">
            <img src="chip.png" alt="logo" id="logo">
            <h3>Cognitive Image Analysis</h3>
            <p>Pilih gambar kemudian <span style="color: #26a69a">upload</span>, kemudian klik <span style="color: #ffca28">analisa</span> untuk mendapatkan informasi detail terkait gambar yang anda upload</p>
            <form class="form" action="index.php" method="POST" enctype="multipart/form-data">
                  <input type="file" class="btn-ch" name="fileToUpload" id="upload_file">
                  <input type="submit" class="btn-up" name="submit" id="submit" value="Upload">
            </form>
      </div>
      <table class="table table-hover table-dark">
            <thead>
                <tr>
                    <th scope="col">Nama File</th>
                    <th scope="col">URL</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
				    do {
					    foreach ($result->getBlobs() as $blob) {
						    ?>
						        <tr>
							        <td><?php echo $blob->getName() ?></td>
							        <td><?php echo $blob->getUrl() ?></td>
							        <td>
								        <form action="analisis.php" method="POST">
									        <input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									        <input type="submit" name="submit" value="Analisis" class="btn-up">
								        </form>
							        </td>
						        </tr>
						    <?php
					    }
					    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
				    } while($result->getContinuationToken());
				?>
            </tbody>
      </table>
</body>
</html>