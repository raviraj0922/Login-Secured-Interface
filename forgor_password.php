<?php
session_start();
$conn = new mysqli("localhost", "root", "", "blog_post");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$stage = 'email';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["stage"])) {
    $stage = $_POST["stage"];

    // 1. Email Submission
    if ($stage === 'email' && isset($_POST["email"])) {
        $email = trim($_POST["email"]);
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;

            // Store code in DB
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET reset_code = ?, reset_requested_at = NOW() WHERE email = ?");
            $stmt->bind_param("ss", $code, $email);
            $stmt->execute();

            // Send email
            mail($email, "Your Reset Code", "Your verification code is: $code", "From: no-reply@skyworldconsultingcompany.com");

            $message = "Verification code sent to your email.";
            $stage = 'verify';
        } else {
            $message = "Email not found.";
        }
        $stmt->close();
    }

    // 2. Verification Stage
    elseif ($stage === 'verify' && isset($_POST["code"])) {
        $code = trim($_POST["code"]);
        $email = $_SESSION['reset_email'] ?? '';

        $stmt = $conn->prepare("SELECT reset_code, reset_requested_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($db_code, $requested_at);
        if ($stmt->fetch()) {
            $valid = strtotime($requested_at) > strtotime('-10 minutes');
            if ($code === $db_code && $valid) {
                $stage = 'reset';
            } else {
                $message = "Invalid or expired verification code.";
                $stage = 'verify';
            }
        } else {
            $message = "User not found.";
            $stage = 'email';
        }
        $stmt->close();
    }

    // 3. Reset Password Stage
    elseif ($stage === 'reset' && isset($_POST["password"], $_POST["confirm_password"])) {
        $password = trim($_POST["password"]);
        $confirm = trim($_POST["confirm_password"]);

        if ($password !== $confirm) {
            $message = "Passwords do not match.";
            $stage = 'reset';
        } else {
            $hashed = md5($password); // ⚠ Replace with password_hash in production
            $email = $_SESSION['reset_email'];

            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_requested_at = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashed, $email);
            if ($stmt->execute()) {
                session_unset();
                echo "<script>alert('Password reset successful!'); window.location.href='signin.php';</script>";
                exit;
            } else {
                $message = "Failed to reset password.";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
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
    <!-- Load Screen -->
    <link href="assets/vendor/loadscreen/css/spinkit.css" rel="stylesheet">
    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i" rel="stylesheet">
    <!-- Font Awesome 5 -->
    <link href="assets/vendor/fontawesome/css/fontawesome-all.min.css" rel="stylesheet">
    <!-- Fables Icons -->
    <link href="assets/custom/css/fables-icons.css" rel="stylesheet"> 
    <!-- Bootstrap CSS -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
    <link href="assets/vendor/bootstrap/css/bootstrap-4-navbar.css" rel="stylesheet">
    <!-- FANCY BOX -->
    <link href="assets/vendor/fancybox-master/jquery.fancybox.min.css" rel="stylesheet">
    <!-- OWL CAROUSEL  -->
    <link href="assets/vendor/owlcarousel/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/vendor/owlcarousel/owl.theme.default.min.css" rel="stylesheet">
    <!-- Timeline -->
    <link rel="stylesheet" href="assets/vendor/timeline/timeline.css"> 
    <!-- FABLES CUSTOM CSS FILE -->
    <link href="assets/custom/css/custom.css" rel="stylesheet">
    <!-- FABLES CUSTOM CSS RESPONSIVE FILE -->
    <link href="assets/custom/css/custom-responsive.css" rel="stylesheet"> 
    <style>
        .form-section { display: none; }
        .form-section.active { display: block; }
        .message { color: red; text-align: center; margin: 10px 0; }
    </style>
     
</head>

<body>

<div class="search-section">
    <a class="close-search" href="#"></a>
    <div class="d-flex justify-content-center align-items-center h-100">
        <form method="post" action="#" class="w-50">
            <div class="row">
                <div class="col-10">
                    <input type="search" value="" class="form-control palce bg-transparent border-0 search-input" placeholder="Search Here ..." /> 
                </div>
                <div class="col-2 mt-3">
                     <button type="submit" class="btn bg-transparent text-white"> <i class="fas fa-search"></i> </button>
                </div>
            </div>
        </form>
    </div>
         
</div>

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
                         <!-- Collapse button -->
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1"
                        aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent1">

                            <!-- Links -->
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item dropdown active">
                                    <a class="nav-link text-uppercase" href="index.html" id="sub-nav1" aria-haspopup="true" aria-expanded="false">
                                        Home
                                    </a>
                                </li>
                    
                                <li class="nav-item dropdown mega-dropdown">
                                    <a class="nav-link dropdown-toggle text-uppercase no-caret" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">About Us</a>
                                    <div class="dropdown-menu mega-menu v-2 row z-depth-1 special-color" aria-labelledby="navbarDropdownMenuLink1">
                                        <div class="row mx-md-4 mx-1">
                                            <div class="col-md-6 col-lg-6 sub-menu my-lg-5 my-4">
                                                <h6 class="sub-title text-uppercase font-weight-bold white-text">About Us</h6>
                                                <ul class="caret-style pl-0">
                                                    <li class=""><a class="menu-item" href="our-purpose.html">Our Purpose</a></li>
                                                    <li class=""><a class="menu-item" href="our-vision.html">Our Vision</a></li>
                                                    <li class=""><a class="menu-item" href="our-mission.html">Our Mission</a></li>
                                                    <li class=""><a class="menu-item" href="why-choose-us.html">Why Choose Us</a></li>
                                                </ul>
                                            </div>
                                            <div class="col-md-12 col-lg-6 sub-menu my-lg-5 mt-5 mb-4">
                                                <h6 class="sub-title text-uppercase font-weight-bold white-text">Our Legacy</h6>
                                                <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                     <p class="text-white">SkyWorld Consulting Company is more than a corporate services provider; we are a multifaceted global enterprise with strong business divisions across various industries. Our diversified portfolio reflects our commitment to innovation, excellence, and global business expansion.</p>
                                                    <div class="mask flex-center">
                                                        <p></p>
                                                    </div>
                                                </div>
                                                <a href="our-legacy.html" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item dropdown mega-dropdown">
                                    <a class="nav-link dropdown-toggle text-uppercase no-caret" id="navbarDropdownMenuLink1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Services</a>
                                    <!-- MEGA MENU DROPDOWN -->
                                    <div class="dropdown-menu mega-menu shadow p-4" aria-labelledby="careerDropdown">
                                        <div class="row">
                                          
                                          <!-- LEFT COLUMN: LIST WITH ARROWS -->
                                          <div class="col-md-6">
                                            <ul class="list-group" id="serviceList">
                                              <li class="list-group-item d-flex justify-content-between align-items-center active" data-target="detail1">
                                                  Legal Advisory & Compliance<span>&rarr;</span>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail2">
                                                  Financial & Taxation Consulting<span>&rarr;</span>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail3">
                                                  Government & Policy Liaison<span>&rarr;</span>
                                              </li>
                                            </ul>
                                          </div>
                                    
                                          <!-- RIGHT COLUMN: DYNAMIC DETAILS -->
                                          <div class="col-md-6">
                                            <div id="serviceDetails">
                                              <div class="detail-box" id="detail1">
                                                  <p class="base-line">Legal Assistance on PMLA- Benami and Under all other act lodged by Enforcement Directorate</p>
                                                  <p class="base-line">Legal Assistance on Cases lodged by Central Bureau of Investigation</p>
                                                  <p class="base-line">Legal Assistance on cases and other litigation lodged by GST (Central & State)</p>
                                                  <p class="base-line">Legal Assistance on cases and litigation lodged by Income Tax Department</p>
                                                  <p class="base-line">Legal Assistance on cases lodged by Customs</p>
                                                  <p class="base-line">Legal documentation and contract management</p>
                                                  <p class="base-line">Dispute resolution and arbitration</p>
                                              </div>
                                              <div class="detail-box d-none" id="detail2">
                                                  <p class="base-line">Income Tax advisory and compliance</p>
                                                  <p class="base-line">GST consultancy and return filing</p>
                                                  <p class="base-line">Financial structuring and corporate tax optimization</p>
                                              </div>
                                              <div class="detail-box d-none" id="detail3">
                                                  <p class="base-line">Representation before government bodies</p>
                                                  <p class="base-line">Policy advocacy and regulatory updates</p>
                                                  <p class="base-line">Facilitation of approvals and exemptions</p>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                </li>
                                <li class="nav-item dropdown mega-dropdown position-static">
                                    <a class="nav-link dropdown-toggle text-uppercase" href="#" id="careerDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Careers</a>
                                  
                                    <!-- MEGA MENU DROPDOWN -->
                                    <div class="dropdown-menu mega-menu shadow p-4" aria-labelledby="careerDropdown">
                                      <div class="row">
                                        
                                        <!-- LEFT COLUMN: LIST WITH ARROWS -->
                                        <div class="col-md-6">
                                          <ul class="list-group" id="careerList">
                                            <li class="list-group-item d-flex justify-content-between align-items-center active" data-target="detail4">
                                                What We Are Looking For<span>&rarr;</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail5">
                                                Application Process<span>&rarr;</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail6">
                                                Interview Process<span>&rarr;</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail7">
                                                Job Offer and Onboarding<span>&rarr;</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center" data-target="detail8">
                                              Apply<span>&rarr;</span>
                                            </li>
                                          </ul>
                                        </div>
                                  
                                        <!-- RIGHT COLUMN: DYNAMIC DETAILS -->
                                        <div class="col-md-6">
                                          <div id="careerDetails">
                                            <div class="detail-box" id="detail4">
                                                <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                <p class="text-white">We’re interested in your strengths, what you want to learn, and your aspirations. Be yourself and let's explore how we can make a bigger impact, together.</p>
                                               <div class="mask flex-center">
                                                   <p></p>
                                               </div>
                                                </div>
                                                <a href="#" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                            </div>
                                            <div class="detail-box d-none" id="detail5">
                                                <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                    <p class="text-white">A list of current openings can be found under career section, and you'll find detailed information about specific requirements in the job descriptions. Simply click on “Apply” button and fill in the application form.</p>
                                                    &nbsp;
                                                    <p class="text-white">Your job application will be reviewed, and we'll let you know when we receive your information and, if your application meets our requirements, we'll invite you to an interview.</p>
                                                   <div class="mask flex-center">
                                                       <p></p>
                                                   </div>
                                                    </div>
                                                    <a href="#" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                            </div>
                                            <div class="detail-box d-none" id="detail6">
                                                <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                    <p class="text-white">We want to get to know you as an individual and discover your strengths and your uniqueness. Having said that, this is a two-way process, hence it is also your chance to find out whether SkyWorld Consulting Company is the place you want to be. You'll be encouraged to interview us about the scope of the role, our expectations, what we offer and our vision for how we could work together. </p>
                                                    &nbsp;
                                                    <p class="text-white">Your interview experience will be a combination of in-person, phone and video.</p>
                                                   <div class="mask flex-center">
                                                       <p></p>
                                                   </div>
                                                    </div>
                                                    <a href="#" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                            </div>
                                            <div class="detail-box d-none" id="detail7">
                                                <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                    <p class="text-white">Upon successfully completion of interview process, and you feel SkyWorld Consulting Company is the place wherein you can fulfil your career aspirations, we'll make a formal offer, and we'll look forward to receiving your confirmation and welcoming you on board.</p>
                                                   <div class="mask flex-center">
                                                       <p></p>
                                                   </div>
                                                    </div>
                                                    <a href="#" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                            </div>
                                            <div class="detail-box d-none" id="detail8">
                                              <div class="view overlay hm-white-slight mb-3 z-depth-1">
                                                  <p class="text-white">Please fill up the job application form diligently, our team will screen through your profile and contact you.</p>
                                                 <div class="mask flex-center">
                                                     <p></p>
                                                 </div>
                                                  </div>
                                                  <a href="registration.html" class="btn fables-second-background-color white-color white-color-hover py-2 px-4 my-2 rounded-0 font-18">Learn More</a>
                                          </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </li>
                    
                            </ul>
                            <!-- Links -->
                    
                            <!-- Search form -->
                            
                        </div>
                        <div class="col-12 col-md-2 col-lg-3 pr-md-0 icons-header-mobile">
                            <div class="fables-header-icons">
                                <div class="dropdown"> 
                                    <a href="#" class="open-search fables-third-text-color right dropdown-toggle right px-3 px-md-2 px-lg-4 fables-second-hover-color top-header-link max-line-height">
                                        <span class="fables-iconsearch-icon"></span>
                                    </a>
                                 </div> 
                            </div>
                           </div>
            </nav>
               </div>
               
           </div>
    </div>
</div>
<script>
    // javascript for career
    document.querySelectorAll('#careerList li').forEach(item => {
    item.addEventListener('click', function () {
        // Remove active from all
        document.querySelectorAll('#careerList li').forEach(li => li.classList.remove('active'));
        this.classList.add('active');

        // Hide all detail boxes
        document.querySelectorAll('#careerDetails .detail-box').forEach(box => box.classList.add('d-none'));

        // Show selected
        const targetId = this.getAttribute('data-target');
        document.getElementById(targetId).classList.remove('d-none');
    });
    });

    // javascript for service
    document.querySelectorAll('#serviceList li').forEach(item => {
    item.addEventListener('click', function () {
        // Remove active from all
        document.querySelectorAll('#serviceList li').forEach(li => li.classList.remove('active'));
        this.classList.add('active');

        // Hide all detail boxes
        document.querySelectorAll('#serviceDetails .detail-box').forEach(box => box.classList.add('d-none'));

        // Show selected
        const targetId = this.getAttribute('data-target');
        document.getElementById(targetId).classList.remove('d-none');
    });
    });
</script>
<!-- /End Fables Navigation -->
     
<!-- Start Header -->
<div class="fables-header bg-rules" style="background-image: url(./assets/custom/images/signIn.png);">
    <div class="container"> 
         <h2 class="fables-page-title fables-main-text-color wow fadeInLeft" data-wow-duration="1.5s">Forgot Password</h2>
    </div>
</div>  
<!-- /End Header -->
     
<!-- Start Breadcrumbs -->
<div class="fables-light-background-color">
    <div class="container"> 
        <nav aria-label="breadcrumb">
          <ol class="fables-breadcrumb breadcrumb px-0 py-3">
            <li class="breadcrumb-item"><a href="#" class="fables-second-text-color">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Forgot Password</li>
          </ol>
        </nav> 
    </div>
</div>
<!-- /End Breadcrumbs -->
     
<!-- Start page content -->   
<div class="container">
     <div class="row my-4 my-lg-5">
          <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 text-center">
          <h2 style="text-align:center;">Forgot Password</h2>
    <div class="message"><?= $message ?></div>

<form method="POST" id="multiForm">
        <!-- Email Form -->
        <div class="form-section <?= $stage == 'email' ? 'active' : '' ?>" id="emailSection">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="hidden" name="stage" value="email">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="email" name="email" placeholder="Enter your email" required>
            <button class="btn btn-block rounded-0 white-color fables-main-hover-background-color fables-second-background-color font-16 semi-font py-3 mt-4" type="submit">Send Verification Code</button>
        </div>

        <!-- Verification Code Form -->
        <div class="form-section <?= $stage == 'verify' ? 'active' : '' ?>" id="verifySection">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="hidden" name="stage" value="verify">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="text" name="code" placeholder="Enter verification code" required>
            <button class="btn btn-block rounded-0 white-color fables-main-hover-background-color fables-second-background-color font-16 semi-font py-3 mt-4" type="submit">Verify Code</button>
        </div>

        <!-- Reset Password Form -->
        <div class="form-section <?= $stage == 'reset' ? 'active' : '' ?>" id="resetSection">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="hidden" name="stage" value="reset">
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="password" name="password" placeholder="New Password" required>
            <input class="form-control rounded-0 py-3 pl-5 font-13 sign-register-input" type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button class="btn btn-block rounded-0 white-color fables-main-hover-background-color fables-second-background-color font-16 semi-font py-3 mt-4" type="submit">Reset Password</button>
        </div>
    </form>

<script>
    // Smooth transition based on PHP variable
    const stage = '<?= $stage ?>';
    document.querySelectorAll('.form-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(stage + 'Section').classList.add('active');
</script>
          </div>
     </div>

</div>
      
<!-- /End page content -->
    
    
<!-- Start Footer 2 Background Image  -->
<div class="fables-footer-image white-color py-4 py-lg-5 bg-rules">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 mt-2 mb-5 text-center">
                <h2 class="font-30 semi-font mb-5">Newsletter</h2>
                <form class="form-inline position-relative"> 
                  <div class="form-group fables-subscribe-formgroup"> 
                    <input type="email" class="form-control fables-subscribe-input fables-btn-rouned" placeholder="Your Email">
                  </div>
                  <button type="submit" class="btn fables-second-background-color fables-btn-rouned fables-subscribe-btn">Subscribe</button>
                </form>
                
            </div>
            <div class="col-12 col-lg-4 mb-4 mb-lg-0">
                <a href="#" class="fables-second-border-color border-bottom pb-3 d-block mb-3 mt-minus-13"><img src="assets/custom/images/swcc-logo.png" alt="swcc-logo" class="fables-logo-footer"></a>
                <p class="font-16 text-white">
                    "We collaborate with the world's leading organizations to deliver strategic, tailor-made solutions that drive business growth and transformation."
                </p> 
                
            </div>
             
            <div class="col-12 col-sm-6 col-lg-4">
                <h2 class="font-20 semi-font fables-second-border-color border-bottom pb-3">Contact us</h2>
               <div class="my-3">
                    <h4 class="font-16 semi-font"><span class="fables-iconmap-icon text-white pr-2 font-20 mt-1 d-inline-block"></span> Address Information</h4>
                    <p class="font-14 text-white mt-2 ml-4">Tower C - Ground Floor, Balaji Estate, Kalkaji, Delhi - 110019</p>
                </div>
                <div class="my-3">
                    <h4 class="font-16 semi-font"><span class="fables-iconphone text-white pr-2 font-20 mt-1 d-inline-block"></span> Call Now </h4>
                    <p class="font-14 text-white mt-2 ml-4"> 011 4039 4403</p>
                </div>
                <div class="my-3">
                    <h4 class="font-16 semi-font"><span class="fables-iconemail text-white pr-2 font-20 mt-1 d-inline-block"></span> Mail </h4>
                    <p class="font-14 text-white mt-2 ml-4">corp.office@skyworldconsultingcompany.com</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <h2 class="font-20 semi-font fables-second-border-color border-bottom pb-3 mb-3">EXPLORE OUR SITE</h2>
                <ul class="nav fables-footer-links">
                    <li><a href="contact-us.html" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Contact Us</a></li>
                    <li><a href="privacy.html" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Privacy</a></li>
                    <li><a href="#" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Legal</a></li>
                    <li><a href="#" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Gallery</a></li>
                    <li><a href="blog.html" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Blog</a></li>
                    <li><a href="#" style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">Testimonials</a></li>
                </ul>
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
<script src="assets/vendor/loadscreen/js/ju-loading-screen.js"></script>
<script src="assets/vendor/jquery-circle-progress/circle-progress.min.js"></script>
<script src="assets/vendor/popper/popper.min.js"></script>
<script src="assets/vendor/WOW-master/dist/wow.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap-4-navbar.js"></script>
<script src="assets/vendor/owlcarousel/owl.carousel.min.js"></script> 
<script src="assets/vendor/timeline/jquery.timelify.js"></script>
<script src="assets/custom/js/custom.js"></script>  
   
    
</body>
</html>