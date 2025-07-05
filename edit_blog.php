<?php
session_start();

// Access check
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "blog_post");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate ID
if (!isset($_GET['id'])) {
    header("Location: modify-blog.php");
    exit;
}

$blog_id = intval($_GET['id']);

// Fetch blog details
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Blog not found.";
    exit;
}

$blog = $result->fetch_assoc();
$stmt->close();

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $image_url = trim($_POST['image_url']);
    $image_path = $blog['image']; // default to existing image

    // Check for new file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            $image_path = $destination;
        }
    }

    // Fallback to image URL if set
    if (empty($_FILES['image']['name']) && !empty($image_url)) {
        $image_path = $image_url;
    }

    // Update the blog
    $stmt = $conn->prepare("UPDATE blogs SET title = ?, category = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $category, $description, $image_path, $blog_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: modify-blog.php?updated=1");
    exit;
}
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Skyworld Consulting Company">
    <meta name="author" content="Skyworld Consulting Company">
    <meta name="keywords" content="Skyworld Consulting Company">
    <link rel="shortcut icon" href="assets/custom/images/shortcut.png">

    <title>SWCC</title>
    
    
    <!-- animate.css-->  
    <link href="assets/vendor/animate.css-master/animate.min.css" rel="stylesheet">
    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <!-- Font Awesome 5 -->
    <link href="assets/vendor/fontawesome/css/fontawesome-all.min.css" rel="stylesheet">
    <!-- Fables Icons -->
    <link href="assets/custom/css/fables-icons.css" rel="stylesheet"> 
    <!-- Bootstrap CSS --> 
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap-4-navbar.css" rel="stylesheet">
    <!-- portfolio filter gallery -->
    <link href="assets/vendor/portfolio-filter-gallery/portfolio-filter-gallery.css" rel="stylesheet">
    <!-- FANCY BOX -->
    <link href="assets/vendor/fancybox-master/jquery.fancybox.min.css" rel="stylesheet"> 
    <!-- RANGE SLIDER -->
    <link href="assets/vendor/range-slider/range-slider.css" rel="stylesheet">
    <!-- OWL CAROUSEL  --> 
    <link href="assets/vendor/owlcarousel/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/vendor/owlcarousel/owl.theme.default.min.css" rel="stylesheet">
    <!-- FABLES CUSTOM CSS FILE -->
    <link href="assets/custom/css/custom.css" rel="stylesheet">
    <!-- FABLES CUSTOM CSS RESPONSIVE FILE -->
    <link href="assets/custom/css/custom-responsive.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script>
    <style>
        body, html {
            height: 100%;
        }
        .full-height {
            min-height: 100vh;
        }
        .btn-back {
            background-color: teal;
            color: #fff;
        }
        .btn-update {
            background-color: #007bff;
            color: #fff;
        }
        .img-preview {
            max-width: 100%;
            max-height: 250px;
            margin-bottom: 15px;
        }
    </style>
         
</head>

<body>

<!-- Loading Screen -->
    <div id="ju-loading-screen">
      <div class="sk-double-bounce">
        <div class="sk-child sk-double-bounce1"></div>
        <div class="sk-child sk-double-bounce2"></div>
      </div>
    </div>
<!-- Start Fables Navigation -->
    <div class="fables-navigation fables-main-background-color py-3 py-lg-0">
        <div class="container">
               <div class="row py-2">
                   <div class="col-12 col-sm-12 col-md-10 col-lg-12 pr-md-0">                       
                       <nav class="navbar fables-mega-menu navbar-expand-lg navbar-dark py-lg-1">
                        <a class="navbar-brand pl-0" href="index.html"><img src="assets/custom/images/swcc-logo.png" alt="swcc-logo" class="fables-logo-mega"></a>  
                </nav>
                   </div>
                   
               </div>
        </div>
    </div>

<!-- End Fables Navigation -->

<div class="container full-height py-5">
    <h2 class="mb-4 text-center">Edit Blog Post</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label><strong>Title:</strong></label>
            <input type="text" name="title" value="<?= htmlspecialchars($blog['title']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label><strong>Category:</strong></label>
            <input type="text" name="category" value="<?= htmlspecialchars($blog['category']) ?>" class="form-control" required>
        </div>

        <div class="form-group">
            <label><strong>Featured Image:</strong></label><br>
            <?php if (!empty($blog['image'])): ?>
                <img src="<?= htmlspecialchars($blog['image']) ?>" class="img-preview">
            <?php endif; ?>
            <input type="file" name="image" class="form-control-file mt-2">
        </div>

        <div class="form-group">
            <label><strong>Or use an image URL:</strong></label>
            <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
        </div>

        <div class="form-group">
            <label for="description"><strong>Blog Description:</strong></label>
            <textarea name="description" id="description" rows="10"><?= htmlspecialchars($blog['description']) ?></textarea>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-update">Update Blog</button>
            <a href="modify-blog.php" class="btn btn-back ml-2">Cancel</a>
        </div>
    </form>

    <div class="text-center mt-auto">
        <a href="dashboard.php" class="btn btn-back mt-3">Back to Dashboard</a>
    </div>
</div>

<script>
    CKEDITOR.replace('description');
</script>

<div class="copyright fables-footer-background-color mt-0 border-0 white-color">
        <ul class="nav fables-footer-social-links just-center fables-light-footer-links">
            <li><a href="#" target="_blank"><i class="fab fa-google-plus-square"></i></a></li>
            <li><a href="#" target="_blank"><i class="fab fa-facebook"></i></a></li>
            <li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>
            <li><a href="#" target="_blank"><i class="fab fa-pinterest-square"></i></a></li>
            <li><a href="#" target="_blank"><i class="fab fa-twitter-square"></i></a></li>
            <li><a href="#" target="_blank"><i class="fab fa-linkedin"></i></a></li>
        </ul>
        <p class="mb-0">Copyright © skyworldconsultingcompany 2025. All rights reserved.</p> 
</div>    
<!-- /End Footer 2 Background Image -->

<script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="assets/vendor/jquery-circle-progress/circle-progress.min.js"></script>
<script src="assets/vendor/popper/popper.min.js"></script>
<script src="assets/vendor/jQuery.countdown-master/jquery.countdown.min.js"></script>
<script src="assets/vendor/timeline/jquery.timelify.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap-4-navbar.js"></script>
<script src="assets/vendor/owlcarousel/owl.carousel.min.js"></script> 
<script src="assets/vendor/WOW-master/dist/wow.min.js"></script>
<script src="assets/custom/js/jquery-data-to.js"></script>  
<script src="assets/custom/js/custom.js"></script>  
</body>
</html>