<?php
session_start();

// Access control
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.html");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "", "blog_post");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch contact messages
$sql = "SELECT name, phone, email, address, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC";
$result = $conn->query($sql);
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
  
    <style>
        body, html {
            height: 100%;
        }
        .full-height {
            min-height: 100vh;
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

<div class="container full-height d-flex flex-column py-5">
    <h2 class="mb-4 text-center">Contact Messages</h2>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Message</th>
                <th>Date of Submission</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($row['submitted_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No contact messages found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-3">
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</div>



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
<?php $conn->close(); ?>