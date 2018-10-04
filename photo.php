<?php

class Photo
{

  private $requestType;

  protected $destination = 'pictures/';

  private $valid_formats = [
    'jpg', 'png', 'gif', 'bmp','jpeg','PNG','JPG','JPEG','GIF','BMP'
  ];


  public function __construct(string $type)
  {
    $this->requestType = $type;
  }

  public function run()
  {
    switch ($this->requestType) {
      case 'get':
        return $this->uploadForm();
        break;

      case 'post':
        return $this->handleUpload();
        break;
      
      default:
        return $this->displayErrors("Invalid request type: {$this->requestType}");
    }
  }

  public function uploadForm()
  {
    return "<form action=\"/photo.php\" method=\"POST\" enctype=\"multipart/form-data\"><input type=\"file\" name=\"file\"><button type=\"submit\" name=\"submit\">Submit</submit></form>";
  }

  public function handleUpload()
  {
    $validate = $this->validate();

    if (!$validate['status'])
      return $this->displayErrors($validate['message']);

    $new_name = time().$validate['message']['name'];

    if (move_uploaded_file($validate['message']['tmp'], $this->destination.$new_name))
      return "File URL: https://{$_SERVER['SERVER_NAME']}/{$this->destination}{$new_name} <a href=\"/photo.php\">Add another</a>";
    else
      return 'An Error occured';
  }

  private function validate()
  {
    $file = $_FILES['file'];
    $file_temp_name = $file['tmp_name'];
    $file_name = str_replace(' ', '_', strtolower($file['name']));
    $a = explode('.', $file['name']);
    $file_extension = strtolower(end($a));

    if (!isset($_POST['submit']))
      return ['status' => false, 'message' => 'HTTP 403 FORBIDDEN'];

    if (!in_array($file_extension, $this->valid_formats))
      return ['status' => false, 'message' => 'Invalid file type'];

    if ($file['error'] !== 0)
      return ['status' => false, 'message' => "An error occured. (Error code: {$file['error']})"];

    if ($file['size'] >= (5 * 1024 * 1024))
      return ['status' => false, 'message' => 'File is too large.'];

    return [
      'status' => true,
      'message' => [ 'extension' => $file_extension, 'tmp' => $file_temp_name, 'name' => $file_name ]
    ];
  }

  protected function displayErrors(string $message)
  {
    return "<pre>$message</pre>";
  }
}


$photo = new Photo(strtolower($_SERVER['REQUEST_METHOD']));
echo $photo->run();