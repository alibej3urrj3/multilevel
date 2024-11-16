<?php
$conn = new mysqli("localhost", "root", "", "exam_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get site settings
$sql = "SELECT * FROM site_settings";
$result = $conn->query($sql);
$settings = [];
while ($row = $result->fetch_assoc()) {
    if ($row['setting_type'] == 'json') {
        $settings[$row['setting_key']] = json_decode($row['setting_value'], true) ?? [];
    } else {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Get carousel settings
$carousel_images = $settings['carousel_images'] ?? [];
$carousel_titles = $settings['carousel_titles'] ?? [];
$carousel_descriptions = $settings['carousel_descriptions'] ?? [];
$carousel_links = $settings['carousel_links'] ?? [];

?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['site_title']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Header styles */
        .top-header {
            background: #f8f9fa;
            padding: 10px 0;
            font-size: 14px;
        }
        .main-header {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .site-logo {
            height: 60px;
            width: auto;
            object-fit: contain;
        }
        /* Carousel styles */
        .banner-carousel {
            background: #000;
        }
        .carousel-item {
            height: 500px;
            overflow: hidden;
            position: relative;
        }
        .carousel-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            opacity: 0.7;
        }
        .carousel-caption {
            z-index: 2;
            text-align: left;
            top: 50%;
            transform: translateY(-50%);
            bottom: auto;
        }
        .carousel-caption h2 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        /* Navigation styles */
        .navbar-nav .nav-link {
            padding: 0.8rem 1.2rem;
            font-weight: 500;
        }
        .navbar-nav .nav-link:hover {
            color: #0d6efd;
        }
        .btn-register {
            background: linear-gradient(45deg, #0d6efd, #0099ff);
            border: none;
            padding: 0.8rem 2rem;
        }
        .btn-register:hover {
            background: linear-gradient(45deg, #0099ff, #0d6efd);
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="me-3">
                        <i class="fas fa-phone me-2"></i>
                        <?php echo htmlspecialchars($settings['contact_phone']); ?>
                    </span>
                    <span>
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($settings['contact_email']); ?>
                    </span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="<?php echo htmlspecialchars($settings['social_facebook']); ?>" class="text-dark me-3">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_telegram']); ?>" class="text-dark me-3">
                        <i class="fab fa-telegram"></i>
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_instagram']); ?>" class="text-dark">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="/">
                    <img src="uploads/<?php echo htmlspecialchars($settings['site_logo']); ?>" 
                         alt="<?php echo htmlspecialchars($settings['site_title']); ?>" 
                         class="site-logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/">Bosh sahifa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/about">Biz haqimizda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/exams">Imtihonlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contact">Bog'lanish</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary btn-register text-white ms-2" href="/register">
                                Ro'yxatdan o'tish
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <!-- Banner Carousel -->
    // Karusel stillarini yangilash
<style>
.banner-carousel {
    background: #000;
}
.carousel-item {
    height: <?php echo $settings['carousel_image_height'] ?? '500'; ?>px;
}
.carousel-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.8;
}
</style>

<!-- Karusel HTML -->
<div id="bannerCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel" 
     data-bs-interval="<?php echo $settings['carousel_interval'] ?? '5000'; ?>">
    <div class="carousel-indicators">
        <?php for($i = 0; $i < count($carousel_images); $i++): ?>
            <button type="button" data-bs-target="#bannerCarousel" 
                    data-bs-slide-to="<?php echo $i; ?>" 
                    <?php echo $i === 0 ? 'class="active"' : ''; ?>></button>
        <?php endfor; ?>
    </div>

    <div class="carousel-inner">
        <?php foreach($carousel_images as $index => $image): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="uploads/<?php echo htmlspecialchars($image); ?>" 
                     class="carousel-image" 
                     alt="<?php echo htmlspecialchars($carousel_titles[$index] ?? ''); ?>">
                <div class="carousel-caption">
                    <?php if (isset($carousel_titles[$index])): ?>
                        <h2><?php echo htmlspecialchars($carousel_titles[$index]); ?></h2>
                    <?php endif; ?>
                    <?php if (isset($carousel_descriptions[$index])): ?>
                        <p class="d-none d-md-block"><?php echo htmlspecialchars($carousel_descriptions[$index]); ?></p>
                    <?php endif; ?>
                    <?php if (isset($carousel_links[$index])): ?>
                        <a href="<?php echo htmlspecialchars($carousel_links[$index]); ?>" 
                           class="btn btn-primary btn-lg">Batafsil</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($carousel_images) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    <?php endif; ?>
</div>

    <!-- Exam Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Imtihon Kategoriyalari</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                        <h4>IELTS</h4>
                        <p>Academic va General Training</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-book fa-3x mb-3"></i>
                        <h4>TOEFL</h4>
                        <p>Internet-based Test (iBT)</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-certificate fa-3x mb-3"></i>
                        <h4>LanguageCERT</h4>
                        <p>International ESOL</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="exam-card text-center">
                        <i class="fas fa-layer-group fa-3x mb-3"></i>
                        <h4>Multilevel</h4>
                        <p>Barcha darajalar uchun</p>
                        <a href="#" class="btn btn-outline-primary">Batafsil</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest Mock Tests -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">So'nggi Mock Testlar</h2>
            <div class="owl-carousel mock-tests-carousel">
                <!-- Mock test items will be dynamically loaded -->
            </div>
        </div>
    </section>

    <!-- User Reviews -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Foydalanuvchilar Fikrlari</h2>
            <div class="owl-carousel review-carousel">
                <div class="review-card">
                    <div class="d-flex align-items-center mb-3">
                        <img src="/api/placeholder/50/50" class="rounded-circle me-3" alt="User">
                        <div>
                            <h5 class="mb-0">Aziz Rahimov</h5>
                            <small class="text-muted">IELTS: Band 7.5</small>
                        </div>
                    </div>
                    <p>"Bu platforma orqali IELTS imtihoniga tayyorgarlik ko'rdim va kutganimdan ham yaxshi natija oldim."</p>
                </div>
                <!-- More review items -->
            </div>
        </div>
    </section>

    <!-- Partners -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-4">Hamkorlarimiz</h2>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-2 text-center">
                    <img src="/api/placeholder/150/80" alt="Partner 1" class="partner-logo">
                </div>
                <!-- More partner logos -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>ExamPrep</h5>
                    <p>Professional imtihonga tayyorgarlik portali</p>
                </div>
                <div class="col-md-4">
                    <h5>Tezkor Havolalar</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Imtihonlar</a></li>
                        <li><a href="#" class="text-light">Mock Testlar</a></li>
                        <li><a href="#" class="text-light">Blog</a></li>
                        <li><a href="#" class="text-light">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Bog'lanish</h5>
                    <p>Email: info@examprep.uz</p>
                    <p>Tel: +998 99 123 45 67</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        $(document).ready(function(){
            // Banner carousel initialization
            $('.banner-carousel').owlCarousel({
                items: 1,
                loop: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                nav: true,
                dots: true
            });

            // Mock tests carousel initialization
            $('.mock-tests-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                }
            });

            // Reviews carousel initialization
            $('.review-carousel').owlCarousel({
                loop: true,
                margin: 20,
                nav: true,
                responsive: {
                    0: { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                }
            });
        });
    </script>
</body>
</html>