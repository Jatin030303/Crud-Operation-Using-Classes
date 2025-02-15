<?php
class VideoCategory
{
    public $con;
    public $file_name;
    public $file_type;
    public $file_size;
    public $file_tmp_name;
    public $file_destination;

    public function __construct($con)
    {
        $this->con = $con;
        if (isset($_POST['submit'])) {
            $this->Storedata(); // Process the form submission when the user clicks submit
        }
    }

    // Function to handle video upload and saving data to the database
    function Uploadvideo()
    {
        $sql = "INSERT INTO `video`(Video) VALUES ('$this->file_name')";
        if (mysqli_query($this->con, $sql)) {
            $success = 'Video uploaded successfully';
        } else {
            $failed = "Something went wrong: " . mysqli_error($this->con);
            echo $failed;
        }
    }

    // Function to validate, upload the file, and store data in the database
    public function Storedata()
    {
        if (isset($_FILES['file'])) {
            $this->file_name = $_FILES['file']['name'];
            $this->file_type = $_FILES['file']['type'];
            $this->file_tmp_name = $_FILES['file']['tmp_name'];
            $this->file_size = $_FILES['file']['size'];
            $this->file_destination = "../upload/" . $this->file_name;

            // Validate the file type (only allow videos, for example)
            $allowed_types = ['video/mp4', 'video/avi', 'video/mkv'];
            if (!in_array($this->file_type, $allowed_types)) {
                echo "Invalid file type. Only MP4, AVI, and MKV are allowed.";
                return;
            }

            // Validate file size (max 10MB)
            if ($this->file_size > 10 * 1024 * 1024) {
                echo "File size exceeds the maximum limit of 10MB.";
                return;
            }

            // Move the file to the destination folder
            if (move_uploaded_file($this->file_tmp_name, $this->file_destination)) {
                $this->Uploadvideo(); // Upload video info to the database
                echo "File uploaded successfully!";
            } else {
                echo "Failed to upload the file.";
            }
        } else {
            echo "No file uploaded.";
        }
    }
}
