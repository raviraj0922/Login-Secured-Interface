<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_id"])) {
    header("Location: signin.html");
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
  
  <style>
    html, body {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
    }

    .full-height {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .dashboard-box {
      padding: 25px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      background-color: #fff;
    }

    .btn-custom {
      width: 160px;
      margin-top: 10px;
    }

    .purple { background-color: #7e6ee0; color: white; }
    .dark-purple { background-color: #3d0070; color: white; }
    .blue { background-color: #0e97ae; color: white; }
    .green { background-color: #0b5e1c; color: white; }
    .red { background-color: #f26c51; color: white; }

    .dashboard-title {
      font-weight: 600;
      margin-bottom: 1rem;
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
  <h3 class="text-center dashboard-title">Admin Dashboard</h3>
  <p class="text-center mb-5">Welcome, <strong><?php echo htmlspecialchars($_SESSION["name"]); ?></strong></p>

  <div class="row text-center justify-content-center">
    <!-- Column 1 -->
    <div class="col-md-4">
      <div class="dashboard-box">
        <h5>Manage Users</h5>
        <p>View and manage registered users.</p>
        <a href="users.php" class="btn purple btn-custom">Go to Users</a>
      </div>

      <div class="dashboard-box">
        <h5>Users Blog</h5>
        <p>Check and Delete User's Blogs</p>
        <a href="user-blogs.php" class="btn dark-purple btn-custom">User Blog</a>
      </div>
    </div>

    <!-- Column 2 -->
    <div class="col-md-4">
      <div class="dashboard-box">
        <h5>Add Blogs</h5>
        <p>Add New blogs</p>
        <a href="add-blog.php" class="btn blue btn-custom">Add Blog</a>
      </div>

      <div class="dashboard-box">
        <h5>Logout</h5>
        <p>Logout from your account securely.</p>
        <a href="logout.php" class="btn red btn-custom">Logout</a>
      </div>
    </div>

    <!-- Column 3 -->
    <div class="col-md-4">
      <div class="dashboard-box">
        <h5>Modify Blog</h5>
        <p>Update or Modify Existing Blogs</p>
        <a href="modify-blog.php" class="btn green btn-custom">Modify Blog</a>
      </div>
    </div>
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